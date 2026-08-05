<?php defined('BASEPATH') or exit('No direct script access allowed');

date_default_timezone_set('Asia/Kolkata');

class Api_Controller extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
  }


  public function index()
  {
    echo "Not Found";
  }

  //================== API authentication (BUG-017) ==================//
  // This controller has no session and is CSRF-exempt, so identity must come
  // from the bearer token issued by user_login(). Endpoints previously trusted
  // a caller-supplied user_id, which let anyone read or write any account.

  /** Token from the Authorization header, or a `token` field for older clients. */
  protected function _bearer_token()
  {
    $header = $this->input->get_request_header('Authorization', TRUE);
    if (!empty($header) && preg_match('/Bearer\s+(\S+)/i', $header, $m)) {
      return $m[1];
    }

    return $this->input->post('token') ?: $this->input->get('token');
  }

  /**
   * Returns the authenticated user id, or emits a 401 JSON body and returns
   * null. Callers must `return` immediately when this yields null.
   *
   * Also pins user_id in the request to the authenticated user so existing
   * endpoint bodies that read $this->input->post('user_id') cannot be spoofed.
   */
  protected function _require_api_user()
  {
    $user_id = $this->Api_Model->user_id_from_token($this->_bearer_token());

    if ($user_id === null) {
      $this->output->set_status_header(401);
      echo json_encode(array(
        'response' => 'error',
        'message'  => 'Unauthorized. Please log in again.',
        'details'  => array(),
      ));
      return null;
    }

    $_POST['user_id'] = $user_id;
    return $user_id;
  }

  /** Ends a session by invalidating the presented token. */
  public function user_logout()
  {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $this->Api_Model->revoke_token($this->_bearer_token());
      echo json_encode(array('response' => 'success', 'message' => 'Logged out'));
    }
  }

  public function user_details()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $user_id = $this->input->post('user_id');
      if ($details = $this->Api_Model->user_details($user_id)) {
        $info = array(
          'response' => 'success',
          'message' => 'User Details',
          'details' => $details
        );
      } else {
        $info = array(
          'response' => 'error',
          'message' => 'No data found',
          'details' => array()
        );
      }
      echo json_encode($info);
    }
  }


  public function user_login()
  {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $mobile   = $this->input->post('m_user_mobile');
      $password = $this->input->post('m_user_password');
      $check = $this->Api_Model->check_mobile($mobile);
      if (empty($check)) {
        // Previously this built the response and then fell through to the
        // login attempt, which overwrote it with "Wrong Password" (BUG-024).
        echo json_encode(array(
          'response' => 'error',
          'message' => 'This Mobile No. Does not exists',
          'user' => array()
        ));
        return;
      }

      if ($user = $this->Api_Model->user_login($mobile, $password)) {
        $info = array(
          'response' => 'success',
          'message' => 'Login successfully',
          // Clients must send this on every subsequent call, as
          // `Authorization: Bearer <token>` (BUG-017).
          'token' => $this->Api_Model->create_token($user[0]->m_user_id),
          'user' => $this->Api_Model->user_details($user[0]->m_user_id),
        );
      } else {

        $info = array(
          'response' => 'error',
          'message' => 'Wrong Password ! Please Try Again',
          'user' => array()
        );
      }
      echo json_encode($info);
    }
  }


  public function get_all_city()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($list = $this->Api_Model->get_all_city()) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }
  public function get_all_state()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($list = $this->Api_Model->get_all_state()) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }


  public function get_all_items()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($list = $this->Api_Model->get_all_items()) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }

  public function get_all_unit()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($list = $this->Api_Model->all_itemgroup(2)) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }
  public function get_all_crate()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($list = $this->Api_Model->all_itemgroup(3, 1)) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }

  public function get_all_group()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($list = $this->Api_Model->all_itemgroup(1)) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }

  public function get_user_customers()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $user_group = $this->input->post('m_user_group');
      if ($list = $this->Api_Model->get_user_customers($user_group)) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }

  public function get_user_items()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($list = $this->Api_Model->get_user_items()) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }


  public function customer_details()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $cust_id = $this->input->post('cust_id');
      if ($details = $this->Api_Model->customer_details($cust_id)) {
        $info = array(
          'response' => 'success',
          'message' => 'Customer Details',
          'details' => $details
        );
      } else {
        $info = array(
          'response' => 'error',
          'message' => 'No data found',
          'details' => array()
        );
      }
      echo json_encode($info);
    }
  }

  public function get_sale_details()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if ($details = $this->Api_Model->get_sale_details()) {
        $info = array(
          'response' => 'success',
          'message' => 'Sale Details',
          'details' => $details
        );
      } else {
        $info = array(
          'response' => 'error',
          'message' => 'No data found',
          'details' => array()
        );
      }
      echo json_encode($info);
    }
  }

  public function get_user_sales()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $user_id = $this->input->post('user_id');
      $fdate = $this->input->post('fdate');
      if ($list = $this->Api_Model->get_user_sales($user_id, $fdate)) {
        $info = array(
          'response' => 'success',
          'list' => $list
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array()
        );
      }
      echo json_encode($info);
    }
  }

  public function insert_sale()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if ($details = $this->Api_Model->insert_sale()) {
        $info = array(
          'response' => 'success',
          'message' => 'Sale Inserted Successfully',
          'm_sale_spo' => (string)$details,

        );
      } else {
        $info = array(
          'response' => 'error',
          'message' => 'Something Went Worng Please Try again',
          'm_sale_spo' => '',

        );
      }
      echo json_encode($info);
    }
  }

  public function insert_expense()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if ($details = $this->Api_Model->insert_expense()) {
        $info = array(
          'response' => 'success',
          'message' => 'Expense Added Successfully',

        );
      } else {
        $info = array(
          'response' => 'error',
          'message' => 'Something Went Worng Please Try again',


        );
      }
      echo json_encode($info);
    }
  }

  public function insert_payment_recieved()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $details = $this->Api_Model->insert_payment_recieved();
      if ($details == 'dupli') {
        $info = array(
          'response' => 'error',
          'message' => 'Payment Already Added !',
          'voucher_no' => '',

        );
      } else if (!empty($details)) {
        $info = array(
          'response' => 'success',
          'message' => 'Payment Collected Successfully',
          'voucher_no' => (string)$details,

        );
      } else {
        $info = array(
          'response' => 'error',
          'message' => 'Something Went Worng Please Try again',
          'voucher_no' => '',

        );
      }
      echo json_encode($info);
    }
  }

  public function insert_crate_recived()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if ($details = $this->Api_Model->insert_crate_recived()) {
        $info = array(
          'response' => 'success',
          'message' => 'Crate Recieved Successfully',
          'voucher_no' => (string)$details,
        );
      } else {
        $info = array(
          'response' => 'error',
          'message' => 'Something Went Worng Please Try again',
          'voucher_no' => '',
        );
      }
      echo json_encode($info);
    }
  }

  public function get_user_payment_received()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $user_id =  $this->input->post('user_id');
      $fdate =  $this->input->post('fdate');
      if ($list = $this->Api_Model->get_user_payment_received($user_id, $fdate)) {

        $expense = $this->db->where('m_exp_user', $user_id)->where('m_exp_added_by', $user_id)->where('m_exp_date', $fdate)->where('m_exp_name', 83)->get('master_expenses_tbl')->row();

        $info = array(
          'response' => 'success',
          'list' => $list,
          'expense_amt' => isset($expense)?$expense->m_exp_amount:"",
          'expense_remark' => isset($expense)?$expense->m_exp_remark:"",
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
          'expense_amt' => "",
          'expense_remark' => "",
        );
      }
      echo json_encode($info);
    }
  }


  public function get_user_crate_received()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $user_id =  $this->input->post('user_id');
      $fdate =  $this->input->post('fdate') ?: date('Y-m-d');
      if ($list = $this->Api_Model->get_user_crate_received($user_id, $fdate)) {
        $info = array(
          'response' => 'success',
          'list' => $list
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array()
        );
      }
      echo json_encode($info);
    }
  }


  public function get_customer_balance()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if ($list = $this->Api_Model->get_customer_balance($this->input->post("cust_id"))) {
        $info = array(
          'response' => 'success',
          'list' => $list
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array()
        );
      }
      echo json_encode($info);
    }
  }

  public function get_customer_crateledger()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if ($list = $this->Api_Model->get_crate_balance($this->input->post("customer_id"))) {
        $info = array(
          'response' => 'success',
          'list' => $list
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array()
        );
      }
      echo json_encode($info);
    }
  }

  public function get_bill_link()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $info = array(
        'response' => 'success',
        'link' => base_url('Sales/bill_print?id=') . $this->input->post("m_sale_spo"),
      );
    } else {
      $info = array(
        'response' => 'error',
        'link' => array()
      );
    }
    echo json_encode($info);
  }

  public function get_cratebill_link()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $info = array(
        'response' => 'success',
        'link' => base_url('Sales/crate_bill_print/') . $this->input->post("m_recvd_voucher"),
      );
    } else {
      $info = array(
        'response' => 'error',
        'link' => array()
      );
    }
    echo json_encode($info);
  }

  public function get_paymentbill_link()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $info = array(
        'response' => 'success',
        'link' => base_url('Sales/payment_bill_print/') . $this->input->post("m_recvd_voucher"),
      );
    } else {
      $info = array(
        'response' => 'error',
        'link' => array()
      );
    }
    echo json_encode($info);
  }

  public function get_payment_report_link()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $fdate =  $this->input->post('fdate') ?: date('Y-m-d');
      $info = array(
        'response' => 'success',
        'link' => base_url('Sales/generate_report_payment/') . $this->input->post("user_id") . '/' . $fdate,
      );
    } else {
      $info = array(
        'response' => 'error',
        'link' => array()
      );
    }
    echo json_encode($info);
  }

  public function get_crate_report_link()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $fdate =  $this->input->post('fdate') ?: date('Y-m-d');
      $info = array(
        'response' => 'success',
        'link' => base_url('Sales/generate_report_crate/') . $this->input->post("user_id") . '/' . $fdate,
      );
    } else {
      $info = array(
        'response' => 'error',
        'link' => array()
      );
    }
    echo json_encode($info);
  }

  public function get_sale_report_link()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $fdate =  $this->input->post('fdate') ?: date('Y-m-d');
      $info = array(
        'response' => 'success',
        'link' => base_url('Sales/generate_report_sale/') . $this->input->post("user_id") . '/' . $fdate,
      );
    } else {
      $info = array(
        'response' => 'error',
        'link' => array()
      );
    }
    echo json_encode($info);
  }


  public function get_user_today_stock()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $user_id = $this->input->post('user_id');
      if ($list = $this->Api_Model->get_user_today_stock($user_id)) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }

  public function get_user_balance_stock()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $user_id = $this->input->post('user_id');
      $fdate = $this->input->post('fdate') ?: date('Y-m-d');
      if ($list = $this->Api_Model->get_user_balance_stock($user_id, $fdate)) {
        $info = array(
          'response' => 'success',
          'list' => $list
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array()
        );
      }
      echo json_encode($info);
    }
  }

  public function todays_stats()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $user_id = $this->input->post('user_id');
      $fdate = $this->input->post('fdate') ?: date('Y-m-d');
      if ($list = $this->Api_Model->todays_stats($user_id, $fdate)) {
        $info = array(
          'response' => 'success',
          'list' => $list
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array()
        );
      }
      echo json_encode($info);
    }
  }


  public function insert_return_item()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $user_id = $this->input->post('user_id');
      $date = $this->input->post('from_date');
      if ($list = $this->Api_Model->insert_return_item($user_id, $date)) {
        $info = array(
          'response' => 'success',
          'message' => 'Stock Retured Successfully',
        );
      } else {
        $info = array(
          'response' => 'error',
          'message' => 'Something Went Worng Please Try again',
        );
      }
      echo json_encode($info);
    }
  }

  public function insert_customer()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if ($list = $this->Api_Model->insert_customer()) {
        $info = array(
          'response' => 'success',
          'message' => 'New Customer Added Successfully',
          'customer' => $this->Api_Model->customer_details($list),
        );
      } else {
        $info = array(
          'response' => 'error',
          'message' => 'Something Went Worng Please Try again',
          'customer' => array(),
        );
      }
      echo json_encode($info);
    }
  }

  ////==================================================== managar apis ===============================================////


  public function insert_issue_item()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if ($list = $this->Api_Model->insert_issue_item()) {
        $info = array(
          'response' => 'success',
          'message' => 'Item Issued Successfully',

        );
      } else {
        $info = array(
          'response' => 'error',
          'message' => 'Something Went Worng Please Try again',

        );
      }
      echo json_encode($info);
    }
  }

  public function insert_purchase()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if ($list = $this->Api_Model->insert_purchase()) {
        $info = array(
          'response' => 'success',
          'message' => 'Purchase Added Successfully',

        );
      } else {
        $info = array(
          'response' => 'error',
          'message' => 'Something Went Worng Please Try again',

        );
      }
      echo json_encode($info);
    }
  }

  public function get_all_agents()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($list = $this->Api_Model->get_all_agents()) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }

  public function get_all_supplier()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($list = $this->Api_Model->get_all_supplier()) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }

  public function get_item_issue_list()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $from_date = $this->input->post('from_date');
      $todate = $this->input->post('todate');
      $agent = $this->input->post('agent');
      $user_id = $this->input->post('user_id');
      if ($list = $this->Api_Model->get_item_issue_list($user_id, $from_date, $todate, $agent)) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }

  public function get_purchase_list()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $from_date = $this->input->post('from_date');
      $todate = $this->input->post('todate');
      $supplier = $this->input->post('supplier');
      $user_id = $this->input->post('user_id');
      if ($list = $this->Api_Model->get_purchase_list($user_id, $from_date, $todate, $supplier)) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }

  public function get_purchase_detail()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      $m_purcs_spo = $this->input->post('m_purcs_spo');

      if ($list = $this->Api_Model->get_purchase_detail($m_purcs_spo)) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }

  public function get_issue_detail()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      $si_issue_spo = $this->input->post('si_issue_spo');

      if ($list = $this->Api_Model->get_issue_detail($si_issue_spo)) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }

  public function get_payment_methods()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if ($list = $this->Api_Model->get_payment_methods()) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }

  public function get_item_avil_lot()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {


      // if ($list = $this->Api_Model->get_item_avil_lot(null, date('Y-m-d'), $this->input->post('m_item_id'))) {
      if ($list = $this->Api_Model->get_avilable_item(date('Y-m-d'), $this->input->post('m_item_id'))) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }

  public function get_avil_items()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {


      if ($list =$this->Api_Model->get_avilable_item(date('Y-m-d'),null,1)) {
        $info = array(
          'response' => 'success',
          'list' => $list,
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array(),
        );
      }
      echo json_encode($info);
    }
  }

  public function get_agents_performance()
  {
    if ($this->_require_api_user() === null) {
      return;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $fdate =  $this->input->post('fdate') ?: date('Y-m-d');
      if ($list = $this->Api_Model->get_agents_performance($fdate)) {
        $info = array(
          'response' => 'success',
          'list' => $list
        );
      } else {
        $info = array(
          'response' => 'error',
          'list' => array()
        );
      }
      echo json_encode($info);
    }
  }
}
