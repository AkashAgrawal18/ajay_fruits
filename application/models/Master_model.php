<?php date_default_timezone_set('Asia/Kolkata');
class Master_model extends CI_model
{


  //===================== machine =======================//
  public function all_itemgroup($type)
  {

    $res = $this->db->where('m_itgrp_type', $type)->order_by('m_itgrp_title')->get('master_itemgroup_tbl')->result();
    return $res;
  }

  public function all_active_itemgroup($type)
  {

    $res = $this->db->select('master_itemgroup_tbl.*')->where('m_itgrp_type', $type)->where('m_itgrp_status', 1)->order_by('m_itgrp_title')->get('master_itemgroup_tbl')->result();
    return $res;
  }

  public function insert_itemgroup()
  {

    $machineid = $this->input->post('m_itgrp_id');
    $machinename = $this->input->post('m_itgrp_title');

    $check = $this->db->where('m_itgrp_title', $machinename)->get('master_itemgroup_tbl')->result();

    if (!empty($check) && empty($machineid)) {

      return 'This Name Already Store in the Database';
    } else {
      $insert_data = array(
        "m_itgrp_title"    => $machinename,
        "m_itgrp_status"    => 1,
        "m_itgrp_type"    => $this->input->post('m_itgrp_type'),
        "m_itgrp_added_on" => date('Y-m-d H:i:s'),

      );

      if (!empty($machineid)) {
        $this->db->where('m_itgrp_id', $machineid)->update('master_itemgroup_tbl', $insert_data);
        return 2;
      } else {
        $this->db->insert('master_itemgroup_tbl', $insert_data);
        return 1;
      }
    }
  }


  public function get_edit_itemgroup($id)
  {
    $this->db->select('master_itemgroup_tbl.*');
    $this->db->where('m_itgrp_id', $id);
    $data = $this->db->get('master_itemgroup_tbl');
    return $data->row();
  }

  public function delete_itemgroup()
  {
    $this->db->where('m_itgrp_id', $this->input->post('delete_id'));
    $this->db->delete('master_itemgroup_tbl');
    return true;
  }
  //===================== machine =======================//

