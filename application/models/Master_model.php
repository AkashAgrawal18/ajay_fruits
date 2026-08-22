<?php date_default_timezone_set('Asia/Kolkata');
class Master_model extends CI_model
{
  /** Why the last write failed - see Main_model for the rationale. */
  protected $last_error = '';

  /** Records why an operation failed and returns false. */
  protected function fail($message)
  {
    $this->last_error = $message;
    return false;
  }

  /** The reason for the last failure, then clears it. */
  public function last_error()
  {
    $msg = $this->last_error;
    $this->last_error = '';
    return $msg;
  }

  /**
   * Helper: returns the current branch ID from session.
   * All queries use this to scope data per branch.
   */
  private function is_superadmin()
  {
    return $this->session->userdata('user_type') == 8;
  }

  /**
   * See Main_model::branch_id() for the rationale. Only a type-9 branch account
   * is scoped by its own user id; applying that to every non-superadmin filters
   * ordinary users against a non-existent branch (BUG-002/BUG-003).
   */
  private function branch_id($override = null)
  {
    $type = $this->session->userdata('user_type');

    if ($type == 9) {
      return (int) $this->session->userdata('user_id');
    }

    if ($type == 8) {
      return ($override !== null && $override !== '') ? (int) $override : null;
    }

    return null;
  }

  /** Branch to stamp on inserts; *_branch columns are NOT NULL (BUG-009). */
  private function branch_for_insert($override = null)
  {
    $branch = $this->branch_id($override);
    return $branch === null ? 0 : (int) $branch;
  }

  private function where_branch($column, $override = null)
  {
    $branch = $this->branch_id($override);
    if ($branch !== null) {
      $this->db->where($column, $branch);
    }
    return $this->db;
  }

  // Unused raw-SQL helper kept for backward compatibility with any
  // legacy callers that may build manual query fragments.
  private function branch_sql($column, $override = null)
  {
    $branch = $this->branch_id($override);
    return $branch !== null ? " AND {$column} = " . (int) $branch . " " : "";
  }

  // ===================== itemgroup =======================//
  // Item groups, units and crate sizes are shared by every branch - the
  // same crate size means the same thing everywhere - so none of these
  // lookups filter on m_itgrp_branch. Inserts still stamp it to record
  // where the row was created; nothing reads it back as a filter.

  public function all_itemgroup($type, $branch_id = null)
  {
    return $this->db->where('m_itgrp_type', $type)
      ->order_by('m_itgrp_title')
      ->get('master_itemgroup_tbl')->result();
  }

  public function all_active_itemgroup($type, $branch_id = null)
  {
    return $this->db->select('master_itemgroup_tbl.*')
      ->where('m_itgrp_type', $type)
      ->where('m_itgrp_status', 1)
      ->order_by('m_itgrp_title')
      ->get('master_itemgroup_tbl')->result();
  }

  public function insert_itemgroup()
  {
    $machineid   = $this->input->post('m_itgrp_id');
    $machinename = $this->input->post('m_itgrp_title');
    $branch = $this->branch_id($this->input->post('m_itgrp_branch'));

    // Item groups, units and crate sizes are three separate screens sharing
    // this one table, told apart by m_itgrp_type. The duplicate check has to
    // be scoped to the same type, or adding an item group named "Crate" is
    // refused because a *unit* of that name exists.
    $type = $this->input->post('m_itgrp_type');
    $labels = array(1 => 'An item group', 2 => 'A unit', 3 => 'A crate size');
    $label = isset($labels[$type]) ? $labels[$type] : 'A record';

    // Shared across branches, so the name has to be unique outright.
    $check = $this->db->where('m_itgrp_title', $machinename)
      ->where('m_itgrp_type', $type)
      ->get('master_itemgroup_tbl')->result();

    if (!empty($check) && empty($machineid)) {
      return $label . ' named "' . $machinename . '" already exists. Use a different name, or edit the existing one.';
    }

    $insert_data = array(
      "m_itgrp_title"    => $machinename,
      "m_itgrp_status"   => 1,
      "m_itgrp_type"     => $this->input->post('m_itgrp_type'),
      "m_itgrp_added_on" => date('Y-m-d H:i:s'),
    );

    if (!empty($machineid)) {
      $this->db->where('m_itgrp_id', $machineid)->update('master_itemgroup_tbl', $insert_data);
      return 2;
    } else {
      $insert_data['m_itgrp_branch'] = $branch ?? 0;
      $this->db->insert('master_itemgroup_tbl', $insert_data);
      return 1;
    }
  }

  public function get_edit_itemgroup($id, $branch_id = null)
  {
    return $this->db->select('master_itemgroup_tbl.*')
      ->where('m_itgrp_id', $id)
      ->get('master_itemgroup_tbl')->row();
  }

  public function delete_itemgroup($branch_id = null)
  {
    $this->db->where('m_itgrp_id', $this->input->post('delete_id'));
    $this->db->delete('master_itemgroup_tbl');

    // a delete that matched no rows is still a successful query
    if ($this->db->affected_rows() < 1) {
      return $this->fail('That unit/crate/group was not found. It may already have been deleted.');
    }
    return true;
  }

