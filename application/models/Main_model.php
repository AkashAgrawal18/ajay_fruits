<?php date_default_timezone_set('Asia/Kolkata');

class Main_model extends CI_model
{

  public function get_user_list($type, $from_date, $to_date, $city_dtl, $orderby = '', $search = '')
  {

    if (!empty($from_date) && !empty($to_date)) {
      $this->db->where('DATE_FORMAT(m_user_added_on,"%Y-%m-%d")>=', $from_date);
      $this->db->where('DATE_FORMAT(m_user_added_on,"%Y-%m-%d")<=', $to_date);
    }
    if (!empty($type)) {
      $this->db->where('m_user_type', $type);
    }
    if (!empty($city_dtl)) {
      $this->db->where_in('m_user_city', $city_dtl);
    }

    if (!empty($search)) {
      $wh = "(m_user_name LIKE '%$search%' OR m_user_mobile LIKE '%$search%' OR m_city_name LIKE '%$search%')";
      $this->db->where($wh);
    }


    $this->db->select('master_users_tbl.*,m_city_name,m_state_name');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = master_users_tbl.m_user_city', 'left');
    $this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_users_tbl.m_user_state', 'left');
    // $this->db->join('master_group_tbl', 'master_group_tbl.m_group_id = master_users_tbl.m_user_group', 'left');

    if (!empty($orderby)) {
      if ($orderby == 1) {
        $this->db->order_by('m_user_name');
      } else if ($orderby == 2) {
      } else if ($orderby == 3) {
        $this->db->order_by('m_city_name');
      } else {
        $this->db->order_by('m_city_name');
      }
    }
    $this->db->group_by('m_user_id');
    $res = $this->db->get('master_users_tbl')->result();
    return $res;
  }

  public function get_active_user_list($type)
  {

    if (!empty($type)) {
      $this->db->where('m_user_type', $type);
    }
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = master_users_tbl.m_user_city', 'left');
    $this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_users_tbl.m_user_state', 'left');
    $res = $this->db->order_by('m_user_name')->get('master_users_tbl')->result();
    return $res;
  }
  public function get_active_users($type)
  {
    if (!empty($type)) {
      $this->db->where('m_user_type', $type);
    }
    return $this->db->select('m_user_id,m_user_name,m_user_mobile,m_user_group,m_user_type')->where('m_user_status', 1)->get('master_users_tbl')->result();
  }



  public function get_user_dtl($id)
  {
    $this->db->select('*');
    $this->db->where('m_user_id', $id);
    // $this->db->join('master_designation_tbl','master_designation_tbl.m_desig_id = master_users_tbl.m_user_desig','left');
    $this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_users_tbl.m_user_state', 'left');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = master_users_tbl.m_user_city', 'left');
    $res = $this->db->get('master_users_tbl')->row();

