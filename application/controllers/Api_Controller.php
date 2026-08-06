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

  //================== API request context ==================//
  //
  // The bearer-token requirement added for BUG-017 was removed at the owner's
  // request: the mobile client cannot be updated right now, and it never sent
  // a token, so every endpoint was answering 401.
  //
  // !! SECURITY NOTE - this restores the pre-BUG-017 behaviour: identity is
  // whatever `user_id` the caller posts, and is NOT verified. Anyone who can
  // reach these endpoints can read or write any account's data by changing
  // that value. This is a deliberate, temporary trade-off to keep the existing
  // app working. Re-introduce token auth (Api_Model::create_token and the
  // `api_tokens` table are still in the schema) as soon as the mobile client
  // can be shipped with it.
  //
  // Branch scoping is derived from that same `user_id`, so no mobile change is
  // needed: whatever the app already sends decides which branch it sees.

  /**
   * Resolves the branch context for this request from the posted `user_id`.
   *
   * Deliberately non-blocking. Only about a quarter of the endpoints are ever
   * given a user_id by the app (the lookup lists - cities, items, payment
   * methods - are not), so refusing the request when it is absent would break
   * exactly the clients this change exists to support. When it is absent the
   * request simply stays unscoped, matching the old behaviour.
   *
   * Returns the resolved user id, or null when none was supplied.
   */
  protected function _api_context()
  {
    $user_id = $this->input->post('user_id') ?: $this->input->get('user_id');

    if (empty($user_id)) {
      $this->Api_Model->set_branch_context(null);
      return null;
    }

    $this->Api_Model->set_branch_context($user_id);
    return $user_id;
  }

  /** Kept so older clients calling it still get a well-formed reply. */
  public function user_logout()
  {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      echo json_encode(array('response' => 'success', 'message' => 'Logged out'));
    }
  }

  public function user_details()
  {
    $this->_api_context();
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
        // No `token` key any more - the response shape is back to what the
        // existing mobile build expects. The app keeps identifying itself by
        // posting `user_id`, which is also what now decides its branch.
        $info = array(
          'response' => 'success',
          'message' => 'Login successfully',
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
          'message' => $this->Api_Model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening contact support.',
          'm_sale_spo' => '',

        );
      }
      echo json_encode($info);
    }
  }

  public function insert_expense()
  {
    $this->_api_context();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if ($details = $this->Api_Model->insert_expense()) {
        $info = array(
          'response' => 'success',
          'message' => 'Expense Added Successfully',

        );
      } else {
        $info = array(
          'response' => 'error',
          'message' => $this->Api_Model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening contact support.',


        );
      }
      echo json_encode($info);
    }
  }

  public function insert_payment_recieved()
  {
    $this->_api_context();
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
          'message' => $this->Api_Model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening contact support.',
          'voucher_no' => '',

        );
      }
      echo json_encode($info);
    }
  }

  public function insert_crate_recived()
  {
    $this->_api_context();
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
          'message' => $this->Api_Model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening contact support.',
          'voucher_no' => '',
        );
      }
      echo json_encode($info);
    }
  }

  public function get_user_payment_received()
  {
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
          'message' => $this->Api_Model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening contact support.',
        );
      }
      echo json_encode($info);
    }
  }

  public function insert_customer()
  {
    $this->_api_context();
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
          'message' => $this->Api_Model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening contact support.',
          'customer' => array(),
        );
      }
      echo json_encode($info);
    }
  }

  ////==================================================== managar apis ===============================================////


  public function insert_issue_item()
  {
    $this->_api_context();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if ($list = $this->Api_Model->insert_issue_item()) {
        $info = array(
          'response' => 'success',
          'message' => 'Item Issued Successfully',

        );
      } else {
        $info = array(
          'response' => 'error',
          'message' => $this->Api_Model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening contact support.',

        );
      }
      echo json_encode($info);
    }
  }

  public function insert_purchase()
  {
    $this->_api_context();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if ($list = $this->Api_Model->insert_purchase()) {
        $info = array(
          'response' => 'success',
          'message' => 'Purchase Added Successfully',

        );
      } else {
        $info = array(
          'response' => 'error',
          'message' => $this->Api_Model->last_error() ?: 'Sorry, that did not go through. Please try again, and if it keeps happening contact support.',

        );
      }
      echo json_encode($info);
    }
  }

  public function get_all_agents()
  {
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
    $this->_api_context();
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
