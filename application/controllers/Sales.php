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
    $data['branch_id'] = $this->input->post('branch_id');
    $data['staff_list'] = $this->Main_model->get_active_user_list(1, $data['branch_id']);
    $data['item_list'] = $this->Master_model->get_all_item('', $data['branch_id']);
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    $data['all_value'] = $this->Main_model->issue_item_group($data['from_date'], $data['to_date'], $data['staff_id'], $data['lot_no'], $data['branch_id']);


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
    $data['branch_id'] = !empty($data['edit_value']) ? $data['edit_value'][0]->si_issue_branch : $this->input->get('branch_id');
    $data['staff_list'] = $this->Main_model->get_active_user_list(1, $data['branch_id']);
    $data['item_list'] = $this->Main_model->get_avilable_item(null, 1, $data['branch_id']);
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    // $data['all_value'] = $this->Main_model->all_custgrp();

    $this->load->view('add_sale_issue', $data);
  }

  public function insert_issue_item()
  {
    if ($this->ajax_login() === false) {
      return;
    }

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
          'message' => $this->Main_model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening send this screen to support.'
        );
      }
      echo json_encode($info);
    }
  }

  public function lotwise_insert_issue()
  {
    if ($this->ajax_login() === false) {
      return;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->lotwise_insert_issue()) {

        $info = array(
          'status' => 'success',
          'message' => 'Issue has been Added successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => $this->Main_model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening send this screen to support.'
        );
      }
      echo json_encode($info);
    }
  }

  public function delete_issue_item()
  {
    if ($this->ajax_login() === false) {
      return;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_issue_item()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => $this->Main_model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening send this screen to support.'
        );
      }
      echo json_encode($info);
    }
  }

  public function delete_issue_item_id()
  {
    if ($this->ajax_login() === false) {
      return;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_issue_item_id()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => $this->Main_model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening send this screen to support.'
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
    $data['branchs_id'] = $this->input->post('branchs_id');
    $data['group_dtl'] = $this->Master_model->get_all_group(1, $data['branchs_id']);
    $data['staff_list'] = $this->Main_model->get_active_user_list(1, $data['branchs_id']);
    $data['item_list'] = $this->Master_model->get_all_item('', $data['branchs_id']);
    $data['custo_list'] = $this->Main_model->get_cust_active_list('', $data['branchs_id']);
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    $data['all_value'] = $this->Main_model->sales_group($data['from_date'], $data['to_date'], null, $data['group_id'], $data['search_in'], null, $data['lot_no'], $data['branchs_id']);

    // Preload every invoice's lines in one query. The view used to call
    // get_edit_sales() per row, which is what made this page time out (BUG-006).
    $spos = array();
    foreach ((array) $data['all_value'] as $row) {
      if (!empty($row->m_sale_spo)) {
        $spos[] = $row->m_sale_spo;
      }
    }
    $data['sale_lines'] = $this->Main_model->get_sales_lines_by_spo($spos, $data['branchs_id']);

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
    if (!empty($data['id'])) {
      $data['edit_value'] = $this->Main_model->get_edit_sales($data['id']);
    }
    $data['branch_id'] = !empty($data['edit_value']) ? $data['edit_value'][0]->m_sale_branch : $this->input->get('branch_id');
    $data['staff_list'] = $this->Main_model->get_active_user_list(1, $data['branch_id']);
    $data['custo_list'] = $this->Main_model->get_cust_active_list('', $data['branch_id']);
    $data['item_list'] = $this->Main_model->get_avilable_item(null, 1, $data['branch_id']);
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    // $data['all_value'] = $this->Main_model->all_custgrp();

    $this->load->view('add_sales', $data);
  }

  public function insert_sales()
  {
    if ($this->ajax_login() === false) {
      return;
    }

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
          $info = $data;
        }
      } else {
        $info = array(
          'status' => 'error',
          'message' => $this->Main_model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening send this screen to support.'
        );
      }
      echo json_encode($info);
    }
  }

  public function lotwise_insert_sales()
  {
    if ($this->ajax_login() === false) {
      return;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->lotwise_insert_sales()) {
        $info = array(
          'status' => 'success',
          'message' => 'Sale has been Added successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => $this->Main_model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening send this screen to support.'
        );
      }
      echo json_encode($info);
    }
  }

  public function delete_sales()
  {
    if ($this->ajax_login() === false) {
      return;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_sales()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => $this->Main_model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening send this screen to support.'
        );
      }
      echo json_encode($info);
    }
  }

  public function delete_sales_id()
  {
    if ($this->ajax_login() === false) {
      return;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_sales_id()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => $this->Main_model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening send this screen to support.'
        );
      }
      echo json_encode($info);
    }
  }

  public function lotwise_sale_print()
  {
    // Was reachable anonymously and rendered sales data straight out (BUG-030).
    $this->require_login();
    // The view titles the document with this.
    $data['pagename'] = "Lotwise Sale Print";
    $data['all_value'] = $this->Report_model->lotwise_sales_list($this->input->get('purid'), $this->input->get('item_id'));
    $this->load->view('lotwise_sale_print', $data);
  }

  public function mulbill_print($sal_date, $group)
  {
    $this->require_login();

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $sal_date)) {
      show_error('Invalid date', 400);
      return;
    }

    $data['sal_date'] = $sal_date;
    $data['data'] = $this->Main_model->get_bill_data($sal_date, $group);
    $data['pgtype'] = 1;
    $this->load->view('bill', $data);
  }

  public function bill_print()
  {
    $this->require_login();

    $data['id'] = $this->input->get('id');
    $data['pgtype'] = 2;
    $data['edit_value'] = $this->Main_model->get_edit_sales($data['id']);
    $data['customer_old_balance'] = $this->Main_model->get_opening_balance($data['edit_value'][0]->m_sale_customer, date('Y-m-d', strtotime(date('Y-m-d') . '-1 day')));
    $data['customer_today_balance'] = $this->Main_model->get_customer_balance($data['edit_value'][0]->m_sale_customer, date('Y-m-d'), 1);
    $this->load->view('bill', $data);
  }

  public function crate_bill_print($id)
  {
    $this->require_login();

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
    $this->require_login();

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
    $user_type = $this->session->userdata('user_type');

    // Sirf 8 aur 9 ko hi is page ka access do, baaki sabko block
    if (!in_array($user_type, [8, 9])) {
      show_error('Access Denied', 403);
      return;
    }

    $data['pagename'] = "All Purchase list";
    $curdate = date('Y-m-d');
    if ($user_type == 9) {
      // user_type 9 -> apni hi branch ka data, dropdown se koi override nahi
      $data['branch_id'] = $this->session->userdata('user_id');
      $data['branch_locked'] = true; // view me dropdown disable karne ke liye
      // The Type dropdown is superadmin-only, so a branch user has no way to
      // switch it - leave it unfiltered rather than undefined. A branch needs
      // to see transfers received as well as its own purchases, and reading
      // the unset key below warned on every load of this page.
      $data['type_pur'] = null;
    } else {
      // user_type 8 -> sab branch dekh sakta hai, dropdown se filter
      $data['branch_id'] = $this->input->post('branch_id');
      $data['branch_locked'] = false;
      // '' is the Type dropdown's "All" option and a real choice, so test for a
      // posted value rather than using ?: - which turned All back into Purchase
      // and left the option impossible to select. A first load posts nothing at
      // all, and still defaults to Purchase.
      $posted_type = $this->input->post('type_pur');
      $data['type_pur'] = ($posted_type === null) ? 1 : $posted_type;
    }
    $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d');
    $data['to_date'] = $this->input->post('to_date') ?: $curdate;
    $data['suppiler_id'] = $this->input->post('suppiler_id');
    $data['suplier_list'] = $this->Main_model->get_active_user_list(2, $data['branch_id']);
    $data['item_list'] = $this->Master_model->get_all_item('', $data['branch_id']);
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    $data['all_value'] = $this->Main_model->purchase_group($data['from_date'], $data['to_date'], $data['suppiler_id'], null, $data['branch_id'], $data['type_pur']);

    $this->load->view('purchase_list', $data);
  }

  // New: show purchase items (not grouped)
  public function purchase_item_list()
  {
    $data = $this->login_details();

    $data['pagename'] = "Stock in & out Report";
    $curdate = date('Y-m-d');
    $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d');
    $data['to_date'] = $this->input->post('to_date') ?: $curdate;
    $data['suppiler_id'] = $this->input->post('suppiler_id');
    $data['search_in'] = $this->input->post('search_in');
    $data['branch_id'] = $this->input->post('branch_id');
    $data['suplier_list'] = $this->Main_model->get_active_user_list(2, $data['branch_id']);
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    $data['all_value'] = $this->Main_model->get_purchase_items($data['from_date'], $data['to_date'], $data['suppiler_id'], $data['search_in'], $data['branch_id']);



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

    $res = $this->db->where('m_sale_id', $id)->update('master_sales_tbl', ['m_sale_lot' => $lot, 'm_sale_updatedon' => date('Y-m-d H:i:s'), 'm_sale_updatedby' => $this->session->userdata('user_id') ?: 0]);

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
    if ($this->session->userdata('user_type') != 8) {
      show_error('Access Denied. Only Super Admin can add/edit purchases.', 403);
      return;
    }
    $data['pagename'] = "Add Purchase";
    $data['id'] = $this->input->get('id');
    $data['edit_value'] = $this->Main_model->get_edit_purchase($data['id']);
    $data['inter_expense'] = $this->Main_model->get_purchase_expense($data['id']);
    // print_r($data['inter_expense']); die ;
    $data['branch_id'] = !empty($data['edit_value']) ? $data['edit_value'][0]->m_purcs_branch : $this->input->get('branch_id');
    $data['suplier_list'] = $this->Main_model->get_active_user_list(2, $data['branch_id']);
    $data['item_list'] = $this->Master_model->get_all_item('', $data['branch_id']);
    $data['expense_lst'] = $this->Master_model->get_all_active_group(2, $data['branch_id']);
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    // $data['all_value'] = $this->Main_model->all_custgrp();


    $this->load->view('add_purchase', $data);
  }

  public function insert_purchase()
  {
    if ($this->session->userdata('user_type') != 8) {
      echo json_encode(['status' => 'error', 'message' => 'Access Denied']);
      return;
    }
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
          'message' => $this->Main_model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening send this screen to support.'
        );
      }
      echo json_encode($info);
    }
  }

  public function delete_purchase()
  {
    if ($this->session->userdata('user_type') != 8) {
      echo json_encode(['status' => 'error', 'message' => 'Access Denied']);
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_purchase()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => $this->Main_model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening send this screen to support.'
        );
      }
      echo json_encode($info);
    }
  }

  public function delete_purchase_id()
  {
    if ($this->session->userdata('user_type') != 8) {
      echo json_encode(['status' => 'error', 'message' => 'Access Denied']);
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_purchase_id()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => $this->Main_model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening send this screen to support.'
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
    $data['branch_id'] = $this->input->post('branch_id');
    $data['group_dtl'] = $this->Master_model->get_all_group(1, $data['branch_id']);
    $data['paymode_lst'] = $this->Master_model->get_payment_methods($data['branch_id']);
    $data['user_list'] = $this->Main_model->get_active_user_list(1, $data['branch_id']);
    $data['general_list'] = $this->Main_model->get_active_user_list(4, $data['branch_id']);
    $data['investment_list'] = $this->Main_model->get_active_user_list(5, $data['branch_id']);
    $data['custo_list'] = $this->Main_model->get_cust_active_list('', $data['branch_id']);
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    $data['all_value'] = $this->Main_model->get_received_list($type, $data['from_date'], $data['to_date'], null, $data['recvd_account'], $data['recvd_method'], $data['group_id'], $data['search_in'], null, $data['branch_id']);
    $data['crate_lst'] = $this->Master_model->all_active_itemgroup(3, $data['branch_id']);

    $this->load->view('payment_recived_list', $data);
  }

  public function insert_recieved_data()
  {
    if ($this->ajax_login() === false) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($this->Main_model->insert_recieved_data()) {
        $this->session->set_flashdata('form_success', 'Receipt saved successfully.');
      } else {
        $this->session->set_flashdata('form_error', $this->Main_model->last_error()
          ?: 'The Receipt could not be saved. Please check the entry and try again.');
      }
    }
    $this->_redirect_back();
  }

  public function update_recieved_data()
  {
    if ($this->ajax_login() === false) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($this->Main_model->update_recieved_data()) {
        $this->session->set_flashdata('form_success', 'Receipt updated successfully.');
      } else {
        $this->session->set_flashdata('form_error', $this->Main_model->last_error()
          ?: 'The Receipt could not be updated. Please check the entry and try again.');
      }
    }
    $this->_redirect_back();
  }

  public function delete_recieved_data()
  {
    if ($this->ajax_login() === false) {
      return;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_recieved_data()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => $this->Main_model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening send this screen to support.'
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
    $data['branch_id'] = $this->input->post('branch_id');
    $data['supplier_list'] = $this->Main_model->get_active_user_list(2, $data['branch_id']);
    $data['loader_list'] = $this->Main_model->get_active_user_list(3, $data['branch_id']);
    $data['staff_list'] = $this->Main_model->get_active_user_list(1, $data['branch_id']);
    $data['general_list'] = $this->Main_model->get_active_user_list(4, $data['branch_id']);
    $data['investment_list'] = $this->Main_model->get_active_user_list(5, $data['branch_id']);
    $data['expense_lst'] = $this->Master_model->get_all_active_group(2, $data['branch_id']);
    $data['paymode_lst'] = $this->Master_model->get_payment_methods($data['branch_id']);
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    $data['all_value'] = $this->Main_model->get_payment_list($type, $data['from_date'], $data['to_date'], null, $data['payment_account'], $data['payment_method'], $data['search_in'], null, $data['branch_id']);
    $data['crate_lst'] = $this->Master_model->all_active_itemgroup(3, $data['branch_id']);
    // echo '<pre>';print_r($data['all_value']); die ;
    $this->load->view('payment_paid_list', $data);
  }

  public function insert_payment_data()
  {
    if ($this->ajax_login() === false) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($this->Main_model->insert_payment_data()) {
        $this->session->set_flashdata('form_success', 'Payment saved successfully.');
      } else {
        $this->session->set_flashdata('form_error', $this->Main_model->last_error()
          ?: 'The Payment could not be saved. Please check the entry and try again.');
      }
    }
    $this->_redirect_back();
  }

  public function update_payment_data()
  {
    if ($this->ajax_login() === false) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($this->Main_model->update_payment_data()) {
        $this->session->set_flashdata('form_success', 'Payment updated successfully.');
      } else {
        $this->session->set_flashdata('form_error', $this->Main_model->last_error()
          ?: 'The Payment could not be updated. Please check the entry and try again.');
      }
    }
    $this->_redirect_back();
  }

  public function delete_payment_data()
  {
    if ($this->ajax_login() === false) {
      return;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_payment_data()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => $this->Main_model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening send this screen to support.'
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
    $data['branch_id'] = $this->input->post('branch_id');
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    $data['all_value'] = $this->Main_model->get_voucher_list($data['type'], $data['from_date'], $data['to_date'], null, $data['search_in'], null, $data['branch_id']);
    // echo '<pre>';print_r($data['all_value']); die ;
    $this->load->view('voucher_list', $data);
  }

  public function insert_voucher_data()
  {
    if ($this->ajax_login() === false) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($this->Main_model->insert_voucher_data()) {
        $this->session->set_flashdata('form_success', 'Voucher saved successfully.');
      } else {
        $this->session->set_flashdata('form_error', $this->Main_model->last_error()
          ?: 'The Voucher could not be saved. Please check the entry and try again.');
      }
    }
    $this->_redirect_back();
  }

  public function update_voucher_data()
  {
    if ($this->ajax_login() === false) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($this->Main_model->update_voucher_data()) {
        $this->session->set_flashdata('form_success', 'Voucher updated successfully.');
      } else {
        $this->session->set_flashdata('form_error', $this->Main_model->last_error()
          ?: 'The Voucher could not be updated. Please check the entry and try again.');
      }
    }
    $this->_redirect_back();
  }

  public function delete_voucher_data()
  {
    if ($this->ajax_login() === false) {
      return;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_voucher_data()) {
        $info = array(
          'status' => 'success',
          'message' => 'Data has been Deleted successfully!'
        );
      } else {
        $info = array(
          'status' => 'error',
          'message' => $this->Main_model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening send this screen to support.'
        );
      }
      echo json_encode($info);
    }
  }

  public function get_vourcher_accounts()
  {
    // Was reachable anonymously and dumped the full customer / supplier /
    // staff / expense lists as JSON (BUG-030).
    if ($this->ajax_login() === false) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $acc_type = $this->input->post('acct_type');
      $branch_id = $this->input->post('branch_id');
      switch ($acc_type) {
        case 1:
          $data['list'] = $this->Main_model->get_cust_active_list('', $branch_id);
          $data['listtype'] = 1;
          $data['listname'] = "Customer";
          break;
        case 2:
          $data['list'] = $this->Main_model->get_active_user_list(2, $branch_id);
          $data['listtype'] = 2;
          $data['listname'] = "Supplier";
          break;
        case 3:
          $data['list'] = $this->Master_model->get_all_active_group(2, $branch_id);
          $data['listtype'] = 3;
          $data['listname'] = "Expense";
          break;
        case 4:
          $data['list'] = $this->Main_model->get_active_user_list(3, $branch_id);
          $data['listtype'] = 2;
          $data['listname'] = "Loader";
          break;
        case 5:
          $data['list'] = $this->Main_model->get_active_user_list(1, $branch_id);
          $data['listtype'] = 2;
          $data['listname'] = "Staff";
          break;
        case 6:
          $data['list'] = $this->Main_model->get_active_user_list(4, $branch_id);
          $data['listtype'] = 2;
          $data['listname'] = "General";
          break;
        case 7:
          $data['list'] = $this->Main_model->get_active_user_list(5, $branch_id);
          $data['listtype'] = 2;
          $data['listname'] = "Investment";
          break;
        case 8:
          // Head Office raising a voucher against a branch. Not branch-scoped:
          // branch accounts all live at Head Office (m_user_branch = 0) - which
          // is also why this needs its own check, or any logged-in branch could
          // ask for the full branch list.
          if ($this->session->userdata('user_type') != 8) {
            $data['list'] = array();
            $data['listtype'] = 2;
            $data['listname'] = "Branch";
            break;
          }
          $data['list'] = $this->Main_model->get_user_list(9);
          $data['listtype'] = 2;
          $data['listname'] = "Branch";
          break;
      }

      // CSRF regenerates its token on every accepted POST (see
      // window.refreshCsrfToken in footer.php). This endpoint can be called
      // repeatedly from the same page load (account type or branch changed
      // more than once) with no reload in between, so the client needs the
      // fresh token back or its *next* call gets rejected as a mismatch.
      $data['csrf_token_name'] = $this->security->get_csrf_token_name();
      $data['csrf_token_value'] = $this->security->get_csrf_hash();

      echo json_encode($data);
    }
  }

  public function get_reciept_accounts()
  {
    // Was reachable anonymously and dumped the full customer / supplier /
    // staff / bank lists as JSON (BUG-030).
    if ($this->ajax_login() === false) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $acc_type = $this->input->post('acct_type');
      $branch_id = $this->input->post('branch_id');
      switch ($acc_type) {
        case 1:
          $data['list'] = $this->Main_model->get_cust_active_list('', $branch_id);
          $data['listtype'] = 1;
          $data['listname'] = "Customer";
          break;
        case 4:
          $data['list'] = $this->Main_model->get_active_user_list(2, $branch_id);
          $data['listtype'] = 2;
          $data['listname'] = "Supplier";
          break;
        case 5:
          $data['list'] = $this->Master_model->get_all_active_group(2, $branch_id);
          $data['listtype'] = 3;
          $data['listname'] = "Expense";
          break;
        case 6:
          $data['list'] = $this->Main_model->get_active_user_list(3, $branch_id);
          $data['listtype'] = 2;
          $data['listname'] = "Loader";
          break;
        case 7:
          $data['list'] = $this->Master_model->get_all_active_group(3, $branch_id);
          $data['listtype'] = 3;
          $data['listname'] = "Bank";
          break;
        case 2:
          $data['list'] = $this->Main_model->get_active_user_list(4, $branch_id);
          $data['listtype'] = 2;
          $data['listname'] = "General";
          break;
        case 3:
          $data['list'] = $this->Main_model->get_active_user_list(5, $branch_id);
          $data['listtype'] = 2;
          $data['listname'] = "Investment";
          break;
        case 8:
          $data['list'] = $this->Main_model->get_active_user_list(9);
          $data['listtype'] = 2;
          $data['listname'] = "Branch";
          break;
      }

      // See the matching note in get_vourcher_accounts() above.
      $data['csrf_token_name'] = $this->security->get_csrf_token_name();
      $data['csrf_token_value'] = $this->security->get_csrf_hash();

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
    // The pgtype 2 branch of the view has its own Group filter, same as pgtype 1.
    $data['group_id'] = $this->input->post('group_id') ?: 'o';
    $data['group_dtl'] = $this->Master_model->get_all_group(1);
    $data['cust_list'] = $this->Main_model->get_cust_active_list();
    $this->load->view('reminder_list', $data);
  }
  public function send_individual_statement()
  {
    $data = $this->login_details();
    $data['pagename'] = "Send Customer Statement";
    $data['pgtype'] = 3;
    // The pgtype 3 branch of the view has its own Group filter, same as pgtype 1.
    $data['group_id'] = $this->input->post('group_id') ?: 'o';
    $data['group_dtl'] = $this->Master_model->get_all_group(1);
    $data['cust_list'] = $this->Main_model->get_cust_active_list();
    $this->load->view('reminder_list', $data);
  }

  public function download_pdf()
  {
    // Was reachable anonymously - any customer id in the query string returned
    // that customer's day summary and balance as a PDF (BUG-030).
    $this->require_login();
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

  public function download_customer_statement()
  {
    // Reachable two ways: by a logged-in staff member, or by a customer
    // following a signed link we sent them over WhatsApp. Anything else is
    // refused - account_name alone must never be enough (BUG-016).
    if ($this->session->userdata('is_user_in') !== true) {
      $this->_require_valid_statement_link($this->input->get('account_name'));
    }

    $data['pagename'] = "Customer Cash Ledger";
    $data['exporttype'] = 2;
    $data['pagelink'] = "export_customer_cash_ledger";

    $data['account_name'] = $this->input->get('account_name');
    $cust_dtl = $this->Main_model->get_cust_dtl($data['account_name']);

    $data['from_date'] = $this->input->get('from_date') ?: row_val($cust_dtl, 'm_cust_added_on', date('Y-m-d'));
    $data['todate'] = $this->input->get('to_date') ?: date('Y-m-d');
    $data['subhead'] = '<div class="col-6">
                                <h4 class="m-0"><strong>' . row_val($cust_dtl, 'm_cust_name') . '</strong></h4>
                                <h4>' . row_val($cust_dtl, 'm_city_name') . '</h4>
                            </div>
                            <div class="col-6 text-end">
                                <h4 class="m-0"></h4>
                                <h4 class="fw-bold"> ' . date('d/m/Y', strtotime($data['from_date'])) . ' TO ' . date('d/m/Y', strtotime($data['todate'])) . '</h4>
                            </div>';

    $data['tableheader'] = '<tr><th scope="col">Sno</th>
                                    <th scope="col">DATE</th>
                                    <th scope="col">PARTICULARE</th>
                                    <th scope="col">DEBIT</th>
                                    <th scope="col">CREDIT</th>
                                    <th scope="col">BALANCE</th>
                                </tr>';

    $all_value = $this->Report_model->customer_detailed_leger($data['from_date'], $data['todate'], $data['account_name']);
    $opening_balance = $this->Main_model->get_opening_balance($data['account_name'], date('Y-m-d', strtotime($data['from_date'] . '-1day')));
    $closing_balance = $this->Main_model->get_opening_balance($data['account_name'], $data['todate']);
    $balance = $opening_balance['balance_amount'];

    if ($balance > 0) {
      $total_debit = $balance;
      $total_credit = 0;
    } else {
      $total_debit = 0;
      $total_credit = $balance;
    }


    $tbody = '<tr>
        <th scope="row"></th>
        <th scope="row">' . date('d/m/Y', strtotime($data['from_date'])) . '</th>
        <th> Opening Balance </th>
        <th>' . $total_debit . '</th>
        <th>' . abs($total_credit) . '</th>
        <th>' . $balance . '</th>
        </tr>';
    if (!empty($all_value)) {
      foreach ($all_value as $contt => $key) {
        $tbody .= '<tr>
                            <th scope="row">' . ($contt + 1) . '</th>
                            <th scope="row">' . date('d/m/Y', strtotime($key['date'])) . '</th>';
        if ($key['type'] == 1) {
          $total_credit += $key['debited'];
          $balance -= $key['debited'];
          $tbody .= '<td class="">
                                    <div class="d-flex bd-highlight">
                                        <div class="flex-grow-1 bd-highlight">
                                        Recipt No. ' . $key['recipt_no'] . '
                                        </div>
                                        <div class="bd-highlight">' . $key['expense'] . '</div>
                                    </div>
                                    <p class="m-0">Remark :-' . $key['note'] . '</p>
                                </td>
                                <td></td>
                                <td>' . $key['debited'] . '</td>
                                <td>' . $balance . '</td>';
        } else if ($key['type'] == 2) {
          $text = '';
          foreach ($key['particular'] as $ket) {
            $text .= $ket->m_crate_name . ' : ' . $ket->m_recvd_qty . ', ';
          }
          $tbody .= '<td class="">
                                    <div class="d-flex bd-highlight">
                                        <div class="flex-grow-1 bd-highlight">
                                            <p class="m-0">Create Receive No. ' . $key['recipt_no'] . '</p>
                                            <p class="m-0">Remark :-' . $key['note'] . '</p>
                                        </div>
                                        <div class="bd-highlight">
                                            <p class="m-0 text-end">Qty = ' . $key['total_qty'] . '</p>
                                            <p class="m-0 text-end">' . $text . '</p>
                                        </div>
                                    </div>
                                </td>
                                <td>' . $key['debited'] . '</td>
                                <td></td> 
                                <td>' . $balance . '</td>';
        } else  if ($key['type'] == 4) {
          if ($key['expense'] == 1) {
            $total_credit += $key['debited'];
            $balance -= $key['debited'];
            $amtdb = '';
            $amtcdt = $key['debited'];
          } else {
            $total_debit += $key['debited'];
            $balance += $key['debited'];
            $amtdb = $key['debited'];
            $amtcdt = '';
          }

          $tbody .= '<td class="">
                                    <div class="d-flex bd-highlight">
                                        <div class="flex-grow-1 bd-highlight">
                                        Voucher No. ' . $key['recipt_no'] . '
                                        </div>
                                        <div class="bd-highlight">' . $key['note'] . '</div>
                                    </div>
                                </td>
                                <td>' . $amtdb . '</td>
                                <td>' .  $amtcdt . '</td>
                                <td>' . $balance . '</td>';
        } else {

          $total_debit += $key['debited'];
          $balance += $key['debited'];
          $tbody .= ' <td class="">
                                    <div class="d-flex bd-highlight">
                                        <div class="flex-grow-1 bd-highlight">
                                            <p class="m-0">Credit Sale No. ' . $key['recipt_no'] . '</p>';
          foreach ($key['particular'] as $ket) {
            $tbody .= '<p class="m-0">' . $ket->m_item_name . '</p>';
          }

          $tbody .= '</div>
                                    <div class="bd-highlight">
                                        <p class="m-0 text-end">Qty:' . $key['total_qty'] . ', Expense = ₹' . $key['expense'] . '</p>';
          foreach ($key['particular'] as $ket) {
            $tbody .= ' <p class="m-0 text-end">' . $ket->m_sale_qty . ' ' . $ket->unitname . ' @ ' . $ket->m_sale_price . ' = ' . $ket->m_sale_total . '</p>';
          }

          $tbody .= '</div>
                                    </div>
                                    <p class="m-0">Note :-' . $key['note'] . '</p>
                                </td>
                                <td>' . $key['debited'] . '</td>
                                <td></td> 
                                <td>' . $balance . '</td>';
        }
        $tbody .= '</tr>';
      }
    }

    $tbody .= ' <tr>
        <th colspan="3"></th>
        <th>' . $total_debit . '</th>
        <th>' . $total_credit . '</th>
        <th></th>
        </tr>';

    $data['Mainarray'] =  $tbody;

    $crtabi = '';
    if (count($closing_balance['crateitems']) > 0) {
      foreach ($closing_balance['crateitems'] as $kryr) {
        $crtabi .=  $kryr['name'] . ' : ' . $kryr['balance'] . ',';
      }
    }

    $data['mainfooter'] = '<div class="">
        <table class="table border-0 m-0">
            <tbody>
                <tr>
                    <td colspan="2" class="border-0">
                        <p class="m-0">Balance Create : ' . $closing_balance['balance_crate'] . '</p>
                        <p class="m-0">' . $crtabi . '</p>
                    </td>
                    <td colspan="2">
                        <div class="text-end">
                            <h4><strong>CLOSING BALANCE: ' .  $closing_balance['balance_amount'] . '</strong></h4>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        </div>';

    $data['cust_dtl'] = $cust_dtl;
    $data['closing_balance'] = $closing_balance;
    $data['crtabi'] = $crtabi;

    $html = $this->load->view('customer_statement_pdf', $data, true);
    $file_name = 'Statement_' . $data['account_name'] . date('dmy') . '.pdf';
    $this->generate_pdf($html, $file_name);
  }

  public function send_statement()
  {
    $from_date = $this->input->post('from_date') ?: date('Y-m-d');
    $to_date = $this->input->post('to_date') ?: date('Y-m-d');
    $cust_ids = $this->input->post('cust_id');
    if (!empty($from_date) and !empty($to_date) and !empty($cust_ids)) {
      foreach ($cust_ids as $cust_id) {
        $cust_bal = $this->Main_model->get_opening_balance($cust_id, $to_date);
        $url = $this->_signed_statement_url($cust_id, $from_date, $to_date);
        $customer_name = !empty($cust_bal['m_cust_hndiname']) ? $cust_bal['m_cust_hndiname'] : $cust_bal['cust_name'];
        $oldcrate = "";
        foreach ($cust_bal['crateitems'] as $cau => $kry) {
          $oldcrate .= $kry['name'] . "- " . $kry['balance'] . ',';
        }

        $message = "📄 *खाता स्टेटमेंट विवरण*\n\n" . "*नाम - " . $customer_name . "*\n\n" . "🔹 *स्टेटमेंट अवधि*\n" . "From: " . $from_date . "\n" . "To: " . $to_date . "\n\n" . "📊 *खाते का सारांश*\n" . "कुल बैलेंस:* " . $cust_bal['balance_amount'] . "\n\n" . "📦 *केरेट स्टेटमेंट*\n🔹 *टोटल बाकी केरेट:* " . $oldcrate . "\n\n" . "🙏🏻 *अजय कुशवाहा एंड कंपनी*\n\n" . "🚀 *पूरा स्टेटमेंट डाउनलोड करें:* [📥 Download PDF] " . $url;

        if (!empty($message)) {
          $cust_dtl = $this->Main_model->get_cust_active_list($cust_id);
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

  public function send_bill()
  {
    $sal_date = $this->input->post('to_date') ?: date('Y-m-d');
    $cust_ids = $this->input->post('cust_id');
    if (!empty($sal_date) and !empty($cust_ids)) {
      foreach ($cust_ids as $cust_id) {
        // $cust_dtl = $this->Main_model->get_cust_active_list($cust_id);
        $response = $this->send_bill_whatsapp($sal_date, $cust_id);
        // if (!empty($message)) {
        //  $response = $this->Api_Model->send_whatsapp_message($cust_dtl[0]->m_cust_mobile, $message);
        // } 
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
    if ($this->_require_cron_auth() === false) {
      return;
    }
    $curr_date = date('Y-m-d');
    $sal_date = date('Y-m-d', strtotime($curr_date . '-1 day'));
    $cust_ids = $this->Main_model->get_custid_by_date($sal_date);
    if (!empty($cust_ids)) {
      foreach ($cust_ids as $value) {
        // $message = $this->bill_msg($sal_date, $value['customer_id']);
        // if (!empty($message)) {
        // $this->Api_Model->send_whatsapp_message($value['m_cust_mobile'], $message);
        // $delay = rand(2,5);
        // sleep($delay);
        // }
        $response = $this->send_bill_whatsapp($sal_date, $value['customer_id']);
      }
    }
  }

  public function send_bill_cron_temp()
  {
    if ($this->_require_cron_auth() === false) {
      return;
    }
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

  public function send_bill_whatsapp($sal_date, $cust_id)
  {
    $data = $this->Main_model->get_cust_day_summary($cust_id, date('Y-m-d', strtotime($sal_date)));
    $customer_old_balance = $this->Main_model->get_opening_balance($cust_id, date('Y-m-d', strtotime($sal_date . ' -1 day')));

    if (empty($data)) return false;

    // 🔗 URL variables
    $url_date = date('Y-m-d', strtotime($sal_date));
    $url_id   = $cust_id;

    // 👤 Customer Name & Mobile
    $customer_name = !empty($data->cust_detail->m_cust_hndiname)
      ? $data->cust_detail->m_cust_hndiname
      : $data->cust_detail->m_cust_name;

    // Ensure mobile has country code (India = 91)
    $raw_mobile      = preg_replace('/\D/', '', $data->cust_detail->m_cust_mobile);
    $customer_mobile = (strlen($raw_mobile) == 10) ? '91' . $raw_mobile : $raw_mobile;

    // 📦 Item Details
    $item_details = "";
    $cret10 = $cret20 = $cret25 = 0;

    if (!empty($data->sale_data)) {
      foreach ($data->sale_data as $key) {
        if ($key->m_item_crate == 20)     $cret10 += $key->m_sale_qty;
        elseif ($key->m_item_crate == 13) $cret20 += $key->m_sale_qty;
        elseif ($key->m_item_crate == 14) $cret25 += $key->m_sale_qty;

        $item_details .= $key->m_item_name . " " . $key->m_sale_qty . "*" . $key->m_sale_price . "  ";
      }
    }
    $item_details = trim($item_details);

    // 💰 Calculations
    $old_balance = $customer_old_balance['balance_amount'] ?? 0;
    $grand_total = $data->grand_total ?? 0;
    $today_paid  = $data->total_recieve ?? 0;
    $discount    = $data->total_discount ?? 0;

    $total_balance = $old_balance + $grand_total;
    $final_balance = $total_balance - $today_paid - $discount;

    // 📦 Crate Calculations
    $balanceFields = [
      '10 KG' => $cret10,
      '20 KG' => $cret20,
      '25 KG' => $cret25
    ];

    $oldcrate_parts   = [];
    $totalcrate_parts = [];
    $balcrate_parts   = [];
    $todaycrate_parts = [];

    $crate_data_map = [];
    if (!empty($data->crate_data)) {
      foreach ($data->crate_data as $kry) {
        $crate_data_map[$kry->m_itgrp_title] = $kry->total_qty;
      }
    }

    if (!empty($customer_old_balance['crateitems'])) {
      foreach ($customer_old_balance['crateitems'] as $kry) {
        $name     = $kry['name'];
        $balance  = $kry['balance'];
        $today    = $balanceFields[$name] ?? 0;
        $returned = $crate_data_map[$name] ?? 0;

        $oldcrate_parts[]   = $name . "- " . $balance;
        $totalcrate_parts[] = $name . "- " . ($balance + $today);
        $balcrate_parts[]   = $name . "- " . ($balance + $today - $returned);
      }
    }

    if (!empty($data->crate_data)) {
      foreach ($data->crate_data as $kry) {
        $todaycrate_parts[] = $kry->m_itgrp_title . "- " . $kry->total_qty;
      }
    }

    $oldcrate   = implode(', ', $oldcrate_parts);
    $totalcrate = implode(', ', $totalcrate_parts);
    $todaycrate = implode(', ', $todaycrate_parts);
    $balcrate   = implode(', ', $balcrate_parts);

    // 📲 WhatsApp Template Payload — corrected format
    $phone_number_id = $this->_whatsapp_credential('whatsapp_phone_number_id');
    $apikey          = $this->_whatsapp_credential('whatsapp_apikey');

    $payload = [
      "messaging_product" => "whatsapp",
      "recipient_type"    => "individual",
      "to"                => $customer_mobile,
      "type"              => "template",
      "template"          => [
        "name"       => "daily_customer_summary",
        "language"   => ["code" => "en"],
        "components" => [
          [
            "type"       => "body",
            "parameters" => [
              ["type" => "text", "text" => $this->safe($customer_name)],
              ["type" => "text", "text" => $this->safe(date('d/m/Y', strtotime($sal_date)))],
              ["type" => "text", "text" => $this->safe($data->invoice_no)],
              ["type" => "text", "text" => $this->safe($item_details)],
              ["type" => "text", "text" => $this->safe($data->sub_total)],
              ["type" => "text", "text" => $this->safe($data->total_expense)],
              ["type" => "text", "text" => $this->safe($grand_total)],
              ["type" => "text", "text" => $this->safe($old_balance)],
              ["type" => "text", "text" => $this->safe($total_balance)],
              ["type" => "text", "text" => $this->safe($today_paid)],
              ["type" => "text", "text" => $this->safe($discount)],
              ["type" => "text", "text" => $this->safe($final_balance)],
              ["type" => "text", "text" => $this->safe($oldcrate)],
              ["type" => "text", "text" => $this->safe($totalcrate)],
              ["type" => "text", "text" => $this->safe($todaycrate)],
              ["type" => "text", "text" => $this->safe($balcrate)],

            ]
          ],
          [
            "type"       => "button",
            "sub_type"   => "url",
            "index"      => "0",
            "parameters" => [
              ["type" => "text", "text" => "?date=" . $this->safe($url_date) . "&id=" . $this->safe($url_id)]  // 1 param only
            ]
          ]
        ]
      ]
    ];

    log_message('error', 'WhatsApp Payload: ' . json_encode($payload));

    return $this->send_api_request($payload, $phone_number_id, $apikey);  // ← Pass id & key
  }

  public function send_reminder_msg()
  {
    $sal_date = date('Y-m-d');
    $cust_ids = $this->input->post('cust_ids');

    if (empty($cust_ids)) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Customer Id Not Found!'
      ]);
      return;
    }

    $final_response = true;

    foreach ($cust_ids as $cust_id) {

      $cust_bal = $this->Main_model->get_opening_balance($cust_id, $sal_date);
      $last_date = $this->Main_model->get_last_saledate($cust_id);

      if (empty($cust_bal)) continue;

      // 👤 Name
      $customer_name = !empty($cust_bal['m_cust_hndiname'])
        ? $cust_bal['m_cust_hndiname']
        : $cust_bal['cust_name'];

      // 📅 Dates
      $lastsale = !empty($last_date->last_sale_date)
        ? date('d/m/Y', strtotime($last_date->last_sale_date))
        : '-';

      $lastrvd = !empty($last_date->last_recvd_date)
        ? date('d/m/Y', strtotime($last_date->last_recvd_date))
        : '-';

      // 📦 Crate
      $oldcrate = "";
      foreach ($cust_bal['crateitems'] as $kry) {
        $oldcrate .= $kry['name'] . "- " . $kry['balance'] . ',';
      }
      $oldcrate = rtrim($oldcrate, ',');

      $phone_number_id = $this->_whatsapp_credential('whatsapp_phone_number_id');
      $apikey          = $this->_whatsapp_credential('whatsapp_apikey');

      $payload = [
        "messaging_product" => "whatsapp",
        "recipient_type"    => "individual",
        "to"                => "91" . $cust_bal['cust_mobile'],
        "type"              => "template",
        "template"          => [
          "name"       => "reminder_msg",
          "language"   => ["code" => "hi"],
          "components" => [
            [
              "type"       => "body",
              "parameters" => [
                ["type" => "text", "text" => $this->safe($customer_name)], //1
                ["type" => "text", "text" => $this->safe($lastsale)], //2
                ["type" => "text", "text" => $this->safe($lastrvd)], //3
                ["type" => "text", "text" => $this->safe($cust_bal['balance_amount'])], //4
                ["type" => "text", "text" => $this->safe($oldcrate)], //5

              ]
            ],
            [
              "type"       => "button",
              "sub_type"   => "url",
              "index"      => "0",
              "parameters" => [
                ["type" => "text", "text" => "?date=" . $this->safe($sal_date) . "&id=" . $this->safe($cust_id)]  // 1 param only
              ]
            ]
          ]
        ]
      ];

      log_message('error', 'WhatsApp Payload: ' . json_encode($payload));

      $response = $this->send_api_request($payload, $phone_number_id, $apikey);

      if (!$response) $final_response = false;
    }

    echo json_encode([
      'status' => $final_response ? 'success' : 'error',
      'message' => $final_response ? 'Reminder Sent Successfully!' : 'Failed to send some messages!'
    ]);
  }

  private function send_api_request($data, $phone_number_id, $apikey)
  {
    $url = "https://partnersv1.pinbot.ai/v3/" . $phone_number_id . "/messages";

    $headers = [
      "Content-Type: application/json",
      "apikey: " . $apikey
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response   = curl_exec($ch);
    $http_code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    log_message('error', 'WhatsApp HTTP Code: ' . $http_code);
    log_message('error', 'WhatsApp Response: ' . $response);

    if ($curl_error) return false;

    return $response;
  }

  // public function test_whatsapp()
  // {
  //   $phone_number_id = $this->_whatsapp_credential('whatsapp_phone_number_id');
  //   $apikey          = $this->_whatsapp_credential('whatsapp_apikey');

  //   $cust_list = $this->Main_model->get_cust_active_list();
  //   foreach ($cust_list as $cust_id) {
  //     $payload = [
  //       "messaging_product" => "whatsapp",
  //       "recipient_type"    => "individual",
  //       "to"                => "91" . $cust_id->m_cust_mobile, // सही नंबर डालो
  //       "type"              => "template",
  //       "template"          => [
  //         "name"       => "information",
  //         "language"   => ["code" => "hi"],
  //         "components" => [
  //           [
  //             "type"       => "body",       // ← Fixed: added required body component
  //             "parameters" => []
  //           ]
  //         ]
  //       ]
  //     ];

  //     $response = $this->send_api_request($payload, $phone_number_id, $apikey);
  //     echo "<pre>";
  //     echo $response;
  //     echo "</pre>";
  //   }
  //   die;
  // }

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
    if ($this->_require_cron_auth() === false) {
      return;
    }
    $cust_list = $this->Main_model->get_cust_active_list();

    if (!empty($cust_list)) {
      foreach ($cust_list as $cust) {
        $this->Main_model->update_cust_bal_cron($cust->m_cust_id, date('Y-m-d'), 1);
      }
    }
  }

  public function update_cust_crtbal_cron()
  {
    if ($this->_require_cron_auth() === false) {
      return;
    }
    $cust_list = $this->Main_model->get_cust_active_list();

    if (!empty($cust_list)) {
      foreach ($cust_list as $cust) {
        $this->Main_model->update_cust_bal_cron($cust->m_cust_id, date('Y-m-d'), 2);
      }
    }
  }

  public function update_supplier_balcron()
  {
    if ($this->_require_cron_auth() === false) {
      return;
    }
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

  public function safe($val)
  {
    return (!empty($val)) ? (string)$val : "-";
  }

  //==========================Details===========================//
  protected function login_details()
  {
    $this->require_login();
    $data['login_detail'] = $this->Login_model->user_details();
    return $data;
  }
  //=========================/Details===========================//

  /**
   * Returns the user to the page they posted from.
   *
   * Replaces redirect($_SERVER['HTTP_REFERER']), which fataled when the header
   * was missing and was an open redirect otherwise, since Referer is entirely
   * caller-controlled (BUG-007).
   */
  protected function _redirect_back($fallback = 'Welcome')
  {
    $referer = isset($_SERVER['HTTP_REFERER']) ? trim($_SERVER['HTTP_REFERER']) : '';

    if ($referer !== '') {
      $host = parse_url($referer, PHP_URL_HOST);
      // Only follow it when it points back at this host.
      if ($host !== null && strcasecmp($host, $_SERVER['HTTP_HOST']) === 0) {
        redirect($referer);
        return;
      }
    }

    redirect($fallback);
  }

  /**
   * Cron endpoints rewrite every stored balance (and one of them sends bulk
   * WhatsApp messages), yet they were reachable by anyone (BUG-010, RISK-12).
   *
   * They still need to run unattended, so instead of a session they require
   * either a CLI invocation or a shared key, passed as `?cron_key=` or an
   * `X-Cron-Key` header. A logged-in superadmin may also trigger them manually.
   *
   * Configure AJAYFRUITS_CRON_KEY in the environment and update the scheduler:
   *   php index.php Sales update_cust_bal_cron
   *   curl -H "X-Cron-Key: <key>" https://…/Sales/update_cust_bal_cron
   */
  protected function _require_cron_auth()
  {
    if (is_cli()) {
      return true;
    }

    if ($this->session->userdata('is_user_in') === true
      && $this->session->userdata('user_type') == 8) {
      return true;
    }

    $expected = (string) getenv('AJAYFRUITS_CRON_KEY');
    if ($expected !== '') {
      $supplied = (string) ($this->input->get_request_header('X-Cron-Key', TRUE)
        ?: $this->input->get('cron_key'));

      if ($supplied !== '' && hash_equals($expected, $supplied)) {
        return true;
      }
    } else {
      log_message('error', 'AJAYFRUITS_CRON_KEY is not set; cron endpoints are refusing all remote calls.');
    }

    $this->output->set_status_header(403);
    echo json_encode(array('status' => 'error', 'message' => 'Access Denied'));
    return false;
  }

  //==================Integration credentials===================//

  /**
   * Reads a third-party credential from config/integrations.php, which sources
   * it from the environment. Previously these were string literals in this
   * file and are still present in git history, so they must be rotated at the
   * provider (BUG-022).
   */
  protected function _whatsapp_credential($key)
  {
    $this->config->load('integrations', TRUE, TRUE);
    $value = (string) $this->config->item($key, 'integrations');

    if ($value === '') {
      log_message('error', "Missing integration credential '{$key}'. Set it in the environment.");
    }

    return $value;
  }

  //=================Signed statement links=====================//

  /**
   * Secret used to sign customer-facing links. Falls back to the framework
   * encryption key so a missing config cannot silently disable signing.
   */
  protected function _link_secret()
  {
    $this->config->load('integrations', TRUE, TRUE);
    $secret = (string) $this->config->item('link_secret', 'integrations');

    if ($secret === '') {
      $secret = (string) $this->config->item('encryption_key');
    }

    return $secret;
  }

  /** Signature for one account/expiry pair. */
  protected function _statement_signature($account_name, $expires)
  {
    return hash_hmac('sha256', $account_name . '|' . $expires, $this->_link_secret());
  }

  /**
   * Builds the customer-facing statement URL with an expiring signature, so a
   * recipient cannot change account_name to read someone else's ledger.
   */
  protected function _signed_statement_url($cust_id, $from_date, $to_date)
  {
    $this->config->load('integrations', TRUE, TRUE);
    $ttl = (int) $this->config->item('link_ttl', 'integrations');
    $expires = time() + ($ttl > 0 ? $ttl : 2592000);

    return 'https://www.ajayfruits.in/Sales/download_customer_statement'
      . '?account_name=' . rawurlencode($cust_id)
      . '&from_date=' . rawurlencode($from_date)
      . '&to_date=' . rawurlencode($to_date)
      . '&expires=' . $expires
      . '&sig=' . $this->_statement_signature($cust_id, $expires);
  }

  /** Aborts unless the request carries a valid, unexpired signature. */
  protected function _require_valid_statement_link($account_name)
  {
    $expires = (int) $this->input->get('expires');
    $sig     = (string) $this->input->get('sig');
    $secret  = $this->_link_secret();

    if ($secret === '') {
      log_message('error', 'Statement link signing has no secret configured; refusing request.');
      show_error('Access Denied', 403);
      return;
    }

    if ($account_name === null || $account_name === '' || $sig === '' || $expires <= 0) {
      show_error('Access Denied', 403);
      return;
    }

    if ($expires < time()) {
      show_error('This statement link has expired. Please request a new one.', 403);
      return;
    }

    if (!hash_equals($this->_statement_signature($account_name, $expires), $sig)) {
      show_error('Access Denied', 403);
      return;
    }
  }

  //======================Login Validation======================//
  protected function require_login()
  {
    $is_user_in = $this->session->userdata('is_user_in');
    if ($is_user_in == true) {
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
    if ($is_user_in == true) {
      return true;
    } else {
      echo json_encode(array('status' => 'error', 'message' => 'You are not Logged in Now!! Please login again.'));
      return false;
    }
  }
  //=====================/Login Validation======================//

  //========================/Profile============================//
}