  //===================== item =======================//
  public function get_all_item($id='')
  {

    if(!empty($id)){
      $this->db->where('m_item_id', $id);
    }

    $res = $this->db->select('master_item_tbl.*,group.m_itgrp_title as groupname,crate.m_itgrp_title as cratetype,unit.m_itgrp_title as unitname')
      ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = master_item_tbl.m_item_group', 'left')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_item_tbl.m_item_crate', 'left')
      ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = master_item_tbl.m_item_unit', 'left')
      ->order_by('m_item_name')->get('master_item_tbl')->result();
    return $res;
  }

  public function insert_item()
  {

    $itemid = $this->input->post('m_item_id');
    $itemname = $this->input->post('m_item_name');

    //  $check = $this->db->where('m_item_name', $itemname)->get('master_item_tbl')->result();

    //  if (!empty($check) && empty($itemid)) {

    //    return 'item Name Already Store in the Database';
    //  } else {
    $insert_data = array(
      "m_item_name"    => $itemname,
      "m_item_group"    => $this->input->post('m_item_group'),
      "m_item_crate"    => $this->input->post('m_item_crate'),
      "m_item_unit"    => $this->input->post('m_item_unit'),
      "m_item_fright"    => $this->input->post('m_item_fright'),
      "m_item_comm"    => $this->input->post('m_item_comm'),
      "m_item_price"    => $this->input->post('m_item_price'),
      "m_item_status"    => 1,
      "m_item_added_on" => date('Y-m-d H:i:s'),

    );

    if (!empty($itemid)) {
      $this->db->where('m_item_id', $itemid)->update('master_item_tbl', $insert_data);
      return 2;
    } else {
      $this->db->insert('master_item_tbl', $insert_data);
      return 1;
    }
    //  }
  }


  public function get_edit_item($id)
  {
    $res = $this->db->select('master_item_tbl.*,group.m_itgrp_title as groupname,crate.m_itgrp_title as cratetype,unit.m_itgrp_title as unitname')
      ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = master_item_tbl.m_item_group', 'left')
      ->join('master_itemgroup_tbl as crate', 'group.m_itgrp_id = master_item_tbl.m_item_crate', 'left')
      ->join('master_itemgroup_tbl as unit', 'group.m_itgrp_id = master_item_tbl.m_item_unit', 'left');
    $this->db->where('m_item_id', $id);
    $data = $this->db->get('master_item_tbl');
    return $data->row();
  }

  public function delete_item()
  {
    $this->db->where('m_item_id', $this->input->post('delete_id'));
    $this->db->delete('master_item_tbl');
    return true;
  }
  //===================== problem =======================//

  //========================== group  =============================//
  public function get_all_group($type)
  {
    if($type == 2){
      $this->db->select('master_group_tbl.*,group.m_group_name as expense_group')
      ->join('master_group_tbl as group','group.m_group_id = master_group_tbl.m_group_group','left');
    }else {
      $this->db->select('*');
    }

    $this->db->where('master_group_tbl.m_group_type',$type);
    $this->db->order_by('master_group_tbl.m_group_name');
    $res = $this->db->get('master_group_tbl')->result();
    return $res;
  }
  public function get_all_active_group($type)
  {
    if($type == 2){
      $this->db->select('master_group_tbl.*,group.m_group_name as expense_group')
      ->join('master_group_tbl as group','group.m_group_id = master_group_tbl.m_group_group','left');
    }else {
      $this->db->select('*');
    }
    $this->db->where('master_group_tbl.m_group_type',$type);
    $this->db->where('master_group_tbl.m_group_status',1);
    $this->db->order_by('master_group_tbl.m_group_name');
    $res = $this->db->get('master_group_tbl')->result();
    return $res;
  }
  public function get_all_expenses()
  {
    $this->db->select('m_group_id,m_group_name,m_group_type,m_group_group');
    $this->db->where('m_group_type !=',1);
    $this->db->where('m_group_status',1);
    $res = $this->db->get('master_group_tbl')->result();
    return $res;
  }
  public function get_edit_group($edid)
  {
    $this->db->select('*');
    $this->db->where('m_group_id', $edid);
    $res = $this->db->get('master_group_tbl')->row();
    return $res;
  }
  public function insert_group()
  {

    if ($this->input->post('m_group_optp') == 1) {
      $openingbal = $this->input->post('m_group_opening') * -1;
    } else {
      $openingbal = $this->input->post('m_group_opening');
    }

    $s_data = array(
      "m_group_name" => $this->input->post('m_group_name'),
      "m_group_status" => $this->input->post('m_group_status'),
      "m_group_type" => $this->input->post('m_group_type'),
      "m_group_number" => $this->input->post('m_group_number') ?:'',
      "m_group_group" => $this->input->post('m_group_group') ?:'',
      "m_group_opening" => $openingbal ?:0,
      "m_group_remark" => $this->input->post('m_group_remark') ?:'',
      "m_group_added_on" => date('Y-m-d H:i'),
    );
    $id = $this->input->post('m_group_id');
    if (!empty($id)) {
      $this->db->where('m_group_id', $id)->update('master_group_tbl', $s_data);
      return 2;
    } else {
      $this->db->insert('master_group_tbl', $s_data);
      return 1;
    }
  }

  public function delete_group()
  {
    $this->db->where('m_group_id', $this->input->post('delete_id'));
    return $this->db->delete('master_group_tbl');
  }
  //========================== group  =============================//







  //========================== State  =============================//
  public function get_all_state()
  {
    $this->db->select('*');
    $this->db->order_by('m_state_name');
    $res = $this->db->get('master_state_tbl')->result();
    return $res;
  }
  public function get_edit_state($edid)
  {
    $this->db->select('*');
    $this->db->where('m_state_id', $edid);
    $res = $this->db->get('master_state_tbl')->row();
    return $res;
  }
  public function insert_state()
  {

    $s_data = array(
      "m_state_name" => $this->input->post('m_state_name'),
      "m_state_country" => 1,
      // "m_state_status" => $this->input->post('m_state_status'),
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
  //========================== State  =============================//


  //========================== City  =============================//

  public function get_all_city()
  {
    $this->db->select('*');
    $this->db->join('master_group_tbl', 'master_group_tbl.m_group_id = master_city_tbl.m_city_line', 'left');
    $this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_city_tbl.m_city_state', 'left');
    $this->db->order_by('m_city_name');
    $res = $this->db->get('master_city_tbl')->result();
    return $res;
  }
  public function get_edit_city($edid)
  {
    $this->db->select('*');
    $this->db->where('m_city_id', $edid);
    $res = $this->db->get('master_city_tbl')->row();
    return $res;
  }
  public function insert_city()
  {

    $s_data = array(
      "m_city_name" => $this->input->post('m_city_name'),
      "m_city_state" => $this->input->post('m_city_state'),
      "m_city_line" => $this->input->post('m_city_line'),
      // "m_city_status" => $this->input->post('m_city_status'),
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
  //Country State City
  public function get_active_country()
  {
    $this->db->select('*');
    $this->db->where('m_country_status', '1');
    $this->db->order_by('m_country_name');
    $res = $this->db->get('master_country_tbl')->result();
    return $res;
  }
  public function get_active_state()
  {
    $this->db->select('*');
    // $this->db->where('m_state_status', '1');
    $this->db->where('m_state_country', '1');
    $this->db->order_by('m_state_name');
    $res = $this->db->get('master_state_tbl')->result();
    return $res;
  }
  public function get_active_city()
  {
    $this->db->select('city.m_city_name,city.m_city_id,state.m_state_name');
    $this->db->join('master_state_tbl state', 'state.m_state_id = city.m_city_state', 'left');
    // $this->db->where('m_city_status', '1');
    $this->db->order_by('m_city_name');
    $res = $this->db->get('master_city_tbl city')->result();
    return $res;
  }
  //=========================================== city ===============================================//

  //===================== perm =======================//
  public function all_perm()
  {
    $res = $this->db->select('*')->get('master_permission_tbl')->result();
    return $res;
  }
  public function all_active_perm()
  {
    $res = $this->db->select('*')->where('m_perm_status', 1)->get('master_permission_tbl')->result();
    return $res;
  }

  public function insert_perm()
  {

    $permid = $this->input->post('m_perm_id');
    $permname = $this->input->post('m_perm_submodule_slug');

    $check = $this->db->where('m_perm_submodule_slug', $permname)->get('master_permission_tbl')->result();

    if (!empty($check) && empty($permid)) {

      return false;
    } else {
      $insert_data = array(
        "m_perm_name"    => $this->input->post('m_perm_name'),
        "m_perm_status"    => $this->input->post('m_perm_status'),
        "m_perm_module"    => $this->input->post('m_perm_module'),
        "m_perm_module_slug"    => $this->input->post('m_perm_module_slug'),
        "m_perm_submodule_slug"    => $permname,
        "m_perm_added_on" => date('Y-m-d H:i:s'),

      );

      if (!empty($permid)) {
        $this->db->where('m_perm_id', $permid)->update('master_permission_tbl', $insert_data);
        return 2;
      } else {
        $this->db->insert('master_permission_tbl', $insert_data);
        return 1;
      }
    }
  }


  public function get_edit_perm($id)
  {
    $this->db->select('*');
    $this->db->where('m_perm_id', $id);
    $data = $this->db->get('master_permission_tbl');
    return $data->row();
  }

  public function delete_perm()
  {
    $this->db->where('m_perm_id', $this->input->post('delete_id'));
    $this->db->delete('master_permission_tbl');
    return true;
  }
  //===================== perm =======================//


  //===================== userperm =======================//
  public function all_userperm_list()
  {
    $res = $this->db->select('*')->get('master_user_permission_tbl')->result();
    return $res;
  }

  public function insert_userperm()
  {

    $permid = $this->input->post('permid');
    $modulee = $this->input->post('modulee');
    $submodule = $this->input->post('submodule');
    $userpermid = $this->input->post('userpermid');
    $userid = $this->input->post('userid');
    $name = $this->input->post('name');
    $value = $this->input->post('value');


    $insert_data = array(
      "m_userperm_userId"    => $userid,
      "m_userperm_module"    => $modulee,
      "m_userperm_submodule"    => $submodule,
      "m_userperm_permId"    => $permid,
      $name    => $value,

    );

    if (!empty($userpermid)) {
      $this->db->where('m_userperm_id', $userpermid)->update('master_user_permission_tbl', $insert_data);
      return 2;
    } else {
      $insert_data["m_userperm_added_on"] = date('Y-m-d H:i:s');
      $this->db->insert('master_user_permission_tbl', $insert_data);
      return 1;
    }
  }

  // public function insertuserperm()
  // {

  //   $m_userperm_permId = $this->input->post('m_userperm_permId');
  //   $m_userperm_userId = $this->input->post('m_userperm_userId');
  //   $m_userperm_module = $this->input->post('m_userperm_module');
  //   $m_userperm_submodule = $this->input->post('m_userperm_submodule');
  //   $m_userperm_list = $this->input->post('m_userperm_list');
  //   $m_userperm_add = $this->input->post('m_userperm_add');
  //   $m_userperm_edit = $this->input->post('m_userperm_edit');
  //   $m_userperm_delete = $this->input->post('m_userperm_delete');
  //   $m_userperm_export = $this->input->post('m_userperm_export');
  //   $m_userperm_filter = $this->input->post('m_userperm_filter');

  //   for ($i = 0; $i < count($m_userperm_permId); $i++) {
  //     if (!empty($m_userperm_list[$i]) || !empty($m_userperm_add[$i]) || !empty($m_userperm_edit[$i]) || !empty($m_userperm_delete[$i]) || !empty($m_userperm_export[$i]) || !empty($m_userperm_filter[$i])) {

  //       if (!empty($m_userperm_list[$i])) {
  //         $userperm_list = 1;
  //       } else {
  //         $userperm_list = 0;
  //       }

  //       if (!empty($m_userperm_add[$i])) {
  //         $userperm_add = 1;
  //       } else {
  //         $userperm_add = 0;
  //       }

  //       if (!empty($m_userperm_edit[$i])) {
  //         $userperm_edit = 1;
  //       } else {
  //         $userperm_edit = 0;
  //       }

  //       if (!empty($m_userperm_delete[$i])) {
  //         $userperm_delete = 1;
  //       } else {
  //         $userperm_delete = 0;
  //       }

  //       if (!empty($m_userperm_export[$i])) {
  //         $userperm_export = 1;
  //       } else {
  //         $userperm_export = 0;
  //       }

  //       if (!empty($m_userperm_filter[$i])) {
  //         $userperm_filter = 1;
  //       } else {
  //         $userperm_filter = 0;
  //       }
  //       $insert_data = array(
  //         "m_userperm_userId"    => $m_userperm_userId,
  //         "m_userperm_module"    => $m_userperm_module[$i],
  //         "m_userperm_submodule"    => $m_userperm_submodule[$i],
  //         "m_userperm_permId"    => $m_userperm_permId[$i],
  //         "m_userperm_list"    => $userperm_list,
  //         "m_userperm_add"    => $userperm_add,
  //         "m_userperm_edit"    => $userperm_edit,
  //         "m_userperm_delete"    => $userperm_delete,
  //         "m_userperm_export"    => $userperm_export,
  //         "m_userperm_filter"    => $userperm_filter,
  //         "m_userperm_added_on" => date('Y-m-d H:i:s'),

  //       );
  //       $this->db->insert('master_user_permission_tbl', $insert_data);
  //     }
  //   }

  //   //  return $indata ;

  // }


  public function get_userperm_userId($id)
  {
    $this->db->select('*');
    $this->db->where('m_userperm_userId', $id);
    $this->db->order_by('m_userperm_permId', $id);
    $data = $this->db->get('master_user_permission_tbl');
    return $data->result();
  }

  // public function delete_userperm()
  // {
  //   $this->db->where('m_userperm_id', $this->input->post('delete_id'));
  //   $this->db->delete('master_user_permission_tbl');
  //   return true;
  // }
  //===================== userperm =======================//

  //======================================================= Designation =================================================//

  public function get_all_design()
  {
    $this->db->select('*');
    $this->db->order_by('m_design_name');
    $res = $this->db->get('master_designation_tbl')->result();
    return $res;
  }
  public function get_edit_design($edid)
  {
    $this->db->select('*');
    $this->db->where('m_design_id', $edid);
    $res = $this->db->get('master_designation_tbl')->row();
    return $res;
  }
  public function insert_design()
  {

    $s_data = array(
      "m_design_name" => $this->input->post('m_design_name'),
      "m_design_code" => $this->input->post('m_design_code'),
      "m_design_status" => $this->input->post('m_design_status'),
      "m_design_added_on" => date('Y-m-d H:i'),
    );
    $id = $this->input->post('m_design_id');
    if (!empty($id)) {
      $this->db->where('m_design_id', $id)->update('master_designation_tbl', $s_data);
      return 2;
    } else {
      $this->db->insert('master_designation_tbl', $s_data);
      return 1;
    }
  }


  public function delete_design()
  {
    $this->db->where('m_design_id', $this->input->post('delete_id'));
    return $this->db->delete('master_designation_tbl');
  }

  public function get_active_design()
  {
    $this->db->select('design.m_design_name,design.m_design_id');
    $this->db->where('m_design_status', '1');
    $this->db->order_by('m_design_name');
    $res = $this->db->get('master_designation_tbl design')->result();
    return $res;
  }

  //=======================================================/Designation=================================================//

  public function get_consignee_by_district()
  {
    $this->db->select('m_user_id,m_user_name,m_user_email,m_user_type,m_user_mobile');
    $this->db->where('m_user_city', $this->input->post('city_id'));
    $this->db->where('m_user_type', 3);
    $data = $this->db->get('master_users_tbl')->result();
    return $data;
  }


  public function get_warranty_machines($from_date, $to_date, $city_dtl, $machine_dtl, $consigne_dtl, $type)
  {
    $logged_user_id = $this->session->userdata('user_id');
    $logged_user_type = $this->session->userdata('user_type');

    if ($logged_user_type == 3) {
      $this->db->where('m_sale_consignee', $logged_user_id);
    } else if ($logged_user_type == 4) {
      $this->db->where('s_item_provider', $logged_user_id);
    }

    if (!empty($from_date) && !empty($to_date)) {
      $this->db->where('DATE_FORMAT(m_sale_date,"%Y-%m-%d")>=', $from_date);
      $this->db->where('DATE_FORMAT(m_sale_date,"%Y-%m-%d")<=', $to_date);
    }
    if (!empty($city_dtl)) {
      $this->db->where_in('Consignee.m_user_city', $city_dtl);
    }
    if (!empty($machine_dtl)) {
      $this->db->where_in('s_item_machine', $machine_dtl);
    }

    if (!empty($consigne_dtl)) {
      $this->db->where_in('m_sale_consignee', $consigne_dtl);
    }

    /// type =1 means warranty active and type=2 means warrenty expired
    if ($type == 1) {
      $this->db->where('s_item_warrantyend >=', date('Y-m-d'));
    } else {
      $this->db->where('s_item_warrantyend <', date('Y-m-d'));
    }

    $res = $this->db->select('master_sales_tbl.*,sale_items_tbl.*,Consignee.m_user_name as consignee,m_machine_title,Iprovider.m_user_name as instProviderName,Mprovider.m_user_name as mtnProviderName,consigne_city.m_city_name as consignee_district')
      ->join('master_sales_tbl', 'master_sales_tbl.m_sale_id = sale_items_tbl.s_item_saleid', 'left')
      ->join('master_machines_tbl', 'master_machines_tbl.m_machine_id = sale_items_tbl.s_item_machine', 'left')
      ->join('master_users_tbl Iprovider', 'Iprovider.m_user_id = sale_items_tbl.s_item_provider', 'left')
      ->join('master_users_tbl Mprovider', 'Mprovider.m_user_id = sale_items_tbl.s_item_mtgprovider', 'left')
      ->join('master_users_tbl Consignee', 'Consignee.m_user_id = master_sales_tbl.m_sale_consignee', 'left')
      ->join('master_city_tbl consigne_city', 'consigne_city.m_city_id = Consignee.m_user_city', 'left')
      ->where('s_item_wrty_perd !=', 0)
      ->order_by('m_sale_id', 'desc')
      ->get('sale_items_tbl')->result();
    return $res;
  }


  public function get_maintainance_list($from_date, $to_date, $city_dtl, $machine_dtl, $consigne_dtl)
  {
    $logged_user_id = $this->session->userdata('user_id');
    $logged_user_type = $this->session->userdata('user_type');

    if ($logged_user_type == 3) {
      $this->db->where('m_sale_consignee', $logged_user_id);
    } else if ($logged_user_type == 4) {
      $this->db->where('s_item_provider', $logged_user_id);
    }

    if (!empty($from_date) && !empty($to_date)) {
      $this->db->where('DATE_FORMAT(s_mtnc_date,"%Y-%m-%d")>=', $from_date);
      $this->db->where('DATE_FORMAT(s_mtnc_date,"%Y-%m-%d")<=', $to_date);
    }
    if (!empty($city_dtl)) {
      $this->db->where_in('Consignee.m_user_city', $city_dtl);
    }
    if (!empty($machine_dtl)) {
      $this->db->where_in('s_item_machine', $machine_dtl);
    }

    if (!empty($consigne_dtl)) {
      $this->db->where_in('m_sale_consignee', $consigne_dtl);
    }

    $res = $this->db->select('*')
      ->join('master_sales_tbl', 'master_sales_tbl.m_sale_id = sales_maintainance_tbl.s_mtnc_sales', 'left')
      ->join('sale_items_tbl', 'sale_items_tbl.s_item_id = sales_maintainance_tbl.s_mtnc_item', 'left')
      ->join('master_machines_tbl', 'master_machines_tbl.m_machine_id = sales_maintainance_tbl.s_mtnc_machine', 'left')
      ->join('master_users_tbl Consignee', 'Consignee.m_user_id = sales_maintainance_tbl.s_mtnc_cosignee', 'left')
      ->join('master_city_tbl consigne_city', 'consigne_city.m_city_id = Consignee.m_user_city', 'left')
      ->order_by('s_mtnc_date')
      ->get('sales_maintainance_tbl')->result();
    return $res;
  }

  public function get_payment_methods()
	{
		$this->db->select('m_group_id,m_group_name,m_group_type,m_group_status,m_group_remark');
		$this->db->where('(m_group_type = 3 OR m_group_type = 4)');
		$this->db->where('m_group_status', 1);
		return $this->db->get('master_group_tbl')->result();
	}


}