  // ===================== item =======================//
  // Items are shared by every branch, same as the item groups above.
  // m_item_branch is still written on insert but is not used to filter.

  public function get_all_item($id = '', $branch_id = null)
  {
    if (!empty($id)) {
      $this->db->where('m_item_id', $id);
    }
    return $this->db->select('master_item_tbl.*,group.m_itgrp_title as groupname,crate.m_itgrp_title as cratetype,unit.m_itgrp_title as unitname')
      ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = master_item_tbl.m_item_group', 'left')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_item_tbl.m_item_crate', 'left')
      ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = master_item_tbl.m_item_unit', 'left')
      ->order_by('m_item_name')
      ->get('master_item_tbl')->result();
  }

  public function insert_item()
  {
    $itemid = $this->input->post('m_item_id');
    $branch = $this->branch_id($this->input->post('m_item_branch'));

    $insert_data = array(
      "m_item_name"    => $this->input->post('m_item_name'),
      "m_item_group"   => $this->input->post('m_item_group'),
      "m_item_crate"   => $this->input->post('m_item_crate'),
      "m_item_unit"    => $this->input->post('m_item_unit'),
      "m_item_fright"  => $this->input->post('m_item_fright'),
      "m_item_comm"    => $this->input->post('m_item_comm'),
      "m_item_price"   => $this->input->post('m_item_price'),
      "m_item_status"  => 1,
      "m_item_added_on" => date('Y-m-d H:i:s'),
    );

    if (!empty($itemid)) {
      $this->db->where('m_item_id', $itemid)->update('master_item_tbl', $insert_data);
      return 2;
    } else {
      $insert_data['m_item_branch'] = $branch ?? 0;
      $this->db->insert('master_item_tbl', $insert_data);
      return 1;
    }
  }

  public function get_edit_item($id, $branch_id = null)
  {
    return $this->db->select('master_item_tbl.*,group.m_itgrp_title as groupname,crate.m_itgrp_title as cratetype,unit.m_itgrp_title as unitname')
      ->where('m_item_id', $id)
      ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = master_item_tbl.m_item_group', 'left')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_item_tbl.m_item_crate', 'left')
      ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = master_item_tbl.m_item_unit', 'left')
      ->get('master_item_tbl')->row();
  }

  public function delete_item($branch_id = null)
  {
    $this->db->where('m_item_id', $this->input->post('delete_id'));
    $this->db->delete('master_item_tbl');

    if ($this->db->affected_rows() < 1) {
      return $this->fail('That item was not found. It may already have been deleted.');
    }
    return true;
  }

  // ========================== group =============================//
  // master_group_tbl has m_group_branch. Note: when type == 2 the table
  // self-joins (alias "group"), so the branch column is qualified with
  // the base table name to avoid ambiguous-column errors.

  public function get_all_group($type, $branch_id = null)
  {
    if ($type == 2) {
      $this->db->select('master_group_tbl.*,group.m_group_name as expense_group')
        ->join('master_group_tbl as group', 'group.m_group_id = master_group_tbl.m_group_group', 'left');
    } else {
      $this->db->select('*');
    }
    $this->where_branch('master_group_tbl.m_group_branch', $branch_id);
    $this->db->where('master_group_tbl.m_group_type', $type)
      ->order_by('master_group_tbl.m_group_name');
    return $this->db->get('master_group_tbl')->result();
  }

  public function get_all_active_group($type, $branch_id = null)
  {
    if ($type == 2) {
      $this->db->select('master_group_tbl.*,group.m_group_name as expense_group')
        ->join('master_group_tbl as group', 'group.m_group_id = master_group_tbl.m_group_group', 'left');
    } else {
      $this->db->select('*');
    }
    $this->where_branch('master_group_tbl.m_group_branch', $branch_id);
    $this->db->where('master_group_tbl.m_group_type', $type)
      ->where('master_group_tbl.m_group_status', 1)
      ->order_by('master_group_tbl.m_group_name');
    return $this->db->get('master_group_tbl')->result();
  }

  public function get_all_expenses($branch_id = null)
  {
    $this->where_branch('m_group_branch', $branch_id);
    return $this->db->select('m_group_id,m_group_name,m_group_type,m_group_group')
      ->where('m_group_type !=', 1)
      ->where('m_group_status', 1)
      ->get('master_group_tbl')->result();
  }

  public function get_edit_group($edid, $branch_id = null)
  {
    $this->where_branch('m_group_branch', $branch_id);
    return $this->db->select('*')
      ->where('m_group_id', $edid)
      ->get('master_group_tbl')->row();
  }

  public function insert_group()
  {
    $branch = $this->branch_id($this->input->post('m_group_branch'));

    if ($this->input->post('m_group_optp') == 1) {
      $openingbal = $this->input->post('m_group_opening') * -1;
    } else {
      $openingbal = $this->input->post('m_group_opening');
    }

    $s_data = array(
      "m_group_name"     => $this->input->post('m_group_name'),
      "m_group_status"   => $this->input->post('m_group_status'),
      "m_group_type"     => $this->input->post('m_group_type'),
      "m_group_number"   => $this->input->post('m_group_number') ?: '',
      "m_group_group"    => $this->input->post('m_group_group') ?: '',
      "m_group_opening"  => $openingbal ?: 0,
      "m_group_remark"   => $this->input->post('m_group_remark') ?: '',
      "m_group_added_on" => date('Y-m-d H:i'),
    );

    $id = $this->input->post('m_group_id');
    if (!empty($id)) {
      $this->where_branch('m_group_branch', $branch);
      $this->db->where('m_group_id', $id)->update('master_group_tbl', $s_data);
      return 2;
    } else {
      $s_data['m_group_branch'] = $branch ?? 0;
      $this->db->insert('master_group_tbl', $s_data);
      return 1;
    }
  }

  public function delete_group($branch_id = null)
  {
    $this->where_branch('m_group_branch', $branch_id);
    $this->db->where('m_group_id', $this->input->post('delete_id'));
    $this->db->delete('master_group_tbl');

    if ($this->db->affected_rows() < 1) {
      return $this->fail('That group was not found. It may already have been deleted, or it belongs to a different branch.');
    }
    return true;
  }

  /**
   * Cash and bank accounts offered as a payment/receipt Method.
   *
   * Not branch-scoped: m_payment_method / m_recvd_method are NOT NULL, and a
   * branch with no cash or bank account of its own would get an empty
   * dropdown and be unable to record any payment or receipt. Same reasoning
   * as items and crate sizes above.
   */
  public function get_payment_methods($branch_id = null)
  {
    $this->db->select('m_group_id,m_group_name,m_group_type,m_group_status,m_group_remark');
    $this->db->where('(m_group_type = 3 OR m_group_type = 4)');
    $this->db->where('m_group_status', 1);
    return $this->db->get('master_group_tbl')->result();
  }

  // ========================== State/City =============================//
  // State, City, Country are GLOBAL reference tables — no branch column,
  // so no branch condition is applied here (left unchanged intentionally).

  public function get_all_state()
  {
    return $this->db->select('*')->order_by('m_state_name')->get('master_state_tbl')->result();
  }

  public function get_edit_state($edid)
  {
    return $this->db->select('*')->where('m_state_id', $edid)->get('master_state_tbl')->row();
  }

  public function insert_state()
  {
    $s_data = array(
      "m_state_name"     => $this->input->post('m_state_name'),
      "m_state_country"  => 1,
      "m_state_added_on" => date('Y-m-d H:i'),
    );
    $id = $this->input->post('m_state_id');
    if (!empty($id)) {
      $this->db->where('m_state_id', $id)->update('master_state_tbl', $s_data);
      return 2;
    } else {
      $this->db->insert('master_state_tbl', $s_data);
      return 1;
    }
  }

  public function delete_state()
  {
    $this->db->where('m_state_id', $this->input->post('delete_id'));
    $this->db->delete('master_state_tbl');

    if ($this->db->affected_rows() < 1) {
      return $this->fail('That state was not found. It may already have been deleted.');
    }
    return true;
  }

  public function get_all_city()
  {
    return $this->db->select('*')
      ->join('master_group_tbl', 'master_group_tbl.m_group_id = master_city_tbl.m_city_line', 'left')
      ->join('master_state_tbl', 'master_state_tbl.m_state_id = master_city_tbl.m_city_state', 'left')
      ->order_by('m_city_name')
      ->get('master_city_tbl')->result();
  }

  public function get_edit_city($edid)
  {
    return $this->db->select('*')->where('m_city_id', $edid)->get('master_city_tbl')->row();
  }

  public function insert_city()
  {
    $s_data = array(
      "m_city_name"     => $this->input->post('m_city_name'),
      "m_city_state"    => $this->input->post('m_city_state'),
      "m_city_line"     => $this->input->post('m_city_line'),
      "m_city_added_on" => date('Y-m-d H:i'),
    );
    $id = $this->input->post('m_city_id');
    if (!empty($id)) {
      $this->db->where('m_city_id', $id)->update('master_city_tbl', $s_data);
      return 2;
    } else {
      $this->db->insert('master_city_tbl', $s_data);
      return 1;
    }
  }

  public function delete_city()
  {
    $this->db->where('m_city_id', $this->input->post('delete_id'));
    $this->db->delete('master_city_tbl');

    if ($this->db->affected_rows() < 1) {
      return $this->fail('That city was not found. It may already have been deleted.');
    }
    return true;
  }

  public function get_active_country()
  {
    return $this->db->select('*')->where('m_country_status', '1')->order_by('m_country_name')->get('master_country_tbl')->result();
  }

  public function get_active_state()
  {
    return $this->db->select('*')->where('m_state_country', '1')->order_by('m_state_name')->get('master_state_tbl')->result();
  }

  public function get_active_city()
  {
    return $this->db->select('city.m_city_name,city.m_city_id,state.m_state_name')
      ->join('master_state_tbl state', 'state.m_state_id = city.m_city_state', 'left')
      ->order_by('m_city_name')
      ->get('master_city_tbl city')->result();
  }

  // ===================== misc =======================//

}
