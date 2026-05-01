<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
//============================Login===========================//

//============================Login===========================//

function __construct() {
    parent::__construct();
    $this->load->model('Login_model');
}

public function index(){ 
  $data['pagename'] = "Log-in";

  if($_SERVER["REQUEST_METHOD"] == "POST"){

    $rules = array(
      array('field'=>'login_id',   'label'=>'Email',   'rules'=>'trim|required'), 
      array('field'=>'login_pass', 'label'=>'Password','rules'=>'trim|required')
    ); 
    $this->form_validation->set_rules($rules); //pass the rules array here

     //by default initial load condition
    if ($this->form_validation->run() == FALSE) { }else{

      if($data = $this->Login_model->validate_user()){
        $usrdata=array('is_user_in' => true, 'user_id' => $data[0]->m_admin_id,'user_type'=>$data[0]->m_admin_type,'user_name'=> $data[0]->m_admin_name);
        $this->session->set_userdata($usrdata);
          redirect('Welcome');
      }else{ 
        $this->session->set_flashdata('status','<div class="alert alert-danger"> <strong><i class="fa fa-warning"></i> &nbsp; Some Problem Occurred !...</strong> Please Try Again. </div>');
      }

    }

  }

  $this->load->view('login', $data); 
}

public function customer_login(){ 
  $data['pagename'] = "Log-in";

  if($_SERVER["REQUEST_METHOD"] == "POST"){

    $rules = array(
      array('field'=>'login_id',   'label'=>'Email',   'rules'=>'trim|required'), 
      array('field'=>'login_pass', 'label'=>'Password','rules'=>'trim|required')
    ); 
    $this->form_validation->set_rules($rules); //pass the rules array here

     //by default initial load condition
    if ($this->form_validation->run() == FALSE) { }else{

      if($data = $this->Login_model->validate_customer()){
        $usrdata=array('is_cust_in' => true, 'cust_id' => $data[0]->m_cust_id,'cust_name'=> $data[0]->m_cust_name);
        $this->session->set_userdata($usrdata);
          redirect('Reports/account_ledger');
      }else{ 
        $this->session->set_flashdata('status','<div class="alert alert-danger"> <strong><i class="fa fa-warning"></i> &nbsp; Incorrect Login Id Or password !...</strong> Please Try Again. </div>');
      }

    }

  }

  $this->load->view('custLogin', $data); 
}

public function change_date() {
    $selected_date = $this->input->post('date');
    $password      = $this->input->post('password');

    if (!$selected_date) {
        echo json_encode(['status' => 'error', 'message' => 'Date is required']);
        exit;
    }

    // Determine financial year start
    $financial_start = $this->_get_financial_year_start();

    // Check if date lock is enabled from DB
    $lock_enabled = get_settings('date_lock_enabled'); // returns 0 or 1

    // If lock is OFF or date is within current financial year → allow freely
    if (!$lock_enabled || strtotime($selected_date) >= strtotime($financial_start)) {
        echo json_encode(['status' => 'success', 'message' => 'Date changed successfully']);
        exit;
    }

    // Lock is ON and date is in previous financial year → require password
    if (!$password) {
        echo json_encode(['status' => 'password_required', 'message' => 'Password required to change date to a previous financial year']);
        exit;
    }

    $user_id = $this->session->userdata('user_id');
    if (!$user_id) {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
        exit;
    }

    $is_valid = get_settings('date_lock_password') === $password; // Validate password from DB
    if (!$is_valid) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
        exit;
    }

    echo json_encode(['status' => 'success', 'message' => 'Date changed successfully']);
    exit;
}

// Private helper to avoid repeated date logic
private function _get_financial_year_start() {
    $year = (date('m') > 3) ? date('Y') : date('Y', strtotime('-1 year'));
    return date($year . '-04-01');
}


public function logout()
{
     session_destroy(); 
     redirect('Login');
}
public function logout_cust()
{
     session_destroy(); 
     redirect('CustLogin');
}










//===========================/Login===========================//

//===========================/Login===========================//
} ?>