    return $res;
  }

  public function get_user_group_dtl($group_id)
  {
    $this->db->select('*');
    $this->db->where('m_user_group', $group_id);
    // $this->db->join('master_designation_tbl','master_designation_tbl.m_desig_id = master_users_tbl.m_user_desig','left');
    $this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_users_tbl.m_user_state', 'left');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = master_users_tbl.m_user_city', 'left');
    $res = $this->db->get('master_users_tbl')->row();

    return $res;
  }

  public function insert_user()
  {

    $userid = $this->input->post('m_user_id');

    // for 1st image upload code.
    //   if(!empty($_FILES['m_user_image']['name'])){
    //   $config['file_name'] = $_FILES['m_user_image']['name'];
    //   $config['upload_path'] = 'uploads/users';
    //   $config['allowed_types'] = 'jpg|jpeg|png';
    //   $config['remove_spaces'] = TRUE;
    //   $config['file_name'] = $_FILES['m_user_image']['name'];
    //   //Load upload library and initialize configuration
    //   $this->load->library('upload',$config);
    //   $this->upload->initialize($config);
    //   if($this->upload->do_upload('m_user_image')){
    //     $uploadData = $this->upload->data();  
    //     if (!empty($update_data['m_user_image'])) { 
    //       if(file_exists($config['m_user_image'].$update_data['m_user_image'])){
    //       unlink($config['upload_path'].$update_data['m_user_image']); /* deleting Image */
    //       } 
    //     }
    //     $m_user_image = $uploadData['file_name'];
    //   }
    // }
    // else{
    //   $m_user_image = $this->input->post('m_user_image1');
    // }
    if (!empty($this->input->post('m_user_group'))) {
      $group = implode(',', $this->input->post('m_user_group'));
    } else {
      $group = '';
    }

    if ($this->input->post('m_user_typeopening') == 1) {
      $openingbal = $this->input->post('m_user_opening') * -1;
    } else {
      $openingbal = $this->input->post('m_user_opening');
    }

    if ($this->input->post('ct10') == 1) {
      $cbv10 = $this->input->post('cbv10') * -1;
    } else {
      $cbv10 = $this->input->post('cbv10');
    }
    if ($this->input->post('ct20') == 1) {
      $cbv20 = $this->input->post('cbv20') * -1;
    } else {
      $cbv20 = $this->input->post('cbv20');
    }
    if ($this->input->post('ct25') == 1) {
      $cbv25 = $this->input->post('cbv25') * -1;
    } else {
      $cbv25 = $this->input->post('cbv25');
    }
    $data = array(

      "m_user_name" => $this->input->post('m_user_name'),
      "m_user_mobile" => $this->input->post('m_user_mobile'),
      // "m_user_phoneno" => $this->input->post('m_user_phoneno'),
      "m_user_remark" => $this->input->post('m_user_remark'),
      "m_user_contractPerd" => $this->input->post('m_user_contractPerd'),
      "m_user_pan_no" => $this->input->post('m_user_pan_no'),
      "m_user_accountno" => $this->input->post('m_user_accountno'),
      "m_user_adharno" => $this->input->post('m_user_adharno'),
      "m_user_state" => $this->input->post('m_user_state'),
      "m_user_city" => $this->input->post('m_user_city'),
      "m_user_address" => $this->input->post('m_user_address'),
      "m_user_trademark" => $this->input->post('m_user_trademark'),
      "m_user_status" => 1,
      "m_user_group" => $group,
      "m_user_type" => $this->input->post('m_user_type'),
      "m_user_design" => $this->input->post('m_user_design') ?: 0,
      "m_user_opening" => $openingbal ?: 0,
      "m_user_crateOP" => $cbv10 . ',' . $cbv20 . ',' . $cbv25,
      "m_user_login_allow" => $this->input->post('m_user_login_allow') ?: 0,
      "m_user_loginid" => $this->input->post('m_user_loginid') ?: '',
      "m_user_password" => $this->input->post('m_user_password') ?: '',
    );

    if (!empty($userid)) {
      $data['m_user_updated_by'] = $this->session->userdata('user_id');
      $data['m_user_updated_on'] = date('Y-m-d H:i:s');
      $this->db->where('m_user_id', $userid)->update('master_users_tbl', $data);
      return 2;
    } else {
      $data['m_user_added_by'] = $this->session->userdata('user_id');
      $data['m_user_added_on'] = date('Y-m-d H:i:s');
      $this->db->insert('master_users_tbl', $data);
      return 1;
    }
  }

  public function delete_user()
  {
    $this->db->where('m_user_id', $this->input->post('delete_id'));
    return $this->db->delete('master_users_tbl');
  }

  public function get_cust_list($from_date = '', $to_date = '', $city_dtl = '', $orderby = '', $group = '')
  {

    if (!empty($from_date) && !empty($to_date)) {
      $this->db->where('DATE_FORMAT(m_cust_added_on,"%Y-%m-%d")>=', $from_date);
      $this->db->where('DATE_FORMAT(m_cust_added_on,"%Y-%m-%d")<=', $to_date);
    }
    if ($group == 'o') {
      $this->db->where('m_cust_group', 0);
    } else if (!empty($group)) {
      $this->db->where('m_cust_group', $group);
    }
    if (!empty($city_dtl)) {
      $this->db->where('m_cust_city', $city_dtl);
    }
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = master_customer_tbl.m_cust_city', 'left');
    $this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_customer_tbl.m_cust_state', 'left');
    $this->db->join('master_group_tbl', 'master_group_tbl.m_group_id = master_customer_tbl.m_cust_group', 'left');
    if (!empty($orderby)) {
      if ($orderby == 1) {
        $this->db->order_by('m_cust_name');
      } else if ($orderby == 2) {
      } else if ($orderby == 3) {
        $this->db->order_by('m_city_name');
      } else {
        $this->db->order_by('m_city_name');
      }
    }
    $res = $this->db->get('master_customer_tbl')->result();
    return $res;
  }

  public function get_cust_active_list($cust_id = '')
  {
    if (!empty($cust_id)) {
      $this->db->where('m_cust_id', $cust_id);
    }
    $res = $this->db->select('m_cust_id,m_cust_name,m_cust_hndiname,m_cust_group,m_cust_mobile,m_cust_balance,m_cust_10bal,m_cust_20bal,m_cust_25bal')->where('m_cust_status', 1)->order_by('m_cust_name')->get('master_customer_tbl')->result();
    return $res;
  }

  public function get_cust_dtl($id)
  {
    $this->db->select('*');
    $this->db->where('m_cust_id', $id);
    // $this->db->join('master_designation_tbl','master_designation_tbl.m_desig_id = master_customer_tbl.m_cust_desig','left');
    $this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_customer_tbl.m_cust_state', 'left');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = master_customer_tbl.m_cust_city', 'left');
    $this->db->join('master_group_tbl', 'master_group_tbl.m_group_id = master_customer_tbl.m_cust_group', 'left');
    $res = $this->db->get('master_customer_tbl')->row();

    return $res;
  }
  public function get_all_customers($from_date = '', $to_date = '', $city_dtl = '', $search = '')
  {
    $result = array();
    $this->db->select('m_cust_id,m_cust_name,m_cust_hndiname,m_cust_mobile,m_cust_opening,m_cust_crateOP,m_cust_image,m_cust_remark,m_cust_pan_no,m_cust_accountno,m_cust_balance,m_cust_10bal,m_cust_20bal,m_cust_25bal,m_state_name,m_city_name,m_cust_address,m_cust_adharno,m_cust_trademark,m_cust_contractPerd,m_cust_status,m_cust_added_on,m_group_name,m_cust_group');
    $this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = mct.m_cust_state', 'left');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = mct.m_cust_city', 'left');
    $this->db->join('master_group_tbl', 'master_group_tbl.m_group_id = mct.m_cust_group', 'left');
    // $this->db->where_in('m_cust_group', explode(',', $this->input->post('m_user_group')));

    if (!empty($from_date)) {
      $this->db->where('DATE_FORMAT(m_cust_added_on,"%Y-%m-%d")>=', $from_date);
    }
    if (!empty($to_date)) {
      $this->db->where('DATE_FORMAT(m_cust_added_on,"%Y-%m-%d")<=', $to_date);
    }

    if (!empty($city_dtl)) {
      $this->db->where('m_cust_city', $city_dtl);
    }

    if (!empty($search)) {
      $wh = "(m_cust_name LIKE '%$search%' OR m_cust_mobile LIKE '%$search%' OR m_group_name LIKE '%$search%')";
      $this->db->where($wh);
    }

    $this->db->order_by('m_cust_name');
    $custo_list = $this->db->get('master_customer_tbl mct')->result();

    // if (!empty($custo_list)) {
    //   foreach ($custo_list as $key) {
    //     // $get_ledge = $this->get_opening_balance($key->m_cust_id, date('Y-m-d'));
    //     $res = array(
    //       "m_cust_id" => $key->m_cust_id,
    //       "m_cust_name" => $key->m_cust_name,
    //       "m_cust_hndiname" => $key->m_cust_hndiname,
    //       "m_cust_mobile" => $key->m_cust_mobile,
    //       "m_cust_image" => $key->m_cust_image,
    //       "m_cust_remark" => $key->m_cust_remark,
    //       "m_cust_pan_no" => $key->m_cust_pan_no,
    //       "m_cust_accountno" => $key->m_cust_accountno,
    //       "m_state_name" => $key->m_state_name,
    //       "m_city_name" => $key->m_city_name,
    //       "m_cust_address" => $key->m_cust_address,
    //       "m_cust_adharno" => $key->m_cust_adharno,
    //       "m_cust_trademark" => $key->m_cust_trademark,
    //       "m_cust_contractPerd" => $key->m_cust_contractPerd,
    //       "m_cust_status" => $key->m_cust_status,
    //       "m_cust_added_on" => $key->m_cust_added_on,
    //       "m_group_name" => $key->m_group_name,
    //       "m_cust_grou" => $key->m_cust_group,
    //       "m_cust_opening" => $key->m_cust_opening,
    //       "m_cust_crateOP" => $key->m_cust_crateOP,
    //       // "total_given_amount" => (int)$get_ledge['grand_total'],
    //       // "total_recieved_amount" => (int)$get_ledge['amount_rcvd'],
    //       // "total_balance" => (int)($get_ledge['balance_amount']),
    //       // "total_crate_balance" => (int)($get_ledge['balance_crate']),
    //     );

    //     // $all_crates = $this->Master_model->all_itemgroup(3);

    //     // foreach ($all_crates as $itect) {
    //     // 	$crateledger = $this->get_crate_ledger($itect->m_itgrp_id, $key->m_cust_id);
    //     // 	$res['recived-' . $itect->m_itgrp_title] = (int)$crateledger['crate_rcvd'];
    //     // 	$res['given-' . $itect->m_itgrp_title] = (int)$crateledger['crate_given'];
    //     // 	$res['Balance-' . $itect->m_itgrp_title] = (int)$crateledger['crate_given'] - (int)$crateledger['crate_rcvd'];
    //     // }

    //     $result[] = $res;
    //   }
    // }
    return $custo_list;
  }


  public function delete_customer()
  {
    $this->db->where('m_cust_id', $this->input->post('delete_id'));
    $this->db->delete('master_customer_tbl');

    $this->db->where('m_recvd_customer', $this->input->post('delete_id'));
    $this->db->delete('master_recieved_tbl');

    $this->db->where('m_sale_customer', $this->input->post('delete_id'));
    $this->db->delete('master_sales_tbl');
    return true;
  }

  public function get_customer_balance($cust_id, $to_date = '', $today = '')
  {

    // $opening_bal = $this->db->select('m_cust_opening,m_cust_crateOP')->where('m_cust_id', $cust_id)->get('master_customer_tbl')->row();

    $sub_total = 0;
    $total_expense = 0;
    $grand_total = 0;
    $crate_total = 0;
    $total_given = 0;
    $total_recieved = 0;

    if ($today == 1) {
      if (!empty($to_date)) {
        $this->db->where('m_sale_date', $to_date);
      }
    } else {
      if (!empty($to_date)) {
        $this->db->where('m_sale_date <=', $to_date);
      }
    }
    $salequery = $this->db->select('sum(m_sale_qty) as tqty,sum(m_sale_total) as sub_total,sum(m_sale_crate) as tcrate,(m_sale_comm+m_sale_fright+m_sale_hamali+m_sale_others) as texpense')->where('m_sale_customer', $cust_id)->group_by('m_sale_spo')->get('master_sales_tbl')->result();
    if (!empty($salequery)) {
      foreach ($salequery as $key) {
        $sub_total += $key->sub_total;
        $total_expense += $key->texpense;
        $grand_total += ($key->sub_total + $key->texpense);
      }
    }

    if ($today == 1) {
      if (!empty($to_date)) {
        $this->db->where('m_recvd_date', $to_date);
      }
    } else {
      if (!empty($to_date)) {
        $this->db->where('m_recvd_date <=', $to_date);
      }
    }

    $amountrcvdquery = $this->db->select('sum(m_recvd_amount) as tamountrcvd')->where('m_recvd_customer', $cust_id)->where('m_recvd_account', 1)->where('m_recvd_type', 1)->get('master_recieved_tbl')->result();

    if ($today == 1) {
      if (!empty($to_date)) {
        $this->db->where('m_voucher_date', $to_date);
      }
    } else {
      if (!empty($to_date)) {
        $this->db->where('m_voucher_date <=', $to_date);
      }
    }
    $vouch_amtcdrt = $this->db->select('sum(m_voucher_amount) as tamountcdt')->where('m_voucher_accountid', $cust_id)->where('m_voucher_account', 1)->where('m_voucher_type', 1)->where('m_voucher_status', 1)->get('master_voucher_tbl')->result();
    if ($today == 1) {
      if (!empty($to_date)) {
        $this->db->where('m_voucher_date', $to_date);
      }
    } else {
      if (!empty($to_date)) {
        $this->db->where('m_voucher_date <=', $to_date);
      }
    }
    $vouch_amtdbt = $this->db->select('sum(m_voucher_amount) as tamountdbt')->where('m_voucher_accountid', $cust_id)->where('m_voucher_account', 1)->where('m_voucher_type', 2)->where('m_voucher_status', 1)->get('master_voucher_tbl')->result();

    $balance_amt = (($grand_total + $vouch_amtdbt[0]->tamountdbt) - ($amountrcvdquery[0]->tamountrcvd + $vouch_amtcdrt[0]->tamountcdt));


    $result = array(
      "sub_total" => $sub_total,
      "total_expense" => $total_expense,
      "grand_total" => $grand_total,
      "amount_rcvd" => $amountrcvdquery[0]->tamountrcvd ?: 0,
      "discount_amt" => $vouch_amtcdrt[0]->tamountcdt ?: 0,
      "balance_amount" => $balance_amt,
    );

    $all_crates = $this->Master_model->all_itemgroup(3);
    // $openin_crate_bal = explode(',', $opening_bal->m_cust_crateOP);
    foreach ($all_crates as $key) {
      $crateledger = $this->get_crate_ledger($key->m_itgrp_id, $cust_id, $to_date, $today);
      $crate_total += ((int) $crateledger['crate_given'] - (int) $crateledger['crate_rcvd']);

      $total_given += (int) $crateledger['crate_given'];
      $total_recieved += (int) $crateledger['crate_rcvd'];

      // if ($key->m_itgrp_title == '10 KG') {
      //   $crattype_bal = isset($openin_crate_bal[0]) ? $openin_crate_bal[0] : 0;
      // } else if ($key->m_itgrp_title == '20 KG') {
      //   $crattype_bal = isset($openin_crate_bal[1]) ? $openin_crate_bal[1] : 0;
      // } else if ($key->m_itgrp_title == '25 KG') {
      //   $crattype_bal = isset($openin_crate_bal[2]) ? $openin_crate_bal[2] : 0;
      // }

      $res = array(
        'name' => $key->m_itgrp_title,
        'recived' => (int) $crateledger['crate_rcvd'],
        'given' => (int) $crateledger['crate_given'],
        'balance' => ((int) $crateledger['crate_given']) - (int) $crateledger['crate_rcvd'],
      );
      $result['crateitems'][] = $res;
    }

    $result['crate_given'] = $total_given;
    $result['crate_recieved'] = $total_recieved;
    $result['balance_crate'] = $crate_total;

    return $result;
  }

  function get_crate_balance($cust_id)
  {

    $opening_bal = $this->db->select('m_cust_opening,m_cust_crateOP')->where('m_cust_id', $cust_id)->get('master_customer_tbl')->row();

    $all_crates = $this->Master_model->all_itemgroup(3);
    $openin_crate_bal = explode(',', $opening_bal->m_cust_crateOP);
    foreach ($all_crates as $itect) {
      $crateledger = $this->get_crate_ledger($itect->m_itgrp_id, $cust_id);

      if ($itect->m_itgrp_title == '10 KG') {
        $crattype_bal = isset($openin_crate_bal[0]) ? $openin_crate_bal[0] : 0;
      } else if ($itect->m_itgrp_title == '20 KG') {
        $crattype_bal = isset($openin_crate_bal[1]) ? $openin_crate_bal[1] : 0;
      } else if ($itect->m_itgrp_title == '25 KG') {
        $crattype_bal = isset($openin_crate_bal[2]) ? $openin_crate_bal[2] : 0;
      }

      $res = array(
        'name' => $itect->m_itgrp_title,
        'recived' => (int) $crateledger['crate_rcvd'],
        'given' => (int) $crateledger['crate_given'],
        'balance' => $crattype_bal + (int) $crateledger['crate_given'] - (int) $crateledger['crate_rcvd'],
      );
      $result[] = $res;
    }
    return $result;
  }

  public function get_crate_ledger($crate_id, $cust_id, $from_date = '', $today = '')
  {

    if ($today == 1) {
      if (!empty($from_date)) {
        $this->db->where('m_sale_date', $from_date);
      }
    } else {
      if (!empty($from_date)) {
        $this->db->where('m_sale_date <=', $from_date);
      }
    }


    $crategiven = $this->db->select('sum(m_sale_crate) as tcrate,m_itgrp_title')->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')->where('m_sale_customer', $cust_id)->where('m_item_crate', $crate_id)->group_by('m_item_crate')->get('master_sales_tbl')->result();

    if ($today == 1) {
      if (!empty($from_date)) {
        $this->db->where('m_recvd_date', $from_date);
      }
    } else {
      if (!empty($from_date)) {
        $this->db->where('m_recvd_date <=', $from_date);
      }
    }

    $cratercvdquery = $this->db->select('sum(m_recvd_qty) as tcrateqty,m_itgrp_title')->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left')->where('m_recvd_customer', $cust_id)->where('m_recvd_type', 2)->where('m_recvd_crate', $crate_id)->group_by('m_recvd_crate')->get('master_recieved_tbl')->result();
    $result = array(
      "crate_rcvd" => $cratercvdquery ? $cratercvdquery[0]->tcrateqty : 0,
      "crate_given" => $crategiven ? $crategiven[0]->tcrate : 0,
      "crate_balance" => (($crategiven ? $crategiven[0]->tcrate : 0) - ($cratercvdquery ? $cratercvdquery[0]->tcrateqty : 0)),
    );
    return $result;
  }



  public function get_opening_balance($cust_id, $from_date)
  {

    $opening_bal = $this->get_cust_dtl($cust_id);

    $sub_total = 0;
    $total_expense = 0;
    $grand_total = 0;
    $crate_total = 0;
    $total_given = 0;
    $total_recieved = 0;

    if (!empty($from_date)) {
      $this->db->where('m_sale_date <=', $from_date);
    }

    $salequery = $this->db->select('sum(m_sale_total) as sub_total,(m_sale_comm+m_sale_fright+m_sale_hamali+m_sale_others) as texpense')->where('m_sale_customer', $cust_id)->group_by('m_sale_spo')->get('master_sales_tbl')->result();
    if (!empty($salequery)) {
      foreach ($salequery as $key) {
        $sub_total += $key->sub_total;
        $total_expense += $key->texpense;
        $grand_total += ($key->sub_total + $key->texpense);
      }
    }

    if (!empty($from_date)) {
      $this->db->where('m_recvd_date <=', $from_date);
    }
    $amountrcvdquery = $this->db->select('sum(m_recvd_amount) as tamountrcvd')->where('m_recvd_customer', $cust_id)->where('m_recvd_account', 1)->where('m_recvd_type', 1)->get('master_recieved_tbl')->result();

    if (!empty($from_date)) {
      $this->db->where('m_voucher_date <=', $from_date);
    }
    $vouch_amtcdrt = $this->db->select('sum(m_voucher_amount) as tamountcdt')->where('m_voucher_accountid', $cust_id)->where('m_voucher_account', 1)->where('m_voucher_type', 1)->where('m_voucher_status', 1)->get('master_voucher_tbl')->result();
    if (!empty($from_date)) {
      $this->db->where('m_voucher_date <=', $from_date);
    }
    $vouch_amtdbt = $this->db->select('sum(m_voucher_amount) as tamountdbt')->where('m_voucher_accountid', $cust_id)->where('m_voucher_account', 1)->where('m_voucher_type', 2)->where('m_voucher_status', 1)->get('master_voucher_tbl')->result();

    $balance_amt = $opening_bal->m_cust_opening + (($grand_total + $vouch_amtdbt[0]->tamountdbt) - ($amountrcvdquery[0]->tamountrcvd + $vouch_amtcdrt[0]->tamountcdt));

    $result = array(
      "cust_name" => $opening_bal->m_cust_name,
      "m_cust_hndiname" => $opening_bal->m_cust_hndiname,
      "cust_mobile" => $opening_bal->m_cust_mobile,
      "sub_total" => $sub_total,
      "total_expense" => $total_expense,
      "grand_total" => $grand_total,
      "amount_rcvd" => $amountrcvdquery[0]->tamountrcvd ?: 0,
      "balance_amount" => $balance_amt,
    );

    $all_crates = $this->Master_model->all_itemgroup(3);
    $openin_crate_bal = explode(',', $opening_bal->m_cust_crateOP);
    foreach ($all_crates as $key) {
      $crateledger = $this->get_crate_ledger($key->m_itgrp_id, $cust_id, $from_date);
      $crate_total += ((int) $crateledger['crate_given'] - (int) $crateledger['crate_rcvd']);

      $total_given += (int) $crateledger['crate_given'];
      $total_recieved += (int) $crateledger['crate_rcvd'];

      if ($key->m_itgrp_title == '10 KG') {
        $crattype_bal = isset($openin_crate_bal[0]) ? $openin_crate_bal[0] : 0;
      } else if ($key->m_itgrp_title == '20 KG') {
        $crattype_bal = isset($openin_crate_bal[1]) ? $openin_crate_bal[1] : 0;
      } else if ($key->m_itgrp_title == '25 KG') {
        $crattype_bal = isset($openin_crate_bal[2]) ? $openin_crate_bal[2] : 0;
      }

      $res = array(
        'name' => $key->m_itgrp_title,
        'recived' => (int) $crateledger['crate_rcvd'],
        'given' => (int) $crateledger['crate_given'],
        'balance' => ((int) $crattype_bal + (int) $crateledger['crate_given'] - (int) $crateledger['crate_rcvd']),
      );
      $result['crateitems'][] = $res;
    }

    $result['crate_given'] = $total_given;
    $result['crate_recieved'] = $total_recieved;
    $result['balance_crate'] = array_sum(explode(',', $opening_bal->m_cust_crateOP)) + $crate_total;


    return $result;
  }


  public function insert_cust()
  {

    $custid = $this->input->post('m_cust_id');
    $check = $this->db->where('m_cust_loginid', $this->input->post('m_cust_loginid'))->where('m_cust_id !=', $custid)->get('master_customer_tbl')->num_rows();
    if ($check > 0) {
      return 3;
    }

    if ($this->input->post('m_cust_typeopening') == 1) {
      $openingbal = $this->input->post('m_cust_opening') * -1;
    } else {
      $openingbal = $this->input->post('m_cust_opening');
    }
    if ($this->input->post('ct10') == 1) {
      $cbv10 = $this->input->post('cbv10') * -1;
    } else {
      $cbv10 = $this->input->post('cbv10');
    }
    if ($this->input->post('ct20') == 1) {
      $cbv20 = $this->input->post('cbv20') * -1;
    } else {
      $cbv20 = $this->input->post('cbv20');
    }
    if ($this->input->post('ct25') == 1) {
      $cbv25 = $this->input->post('cbv25') * -1;
    } else {
      $cbv25 = $this->input->post('cbv25');
    }

    $data = array(

      "m_cust_name" => $this->input->post('m_cust_name'),
      "m_cust_hndiname" => $this->input->post('m_cust_hndiname'),
      "m_cust_mobile" => $this->input->post('m_cust_mobile'),
      // "m_cust_phoneno" => $this->input->post('m_cust_phoneno'),
      "m_cust_remark" => $this->input->post('m_cust_remark'),
      "m_cust_contractPerd" => $this->input->post('m_cust_contractPerd'),
      // "m_cust_pan_no" => $this->input->post('m_cust_pan_no'),
      "m_cust_accountno" => $this->input->post('m_cust_accountno'),
      // "m_cust_adharno" => $this->input->post('m_cust_adharno'),
      "m_cust_state" => $this->input->post('m_cust_state'),
      "m_cust_city" => $this->input->post('m_cust_city'),
      "m_cust_address" => $this->input->post('m_cust_address'),
      "m_cust_trademark" => $this->input->post('m_cust_trademark'),
      "m_cust_group" => $this->input->post('m_cust_group'),
      "m_cust_loginid" => $this->input->post('m_cust_loginid'),
      "m_cust_password" => $this->input->post('m_cust_password'),
      "m_cust_opening" => $openingbal,
      "m_cust_crateOP" => $cbv10 . ',' . $cbv20 . ',' . $cbv25,
      "m_cust_status" => 1,

    );

    if (!empty($custid)) {
      $data['m_cust_updated_by'] = $this->session->userdata('user_id');
      $data['m_cust_updated_on'] = date('Y-m-d H:i:s');
      $this->db->where('m_cust_id', $custid)->update('master_customer_tbl', $data);
      return 2;
    } else {
      $data['m_cust_added_by'] = $this->session->userdata('user_id');
      $data['m_cust_added_on'] = date('Y-m-d H:i:s');
      $this->db->insert('master_customer_tbl', $data);
      return 1;
    }
  }



  //===================== customer_group =======================//

  public function get_customer_group_list($group = '')
  {


    if (!empty($group)) {
      $this->db->where('m_cust_group', $group);
    }

    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = master_customer_tbl.m_cust_city', 'left');
    $this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_customer_tbl.m_cust_state', 'left');
    $this->db->join('master_group_tbl', 'master_group_tbl.m_group_id = master_customer_tbl.m_cust_group');
    $res = $this->db->order_by('m_cust_name')->get('master_customer_tbl')->result();
    return $res;
  }


  //===================== customer_group =======================//

  //===================== custgrp =======================//
  public function all_custgrp()
  {
    $res = $this->db->select('*')
      ->join('master_users_tbl mut', 'mut.m_user_id = master_custgroup_tbl.m_custgrp_user', 'left')
      ->join('master_customer_tbl mct', 'mct.m_cust_id = master_custgroup_tbl.m_custgrp_customer', 'left')

      ->order_by('m_custgrp_name')->get('master_custgroup_tbl')->result();
    return $res;
  }

  public function insert_custgrp()
  {

    $custgrp_id = $this->input->post('m_custgrp_id');
    $custgrp_customer = $this->input->post('m_custgrp_customer');

    foreach ($custgrp_customer as $key => $cau) {

      $check = $this->db->where('m_custgrp_user', $this->input->post('m_custgrp_user'))->where('m_custgrp_customer', $cau)->where('m_custgrp_name', $this->input->post('m_custgrp_name'))->get('master_custgroup_tbl')->result();

      $insert_data = array(
        "m_custgrp_status" => 1,
        "m_custgrp_name" => $this->input->post('m_custgrp_name'),
        "m_custgrp_user" => $this->input->post('m_custgrp_user'),
        "m_custgrp_customer" => $cau,

      );

      if (!empty($custgrp_id[$key])) {

        $this->db->where('m_custgrp_id', $custgrp_id[$key])->update('master_custgroup_tbl', $insert_data);
        $res = 2;
      } else {
        if (empty($check)) {
          $insert_data['m_custgrp_addedby'] = $this->session->userdata('user_id');
          $insert_data['m_custgrp_code'] = date('dmi') . $this->input->post('m_custgrp_user');
          $insert_data['m_custgrp_added_on'] = date('Y-m-d H:i:s');
          $this->db->insert('master_custgroup_tbl', $insert_data);
          $res = 1;
        }
      }
    }
    return $res;
  }


  public function get_edit_custgrp($id)
  {
    $this->db->select('*')
      ->join('master_users_tbl mut', 'mut.m_user_id = master_custgroup_tbl.m_custgrp_user', 'left')
      ->join('master_customer_tbl mct', 'mct.m_cust_id = master_custgroup_tbl.m_custgrp_customer', 'left');
    $this->db->where('m_custgrp_id', $id);
    $data = $this->db->get('master_custgroup_tbl');
    return $data->row();
  }

  public function delete_custgrp()
  {
    $this->db->where('m_custgrp_id', $this->input->post('delete_id'));
    $this->db->delete('master_custgroup_tbl');
    return true;
  }
  //===================== custgrp =======================//

  //===================== item_issue =======================//
  public function get_edit_item_issue($id='',$lot_no = '',$type = '')
  {
    $this->db->select('staff_itemissue_tbl.*,m_item_name,group.m_itgrp_title as groupname,crate.m_itgrp_title as cratetype,si_issue_user,unit.m_itgrp_title as unitname,m_user_name,m_user_mobile,(select m_purcs_lot from master_purchase_tbl where si_issue_lotno = m_purcs_id) as pur_lotno,(select m_purcs_available from master_purchase_tbl where si_issue_lotno = m_purcs_id) as available_stock'); //new change
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = staff_itemissue_tbl.si_issue_item', 'left')
      ->join('master_users_tbl mut', 'mut.m_user_id = staff_itemissue_tbl.si_issue_user', 'left')
      ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
      ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');
      if(!empty($lot_no)){
        $this->db->where('si_issue_lotno', $lot_no);
      }
      if(!empty($type)){
        $this->db->where('si_issue_type', $type);
      }
      if(!empty($id)){
        $this->db->where('si_issue_spo', $id);
      }
    $this->db->where('si_issue_status', 1);
    $this->db->order_by('m_item_name');
    return $this->db->get('staff_itemissue_tbl')->result();
  }

  public function issue_item_group($from_date = '', $todate = '', $staff = '', $lot_no = '')
  {
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = staff_itemissue_tbl.si_issue_user', 'left')
      ->join('master_city_tbl', 'master_city_tbl.m_city_id = mut.m_user_city', 'left')
      ->join('master_state_tbl', 'master_state_tbl.m_state_id = mut.m_user_state', 'left')
      ->join('master_group_tbl', 'master_group_tbl.m_group_id = mut.m_user_group', 'left')
      ->join('master_users_tbl as issueby', 'issueby.m_user_id = staff_itemissue_tbl.si_issue_added_by', 'left');

    if (!empty($from_date)) {
      $this->db->where('DATE_FORMAT(si_issue_date,"%Y-%m-%d")>=', $from_date);
    }
    if (!empty($todate)) {
      $this->db->where('DATE_FORMAT(si_issue_date,"%Y-%m-%d")<=', $todate);
    }

    if (!empty($staff)) {
      $this->db->where_in('si_issue_user', $staff);
    }
    if (!empty($lot_no)) {
      $this->db->select('si_issue_id,si_issue_spo,si_issue_trackno,si_issue_qty,si_issue_type,si_issue_date,si_issue_weight,si_issue_user,si_issue_qty as tqty,si_issue_weight as twght,si_issue_total as tamount,si_issue_crate as tcrate,mut.m_user_name,mut.m_user_mobile,mut.m_user_address,m_city_name,m_state_name,m_group_name,issueby.m_user_name as issuebyname');

      $this->db->where('si_issue_lotno', $lot_no);
    }
    $this->db->where('si_issue_status', 1);
    $this->db->order_by('si_issue_date', 'desc');
    if (empty($lot_no)) {
      $this->db->select('si_issue_spo,si_issue_trackno,si_issue_qty,si_issue_type,si_issue_date,si_issue_weight,si_issue_user,sum(si_issue_qty) as tqty,sum(si_issue_weight) as twght,sum(si_issue_total) as tamount,sum(si_issue_crate) as tcrate,mut.m_user_name,mut.m_user_mobile,mut.m_user_address,m_city_name,m_state_name,m_group_name,issueby.m_user_name as issuebyname');

      $this->db->group_by('si_issue_spo');
      $this->db->group_by('si_issue_date');
      $this->db->group_by('si_issue_user');
    }
    return $this->db->get('staff_itemissue_tbl')->result();
  }

  public function insert_issue_item()
  {

    $issue_id = $this->input->post('si_issue_id');
    $issue_item = $this->input->post('si_issue_item');
    $pre_qty = $this->input->post('pre_item_qty');
    $issue_qty = $this->input->post('si_issue_qty');
    $issue_weight = $this->input->post('si_issue_weight');
    $issue_crate = $this->input->post('si_issue_crate');
    $issue_price = $this->input->post('si_issue_price');
    $issue_total = $this->input->post('si_issue_total');
    $issue_lotno = $this->input->post('si_issue_lotno');

    $issue_dtl = $this->db->select('si_issue_spo')->where('si_issue_type', 1)->order_by('si_issue_id', 'desc')->group_by('si_issue_spo')->get('staff_itemissue_tbl')->result();
    if (!empty($issue_dtl)) {
      $spo_coun = explode('/', $issue_dtl[0]->si_issue_spo);
      $issue_spo = ((int) $spo_coun[0] + 1) . '/' . date('dm', strtotime($this->input->post('si_issue_date')));
    } else {
      $issue_spo = '1/' . date('dm', strtotime($this->input->post('si_issue_date')));
    }


    foreach ($issue_item as $key => $cau) {

      $insert_data = array(
        "si_issue_date" => $this->input->post('si_issue_date'),
        "si_issue_trackno" => $this->input->post('si_issue_trackno'),
        "si_issue_type" => $this->input->post('si_issue_type'),
        "si_issue_user" => $this->input->post('si_issue_user'),
        "si_issue_item" => $cau,
        "si_issue_qty" => $issue_qty[$key],
        "si_issue_lotno" => $issue_lotno[$key],
        "si_issue_weight" => $issue_weight[$key],
        "si_issue_crate" => $issue_crate[$key],
        "si_issue_price" => $issue_price[$key],
        "si_issue_total" => $issue_total[$key],

      );

      if (!empty($issue_id[$key])) {
        $new_qty = ((int) $issue_qty[$key] - (int) $pre_qty[$key]);
        $this->db->where('si_issue_id', $issue_id[$key])->update('staff_itemissue_tbl', $insert_data);
        $this->update_cust_balance(null, null, $new_qty, $cau, $issue_lotno[$key]);
        $res = 2;
      } else {
        if (!empty($this->input->post('si_issue_spo'))) {
          $insert_data['si_issue_spo'] = $this->input->post('si_issue_spo');
        } else {
          $insert_data['si_issue_spo'] = $issue_spo;
        }
        $insert_data['si_issue_status'] = 1;
        $insert_data['si_issue_added_by'] = $this->session->userdata('user_id');
        $insert_data['si_issue_added_on'] = date('Y-m-d H:i');
        $this->db->insert('staff_itemissue_tbl', $insert_data);

        $this->update_cust_balance(null, null, $issue_qty[$key], $cau, $issue_lotno[$key]);
        $res = 1;
      }
    }
    return $res;
  }

  public function lotwise_insert_issue()
  {

    $issue_date = $this->input->post('si_issue_date');
    $issue_crate = $this->input->post('si_issue_crate');
    $issue_item = $this->input->post('si_issue_item');
    $issue_user = $this->input->post('si_issue_user');
    $issue_qty = $this->input->post('si_issue_qty');
    $issue_weight = $this->input->post('si_issue_weight');
    $issue_price = $this->input->post('si_issue_price');
    $issue_lotno = $this->input->post('si_issue_lotno');
    $issue_total = $this->input->post('si_issue_total');

    foreach ($issue_user as $key => $cau) {

      $issue_dtl = $this->db->select('si_issue_spo')->where('si_issue_type', 1)->order_by('si_issue_id', 'desc')->group_by('si_issue_spo')->get('staff_itemissue_tbl')->result();
      $spo_coun = explode('/', $issue_dtl[0]->si_issue_spo);
      if (!empty($issue_dtl)) {
        $issue_spo = ((int) $spo_coun[0] + 1) . '/' . date('dm', strtotime($issue_date[$key]));
      } else {
        $issue_spo = '1/' . date('dm', strtotime($issue_date[$key]));
      }


      $insert_data = array(
        "si_issue_date" => $issue_date[$key],
        // "si_issue_trackno"    => $this->input->post('si_issue_trackno'),
        "si_issue_type" => 1,
        "si_issue_user" => $cau,
        "si_issue_item" => $issue_item[$key],
        "si_issue_qty" => $issue_qty[$key],
        "si_issue_lotno" => $issue_lotno[$key],
        "si_issue_weight" => $issue_weight[$key],
        "si_issue_crate" => $issue_crate[$key],
        "si_issue_price" => $issue_price[$key],
        "si_issue_total" => $issue_total[$key],

      );


      $insert_data['si_issue_status'] = 1;
      $insert_data['si_issue_added_by'] = $this->session->userdata('user_id');
      $insert_data['si_issue_spo'] = $issue_spo;
      $insert_data['si_issue_added_on'] = date('Y-m-d H:i');
      $res = $this->db->insert('staff_itemissue_tbl', $insert_data);
      $this->update_cust_balance(null, null, $issue_qty[$key], $issue_item[$key], $issue_lotno[$key]);
    }
    return $res;
  }


  public function delete_issue_item()
  {

    $issue_datil = $this->db->select('*')->where('si_issue_spo', $this->input->post('delete_id'))->get('staff_itemissue_tbl')->result();

    foreach ($issue_datil as $kry) {

      $this->update_cust_balance(null, null, ($kry->si_issue_qty * (-1)), $kry->si_issue_item, $kry->si_issue_lotno);
    }

    $res = $this->db->set('si_issue_status', 0)->where('si_issue_spo', $this->input->post('delete_id'))->update('staff_itemissue_tbl');

    return $res;
  }
  public function delete_issue_item_id()
  {
    $issue_datil = $this->db->select('*')->where('si_issue_id', $this->input->post('delete_id'))->get('staff_itemissue_tbl')->row();

    $this->update_cust_balance(null, null, ($issue_datil->si_issue_qty * (-1)), $issue_datil->si_issue_item, $issue_datil->si_issue_lotno);

    return $this->db->set('si_issue_status', 0)->where('si_issue_id', $this->input->post('delete_id'))->update('staff_itemissue_tbl');
  }
  //===================== item_issue =======================//

  //===================== sales =======================//
  public function get_edit_sales($id='',$lot_no = '')
  {
    $this->db->select('master_sales_tbl.*,m_item_name,m_item_fright,group.m_itgrp_title as groupname,crate.m_itgrp_title as cratetype,m_sale_customer,unit.m_itgrp_title as unitname,m_cust_name,m_cust_mobile,(select m_purcs_lot from master_purchase_tbl where m_sale_lot = m_purcs_id) as pur_lotno,(select m_purcs_available from master_purchase_tbl where m_sale_lot = m_purcs_id) as available_stock,m_user_name');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
      ->join('master_customer_tbl mut', 'mut.m_cust_id = master_sales_tbl.m_sale_customer', 'left')
      ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
      ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left')
      ->join('master_users_tbl', 'master_users_tbl.m_user_id = master_sales_tbl.m_sale_user', 'left');
      if(!empty($lot_no)){
        $this->db->where('m_sale_lot', $lot_no);
      }
      if(!empty($id)){
        $this->db->where('m_sale_spo', $id);
      }
    $this->db->order_by('m_item_name');
    return $this->db->get('master_sales_tbl')->result();
  }

  public function sales_group($from_date = '', $todate = '', $customers = '', $group = '', $search_in = '', $order_by = '', $lot_no = '')
  {

    if (!empty($from_date)) {
      $this->db->where('DATE_FORMAT(m_sale_date,"%Y-%m-%d")>=', $from_date);
    }
    if (!empty($todate)) {
      $this->db->where('DATE_FORMAT(m_sale_date,"%Y-%m-%d")<=', $todate);
    }

    if (!empty($search_in)) {
      $wh = "(m_user_name LIKE '%$search_in%' OR m_user_mobile LIKE '%$search_in%' OR mut.m_cust_name LIKE '%$search_in%' OR mut.m_cust_mobile LIKE '%$search_in%')";
      $this->db->where($wh);
    }

    if (!empty($group)) {
      $this->db->where('m_cust_group', $group);
    }

    if (!empty($customers)) {
      $this->db->where('m_sale_customer', $customers);
    }

    if (!empty($lot_no)) {
      $this->db->select('m_sale_spo,m_sale_trackno,m_sale_date,m_sale_customer,m_sale_note,m_cust_name,m_cust_mobile,m_cust_address,m_sale_price,m_sale_qty as tqty,m_sale_weight as twght,m_sale_crate as tcreate,m_user_name,m_sale_total as total_amount,m_sale_comrate,m_sale_comm,m_sale_fright,m_sale_hamali,m_sale_others,(m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) as total_expense,m_city_name,m_state_name,m_group_name');
      $this->db->where('m_sale_lot', $lot_no);
    } else {
      $this->db->select('m_sale_spo,m_sale_trackno,m_sale_date,m_sale_customer,m_sale_note,m_cust_name,m_cust_mobile,m_cust_address,sum(m_sale_qty) as tqty,sum(m_sale_weight) as twght,sum(m_sale_crate) as tcreate,m_user_name,sum(m_sale_total) as total_amount,m_sale_comrate,m_sale_comm,m_sale_fright,m_sale_hamali,m_sale_others,(m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) as total_expense,m_city_name,m_state_name,m_group_name');
      $this->db->group_by('m_sale_spo');
      $this->db->group_by('m_sale_date');
    }
    $this->db->join('master_customer_tbl mut', 'mut.m_cust_id = master_sales_tbl.m_sale_customer', 'left')
      ->join('master_city_tbl', 'master_city_tbl.m_city_id = mut.m_cust_city', 'left')
      ->join('master_state_tbl', 'master_state_tbl.m_state_id = mut.m_cust_state', 'left')
      ->join('master_group_tbl', 'master_group_tbl.m_group_id = mut.m_cust_group', 'left')
      ->join('master_users_tbl', 'master_users_tbl.m_user_id = master_sales_tbl.m_sale_user', 'left');

    if (!empty($order_by)) {
      $this->db->order_by('m_sale_date', $order_by);
    } else {
      $this->db->order_by('m_sale_date', 'desc');
    }



    // $this->db->group_by('m_sale_user');
    return $this->db->get('master_sales_tbl')->result();
  }

  public function insert_sales()
  {

    $issue_id = $this->input->post('m_sale_id');
    $sales = $this->input->post('m_sale_item');
    $pre_qty = $this->input->post('pre_item_qty');
    $issue_qty = $this->input->post('m_sale_qty');
    $issue_weight = $this->input->post('m_sale_weight');
    $issue_crate = $this->input->post('m_sale_crate');
    $issue_price = $this->input->post('m_sale_price');
    $m_sale_total = $this->input->post('m_sale_total');
    $m_sale_lot = $this->input->post('m_sale_lot');

    $sale_dtl = $this->db->select('m_sale_spo')->order_by('m_sale_id', 'desc')->group_by('m_sale_spo')->get('master_sales_tbl')->result();
    if (!empty($sale_dtl)) {
      $spo_coun = explode('/', $sale_dtl[0]->m_sale_spo);
      $sale_spo = ((int) $spo_coun[0] + 1) . '/' . date('dm', strtotime($this->input->post('m_sale_date')));
    } else {
      $sale_spo = '1/' . date('dm', strtotime($this->input->post('m_sale_date')));
    }

    $saleTotalAmt = 0;

    foreach ($sales as $key => $cau) {

      if (!empty($issue_weight[$key]) && $issue_weight[$key] != "0.00" && $issue_weight[$key] != "0") {
        $sale_total = ($issue_weight[$key] * $issue_price[$key]);
      } else {
        $sale_total = ($issue_qty[$key] * $issue_price[$key]);
      }
      $saleTotalAmt += (float) $sale_total;
      $insert_data = array(
        "m_sale_date" => $this->input->post('m_sale_date'),
        "m_sale_trackno" => $this->input->post('m_sale_trackno'),
        "m_sale_customer" => $this->input->post('m_sale_customer'),
        "m_sale_voucher" => $this->input->post('m_sale_voucher'),
        "m_sale_comrate" => $this->input->post('m_sale_comrate'),
        "m_sale_comm" => $this->input->post('m_sale_comm'),
        "m_sale_fright" => $this->input->post('m_sale_fright'),
        "m_sale_hamali" => $this->input->post('m_sale_hamali'),
        "m_sale_others" => $this->input->post('m_sale_others'),
        "m_sale_note" => $this->input->post('m_sale_note'),
        "m_sale_user" => $this->input->post('m_sale_user') ?: '',
        "m_sale_item" => $cau,
        "m_sale_qty" => $issue_qty[$key],
        "m_sale_weight" => $issue_weight[$key],
        "m_sale_crate" => $issue_crate[$key],
        "m_sale_price" => $issue_price[$key],
        "m_sale_total" => $sale_total,
        "m_sale_lot" => $m_sale_lot[$key],

      );

      if (!empty($issue_id[$key])) {
        $insert_data['m_sale_updatedby'] = $this->session->userdata('user_id');
        $insert_data['m_sale_updatedon'] = date('Y-m-d H:i');
        $new_qty = ((int) $pre_qty[$key] - (int) $issue_qty[$key]) * (-1);
        $this->db->where('m_sale_id', $issue_id[$key])->update('master_sales_tbl', $insert_data);
        $res = 2;
        if ($this->input->post('m_sale_customer') == $this->input->post('precust')) {
          $this->update_cust_balance($this->input->post('m_sale_customer'), null, $new_qty, $cau, $m_sale_lot[$key]);
        } else {
          $this->update_cust_balance($this->input->post('m_sale_customer'), null, (int) $issue_qty[$key], $cau, $m_sale_lot[$key]);
          $this->update_cust_balance($this->input->post('precust'), null, ((int) $pre_qty[$key] * (-1)), $cau, $m_sale_lot[$key]);
        }
      } else {

        if (!empty($this->input->post('m_sale_spo'))) {
          $insert_data['m_sale_spo'] = $this->input->post('m_sale_spo');
        } else {
          $insert_data['m_sale_spo'] = $sale_spo;
        }

        $insert_data['m_sale_added_by'] = $this->session->userdata('user_id');

        $insert_data['m_sale_added_on'] = date('Y-m-d H:i');
        $this->db->insert('master_sales_tbl', $insert_data);
        $res = 1;
        $this->update_cust_balance($this->input->post('m_sale_customer'), null, $issue_qty[$key], $cau, $m_sale_lot[$key]); //new change
      }
    }
    $saleTotalAmt += ((float) $this->input->post('m_sale_comm') + (float) $this->input->post('m_sale_fright') + (float) $this->input->post('m_sale_hamali') + (float) $this->input->post('m_sale_others'));
    if (empty($this->input->post('m_sale_spo'))) {
      $this->update_cust_balance($this->input->post('m_sale_customer'), $saleTotalAmt);

      //   $this->Api_Model->send_sale_sms($sale_spo);
    } else {

      $new_amt = ($saleTotalAmt - (float) $this->input->post('pre_grand_total'));
      if ($this->input->post('m_sale_customer') == $this->input->post('precust')) {
        $this->update_cust_balance($this->input->post('m_sale_customer'), $new_amt);
      } else {
        $this->update_cust_balance($this->input->post('m_sale_customer'), $saleTotalAmt);
        $this->update_cust_balance($this->input->post('precust'), ((float) $this->input->post('pre_grand_total') * (-1)));
      }
    }
    return $res;
  }

  public function lotwise_insert_sales()
  {

    $sale_date = $this->input->post('m_sale_date');
    $sale_crate = $this->input->post('m_sale_crate');
    $sale_item = $this->input->post('m_sale_item');
    $sale_customer = $this->input->post('m_sale_customer');
    $sale_qty = $this->input->post('m_sale_qty');
    $sale_weight = $this->input->post('m_sale_weight');
    $sale_price = $this->input->post('m_sale_price');
    $sale_total = $this->input->post('m_sale_total');
    $sale_fright = $this->input->post('m_sale_fright');
    $sale_note = $this->input->post('m_sale_note');
    $sale_lot = $this->input->post('m_sale_lot');
    $sale_user = $this->input->post('m_sale_user');


    foreach ($sale_customer as $key => $cau) {

      $sale_dtl = $this->db->select('m_sale_spo')->order_by('m_sale_id', 'desc')->group_by('m_sale_spo')->get('master_sales_tbl')->result();
      if (!empty($sale_dtl)) {
        $spo_coun = explode('/', $sale_dtl[0]->m_sale_spo);
        $sale_spo = ((int) $spo_coun[0] + 1) . '/' . date('dm', strtotime($sale_date[$key]));
      } else {
        $sale_spo = '1/' . date('dm', strtotime($sale_date[$key]));
      }

      if (!empty($sale_weight[$key]) && $sale_weight[$key] != "0.00" && $sale_weight[$key] != "0") {
        $sale_total = ($sale_weight[$key] * $sale_price[$key]);
      } else {
        $sale_total = ($sale_qty[$key] * $sale_price[$key]);
      }

      $insert_data = array(
        "m_sale_date" => $sale_date[$key],
        // "m_sale_trackno"    => $this->input->post('m_sale_trackno'),
        "m_sale_customer" => $cau,
        // "m_sale_comrate"    => $this->input->post('m_sale_comrate'),
        // "m_sale_comm"    => $this->input->post('m_sale_comm'),
        "m_sale_fright" => $sale_fright[$key],
        // "m_sale_hamali"    => $this->input->post('m_sale_hamali'),
        // "m_sale_others"    => $this->input->post('m_sale_others'),
        "m_sale_note" => $sale_note[$key],
        "m_sale_user" => $sale_user[$key] ?: '',
        "m_sale_item" => $sale_item[$key],
        "m_sale_qty" => $sale_qty[$key],
        "m_sale_weight" => $sale_weight[$key],
        "m_sale_crate" => $sale_crate[$key],
        "m_sale_price" => $sale_price[$key],
        "m_sale_total" => $sale_total,
        "m_sale_lot" => $sale_lot[$key],

      );

      $insert_data['m_sale_added_by'] = $this->session->userdata('user_id');
      $insert_data['m_sale_spo'] = $sale_spo;
      $insert_data['m_sale_added_on'] = date('Y-m-d H:i');
      $res = $this->db->insert('master_sales_tbl', $insert_data);

      $saleTotalAmt = ((float) $sale_total + (float) $sale_fright[$key]); //new change
      $this->update_cust_balance($cau, $saleTotalAmt, $sale_qty[$key], $sale_item[$key], $sale_lot[$key]); //new change
      // $this->Api_Model->send_sale_sms($sale_spo);
    }
    return $res;
  }

  public function delete_sales()
  {

    $sale_datil = $this->db->select('*')->where('m_sale_spo', $this->input->post('delete_id'))->get('master_sales_tbl')->result();

    $pre_grandtotal = ($sale_datil[0]->m_sale_comm + $sale_datil[0]->m_sale_fright + $sale_datil[0]->m_sale_hamali + $sale_datil[0]->m_sale_others);
    foreach ($sale_datil as $kry) {
      $pre_grandtotal += $kry->m_sale_total;
      $this->update_cust_balance($kry->m_sale_customer, null, ($kry->m_sale_qty * (-1)), $kry->m_sale_item, $kry->m_sale_lot);
    }
    $this->update_cust_balance($sale_datil[0]->m_sale_customer, ($pre_grandtotal * (-1)));

    $this->db->where('m_sale_spo', $this->input->post('delete_id'));
    $this->db->delete('master_sales_tbl');
    return true;
  }

  public function delete_sales_id()
  {
    $sale_datil = $this->db->select('m_sale_qty,m_sale_lot,m_sale_item,m_sale_customer,m_sale_total')->where('m_sale_id', $this->input->post('delete_id'))->get('master_sales_tbl')->row();

    $this->update_cust_balance($sale_datil->m_sale_customer, ($sale_datil->m_sale_total * (-1)), ($sale_datil->m_sale_qty * (-1)), $sale_datil->m_sale_item, $sale_datil->m_sale_lot);

    $this->db->where('m_sale_id', $this->input->post('delete_id'));
    $this->db->delete('master_sales_tbl');

    return true;
  }
  //===================== sales =======================//

  //===================== purchase =======================//
  public function get_purchase_expense($id)
  {
    $this->db->select('master_expenses_tbl.*,expense.m_group_name as expense_name');
    $this->db->join('master_group_tbl as expense', 'expense.m_group_id = master_expenses_tbl.m_exp_name', 'left');
    $this->db->where('m_exp_accno', $id);
    return $this->db->get('master_expenses_tbl')->result();
  }

  public function get_edit_purchase($id)
  {
    $this->db->select('master_purchase_tbl.*,m_item_name,group.m_itgrp_title as groupname,crate.m_itgrp_title as cratetype,m_purcs_suplier,unit.m_itgrp_title as unitname,m_user_name,m_user_mobile');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_purchase_tbl.m_purcs_item', 'left')
      ->join('master_users_tbl mut', 'mut.m_user_id = master_purchase_tbl.m_purcs_suplier', 'left')
      ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
      ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');
    $this->db->where('m_purcs_spo', $id);
    $this->db->order_by('m_item_name');
    return $this->db->get('master_purchase_tbl')->result();
  }

  public function purchase_group($from_date = '', $todate = '', $supplier = '', $order_by = '')
  {

    if (!empty($from_date)) {
      $this->db->where('DATE_FORMAT(m_purcs_date,"%Y-%m-%d")>=', $from_date);
    }
    if (!empty($todate)) {
      $this->db->where('DATE_FORMAT(m_purcs_date,"%Y-%m-%d")<=', $todate);
    }

    if (!empty($supplier)) {
      $this->db->where_in('m_purcs_suplier', $supplier);
    }

    $this->db->select('m_purcs_spo,m_purcs_truckno,m_purcs_note,m_purcs_date,m_purcs_suplier,mut.m_user_name as supplier_name,mut.m_user_mobile as supplier_mobile,sum(m_purcs_qty) as tqty,sum(m_purcs_weight) as twght,sum(m_purcs_crate) as tcrate,master_users_tbl.m_user_name,sum(m_purcs_total) as total_amount,m_purcs_comm,m_purcs_comrate,m_purcs_fright,m_purcs_hamali,m_purcs_charity,m_purcs_packaging,m_purcs_loading,m_purcs_advance,m_purcs_others,(m_purcs_comm + m_purcs_fright + m_purcs_hamali + m_purcs_charity + m_purcs_packaging + m_purcs_loading + m_purcs_advance + m_purcs_others) as total_expense,mut.m_user_address as supplier_address,m_city_name,m_state_name');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_purchase_tbl.m_purcs_suplier', 'left')
      ->join('master_city_tbl', 'master_city_tbl.m_city_id = mut.m_user_city', 'left')
      ->join('master_state_tbl', 'master_state_tbl.m_state_id = mut.m_user_state', 'left')

      ->join('master_users_tbl', 'master_users_tbl.m_user_id = master_purchase_tbl.m_purcs_user', 'left');

    if (!empty($order_by)) {
      $this->db->order_by('m_purcs_date', $order_by);
    } else {
      $this->db->order_by('m_purcs_date', 'desc');
    }
    $this->db->group_by('m_purcs_spo');
    $this->db->group_by('m_purcs_date');
    // $this->db->group_by('m_purcs_user');
    return $this->db->get('master_purchase_tbl')->result();
  }

  public function get_purchase_items($from_date = '', $todate = '', $supplier = '', $search_in = '')
  {
    $this->db->select(" mp.*, mit.m_item_name, mut.m_user_name AS supplier_name, mut.m_user_mobile, COALESCE(ms.sold_qty, 0) AS sold_qty, COALESCE(si1.issued_qty, 0) AS issued_qty, COALESCE(si2.returned_qty, 0) AS returned_qty");

    $this->db->from('master_purchase_tbl mp');
    // Item Join
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = mp.m_purcs_item', 'left');
    // Supplier Join
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = mp.m_purcs_suplier', 'left');
    // Sold Qty (Optimized Join Instead of Subquery)
    $this->db->join('(SELECT m_sale_lot, SUM(m_sale_qty) AS sold_qty FROM master_sales_tbl GROUP BY m_sale_lot) ms', 'ms.m_sale_lot = mp.m_purcs_id', 'left');

    // Issued Qty
    $this->db->join('(SELECT si_issue_lotno, SUM(si_issue_qty) AS issued_qty FROM staff_itemissue_tbl WHERE si_issue_type = 1 AND si_issue_status = 1 GROUP BY si_issue_lotno) si1', 'si1.si_issue_lotno = mp.m_purcs_id', 'left');

    // Returned Qty
    $this->db->join('(SELECT si_issue_lotno, SUM(si_issue_qty) AS returned_qty FROM staff_itemissue_tbl WHERE si_issue_type = 2 AND si_issue_status = 1 GROUP BY si_issue_lotno) si2', 'si2.si_issue_lotno = mp.m_purcs_id', 'left');

    // Date Filters (Index Friendly)
    if (!empty($from_date)) {
      $this->db->where('mp.m_purcs_date >=', $from_date . ' 00:00:00');
    }

    if (!empty($todate)) {
      $this->db->where('mp.m_purcs_date <=', $todate . ' 23:59:59');
    }

    // Supplier Filter
    if (!empty($supplier)) {
      $this->db->where_in('mp.m_purcs_suplier', $supplier);
    }

    // Secure Search
    if (!empty($search_in)) {
      $this->db->group_start();
      $this->db->like('mit.m_item_name', $search_in);
      $this->db->or_like('mp.m_purcs_truckno', $search_in);
      $this->db->or_like('mp.m_purcs_spo', $search_in);
      $this->db->or_like('mp.m_purcs_lot', $search_in);
      $this->db->group_end();
    }
      $this->db->order_by('mp.m_purcs_available', 'ASC');
  
    return $this->db->get()->result();
  }
  public function insert_purchase()
  {

    $supp_tm = $this->db->select('m_user_trademark')->where('m_user_type', 2)->where('m_user_id', $this->input->post('m_purcs_suplier'))->get('master_users_tbl')->row();

    $issue_id = $this->input->post('m_purcs_id');
    $purchase = $this->input->post('m_purcs_item');
    $issue_qty = $this->input->post('m_purcs_qty');
    $pre_qty = $this->input->post('pre_item_qty');
    $issue_weight = $this->input->post('m_purcs_weight');
    $issue_crate = $this->input->post('m_purcs_crate');
    $issue_price = $this->input->post('m_purcs_price');
    $m_purcs_total = $this->input->post('m_purcs_total');
    $m_purcs_lot = $this->input->post('m_purcs_lot');

    $supp_tm = $this->db->select('m_user_trademark')->where('m_user_type', 2)->where('m_user_id', $this->input->post('m_purcs_suplier'))->get('master_users_tbl')->row();
    $purchase_dtl = $this->db->select('m_purcs_spo')->order_by('m_purcs_id', 'desc')->group_by('m_purcs_spo')->get('master_purchase_tbl')->result();
    if (!empty($purchase_dtl)) {
      $spo_coun = explode('/', $purchase_dtl[0]->m_purcs_spo);
      $purcs_spo = $supp_tm->m_user_trademark . '/' . ($spo_coun[1] + 1) . '/' . date('d/m', strtotime($this->input->post('m_purcs_date')));
    } else {
      $purcs_spo = $supp_tm->m_user_trademark . '/1/' . date('d/m', strtotime($this->input->post('m_purcs_date')));
    }
    $purTotalAmt = 0;
    foreach ($purchase as $key => $cau) {

      $insert_data = array(
        "m_purcs_date" => $this->input->post('m_purcs_date'),
        "m_purcs_suplier" => $this->input->post('m_purcs_suplier'),
        "m_purcs_billno" => $this->input->post('m_purcs_billno'),
        "m_purcs_comrate" => $this->input->post('m_purcs_comrate'),
        "m_purcs_comm" => $this->input->post('m_purcs_comm'),
        "m_purcs_fright" => $this->input->post('m_purcs_fright'),
        "m_purcs_hamali" => $this->input->post('m_purcs_hamali'),
        "m_purcs_charity" => $this->input->post('m_purcs_charity'),
        "m_purcs_packaging" => $this->input->post('m_purcs_packaging'),
        "m_purcs_loading" => $this->input->post('m_purcs_loading'),
        "m_purcs_advance" => $this->input->post('m_purcs_advance'),
        "m_purcs_others" => $this->input->post('m_purcs_others'),
        "m_purcs_note" => $this->input->post('m_purcs_note'),
        "m_purcs_truckno" => $this->input->post('m_purcs_truckno'),
        // "m_purcs_user"    => $this->input->post('m_purcs_user'),
        "m_purcs_item" => $cau,
        "m_purcs_qty" => $issue_qty[$key],
        "m_purcs_weight" => $issue_weight[$key],
        "m_purcs_crate" => $issue_crate[$key],
        "m_purcs_price" => $issue_price[$key],
        "m_purcs_total" => $m_purcs_total[$key],
        "m_purcs_lot" => $m_purcs_lot[$key],
        "m_purcs_available" => $issue_qty[$key],
      );
      $purTotalAmt += (float) $m_purcs_total[$key];
      if (!empty($issue_id[$key])) {
        $purase_dtl = $this->db->select('m_purcs_spo')->where('m_purcs_id', $issue_id[$key])->get('master_purchase_tbl')->row();
        $purcs_spo = $purase_dtl->m_purcs_spo;
        $this->db->where('m_purcs_id', $issue_id[$key])->update('master_purchase_tbl', $insert_data);
        $new_qty = $issue_qty[$key] - $pre_qty[$key];
        if ($this->input->post('m_purcs_suplier') == $this->input->post('precust')) {
          $this->update_userbalance($this->input->post('m_purcs_suplier'), null, $new_qty, $cau);
        } else {
          $this->update_userbalance($this->input->post('m_purcs_suplier'), null, $issue_qty[$key], $cau);
          $this->update_userbalance($this->input->post('precust'), null, ($pre_qty[$key] * (-1)), $cau);
        }
        $res = 2;
      } else {

        if (!empty($this->input->post('m_purcs_spo'))) {
          $insert_data['m_purcs_spo'] = $this->input->post('m_purcs_spo');
        } else {
          $insert_data['m_purcs_spo'] = $purcs_spo;
        }

        $insert_data['m_purcs_added_by'] = $this->session->userdata('user_id');
        $insert_data['m_purcs_added_on'] = date('Y-m-d H:i');
        $this->db->insert('master_purchase_tbl', $insert_data);
        $this->update_userbalance($this->input->post('m_purcs_suplier'), null, $issue_qty[$key], $cau); //new change:end
        $res = 1;
      }
    }
    //new change:start
    $purTotalAmt += ((float) $this->input->post('m_purcs_comm') + (float) $this->input->post('m_purcs_fright') + (float) $this->input->post('m_purcs_hamali') + (float) $this->input->post('m_purcs_charity') + (float) $this->input->post('m_purcs_packaging') + (float) $this->input->post('m_purcs_loading') + (float) $this->input->post('m_purcs_advance') + (float) $this->input->post('m_purcs_others'));
    if (empty($this->input->post('m_purcs_spo'))) {
      $this->update_userbalance($this->input->post('m_purcs_suplier'), $purTotalAmt);

      //   $this->Api_Model->send_sale_sms($sale_spo);
    } else {

      $new_amt = ($purTotalAmt - (float) $this->input->post('pre_grand_total'));
      if ($this->input->post('m_purcs_suplier') == $this->input->post('precust')) {
        $this->update_userbalance($this->input->post('m_purcs_suplier'), $new_amt);
      } else {
        $this->update_userbalance($this->input->post('m_purcs_suplier'), $purTotalAmt);
        $this->update_userbalance($this->input->post('precust'), ((float) $this->input->post('pre_grand_total') * (-1)));
      }
    }
    $m_exp_id = $this->input->post('m_exp_id');
    $m_exp_name = $this->input->post('m_exp_name');
    $m_exp_amount = $this->input->post('m_exp_amount');

    foreach ($m_exp_name as $cou => $kky) {
      $voucher_no = $kky . '/' . $supp_tm->m_user_trademark . '/' . date('dms');
      if ($m_exp_amount[$cou] != null && $m_exp_amount[$cou] != '' && $m_exp_amount[$cou] != 0) {
        $insertt_data = array(

          "m_exp_type" => 1,
          "m_exp_name" => $kky,
          "m_exp_amount" => $m_exp_amount[$cou],
          "m_exp_accno" => $purcs_spo,
          "m_exp_remark" => "Purchase No =" . $purcs_spo,
          "m_exp_voucher" => $voucher_no,
          "m_exp_date" => $this->input->post('m_purcs_date'),
          "m_exp_status" => 1,

        );
        // echo'<pre>'; print_r($insertt_data); 
        if (!empty($m_exp_id[$cou])) {
          $rres = $this->db->where('m_exp_id', $m_exp_id[$cou])->update('master_expenses_tbl', $insertt_data);
        } else {
          $insertt_data['m_exp_added_by'] = $this->session->userdata('user_id');
          $insertt_data['m_exp_added_on'] = date('Y-m-d H:i');
          $rres = $this->db->insert('master_expenses_tbl', $insertt_data);
          //  echo'<pre>'; print_r($rres); 
        }
      }
    }
    // die;
    return $res;
  }


  public function delete_purchase()
  {
    $pur_datil = $this->db->select('*')
      ->where('m_purcs_spo', $this->input->post('delete_id'))->get('master_purchase_tbl')->result();

    $pre_grandtotal = ($pur_datil[0]->m_purcs_comm + $pur_datil[0]->m_purcs_fright + $pur_datil[0]->m_purcs_hamali + $pur_datil[0]->m_purcs_charity + $pur_datil[0]->m_purcs_packaging + $pur_datil[0]->m_purcs_loading + $pur_datil[0]->m_purcs_advance + $pur_datil[0]->m_purcs_others);
    foreach ($pur_datil as $kry) {
      $pre_grandtotal += $kry->m_purcs_total;
      $this->update_userbalance($kry->m_purcs_suplier, null, ($kry->m_purcs_qty * (-1)), $kry->m_purcs_item, $kry->m_purcs_lot);
    }
    $this->update_userbalance($pur_datil[0]->m_purcs_suplier, ($pre_grandtotal * (-1)));
    $this->db->where('m_purcs_spo', $this->input->post('delete_id'));
    $this->db->delete('master_purchase_tbl');

    $this->db->where('m_exp_accno', $this->input->post('delete_id'));
    $this->db->delete('master_expenses_tbl');
    return true;
  }

  public function delete_purchase_id()
  {
    $pur_datil = $this->db->select('m_purcs_qty,m_purcs_lot')->where('m_purcs_id', $this->input->post('delete_id'))->get('master_purchase_tbl')->row(); //new change
    $this->update_userbalance(null, null, ($pur_datil->m_purcs_qty * (-1)), null, $pur_datil->m_purcs_lot); //new change

    $this->db->where('m_purcs_id', $this->input->post('delete_id'));
    $this->db->delete('master_purchase_tbl');
    return true;
  }
  //===================== purchase =======================//

  //===================== recieved payment/crate =======================//

  public function get_received_list($type, $from_date, $to_date, $scustomer = '', $account = '', $method = '', $group = '', $search_in = '', $order_by = '')
  {
    $this->db->select('master_recieved_tbl.*,(CASE WHEN m_recvd_account = 1 || m_recvd_type = 2 THEN mct.m_cust_name WHEN m_recvd_account = 5 || m_recvd_account = 7 THEN mug.m_group_name ELSE mutt.m_user_name END) as m_cust_name,(CASE WHEN m_recvd_account = 1 THEN mct.m_cust_mobile  WHEN m_recvd_account = 5 || m_recvd_account = 7 THEN mug.m_group_number ELSE mutt.m_user_mobile END) as m_cust_mobile,mut.m_user_name,mut.m_user_mobile,crate.m_itgrp_title,method.m_group_name as method_name');
    $this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_users_tbl mutt', 'mutt.m_user_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_group_tbl mug', 'mug.m_group_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_recieved_tbl.m_recvd_user', 'left');
    $this->db->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_recieved_tbl.m_recvd_method', 'left');

    if (!empty($from_date)) {
      $this->db->where('DATE_FORMAT(m_recvd_date,"%Y-%m-%d")>=', $from_date);
    }
    if (!empty($to_date)) {
      $this->db->where('DATE_FORMAT(m_recvd_date,"%Y-%m-%d")<=', $to_date);
    }

    if (!empty($group)) {
      $this->db->where_in('m_cust_group', $group);
    }
    if (!empty($account)) {
      $this->db->where_in('m_recvd_account', $account);
    }

    if (!empty($method)) {
      $this->db->where_in('m_recvd_method', $method);
    }
    if (!empty($scustomer)) {
      $this->db->where_in('m_recvd_customer', $scustomer);
    }

    if (!empty($search_in)) {
      $wh = "(mutt.m_user_name LIKE '%$search_in%' OR mutt.m_user_mobile LIKE '%$search_in%' OR m_cust_name LIKE '%$search_in%' OR m_cust_mobile LIKE '%$search_in%' OR mut.m_user_name LIKE '%$search_in%' OR mut.m_user_mobile LIKE '%$search_in%')";
      $this->db->where($wh);
    }

    $this->db->where('m_recvd_type', $type);
    if (!empty($order_by)) {
      $this->db->order_by('m_recvd_date', $order_by);
    } else {
      $this->db->order_by('m_recvd_date', 'desc');
    }

    return $this->db->get('master_recieved_tbl')->result();
  }

  public function get_received_detail($type, $voucher)
  {
    $this->db->select('master_recieved_tbl.*,(CASE WHEN m_recvd_account = 2 || m_recvd_account = 3 THEN mutt.m_user_name ELSE m_cust_name END) as m_cust_name,(CASE WHEN m_recvd_account = 2 || m_recvd_account = 3 THEN mutt.m_user_mobile ELSE m_cust_mobile END) as m_cust_mobile,mut.m_user_name,mut.m_user_mobile,crate.m_itgrp_title,mgt.m_group_name,mgt.m_group_type');
    $this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_users_tbl mutt', 'mutt.m_user_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_recieved_tbl.m_recvd_user', 'left');
    $this->db->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left');
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_recieved_tbl.m_recvd_method', 'left');
    $this->db->where('m_recvd_voucher', $voucher);

    $this->db->where('m_recvd_type', $type);
    $this->db->order_by('m_recvd_date', 'desc');
    return $this->db->get('master_recieved_tbl')->result();
  }


  public function insert_recieved_data()
  {
    $m_recvd_type = $this->input->post('m_recvd_type');
    $m_recvd_date = $this->input->post('m_recvd_date');
    $m_recvd_account = $this->input->post('m_recvd_account');
    $user_id = $this->session->userdata('user_id');

    if ($m_recvd_type == 1) {
      $m_recvd_customer = $this->input->post('m_recvd_customer');
      $m_recvd_amount = $this->input->post('m_recvd_amount');
      $m_recvd_remark = $this->input->post('m_recvd_remark');
      $m_recvd_user = $this->input->post('m_recvd_user');
      $m_recvd_method = $this->input->post('m_recvd_method');

      $last_id = $this->db->select('m_recvd_id')->where('m_recvd_type', 1)->order_by('m_recvd_id', 'desc')->get('master_recieved_tbl')->row();
      $vlastid = empty($last_id) ? 0 : $last_id->m_recvd_id;
      $voucher_no = date('d') . $vlastid . $m_recvd_account . $m_recvd_type;

      foreach ($m_recvd_customer as $index => $customer) {
        if ($m_recvd_amount[$index] == 0)
          continue;

        $exists = $this->db->where(['m_recvd_customer' => $customer, 'm_recvd_method' => $m_recvd_method, 'm_recvd_amount' => $m_recvd_amount[$index], 'm_recvd_date' => $m_recvd_date, 'm_recvd_type' => $m_recvd_type])->get('master_recieved_tbl')->row();

        if (!$exists) {
          $data = [
            "m_recvd_customer" => $customer,
            "m_recvd_voucher" => $voucher_no,
            "m_recvd_method" => $m_recvd_method,
            "m_recvd_amount" => $m_recvd_amount[$index],
            "m_recvd_account" => $m_recvd_account,
            "m_recvd_remark" => $m_recvd_remark[$index],
            "m_recvd_user" => $m_recvd_user[$index] ?? '',
            "m_recvd_date" => $m_recvd_date,
            "m_recvd_type" => $m_recvd_type,
            "m_recvd_added_by" => $user_id,
            "m_recvd_added_on" => date('Y-m-d H:i')
          ];
          $this->db->insert('master_recieved_tbl', $data);

          // Update balance based on account type
          if ($m_recvd_account == 1) {
            $this->update_cust_balance($customer, -$m_recvd_amount[$index]);
          } elseif (in_array($m_recvd_account, [2, 3, 4, 6])) {
            $this->update_userbalance($customer, $m_recvd_amount[$index]);
          }

          // $this->Api_Model->send_paymentreicvd_sms($voucher_no, $customer, $m_recvd_amount[$index]);
        }
      }
    } else {
      $m_recvd_qty = $this->input->post('m_recvd_qty');
      $m_recvd_crate = $this->input->post('m_recvd_crate');
      $m_recvd_customer = $this->input->post('m_recvd_customer');
      $m_recvd_remark = $this->input->post('m_recvd_remark');
      $m_recvd_user = $this->input->post('m_recvd_user');
      $uniqut = $this->input->post('uniqut');

      $last_id = $this->db->select('m_recvd_id')->where('m_recvd_type', 2)->order_by('m_recvd_id', 'desc')->get('master_recieved_tbl')->row();
      $vlastid = empty($last_id) ? 0 : $last_id->m_recvd_id;

      $crate_mapping = [
        20 => 'm_cust_10bal',
        13 => 'm_cust_20bal',
        14 => 'm_cust_25bal'
      ];

      foreach ($m_recvd_customer as $index => $customer) {
        $voucher_no = date('d') . $vlastid . $customer . $m_recvd_type;

        foreach ($m_recvd_crate[$customer . $uniqut[$index]] as $subIndex => $crate_type) {
          $qty = (int) $m_recvd_qty[$customer . $uniqut[$index]][$subIndex];
          if ($qty == 0)
            continue;

          $data = [
            "m_recvd_customer" => $customer,
            "m_recvd_qty" => $qty,
            "m_recvd_crate" => $crate_type,
            "m_recvd_remark" => $m_recvd_remark[$index],
            "m_recvd_user" => $m_recvd_user[$index] ?? '',
            "m_recvd_voucher" => $voucher_no,
            "m_recvd_date" => $m_recvd_date,
            "m_recvd_type" => $m_recvd_type,
            "m_recvd_added_by" => $user_id,
            "m_recvd_added_on" => date('Y-m-d H:i')
          ];
          $this->db->insert('master_recieved_tbl', $data);

          // Update customer balance for crate type
          if (isset($crate_mapping[$crate_type])) {
            $this->db->set($crate_mapping[$crate_type], "$crate_mapping[$crate_type] - $qty", FALSE)
              ->where('m_cust_id', $customer)
              ->update('master_customer_tbl');
          }
        }
        // $this->Api_Model->send_cratereicvd_sms($voucher_no, $customer);
      }
    }

    return isset($res) ? $res : false;
  }

  public function update_recieved_data()
  {
    $postData = $this->input->post();
    $userId = $this->session->userdata('user_id');
    $currentDate = date('Y-m-d H:i');

    $insert_data = [
      'm_recvd_customer' => $postData['m_recvd_customer'],
      'm_recvd_date' => $postData['m_recvd_date'],
      'm_recvd_remark' => $postData['m_recvd_remark'],
      'm_recvd_updated_by' => $userId,
      'm_recvd_updated_on' => $currentDate,
    ];

    if ($postData['m_recvd_type'] == 1) {
      $insert_data['m_recvd_method'] = $postData['m_recvd_method'];
      $insert_data['m_recvd_amount'] = $postData['m_recvd_amount'];
    } else {
      $insert_data['m_recvd_qty'] = $postData['m_recvd_qty'];
      $insert_data['m_recvd_crate'] = $postData['m_recvd_crate'];
    }

    $res = $this->db->where('m_recvd_id', $postData['m_recvd_id'])->update('master_recieved_tbl', $insert_data);

    $isSameCustomer = ($postData['m_recvd_customer'] == $postData['precust']);

    if ($postData['m_recvd_type'] == 1) {
      if ($postData['m_recvd_account'] == 1) {
        if (!$isSameCustomer) {
          $this->update_cust_balance($postData['precust'], $postData['preamount']);
          $this->update_cust_balance($postData['m_recvd_customer'], ($postData['m_recvd_amount'] * (-1)));
        } else {
          $this->update_cust_balance($postData['m_recvd_customer'], ($postData['m_recvd_amount'] - $postData['preamount']) * (-1));
        }
      } elseif (in_array($postData['m_recvd_account'], [2, 3, 4, 6])) {
        if (!$isSameCustomer) {
          $this->update_userbalance($postData['precust'], $postData['preamount'] * (-1));
          $this->update_userbalance($postData['m_recvd_customer'], $postData['m_recvd_amount']);
        } else {
          $this->update_userbalance($postData['m_recvd_customer'], ($postData['m_recvd_amount'] - $postData['preamount']));
        }
      }
    } elseif ($postData['m_recvd_type'] == 2) {
      $crateMapping = [
        20 => 'm_cust_10bal',
        13 => 'm_cust_20bal',
        14 => 'm_cust_25bal',
      ];

      if (isset($crateMapping[$postData['m_recvd_crate']])) {
        $field = $crateMapping[$postData['m_recvd_crate']];
        if (!$isSameCustomer) {
          $this->db->set($field, "$field + " . (int) $postData['preqty'], FALSE);
          $this->db->where('m_cust_id', $postData['precust'])->update('master_customer_tbl');

          $this->db->set($field, "$field - " . (int) $postData['m_recvd_qty'], FALSE);
          $this->db->where('m_cust_id', $postData['m_recvd_customer'])->update('master_customer_tbl');
        } else {
          $this->db->set($field, "$field - " . ((int) $postData['m_recvd_qty'] - (int) $postData['preqty']), FALSE);
          $this->db->where('m_cust_id', $postData['m_recvd_customer'])->update('master_customer_tbl');
        }
      }
    }

    return $res;
  }

  public function delete_recieved_data()
  {
    $delete_id = $this->input->post('delete_id');
    $res_list = $this->db->where('m_recvd_voucher', $delete_id)->get('master_recieved_tbl')->result();

    if (!empty($res_list)) {
      $crate_mapping = [
        20 => 'm_cust_10bal',
        13 => 'm_cust_20bal',
        14 => 'm_cust_25bal'
      ];

      foreach ($res_list as $value) {
        if ($value->m_recvd_type == 1) {
          if ($value->m_recvd_account == 1) {
            $this->update_cust_balance($value->m_recvd_customer, $value->m_recvd_amount);
          } elseif (in_array($value->m_recvd_account, [2, 3, 4, 6])) {
            $this->update_userbalance($value->m_recvd_customer, $value->m_recvd_amount * (-1));
          }
        } elseif ($value->m_recvd_type == 2) {
          if (isset($crate_mapping[$value->m_recvd_crate])) {
            $this->db->set($crate_mapping[$value->m_recvd_crate], "{$crate_mapping[$value->m_recvd_crate]} + {$value->m_recvd_qty}", FALSE)
              ->where('m_cust_id', $value->m_recvd_customer)
              ->update('master_customer_tbl');
          }
        }
      }
    }

    return $this->db->where('m_recvd_voucher', $delete_id)->delete('master_recieved_tbl');
  }

  //===================== recieved payment/crate =======================//

  //===================== paid payment/crate =======================//

  public function get_payment_list($type, $from_date, $to_date, $scustomer, $payment_account = '', $payment_method = '', $search_in = '', $order_by = '')
  {
    $this->db->select('master_payment_tbl.*,(CASE WHEN m_payment_account = 2 || m_payment_account = 7 THEN mgt.m_group_name ELSE mut.m_user_name END) as m_user_name,(CASE WHEN m_payment_account = 2 || m_payment_account = 7 THEN mgt.m_group_number ELSE m_user_mobile END) as m_user_mobile,crate.m_itgrp_title,m_payment_account as account_type,method.m_group_name as method_name');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_payment_tbl.m_payment_crate', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_payment_tbl.m_payment_method', 'left');

    if (!empty($from_date)) {
      $this->db->where('DATE_FORMAT(m_payment_date,"%Y-%m-%d")>=', $from_date);
    }
    if (!empty($to_date)) {
      $this->db->where('DATE_FORMAT(m_payment_date,"%Y-%m-%d")<=', $to_date);
    }
    if (!empty($payment_account)) {
      $this->db->where('m_payment_account', $payment_account);
    }
    if (!empty($payment_method)) {
      $this->db->where('m_payment_method', $payment_method);
    }
    if (!empty($scustomer)) {
      $this->db->where_in('m_payment_supplier', $scustomer);
    }

    if (!empty($search_in)) {
      $wh = "(method.m_group_name LIKE '%$search_in%' OR mgt.m_group_name LIKE '%$search_in%' OR  mut.m_user_name LIKE '%$search_in%' OR mut.m_user_mobile LIKE '%$search_in%')";
      $this->db->where($wh);
    }

    $this->db->where('m_payment_type', $type);
    if ($order_by == 1) {
      $this->db->order_by('m_user_name');
    } else if ($order_by == 2) {
      $this->db->order_by('m_payment_amount', 'desc');
    } else if ($order_by == 4) {
      $this->db->order_by('m_payment_date', 'desc');
      $this->db->order_by('m_payment_amount', 'desc');
    } else {
      $this->db->order_by('m_payment_date', 'desc');
    }

    return $this->db->get('master_payment_tbl')->result();
  }

  public function get_payment_detail($type, $voucher)
  {
    $this->db->select('master_payment_tbl.*,m_user_name,m_user_mobile,crate.m_itgrp_title');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_payment_tbl.m_payment_crate', 'left');
    $this->db->where('m_payment_voucher', $voucher);

    $this->db->where('m_payment_type', $type);
    $this->db->order_by('m_payment_date', 'desc');
    return $this->db->get('master_payment_tbl')->result();
  }

  public function insert_payment_data()
  {
    $m_payment_type = $this->input->post('m_payment_type');
    $m_payment_date = $this->input->post('m_payment_date');
    $m_payment_method = $this->input->post('m_payment_method');
    $m_payment_account = $this->input->post('m_payment_account');

    if ($m_payment_type == 1) {
      $m_payment_supplier = $this->input->post('m_payment_supplier');
      $m_payment_amount = $this->input->post('m_payment_amount');
      $m_payment_remark = $this->input->post('m_payment_remark');

      $last_id = $this->db->select('m_payment_id')->where('m_payment_type', $m_payment_type)->order_by('m_payment_id', 'desc')->get('master_payment_tbl')->row();
      $vlastid = empty($last_id) ? 0 : $last_id->m_payment_id;

      foreach ($m_payment_supplier as $cou => $supplier) {
        if ($m_payment_amount[$cou] == 0)
          continue;

        $exists = $this->db->where(['m_payment_supplier' => $supplier, 'm_payment_method' => $m_payment_method, 'm_payment_amount' => $m_payment_amount[$cou], 'm_payment_date' => $m_payment_date, 'm_payment_type' => $m_payment_type])->get('master_payment_tbl')->row();

        if (!$exists) {
          $voucher_no = date('d') . $vlastid . $m_payment_account . $m_payment_type;

          $insert_data = [
            "m_payment_supplier" => $supplier,
            "m_payment_voucher" => $voucher_no,
            "m_payment_method" => $m_payment_method,
            "m_payment_account" => $m_payment_account,
            "m_payment_amount" => $m_payment_amount[$cou],
            "m_payment_remark" => $m_payment_remark[$cou],
            "m_payment_date" => $m_payment_date,
            "m_payment_type" => $m_payment_type,
            "m_payment_added_by" => $this->session->userdata('user_id'),
            "m_payment_added_on" => date('Y-m-d H:i')
          ];

          $res = $this->db->insert('master_payment_tbl', $insert_data);

          // Update user balance if applicable
          if (!in_array($m_payment_account, [2, 7])) {
            $this->update_userbalance($supplier, -$m_payment_amount[$cou]);
          }
        }
      }
    } else {
      $uniqut = $this->input->post('uniqut');
      $m_payment_qty = $this->input->post('m_payment_qty');
      $m_payment_crate = $this->input->post('m_payment_crate');
      $m_payment_supplier = $this->input->post('m_payment_supplier');
      $m_payment_remark = $this->input->post('m_payment_remark');

      $crate_mapping = [
        20 => 'm_user_10bal',
        13 => 'm_user_20bal',
        14 => 'm_user_25bal'
      ];

      $last_id = $this->db->select('m_payment_id')
        ->where('m_payment_type', $m_payment_type)
        ->order_by('m_payment_id', 'desc')
        ->get('master_payment_tbl')
        ->row();
      $vlastid = empty($last_id) ? 0 : $last_id->m_payment_id;

      foreach ($m_payment_supplier as $cau => $supplier) {
        $voucher_no = date('d') . $vlastid . $supplier . $m_payment_type;

        foreach ($m_payment_crate[$supplier . $uniqut[$cau]] as $cou => $crate) {
          if ($m_payment_qty[$supplier . $uniqut[$cau]][$cou] == 0)
            continue;

          $insert_data = [
            "m_payment_supplier" => $supplier,
            "m_payment_qty" => $m_payment_qty[$supplier . $uniqut[$cau]][$cou],
            "m_payment_crate" => $crate,
            "m_payment_remark" => $m_payment_remark[$cau],
            "m_payment_voucher" => $voucher_no,
            "m_payment_date" => $m_payment_date,
            "m_payment_type" => $m_payment_type,
            "m_payment_added_by" => $this->session->userdata('user_id'),
            "m_payment_added_on" => date('Y-m-d H:i')
          ];

          $res = $this->db->insert('master_payment_tbl', $insert_data);

          // Update user crate balance if applicable
          if (isset($crate_mapping[$crate])) {
            $this->db->set($crate_mapping[$crate], "{$crate_mapping[$crate]} - {$m_payment_qty[$supplier . $uniqut[$cau]][$cou]}", FALSE)
              ->where('m_user_id', $supplier)->update('master_users_tbl');
          }
        }
      }
    }

    return $res ?? false;
  }

  public function update_payment_data()
  {
    $postData = $this->input->post();
    $userId = $this->session->userdata('user_id');
    $currentDate = date('Y-m-d H:i');

    $insert_data = [
      'm_payment_supplier' => $postData['m_payment_supplier'],
      'm_payment_date' => $postData['m_payment_date'],
      'm_payment_remark' => $postData['m_payment_remark'],
      'm_payment_updated_by' => $userId,
      'm_payment_updated_on' => $currentDate,
    ];

    if ($postData['m_payment_type'] == 1) {
      $insert_data['m_payment_method'] = $postData['m_payment_method'];
      $insert_data['m_payment_amount'] = $postData['m_payment_amount'];
    } else {
      $insert_data['m_payment_qty'] = $postData['m_payment_qty'];
      $insert_data['m_payment_crate'] = $postData['m_payment_crate'];
    }

    // Update payment data in DB
    $res = $this->db->where('m_payment_id', $postData['m_payment_id'])
      ->update('master_payment_tbl', $insert_data);

    // Check if account type requires balance updates
    $isBalanceUpdateRequired = !in_array($postData['m_payment_account'], [2, 7]);
    $isSameCustomer = ($postData['m_payment_supplier'] == $postData['precust']);

    if ($isBalanceUpdateRequired && $postData['m_payment_type'] == 1) {
      $balanceChange = ($postData['m_payment_amount'] - $postData['preamount']) * (-1);

      if (!$isSameCustomer) {
        $this->update_userbalance($postData['precust'], $postData['preamount']);
        $this->update_userbalance($postData['m_payment_supplier'], $postData['m_payment_amount'] * (-1));
      } else {
        $this->update_userbalance($postData['m_payment_supplier'], $balanceChange);
      }
    }

    if ($postData['m_payment_type'] == 2) {
      $crateMapping = [
        20 => 'm_user_10bal',
        13 => 'm_user_20bal',
        14 => 'm_user_25bal',
      ];

      if (isset($crateMapping[$postData['m_payment_crate']])) {
        $field = $crateMapping[$postData['m_payment_crate']];
        $qtyDiff = (int) $postData['m_payment_qty'] - (int) $postData['preqty'];

        // Restore balance for the previous supplier if changed
        if (!$isSameCustomer) {
          $this->db->set($field, "$field + " . (int) $postData['preqty'], FALSE)
            ->where('m_user_id', $postData['precust'])
            ->update('master_users_tbl');
          $this->db->set($field, "$field - " . (int) $postData['m_payment_qty'], FALSE)
            ->where('m_user_id', $postData['m_payment_supplier'])
            ->update('master_users_tbl');
        } else {
          $this->db->set($field, "$field - $qtyDiff", FALSE)
            ->where('m_user_id', $postData['m_payment_supplier'])
            ->update('master_users_tbl');
        }
      }
    }

    return $res;
  }

  public function delete_payment_data()
  {
    $delete_id = $this->input->post('delete_id');
    $res_list = $this->db->where('m_payment_voucher', $delete_id)->get('master_payment_tbl')->result();

    if (!empty($res_list)) {
      $crate_mapping = [
        20 => 'm_user_10bal',
        13 => 'm_user_20bal',
        14 => 'm_user_25bal'
      ];

      foreach ($res_list as $value) {
        if ($value->m_payment_type == 1 && !in_array($value->m_payment_account, [2, 7])) {
          $this->update_userbalance($value->m_payment_supplier, $value->m_payment_amount);
        } elseif ($value->m_payment_type == 2) {
          if (isset($crate_mapping[$value->m_payment_crate])) {
            $this->db->set($crate_mapping[$value->m_payment_crate], "{$crate_mapping[$value->m_payment_crate]} + {$value->m_payment_qty}", FALSE)
              ->where('m_user_id', $value->m_payment_supplier)
              ->update('master_users_tbl');
          }
        }
      }
    }

    return $this->db->where('m_payment_voucher', $delete_id)->delete('master_payment_tbl');
  }
  //===================== paid payment/crate =======================//

  //===================== voucher =======================//

  public function get_voucher_list($type, $from_date, $to_date, $scustomer, $search_in = '', $order_by = '')
  {
    $this->db->select('master_voucher_tbl.*,(CASE WHEN m_voucher_account = 3 THEN mgt.m_group_name WHEN m_voucher_account = 1 THEN mct.m_cust_name ELSE mut.m_user_name END) as m_user_name,(CASE WHEN m_voucher_account = 3 THEN mgt.m_group_number WHEN m_voucher_account = 1 THEN mct.m_cust_mobile ELSE mut.m_user_mobile END) as m_user_mobile,m_voucher_account as account_type');
    $this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_voucher_tbl.m_voucher_accountid', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_voucher_tbl.m_voucher_accountid', 'left');
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_voucher_tbl.m_voucher_accountid', 'left');


    if (!empty($from_date)) {
      $this->db->where('DATE_FORMAT(m_voucher_date,"%Y-%m-%d")>=', $from_date);
    }
    if (!empty($to_date)) {
      $this->db->where('DATE_FORMAT(m_voucher_date,"%Y-%m-%d")<=', $to_date);
    }
    if (!empty($type)) {
      $this->db->where('m_voucher_type', $type);
    }
    if (!empty($scustomer)) {
      $this->db->where_in('m_voucher_accountid', $scustomer);
    }

    if (!empty($search_in)) {
      $wh = "(mct.m_cust_name LIKE '%$search_in%' OR mgt.m_group_name LIKE '%$search_in%' OR  mut.m_user_name LIKE '%$search_in%' OR mut.m_user_mobile LIKE '%$search_in%' OR mct.m_cust_mobile LIKE '%$search_in%')";
      $this->db->where($wh);
    }

    if (!empty($order_by)) {
      $this->db->order_by('m_voucher_date', $order_by);
    } else {
      $this->db->order_by('m_voucher_date', 'desc');
    }

    return $this->db->get('master_voucher_tbl')->result();
  }

  public function get_voucher_detail($voucher)
  {
    $this->db->select('master_voucher_tbl.*,(CASE WHEN m_voucher_account = 3 THEN mgt.m_group_name WHEN m_voucher_account = 1 THEN mct.m_cust_name ELSE mut.m_user_name END) as m_user_name,(CASE WHEN m_voucher_account = 3 THEN mgt.m_group_number WHEN m_voucher_account = 1 THEN mct.m_cust_mobile ELSE mut.m_user_mobile END) as m_user_mobile,m_voucher_account as account_type');
    $this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_voucher_tbl.m_voucher_accountid', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_voucher_tbl.m_voucher_accountid', 'left');
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_voucher_tbl.m_voucher_accountid', 'left');
    $this->db->where('m_voucher_id', $voucher);
    return $this->db->get('master_voucher_tbl')->row();
  }


  public function insert_voucher_data()
  {
    $postData = $this->input->post();
    $userId = $this->session->userdata('user_id');
    $currentDate = date('Y-m-d H:i');
    $m_voucher_accountid = $postData['m_voucher_accountid'];
    $m_voucher_account = $postData['m_voucher_account'];
    $insertBatch = [];
    foreach ($m_voucher_accountid as $index => $accountId) {
      if ($postData['m_voucher_amount'][$index] == 0) {
        continue; // Skip zero amount entries
      }

      $voucherAmount = $postData['m_voucher_amount'][$index];
      $voucherType = $postData['m_voucher_type'][$index];
      $voucherRemark = $postData['m_voucher_remark'][$index];

      $insertBatch[] = [
        "m_voucher_accountid" => $accountId,
        "m_voucher_account" => $m_voucher_account,
        "m_voucher_amount" => $voucherAmount,
        "m_voucher_remark" => $voucherRemark,
        "m_voucher_date" => $postData['m_voucher_date'],
        "m_voucher_type" => $voucherType,
        "m_voucher_status" => 1,
        "m_voucher_added_by" => $userId,
        "m_voucher_added_on" => $currentDate
      ];

      // Update balance logic
      if ($voucherType == 1) {
        if ($m_voucher_account != 1 && $m_voucher_account != 3) {
          $this->update_userbalance($accountId, +$voucherAmount);
        } elseif ($m_voucher_account == 1) {
          $this->update_cust_balance($accountId, -$voucherAmount);
        }
      } elseif ($voucherType == 2) {
        if ($m_voucher_account == 1) {
          $this->update_cust_balance($accountId, $voucherAmount);
        } elseif ($m_voucher_account != 1 && $m_voucher_account != 3) {
          $this->update_userbalance($accountId, -$voucherAmount);
        }
      }
    }
    if (!empty($insertBatch)) {
      $this->db->insert_batch('master_voucher_tbl', $insertBatch);
      return true;
    }
    return false;
  }

  public function update_voucher_data()
  {
    $postData = $this->input->post();
    $userId = $this->session->userdata('user_id');
    $currentDate = date('Y-m-d H:i');

    // Prepare update data
    $updateData = [
      'm_voucher_amount' => $postData['m_voucher_amount'],
      'm_voucher_type' => $postData['m_voucher_type'],
      'm_voucher_date' => $postData['m_voucher_date'],
      'm_voucher_accountid' => $postData['m_voucher_accountid'],
      'm_voucher_remark' => $postData['m_voucher_remark'],
      'm_voucher_updated_by' => $userId,
      'm_voucher_updated_on' => $currentDate
    ];

    // Update voucher record
    $res = $this->db->where('m_voucher_id', $postData['m_voucher_id'])->update('master_voucher_tbl', $updateData);

    $isSameCustomer = ($postData['m_voucher_accountid'] == $postData['precust']);
    $balanceChange = $postData['m_voucher_amount'] - $postData['preamount'];

    if (!$isSameCustomer) {
      if ($postData['m_voucher_type'] == 1) {
        if ($postData['m_voucher_account'] == 1) {
          $this->update_cust_balance($postData['precust'], $postData['preamount']);
          $this->update_cust_balance($postData['m_voucher_accountid'], -$postData['m_voucher_amount']);
        } elseif ($postData['m_voucher_account'] != 3) {
          $this->update_userbalance($postData['precust'], -$postData['preamount']);
          $this->update_userbalance($postData['m_voucher_accountid'], $postData['m_voucher_amount']);
        }
      } else {
        if ($postData['m_voucher_account'] == 1) {
          $this->update_cust_balance($postData['precust'], -$postData['preamount']);
          $this->update_cust_balance($postData['m_voucher_accountid'], $postData['m_voucher_amount']);
        } elseif ($postData['m_voucher_account'] != 3) {
          $this->update_userbalance($postData['precust'], $postData['preamount']);
          $this->update_userbalance($postData['m_voucher_accountid'], -$postData['m_voucher_amount']);
        }
      }
    } else {
      if ($postData['m_voucher_type'] == 1) {
        if ($postData['m_voucher_account'] == 1) {
          $this->update_cust_balance($postData['m_voucher_accountid'], -$balanceChange);
        } elseif ($postData['m_voucher_account'] != 3) {
          $this->update_userbalance($postData['m_voucher_accountid'], $balanceChange);
        }
      } else {
        if ($postData['m_voucher_account'] == 1) {
          $this->update_cust_balance($postData['m_voucher_accountid'], $balanceChange);
        } elseif ($postData['m_voucher_account'] != 3) {
          $this->update_userbalance($postData['m_voucher_accountid'], -$balanceChange);
        }
      }
    }

    return $res;
  }

  public function delete_voucher_data()
  {
    $res = $this->db->where('m_voucher_id', $this->input->post('delete_id'))->get('master_voucher_tbl')->row();

    if ($res->m_voucher_type == 1 && $res->m_voucher_account != 1 && $res->m_voucher_account != 3) {
      $this->update_userbalance($res->m_voucher_accountid, ($res->m_voucher_amount * (-1)));
    } else if ($res->m_voucher_type == 1 && $res->m_voucher_account == 1) {
      $this->update_cust_balance($res->m_voucher_accountid, $res->m_voucher_amount);
    } else if ($res->m_voucher_type == 2 && $res->m_voucher_account == 1) {
      $this->update_cust_balance($res->m_voucher_accountid, ($res->m_voucher_amount * (-1)));
    } else if ($res->m_voucher_type == 2 && $res->m_voucher_account != 1 && $res->m_voucher_account != 3) {
      $this->update_userbalance($res->m_voucher_accountid, ($res->m_voucher_amount));
    }
    $this->db->where('m_voucher_id', $this->input->post('delete_id'));
    $delres = $this->db->delete('master_voucher_tbl');
    return $delres;
  }


  //===================== voucher =======================//



  //==========================Stock List===========================//

  public function get_avilable_item($itemid = '', $group = '')
  {

    $this->db->select('m_item_id,m_item_name,m_item_crate,m_item_fright,m_item_price,m_purcs_price,m_purcs_available,m_purcs_lot,m_purcs_id,unit.m_itgrp_title as m_unit_name,crate.m_itgrp_title as m_crate_name,m_purcs_date,m_user_trademark')
      ->join('master_item_tbl', 'master_item_tbl.m_item_id = master_purchase_tbl.m_purcs_item')
      ->join('master_itemgroup_tbl crate', 'crate.m_itgrp_id = master_item_tbl.m_item_crate', 'left')
      ->join('master_itemgroup_tbl unit', 'unit.m_itgrp_id = master_item_tbl.m_item_unit', 'left')
      ->join('master_users_tbl mut', 'mut.m_user_id = master_purchase_tbl.m_purcs_suplier', 'left');
    $this->db->where('m_purcs_available >', 0);

    if (!empty($itemid)) {
      $this->db->where('m_item_id', $itemid);
    }
    if ($group == 1) {
      $this->db->group_by('m_item_id');
    }
    return $this->db->order_by('m_item_name')->get('master_purchase_tbl')->result();
  }
  //==========================Stock List===========================//

  public function update_cust_balance($id = '', $amt = '', $qty = '', $itemID = '', $purID = '')
  {
    if (!empty($purID) && !empty($qty)) {
      $this->db->set('m_purcs_available', 'm_purcs_available - ' . (int) $qty, FALSE)
        ->where('m_purcs_id', $purID)->update('master_purchase_tbl');
    }

    if (!empty($itemID)) {
      $itemDtl = $this->db->select('m_itgrp_title')
        ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_item_tbl.m_item_crate', 'left')
        ->where('m_item_id', $itemID)->get('master_item_tbl')->row();

      $balanceFields = [
        '10 KG' => 'm_cust_10bal',
        '20 KG' => 'm_cust_20bal',
        '25 KG' => 'm_cust_25bal'
      ];

      if (!empty($itemDtl->m_itgrp_title) && isset($balanceFields[$itemDtl->m_itgrp_title])) {
        $this->db->set($balanceFields[$itemDtl->m_itgrp_title], $balanceFields[$itemDtl->m_itgrp_title] . ' + ' . (float) $qty, FALSE);
        $this->db->where('m_cust_id', $id)->update('master_customer_tbl');
      }
    }

    if (!empty($amt) && !empty($id)) {
      $this->db->set('m_cust_balance', 'm_cust_balance + ' . (float) $amt, FALSE)
        ->where('m_cust_id', $id)->update('master_customer_tbl');
    }

    return true;
  }

  public function update_userbalance($id = '', $amt = '', $qty = '', $itemID = '')
  {
    if (!empty($itemID)) {
      $itemDtl = $this->db->select('m_itgrp_title')
        ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_item_tbl.m_item_crate', 'left')
        ->where('m_item_id', $itemID)->get('master_item_tbl')->row();

      $balanceFields = [
        '10 KG' => 'm_user_10bal',
        '20 KG' => 'm_user_20bal',
        '25 KG' => 'm_user_25bal'
      ];

      if (!empty($itemDtl->m_itgrp_title) && isset($balanceFields[$itemDtl->m_itgrp_title])) {
        $this->db->set($balanceFields[$itemDtl->m_itgrp_title], $balanceFields[$itemDtl->m_itgrp_title] . ' + ' . (float) $qty, FALSE);
        $this->db->where('m_user_id', $id)->update('master_users_tbl');
      }
    }

    if (!empty($amt) && !empty($id)) {
      $this->db->set('m_user_balance', 'm_user_balance + ' . (float) $amt, FALSE)
        ->where('m_user_id', $id)->update('master_users_tbl');
    }

    return true;
  }

  public function get_bill_data($to_date, $group = '')
  {
    $result = array();
    // Start building the SQL query
    $sql = "
        SELECT DISTINCT c.m_cust_mobile, s.m_sale_customer AS customer_id 
        FROM master_sales_tbl s
        JOIN master_customer_tbl c ON s.m_sale_customer = c.m_cust_id
        WHERE s.m_sale_date = '$to_date' ";
    if (!empty($group)) {
      $sql .= " AND c.m_cust_group = '$group' ";
    }
    $sql .= " UNION 
        SELECT DISTINCT c.m_cust_mobile, r.m_recvd_customer AS customer_id 
        FROM master_recieved_tbl r
        JOIN master_customer_tbl c ON r.m_recvd_customer = c.m_cust_id
        WHERE r.m_recvd_date = '$to_date' 
        AND r.m_recvd_account IN ('0', '1') ";
    if (!empty($group)) {
      $sql .= " AND c.m_cust_group = '$group' ";
    }
    $query = $this->db->query($sql)->result();

    if (empty($query))
      return null;

    foreach ($query as $value) {
      $customer_id = $value->customer_id;
      $opening = $this->get_opening_balance($customer_id, $to_date);

      $sale_data = $this->db->select('m_sale_spo, SUM(m_sale_qty) AS total_qty, SUM(m_sale_total) AS sub_total, SUM(m_sale_crate) AS total_crate, (m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) AS total_expense')
        ->where('m_sale_customer', $customer_id)
        ->where('m_sale_date', $to_date)
        ->group_by('m_sale_spo')
        ->get('master_sales_tbl')
        ->result();

      $total_sqty = array_sum(array_column($sale_data, 'total_qty'));
      $sub_total = array_sum(array_column($sale_data, 'sub_total'));
      $total_expense = array_sum(array_column($sale_data, 'total_expense'));
      $grand_total = $sub_total + $total_expense;

      $sale_items = $this->db->select('m_sale_spo, m_sale_qty, m_sale_total, m_sale_price, m_item_name, m_item_fright, m_sale_customer, unit.m_itgrp_title AS unitname, m_item_crate,m_sale_crate')
        ->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
        ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left')
        ->where('m_sale_customer', $customer_id)
        ->where('m_sale_date', $to_date)
        ->order_by('m_item_name')
        ->get('master_sales_tbl')
        ->result();

      $amount_received = $this->db->select_sum('m_recvd_amount', 'total_received')
        ->where('m_recvd_customer', $customer_id)
        ->where('m_recvd_account', 1)
        ->where('m_recvd_type', 1)
        ->where('m_recvd_date', $to_date)
        ->get('master_recieved_tbl')
        ->row();

      $crate_received = $this->db->select("m_itgrp_id, m_itgrp_title, COALESCE((SELECT SUM(m_recvd_qty) FROM master_recieved_tbl WHERE m_recvd_customer = '$customer_id' AND m_recvd_date = '$to_date' AND m_recvd_type = 2 AND m_recvd_crate = master_itemgroup_tbl.m_itgrp_id), 0) AS total_qty")
        ->where('m_itgrp_type', 3)
        ->order_by('m_itgrp_title')
        ->get('master_itemgroup_tbl')
        ->result();
      $vouch_amtcdrt = $this->db->select('sum(m_voucher_amount) as tamountcdt')->where('m_voucher_accountid', $customer_id)->where('m_voucher_account', 1)->where('m_voucher_type', 1)->where('m_voucher_status', 1)->where('m_voucher_date', $to_date)->get('master_voucher_tbl')->row();

      $receipt_no = $this->db->select("m_recvd_voucher")
        ->where('m_recvd_customer', $customer_id)
        ->where_in('m_recvd_account', ['0', '1'])
        ->where('m_recvd_date', $to_date)
        ->get('master_recieved_tbl')
        ->row();

      if (!empty($sale_items) || !empty($amount_received) || !empty($crate_received)) {
        $res = (object) [
          'opening' => $opening,
          'invoice_no' => !empty($sale_data) ? $sale_data[0]->m_sale_spo : $receipt_no->m_recvd_voucher,
          'total_sqty' => $total_sqty,
          'sub_total' => $sub_total,
          'total_expense' => $total_expense,
          'grand_total' => $grand_total,
          'sale_data' => $sale_items,
          'total_receive' => $amount_received->total_received ?? 0,
          'total_discount' => $vouch_amtcdrt->tamountcdt ?? 0,
          'crate_data' => $crate_received,
        ];
        $result[] = $res;
      }
    }
    return $result;
  }


  public function get_cust_day_summary($cust_id, $to_date)
  {
    $cust_detail = $this->db->select('m_cust_id, m_cust_name,m_cust_hndiname, m_cust_mobile,m_cust_address,m_cust_balance,m_cust_10bal,m_cust_20bal,m_cust_25bal')
      ->where('m_cust_id', $cust_id)->get('master_customer_tbl')->row();
    $total_sqty = 0;
    $sub_total = 0;
    $total_expense = 0;
    $grand_total = 0;
    $saletotal = $this->db->select('m_sale_spo,SUM(m_sale_qty) AS tqty, SUM(m_sale_total) AS sub_total, SUM(m_sale_crate) AS tcrate, (m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) AS texpense')
      ->where('m_sale_customer', $cust_id)->where('m_sale_date', $to_date)->group_by('m_sale_spo')->get('master_sales_tbl')->result();
    if (!empty($saletotal)) {
      foreach ($saletotal as $value) {
        $total_sqty += $value->tqty;
        $sub_total += $value->sub_total;
        $total_expense += $value->texpense;
      }
      $grand_total += $sub_total + $total_expense;
    }

    // Fetch sale items
    $saleitems = $this->db->select('m_sale_spo, m_sale_qty, m_sale_total, m_sale_price, m_item_name, m_item_fright, m_sale_customer, unit.m_itgrp_title AS unitname,m_item_crate')
      ->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
      ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left')
      ->where('m_sale_customer', $cust_id)->where('m_sale_date', $to_date)->order_by('m_item_name')->get('master_sales_tbl')->result();

    // Fetch received amounts
    $amountrcvdquery = $this->db->select('sum(m_recvd_amount) as total_recieve')
      ->where('m_recvd_customer', $cust_id)->where('m_recvd_account', 1)->where('m_recvd_type', 1)->where('m_recvd_date', $to_date)->get('master_recieved_tbl')->row();

    if (!empty($from_date)) {

    }
    $vouch_amtcdrt = $this->db->select('sum(m_voucher_amount) as tamountcdt')->where('m_voucher_accountid', $cust_id)->where('m_voucher_account', 1)->where('m_voucher_type', 1)->where('m_voucher_status', 1)->where('m_voucher_date', $to_date)->get('master_voucher_tbl')->row();

    // Fetch received crates and group by m_itgrp_title, ensuring missing quantities are treated as 0
    $cratercvdquery = $this->db->select("m_itgrp_id,m_itgrp_title, COALESCE((SELECT SUM(m_recvd_qty) FROM master_recieved_tbl WHERE m_recvd_customer = '$cust_id' AND m_recvd_date = '$to_date' AND m_recvd_type = 2 AND m_recvd_crate = master_itemgroup_tbl.m_itgrp_id), 0) AS total_qty")
      ->where('m_itgrp_type', 3)->order_by('m_itgrp_title')->get('master_itemgroup_tbl')->result();

    $recipt_no = $this->db->select('m_recvd_voucher')->where('m_recvd_customer', $cust_id)->where_in('m_recvd_account', [0, 1])
      ->where('m_recvd_date', $to_date)->get('master_recieved_tbl')->row();

    return (!empty($saleitems) || !empty($recipt_no)) ? (object) [
      'cust_detail' => $cust_detail,
      'invoice_no' => !empty($saletotal) ? $saletotal[0]->m_sale_spo : $recipt_no->m_recvd_voucher,
      'total_sqty' => $total_sqty,
      'sub_total' => $sub_total,
      'total_expense' => $total_expense,
      'grand_total' => $grand_total,
      'sale_data' => $saleitems,
      'total_recieve' => $amountrcvdquery->total_recieve,
      'total_discount' => $vouch_amtcdrt->tamountcdt,
      'crate_data' => $cratercvdquery,
    ] : null;
  }

  public function get_custid_by_date($to_date)
  {
    $query = $this->db->query("
        SELECT DISTINCT c.m_cust_mobile, s.m_sale_customer AS customer_id 
        FROM master_sales_tbl s
        JOIN master_customer_tbl c ON s.m_sale_customer = c.m_cust_id
        WHERE s.m_sale_date = '$to_date'
        
        UNION
        
        SELECT DISTINCT c.m_cust_mobile, r.m_recvd_customer AS customer_id 
        FROM master_recieved_tbl r
        JOIN master_customer_tbl c ON r.m_recvd_customer = c.m_cust_id
        WHERE r.m_recvd_date = '$to_date' 
        AND (r.m_recvd_account = '1' OR r.m_recvd_account = '0')
    ");

    return $query->result_array();
  }


  public function get_last_saledate($cust_id)
  {
    $sql = "SELECT sale.last_sale_date, rec.last_recvd_date
              FROM master_customer_tbl mc
              LEFT JOIN (
                  SELECT m_sale_customer, MAX(m_sale_date) as last_sale_date
                  FROM master_sales_tbl
                  GROUP BY m_sale_customer
              ) as sale ON sale.m_sale_customer = mc.m_cust_id
              LEFT JOIN (
                  SELECT m_recvd_customer, MAX(m_recvd_date) as last_recvd_date
                  FROM master_recieved_tbl
                  WHERE m_recvd_account = 0 OR m_recvd_account = 1
                  GROUP BY m_recvd_customer
              ) as rec ON rec.m_recvd_customer = mc.m_cust_id
              WHERE mc.m_cust_id = ?";

    $query = $this->db->query($sql, [$cust_id])->result();

    return !empty($query) ? $query[0] : null;
  }


  public function get_custid_by_last_sale($days, $group)
  {
    $last_ago = date('Y-m-d', strtotime("-{$days} days"));

    // Group condition
    $group_condition = "";
    if ($group == 'o') {
      $group_condition = "AND mc.m_cust_group = 0";
    } else if (!empty($group)) {
      $group_condition = "AND mc.m_cust_group = " . $this->db->escape($group);
    }

    $sql = "
          SELECT 
              mc.m_cust_id, 
              mc.m_cust_name, 
              mc.m_cust_hndiname, 
              mc.m_cust_mobile, 
              mg.m_group_name,
              sale.last_sale_date,
              rec.last_recvd_date
          FROM master_customer_tbl mc
          LEFT JOIN (
              SELECT m_sale_customer, MAX(m_sale_date) as last_sale_date
              FROM master_sales_tbl
              GROUP BY m_sale_customer
          ) as sale ON sale.m_sale_customer = mc.m_cust_id
          LEFT JOIN (
              SELECT m_recvd_customer, MAX(m_recvd_date) as last_recvd_date
              FROM master_recieved_tbl
              WHERE m_recvd_account = 0 OR m_recvd_account = 1
              GROUP BY m_recvd_customer
          ) as rec ON rec.m_recvd_customer = mc.m_cust_id
          LEFT JOIN master_group_tbl mg ON mg.m_group_id = mc.m_cust_group
          WHERE 1=1 {$group_condition}
          AND (
              (sale.last_sale_date IS NULL OR sale.last_sale_date <= '{$last_ago}')
              OR
              (rec.last_recvd_date IS NULL OR rec.last_recvd_date <= '{$last_ago}')
          )
      ";

    $query = $this->db->query($sql)->result();

    return array_values(array_filter(array_map(function ($customer) {
      $ledger = $this->get_opening_balance($customer->m_cust_id, date('Y-m-d'));

      // Filter out customers with zero balance amounts
      if (
        !empty($ledger) &&
        ((isset($ledger['balance_amount']) && $ledger['balance_amount'] > 0) ||
          (isset($ledger['balance_crate']) && $ledger['balance_crate'] > 0))
      ) {
        return [
          "m_cust_id" => $customer->m_cust_id,
          "m_cust_name" => $customer->m_cust_name,
          "m_cust_hndiname" => $customer->m_cust_hndiname,
          "m_cust_mobile" => $customer->m_cust_mobile,
          "m_group_name" => $customer->m_group_name,
          "last_recvd_date" => $customer->last_recvd_date,
          "last_sale_date" => $customer->last_sale_date,
          "total_balance" => (int) ($ledger['balance_amount'] ?? 0),
          "total_crate_balance" => (int) ($ledger['balance_crate'] ?? 0),
        ];
      }
      return null;
    }, $query)));
  }

  //===================== System setting =======================//

  public function update_profile()
  {
    $update_data = array(
      "m_user_name" => $this->input->post('m_user_name'),
      // "m_user_email"   => $this->input->post('m_admin_email'),
      "m_user_loginid" => $this->input->post('m_user_loginid'),
      "m_user_password" => $this->input->post('m_user_password'),
      "m_user_mobile" => $this->input->post('m_user_mobile'),
      "m_user_image" => $this->input->post('pre_m_user_image'),
    );

    if (!empty($_FILES['m_user_image']['name'])) {
      $config['upload_path'] = 'uploads/users/';
      $config['allowed_types'] = 'jpg|jpeg|png';
      $config['remove_spaces'] = TRUE;
      $config['file_name'] = $_FILES['m_user_image']['name'];
      //Load upload library and initialize configuration
      $this->load->library('upload', $config);
      $this->upload->initialize($config);

      if ($this->upload->do_upload('m_user_image')) {
        $uploadData = $this->upload->data();

        if (!empty($update_data['m_user_image'])) {
          if (file_exists($config['upload_path'] . $update_data['m_user_image'])) {
            unlink($config['upload_path'] . $update_data['m_user_image']); /* deleting Image */
          }
        }

        $update_data['m_user_image'] = $uploadData['file_name'];
      }
    }

    $this->db->where('m_user_id', $this->session->userdata('user_id'));
    return $this->db->update('master_users_tbl', $update_data);
  }
  public function get_application_settings()
  {
    $res = $this->db->get('application_settings')->result();
    return $res;
  }
  public function update_application_settings()
  {
    if (!empty($_FILES['m_app_logo']['name'])) {
      $config['file_name'] = $_FILES['m_app_logo']['name'];
      $config['upload_path'] = 'uploads/';
      $config['allowed_types'] = 'jpg|jpeg|png';
      $config['remove_spaces'] = TRUE;
      $config['file_name'] = $_FILES['m_app_logo']['name'];
      //Load upload library and initialize configuration
      $this->load->library('upload', $config);
      $this->upload->initialize($config);
      if ($this->upload->do_upload('m_app_logo')) {
        $uploadData = $this->upload->data();
        if (!empty($update_data['m_app_logo'])) {
          if (file_exists($config['m_app_logo'] . $update_data['m_app_logo'])) {
            unlink($config['upload_path'] . $update_data['m_app_logo']); /* deleting Image */
          }
        }
        $m_app_logo = $uploadData['file_name'];
      }
    } else {
      $m_app_logo = $this->input->post('applogo');
    }
    if (!empty($_FILES['m_app_icon']['name'])) {
      $config['file_name'] = $_FILES['m_app_icon']['name'];
      $config['upload_path'] = 'uploads/';
      $config['allowed_types'] = 'jpg|jpeg|png';
      $config['remove_spaces'] = TRUE;
      $config['file_name'] = $_FILES['m_app_icon']['name'];
      //Load upload library and initialize configuration
      $this->load->library('upload', $config);
      $this->upload->initialize($config);
      if ($this->upload->do_upload('m_app_icon')) {
        $uploadData = $this->upload->data();
        if (!empty($update_data['m_app_icon'])) {
          if (file_exists($config['m_app_icon'] . $update_data['m_app_icon'])) {
            unlink($config['upload_path'] . $update_data['m_app_icon']); /* deleting Image */
          }
        }
        $m_app_icon = $uploadData['file_name'];
      }
    } else {
      $m_app_icon = $this->input->post('appfavicon');
    }
    if (!empty($_FILES['m_app_black_logo']['name'])) {
      $config['file_name'] = $_FILES['m_app_black_logo']['name'];
      $config['upload_path'] = 'uploads/';
      $config['allowed_types'] = 'jpg|jpeg|png';
      $config['remove_spaces'] = TRUE;
      $config['file_name'] = $_FILES['m_app_black_logo']['name'];
      //Load upload library and initialize configuration
      $this->load->library('upload', $config);
      $this->upload->initialize($config);
      if ($this->upload->do_upload('m_app_black_logo')) {
        $uploadData = $this->upload->data();
        if (!empty($update_data['m_app_black_logo'])) {
          if (file_exists($config['m_app_black_logo'] . $update_data['m_app_black_logo'])) {
            unlink($config['upload_path'] . $update_data['m_app_black_logo']); /* deleting Image */
          }
        }
        $m_app_black_logo = $uploadData['file_name'];
      }
    } else {
      $m_app_black_logo = $this->input->post('app_black_logo');
    }

    if (!empty($_FILES['m_app_white_logo']['name'])) {
      $config['file_name'] = $_FILES['m_app_white_logo']['name'];
      $config['upload_path'] = 'uploads/';
      $config['allowed_types'] = 'jpg|jpeg|png';
      $config['remove_spaces'] = TRUE;
      $config['file_name'] = $_FILES['m_app_white_logo']['name'];
      //Load upload library and initialize configuration
      $this->load->library('upload', $config);
      $this->upload->initialize($config);
      if ($this->upload->do_upload('m_app_white_logo')) {
        $uploadData = $this->upload->data();
        if (!empty($update_data['m_app_white_logo'])) {
          if (file_exists($config['m_app_white_logo'] . $update_data['m_app_white_logo'])) {
            unlink($config['upload_path'] . $update_data['m_app_white_logo']); /* deleting Image */
          }
        }
        $m_app_white_logo = $uploadData['file_name'];
      }
    } else {
      $m_app_white_logo = $this->input->post('app_white_logo');
    }

    $data = array(
      "m_app_name" => $this->input->post('m_app_name'),
      "m_app_title" => $this->input->post('m_app_title'),
      "m_app_email" => $this->input->post('m_app_mail'),
      "m_app_mobile" => $this->input->post('m_app_contact'),
      "m_app_alt_mobile" => $this->input->post('m_app_alt_contact'),
      "m_app_address" => $this->input->post('m_app_address'),
      "m_app_fb" => $this->input->post('m_app_fesbook'),
      "m_app_insta" => $this->input->post('m_app_instagram'),
      "m_app_youtube" => $this->input->post('m_app_youtude'),
      "m_app_linkedin" => $this->input->post('m_app_linkedin'),
      "m_app_whatsapp" => $this->input->post('m_app_whatsapp'),
      "m_app_twitter" => $this->input->post('m_app_twitter'),
      "m_app_logo" => "$m_app_logo",
      "m_app_icon" => "$m_app_icon",
      "m_app_black_logo" => "$m_app_black_logo",
      "m_app_white_logo" => "$m_app_white_logo",
    );
    // print_r($data);
    $this->db->where('m_app_id', 1);
    $this->db->update('application_settings', $data);

    $update_data = array(
      "m_admin_branch" => $this->input->post('m_admin_branch'),
      "m_admin_pass" => $this->input->post('m_admin_pass'),
      "m_admin_login_id" => $this->input->post('m_admin_login_id')
    );
    $this->db->where('m_admin_id', $this->session->userdata('user_id'));
    $this->db->update('master_admin_tbl', $update_data);
    return true;
  }

  public function update_cust_bal_cron($cust_id, $from_date, $type)
  {

    $opening_bal = $this->db->select('m_cust_opening,m_cust_crateOP')->where('m_cust_id', $cust_id)->get('master_customer_tbl')->row();
    if ($type == 1) {
      $sub_total = 0;
      $total_expense = 0;
      $grand_total = 0;

      if (!empty($from_date)) {
        $this->db->where('m_sale_date <=', $from_date);
      }

      $salequery = $this->db->select('sum(m_sale_total) as sub_total,(m_sale_comm+m_sale_fright+m_sale_hamali+m_sale_others) as texpense')->where('m_sale_customer', $cust_id)->group_by('m_sale_spo')->get('master_sales_tbl')->result();
      if (!empty($salequery)) {
        foreach ($salequery as $key) {
          $sub_total += $key->sub_total;
          $total_expense += $key->texpense;
          $grand_total += ($key->sub_total + $key->texpense);
        }
      }

      if (!empty($from_date)) {
        $this->db->where('m_recvd_date <=', $from_date);
      }
      $amountrcvdquery = $this->db->select('sum(m_recvd_amount) as tamountrcvd')->where('m_recvd_customer', $cust_id)->where('m_recvd_account', 1)->where('m_recvd_type', 1)->get('master_recieved_tbl')->result();

      if (!empty($from_date)) {
        $this->db->where('m_voucher_date <=', $from_date);
      }
      $vouch_amtcdrt = $this->db->select('sum(m_voucher_amount) as tamountcdt')->where('m_voucher_accountid', $cust_id)->where('m_voucher_account', 1)->where('m_voucher_type', 1)->where('m_voucher_status', 1)->get('master_voucher_tbl')->result();
      if (!empty($from_date)) {
        $this->db->where('m_voucher_date <=', $from_date);
      }
      $vouch_amtdbt = $this->db->select('sum(m_voucher_amount) as tamountdbt')->where('m_voucher_accountid', $cust_id)->where('m_voucher_account', 1)->where('m_voucher_type', 2)->where('m_voucher_status', 1)->get('master_voucher_tbl')->result();

      $balance_amt = $opening_bal->m_cust_opening + (($grand_total + $vouch_amtdbt[0]->tamountdbt) - ($amountrcvdquery[0]->tamountrcvd + $vouch_amtcdrt[0]->tamountcdt));

      $this->db->set('m_cust_balance', $balance_amt)->where('m_cust_id', $cust_id)->update('master_customer_tbl');
    } else if ($type == 2) {
      $balanceFields = [
        '10 KG' => 'm_cust_10bal',
        '20 KG' => 'm_cust_20bal',
        '25 KG' => 'm_cust_25bal'
      ];

      $all_crates = $this->Master_model->all_itemgroup(3);
      $openin_crate_bal = explode(',', $opening_bal->m_cust_crateOP);
      foreach ($all_crates as $key) {
        $crattype_bal = 0;

        if (isset($balanceFields[$key->m_itgrp_title])) {
          $index = array_search($key->m_itgrp_title, array_keys($balanceFields));
          $crattype_bal = isset($openin_crate_bal[$index]) ? (int) $openin_crate_bal[$index] : 0;
        }

        if (!empty($from_date)) {
          $this->db->where('m_sale_date <=', $from_date);
        }

        $crategiven = $this->db->select('sum(m_sale_crate) as tcrate,m_itgrp_title')->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')->where('m_sale_customer', $cust_id)->where('m_item_crate', $key->m_itgrp_id)->group_by('m_item_crate')->get('master_sales_tbl')->row();

        if (!empty($from_date)) {
          $this->db->where('m_recvd_date <=', $from_date);
        }
        $cratercvdquery = $this->db->select('sum(m_recvd_qty) as tcrateqty,m_itgrp_title')->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left')->where('m_recvd_customer', $cust_id)->where('m_recvd_type', 2)->where('m_recvd_crate', $key->m_itgrp_id)->group_by('m_recvd_crate')->get('master_recieved_tbl')->row();

        $createbalance = (int) $crattype_bal + (($crategiven ? $crategiven->tcrate : 0) - ($cratercvdquery ? $cratercvdquery->tcrateqty : 0));

        if (!empty($key->m_itgrp_title) && isset($balanceFields[$key->m_itgrp_title])) {
          $this->db->set($balanceFields[$key->m_itgrp_title], $createbalance)->where('m_cust_id', $cust_id)->update('master_customer_tbl');
        }
      }
    }

    return true;
  }
}
