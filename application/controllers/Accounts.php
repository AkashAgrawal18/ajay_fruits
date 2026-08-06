<?php defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class Accounts extends CI_Controller
{

  ////================================ user ===============================================////
  public function index()
  {
   echo "Welcome";
  }

  public function branch_list()
  {
    $data = $this->login_details();
    $data['pagename'] = "All Branch List";
    $data['pgtype'] = 9;
    $data['from_date'] = $this->input->post('from_date');
    $data['to_date'] = $this->input->post('to_date');
    $data['city_dtl'] = $this->input->post('city_dtl');
    $data['search_in'] = $this->input->post('search_in');
    $data['branch_id'] = $this->input->post('branch_id');

    $data['city_list'] = $this->Master_model->get_active_city();
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    $data['mech_value'] = $this->Main_model->get_user_list($data['pgtype'], $data['from_date'], $data['to_date'], $data['city_dtl'],1,$data['search_in'],$data['branch_id']);
   
    $this->load->view('user_list', $data);
  }

  public function user_list()
  {
    $data = $this->login_details();
    $data['pagename'] = "All Staff Accounts";
    $data['pgtype'] = 1;
    $data['from_date'] = $this->input->post('from_date');
    $data['to_date'] = $this->input->post('to_date');
    $data['city_dtl'] = $this->input->post('city_dtl');
    $data['search_in'] = $this->input->post('search_in');
    $data['branch_id'] = $this->input->post('branch_id');

    $data['city_list'] = $this->Master_model->get_active_city();
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    $data['mech_value'] = $this->Main_model->get_user_list($data['pgtype'], $data['from_date'], $data['to_date'], $data['city_dtl'],1,$data['search_in'],$data['branch_id']);
   
    $this->load->view('user_list', $data);
  }

  public function supplier_list()
  {
    $data = $this->login_details();
    $data['pagename'] = "All Supplier Accounts";
    $data['pgtype'] = 2;
    $data['from_date'] = $this->input->post('from_date');
    $data['to_date'] = $this->input->post('to_date');
    $data['city_dtl'] = $this->input->post('city_dtl');
    $data['search_in'] = $this->input->post('search_in');
    $data['branch_id'] = $this->input->post('branch_id');

    $data['city_list'] = $this->Master_model->get_active_city();
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    $data['mech_value'] = $this->Main_model->get_user_list($data['pgtype'], $data['from_date'], $data['to_date'], $data['city_dtl'],1,$data['search_in'],$data['branch_id']);
   
    $this->load->view('user_list', $data);
  }

  public function loader_list()
  {
    $data = $this->login_details();
    $data['pagename'] = "All Loader Accounts";
    $data['pgtype'] = 3;
    $data['from_date'] = $this->input->post('from_date');
    $data['to_date'] = $this->input->post('to_date');
    $data['city_dtl'] = $this->input->post('city_dtl');
    $data['search_in'] = $this->input->post('search_in');
    $data['branch_id'] = $this->input->post('branch_id');

    $data['city_list'] = $this->Master_model->get_active_city();
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    $data['mech_value'] = $this->Main_model->get_user_list($data['pgtype'], $data['from_date'], $data['to_date'], $data['city_dtl'],1,$data['search_in'],$data['branch_id']);
  
    $this->load->view('user_list', $data);
  }
  public function general_list()
  {
    $data = $this->login_details();
    $data['pagename'] = "All General Accounts";
    $data['pgtype'] = 4;
    $data['from_date'] = $this->input->post('from_date');
    $data['to_date'] = $this->input->post('to_date');
    $data['city_dtl'] = $this->input->post('city_dtl');
    $data['search_in'] = $this->input->post('search_in');
    $data['branch_id'] = $this->input->post('branch_id');

    $data['city_list'] = $this->Master_model->get_active_city();
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    $data['mech_value'] = $this->Main_model->get_user_list($data['pgtype'], $data['from_date'], $data['to_date'], $data['city_dtl'],1,$data['search_in'],$data['branch_id']);
   
    $this->load->view('user_list', $data);
  }
  public function investment_list()
  {
    $data = $this->login_details();
    $data['pagename'] = "All Investment Accounts";
    $data['pgtype'] = 5;
    $data['from_date'] = $this->input->post('from_date');
    $data['to_date'] = $this->input->post('to_date');
    $data['city_dtl'] = $this->input->post('city_dtl');
    $data['search_in'] = $this->input->post('search_in');
    $data['branch_id'] = $this->input->post('branch_id');

    $data['city_list'] = $this->Master_model->get_active_city();
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    $data['mech_value'] = $this->Main_model->get_user_list($data['pgtype'], $data['from_date'], $data['to_date'], $data['city_dtl'],1,$data['search_in'],$data['branch_id']);
  
    $this->load->view('user_list', $data);
  }

  public function add_user()
  {
    $data = $this->login_details();
    $data['id'] = $this->input->get('id');
    $data['pgtype'] = $this->input->get('pgtype');
    if (!empty($data['id'])) {
      $data['pagename'] = "Edit Details";
    } else {
      $data['pagename'] = "Add New";
    }
    $data['state_dtl'] = $this->Master_model->get_active_state();
    $data['city_dtl'] = $this->Master_model->get_active_city();
    $data['edit_value'] = $this->Main_model->get_user_dtl($data['id']);
    $data['branch_id'] = !empty($data['edit_value']) ? $data['edit_value']->m_user_branch : $this->input->get('branch_id');
    $data['group_dtl'] = $this->Master_model->get_all_group(1, $data['branch_id']);
    $data['branch_list'] = $this->Main_model->get_user_list(9);

    $this->load->view('add_user', $data);
  }

  public function insert_user()
  {
    if ($this->ajax_login() === false) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->insert_user()) {

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
        } else if ($data == 3) {
          $info = array(
            'status' => 'error',
            'message' => 'Login id Already taken by another account! Please try again'
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

  public function delete_user()
  {
    if ($this->ajax_login() === false) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_user()) {
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

  // Superadmin-only: decrypt and reveal a staff/branch account's password.
  public function view_password()
  {
    if ($this->ajax_login() === false) {
      return;
    }
    if ($this->session->userdata('user_type') != 8) {
      echo json_encode(array('status' => 'error', 'message' => 'Access Denied'));
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $row = $this->Main_model->get_user_password_enc($this->input->post('id'));
      $password = !empty($row) ? decrypt_password_for_admin($row->m_user_password_enc) : '';

      if ($password === '') {
        echo json_encode(array(
          'status'  => 'error',
          'message' => 'Password not available. It becomes viewable again after this user\'s password is next changed.',
        ));
        return;
      }
      echo json_encode(array('status' => 'success', 'password' => $password));
    }
  }

  public function view_users_detail()
  {
    $data = $this->login_details();
    $data['id'] = $this->input->get('id');

    $data['pagename'] = "Profile Details";
    $data['edit_value'] = $this->Main_model->get_user_dtl($data['id']);
    $data['mech_value'] = $this->Main_model->get_provider_staff_by_id($data['id']);
    $this->load->view('User_details', $data);
  }

  ////================================ user ===============================================////

  ////================================ Customer ===============================================////

  public function cust_list()
  {
    $data = $this->login_details();
    $data['pagename'] = "All Customers List";
    $data['type'] = 2;
    $data['from_date'] = $this->input->post('from_date');
    $data['to_date'] = $this->input->post('to_date');
    $data['city_dtl'] = $this->input->post('city_dtl');
    $data['search_in'] = $this->input->post('search_in');
    $data['branch_id'] = $this->input->post('branch_id');

    $data['city_list'] = $this->Master_model->get_active_city();
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    $data['mech_value'] = $this->Main_model->get_all_customers($data['from_date'], $data['to_date'], $data['city_dtl'], $data['search_in'], $data['branch_id']);
    $this->load->view('customer_list', $data);
  }

  public function customer_ledger($id)
  {
    $data = $this->login_details();
    $data['pagename'] = "Customer Ledger";
    $data['id'] = $id;
    $data['from_date'] = $this->input->post('from_date');
    $data['to_date'] = $this->input->post('to_date');
    $data['cust_detail'] = $this->Main_model->get_cust_dtl($data['id']);
    $data['cust_balance'] = $this->Main_model->get_customer_balance($data['id']);
    $data['crate_balance'] = $this->Main_model->get_crate_balance($data['id']);
    // $data['all_crates'] = $this->Master_model->all_itemgroup(3);
    $data['amount_ledger'] = $this->Report_model->get_customer_amount_ledger($data['id']);
    $data['crate_ledger'] = $this->Report_model->get_customer_crate_ledger($data['id']);

    $this->load->view('customer_ledger', $data);
  }

  public function add_cust()
  {
    $data = $this->login_details();
    $data['id'] = $this->input->get('id');
    $data['type'] = $this->input->get('type');
    if (!empty($data['id'])) {
      $data['pagename'] = "Edit Details";
    } else {
      $data['pagename'] = "Add New Customer";
    }
    $data['state_dtl'] = $this->Master_model->get_active_state();
    $data['city_dtl'] = $this->Master_model->get_active_city();
    $data['edit_value'] = $this->Main_model->get_cust_dtl($data['id']);
    $data['branch_id'] = !empty($data['edit_value']) ? $data['edit_value']->m_cust_branch : $this->input->get('branch_id');
    $data['group_dtl'] = $this->Master_model->get_all_group(1, $data['branch_id']);
    $data['branch_list'] = $this->Main_model->get_user_list(9);

    $this->load->view('add_customer', $data);
  }

  public function insert_cust()
  {
    if ($this->ajax_login() === false) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->insert_cust()) {

        if ($data == 1) {
          $info = array(
            'status' => 'success',
            'message' => 'New Customer has been Added successfully!'
          );
        } else if ($data == 2) {
          $info = array(
            'status' => 'success',
            'message' => 'Customer data Updated Successfully'
          );
        } else if ($data == 3) {
          $info = array(
            'status' => 'errpr',
            'message' => 'Login id Already taken by other customer! Please try again'
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


  public function excelForcust($allreportdata)
  {

    $count = 0;
    $data = array();
    foreach ($allreportdata as $key) {
      $count++;
      $subArray = array();

      $subArray[] = $count;
      $subArray[] = $key->m_cust_name;
      $subArray[] = $key->m_cust_mobile;
      $subArray[] = $key->m_city_name;
      $subArray[] = $key->m_state_name;
      $subArray[] = $key->m_cust_address;
      $subArray[] = $key->m_cust_trademark;
      $subArray[] = $key->m_cust_contractPerd;
      if ($key->m_cust_status == 1) {
        $status = "Active";
      } else {
        $status = "In-Active";
      }
      $subArray[] = $status;
      $subArray[] = date('d-m-Y h:i', strtotime($key->m_cust_added_on));

      $data[] = $subArray;
    }

    //  echo "<pre>" ;   print_r($data) ; die ;
    $fileName = 'customer' . date('Y_m_d_h_i_s') . '.csv';
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$fileName");
    header("Content-Type: application/csv; ");
    $report = $data;
    $file = fopen('php://output', 'w');

    $header = array(
      "ID",
      "Name",
      "Mobile No.",
      "City",
      "State",
      "Address",
      "Trade Mark",
      "Contact Person",
      "Status",
      "Joining Date",

    );


    fputcsv($file, $header);
    foreach ($report as $line) {
      fputcsv($file, $line);
    }
    fclose($file);

    exit;
  }

  public function import_custs_data()
  {
    $this->require_login();
    //$salon_id = $this->session->custdata('s_id');
    if (isset($_FILES['import_file'])) {
      require_once "Simplexlsx.class.php";
      $xlsx = new SimpleXLSX($_FILES['import_file']['tmp_name']);
      list($cols, $rows) = $xlsx->dimension();
      $i = 0;
      foreach ($xlsx->rows() as $row) {
        $i++;
        if ($i != 1) {

          $checkState = $this->db->where('m_state_name', $row[8])->get('master_state_tbl')->result();
          $checkcity = $this->db->where('m_city_name', $row[9])->get('master_city_tbl')->result();

          $s_data = array(
            "m_cust_name" => $row[1],
            "m_cust_mobile" => $row[2],
            // "m_cust_phoneno" => $row[5],
            "m_cust_remark" => $row[3],
            "m_cust_contractPerd" => $row[4],
            "m_cust_pan_no" => $row[5],
            "m_cust_accountno" => $row[6],
            "m_cust_adharno" => $row[7],
            "m_cust_state" => $checkState[0]->m_state_id ?: '',
            "m_cust_city" => $checkcity[0]->m_city_id ?: '',
            "m_cust_address" => $row[10],
            "m_cust_trademark" => $row[11],
            "m_cust_status" => 1,

          );

          $this->db->insert('master_customer_tbl', $s_data);
        }
      }
      echo "<script> alert('import Successfull'); </script>";
      redirect($redirt);
    } else {
      echo "<script> alert('Import is wrong'); </script>";
    }
  }

  public function delete_customer()
  {
    if ($this->ajax_login() === false) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_customer()) {
        $info = array(
          'status' => 'success',
          'message' => 'cust has been Deleted successfully!'
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


  ////================================ Customer ===============================================////

  //========================= custgrp ===========================//

  public function customer_group_list($group = '')
  {
    $data = $this->login_details();
    $data['pagename'] = "Customer Group list";

    // $data['to_date'] = $this->input->post('to_date');
    // $data['city_dtl'] = $this->input->post('city_dtl');
    $data['branch_id'] = $this->input->post('branch_id');
    $data['branch_list'] = $this->Main_model->get_user_list(9);

    $data['all_value'] = $this->Main_model->get_customer_group_list($group, $data['branch_id']);
    $this->load->view('customer_group', $data);
  }

  public function custgrp_list()
  {
    $data = $this->login_details();
    $data['pagename'] = "All Group list";
    $data['from_date'] = $this->input->post('from_date');
    $data['to_date'] = $this->input->post('to_date');
    $data['city_dtl'] = $this->input->post('city_dtl');
    $data['branch_id'] = $this->input->post('branch_id');
    $data['branch_list'] = $this->Main_model->get_user_list(9);

    $data['all_value'] = $this->Main_model->all_custgrp($data['branch_id']);
    $this->load->view('customer_group', $data);
  }

  public function add_custgrp()
  {
    $data = $this->login_details();
    $data['pagename'] = "Add Customer Group";
    $data['id'] = $this->input->get('id');
    $data['edit_value'] = $this->Main_model->get_edit_custgrp($data['id']);
    $data['branch_id'] = !empty($data['edit_value']) ? $data['edit_value']->m_custgrp_branch : $this->input->get('branch_id');
    $data['staff_list'] = $this->Main_model->get_active_user_list(1, $data['branch_id']);
    $data['cust_list'] = $this->Main_model->get_cust_list(null, null, null, null, null, $data['branch_id']);
    $data['branch_list'] = $this->Main_model->get_user_list(9);
    $this->load->view('add_customer_group', $data);
  }

  public function insert_custgrp()
  {
    if ($this->ajax_login() === false) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->insert_custgrp()) {

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

  public function delete_custgrp()
  {
    if ($this->ajax_login() === false) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->delete_custgrp()) {
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
  //========================= custgrp ===========================//

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
