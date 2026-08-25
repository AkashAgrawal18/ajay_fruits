<?php defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

class Transfer extends CI_Controller
{

  protected function login_details()
  {
    $this->require_login();
    $data['login_detail'] = $this->Login_model->user_details();
    return $data;
  }

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

  protected function ajax_login()
  {
    $is_user_in = $this->session->userdata('is_user_in');
    if ($is_user_in == true) {
      return true;
    }
    echo json_encode(['status' => 'error', 'message' => 'You are not Logged in Now!! Please login again.']);
    return false;
  }

  //========================= Branch Transfer ===========================//

  public function add_transfer()
  {
    $data = $this->login_details();
    if ($this->session->userdata('user_type') != 8) {
      show_error('Access Denied. Only Super Admin can create branch transfers.', 403);
      return;
    }

    $data['pagename']    = "Create Branch Transfer";
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    $data['stock_list']  = $this->Main_model->get_ho_stock_list();

    $this->load->view('add_transfer', $data);
  }

  public function insert_transfer()
  {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') return;

    if ($this->session->userdata('user_type') != 8) {
      echo json_encode(['status' => 'error', 'message' => 'Only Super Admin can transfer stock to a branch']);
      return;
    }

    $to_branch     = $this->input->post('to_branch');
    $transfer_date = $this->input->post('transfer_date');
    $lot_ids       = $this->input->post('lot_id');
    $qtys          = $this->input->post('qty');
    $rates         = $this->input->post('rate');

    if (empty($to_branch) || empty($lot_ids)) {
      echo json_encode(['status' => 'error', 'message' => 'Missing required data']);
      return;
    }

    $items = [];
    foreach ($lot_ids as $i => $lot_id) {
      $items[] = [
        'lot_id' => $lot_id,
        'qty'    => $qtys[$i] ?? 0,
        'rate'   => $rates[$i] ?? 0,
      ];
    }

    $result = $this->Main_model->insert_transfer($items, $to_branch, $transfer_date);

    echo json_encode($result
      ? ['status' => 'success', 'message' => 'Stock transferred to branch successfully!']
      : ['status' => 'error', 'message' => $this->Main_model->last_error()
        ?: 'Transfer failed. Please check available stock and rate.']);
  }

  public function delete_transfer()
  {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') return;

    if ($this->session->userdata('user_type') != 8) {
      echo json_encode(['status' => 'error', 'message' => 'Access Denied']);
      return;
    }

    $spo = $this->input->post('delete_id');
    if (empty($spo)) {
      echo json_encode(['status' => 'error', 'message' => 'Missing transfer reference']);
      return;
    }

    $result = $this->Main_model->delete_transfer($spo);

    echo json_encode($result
      ? ['status' => 'success', 'message' => 'Transfer deleted successfully!']
      : ['status' => 'error', 'message' => 'Delete failed. Please try again.']);
  }

  //========================= Branch Transfer ===========================//

  //========================= Branch Return ===========================//
  // A branch sending its own unsold, still-held transferred stock back to
  // Head Office - the reverse of add_transfer/insert_transfer/delete_transfer
  // above. Gated the same way as those: every stock-moving action in this
  // app is Head-Office-only, so a branch has no route to any of this - only
  // Head Office records that stock physically came back.

  public function return_stock()
  {
    $data = $this->login_details();
    if ($this->session->userdata('user_type') != 8) {
      show_error('Access Denied. Only Super Admin can record stock returned by a branch.', 403);
      return;
    }

    $data['pagename']    = "Return Branch Stock";
    $data['branch_list'] = $this->Main_model->get_user_list(9);

    $this->load->view('return_transfer', $data);
  }

  // AJAX: the chosen branch's currently-held transfer stock, for the lot
  // picker on return_stock() - unlike Transfer To Branch's stock list (HO's
  // own, fixed), the source branch here is chosen at runtime.
  public function get_branch_stock()
  {
    if ($this->ajax_login() === false) {
      return;
    }
    if ($this->session->userdata('user_type') != 8) {
      echo json_encode(['status' => 'error', 'message' => 'Access Denied']);
      return;
    }

    $branch_id = $this->input->post('branch_id');
    if (empty($branch_id)) {
      echo json_encode(['status' => 'error', 'message' => 'Choose a branch first']);
      return;
    }

    echo json_encode(['status' => 'success', 'stock' => $this->Main_model->get_branch_stock_list($branch_id)]);
  }

  public function insert_return()
  {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') return;

    if ($this->session->userdata('user_type') != 8) {
      echo json_encode(['status' => 'error', 'message' => 'Only Super Admin can record a branch return']);
      return;
    }

    $branch_id   = $this->input->post('from_branch');
    $return_date = $this->input->post('return_date');
    $lot_ids     = $this->input->post('lot_id');
    $qtys        = $this->input->post('qty');

    if (empty($branch_id) || empty($lot_ids)) {
      echo json_encode(['status' => 'error', 'message' => 'Missing required data']);
      return;
    }

    $items = [];
    foreach ($lot_ids as $i => $lot_id) {
      $items[] = [
        'lot_id' => $lot_id,
        'qty'    => $qtys[$i] ?? 0,
      ];
    }

    $result = $this->Main_model->insert_return($items, $branch_id, $return_date);

    echo json_encode($result
      ? ['status' => 'success', 'message' => 'Stock return recorded successfully!']
      : ['status' => 'error', 'message' => $this->Main_model->last_error()
        ?: 'Return failed. Please check the branch\'s available stock.']);
  }

  public function delete_return()
  {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') return;

    if ($this->session->userdata('user_type') != 8) {
      echo json_encode(['status' => 'error', 'message' => 'Access Denied']);
      return;
    }

    $spo = $this->input->post('delete_id');
    if (empty($spo)) {
      echo json_encode(['status' => 'error', 'message' => 'Missing return reference']);
      return;
    }

    $result = $this->Main_model->delete_return($spo);

    echo json_encode($result
      ? ['status' => 'success', 'message' => 'Return deleted successfully!']
      : ['status' => 'error', 'message' => 'Delete failed. Please try again.']);
  }

  //========================= Branch Return ===========================//
}
