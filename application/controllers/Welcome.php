<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('Login_model');
  }

  public function index()
  {
    $data = $this->login_details();
    $data['pagename'] = "Dashboard";
    $data['date'] =  $this->input->post('date') ?: date('Y-m-d');
    $data['day_report'] = $this->Report_model->dashboard_staff_summary($data['date']);
    // $data['dashcounts'] = $this->Report_model->dashboard_counts($data['date']);
    $data['pie_data'] = $this->Report_model->get_piechart_data($data['date']);
  //  echo "<pre>"; print_r($data['pie_data']); die ;
    $this->load->view('index', $data);
  }

  public function privacy()
  {
    $this->load->view('privacy');
  }


  public function application_setting()
  {
    $data = $this->login_details();
    $data['pagename'] = "Application Setting";
    $data['app_details'] = $this->Main_model->get_application_settings();
    $this->load->view('application_setting', $data);
  }

  public function update_application_settings()
  {
    if ($this->ajax_login() === false) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($data = $this->Main_model->update_application_settings()) {
        $info = array(
          'status' => 'success',
          'message' => 'Application Settings has been update successfully!'
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


  public function db_backup()
  {
    // Only Super Admin may download a full database backup - this contains
    // every branch's data plus (hashed) user credentials.
    $this->require_login();
    if ($this->session->userdata('user_type') != 8) {
      show_error('Access Denied', 403);
      return;
    }

    $this->load->helper('url');
    $this->load->helper('file');
    $this->load->helper('download');
    $this->load->library('zip');
    $this->load->dbutil();
    $db_format = array(
      'format' => 'zip',
      'filename' => 'sql_backup.sql'
    );
    $backup = &$this->dbutil->backup($db_format);
    $dbname = 'backup-on-' . date('Y-m-d_H:i') . '.zip';

    $backup_dir = FCPATH . 'assets/database_backup/';
    if (!is_dir($backup_dir)) {
      mkdir($backup_dir, 0755, true);
    }
    write_file($backup_dir . $dbname, $backup);

    force_download($dbname, $backup);
  }

  function send_balance_sms()
  {
    // Bulk SMS to every customer with an outstanding balance - restrict to
    // Super Admin so it can't be triggered anonymously (cost/spam vector).
    $this->require_login();
    if ($this->session->userdata('user_type') != 8) {
      show_error('Access Denied', 403);
      return;
    }

    $cust_list = $this->Main_model->get_all_customers();
    if (!empty($cust_list)) {
      foreach ($cust_list as $key) {
        if ($key['total_balance'] > 0 || $key['total_crate_balance'] > 0) {
          // echo '<pre>';
          $this->Api_Model->send_balance_sms($key['m_cust_id']);
        }
      }
    }
  }


  public function get_long_url()
  {
    $alias = $this->uri->segment(1);
    $this->Login_model->get_long_url($alias);
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
    } else {
      echo json_encode(array('status' => 'error', 'message' => 'You are not Logged in Now!! Please login again.'));
      return false;
    }
  }
}
