<?php date_default_timezone_set('Asia/Kolkata');
class Master_model extends CI_model
{

  /**
   * Helper: returns the current branch ID from session.
   * All queries use this to scope data per branch.
   */
  private function is_superadmin()
  {
    return $this->session->userdata('user_type') == 8;
  }

  private function branch_id($override = null)
  {
    if (!$this->is_superadmin()) {
      return (int) $this->session->userdata('user_id');
    }
    return ($override !== null && $override !== '') ? (int) $override : null;
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
  // itemgroup, item, group, designation, perm, userperm are shared/config
  // tables per-branch — branch column added per the ALTER statements.

  public function all_itemgroup($type, $branch_id = null)
  {
    $this->where_branch('m_itgrp_branch', $branch_id);
    return $this->db->where('m_itgrp_type', $type)
      ->order_by('m_itgrp_title')
      ->get('master_itemgroup_tbl')->result();
  }

  public function all_active_itemgroup($type, $branch_id = null)
  {
    $this->where_branch('m_itgrp_branch', $branch_id);
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

    $this->where_branch('m_itgrp_branch', $branch);
    $check = $this->db->where('m_itgrp_title', $machinename)->get('master_itemgroup_tbl')->result();

    if (!empty($check) && empty($machineid)) {
      return 'This Name Already Store in the Database';
    }

    $insert_data = array(
      "m_itgrp_title"    => $machinename,
      "m_itgrp_status"   => 1,
      "m_itgrp_type"     => $this->input->post('m_itgrp_type'),
      "m_itgrp_added_on" => date('Y-m-d H:i:s'),
    );

    if (!empty($machineid)) {
      $this->where_branch('m_itgrp_branch', $branch);
      $this->db->where('m_itgrp_id', $machineid)->update('master_itemgroup_tbl', $insert_data);
      return 2;
    } else {
      $insert_data['m_itgrp_branch'] = $branch;
      $this->db->insert('master_itemgroup_tbl', $insert_data);
      return 1;
    }
  }

  public function get_edit_itemgroup($id, $branch_id = null)
  {
    $this->where_branch('m_itgrp_branch', $branch_id);
    return $this->db->select('master_itemgroup_tbl.*')
      ->where('m_itgrp_id', $id)
      ->get('master_itemgroup_tbl')->row();
  }

  public function delete_itemgroup($branch_id = null)
  {
    $this->where_branch('m_itgrp_branch', $branch_id);
    $this->db->where('m_itgrp_id', $this->input->post('delete_id'));
    return $this->db->delete('master_itemgroup_tbl');
  }

  // ===================== item =======================//
  // master_item_tbl has m_item_branch — branch scoping applied on every
  // select/insert/update/delete, with an optional override so a
  // superadmin can target a specific branch.

  public function get_all_item($id = '', $branch_id = null)
  {
    if (!empty($id)) {
      $this->db->where('m_item_id', $id);
    }
    $this->where_branch('master_item_tbl.m_item_branch', $branch_id);
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
      $this->where_branch('m_item_branch', $branch);
      $this->db->where('m_item_id', $itemid)->update('master_item_tbl', $insert_data);
      return 2;
    } else {
      $insert_data['m_item_branch'] = $branch;
      $this->db->insert('master_item_tbl', $insert_data);
      return 1;
    }
  }

