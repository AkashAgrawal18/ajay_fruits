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
  $password = $this->input->post('password');

  // Define financial year range
  $current_date = date('Y-m-d'); // Corrected variable name
  if(date('m') > 3){
      $crter = date('Y');
  } else {
      $crter = date('Y', strtotime('-1 year', strtotime($current_date))); // Corrected date string
  }
  
  $financial_start = date($crter.'-04-01'); // April 1st
 
  // Check if selected date falls in the last financial year
  if (strtotime($selected_date) < strtotime($financial_start)) {
      if (!$password) {
          echo json_encode(['status' => 'password_required', 'message' => 'Password required to change date']);
          exit; // Ensure script exits to prevent loops
      }

      // Validate password (ensure session is properly managed)
      $user_id = $this->session->userdata('user_id');
      if (!$user_id) {
          echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
          exit;
      }

      $is_valid = $this->Login_model->validate_password($user_id, $password);
      if (!$is_valid) {
          echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
          exit;
      }
  }

  // Proceed with date change
  echo json_encode(['status' => 'success', 'message' => 'Date changed successfully']);
  exit;
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