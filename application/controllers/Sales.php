<?php defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class Sales extends CI_Controller
{
  public function index()
  {
    echo "Welcome";
  }
  //========================= Item_issue ===========================//

  public function issue_item_list()
  {
    $data = $this->login_details();

    $data['pagename'] = "All Issue list";
    $curdate = date('Y-m-d');
    $data['from_date'] = $this->input->post('from_date') ?: $curdate;
    $data['to_date'] = $this->input->post('to_date') ?: $curdate;
    $data['staff_id'] = $this->input->post('staff_id');
    $data['lot_no'] = $this->input->post('lot_no');
    $data['staff_list'] = $this->Main_model->get_active_user_list(1);
    $data['item_list'] = $this->Master_model->get_all_item();
    $data['all_value'] = $this->Main_model->issue_item_group($data['from_date'], $data['to_date'], $data['staff_id'], $data['lot_no']);


    $this->load->view('sale_issue_list', $data);
  }

  public function add_issue_item()
  {
    $data = $this->login_details();

    $data['pagename'] = "Add Issue Item";
    $data['id'] = $this->input->get('id');
    if (!empty($data['id'])) {
      $data['edit_value'] = $this->Main_model->get_edit_item_issue($data['id']);
    }
    $data['staff_list'] = $this->Main_model->get_active_user_list(1);
    $data['item_list'] = $this->Main_model->get_avilable_item(null, 1);
    // $data['all_value'] = $this->Main_model->all_custgrp();

    $this->load->view('add_sale_issue', $data);
  }

  public function insert_issue_item()
  {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->insert_issue_item()) {

        if ($data == 1) {
          $info = array(
            'status' => 'success',
            'message' => 'Data has been Added successfully!'
          );
        } else if ($data == 2) {
          $info = array(
            'status' => 'success',
            'message' => 'Data Updated Successfully'
          );
        } else {
          $info = array(
            'status' => 'error',
            'message' => 'Data Already Exist'
          );
        }
      } else {
        $info = array(
          'status' => 'error',
          'message' => 'Some problem Occurred!! please try again'
        );
      }
      echo json_encode($info);
    }
  }

  public function lotwise_insert_issue()
  {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->lotwise_insert_issue()) {

        $info = array(
          'status' => 'success',
          'message' => 'Issue has been Added successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => 'Some problem Occurred!! please try again'
        );
      }
      echo json_encode($info);
    }
  }

  public function delete_issue_item()
  {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_issue_item()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => 'Some problem Occurred!! please try again'
        );
      }
      echo json_encode($info);
    }
  }

  public function delete_issue_item_id()
  {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_issue_item_id()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => 'Some problem Occurred!! please try again'
        );
      }
      echo json_encode($info);
    }
  }


  //========================= Sales ===========================//

  public function sales_list()
  {
    $data = $this->login_details();
    $data['pagename'] = "All Sales list";
    $curdate = date('Y-m-d');
    $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d');
    $data['to_date'] = $this->input->post('to_date') ?: $curdate;
    $data['search_in'] = $this->input->post('search_in');
    $data['group_id'] = $this->input->post('group_id');
    $data['lot_no'] = $this->input->post('lot_no');
    $data['group_dtl'] = $this->Master_model->get_all_group(1);
    $data['staff_list'] = $this->Main_model->get_active_user_list(1);
    $data['item_list'] = $this->Master_model->get_all_item();
    $data['custo_list'] = $this->Main_model->get_cust_active_list();
    $data['all_value'] = $this->Main_model->sales_group($data['from_date'], $data['to_date'], null, $data['group_id'], $data['search_in'], null, $data['lot_no']);

    if (!empty($this->input->post('print_bill'))) {
      $this->mulbill_print($data['to_date'], $data['group_id']);
    } else {
      $this->load->view('sales_list', $data);
    }
  }

  public function add_sales()
  {
    $data = $this->login_details();

    $data['pagename'] = "Add Sales";
    $data['id'] = $this->input->get('id');
    // print_r($data['id']); die ;
    $data['staff_list'] = $this->Main_model->get_active_user_list(1);
    if (!empty($data['id'])) {
      $data['edit_value'] = $this->Main_model->get_edit_sales($data['id']);
    }
    $data['custo_list'] = $this->Main_model->get_cust_active_list();
    $data['item_list'] = $this->Main_model->get_avilable_item(null, 1);
    // $data['all_value'] = $this->Main_model->all_custgrp();

    $this->load->view('add_sales', $data);
  }

  public function insert_sales()
  {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->insert_sales()) {

        if ($data == 1) {
          $info = array(
            'status' => 'success',
            'message' => 'Sale has been Added successfully!'
          );
        } else if ($data == 2) {
          $info = array(
            'status' => 'success',
            'message' => 'Sale Details Updated Successfully'
          );
        } else {
          $info = array(
            'status' => 'error',
            'message' => 'Sale Already Exist'
          );
        }
      } else {
        $info = array(
          'status' => 'error',
          'message' => 'Some problem Occurred!! please try again'
        );
      }
      echo json_encode($info);
    }
  }

  public function lotwise_insert_sales()
  {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->lotwise_insert_sales()) {
        $info = array(
          'status' => 'success',
          'message' => 'Sale has been Added successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => 'Some problem Occurred!! please try again'
        );
      }
      echo json_encode($info);
    }
  }

  public function delete_sales()
  {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_sales()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => 'Some problem Occurred!! please try again'
        );
      }
      echo json_encode($info);
    }
  }

  public function delete_sales_id()
  {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_sales_id()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => 'Some problem Occurred!! please try again'
        );
      }
      echo json_encode($info);
    }
  }

  public function lotwise_sale_print()
  {
    $data['all_value'] = $this->Report_model->lotwise_sales_list($this->input->get('purid'), $this->input->get('item_id'));
    $this->load->view('lotwise_sale_print', $data);
  }

  public function mulbill_print($sal_date, $group)
  {

    $data['sal_date'] = $sal_date;
    $data['data'] = $this->Main_model->get_bill_data($sal_date, $group);
    $data['pgtype'] = 1;
    $this->load->view('bill', $data);
  }

  public function bill_print()
  {

    $data['id'] = $this->input->get('id');
    $data['pgtype'] = 2;
    $data['edit_value'] = $this->Main_model->get_edit_sales($data['id']);
    $data['customer_old_balance'] = $this->Main_model->get_opening_balance($data['edit_value'][0]->m_sale_customer, date('Y-m-d', strtotime(date('Y-m-d') . '-1 day')));
    $data['customer_today_balance'] = $this->Main_model->get_customer_balance($data['edit_value'][0]->m_sale_customer, date('Y-m-d'), 1);
    $this->load->view('bill', $data);
  }

  public function crate_bill_print($id)
  {

    $data['id'] = $id;
    $data['type'] = 2;
    $data['edit_value'] = $this->Main_model->get_received_detail(2, $id);
    $data['customer_old_balance'] = $this->Main_model->get_opening_balance($data['edit_value'][0]->m_recvd_customer, date('Y-m-d', strtotime(date('Y-m-d') . '-1 day')));
    $data['customer_today_balance'] = $this->Main_model->get_customer_balance($data['edit_value'][0]->m_recvd_customer, date('Y-m-d'), 1);
    // echo '<pre>';   print_r($data['customer_today_balance']);
    // die;
    $this->load->view('crate_recived_bill', $data);
  }

  public function payment_bill_print($id)
  {

    $data['id'] = $id;
    $data['type'] = 1;
    $data['edit_value'] = $this->Main_model->get_received_detail(1, $id);
    $data['customer_old_balance'] = $this->Main_model->get_opening_balance($data['edit_value'][0]->m_recvd_customer, date('Y-m-d', strtotime(date('Y-m-d') . '-1 day')));
    $data['customer_today_balance'] = $this->Main_model->get_customer_balance($data['edit_value'][0]->m_recvd_customer, date('Y-m-d'), 1);
    $this->load->view('crate_recived_bill', $data);
  }

  public function generate_report_crate($id, $date)
  {
    $data['type'] = 2;
    $data['pagename'] = 'Crate Received Report';
    $data['user_details'] = $this->Api_Model->user_details($id);
    $data['crate_list'] = $this->Master_model->all_active_itemgroup(3);
    $data['all_value'] = $this->Api_Model->get_user_crate_received($id, $date);

    $this->load->view('ganerate_recieved_print', $data);
  }

  public function generate_report_payment($id, $date)
  {

    $data['type'] = 1;
    $data['pagename'] = 'Cash Received Report';
    $data['user_details'] = $this->Api_Model->user_details($id);
    $data['all_value'] = $this->Api_Model->get_user_payment_received($id, $date);
    $data['expense'] = $this->db->where('m_exp_user', $id)->where('m_exp_added_by', $id)->where('m_exp_date', $date)->where('m_exp_name', 83)->get('master_expenses_tbl')->row();

    $this->load->view('ganerate_recieved_print', $data);
  }

  public function generate_report_sale($id, $date)
  {

    $data['type'] = 3;
    $data['pagename'] = 'Today Sale Report';
    $data['user_details'] = $this->Api_Model->user_details($id);
    $data['all_value'] = $this->Api_Model->get_user_sales($id, $date);

    $this->load->view('ganerate_recieved_print', $data);
  }

  //========================= Sales ===========================//

  //========================= Purchase ===========================//

  public function purchase_list()
  {
    $data = $this->login_details();

    $data['pagename'] = "All Purchase list";
    $curdate = date('Y-m-d');
    $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d');
    $data['to_date'] = $this->input->post('to_date') ?: $curdate;
    $data['suppiler_id'] = $this->input->post('suppiler_id');
    $data['suplier_list'] = $this->Main_model->get_active_user_list(2);
    $data['item_list'] = $this->Master_model->get_all_item();
    $data['all_value'] = $this->Main_model->purchase_group($data['from_date'], $data['to_date'], $data['suppiler_id']);


    $this->load->view('purchase_list', $data);
  }

  // New: show purchase items (not grouped)
  public function purchase_item_list()
  {
    $data = $this->login_details();

    $data['pagename'] = "All Purchase Item list";
    $curdate = date('Y-m-d');
    $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d');
    $data['to_date'] = $this->input->post('to_date') ?: $curdate;
    $data['suppiler_id'] = $this->input->post('suppiler_id');
    $data['search_in'] = $this->input->post('search_in');
    $data['suplier_list'] = $this->Main_model->get_active_user_list(2);
    $data['all_value'] = $this->Main_model->get_purchase_items($data['from_date'], $data['to_date'], $data['suppiler_id'], $data['search_in']);



    $this->load->view('purchase_item_list', $data);
  }

  public function purchase_sales_list()
  {
    $all_value = $this->Main_model->get_edit_sales(null, $this->input->post('purid'));
    echo json_encode($all_value);
  }

  public function purchase_issue_list()
  {
    $purid = $this->input->post('purid');
    $itype = $this->input->post('itype'); // 1 = issue, 2 = return

    $res = $this->Main_model->get_edit_item_issue(null, $purid, $itype);
    $final_res = array();
    $sumtqty = 0;
    $sumtSaleqty = 0;
    $sumtPenqty = 0;
    $sumtamt = 0;
    if (!empty($res)) {
      foreach ($res as $value) {
        $indicator = $this->Report_model->get_issue_itemsale(null, $value->si_issue_id);
        if ($value->si_issue_type == 2) {
          $badge = '<span class="badge btn btn-danger">return</span>';
        } else if ($indicator['status'] == 2) {
          $badge = '<span class="badge btn btn-warning">Stock Pending</span>';
        } else if ($indicator['status'] == 3) {
          $badge = '<span class="badge btn btn-success">Completed</span>';
        }
        $sumtqty += $value->si_issue_qty;
        $sumtamt += isset($indicator) ? $indicator['total_sale_amount'] : 0;
        $sumtSaleqty += isset($indicator) ? $indicator['total_sale_qty'] : 0;
        $sumtPenqty += isset($indicator) ? $indicator['total_balance_qty'] : 0;
       
        $result = array(
          "si_issue_id" => $value->si_issue_id,
          "si_issue_type" => $value->si_issue_type,
          "si_issue_spo" => $value->si_issue_spo,
          "si_issue_date" => $value->si_issue_date,
          "m_user_name" => $value->m_user_name,
          "si_issue_user" => $value->si_issue_user,
          "m_item_name" => $value->m_item_name,
          "si_issue_qty" => $value->si_issue_qty,
          "si_issue_weight" => $value->si_issue_weight,
          "si_issue_trackno" => $value->si_issue_trackno,
          "badge" => $badge,
          "total_sale_qty" => isset($indicator) ? $indicator['total_sale_qty'] : 0,
          "total_balance_qty" => isset($indicator) ? $indicator['total_balance_qty'] : 0,
          "total_sale_amount" => isset($indicator) ? $indicator['total_sale_amount'] : 0,
        );
        $final_res[] = $result;
      }
    }
    echo json_encode($final_res);
  }

  // AJAX: update sale lot id
  public function ajax_update_sale_lot()
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
      return;
    }

    $id = $this->input->post('id');
    $lot = $this->input->post('m_sale_lot');

    if (empty($id) || $lot === null) {
      echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
      return;
    }

    $res = $this->db->where('m_sale_id', $id)->update('master_sales_tbl', ['m_sale_lot' => $lot, 'm_sale_updatedon' => date('Y-m-d H:i:s'), 'm_sale_updatedby' => $this->session->userdata('m_user_id') ?: 0]);

    if ($res) {
      echo json_encode(['status' => 'success', 'message' => 'Sale lot updated']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Update failed']);
    }
  }

  // AJAX: update issue lot id
  public function ajax_update_issue_lot()
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
      return;
    }

    $id = $this->input->post('id');
    $lot = $this->input->post('si_issue_lotno');

    if (empty($id) || $lot === null) {
      echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
      return;
    }

    $res = $this->db->where('si_issue_id', $id)->update('staff_itemissue_tbl', ['si_issue_lotno' => $lot]);

    if ($res) {
      echo json_encode(['status' => 'success', 'message' => 'Issue lot updated']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Update failed']);
    }
  }

  public function add_purchase()
  {
    $data = $this->login_details();

    $data['pagename'] = "Add Purchase";
    $data['id'] = $this->input->get('id');
    $data['edit_value'] = $this->Main_model->get_edit_purchase($data['id']);
    $data['inter_expense'] = $this->Main_model->get_purchase_expense($data['id']);
    // print_r($data['inter_expense']); die ;
    $data['suplier_list'] = $this->Main_model->get_active_user_list(2);
    $data['item_list'] = $this->Master_model->get_all_item();
    $data['expense_lst'] = $this->Master_model->get_all_active_group(2);
    // $data['all_value'] = $this->Main_model->all_custgrp();


    $this->load->view('add_purchase', $data);
  }

  public function insert_purchase()
  {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->insert_purchase()) {

        if ($data == 1) {
          $info = array(
            'status' => 'success',
            'message' => 'Purchase has been Added successfully!'
          );
        } else if ($data == 2) {
          $info = array(
            'status' => 'success',
            'message' => 'Purchase Details Updated Successfully'
          );
        } else {
          $info = array(
            'status' => 'error',
            'message' => 'Purchase Already Exist'
          );
        }
      } else {
        $info = array(
          'status' => 'error',
          'message' => 'Some problem Occurred!! please try again'
        );
      }
      echo json_encode($info);
    }
  }

  public function delete_purchase()
  {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_purchase()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => 'Some problem Occurred!! please try again'
        );
      }
      echo json_encode($info);
    }
  }

  public function delete_purchase_id()
  {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_purchase_id()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => 'Some problem Occurred!! please try again'
        );
      }
      echo json_encode($info);
    }
  }

  //========================= Purchase ===========================//

  //========================= Cash Recipt/crate recieved ===========================//

  public function recieved_list($type = 1)
  {
    $data = $this->login_details();
    if ($type == 1) {
      $data['pagename'] = "RECEIPT LIST";
    } else {
      $data['pagename'] = "CRATE RECIEVED LIST";
    }

    $data['type'] = $type;
    $curdate = date('Y-m-d');
    $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d');
    $data['to_date'] = $this->input->post('to_date') ?: $curdate;
    $data['search_in'] = $this->input->post('search_in');
    $data['recvd_account'] = $this->input->post('recvd_account');
    $data['recvd_method'] = $this->input->post('recvd_method');
    $data['group_id'] = $this->input->post('group_id');
    $data['group_dtl'] = $this->Master_model->get_all_group(1);
    $data['paymode_lst'] = $this->Master_model->get_payment_methods();
    $data['user_list'] = $this->Main_model->get_active_user_list(1);
    $data['general_list'] = $this->Main_model->get_active_user_list(4);
    $data['investment_list'] = $this->Main_model->get_active_user_list(5);
    $data['custo_list'] = $this->Main_model->get_cust_active_list();
    $data['all_value'] = $this->Main_model->get_received_list($type, $data['from_date'], $data['to_date'], null, $data['recvd_account'], $data['recvd_method'], $data['group_id'], $data['search_in']);
    $data['crate_lst'] = $this->Master_model->all_active_itemgroup(3);

    $this->load->view('payment_recived_list', $data);
  }

  public function insert_recieved_data()
  {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $data = $this->Main_model->insert_recieved_data();
    }
    redirect($_SERVER['HTTP_REFERER']);
  }

  public function update_recieved_data()
  {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $data = $this->Main_model->update_recieved_data();
    }
    redirect($_SERVER['HTTP_REFERER']);
  }

  public function delete_recieved_data()
  {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_recieved_data()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => 'Some problem Occurred!! please try again'
        );
      }
      echo json_encode($info);
    }
  }

  //========================= Cash Recipt/crate recieved ===========================//

  //========================= payment/crate Paid ===========================//

  public function payment_list($type = 1)
  {
    $data = $this->login_details();
    if ($type == 1) {
      $data['pagename'] = "PAYMENT LIST";
    } else {
      $data['pagename'] = "CRATE ISSUE LIST";
    }

    $data['type'] = $type;
    $curdate = date('Y-m-d');
    $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d');
    $data['to_date'] = $this->input->post('to_date') ?: $curdate;
    $data['payment_account'] = $this->input->post('payment_account');
    $data['payment_method'] = $this->input->post('payment_method');
    $data['search_in'] = $this->input->post('search_in');
    $data['supplier_list'] = $this->Main_model->get_active_user_list(2);
    $data['loader_list'] = $this->Main_model->get_active_user_list(3);
    $data['staff_list'] = $this->Main_model->get_active_user_list(1);
    $data['general_list'] = $this->Main_model->get_active_user_list(4);
    $data['investment_list'] = $this->Main_model->get_active_user_list(5);
    $data['expense_lst'] = $this->Master_model->get_all_active_group(2);
    $data['paymode_lst'] = $this->Master_model->get_payment_methods();
    $data['all_value'] = $this->Main_model->get_payment_list($type, $data['from_date'], $data['to_date'], null, $data['payment_account'], $data['payment_method'], $data['search_in']);
    $data['crate_lst'] = $this->Master_model->all_active_itemgroup(3);
    // echo '<pre>';print_r($data['all_value']); die ;
    $this->load->view('payment_paid_list', $data);
  }

  public function insert_payment_data()
  {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $data = $this->Main_model->insert_payment_data();
    }
    redirect($_SERVER['HTTP_REFERER']);
  }

  public function update_payment_data()
  {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $data = $this->Main_model->update_payment_data();
    }
    redirect($_SERVER['HTTP_REFERER']);
  }

  public function delete_payment_data()
  {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_payment_data()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => 'Some problem Occurred!! please try again'
        );
      }
      echo json_encode($info);
    }
  }
  //========================= payment/crate paid ===========================//

  //========================= Voucher ===========================//

  public function voucher_list()
  {
    $data = $this->login_details();
    $data['pagename'] = "Voucher list";

    $curdate = date('Y-m-d');
    $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d');
    $data['to_date'] = $this->input->post('to_date') ?: $curdate;
    $data['search_in'] = $this->input->post('search_in');
    $data['type'] = $this->input->post('type') ?: '';
    $data['all_value'] = $this->Main_model->get_voucher_list($data['type'], $data['from_date'], $data['to_date'], null, $data['search_in']);
    // echo '<pre>';print_r($data['all_value']); die ;
    $this->load->view('voucher_list', $data);
  }

  public function insert_voucher_data()
  {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $data = $this->Main_model->insert_voucher_data();
    }
    redirect($_SERVER['HTTP_REFERER']);
  }

  public function update_voucher_data()
  {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $data = $this->Main_model->update_voucher_data();
    }
    redirect($_SERVER['HTTP_REFERER']);
  }

  public function delete_voucher_data()
  {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_voucher_data()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => 'Some problem Occurred!! please try again'
        );
      }
      echo json_encode($info);
    }
  }

  public function get_vourcher_accounts()
  {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $acc_type = $this->input->post('acct_type');
      switch ($acc_type) {
        case 1:
          $data['list'] = $this->Main_model->get_cust_active_list();
          $data['listtype'] = 1;
          $data['listname'] = "Customer";
          break;
        case 2:
          $data['list'] = $this->Main_model->get_active_user_list(2);
          $data['listtype'] = 2;
          $data['listname'] = "Supplier";
          break;
        case 3:
          $data['list'] = $this->Master_model->get_all_active_group(2);
          $data['listtype'] = 3;
          $data['listname'] = "Expense";
          break;
        case 4:
          $data['list'] = $this->Main_model->get_active_user_list(3);
          $data['listtype'] = 2;
          $data['listname'] = "Loader";
          break;
        case 5:
          $data['list'] = $this->Main_model->get_active_user_list(1);
          $data['listtype'] = 2;
          $data['listname'] = "Staff";
          break;
        case 6:
          $data['list'] = $this->Main_model->get_active_user_list(4);
          $data['listtype'] = 2;
          $data['listname'] = "General";
          break;
        case 7:
          $data['list'] = $this->Main_model->get_active_user_list(5);
          $data['listtype'] = 2;
          $data['listname'] = "Investment";
          break;
      }

      echo json_encode($data);
    }
  }

  public function get_reciept_accounts()
  {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $acc_type = $this->input->post('acct_type');
      switch ($acc_type) {
        case 1:
          $data['list'] = $this->Main_model->get_cust_active_list();
          $data['listtype'] = 1;
          $data['listname'] = "Customer";
          break;
        case 4:
          $data['list'] = $this->Main_model->get_active_user_list(2);
          $data['listtype'] = 2;
          $data['listname'] = "Supplier";
          break;
        case 5:
          $data['list'] = $this->Master_model->get_all_active_group(2);
          $data['listtype'] = 3;
          $data['listname'] = "Expense";
          break;
        case 6:
          $data['list'] = $this->Main_model->get_active_user_list(3);
          $data['listtype'] = 2;
          $data['listname'] = "Loader";
          break;
        case 7:
          $data['list'] = $this->Master_model->get_all_active_group(3);
          $data['listtype'] = 3;
          $data['listname'] = "Bank";
          break;
        case 2:
          $data['list'] = $this->Main_model->get_active_user_list(4);
          $data['listtype'] = 2;
          $data['listname'] = "General";
          break;
        case 3:
          $data['list'] = $this->Main_model->get_active_user_list(5);
          $data['listtype'] = 2;
          $data['listname'] = "Investment";
          break;
      }

      echo json_encode($data);
    }
  }
  //========================= voucher ===========================//


  public function Reminder_list()
  {
    $data = $this->login_details();
    $data['pagename'] = "Reminder List";
    $data['pgtype'] = 1;
    $data['days'] = $this->input->post('days') ?: 15;
    $data['group_id'] = $this->input->post('group_id') ?: 'o';
    $data['group_dtl'] = $this->Master_model->get_all_group(1);
    $data['mech_value'] = $this->Main_model->get_custid_by_last_sale($data['days'], $data['group_id']);
    // echo '<pre>'; print_r($data['mech_value']);die;
    $this->load->view('reminder_list', $data);
  }
  public function send_bill_indiviouly()
  {
    $data = $this->login_details();
    $data['pagename'] = "Send Customer Summary";
    $data['pgtype'] = 2;
    $data['group_dtl'] = $this->Master_model->get_all_group(1);
    $data['cust_list'] = $this->Main_model->get_cust_active_list();
    $this->load->view('reminder_list', $data);
  }

  public function download_pdf()
  {
    $data['sal_date'] = $this->input->get('date') ?: date('Y-m-d');
    $cust_id = $this->input->get('id');
    if (!empty($data['sal_date']) and !empty($cust_id)) {
      $data['data'] = $this->Main_model->get_cust_day_summary($cust_id, date('Y-m-d', strtotime($data['sal_date'])));
      $data['customer_old_balance'] = $this->Main_model->get_opening_balance($cust_id, date('Y-m-d', strtotime($data['sal_date'] . '-1 day')));
      $html = $this->load->view('bill_2', $data, true);
      $file_name = 'summary_' . $cust_id . date('dmy') . '.pdf';
      $this->generate_pdf($html, $file_name);
    }
  }
  public function send_reminder_msg()
  {
    $sal_date = date('Y-m-d');
    $cust_ids = $this->input->post('cust_ids');
    if (!empty($cust_ids)) {
      foreach ($cust_ids as $cust_id) {
        $cust_bal = $this->Main_model->get_opening_balance($cust_id, $sal_date);
        $last_date = $this->Main_model->get_last_saledate($cust_id);

        if (!empty($cust_bal)) {
          $oldcrate = "";
          foreach ($cust_bal['crateitems'] as $cau => $kry) {
            $oldcrate .= $kry['name'] . "- " . $kry['balance'] . ',';
          }
          $lastsale = !empty($last_date->last_sale_date) ? date('d/m/Y', strtotime($last_date->last_sale_date)) : date('d/m/Y', strtotime('2024-04-01'));
          $lastrvd = !empty($last_date->last_recvd_date) ? date('d/m/Y', strtotime($last_date->last_recvd_date)) : date('d/m/Y', strtotime('2024-04-01'));
          $url = "https://www.ajayfruits.in/Sales/download_pdf?date=" . $sal_date . "&id=" . $cust_id;
          $customer_name = !empty($cust_bal['m_cust_hndiname']) ? $cust_bal['m_cust_hndiname'] : $cust_bal['cust_name'];
          $message = "आपके तरफ़ नीचे दी गई रकम/ केरेट बकाया है\nजमा करवा कर खाता क्लियर करे\n\n*नाम - " . $customer_name . "*\n\n🔹 *विवरण*\n\nआख़िरी बार ख़रीदी: " . $lastsale . "\nआखरी बार जमा: " . $lastrvd . "\n📌 *आज टोटल रकम बाकी:*" . $cust_bal['balance_amount'] . "\n\n📦 *खाली केरेट विवरण:*\n\n🔹 *टोटल बाकी:* " . $oldcrate . "\n\n🙏🏻*अजय कुशवाहा एंड कंपनी* \n\n 🚀 *बिल डाउनलोड करें:* [📥 Download PDF] $url";
        }
        if (!empty($message)) {
          $response = $this->Api_Model->send_whatsapp_message($cust_bal['cust_mobile'], $message);
        }
      }
      if ($response) {
        $info = array(
          'status' => 'success',
          'message' => 'Reminder Send Successfilly!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => 'Failed to send Summary!'
        );
      }
    } else {
      $info = array(
        'status' => 'error',
        'message' => 'Customer Id Not Found!'
      );
    }
    echo json_encode($info);
  }


  public function send_bill()
  {
    $sal_date = $this->input->post('to_date') ?: date('Y-m-d');
    $cust_ids = $this->input->post('cust_id');
    if (!empty($sal_date) and !empty($cust_ids)) {
      foreach ($cust_ids as $cust_id) {
        $cust_dtl = $this->Main_model->get_cust_active_list($cust_id);
        $message = $this->bill_msg($sal_date, $cust_id);
        if (!empty($message)) {
          $response = $this->Api_Model->send_whatsapp_message($cust_dtl[0]->m_cust_mobile, $message);
        }
      }
      if ($response) {
        $info = array(
          'status' => 'success',
          'message' => 'Summary Send Successfilly!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => 'Failed to send Summary!'
        );
      }
    } else {
      $info = array(
        'status' => 'error',
        'message' => 'Customer Id Not Found!'
      );
    }
    echo json_encode($info);
  }

  public function send_bill_cron()
  {
    $curr_date = date('Y-m-d');
    $sal_date = date('Y-m-d', strtotime($curr_date . '-1 day'));
    $cust_ids = $this->Main_model->get_custid_by_date($sal_date);
    if (!empty($cust_ids)) {
      foreach ($cust_ids as $value) {
        $message = $this->bill_msg($sal_date, $value['customer_id']);
        if (!empty($message)) {
          $this->Api_Model->send_whatsapp_message($value['m_cust_mobile'], $message);
          $delay = rand(2, 6);
          sleep($delay);
        }
      }
    }
  }


  public function send_bill_cron_temp()
  {
    $curr_date = date('Y-m-d');
    $sal_date = date('Y-m-d', strtotime($curr_date . '-1 day'));
    $cust_ids = $this->Main_model->get_custid_by_date($sal_date);
    if (!empty($cust_ids)) {
      foreach ($cust_ids as $value) {
        echo $value['customer_id'];
        echo "<br>";
      }
    }
  }

  public function bill_msg($sal_date, $cust_id)
  {
    $data = $this->Main_model->get_cust_day_summary($cust_id, date('Y-m-d', strtotime($sal_date)));
    $customer_old_balance = $this->Main_model->get_opening_balance($cust_id, date('Y-m-d', strtotime($sal_date . '-1 day')));
    if (!empty($data)) {

      $url = "https://www.ajayfruits.in/Sales/download_pdf?date=" . $sal_date . "&id=" . $cust_id;
      $customer_name = !empty($data->cust_detail->m_cust_hndiname) ? $data->cust_detail->m_cust_hndiname : $data->cust_detail->m_cust_name;
      $message = "*अजय कुशवाहा एंड कंपनी*\n\n*नाम - " . $customer_name . "*\nदिनाक- " . date('d/m/Y', strtotime($sal_date)) . "\nबिल no-" . $data->invoice_no . "\n\n🔹 *विवरण*\n\n";

      $cret10 = $cret20 = $cret25 = 0;
      $oldcrate = $totalcrate = $todaycrate = $balcrate = "";
      if (!empty($data->sale_data)) {
        foreach ($data->sale_data as $key) {
          if ($key->m_item_crate == 20) {
            $cret10 += $key->m_sale_qty;
          } else if ($key->m_item_crate == 13) {
            $cret20 += $key->m_sale_qty;
          } else if ($key->m_item_crate == 14) {
            $cret25 += $key->m_sale_qty;
          }

          $message .= $key->m_item_name . "     " . $key->m_sale_qty . "*" . $key->m_sale_price . "\n";
        }
        $message .= "\nटोटल: " . $data->sub_total . "\nट्रांसपोर्ट: " . $data->total_expense . "\n\n💰 *टोटल:* " . $data->grand_total;
      }
      $message .= "\n💳 *पुराना बाकी:* " . $customer_old_balance['balance_amount'] . "\n📌 *टोटल बाकी:* " . ($customer_old_balance['balance_amount'] + $data->grand_total) . "\n💵 *आज जमा:* " . $data->total_recieve . "\n 💵 *कुल छूट:* " . $data->total_discount . "\n📌 *टोटल बाकी:* " . ($customer_old_balance['balance_amount'] + $data->grand_total - $data->total_recieve - $data->total_discount);

      $balanceFields = [
        '10 KG' => $cret10,
        '20 KG' => $cret20,
        '25 KG' => $cret25
      ];
      foreach ($customer_old_balance['crateitems'] as $cau => $kry) {
        $oldcrate .= $kry['name'] . "- " . $kry['balance'] . ',';
        $totalcrate .= $kry['name'] . "- " . ($kry['balance'] + $balanceFields[$kry['name']]) . ',';
        if ($kry['name'] == $data->crate_data[$cau]->m_itgrp_title) {
          $balcrate .= $kry['name'] . "- " . ($kry['balance'] + $balanceFields[$kry['name']] - $data->crate_data[$cau]->total_qty) . ',';
        }
      }
      foreach ($data->crate_data as $kry) {
        $todaycrate .= $kry->m_itgrp_title . "- " . $kry->total_qty . ',';
      }
      $message .= "\n\n📦 *खाली केरेट विवरण:*\n\n🔹 *पुराना बाकी:* " . $oldcrate . "\n🔹 *टोटल बाकी:* " . $totalcrate . "\n🔹 *आज जमा:* " . $todaycrate . "\n🔹 *टोटल बाकी:* " . $balcrate;
      $message .= "\n\n 🚀 *बिल डाउनलोड करें:* [📥 Download PDF] $url";
      return $message;
    }
  }

  public function generate_pdf($html, $file_name)
  {
    // Load TCPDF library
    $pdf = new Tcpdf_lib();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('AjayFruits');
    $pdf->SetTitle('Day Summary');
    $pdf->SetSubject('TCPDF CodeIgniter');
    $pdf->SetKeywords('TCPDF, PDF, CodeIgniter');
    // Set margins
    $pdf->SetMargins(10, 15, 10);
    // Set auto page breaks
    $pdf->SetAutoPageBreak(true, 10);
    // Add a page
    $pdf->AddPage();
    // Write HTML content to PDF
    $pdf->writeHTML($html, true, false, true, false, '');
    // Save the file in the upload directory
    $pdf->Output($file_name, 'd'); // 'F' saves to a file

  }

  public function update_cust_bal_cron()
  {
    $cust_list = $this->Main_model->get_cust_active_list();

    if (!empty($cust_list)) {
      foreach ($cust_list as $cust) {
        $this->Main_model->update_cust_bal_cron($cust->m_cust_id, date('Y-m-d'), 1);
      }
    }
  }
  public function update_cust_crtbal_cron()
  {
    $cust_list = $this->Main_model->get_cust_active_list();

    if (!empty($cust_list)) {
      foreach ($cust_list as $cust) {
        $this->Main_model->update_cust_bal_cron($cust->m_cust_id, date('Y-m-d'), 2);
      }
    }
  }
  public function update_supplier_balcron()
  {
    $supplier_list = $this->Main_model->get_active_users(2);

    if (!empty($supplier_list)) {
      foreach ($supplier_list as $supp) {
        $supp_balance = $this->Report_model->get_sup_opening_balance($supp->m_user_id, date('Y-m-d'));

        foreach ($supp_balance['crateitems'] as $craty) {
          if ($craty['name'] == '10 KG') {
            $this->db->set('m_user_10bal', $craty['balance'])->where('m_user_id', $supp->m_user_id)->update('master_users_tbl');
          } else if ($craty['name'] == '20 KG') {
            $this->db->set('m_user_20bal', $craty['balance'])->where('m_user_id', $supp->m_user_id)->update('master_users_tbl');
          } else if ($craty['name'] == '25 KG') {
            $this->db->set('m_user_25bal', $craty['balance'])->where('m_user_id', $supp->m_user_id)->update('master_users_tbl');
          }
        }

        $this->db->set('m_user_balance', $supp_balance['balance_amount'])->where('m_user_id', $supp->m_user_id)->update('master_users_tbl');
      }
    }
  }

  //==========================Details===========================//
  protected function login_details()
  {
    $this->require_login();
    $data['login_detail'] = $this->Login_model->user_details();
    return $data;
  }
  //=========================/Details===========================//

  //======================Login Validation======================//
  protected function require_login()
  {
    $is_user_in = $this->session->userdata('is_user_in');
    if (isset($is_user_in) || $is_user_in == true) {
      return;
    } else if ($this->session->userdata('is_cust_in') == true) {
      redirect('Reports/account_ledger');
    } else {
      redirect('Login');
    }
  }

  protected function ajax_login($nav_id = '')
  {
    $is_user_in = $this->session->userdata('is_user_in');
    if (isset($is_user_in) || $is_user_in == true) {
      return true;
    } else {
      echo json_encode(array('status' => 'error', 'message' => 'You are not Logged in Now!! Please login again.'));
      return false;
    }
  }
  //=====================/Login Validation======================//

  //========================/Profile============================//
}