  public function get_edit_item($id, $branch_id = null)
  {
    $this->where_branch('master_item_tbl.m_item_branch', $branch_id);
    return $this->db->select('master_item_tbl.*,group.m_itgrp_title as groupname,crate.m_itgrp_title as cratetype,unit.m_itgrp_title as unitname')
      ->where('m_item_id', $id)
      ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = master_item_tbl.m_item_group', 'left')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_item_tbl.m_item_crate', 'left')
      ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = master_item_tbl.m_item_unit', 'left')
      ->get('master_item_tbl')->row();
  }

  public function delete_item($branch_id = null)
  {
    $this->where_branch('m_item_branch', $branch_id);
    $this->db->where('m_item_id', $this->input->post('delete_id'));
    $this->db->delete('master_item_tbl');
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
      $s_data['m_group_branch'] = $branch;
      $this->db->insert('master_group_tbl', $s_data);
      return 1;
    }
  }

  public function delete_group($branch_id = null)
  {
    $this->where_branch('m_group_branch', $branch_id);
    $this->db->where('m_group_id', $this->input->post('delete_id'));
    return $this->db->delete('master_group_tbl');
  }

  public function get_payment_methods($branch_id = null)
  {
    $this->where_branch('m_group_branch', $branch_id);
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
    return $this->db->delete('master_state_tbl');
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
    return $this->db->delete('master_city_tbl');
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

  // ===================== perm =======================//
  // Permissions are branch-scoped (m_perm_branch).

  public function all_perm($branch_id = null)
  {
    $this->where_branch('m_perm_branch', $branch_id);
    return $this->db->select('*')->get('master_permission_tbl')->result();
  }

  public function all_active_perm($branch_id = null)
  {
    $this->where_branch('m_perm_branch', $branch_id);
    return $this->db->select('*')
      ->where('m_perm_status', 1)
      ->get('master_permission_tbl')->result();
  }

  public function insert_perm()
  {
    $permid   = $this->input->post('m_perm_id');
    $permname = $this->input->post('m_perm_submodule_slug');
    $branch   = $this->branch_id($this->input->post('m_perm_branch'));

    $this->where_branch('m_perm_branch', $branch);
    $check = $this->db->where('m_perm_submodule_slug', $permname)->get('master_permission_tbl')->result();

    if (!empty($check) && empty($permid)) return false;

    $insert_data = array(
      "m_perm_name"           => $this->input->post('m_perm_name'),
      "m_perm_status"         => $this->input->post('m_perm_status'),
      "m_perm_module"         => $this->input->post('m_perm_module'),
      "m_perm_module_slug"    => $this->input->post('m_perm_module_slug'),
      "m_perm_submodule_slug" => $permname,
      "m_perm_added_on"       => date('Y-m-d H:i:s'),
    );

    if (!empty($permid)) {
      $this->where_branch('m_perm_branch', $branch);
      $this->db->where('m_perm_id', $permid)->update('master_permission_tbl', $insert_data);
      return 2;
    } else {
      $insert_data['m_perm_branch'] = $branch;
      $this->db->insert('master_permission_tbl', $insert_data);
      return 1;
    }
  }

  public function get_edit_perm($id, $branch_id = null)
  {
    $this->where_branch('m_perm_branch', $branch_id);
    return $this->db->select('*')
      ->where('m_perm_id', $id)
      ->get('master_permission_tbl')->row();
  }

  public function delete_perm($branch_id = null)
  {
    $this->where_branch('m_perm_branch', $branch_id);
    $this->db->where('m_perm_id', $this->input->post('delete_id'));
    $this->db->delete('master_permission_tbl');
    return true;
  }

  // ===================== userperm =======================//
  // master_user_permission_tbl has m_userperm_branch.

  public function all_userperm_list($branch_id = null)
  {
    $this->where_branch('m_userperm_branch', $branch_id);
    return $this->db->select('*')->get('master_user_permission_tbl')->result();
  }

  public function insert_userperm()
  {
    $permid     = $this->input->post('permid');
    $modulee    = $this->input->post('modulee');
    $submodule  = $this->input->post('submodule');
    $userpermid = $this->input->post('userpermid');
    $userid     = $this->input->post('userid');
    $name       = $this->input->post('name');
    $value      = $this->input->post('value');
    $branch     = $this->branch_id($this->input->post('branch'));

    $insert_data = array(
      "m_userperm_userId"    => $userid,
      "m_userperm_module"    => $modulee,
      "m_userperm_submodule" => $submodule,
      "m_userperm_permId"    => $permid,
      $name                  => $value,
    );

    if (!empty($userpermid)) {
      $this->where_branch('m_userperm_branch', $branch);
      $this->db->where('m_userperm_id', $userpermid)->update('master_user_permission_tbl', $insert_data);
      return 2;
    } else {
      $insert_data['m_userperm_branch']   = $branch;
      $insert_data['m_userperm_added_on'] = date('Y-m-d H:i:s');
      $this->db->insert('master_user_permission_tbl', $insert_data);
      return 1;
    }
  }

  public function get_userperm_userId($id, $branch_id = null)
  {
    $this->where_branch('m_userperm_branch', $branch_id);
    return $this->db->select('*')
      ->where('m_userperm_userId', $id)
      ->order_by('m_userperm_permId', 'ASC')
      ->get('master_user_permission_tbl')->result();
  }

  // ===================== Designation =======================//
  // master_designation_tbl has m_design_branch.

  public function get_all_design($branch_id = null)
  {
    $this->where_branch('m_design_branch', $branch_id);
    return $this->db->select('*')
      ->order_by('m_design_name')
      ->get('master_designation_tbl')->result();
  }

  public function get_edit_design($edid, $branch_id = null)
  {
    $this->where_branch('m_design_branch', $branch_id);
    return $this->db->select('*')
      ->where('m_design_id', $edid)
      ->get('master_designation_tbl')->row();
  }

  public function insert_design()
  {
    $branch = $this->branch_id($this->input->post('m_design_branch'));
    $s_data = array(
      "m_design_name"     => $this->input->post('m_design_name'),
      "m_design_code"     => $this->input->post('m_design_code'),
      "m_design_status"   => $this->input->post('m_design_status'),
      "m_design_added_on" => date('Y-m-d H:i'),
    );
    $id = $this->input->post('m_design_id');
    if (!empty($id)) {
      $this->where_branch('m_design_branch', $branch);
      $this->db->where('m_design_id', $id)->update('master_designation_tbl', $s_data);
      return 2;
    } else {
      $s_data['m_design_branch'] = $branch;
      $this->db->insert('master_designation_tbl', $s_data);
      return 1;
    }
  }

  public function delete_design($branch_id = null)
  {
    $this->where_branch('m_design_branch', $branch_id);
    $this->db->where('m_design_id', $this->input->post('delete_id'));
    return $this->db->delete('master_designation_tbl');
  }

  public function get_active_design($branch_id = null)
  {
    $this->where_branch('m_design_branch', $branch_id);
    return $this->db->select('design.m_design_name,design.m_design_id')
      ->where('m_design_status', '1')
      ->order_by('m_design_name')
      ->get('master_designation_tbl design')->result();
  }

  // ===================== misc =======================//

}
