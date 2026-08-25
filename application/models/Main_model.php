<?php date_default_timezone_set('Asia/Kolkata');

class Main_model extends CI_model
{
  /** Crate item-groups, memoised per request (see get_opening_balance). */
  private $crate_types_cache = null;

  /**
   * Why the last write failed.
   *
   * These methods signal failure by returning false, which left every caller
   * with nothing to say but "Some problem Occurred!! please try again" - the
   * same sentence whether the date was locked, the record belonged to another
   * branch, or there was no stock. Failure paths now call fail() with the
   * actual reason and the controller reports it.
   */
  protected $last_error = '';

  /**
   * Records why an operation failed and returns false, so failure paths can
   * read `return $this->fail('...');`.
   */
  protected function fail($message)
  {
    $this->last_error = $message;
    return false;
  }

  /**
   * The reason for the last failure, then clears it - so a stale message from
   * an earlier call in the same request can never be reported against a later
   * one. Returns '' when the failure had no recorded reason.
   */
  /**
   * Replaces null with '' in an insert/update payload.
   *
   * Almost every column in this schema is NOT NULL with no default, while
   * input->post() yields null for a field that was not submitted. Sending that
   * null through produced "A Database Error Occurred" with the statement on
   * screen; '' stores as '' for text and 0 for numerics (sql_mode is not
   * strict), which is what the forms send for a blank field anyway.
   *
   * The few columns that are genuinely nullable are left alone.
   */
  protected function no_nulls($data)
  {
    if (!is_array($data)) {
      return $data;
    }

    static $keep_null = array(
      'm_purcs_from_branch',
      'm_purcs_ref_lot',
      'm_purcs_type',
      'm_user_password_enc',
    );

    foreach ($data as $k => $v) {
      if ($v === null && !in_array($k, $keep_null, true)) {
        $data[$k] = '';
      }
    }

    return $data;
  }

  public function last_error()
  {
    $msg = $this->last_error;
    $this->last_error = '';
    return $msg;
  }

  /**
   * Helper: returns the current branch ID from session.
   * All queries use this to scope data per branch.
   */
  private function is_superadmin()
  {
    return $this->session->userdata('user_type') == 8;
  }

  /**
   * A "branch" is a master_users_tbl row of m_user_type = 9 — the ledgers join
   * on it (Report_model::transfer_ledger_data joins m_user_id = m_purcs_branch).
   * So a branch account really is scoped by its own user id.
   *
   * That rule must NOT be applied to every non-superadmin, though: an ordinary
   * type-1 user is not a branch, so scoping them by their own user id filters
   * against a branch that does not exist and returns nothing (BUG-002), while
   * their writes get stamped with a non-branch id (BUG-003).
   *
   * Returns null to mean "no branch filter".
   */
  private function branch_id($override = null)
  {
    $type = $this->session->userdata('user_type');

    // Branch account: scoped to itself.
    if ($type == 9) {
      return (int) $this->session->userdata('user_id');
    }

    // Superadmin: unscoped unless a branch was explicitly chosen.
    if ($type == 8) {
      return ($override !== null && $override !== '') ? (int) $override : null;
    }

    // Everyone else: unscoped. Head-office data carries branch 0.
    return null;
  }

  /**
   * Branch value to stamp on a new row. The *_branch columns are NOT NULL, so a
   * null filter must persist as 0 (head office) rather than being written as
   * NULL, which MySQL rejects outright (BUG-009).
   */
  private function branch_for_insert($override = null)
  {
    $branch = $this->branch_id($override);
    return $branch === null ? 0 : (int) $branch;
  }

  /**
   * Moves what a branch owes Head Office, held on the branch account's own
   * m_user_balance.
   *
   * Deliberately not update_userbalance(): that scopes its UPDATE by
   * m_user_branch, and a branch account's row lives AT Head Office
   * (m_user_branch = 0, see master_users_tbl). So when a branch user records
   * their own payment to Head Office, the session scope resolves to their id
   * and the WHERE matches nothing - the row saves and the balance silently
   * does not move. Head Office's own receipt path never hit this because
   * superadmin resolves to no branch filter at all.
   *
   * The m_user_type check keeps this from ever moving a non-branch account.
   */
  private function update_branch_ho_balance($branch_id, $amt)
  {
    if (empty($branch_id) || empty($amt)) {
      return;
    }

    $this->db->set('m_user_balance', 'm_user_balance + ' . (float) $amt, FALSE)
      ->where('m_user_id', (int) $branch_id)
      ->where('m_user_type', 9)
      ->update('master_users_tbl');
  }

  /**
   * Start of the current financial year (1 April), mirroring
   * Login::_get_financial_year_start().
   */
  private function financial_year_start()
  {
    $year = (date('m') > 3) ? date('Y') : date('Y', strtotime('-1 year'));
    return $year . '-04-01';
  }

  /**
   * True when $date falls in a locked (previous) financial year and the lock is
   * switched on. The lock used to be enforced only in the browser, so a crafted
   * POST could write straight into closed books (BUG-018).
   */
  /**
   * The message shown when a write is refused because its date is locked.
   * Names the date and the cut-off, so the user knows why and what to do -
   * "This date falls in a locked financial year" left them guessing which
   * date was at fault on a multi-row form.
   */
  protected function locked_date_message($date)
  {
    $start = $this->financial_year_start();
    return 'Entry dated ' . date('d-m-Y', strtotime($date))
      . ' was refused: entries before ' . date('d-m-Y', strtotime($start))
      . ' are locked for the closed financial year.'
      . ' A super admin can unlock that period in Settings.';
  }

  protected function date_is_locked($date)
  {
    if (empty($date)) {
      return false;
    }

    if (!function_exists('get_settings') || !get_settings('date_lock_enabled')) {
      return false;
    }

    return strtotime($date) < strtotime($this->financial_year_start());
  }

  private function where_branch($column, $override = null)
  {
    $branch = $this->branch_id($override);
    if ($branch !== null) {
      $this->db->where($column, $branch);
    }
    return $this->db;
  }

  private function branch_sql($column, $override = null)
  {
    $branch = $this->branch_id($override);
    return $branch !== null ? " AND {$column} = " . (int) $branch . " " : "";
  }

  // ===================== users =======================//

  public function get_user_list($type, $from_date = '', $to_date = '', $city_dtl = '', $orderby = '', $search = '', $branch_id = null)
  {

    $this->where_branch('master_users_tbl.m_user_branch', $branch_id);

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
      // Bound like() rather than interpolating into where("...") - a single
      // string argument to where() is passed through unescaped, which made
      // this search box a SQL injection point (BUG-029).
      $this->db->group_start()
        ->like('m_user_name', $search)
        ->or_like('m_user_mobile', $search)
        ->or_like('m_city_name', $search)
        ->group_end();
    }

    $this->db->select('master_users_tbl.*,m_city_name,m_state_name');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = master_users_tbl.m_user_city', 'left');
    $this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_users_tbl.m_user_state', 'left');

    if (!empty($orderby)) {
      if ($orderby == 1) {
        $this->db->order_by('m_user_name');
      } else if ($orderby == 3) {
        $this->db->order_by('m_city_name');
      } else {
        $this->db->order_by('m_city_name');
      }
    }
    $this->db->group_by('m_user_id');
    return $this->db->get('master_users_tbl')->result();
  }

  public function get_active_user_list($type, $branch_id = null)
  {
    $this->where_branch('master_users_tbl.m_user_branch', $branch_id);
    if (!empty($type)) {
      $this->db->where('m_user_type', $type);
    }
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = master_users_tbl.m_user_city', 'left');
    $this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_users_tbl.m_user_state', 'left');
    $list = $this->db->order_by('m_user_name')->get('master_users_tbl')->result();

    // A branch buys from Head Office and settles with it, so Head Office belongs
    // in the branch's supplier picker. Prepended rather than stored, because
    // there is no Head Office row to store - see head_office_party().
    if ($type == 2 && $this->is_branch_user()) {
      array_unshift($list, $this->head_office_party());
    }

    return $list;
  }

  /** True when the logged-in account is a branch (master_users_tbl type 9). */
  private function is_branch_user()
  {
    return $this->session->userdata('user_type') == 9;
  }

  /**
   * The "Head Office" entry a branch sees in its supplier picker.
   *
   * Shaped like a master_users_tbl row so every existing supplier dropdown,
   * datalist and JSON list renders it without a special case. Its id is 0 - the
   * same branch = 0 sentinel used throughout - and the balance shown is what
   * this branch currently owes Head Office, which is the figure a user picking
   * it wants to see.
   */
  private function head_office_party()
  {
    $balance = 0;
    $branch  = (int) $this->session->userdata('user_id');
    if ($branch > 0) {
      $row = $this->db->select('m_user_balance')
        ->where('m_user_id', $branch)
        ->where('m_user_type', 9)
        ->get('master_users_tbl')->row();
      $balance = !empty($row) ? (float) $row->m_user_balance : 0;
    }

    return (object) array(
      'm_user_id'      => 0,
      'm_user_name'    => 'Head Office',
      'm_user_mobile'  => '',
      'm_user_balance' => $balance,
      'm_user_group'   => '',
      'm_user_type'    => 2,
      'm_city_name'    => '',
      'm_state_name'   => '',
    );
  }

  public function get_active_users($type, $branch_id = null)
  {
    if (!empty($type)) {
      $this->db->where('m_user_type', $type);
    }
    $this->db->where('m_user_status', 1);
    $this->where_branch('m_user_branch', $branch_id);
    return $this->db->select('m_user_id,m_user_name,m_user_mobile,m_user_group,m_user_type')
      ->get('master_users_tbl')->result();
  }

  /**
   * A branch account's own row, looked up WITHOUT branch scoping.
   *
   * Branch accounts define the branches themselves, so they all sit on
   * m_user_branch = 0 (Head Office) rather than on their own id - the same
   * reason items and item groups are treated as shared. Fetching one through
   * the scoped get_user_dtl() therefore finds nothing whenever the caller IS
   * that branch: branch_id() filters on m_user_branch = <the branch's own
   * id>, which no row carries.
   */
  public function get_branch_dtl($id)
  {
    return $this->db->where('m_user_id', $id)
      ->where('m_user_type', 9)
      ->get('master_users_tbl')->row();
  }

  public function get_user_dtl($id, $branch_id = null)
  {
    $this->db->select('*');
    $this->db->where('m_user_id', $id);
    $this->where_branch('master_users_tbl.m_user_branch', $branch_id);
    $this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_users_tbl.m_user_state', 'left');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = master_users_tbl.m_user_city', 'left');
    return $this->db->get('master_users_tbl')->row();
  }

  // Superadmin-only "view login details" feature - caller must already have
  // checked user_type == 8 before calling this.
  public function get_user_login_credentials($id, $branch_id = null)
  {
    $this->where_branch('m_user_branch', $branch_id);
    return $this->db->select('m_user_loginid,m_user_password_enc')
      ->where('m_user_id', $id)
      ->get('master_users_tbl')->row();
  }

  public function get_user_group_dtl($group_id, $branch_id = null)
  {
    $this->db->select('*');
    $this->db->where('m_user_group', $group_id);
    $this->where_branch('master_users_tbl.m_user_branch', $branch_id);
    $this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_users_tbl.m_user_state', 'left');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = master_users_tbl.m_user_city', 'left');
    return $this->db->get('master_users_tbl')->row();
  }

  public function insert_user()
  {
    $userid = $this->input->post('m_user_id');

    $branch = $this->branch_id($this->input->post('m_user_branch'));

    // Login ids must be unique or the second account can never sign in:
    // validate_user() resolves a login id with ->row() and takes the first
    // match. Checked GLOBALLY (not per branch) because validate_user() does
    // not filter by branch either. Blank ids are skipped - most accounts have
    // none and they would all collide with each other.
    $loginid = trim((string) $this->input->post('m_user_loginid'));
    if ($loginid !== '') {
      $this->db->where('m_user_loginid', $loginid);
      if (!empty($userid)) {
        $this->db->where('m_user_id !=', $userid);
      }
      if ($this->db->get('master_users_tbl')->num_rows() > 0) {
        return 3;
      }
    }

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
      "m_user_name"         => $this->input->post('m_user_name'),
      "m_user_mobile"       => $this->input->post('m_user_mobile'),
      "m_user_remark"       => $this->input->post('m_user_remark'),
      "m_user_contractPerd" => $this->input->post('m_user_contractPerd'),
      "m_user_pan_no"       => $this->input->post('m_user_pan_no'),
      "m_user_accountno"    => $this->input->post('m_user_accountno'),
      "m_user_adharno"      => $this->input->post('m_user_adharno'),
      "m_user_state"        => $this->input->post('m_user_state'),
      "m_user_city"         => $this->input->post('m_user_city'),
      "m_user_address"      => $this->input->post('m_user_address'),
      "m_user_trademark"    => $this->input->post('m_user_trademark'),
      "m_user_status"       => 1,
      "m_user_group"        => $group,
      "m_user_type"         => $this->input->post('m_user_type'),
      "m_user_design"       => $this->input->post('m_user_design') ?: 0,
      "m_user_opening"      => $openingbal ?: 0,
      "m_user_crateOP"      => $cbv10 . ',' . $cbv20 . ',' . $cbv25,
      "m_user_login_allow"  => $this->input->post('m_user_login_allow') ?: 0,
      "m_user_loginid"      => $this->input->post('m_user_loginid') ?: '',
    );

    // Only touch the password if a new one was actually submitted - otherwise
    // an edit save would blank out (or re-hash) the existing credential.
    $newPassword = $this->input->post('m_user_password');
    if (!empty($newPassword)) {
      $data['m_user_password']     = password_hash($newPassword, PASSWORD_DEFAULT);
      $data['m_user_password_enc'] = encrypt_password_for_admin($newPassword);
    }

    if (!empty($userid)) {
      $data['m_user_updated_by'] = $this->session->userdata('user_id');
      $data['m_user_updated_on'] = date('Y-m-d H:i:s');
      $this->db->where('m_user_id', $userid);
      $this->where_branch('m_user_branch', $branch);
      $this->db->update('master_users_tbl', $this->no_nulls($data));
      return 2;
    } else {
      // Honour the branch the form actually selected. branch_id() already
      // resolves this per role (superadmin -> the posted override, everyone
      // else -> their own branch), so forcing 0 for superadmin here made the
      // Branch selector on add_user.php inert and orphaned every account it
      // created onto branch 0. Matches insert_cust/insert_item/insert_group.
      $data['m_user_branch']   = $branch ?? 0;
      $data['m_user_added_by'] = $this->session->userdata('user_id');
      $data['m_user_added_on'] = date('Y-m-d H:i:s');
      $this->db->insert('master_users_tbl', $this->no_nulls($data));
      return 1;
    }
  }

  public function delete_user($branch_id = null)
  {
    $this->db->where('m_user_id', $this->input->post('delete_id'));
    $this->where_branch('m_user_branch', $branch_id);
    $this->db->delete('master_users_tbl');

    // see delete_customer(): a delete that matched nothing is still a
    // successful query, so it has to be checked explicitly
    if ($this->db->affected_rows() < 1) {
      return $this->fail('That account was not found. It may already have been deleted, or it belongs to a different branch.');
    }
    return true;
  }

  // ===================== customers =======================//

  public function get_cust_list($from_date = '', $to_date = '', $city_dtl = '', $orderby = '', $group = '', $branch_id = null)
  {
    $this->where_branch('master_customer_tbl.m_cust_branch', $branch_id);

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
      } else if ($orderby == 3) {
        $this->db->order_by('m_city_name');
      } else {
        $this->db->order_by('m_city_name');
      }
    }
    return $this->db->get('master_customer_tbl')->result();
  }

  public function get_cust_active_list($cust_id = '', $branch_id = null)
  {
    $this->where_branch('m_cust_branch', $branch_id);
    if (!empty($cust_id)) {
      $this->db->where('m_cust_id', $cust_id);
    }
    return $this->db->select('m_cust_id,m_cust_name,m_cust_hndiname,m_cust_group,m_cust_mobile,m_cust_balance,m_cust_10bal,m_cust_20bal,m_cust_25bal')
      ->where('m_cust_status', 1)
      ->order_by('m_cust_name')
      ->get('master_customer_tbl')->result();
  }

  public function get_cust_dtl($id, $branch_id = null)
  {
    $this->db->select('*');
    $this->db->where('m_cust_id', $id);
    $this->where_branch('master_customer_tbl.m_cust_branch', $branch_id);
    $this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_customer_tbl.m_cust_state', 'left');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = master_customer_tbl.m_cust_city', 'left');
    $this->db->join('master_group_tbl', 'master_group_tbl.m_group_id = master_customer_tbl.m_cust_group', 'left');
    return $this->db->get('master_customer_tbl')->row();
  }

  public function get_all_customers($from_date = '', $to_date = '', $city_dtl = '', $search = '', $branch_id = null)
  {
    $this->db->select('m_cust_id,m_cust_name,m_cust_hndiname,m_cust_mobile,m_cust_opening,m_cust_crateOP,m_cust_image,m_cust_remark,m_cust_pan_no,m_cust_accountno,m_cust_balance,m_cust_10bal,m_cust_20bal,m_cust_25bal,m_state_name,m_city_name,m_cust_address,m_cust_adharno,m_cust_trademark,m_cust_contractPerd,m_cust_status,m_cust_added_on,m_group_name,m_cust_group');
    $this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = mct.m_cust_state', 'left');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = mct.m_cust_city', 'left');
    $this->db->join('master_group_tbl', 'master_group_tbl.m_group_id = mct.m_cust_group', 'left');

    $this->where_branch('mct.m_cust_branch', $branch_id);

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
      // Bound like() - see the note in get_user_list() (BUG-029).
      $this->db->group_start()
        ->like('m_cust_name', $search)
        ->or_like('m_cust_mobile', $search)
        ->or_like('m_group_name', $search)
        ->group_end();
    }

    $this->db->order_by('m_cust_name');
    return $this->db->get('master_customer_tbl mct')->result();
  }

  public function delete_customer($branch_id = null)
  {
    $delete_id = $this->input->post('delete_id');

    $this->db->where('m_cust_id', $delete_id);
    $this->where_branch('m_cust_branch', $branch_id);
    $this->db->delete('master_customer_tbl');

    // db->delete() reports success for a query that matched nothing, so a
    // stale id or another branch's customer used to come back as "Deleted
    // successfully" while the row was still there.
    if ($this->db->affected_rows() < 1) {
      return $this->fail('That customer was not found. They may already have been deleted, or they belong to a different branch.');
    }

    $this->db->where('m_recvd_customer', $delete_id);
    $this->where_branch('m_recvd_branch', $branch_id);
    $this->db->delete('master_recieved_tbl');

    $this->db->where('m_sale_customer', $delete_id);
    $this->where_branch('m_sale_branch', $branch_id);
    $this->db->delete('master_sales_tbl');

    return true;
  }

  public function get_customer_balance($cust_id, $to_date = '', $today = '', $branch_id = null)
  {
    $sub_total      = 0;
    $total_expense  = 0;
    $grand_total    = 0;
    $crate_total    = 0;
    $total_given    = 0;
    $total_recieved = 0;

    // Sales
    if ($today == 1) {
      if (!empty($to_date)) $this->db->where('m_sale_date', $to_date);
    } else {
      if (!empty($to_date)) $this->db->where('m_sale_date <=', $to_date);
    }
    $this->db->where('m_sale_customer', $cust_id);
    $this->where_branch('m_sale_branch', $branch_id);
    $salequery = $this->db->select('sum(m_sale_qty) as tqty,sum(m_sale_total) as sub_total,sum(m_sale_crate) as tcrate,(m_sale_comm+m_sale_fright+m_sale_hamali+m_sale_others) as texpense')
      ->group_by('m_sale_spo')
      ->get('master_sales_tbl')->result();

    if (!empty($salequery)) {
      foreach ($salequery as $key) {
        $sub_total     += $key->sub_total;
        $total_expense += $key->texpense;
        $grand_total   += ($key->sub_total + $key->texpense);
      }
    }

    // Received amounts
    if ($today == 1) {
      if (!empty($to_date)) $this->db->where('m_recvd_date', $to_date);
    } else {
      if (!empty($to_date)) $this->db->where('m_recvd_date <=', $to_date);
    }
    $this->db->where('m_recvd_customer', $cust_id)
      ->where('m_recvd_account', 1)
      ->where('m_recvd_type', 1);
    $this->where_branch('m_recvd_branch', $branch_id);
    $amountrcvdquery = $this->db->select('sum(m_recvd_amount) as tamountrcvd')
      ->get('master_recieved_tbl')->result();

    // Voucher credit
    if ($today == 1) {
      if (!empty($to_date)) $this->db->where('m_voucher_date', $to_date);
    } else {
      if (!empty($to_date)) $this->db->where('m_voucher_date <=', $to_date);
    }
    $this->db->where('m_voucher_accountid', $cust_id)
      ->where('m_voucher_account', 1)
      ->where('m_voucher_type', 1)
      ->where('m_voucher_status', 1);
    $this->where_branch('m_voucher_branch', $branch_id);
    $vouch_amtcdrt = $this->db->select('sum(m_voucher_amount) as tamountcdt')
      ->get('master_voucher_tbl')->result();

    // Voucher debit
    if ($today == 1) {
      if (!empty($to_date)) $this->db->where('m_voucher_date', $to_date);
    } else {
      if (!empty($to_date)) $this->db->where('m_voucher_date <=', $to_date);
    }
    $this->db->where('m_voucher_accountid', $cust_id)
      ->where('m_voucher_account', 1)
      ->where('m_voucher_type', 2)
      ->where('m_voucher_status', 1);
    $this->where_branch('m_voucher_branch', $branch_id);
    $vouch_amtdbt = $this->db->select('sum(m_voucher_amount) as tamountdbt')
      ->get('master_voucher_tbl')->result();

    $balance_amt = (($grand_total + $vouch_amtdbt[0]->tamountdbt) - ($amountrcvdquery[0]->tamountrcvd + $vouch_amtcdrt[0]->tamountcdt));

    $result = array(
      "sub_total"      => $sub_total,
      "total_expense"  => $total_expense,
      "grand_total"    => $grand_total,
      "amount_rcvd"    => $amountrcvdquery[0]->tamountrcvd ?: 0,
      "discount_amt"   => $vouch_amtcdrt[0]->tamountcdt ?: 0,
      "balance_amount" => $balance_amt,
    );

    $all_crates = $this->Master_model->all_itemgroup(3);
    foreach ($all_crates as $key) {
      $crateledger = $this->get_crate_ledger($key->m_itgrp_id, $cust_id, $to_date, $today, $branch_id);
      $crate_total    += ((int) $crateledger['crate_given'] - (int) $crateledger['crate_rcvd']);
      $total_given    += (int) $crateledger['crate_given'];
      $total_recieved += (int) $crateledger['crate_rcvd'];

      $result['crateitems'][] = array(
        'name'    => $key->m_itgrp_title,
        'recived' => (int) $crateledger['crate_rcvd'],
        'given'   => (int) $crateledger['crate_given'],
        'balance' => ((int) $crateledger['crate_given']) - (int) $crateledger['crate_rcvd'],
      );
    }

    $result['crate_given']     = $total_given;
    $result['crate_recieved']  = $total_recieved;
    $result['balance_crate']   = $crate_total;

    return $result;
  }

  function get_crate_balance($cust_id, $branch_id = null)
  {
    $this->db->select('m_cust_opening,m_cust_crateOP')
      ->where('m_cust_id', $cust_id);
    $this->where_branch('m_cust_branch', $branch_id);
    $opening_bal = $this->db->get('master_customer_tbl')->row();

    $all_crates        = $this->Master_model->all_itemgroup(3);
    $openin_crate_bal  = explode(',', $opening_bal->m_cust_crateOP);
    $result            = [];

    foreach ($all_crates as $itect) {
      $crateledger = $this->get_crate_ledger($itect->m_itgrp_id, $cust_id, '', '', $branch_id);

      if ($itect->m_itgrp_title == '10 KG') {
        $crattype_bal = isset($openin_crate_bal[0]) ? $openin_crate_bal[0] : 0;
      } else if ($itect->m_itgrp_title == '20 KG') {
        $crattype_bal = isset($openin_crate_bal[1]) ? $openin_crate_bal[1] : 0;
      } else if ($itect->m_itgrp_title == '25 KG') {
        $crattype_bal = isset($openin_crate_bal[2]) ? $openin_crate_bal[2] : 0;
      } else {
        $crattype_bal = 0;
      }

      $result[] = array(
        'name'    => $itect->m_itgrp_title,
        'recived' => (int) $crateledger['crate_rcvd'],
        'given'   => (int) $crateledger['crate_given'],
        'balance' => $crattype_bal + (int) $crateledger['crate_given'] - (int) $crateledger['crate_rcvd'],
      );
    }
    return $result;
  }

  /**
   * Every crate type's ledger for one customer in two queries instead of two
   * per crate type. Same SQL as get_crate_ledger(), only grouped by crate
   * rather than filtered to a single one, so the totals are identical.
   *
   * Returns array(crate_id => array('crate_given' => n, 'crate_rcvd' => n)).
   */
  public function get_crate_ledger_all($cust_id, $from_date = '', $today = '', $branch_id = null)
  {
    $out = array();

    if ($today == 1) {
      if (!empty($from_date)) $this->db->where('m_sale_date', $from_date);
    } else {
      if (!empty($from_date)) $this->db->where('m_sale_date <=', $from_date);
    }
    $this->db->select('sum(m_sale_crate) as tcrate, m_item_crate')
      ->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
      ->where('m_sale_customer', $cust_id);
    $this->where_branch('master_sales_tbl.m_sale_branch', $branch_id);
    foreach ($this->db->group_by('m_item_crate')->get('master_sales_tbl')->result() as $r) {
      $out[$r->m_item_crate]['crate_given'] = $r->tcrate;
    }

    if ($today == 1) {
      if (!empty($from_date)) $this->db->where('m_recvd_date', $from_date);
    } else {
      if (!empty($from_date)) $this->db->where('m_recvd_date <=', $from_date);
    }
    $this->db->select('sum(m_recvd_qty) as tcrateqty, m_recvd_crate')
      ->where('m_recvd_customer', $cust_id)
      ->where('m_recvd_type', 2);
    $this->where_branch('master_recieved_tbl.m_recvd_branch', $branch_id);
    foreach ($this->db->group_by('m_recvd_crate')->get('master_recieved_tbl')->result() as $r) {
      $out[$r->m_recvd_crate]['crate_rcvd'] = $r->tcrateqty;
    }

    return $out;
  }

  public function get_crate_ledger($crate_id, $cust_id, $from_date = '', $today = '', $branch_id = null)
  {
    // Crate given (via sales)
    if ($today == 1) {
      if (!empty($from_date)) $this->db->where('m_sale_date', $from_date);
    } else {
      if (!empty($from_date)) $this->db->where('m_sale_date <=', $from_date);
    }
    $this->db->select('sum(m_sale_crate) as tcrate,m_itgrp_title')
      ->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
      ->where('m_sale_customer', $cust_id)
      ->where('m_item_crate', $crate_id);
    $this->where_branch('master_sales_tbl.m_sale_branch', $branch_id);
    $crategiven = $this->db->group_by('m_item_crate')->get('master_sales_tbl')->result();

    // Crate received
    if ($today == 1) {
      if (!empty($from_date)) $this->db->where('m_recvd_date', $from_date);
    } else {
      if (!empty($from_date)) $this->db->where('m_recvd_date <=', $from_date);
    }
    $this->db->select('sum(m_recvd_qty) as tcrateqty,m_itgrp_title')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left')
      ->where('m_recvd_customer', $cust_id)
      ->where('m_recvd_type', 2)
      ->where('m_recvd_crate', $crate_id);
    $this->where_branch('master_recieved_tbl.m_recvd_branch', $branch_id);
    $cratercvdquery = $this->db->group_by('m_recvd_crate')->get('master_recieved_tbl')->result();

    return array(
      "crate_rcvd"    => $cratercvdquery ? $cratercvdquery[0]->tcrateqty : 0,
      "crate_given"   => $crategiven ? $crategiven[0]->tcrate : 0,
      "crate_balance" => (($crategiven ? $crategiven[0]->tcrate : 0) - ($cratercvdquery ? $cratercvdquery[0]->tcrateqty : 0)),
    );
  }

  public function get_opening_balance($cust_id, $from_date, $branch_id = null)
  {
    $opening_bal = $this->get_cust_dtl($cust_id, $branch_id);

    $sub_total      = 0;
    $total_expense  = 0;
    $grand_total    = 0;
    $crate_total    = 0;
    $total_given    = 0;
    $total_recieved = 0;

    if (!empty($from_date)) $this->db->where('m_sale_date <=', $from_date);
    $this->db->where('m_sale_customer', $cust_id);
    $this->where_branch('m_sale_branch', $branch_id);
    $salequery = $this->db->select('sum(m_sale_total) as sub_total,(m_sale_comm+m_sale_fright+m_sale_hamali+m_sale_others) as texpense')
      ->group_by('m_sale_spo')->get('master_sales_tbl')->result();
    if (!empty($salequery)) {
      foreach ($salequery as $key) {
        $sub_total     += $key->sub_total;
        $total_expense += $key->texpense;
        $grand_total   += ($key->sub_total + $key->texpense);
      }
    }

    if (!empty($from_date)) $this->db->where('m_recvd_date <=', $from_date);
    $this->db->where('m_recvd_customer', $cust_id)
      ->where('m_recvd_account', 1)
      ->where('m_recvd_type', 1);
    $this->where_branch('m_recvd_branch', $branch_id);
    $amountrcvdquery = $this->db->select('sum(m_recvd_amount) as tamountrcvd')
      ->get('master_recieved_tbl')->result();

    if (!empty($from_date)) $this->db->where('m_voucher_date <=', $from_date);
    $this->db->where('m_voucher_accountid', $cust_id)
      ->where('m_voucher_account', 1)
      ->where('m_voucher_type', 1)
      ->where('m_voucher_status', 1);
    $this->where_branch('m_voucher_branch', $branch_id);
    $vouch_amtcdrt = $this->db->select('sum(m_voucher_amount) as tamountcdt')
      ->get('master_voucher_tbl')->result();

    if (!empty($from_date)) $this->db->where('m_voucher_date <=', $from_date);
    $this->db->where('m_voucher_accountid', $cust_id)
      ->where('m_voucher_account', 1)
      ->where('m_voucher_type', 2)
      ->where('m_voucher_status', 1);
    $this->where_branch('m_voucher_branch', $branch_id);
    $vouch_amtdbt = $this->db->select('sum(m_voucher_amount) as tamountdbt')
      ->get('master_voucher_tbl')->result();

    $balance_amt = $opening_bal->m_cust_opening + (($grand_total + $vouch_amtdbt[0]->tamountdbt) - ($amountrcvdquery[0]->tamountrcvd + $vouch_amtcdrt[0]->tamountcdt));

    $result = array(
      "cust_name"      => $opening_bal->m_cust_name,
      "m_cust_hndiname" => $opening_bal->m_cust_hndiname,
      "cust_mobile"    => $opening_bal->m_cust_mobile,
      "sub_total"      => $sub_total,
      "total_expense"  => $total_expense,
      "grand_total"    => $grand_total,
      "amount_rcvd"    => $amountrcvdquery[0]->tamountrcvd ?: 0,
      "balance_amount" => $balance_amt,
    );

    // The crate-type list is identical for every call; re-querying it per
    // customer cost one query each on a report that loops all 534 of them.
    if ($this->crate_types_cache === null) {
      $this->crate_types_cache = $this->Master_model->all_itemgroup(3);
    }
    $all_crates       = $this->crate_types_cache;
    $openin_crate_bal = explode(',', $opening_bal->m_cust_crateOP);
    $crate_ledgers    = $this->get_crate_ledger_all($cust_id, $from_date, '', $branch_id);
    foreach ($all_crates as $key) {
      $crateledger = array(
        'crate_given' => $crate_ledgers[$key->m_itgrp_id]['crate_given'] ?? 0,
        'crate_rcvd'  => $crate_ledgers[$key->m_itgrp_id]['crate_rcvd'] ?? 0,
      );
      $crate_total    += ((int) $crateledger['crate_given'] - (int) $crateledger['crate_rcvd']);
      $total_given    += (int) $crateledger['crate_given'];
      $total_recieved += (int) $crateledger['crate_rcvd'];

      if ($key->m_itgrp_title == '10 KG') {
        $crattype_bal = isset($openin_crate_bal[0]) ? $openin_crate_bal[0] : 0;
      } else if ($key->m_itgrp_title == '20 KG') {
        $crattype_bal = isset($openin_crate_bal[1]) ? $openin_crate_bal[1] : 0;
      } else if ($key->m_itgrp_title == '25 KG') {
        $crattype_bal = isset($openin_crate_bal[2]) ? $openin_crate_bal[2] : 0;
      } else {
        $crattype_bal = 0;
      }

      $result['crateitems'][] = array(
        'name'    => $key->m_itgrp_title,
        'recived' => (int) $crateledger['crate_rcvd'],
        'given'   => (int) $crateledger['crate_given'],
        'balance' => ((int) $crattype_bal + (int) $crateledger['crate_given'] - (int) $crateledger['crate_rcvd']),
      );
    }

    $result['crate_given']    = $total_given;
    $result['crate_recieved'] = $total_recieved;
    $result['balance_crate']  = array_sum(explode(',', $opening_bal->m_cust_crateOP)) + $crate_total;

    return $result;
  }

  public function insert_cust()
  {
    // Browser-only validation until now: a direct post created blank
    // customers and unlimited exact duplicates.
    $cust_id   = $this->input->post('m_cust_id');
    $cust_name = trim((string) $this->input->post('m_cust_name'));
    $cust_mob  = trim((string) $this->input->post('m_cust_mobile'));

    if ($cust_name === '') {
      return $this->fail('Enter a customer name before saving.');
    }

    if (empty($cust_id)) {
      $this->db->where('m_cust_name', $cust_name);
      $this->db->where('m_cust_mobile', $cust_mob);
      $this->where_branch('m_cust_branch', $this->input->post('m_cust_branch'));
      if ($this->db->count_all_results('master_customer_tbl') > 0) {
        return $this->fail('A customer named "' . $cust_name . '" with that mobile number already exists. Edit the existing record instead.');
      }
    }

    $custid = $this->input->post('m_cust_id');
    $branch = $this->branch_id($this->input->post('m_cust_branch'));

    // Only a real login id can collide. Most customers have none, and the
    // blank ones all share '' - running this check unconditionally matched
    // those and refused every new customer that had no login id at all.
    $loginid = trim((string) $this->input->post('m_cust_loginid'));
    if ($loginid !== '') {
      $this->db->where('m_cust_loginid', $loginid)
        ->where('m_cust_id !=', $custid);
      $this->where_branch('m_cust_branch', $branch);
      $check = $this->db->get('master_customer_tbl')->num_rows();
      if ($check > 0) return 3;
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
      "m_cust_name"         => $this->input->post('m_cust_name'),
      "m_cust_hndiname"     => $this->input->post('m_cust_hndiname'),
      "m_cust_mobile"       => $this->input->post('m_cust_mobile'),
      "m_cust_remark"       => $this->input->post('m_cust_remark'),
      "m_cust_contractPerd" => $this->input->post('m_cust_contractPerd'),
      "m_cust_accountno"    => $this->input->post('m_cust_accountno'),
      "m_cust_state"        => $this->input->post('m_cust_state'),
      "m_cust_city"         => $this->input->post('m_cust_city'),
      "m_cust_address"      => $this->input->post('m_cust_address'),
      "m_cust_trademark"    => $this->input->post('m_cust_trademark'),
      "m_cust_group"        => $this->input->post('m_cust_group'),
      "m_cust_loginid"      => $this->input->post('m_cust_loginid'),
      "m_cust_opening"      => $openingbal,
      "m_cust_crateOP"      => $cbv10 . ',' . $cbv20 . ',' . $cbv25,
      "m_cust_status"       => 1,
    );

    // Only touch the password if a new one was actually submitted - otherwise
    // an edit save would blank out (or re-hash) the existing credential.
    $newPassword = $this->input->post('m_cust_password');
    if (!empty($newPassword)) {
      $data['m_cust_password'] = password_hash($newPassword, PASSWORD_DEFAULT);
    }

    if (!empty($custid)) {
      $data['m_cust_updated_by'] = $this->session->userdata('user_id');
      $data['m_cust_updated_on'] = date('Y-m-d H:i:s');
      $this->db->where('m_cust_id', $custid);
      $this->where_branch('m_cust_branch', $branch);
      $this->db->update('master_customer_tbl', $this->no_nulls($data));
      return 2;
    } else {
      $data['m_cust_branch']   = $branch ?? 0;
      $data['m_cust_added_by'] = $this->session->userdata('user_id');
      $data['m_cust_added_on'] = date('Y-m-d H:i:s');
      $this->db->insert('master_customer_tbl', $this->no_nulls($data));
      return 1;
    }
  }

  // ===================== customer_group =======================//

  public function get_customer_group_list($group = '', $branch_id = null)
  {
    $this->where_branch('master_customer_tbl.m_cust_branch', $branch_id);
    if (!empty($group)) {
      $this->db->where('m_cust_group', $group);
    }
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = master_customer_tbl.m_cust_city', 'left');
    $this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_customer_tbl.m_cust_state', 'left');
    $this->db->join('master_group_tbl', 'master_group_tbl.m_group_id = master_customer_tbl.m_cust_group');
    return $this->db->order_by('m_cust_name')->get('master_customer_tbl')->result();
  }

  // ===================== custgrp =======================//

  public function all_custgrp($branch_id = null)
  {
    $this->db->select('*')
      ->join('master_users_tbl mut', 'mut.m_user_id = master_custgroup_tbl.m_custgrp_user', 'left')
      ->join('master_customer_tbl mct', 'mct.m_cust_id = master_custgroup_tbl.m_custgrp_customer', 'left');
    $this->where_branch('master_custgroup_tbl.m_custgrp_branch', $branch_id);
    return $this->db->order_by('m_custgrp_name')->get('master_custgroup_tbl')->result();
  }

  public function insert_custgrp()
  {
    $custgrp_id       = $this->input->post('m_custgrp_id');
    $custgrp_customer = $this->input->post('m_custgrp_customer');
    $branch           = $this->branch_id($this->input->post('m_custgrp_branch'));
    $res              = 0;

    foreach ($custgrp_customer as $key => $cau) {
      $this->db->where('m_custgrp_user', $this->input->post('m_custgrp_user'))
        ->where('m_custgrp_customer', $cau)
        ->where('m_custgrp_name', $this->input->post('m_custgrp_name'));
      $this->where_branch('m_custgrp_branch', $branch);
      $check = $this->db->get('master_custgroup_tbl')->result();

      $insert_data = array(
        "m_custgrp_status"   => 1,
        "m_custgrp_name"     => $this->input->post('m_custgrp_name'),
        "m_custgrp_user"     => $this->input->post('m_custgrp_user'),
        "m_custgrp_customer" => $cau,
      );

      if (!empty($custgrp_id[$key])) {
        $this->db->where('m_custgrp_id', $custgrp_id[$key]);
        $this->where_branch('m_custgrp_branch', $branch);
        $this->db->update('master_custgroup_tbl', $this->no_nulls($insert_data));
        $res = 2;
      } else {
        if (empty($check)) {
          $insert_data['m_custgrp_branch']   = $branch ?? 0;
          $insert_data['m_custgrp_addedby']  = $this->session->userdata('user_id');
          $insert_data['m_custgrp_code']     = date('dmi') . $this->input->post('m_custgrp_user');
          $insert_data['m_custgrp_added_on'] = date('Y-m-d H:i:s');
          $this->db->insert('master_custgroup_tbl', $this->no_nulls($insert_data));
          $res = 1;
        }
      }
    }
    return $res;
  }

  public function get_edit_custgrp($id, $branch_id = null)
  {
    $this->db->select('*')
      ->join('master_users_tbl mut', 'mut.m_user_id = master_custgroup_tbl.m_custgrp_user', 'left')
      ->join('master_customer_tbl mct', 'mct.m_cust_id = master_custgroup_tbl.m_custgrp_customer', 'left')
      ->where('m_custgrp_id', $id);
    $this->where_branch('master_custgroup_tbl.m_custgrp_branch', $branch_id);
    return $this->db->get('master_custgroup_tbl')->row();
  }

  public function delete_custgrp($branch_id = null)
  {
    $this->db->where('m_custgrp_id', $this->input->post('delete_id'));
    $this->where_branch('m_custgrp_branch', $branch_id);
    $this->db->delete('master_custgroup_tbl');

    if ($this->db->affected_rows() < 1) {
      return $this->fail('That customer group was not found. It may already have been deleted, or it belongs to a different branch.');
    }
    return true;
  }

  // ===================== item_issue =======================//

  public function get_edit_item_issue($id = '', $lot_no = '', $type = '', $branch_id = null)
  {
    $this->db->select('staff_itemissue_tbl.*,m_item_name,group.m_itgrp_title as groupname,crate.m_itgrp_title as cratetype,si_issue_user,unit.m_itgrp_title as unitname,m_user_name,m_user_mobile,(select m_purcs_lot from master_purchase_tbl where si_issue_lotno = m_purcs_id) as pur_lotno,(select m_purcs_available from master_purchase_tbl where si_issue_lotno = m_purcs_id) as available_stock');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = staff_itemissue_tbl.si_issue_item', 'left')
      ->join('master_users_tbl mut', 'mut.m_user_id = staff_itemissue_tbl.si_issue_user', 'left')
      ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
      ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');
    $this->where_branch('staff_itemissue_tbl.si_issue_branch', $branch_id);
    if (!empty($lot_no))  $this->db->where('si_issue_lotno', $lot_no);
    if (!empty($type))    $this->db->where('si_issue_type', $type);
    if (!empty($id))      $this->db->where('si_issue_spo', $id);
    $this->db->where('si_issue_status', 1);
    $this->db->order_by('m_item_name');
    return $this->db->get('staff_itemissue_tbl')->result();
  }

  public function issue_item_group($from_date = '', $todate = '', $staff = '', $lot_no = '', $branch_id = null)
  {
    $this->where_branch('staff_itemissue_tbl.si_issue_branch', $branch_id);
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = staff_itemissue_tbl.si_issue_user', 'left')
      ->join('master_city_tbl', 'master_city_tbl.m_city_id = mut.m_user_city', 'left')
      ->join('master_state_tbl', 'master_state_tbl.m_state_id = mut.m_user_state', 'left')
      ->join('master_group_tbl', 'master_group_tbl.m_group_id = mut.m_user_group', 'left')
      ->join('master_users_tbl as issueby', 'issueby.m_user_id = staff_itemissue_tbl.si_issue_added_by', 'left');

    if (!empty($from_date)) $this->db->where('si_issue_date>=', $from_date);
    if (!empty($todate))    $this->db->where('si_issue_date<=', $todate);
    if (!empty($staff))     $this->db->where_in('si_issue_user', $staff);

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
    $post         = $this->input->post();
    $branch       = $this->branch_id($post['si_issue_branch'] ?? null);

    if ($this->date_is_locked($post['si_issue_date'] ?? null)) {
      return ['status' => 'error', 'message' => 'This date falls in a locked financial year.'];
    }

    $issue_id     = $post['si_issue_id'] ?? [];
    $issue_item   = $post['si_issue_item'] ?? [];
    $pre_qty      = $post['pre_item_qty'] ?? [];
    $issue_qty    = $post['si_issue_qty'] ?? [];
    $issue_weight = $post['si_issue_weight'] ?? [];
    $issue_crate  = $post['si_issue_crate'] ?? [];
    $issue_price  = $post['si_issue_price'] ?? [];
    $issue_total  = $post['si_issue_total'] ?? [];
    $issue_lotno  = $post['si_issue_lotno'] ?? [];

    $this->db->trans_start();

    // SPO generation: highest counter ever issued (locked FOR UPDATE), not the
    // last inserted row - editing an older issue can leave a stale spo on the
    // newest row and cause the next issue number to be reused.
    if (empty($post['si_issue_spo'])) {
      $where_parts = ['si_issue_type = 1'];
      $binds       = [];
      if ($branch !== null) {
        $where_parts[] = 'si_issue_branch = ?';
        $binds[] = (int) $branch;
      }
      $where_sql = 'WHERE ' . implode(' AND ', $where_parts);

      $maxSpo = $this->db->query(
        "SELECT MAX(CAST(SUBSTRING_INDEX(si_issue_spo, '/', 1) AS UNSIGNED)) AS max_counter FROM staff_itemissue_tbl {$where_sql} FOR UPDATE",
        $binds
      )->row();

      $next_counter = (!empty($maxSpo) && $maxSpo->max_counter !== null) ? ((int) $maxSpo->max_counter + 1) : 1;
      $issue_spo    = $next_counter . '/' . date('dm', strtotime($post['si_issue_date'] ?? ''));
    } else {
      $issue_spo = ($post['si_issue_spo'] ?? '');
    }

    foreach ($issue_item as $key => $item) {
      $qty = (float) ($issue_qty[$key] ?? 0);
      $pre = (float) ($pre_qty[$key] ?? 0);
      $lot = $issue_lotno[$key] ?? null;

      $available_qty = $this->get_lot_available_qty($item, $lot, $branch);
      $check_qty     = !empty($issue_id[$key]) ? ($qty - $pre) : $qty;

      if ($check_qty > $available_qty) {
        $this->db->trans_rollback();
        return ['status' => false, 'msg' => "Insufficient stock for item {$item} (Lot: {$lot})"];
      }

      $data = [
        "si_issue_date"    => ($post['si_issue_date'] ?? ''),
        "si_issue_trackno" => ($post['si_issue_trackno'] ?? ''),
        "si_issue_type"    => ($post['si_issue_type'] ?? ''),
        "si_issue_user"    => ($post['si_issue_user'] ?? ''),
        "si_issue_item"    => $item,
        "si_issue_qty"     => $qty,
        "si_issue_lotno"   => $lot,
        "si_issue_weight"  => $issue_weight[$key] ?? 0,
        "si_issue_crate"   => $issue_crate[$key] ?? 0,
        "si_issue_price"   => $issue_price[$key] ?? 0,
        "si_issue_total"   => $issue_total[$key] ?? 0,
      ];

      if (!empty($issue_id[$key])) {
        $this->db->where('si_issue_id', $issue_id[$key]);
        $this->where_branch('si_issue_branch', $branch);
        $this->db->update('staff_itemissue_tbl', $this->no_nulls($data));
        $this->update_cust_balance(null, null, ($qty - $pre), $item, $lot);
        $res = 2;
      } else {
        $data['si_issue_branch']   = $branch ?? 0;
        $data['si_issue_spo']      = $issue_spo;
        $data['si_issue_status']   = 1;
        $data['si_issue_added_by'] = $this->session->userdata('user_id');
        $data['si_issue_added_on'] = date('Y-m-d H:i');
        $this->db->insert('staff_itemissue_tbl', $this->no_nulls($data));
        $this->update_cust_balance(null, null, $qty, $item, $lot);
        $res = 1;
      }
    }

    $this->db->trans_complete();
    return $res ?? 0;
  }

  public function lotwise_insert_issue()
  {
    $this->db->trans_start();

    $branch       = $this->branch_id($this->input->post('si_issue_branch'));
    $issue_date   = $this->input->post('si_issue_date');
    $issue_crate  = $this->input->post('si_issue_crate');
    $issue_item   = $this->input->post('si_issue_item');
    $issue_user   = $this->input->post('si_issue_user');
    $issue_qty    = $this->input->post('si_issue_qty');
    $issue_weight = $this->input->post('si_issue_weight');
    $issue_price  = $this->input->post('si_issue_price');
    $issue_lotno  = $this->input->post('si_issue_lotno');
    $issue_total  = $this->input->post('si_issue_total');

    // SPO generation: once per batch (not per item) so the whole lotwise issue
    // shares one bill number. Uses the highest counter ever issued (locked FOR
    // UPDATE), not the last inserted row - editing an older issue can leave a
    // stale spo on the newest row and cause the next issue number to be reused.
    $where_parts = ['si_issue_type = 1'];
    $binds       = [];
    if ($branch !== null) {
      $where_parts[] = 'si_issue_branch = ?';
      $binds[] = (int) $branch;
    }
    $where_sql = 'WHERE ' . implode(' AND ', $where_parts);

    $maxSpo = $this->db->query(
      "SELECT MAX(CAST(SUBSTRING_INDEX(si_issue_spo, '/', 1) AS UNSIGNED)) AS max_counter FROM staff_itemissue_tbl {$where_sql} FOR UPDATE",
      $binds
    )->row();

    $next_counter = (!empty($maxSpo) && $maxSpo->max_counter !== null) ? ((int) $maxSpo->max_counter + 1) : 1;
    $issue_spo    = $next_counter . '/' . date('dm', strtotime($issue_date[0]));

    foreach ($issue_user as $key => $cau) {
      $insert_data = array(
        "si_issue_date"     => $issue_date[$key],
        "si_issue_type"     => 1,
        "si_issue_user"     => $cau,
        "si_issue_item"     => $issue_item[$key],
        "si_issue_qty"      => $issue_qty[$key],
        "si_issue_lotno"    => $issue_lotno[$key],
        "si_issue_weight"   => $issue_weight[$key],
        "si_issue_crate"    => $issue_crate[$key],
        "si_issue_price"    => $issue_price[$key],
        "si_issue_total"    => $issue_total[$key],
        "si_issue_branch"   => $branch ?? 0,
        "si_issue_status"   => 1,
        "si_issue_added_by" => $this->session->userdata('user_id'),
        "si_issue_spo"      => $issue_spo,
        "si_issue_added_on" => date('Y-m-d H:i'),
      );

      $res = $this->db->insert('staff_itemissue_tbl', $this->no_nulls($insert_data));
      $this->update_cust_balance(null, null, $issue_qty[$key], $issue_item[$key], $issue_lotno[$key]);
    }

    $this->db->trans_complete();
    return $res;
  }

  public function delete_issue_item($branch_id = null)
  {
    $this->db->where('si_issue_spo', $this->input->post('delete_id'));
    $this->where_branch('si_issue_branch', $branch_id);
    $issue_datil = $this->db->get('staff_itemissue_tbl')->result();

    if (empty($issue_datil)) {
      return $this->fail('Issue slip "' . $this->input->post('delete_id') . '" was not found. It may already have been cancelled, or it belongs to a different branch.');
    }

    foreach ($issue_datil as $kry) {
      $this->update_cust_balance(null, null, ($kry->si_issue_qty * (-1)), $kry->si_issue_item, $kry->si_issue_lotno);
    }
    $this->db->set('si_issue_status', 0);
    $this->db->where('si_issue_spo', $this->input->post('delete_id'));
    $this->where_branch('si_issue_branch', $branch_id);
    return $this->db->update('staff_itemissue_tbl');
  }

  public function delete_issue_item_id($branch_id = null)
  {
    $this->db->where('si_issue_id', $this->input->post('delete_id'));
    $this->where_branch('si_issue_branch', $branch_id);
    $issue_datil = $this->db->get('staff_itemissue_tbl')->row();

    // reading ->si_issue_qty off a missing row raised a PHP notice and then
    // reported success without having reversed anything
    if (empty($issue_datil)) {
      return $this->fail('That issue line was not found. It may already have been cancelled, or it belongs to a different branch.');
    }

    $this->update_cust_balance(null, null, ($issue_datil->si_issue_qty * (-1)), $issue_datil->si_issue_item, $issue_datil->si_issue_lotno);
    $this->db->set('si_issue_status', 0);
    $this->db->where('si_issue_id', $this->input->post('delete_id'));
    $this->where_branch('si_issue_branch', $branch_id);
    return $this->db->update('staff_itemissue_tbl');
  }

  // ===================== sales =======================//

  /**
   * Batch version of get_edit_sales(): loads the lines for many invoices in one
   * query and returns them grouped by SPO.
   *
   * sales_list.php used to call get_edit_sales() inside its row loop, firing
   * one query per invoice - about 3,200 extra round trips on a one-month range,
   * which pushed the page past PHP's execution limit (BUG-006).
   */
  public function get_sales_lines_by_spo(array $spos, $branch_id = null)
  {
    if (empty($spos)) {
      return array();
    }

    $this->db->select('master_sales_tbl.*,m_item_name,m_item_fright,group.m_itgrp_title as groupname,crate.m_itgrp_title as cratetype,m_sale_customer,unit.m_itgrp_title as unitname,m_cust_name,m_cust_mobile,(select m_purcs_lot from master_purchase_tbl where m_sale_lot = m_purcs_id) as pur_lotno,(select m_purcs_available from master_purchase_tbl where m_sale_lot = m_purcs_id) as available_stock,m_user_name');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
      ->join('master_customer_tbl mut', 'mut.m_cust_id = master_sales_tbl.m_sale_customer', 'left')
      ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
      ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left')
      ->join('master_users_tbl', 'master_users_tbl.m_user_id = master_sales_tbl.m_sale_user', 'left');
    $this->where_branch('master_sales_tbl.m_sale_branch', $branch_id);
    $this->db->where_in('m_sale_spo', array_values(array_unique($spos)));
    $this->db->order_by('m_item_name');

    $grouped = array();
    foreach ($this->db->get('master_sales_tbl')->result() as $row) {
      $grouped[$row->m_sale_spo][] = $row;
    }

    return $grouped;
  }

  public function get_edit_sales($id = '', $lot_no = '', $branch_id = null)
  {
    $this->db->select('master_sales_tbl.*,m_item_name,m_item_fright,group.m_itgrp_title as groupname,crate.m_itgrp_title as cratetype,m_sale_customer,unit.m_itgrp_title as unitname,m_cust_name,m_cust_mobile,(select m_purcs_lot from master_purchase_tbl where m_sale_lot = m_purcs_id) as pur_lotno,(select m_purcs_available from master_purchase_tbl where m_sale_lot = m_purcs_id) as available_stock,m_user_name');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
      ->join('master_customer_tbl mut', 'mut.m_cust_id = master_sales_tbl.m_sale_customer', 'left')
      ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
      ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left')
      ->join('master_users_tbl', 'master_users_tbl.m_user_id = master_sales_tbl.m_sale_user', 'left');
    $this->where_branch('master_sales_tbl.m_sale_branch', $branch_id);
    if (!empty($lot_no)) $this->db->where('m_sale_lot', $lot_no);
    if (!empty($id))     $this->db->where('m_sale_spo', $id);
    $this->db->order_by('m_item_name');
    return $this->db->get('master_sales_tbl')->result();
  }

  public function sales_group($from_date = '', $todate = '', $customers = '', $group = '', $search_in = '', $order_by = '', $lot_no = '', $branch_id = null)
  {
    $this->where_branch('master_sales_tbl.m_sale_branch', $branch_id);

    if (!empty($from_date)) $this->db->where('m_sale_date>=', $from_date);
    if (!empty($todate))    $this->db->where('m_sale_date<=', $todate);
    if (!empty($search_in)) {
      // Bound like() - see the note in get_user_list() (BUG-029).
      $this->db->group_start()
        ->like('m_user_name', $search_in)
        ->or_like('m_user_mobile', $search_in)
        ->or_like('mut.m_cust_name', $search_in)
        ->or_like('mut.m_cust_mobile', $search_in)
        ->group_end();
    }
    if (!empty($group))     $this->db->where('m_cust_group', $group);
    if (!empty($customers)) $this->db->where('m_sale_customer', $customers);

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

    $this->db->order_by('m_sale_date', !empty($order_by) ? $order_by : 'desc');
    return $this->db->get('master_sales_tbl')->result();
  }

  public function insert_sales()
  {
    $post   = $this->input->post();
    $branch = $this->branch_id($post['m_sale_branch'] ?? null);

    $issue_id     = $post['m_sale_id'] ?? [];
    $sales        = $post['m_sale_item'] ?? [];
    $pre_qty      = $post['pre_item_qty'] ?? [];
    $issue_qty    = $post['m_sale_qty'] ?? [];
    $issue_weight = $post['m_sale_weight'] ?? [];
    $issue_crate  = $post['m_sale_crate'] ?? [];
    $issue_price  = $post['m_sale_price'] ?? [];
    $m_sale_lot   = $post['m_sale_lot'] ?? [];

    if ($this->date_is_locked($post['m_sale_date'] ?? null)) {
      return ['status' => 'error', 'message' => 'This date falls in a locked financial year.'];
    }

    $this->db->trans_start();

    // SPO generation
    if (empty($post['m_sale_spo'])) {
      // Use the highest counter ever issued (locked FOR UPDATE), not the last
      // inserted row - editing an older invoice can leave a stale spo on the
      // newest row and cause the next invoice number to be reused.
      $where_sql = $branch !== null ? 'WHERE m_sale_branch = ?' : '';
      $binds     = $branch !== null ? [(int) $branch] : [];

      $maxSpo = $this->db->query(
        "SELECT MAX(CAST(SUBSTRING_INDEX(m_sale_spo, '/', 1) AS UNSIGNED)) AS max_counter FROM master_sales_tbl {$where_sql} FOR UPDATE",
        $binds
      )->row();

      $next_counter = (!empty($maxSpo) && $maxSpo->max_counter !== null) ? ((int) $maxSpo->max_counter + 1) : 1;
      $sale_spo = $next_counter . '/' . date('dm', strtotime($post['m_sale_date'] ?? ''));
    } else {
      $sale_spo = ($post['m_sale_spo'] ?? '');
    }

    $saleTotalAmt = 0;
    $res = 0;
    foreach ($sales as $key => $item) {
      $qty    = (float) ($issue_qty[$key] ?? 0);
      $weight = (float) ($issue_weight[$key] ?? 0);
      $price  = (float) ($issue_price[$key] ?? 0);
      $lot    = $m_sale_lot[$key] ?? null;

      $available_qty = $this->get_lot_available_qty($item, $lot, $branch);

      if (empty($issue_id[$key]) && $qty > $available_qty) {
        $this->db->trans_rollback();
        return ['status' => 'error', 'message' => "Stock not available for item {$item} in lot {$lot}"];
      } else if ($qty > ((int)($pre_qty[$key] ?? 0) + (int)$available_qty)) {
        $this->db->trans_rollback();
        return ['status' => 'error', 'message' => "Stock not available for item {$item} in lot {$lot}"];
      }

      // m_sale_qty is an INT column, so a fractional quantity was silently
      // rounded on the way in while the line total was still computed from the
      // fraction - the saved row then did not multiply out (BUG-014). Reject it
      // rather than quietly changing either number.
      if ($qty != floor($qty)) {
        $this->db->trans_rollback();
        return [
          'status'  => 'error',
          'message' => "Quantity must be a whole number (got {$qty} for item {$item}).",
        ];
      }

      $sale_total = ($weight > 0) ? ($weight * $price) : ($qty * $price);

      // Duplicate check. This must run BEFORE $saleTotalAmt is accumulated:
      // a skipped line used to keep contributing to the invoice total, so the
      // customer was billed for a row that was never written (BUG-011).
      if (empty($issue_id[$key])) {
        $this->db->where([
          'm_sale_date'     => ($post['m_sale_date'] ?? ''),
          'm_sale_customer' => ($post['m_sale_customer'] ?? ''),
          'm_sale_item'     => $item,
          'm_sale_lot'      => $lot,
          'm_sale_qty'      => $qty,
          'm_sale_price'    => $price,
        ]);
        $this->where_branch('m_sale_branch', $branch);
        $exists = $this->db->get('master_sales_tbl')->row();
        if ($exists) continue;
      }

      $saleTotalAmt += $sale_total;

      $data = [
        "m_sale_date"     => ($post['m_sale_date'] ?? ''),
        "m_sale_trackno"  => ($post['m_sale_trackno'] ?? ''),
        "m_sale_customer" => ($post['m_sale_customer'] ?? ''),
        "m_sale_voucher"  => ($post['m_sale_voucher'] ?? ''),
        "m_sale_comrate"  => ($post['m_sale_comrate'] ?? ''),
        "m_sale_comm"     => ($post['m_sale_comm'] ?? ''),
        "m_sale_fright"   => ($post['m_sale_fright'] ?? ''),
        "m_sale_hamali"   => ($post['m_sale_hamali'] ?? ''),
        "m_sale_others"   => ($post['m_sale_others'] ?? ''),
        "m_sale_note"     => ($post['m_sale_note'] ?? ''),
        "m_sale_user"     => $post['m_sale_user'] ?? '',
        "m_sale_item"     => $item,
        "m_sale_qty"      => $qty,
        "m_sale_weight"   => $weight,
        "m_sale_crate"    => $issue_crate[$key] ?? 0,
        "m_sale_price"    => $price,
        "m_sale_total"    => $sale_total,
        "m_sale_lot"      => $lot,
      ];

      if (!empty($issue_id[$key])) {
        $data['m_sale_updatedby'] = $this->session->userdata('user_id');
        $data['m_sale_updatedon'] = date('Y-m-d H:i');
        $this->db->where('m_sale_id', $issue_id[$key]);
        $this->where_branch('m_sale_branch', $branch);
        $this->db->update('master_sales_tbl', $this->no_nulls($data));
        $new_qty = ((($pre_qty[$key] ?? 0) - $qty) * -1);
        if (($post['m_sale_customer'] ?? '') == ($post['precust'] ?? '')) {
          $this->update_cust_balance(($post['m_sale_customer'] ?? ''), null, $new_qty, $item, $lot);
        } else {
          $this->update_cust_balance(($post['m_sale_customer'] ?? ''), null, $qty, $item, $lot);
          $this->update_cust_balance(($post['precust'] ?? ''), null, -$pre_qty[$key], $item, $lot);
        }
        $res = 2;
      } else {
        $data['m_sale_branch']   = $branch ?? 0;
        $data['m_sale_spo']      = $sale_spo;
        $data['m_sale_added_by'] = $this->session->userdata('user_id');
        $data['m_sale_added_on'] = date('Y-m-d H:i');
        $this->db->insert('master_sales_tbl', $this->no_nulls($data));
        $this->update_cust_balance(($post['m_sale_customer'] ?? ''), null, $qty, $item, $lot);
        $res = 1;
      }
    }

    $extra = (float)($post['m_sale_comm'] ?? '') + (float)($post['m_sale_fright'] ?? '') + (float)($post['m_sale_hamali'] ?? '') + (float)($post['m_sale_others'] ?? '');
    $saleTotalAmt += $extra;

    if (empty($post['m_sale_spo'])) {
      $this->update_cust_balance(($post['m_sale_customer'] ?? ''), $saleTotalAmt);
    } else {
      $new_amt = $saleTotalAmt - (float)($post['pre_grand_total'] ?? '');
      if (($post['m_sale_customer'] ?? '') == ($post['precust'] ?? '')) {
        $this->update_cust_balance(($post['m_sale_customer'] ?? ''), $new_amt);
      } else {
        $this->update_cust_balance(($post['m_sale_customer'] ?? ''), $saleTotalAmt);
        $this->update_cust_balance(($post['precust'] ?? ''), -($post['pre_grand_total'] ?? ''));
      }
    }

    $this->db->trans_complete();
    return $res;
  }

  public function lotwise_insert_sales()
  {
    $branch        = $this->branch_id($this->input->post('m_sale_branch'));
    $sale_date     = $this->input->post('m_sale_date');
    $sale_crate    = $this->input->post('m_sale_crate');
    $sale_item     = $this->input->post('m_sale_item');
    $sale_customer = $this->input->post('m_sale_customer');
    $sale_qty      = $this->input->post('m_sale_qty');
    $sale_weight   = $this->input->post('m_sale_weight');
    $sale_price    = $this->input->post('m_sale_price');
    $sale_fright   = $this->input->post('m_sale_fright');
    $sale_note     = $this->input->post('m_sale_note');
    $sale_lot      = $this->input->post('m_sale_lot');
    $sale_user     = $this->input->post('m_sale_user');

    foreach ((array) $sale_date as $d) {
      if ($this->date_is_locked($d)) {
        return ['status' => 'error', 'message' => 'This date falls in a locked financial year.'];
      }
    }

    $this->db->trans_start();

    foreach ($sale_customer as $key => $cau) {
      // Availability guard, matching insert_sales(). Without it this bulk path
      // happily oversold a lot and drove m_purcs_available negative (BUG-012).
      $qty_wanted    = (float) ($sale_qty[$key] ?? 0);
      $available_qty = $this->get_lot_available_qty($sale_item[$key] ?? null, $sale_lot[$key] ?? null, $branch);

      if ($qty_wanted > $available_qty) {
        $this->db->trans_rollback();
        return [
          'status'  => 'error',
          'message' => "Stock not available for item {$sale_item[$key]} in lot {$sale_lot[$key]}",
        ];
      }

      // Highest counter ever issued, locked - the same rule insert_sales()
      // uses. The previous "last inserted row" logic could reuse a number
      // after an older invoice was edited.
      $where_sql = $branch !== null ? 'WHERE m_sale_branch = ?' : '';
      $binds     = $branch !== null ? [(int) $branch] : [];

      $maxSpo = $this->db->query(
        "SELECT MAX(CAST(SUBSTRING_INDEX(m_sale_spo, '/', 1) AS UNSIGNED)) AS max_counter FROM master_sales_tbl {$where_sql} FOR UPDATE",
        $binds
      )->row();

      $next_counter = (!empty($maxSpo) && $maxSpo->max_counter !== null) ? ((int) $maxSpo->max_counter + 1) : 1;
      $sale_spo = $next_counter . '/' . date('dm', strtotime($sale_date[$key]));

      $sale_total = (!empty($sale_weight[$key]) && $sale_weight[$key] != "0.00" && $sale_weight[$key] != "0")
        ? ($sale_weight[$key] * $sale_price[$key])
        : ($sale_qty[$key] * $sale_price[$key]);

      $insert_data = array(
        "m_sale_date"     => $sale_date[$key],
        "m_sale_customer" => $cau,
        "m_sale_fright"   => $sale_fright[$key],
        "m_sale_note"     => $sale_note[$key],
        "m_sale_user"     => $sale_user[$key] ?: '',
        "m_sale_item"     => $sale_item[$key],
        "m_sale_qty"      => $sale_qty[$key],
        "m_sale_weight"   => $sale_weight[$key],
        "m_sale_crate"    => $sale_crate[$key],
        "m_sale_price"    => $sale_price[$key],
        "m_sale_total"    => $sale_total,
        "m_sale_lot"      => $sale_lot[$key],
        "m_sale_branch"   => $branch ?? 0,
        "m_sale_added_by" => $this->session->userdata('user_id'),
        "m_sale_spo"      => $sale_spo,
        "m_sale_added_on" => date('Y-m-d H:i'),
      );

      $res = $this->db->insert('master_sales_tbl', $this->no_nulls($insert_data));
      $saleTotalAmt = ((float)$sale_total + (float)$sale_fright[$key]);
      $this->update_cust_balance($cau, $saleTotalAmt, $sale_qty[$key], $sale_item[$key], $sale_lot[$key]);
    }

    $this->db->trans_complete();
    return $res;
  }

  public function delete_sales($branch_id = null)
  {
    $this->db->where('m_sale_spo', $this->input->post('delete_id'));
    $this->where_branch('m_sale_branch', $branch_id);
    $sale_datil  = $this->db->get('master_sales_tbl')->result();

    // An unknown or foreign delete_id returns no rows; indexing [0] here used
    // to raise a PHP error while the caller still reported success (BUG-008,
    // BUG-013).
    if (empty($sale_datil)) {
      return $this->fail('Sale bill "' . $this->input->post('delete_id') . '" was not found. It may already have been deleted, or it belongs to a different branch.');
    }

    $pre_grandtotal = ($sale_datil[0]->m_sale_comm + $sale_datil[0]->m_sale_fright + $sale_datil[0]->m_sale_hamali + $sale_datil[0]->m_sale_others);
    foreach ($sale_datil as $kry) {
      $pre_grandtotal += $kry->m_sale_total;
      $this->update_cust_balance($kry->m_sale_customer, null, ($kry->m_sale_qty * (-1)), $kry->m_sale_item, $kry->m_sale_lot);
    }
    $this->update_cust_balance($sale_datil[0]->m_sale_customer, ($pre_grandtotal * (-1)));

    $this->db->where('m_sale_spo', $this->input->post('delete_id'));
    $this->where_branch('m_sale_branch', $branch_id);
    $this->db->delete('master_sales_tbl');
    return true;
  }

  public function delete_sales_id($branch_id = null)
  {
    $this->db->where('m_sale_id', $this->input->post('delete_id'));
    $this->where_branch('m_sale_branch', $branch_id);
    $sale_datil = $this->db->select('m_sale_qty,m_sale_lot,m_sale_item,m_sale_customer,m_sale_total')
      ->get('master_sales_tbl')->row();

    if (empty($sale_datil)) {
      return $this->fail('That sale line was not found. It may already have been deleted, or it belongs to a different branch.');
    }

    $this->update_cust_balance($sale_datil->m_sale_customer, ($sale_datil->m_sale_total * (-1)), ($sale_datil->m_sale_qty * (-1)), $sale_datil->m_sale_item, $sale_datil->m_sale_lot);
    $this->db->where('m_sale_id', $this->input->post('delete_id'));
    $this->where_branch('m_sale_branch', $branch_id);
    $this->db->delete('master_sales_tbl');
    return true;
  }

  // ===================== purchase =======================//


  public function get_purchase_expense($id, $branch_id = null)
  {
    $this->db->select('master_expenses_tbl.*,expense.m_group_name as expense_name');
    $this->db->join('master_group_tbl as expense', 'expense.m_group_id = master_expenses_tbl.m_exp_name', 'left');
    $this->db->where('m_exp_accno', $id);
    $this->where_branch('m_exp_branch', $branch_id);
    return $this->db->get('master_expenses_tbl')->result();
  }

  public function get_edit_purchase($id, $branch_id = null)
  {
    $this->db->select('master_purchase_tbl.*,m_item_name,group.m_itgrp_title as groupname,crate.m_itgrp_title as cratetype,m_purcs_suplier,unit.m_itgrp_title as unitname,m_user_name,m_user_mobile');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_purchase_tbl.m_purcs_item', 'left')
      ->join('master_users_tbl mut', 'mut.m_user_id = master_purchase_tbl.m_purcs_suplier', 'left')
      ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
      ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');
    $this->db->where('m_purcs_spo', $id);
    $this->where_branch('master_purchase_tbl.m_purcs_branch', $branch_id);
    $this->db->order_by('m_item_name');
    return $this->db->get('master_purchase_tbl')->result();
  }

  public function purchase_group($from_date = '', $todate = '', $supplier = '', $order_by = '', $branch_id = null,$type = null)
  {
    $this->where_branch('master_purchase_tbl.m_purcs_branch', $branch_id);
    if (!empty($from_date)) $this->db->where('m_purcs_date>=', $from_date);
    if (!empty($todate))    $this->db->where('m_purcs_date<=', $todate);
    if (!empty($supplier))  $this->db->where_in('m_purcs_suplier', $supplier);
    if (!empty($type))      $this->db->where('m_purcs_type', $type);

    $this->db->select('m_purcs_spo,m_purcs_billno,m_purcs_truckno,m_purcs_note,m_purcs_date,m_purcs_suplier,m_purcs_branch,m_purcs_type,mut.m_user_name as supplier_name,mut.m_user_mobile as supplier_mobile,sum(m_purcs_qty) as tqty,sum(m_purcs_weight) as twght,sum(m_purcs_crate) as tcrate,master_users_tbl.m_user_name,branchu.m_user_name as branch_name,sum(m_purcs_total) as total_amount,m_purcs_comm,m_purcs_comrate,m_purcs_fright,m_purcs_hamali,m_purcs_charity,m_purcs_packaging,m_purcs_loading,m_purcs_advance,m_purcs_others,(m_purcs_comm + m_purcs_fright + m_purcs_hamali + m_purcs_charity + m_purcs_packaging + m_purcs_loading + m_purcs_advance + m_purcs_others) as total_expense,mut.m_user_address as supplier_address,m_city_name,m_state_name');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_purchase_tbl.m_purcs_suplier', 'left')
      ->join('master_city_tbl', 'master_city_tbl.m_city_id = mut.m_user_city', 'left')
      ->join('master_state_tbl', 'master_state_tbl.m_state_id = mut.m_user_state', 'left')
      ->join('master_users_tbl', 'master_users_tbl.m_user_id = master_purchase_tbl.m_purcs_user', 'left')
      ->join('master_users_tbl branchu', 'branchu.m_user_id = master_purchase_tbl.m_purcs_branch', 'left');

    $this->db->order_by('m_purcs_date', !empty($order_by) ? $order_by : 'desc');
    $this->db->group_by('m_purcs_spo');
    $this->db->group_by('m_purcs_date');
    return $this->db->get('master_purchase_tbl')->result();
  }

  public function get_purchase_items($from_date = '', $todate = '', $supplier = '', $search_in = '', $branch_id = null)
  {
    $branch = $this->branch_id($branch_id);
    $branch_filter_mp  = $branch !== null ? 'mp.m_purcs_branch = ' . (int) $branch . ' AND' : '';
    $branch_filter_sub = $branch !== null ? ' AND %s = ' . (int) $branch . ' ' : ' ';

    $this->db->select("mp.*, mit.m_item_name, mut.m_user_name AS supplier_name, mut.m_user_mobile, COALESCE(ms.sold_qty, 0) AS sold_qty, COALESCE(si1.issued_qty, 0) AS issued_qty, COALESCE(si2.returned_qty, 0) AS returned_qty");
    $this->db->from('master_purchase_tbl mp');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = mp.m_purcs_item', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = mp.m_purcs_suplier', 'left');
    $this->db->join('(SELECT m_sale_lot, SUM(m_sale_qty) AS sold_qty FROM master_sales_tbl WHERE 1=1' . sprintf($branch_filter_sub, 'm_sale_branch') . 'GROUP BY m_sale_lot) ms', 'ms.m_sale_lot = mp.m_purcs_id', 'left');
    $this->db->join('(SELECT si_issue_lotno, SUM(si_issue_qty) AS issued_qty FROM staff_itemissue_tbl WHERE si_issue_type = 1 AND si_issue_status = 1' . sprintf($branch_filter_sub, 'si_issue_branch') . 'GROUP BY si_issue_lotno) si1', 'si1.si_issue_lotno = mp.m_purcs_id', 'left');
    $this->db->join('(SELECT si_issue_lotno, SUM(si_issue_qty) AS returned_qty FROM staff_itemissue_tbl WHERE si_issue_type = 2 AND si_issue_status = 1' . sprintf($branch_filter_sub, 'si_issue_branch') . 'GROUP BY si_issue_lotno) si2', 'si2.si_issue_lotno = mp.m_purcs_id', 'left');
    $this->where_branch('mp.m_purcs_branch', $branch_id);

    if (!empty($from_date)) $this->db->where('mp.m_purcs_date >=', $from_date . ' 00:00:00');
    if (!empty($todate))    $this->db->where('mp.m_purcs_date <=', $todate . ' 23:59:59');
    if (!empty($supplier))  $this->db->where_in('mp.m_purcs_suplier', $supplier);
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
    if ($this->date_is_locked($this->input->post('m_purcs_date'))) {
      return ['status' => 'error', 'message' => 'This date falls in a locked financial year.'];
    }

    $this->db->trans_start();

    $branch = $this->branch_id($this->input->post('m_purcs_branch'));

    $supp_tm = $this->db->select('m_user_trademark')->where('m_user_type', 2)->where('m_user_id', $this->input->post('m_purcs_suplier'))->get('master_users_tbl')->row();

    // the challan number is built from the supplier's trademark below; without
    // this the whole save died on a null property read
    if (empty($supp_tm)) {
      return $this->fail('Please choose a supplier. The one selected is not on the supplier list for this branch.');
    }

    $issue_id     = $this->input->post('m_purcs_id');
    $purchase     = $this->input->post('m_purcs_item');
    $issue_qty    = $this->input->post('m_purcs_qty');
    $pre_qty      = $this->input->post('pre_item_qty');
    $issue_weight = $this->input->post('m_purcs_weight');
    $issue_crate  = $this->input->post('m_purcs_crate');
    $issue_price  = $this->input->post('m_purcs_price');
    $m_purcs_total = $this->input->post('m_purcs_total');
    $m_purcs_lot  = $this->input->post('m_purcs_lot');

    // SPO generation: highest counter ever issued (locked FOR UPDATE), not the
    // last inserted row - editing an older purchase can leave a stale spo on
    // the newest row and cause the next invoice number to be reused. Restricted
    // to m_purcs_type = 1 because Transfer rows (type 2) share this table and
    // their spo format doesn't carry a numeric counter in this position.
    $where_parts = ['m_purcs_type = 1'];
    $binds       = [];
    if ($branch !== null) {
      $where_parts[] = 'm_purcs_branch = ?';
      $binds[] = (int) $branch;
    }
    $where_sql = 'WHERE ' . implode(' AND ', $where_parts);

    $maxSpo = $this->db->query(
      "SELECT MAX(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(m_purcs_spo, '/', 2), '/', -1) AS UNSIGNED)) AS max_counter FROM master_purchase_tbl {$where_sql} FOR UPDATE",
      $binds
    )->row();

    $next_counter = (!empty($maxSpo) && $maxSpo->max_counter !== null) ? ((int) $maxSpo->max_counter + 1) : 1;
    $purcs_spo    = $supp_tm->m_user_trademark . '/' . $next_counter . '/' . date('d/m', strtotime($this->input->post('m_purcs_date')));
    $res = 0;
    $purTotalAmt = 0;
    // A negative line saved happily and left m_purcs_available negative -
    // stock that cannot exist, feeding every stock report and lot balance
    // from then on. Reject the whole bill rather than storing part of it.
    foreach ($purchase as $chk_key => $chk_item) {
      $chk_qty    = (float) ($issue_qty[$chk_key] ?? 0);
      $chk_weight = (float) ($issue_weight[$chk_key] ?? 0);
      $chk_price  = (float) ($issue_price[$chk_key] ?? 0);
      if ($chk_qty < 0 || $chk_weight < 0 || $chk_price < 0) {
        $this->db->trans_rollback();
        return $this->fail('Quantity, weight and rate cannot be negative. Check the line for item ' . $chk_item . ' and save again.');
      }
      if ($chk_qty == 0) {
        $this->db->trans_rollback();
        return $this->fail('Enter a quantity greater than 0 for item ' . $chk_item . '.');
      }
    }

    foreach ($purchase as $key => $cau) {
      $insert_data = array(
        "m_purcs_date"      => $this->input->post('m_purcs_date'),
        "m_purcs_suplier"   => $this->input->post('m_purcs_suplier'),
        "m_purcs_billno"    => $this->input->post('m_purcs_billno'),
        "m_purcs_comrate"   => $this->input->post('m_purcs_comrate'),
        "m_purcs_comm"      => $this->input->post('m_purcs_comm'),
        "m_purcs_fright"    => $this->input->post('m_purcs_fright'),
        "m_purcs_hamali"    => $this->input->post('m_purcs_hamali'),
        "m_purcs_charity"   => $this->input->post('m_purcs_charity'),
        "m_purcs_packaging" => $this->input->post('m_purcs_packaging'),
        "m_purcs_loading"   => $this->input->post('m_purcs_loading'),
        "m_purcs_advance"   => $this->input->post('m_purcs_advance'),
        "m_purcs_others"    => $this->input->post('m_purcs_others'),
        "m_purcs_note"      => $this->input->post('m_purcs_note'),
        "m_purcs_truckno"   => $this->input->post('m_purcs_truckno'),
        "m_purcs_item"      => $cau,
        "m_purcs_qty"       => $issue_qty[$key],
        "m_purcs_weight"    => $issue_weight[$key],
        "m_purcs_crate"     => $issue_crate[$key],
        "m_purcs_price"     => $issue_price[$key],
        "m_purcs_total"     => $m_purcs_total[$key],
        "m_purcs_lot"       => $m_purcs_lot[$key],
        "m_purcs_available" => $issue_qty[$key],
      );
      $purTotalAmt += (float)$m_purcs_total[$key];

      if (!empty($issue_id[$key])) {
        $this->db->where('m_purcs_id', $issue_id[$key]);
        $this->where_branch('m_purcs_branch', $branch);
        $purase_dtl = $this->db->select('m_purcs_spo')->get('master_purchase_tbl')->row();
        if (empty($purase_dtl)) {
          return $this->fail('One of the purchase lines being edited no longer exists, or belongs to a different branch. Reload the purchase and try again.');
        }
        $purcs_spo  = $purase_dtl->m_purcs_spo;
        $this->db->where('m_purcs_id', $issue_id[$key]);
        $this->where_branch('m_purcs_branch', $branch);
        $this->db->update('master_purchase_tbl', $this->no_nulls($insert_data));
        $new_qty = $issue_qty[$key] - $pre_qty[$key];
        if ($this->input->post('m_purcs_suplier') == $this->input->post('precust')) {
          $this->update_userbalance($this->input->post('m_purcs_suplier'), null, $new_qty, $cau);
        } else {
          $this->update_userbalance($this->input->post('m_purcs_suplier'), null, $issue_qty[$key], $cau);
          $this->update_userbalance($this->input->post('precust'), null, ($pre_qty[$key] * (-1)), $cau);
        }
        $res = 2;
      } else {
        $insert_data['m_purcs_branch']   = $branch ?? 0;
        $insert_data['m_purcs_spo']      = !empty($this->input->post('m_purcs_spo')) ? $this->input->post('m_purcs_spo') : $purcs_spo;
        $insert_data['m_purcs_added_by'] = $this->session->userdata('user_id');
        $insert_data['m_purcs_added_on'] = date('Y-m-d H:i');
        $this->db->insert('master_purchase_tbl', $this->no_nulls($insert_data));
        $this->update_userbalance($this->input->post('m_purcs_suplier'), null, $issue_qty[$key], $cau);
        $res = 1;
      }
    }

    $purTotalAmt += ((float)$this->input->post('m_purcs_comm') + (float)$this->input->post('m_purcs_fright') + (float)$this->input->post('m_purcs_hamali') + (float)$this->input->post('m_purcs_charity') + (float)$this->input->post('m_purcs_packaging') + (float)$this->input->post('m_purcs_loading') + (float)$this->input->post('m_purcs_advance') + (float)$this->input->post('m_purcs_others'));

    if (empty($this->input->post('m_purcs_spo'))) {
      $this->update_userbalance($this->input->post('m_purcs_suplier'), $purTotalAmt);
    } else {
      $new_amt = ($purTotalAmt - (float)$this->input->post('pre_grand_total'));
      if ($this->input->post('m_purcs_suplier') == $this->input->post('precust')) {
        $this->update_userbalance($this->input->post('m_purcs_suplier'), $new_amt);
      } else {
        $this->update_userbalance($this->input->post('m_purcs_suplier'), $purTotalAmt);
        $this->update_userbalance($this->input->post('precust'), ((float)$this->input->post('pre_grand_total') * (-1)));
      }
    }

    // Expenses
    // A <select> with no <option>s posts nothing, so a branch with zero expense
    // accounts makes these null and the loop below fatals mid-save - after the
    // purchase rows and balance updates have already been written.
    $m_exp_id     = $this->input->post('m_exp_id') ?: array();
    $m_exp_name   = $this->input->post('m_exp_name') ?: array();
    $m_exp_amount = $this->input->post('m_exp_amount') ?: array();

    foreach ($m_exp_name as $cou => $kky) {
      if (isset($m_exp_amount[$cou]) && $m_exp_amount[$cou] != null && $m_exp_amount[$cou] != '' && $m_exp_amount[$cou] != 0) {
        $voucher_no   = $kky . '/' . $supp_tm->m_user_trademark . '/' . date('dms');
        $insertt_data = array(
          "m_exp_type"    => 1,
          "m_exp_name"    => $kky,
          "m_exp_amount"  => $m_exp_amount[$cou],
          "m_exp_accno"   => $purcs_spo,
          "m_exp_remark"  => "Purchase No =" . $purcs_spo,
          "m_exp_voucher" => $voucher_no,
          "m_exp_date"    => $this->input->post('m_purcs_date'),
          "m_exp_status"  => 1,
        );
        if (!empty($m_exp_id[$cou])) {
          $this->db->where('m_exp_id', $m_exp_id[$cou]);
          $this->where_branch('m_exp_branch', $branch);
          $this->db->update('master_expenses_tbl', $this->no_nulls($insertt_data));
        } else {
          $insertt_data['m_exp_branch']   = $branch ?? 0;
          $insertt_data['m_exp_added_by'] = $this->session->userdata('user_id');
          $insertt_data['m_exp_added_on'] = date('Y-m-d H:i');
          $this->db->insert('master_expenses_tbl', $this->no_nulls($insertt_data));
        }
      }
    }

    $this->db->trans_complete();
    return $res;
  }

  public function delete_purchase($branch_id = null)
  {
    $this->db->where('m_purcs_spo', $this->input->post('delete_id'));
    $this->where_branch('m_purcs_branch', $branch_id);
    $pur_datil  = $this->db->get('master_purchase_tbl')->result();

    // Same unchecked [0] as delete_sales() had (BUG-023).
    if (empty($pur_datil)) {
      return $this->fail('Purchase "' . $this->input->post('delete_id') . '" was not found. It may already have been deleted, or it belongs to a different branch.');
    }

    $pre_grandtotal = ($pur_datil[0]->m_purcs_comm + $pur_datil[0]->m_purcs_fright + $pur_datil[0]->m_purcs_hamali + $pur_datil[0]->m_purcs_charity + $pur_datil[0]->m_purcs_packaging + $pur_datil[0]->m_purcs_loading + $pur_datil[0]->m_purcs_advance + $pur_datil[0]->m_purcs_others);
    foreach ($pur_datil as $kry) {
      $pre_grandtotal += $kry->m_purcs_total;
      $this->update_userbalance($kry->m_purcs_suplier, null, ($kry->m_purcs_qty * (-1)), $kry->m_purcs_item, $kry->m_purcs_lot);
    }
    $this->update_userbalance($pur_datil[0]->m_purcs_suplier, ($pre_grandtotal * (-1)));

    $this->db->where('m_purcs_spo', $this->input->post('delete_id'));
    $this->where_branch('m_purcs_branch', $branch_id);
    $this->db->delete('master_purchase_tbl');

    $this->db->where('m_exp_accno', $this->input->post('delete_id'));
    $this->where_branch('m_exp_branch', $branch_id);
    $this->db->delete('master_expenses_tbl');
    return true;
  }

  public function delete_purchase_id($branch_id = null)
  {
    $this->db->where('m_purcs_id', $this->input->post('delete_id'));
    $this->where_branch('m_purcs_branch', $branch_id);
    $pur_datil = $this->db->select('m_purcs_qty,m_purcs_lot')
      ->get('master_purchase_tbl')->row();

    if (empty($pur_datil)) {
      return $this->fail('That purchase line was not found. It may already have been deleted, or it belongs to a different branch.');
    }

    $this->update_userbalance(null, null, ($pur_datil->m_purcs_qty * (-1)), null, $pur_datil->m_purcs_lot);
    $this->db->where('m_purcs_id', $this->input->post('delete_id'));
    $this->where_branch('m_purcs_branch', $branch_id);
    $this->db->delete('master_purchase_tbl');
    return true;
  }

  // HO's own available stock (m_purcs_branch = 0 is the Head Office convention:
  // superadmin purchases never send an m_purcs_branch override, so branch_id() returns
  // null and the NOT NULL column coerces it to 0 on insert).
  // landed_rate = item rate + (bill's total expense / bill's total purchased qty)
  public function get_ho_stock_list()
  {
    $this->db->select("
      master_purchase_tbl.m_purcs_id,
      master_purchase_tbl.m_purcs_available,
      master_purchase_tbl.m_purcs_price,
      master_purchase_tbl.m_purcs_lot,
      master_purchase_tbl.m_purcs_spo,
      mit.m_item_name,
      (master_purchase_tbl.m_purcs_comm + master_purchase_tbl.m_purcs_fright + master_purchase_tbl.m_purcs_hamali + master_purchase_tbl.m_purcs_charity + master_purchase_tbl.m_purcs_packaging + master_purchase_tbl.m_purcs_loading + master_purchase_tbl.m_purcs_advance + master_purchase_tbl.m_purcs_others) as bill_total_expense,
      COALESCE(billqty.total_qty, master_purchase_tbl.m_purcs_qty) as bill_total_qty,
      ROUND(master_purchase_tbl.m_purcs_price + ((master_purchase_tbl.m_purcs_comm + master_purchase_tbl.m_purcs_fright + master_purchase_tbl.m_purcs_hamali + master_purchase_tbl.m_purcs_charity + master_purchase_tbl.m_purcs_packaging + master_purchase_tbl.m_purcs_loading + master_purchase_tbl.m_purcs_advance + master_purchase_tbl.m_purcs_others) / COALESCE(billqty.total_qty, master_purchase_tbl.m_purcs_qty)), 2) as landed_rate
    ");
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_purchase_tbl.m_purcs_item', 'left');
    $this->db->join('(SELECT m_purcs_spo, SUM(m_purcs_qty) as total_qty FROM master_purchase_tbl WHERE m_purcs_type = 1 GROUP BY m_purcs_spo) billqty', 'billqty.m_purcs_spo = master_purchase_tbl.m_purcs_spo', 'left');
    $this->db->where('master_purchase_tbl.m_purcs_branch', 0);
    $this->db->where('master_purchase_tbl.m_purcs_type', 1);
    $this->db->where('master_purchase_tbl.m_purcs_available >', 0);
    $this->db->order_by('mit.m_item_name');
    return $this->db->get('master_purchase_tbl')->result();
  }

  // items: array of ['lot_id' => m_purcs_id, 'qty' => .., 'rate' => issue rate per unit]
  public function insert_transfer($items, $to_branch, $transfer_date = null)
  {
    $this->db->trans_start();

    $transfer_date = !empty($transfer_date) ? $transfer_date : date('Y-m-d');

    // SPO generation: highest counter ever issued (locked FOR UPDATE), not the
    // last inserted row - deleting an older transfer can leave a stale spo on
    // the newest row and cause the next number to be reused. Same pattern as
    // m_sale_spo/si_issue_spo/m_purcs_billno elsewhere in this model, scoped
    // to type = 2 so it's a short, independent sequence from those. The
    // REGEXP excludes old long-form spo values (TRF/ddmmyyyy/branch/random)
    // from earlier - without it MAX() would inherit their random 8-digit
    // tail forever instead of starting the new short counter at 100000.
    $maxSpo = $this->db->query(
      "SELECT MAX(CAST(SUBSTRING_INDEX(m_purcs_spo, '/', -1) AS UNSIGNED)) AS max_counter FROM master_purchase_tbl WHERE m_purcs_type = 2 AND m_purcs_spo REGEXP '^TRF/[0-9]+$' FOR UPDATE"
    )->row();
    $next_counter = (!empty($maxSpo) && $maxSpo->max_counter !== null) ? ((int) $maxSpo->max_counter + 1) : 100000;
    $spo         = 'TRF/' . $next_counter;
    $total_value = 0;

    foreach ($items as $it) {
      $lot_id = $it['lot_id'];
      $qty    = (float) $it['qty'];
      $rate   = (float) $it['rate'];

      $src = $this->db->where('m_purcs_id', $lot_id)->get('master_purchase_tbl')->row();

      // These four refusals used to share one `return false`, so the screen
      // said "check available stock and rate" even when the real problem was
      // an unknown lot or a zero quantity.
      if (!$src) {
        $this->db->trans_rollback();
        return $this->fail('Lot "' . $lot_id . '" was not found, so nothing was transferred.');
      }
      if ($qty <= 0) {
        $this->db->trans_rollback();
        return $this->fail('Quantity for lot "' . $lot_id . '" must be more than 0. Nothing was transferred.');
      }
      if ($rate <= 0) {
        $this->db->trans_rollback();
        return $this->fail('Rate for lot "' . $lot_id . '" must be more than 0. Nothing was transferred.');
      }
      if ($qty > $src->m_purcs_available) {
        $this->db->trans_rollback();
        return $this->fail('Lot "' . $lot_id . '" has only ' . $src->m_purcs_available
          . ' available but ' . $qty . ' was requested. Nothing was transferred.');
      }

      // reduce source (HO) availability
      $this->db->where('m_purcs_id', $lot_id)
        ->set('m_purcs_available', 'm_purcs_available - ' . $qty, false)
        ->update('master_purchase_tbl');

      $line_total   = round($qty * $rate, 2);
      $total_value += $line_total;

      $insert = [
        'm_purcs_date'        => $transfer_date,
        'm_purcs_suplier'     => $src->m_purcs_suplier,
        'm_purcs_item'        => $src->m_purcs_item,
        'm_purcs_qty'         => $qty,
        'm_purcs_available'   => $qty,
        'm_purcs_price'       => $rate,
        'm_purcs_total'       => $line_total,
        'm_purcs_lot'         => $src->m_purcs_lot,
        'm_purcs_truckno'     => $src->m_purcs_truckno,
        'm_purcs_spo'         => $spo,
        'm_purcs_branch'      => $to_branch,
        'm_purcs_from_branch' => $src->m_purcs_branch,
        'm_purcs_type'        => 2,
        // Explicit: the column is NOT NULL with no default, so omitting it let
        // MySQL's implicit default write 0 while every purchase row carries 1
        // (BUG-015).
        'm_purcs_status'      => 1,
        'm_purcs_ref_lot'     => $lot_id,
        'm_purcs_added_by'    => $this->session->userdata('user_id'),
        'm_purcs_added_on'    => date('Y-m-d H:i'),
      ];
      $this->db->insert('master_purchase_tbl', $this->no_nulls($insert));
    }

    // branch now owes HO the issued value (mirrors insert_purchase()'s +amount on supplier balance)
    if ($total_value > 0) {
      $this->update_userbalance($to_branch, $total_value);
    }

    $this->db->trans_complete();
    return $this->db->trans_status();
  }

  public function delete_transfer($spo, $branch_id = null)
  {
    $this->db->where('m_purcs_spo', $spo)->where('m_purcs_type', 2);
    $this->where_branch('m_purcs_branch', $branch_id);
    $rows = $this->db->get('master_purchase_tbl')->result();

    if (empty($rows)) return false;

    $total_value = 0;
    $to_branch   = $rows[0]->m_purcs_branch;

    foreach ($rows as $row) {
      if (!empty($row->m_purcs_ref_lot)) {
        $this->db->where('m_purcs_id', $row->m_purcs_ref_lot)
          ->set('m_purcs_available', 'm_purcs_available + ' . (float) $row->m_purcs_qty, false)
          ->update('master_purchase_tbl');
      }
      $total_value += (float) $row->m_purcs_total;
    }

    if ($total_value > 0) {
      $this->update_userbalance($to_branch, $total_value * -1);
    }

    $this->db->where('m_purcs_spo', $spo)->where('m_purcs_type', 2);
    $this->where_branch('m_purcs_branch', $branch_id);
    $this->db->delete('master_purchase_tbl');

    return true;
  }

  // A branch's own currently-held transfer stock - the mirror of
  // get_ho_stock_list(), scoped to one branch instead of HO. Feeds
  // Transfer::return_stock()'s lot picker once a branch is chosen.
  public function get_branch_stock_list($branch_id)
  {
    $this->db->select('m_purcs_id, m_purcs_available, m_purcs_price, m_purcs_lot, m_purcs_spo, mit.m_item_name');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_purchase_tbl.m_purcs_item', 'left');
    $this->db->where('master_purchase_tbl.m_purcs_branch', $branch_id);
    $this->db->where('master_purchase_tbl.m_purcs_type', 2);
    $this->db->where('master_purchase_tbl.m_purcs_available >', 0);
    $this->db->order_by('mit.m_item_name');
    return $this->db->get('master_purchase_tbl')->result();
  }

  /**
   * A branch sending part (or all) of its still-unsold transferred stock back
   * to Head Office - the reverse of insert_transfer().
   *
   * items: array of ['lot_id' => the BRANCH's own held row's m_purcs_id, 'qty'
   * => how much of it is coming back]. The rate is never re-entered - a
   * return reverses part of an existing charge, so its value is the rate the
   * branch was actually charged (that row's own m_purcs_price).
   *
   * No new row is created at Head Office: the returned qty is credited
   * straight back onto the ORIGINAL lot (found via the held row's own
   * m_purcs_ref_lot), the same reversal delete_transfer() already performs
   * for a full undo. get_ho_stock_list() therefore reflects a return with no
   * change of its own - it already just reads that lot's m_purcs_available.
   */
  public function insert_return($items, $branch_id, $return_date = null)
  {
    $this->db->trans_start();

    $return_date = !empty($return_date) ? $return_date : date('Y-m-d');

    // SPO generation: same MAX-counter-locked pattern as insert_transfer()
    // above, scoped to type = 3 so it's a short, independent sequence. Same
    // REGEXP guard against old long-form spo values (RTN/ddmmyyyy/branch/random).
    $maxSpo = $this->db->query(
      "SELECT MAX(CAST(SUBSTRING_INDEX(m_purcs_spo, '/', -1) AS UNSIGNED)) AS max_counter FROM master_purchase_tbl WHERE m_purcs_type = 3 AND m_purcs_spo REGEXP '^RTN/[0-9]+$' FOR UPDATE"
    )->row();
    $next_counter = (!empty($maxSpo) && $maxSpo->max_counter !== null) ? ((int) $maxSpo->max_counter + 1) : 100000;
    $spo         = 'RTN/' . $next_counter;
    $total_value = 0;

    foreach ($items as $it) {
      $branch_lot_id = $it['lot_id'];
      $qty           = (float) $it['qty'];

      // Scoped to this branch and to a transfer-in row, not just any
      // m_purcs_id - refuses a lot that isn't actually this branch's to
      // return, the same way insert_transfer() refuses an unknown lot.
      $src = $this->db->where('m_purcs_id', $branch_lot_id)
        ->where('m_purcs_branch', $branch_id)
        ->where('m_purcs_type', 2)
        ->get('master_purchase_tbl')->row();

      if (!$src) {
        $this->db->trans_rollback();
        return $this->fail('Lot "' . $branch_lot_id . '" is not stock this branch currently holds from a transfer. Nothing was returned.');
      }
      if ($qty <= 0) {
        $this->db->trans_rollback();
        return $this->fail('Quantity for lot "' . $branch_lot_id . '" must be more than 0. Nothing was returned.');
      }
      if ($qty > $src->m_purcs_available) {
        $this->db->trans_rollback();
        return $this->fail('Lot "' . $branch_lot_id . '" - the branch only holds ' . $src->m_purcs_available
          . ' but ' . $qty . ' was requested. Nothing was returned.');
      }

      // reduce the branch's held stock
      $this->db->where('m_purcs_id', $branch_lot_id)
        ->set('m_purcs_available', 'm_purcs_available - ' . $qty, false)
        ->update('master_purchase_tbl');

      // credit it back onto the original Head Office lot, if this row still
      // points at one (every row insert_transfer() writes does)
      if (!empty($src->m_purcs_ref_lot)) {
        $this->db->where('m_purcs_id', $src->m_purcs_ref_lot)
          ->set('m_purcs_available', 'm_purcs_available + ' . $qty, false)
          ->update('master_purchase_tbl');
      }

      $line_total   = round($qty * $src->m_purcs_price, 2);
      $total_value += $line_total;

      $insert = [
        'm_purcs_date'        => $return_date,
        'm_purcs_suplier'     => $src->m_purcs_suplier,
        'm_purcs_item'        => $src->m_purcs_item,
        'm_purcs_qty'         => $qty,
        // Nothing further is ever sold or transferred from a return row - it
        // is a closed, historical entry, not held stock - so there is
        // nothing left on it to be available.
        'm_purcs_available'   => 0,
        'm_purcs_price'       => $src->m_purcs_price,
        'm_purcs_total'       => $line_total,
        'm_purcs_lot'         => $src->m_purcs_lot,
        'm_purcs_truckno'     => $src->m_purcs_truckno,
        'm_purcs_spo'         => $spo,
        'm_purcs_branch'      => 0,
        'm_purcs_from_branch' => $branch_id,
        'm_purcs_type'        => 3,
        'm_purcs_status'      => 1,
        'm_purcs_ref_lot'     => $branch_lot_id,
        'm_purcs_added_by'    => $this->session->userdata('user_id'),
        'm_purcs_added_on'    => date('Y-m-d H:i'),
      ];
      $this->db->insert('master_purchase_tbl', $this->no_nulls($insert));
    }

    // branch now owes HO less by the returned value (mirrors
    // insert_transfer()'s own +amount, reversed)
    if ($total_value > 0) {
      $this->update_userbalance($branch_id, $total_value * -1);
    }

    $this->db->trans_complete();
    return $this->db->trans_status();
  }

  public function delete_return($spo, $branch_id = null)
  {
    $this->db->where('m_purcs_spo', $spo)->where('m_purcs_type', 3);
    if (!empty($branch_id)) {
      $this->db->where('m_purcs_from_branch', $branch_id);
    }
    $rows = $this->db->get('master_purchase_tbl')->result();

    if (empty($rows)) return false;

    $total_value  = 0;
    $return_branch = $rows[0]->m_purcs_from_branch;

    foreach ($rows as $row) {
      // give the qty back to the branch's held row...
      if (!empty($row->m_purcs_ref_lot)) {
        $this->db->where('m_purcs_id', $row->m_purcs_ref_lot)
          ->set('m_purcs_available', 'm_purcs_available + ' . (float) $row->m_purcs_qty, false)
          ->update('master_purchase_tbl');

        // ...and take it back off the original Head Office lot the branch's
        // row itself points at (one hop further than the return row's own
        // ref_lot, which stops at the branch's row).
        $branch_row = $this->db->select('m_purcs_ref_lot')
          ->where('m_purcs_id', $row->m_purcs_ref_lot)
          ->get('master_purchase_tbl')->row();
        if (!empty($branch_row->m_purcs_ref_lot)) {
          $this->db->where('m_purcs_id', $branch_row->m_purcs_ref_lot)
            ->set('m_purcs_available', 'm_purcs_available - ' . (float) $row->m_purcs_qty, false)
            ->update('master_purchase_tbl');
        }
      }
      $total_value += (float) $row->m_purcs_total;
    }

    if ($total_value > 0) {
      $this->update_userbalance($return_branch, $total_value);
    }

    $this->db->where('m_purcs_spo', $spo)->where('m_purcs_type', 3);
    if (!empty($branch_id)) {
      $this->db->where('m_purcs_from_branch', $branch_id);
    }
    $this->db->delete('master_purchase_tbl');

    return true;
  }

  // ===================== received payment/crate =======================//

  public function get_received_list($type, $from_date, $to_date, $scustomer = '', $account = '', $method = '', $group = '', $search_in = '', $order_by = '', $branch_id = null)
  {
    $this->where_branch('master_recieved_tbl.m_recvd_branch', $branch_id);
    $this->db->select('master_recieved_tbl.*,(CASE WHEN m_recvd_account = 1 || m_recvd_type = 2 THEN mct.m_cust_name WHEN m_recvd_account = 5 || m_recvd_account = 7 THEN mug.m_group_name ELSE mutt.m_user_name END) as m_cust_name,(CASE WHEN m_recvd_account = 1 THEN mct.m_cust_mobile  WHEN m_recvd_account = 5 || m_recvd_account = 7 THEN mug.m_group_number ELSE mutt.m_user_mobile END) as m_cust_mobile,mut.m_user_name,mut.m_user_mobile,crate.m_itgrp_title,method.m_group_name as method_name');
    $this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_users_tbl mutt', 'mutt.m_user_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_group_tbl mug', 'mug.m_group_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_recieved_tbl.m_recvd_user', 'left');
    $this->db->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_recieved_tbl.m_recvd_method', 'left');

    if (!empty($from_date))   $this->db->where('m_recvd_date>=', $from_date);
    if (!empty($to_date))     $this->db->where('m_recvd_date<=', $to_date);
    if (!empty($group))       $this->db->where_in('m_cust_group', $group);
    if (!empty($account))     $this->db->where_in('m_recvd_account', $account);
    if (!empty($method))      $this->db->where_in('m_recvd_method', $method);
    if (!empty($scustomer))   $this->db->where_in('m_recvd_customer', $scustomer);
    if (!empty($search_in)) {
      // Bound like() - see the note in get_user_list() (BUG-029).
      $this->db->group_start()
        ->like('mutt.m_user_name', $search_in)
        ->or_like('mutt.m_user_mobile', $search_in)
        ->or_like('m_cust_name', $search_in)
        ->or_like('m_cust_mobile', $search_in)
        ->or_like('mut.m_user_name', $search_in)
        ->or_like('mut.m_user_mobile', $search_in)
        ->group_end();
    }
    $this->db->where('m_recvd_type', $type);
    $this->db->order_by('m_recvd_date', !empty($order_by) ? $order_by : 'desc');
    return $this->db->get('master_recieved_tbl')->result();
  }

  public function get_received_detail($type, $voucher, $branch_id = null)
  {
    $this->where_branch('master_recieved_tbl.m_recvd_branch', $branch_id);
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
    $branch          = $this->branch_id($this->input->post('m_recvd_branch'));
    $m_recvd_type    = $this->input->post('m_recvd_type');
    $m_recvd_date    = $this->input->post('m_recvd_date');
    $m_recvd_account = $this->input->post('m_recvd_account');
    $user_id         = $this->session->userdata('user_id');

    if ($this->date_is_locked($m_recvd_date)) {
      return $this->fail($this->locked_date_message($m_recvd_date));
    }

    // Head Office receipts move the branch's own balance, so they need a
    // branch - see insert_payment_data.
    if ($m_recvd_account == 4 && $m_recvd_type == 1 && empty($branch)
      && in_array('0', array_map('strval', (array) $this->input->post('m_recvd_customer')), true)) {
      return $this->fail('Only a branch can record a receipt from Head Office.');
    }

    // Same NOT NULL trap as insert_payment_data - see the note there.
    $m_recvd_method_posted = $this->input->post('m_recvd_method');
    if ($m_recvd_type == 1 && ($m_recvd_method_posted === null || $m_recvd_method_posted === '')) {
      return $this->fail('Choose a receipt method. If the Method list is empty, this branch has no cash or bank account set up yet - add one under Master before recording receipts.');
    }

    // What actually reached the table. The old code tested `isset($res)`
    // against a variable nothing ever assigned, so every save - including
    // the ones that wrote rows - came back as a failure.
    $inserted   = 0;
    $duplicates = 0;

    if ($m_recvd_type == 1) {
      $m_recvd_customer = $this->input->post('m_recvd_customer');
      $m_recvd_amount   = $this->input->post('m_recvd_amount');
      $m_recvd_remark   = $this->input->post('m_recvd_remark');
      $m_recvd_user     = $this->input->post('m_recvd_user');
      $m_recvd_method   = $this->input->post('m_recvd_method');

      $this->db->where('m_recvd_type', 1);
      $this->where_branch('m_recvd_branch', $branch);
      $last_id = $this->db->order_by('m_recvd_id', 'desc')->get('master_recieved_tbl')->row();
      $vlastid = empty($last_id) ? 0 : $last_id->m_recvd_id;
      $voucher_no = date('d') . $vlastid . $m_recvd_account . $m_recvd_type;

      foreach ($m_recvd_customer as $index => $customer) {
        if ($m_recvd_amount[$index] == 0) continue;

        $this->db->where(['m_recvd_customer' => $customer, 'm_recvd_method' => $m_recvd_method, 'm_recvd_amount' => $m_recvd_amount[$index], 'm_recvd_date' => $m_recvd_date, 'm_recvd_type' => $m_recvd_type]);
        $this->where_branch('m_recvd_branch', $branch);
        $exists = $this->db->get('master_recieved_tbl')->row();

        if (!$exists) {
          $data = [
            "m_recvd_customer" => $customer,
            "m_recvd_voucher"  => $voucher_no,
            "m_recvd_method"   => $m_recvd_method,
            "m_recvd_amount"   => $m_recvd_amount[$index],
            "m_recvd_account"  => $m_recvd_account,
            "m_recvd_remark"   => $m_recvd_remark[$index],
            "m_recvd_user"     => $m_recvd_user[$index] ?? '',
            "m_recvd_date"     => $m_recvd_date,
            "m_recvd_type"     => $m_recvd_type,
            "m_recvd_branch"   => $branch ?? 0,
            "m_recvd_added_by" => $user_id,
            "m_recvd_added_on" => date('Y-m-d H:i'),
          ];
          $this->db->insert('master_recieved_tbl', $this->no_nulls($data));
          $inserted++;

          if ($m_recvd_account == 4 && (int) $customer === 0) {
            // Head Office as a supplier: money received from it raises what
            // this branch owes Head Office, carried on the branch's own row.
            $this->update_branch_ho_balance($branch, $m_recvd_amount[$index]);
          } elseif ($m_recvd_account == 1) {
            $this->update_cust_balance($customer, -$m_recvd_amount[$index]);
          } elseif ($m_recvd_account == 8) {
            // Branch paying HO reduces what the branch owes (mirrors supplier payment)
            $this->update_userbalance($customer, -$m_recvd_amount[$index]);
          } elseif (in_array($m_recvd_account, [2, 3, 4, 6])) {
            $this->update_userbalance($customer, $m_recvd_amount[$index]);
          }
        } else {
          $duplicates++;
        }
      }
    } else {
      $m_recvd_qty      = $this->input->post('m_recvd_qty');
      $m_recvd_crate    = $this->input->post('m_recvd_crate');
      $m_recvd_customer = $this->input->post('m_recvd_customer');
      $m_recvd_remark   = $this->input->post('m_recvd_remark');
      $m_recvd_user     = $this->input->post('m_recvd_user');
      $uniqut           = $this->input->post('uniqut');

      $this->db->where('m_recvd_type', 2);
      $this->where_branch('m_recvd_branch', $branch);
      $last_id = $this->db->order_by('m_recvd_id', 'desc')->get('master_recieved_tbl')->row();
      $vlastid = empty($last_id) ? 0 : $last_id->m_recvd_id;

      $crate_mapping = [20 => 'm_cust_10bal', 13 => 'm_cust_20bal', 14 => 'm_cust_25bal'];

      foreach ($m_recvd_customer as $index => $customer) {
        $voucher_no = date('d') . $vlastid . $customer . $m_recvd_type;

        foreach ($m_recvd_crate[$customer . $uniqut[$index]] as $subIndex => $crate_type) {
          $qty = (int)$m_recvd_qty[$customer . $uniqut[$index]][$subIndex];
          if ($qty == 0) continue;

          $data = [
            "m_recvd_customer" => $customer,
            "m_recvd_qty"      => $qty,
            "m_recvd_crate"    => $crate_type,
            "m_recvd_remark"   => $m_recvd_remark[$index],
            "m_recvd_user"     => $m_recvd_user[$index] ?? '',
            "m_recvd_voucher"  => $voucher_no,
            "m_recvd_date"     => $m_recvd_date,
            "m_recvd_type"     => $m_recvd_type,
            "m_recvd_branch"   => $branch ?? 0,
            "m_recvd_added_by" => $user_id,
            "m_recvd_added_on" => date('Y-m-d H:i'),
          ];
          $this->db->insert('master_recieved_tbl', $this->no_nulls($data));
          $inserted++;

          if (isset($crate_mapping[$crate_type])) {
            $this->db->set($crate_mapping[$crate_type], "$crate_mapping[$crate_type] - $qty", FALSE)
              ->where('m_cust_id', $customer)
              ->update('master_customer_tbl');
          }
        }
      }
    }
    if ($inserted === 0) {
      if ($duplicates > 0) {
        return $this->fail('Nothing was saved - an identical receipt is already recorded for that customer, amount and date. Change something on the line, or delete the existing entry first.');
      }
      return $this->fail('Nothing was saved - every line had an amount of 0. Enter an amount against at least one line and save again.');
    }

    return true;
  }

  public function update_recieved_data()
  {
    $branch      = $this->branch_id($this->input->post('m_recvd_branch'));
    $postData    = $this->input->post();
    $userId      = $this->session->userdata('user_id');
    $currentDate = date('Y-m-d H:i');

    $insert_data = [
      'm_recvd_customer'   => $postData['m_recvd_customer'],
      'm_recvd_date'       => $postData['m_recvd_date'],
      'm_recvd_remark'     => $postData['m_recvd_remark'],
      'm_recvd_updated_by' => $userId,
      'm_recvd_updated_on' => $currentDate,
    ];
    if ($postData['m_recvd_type'] == 1) {
      $insert_data['m_recvd_method'] = $postData['m_recvd_method'];
      $insert_data['m_recvd_amount'] = $postData['m_recvd_amount'];
    } else {
      $insert_data['m_recvd_qty']   = $postData['m_recvd_qty'];
      $insert_data['m_recvd_crate'] = $postData['m_recvd_crate'];
    }

    // The insert path refuses a locked-period date; this one used to let
    // an existing entry be dated back into one.
    if ($this->date_is_locked($postData['m_recvd_date'] ?? null)) {
      return $this->fail($this->locked_date_message($postData['m_recvd_date']));
    }

    // Confirm the row is really there, and in this branch, BEFORE anything
    // else. db->update() reports success even when its WHERE matched no
    // rows, and the balance adjustments further down used to run
    // regardless - so a stale id, or a branch override that doesn't match
    // the row's own branch, moved a balance without changing the receipt
    // it was supposed to be correcting.
    $this->db->where('m_recvd_id', $postData['m_recvd_id']);
    $this->where_branch('m_recvd_branch', $branch);
    $existing = $this->db->get('master_recieved_tbl')->row();
    if (empty($existing)) {
      return $this->fail('That receipt could not be found, so nothing was changed. It may have been deleted, or it belongs to a different branch - reload the list and try again.');
    }

    $this->db->where('m_recvd_id', $postData['m_recvd_id']);
    $this->where_branch('m_recvd_branch', $branch);
    $this->db->update('master_recieved_tbl', $this->no_nulls($insert_data));

    $isSameCustomer = ($postData['m_recvd_customer'] == $postData['precust']);

    $rcvd_was_ho = ((int) $postData['precust'] === 0);
    $rcvd_now_ho = ((int) $postData['m_recvd_customer'] === 0);
    if ($postData['m_recvd_type'] == 1 && $postData['m_recvd_account'] == 4
      && ($rcvd_was_ho || $rcvd_now_ho)) {
      // See update_payment_data: Head Office has no counterparty row, so both
      // ends of a swap have to be moved by hand.
      $ho_branch = $branch ?: ($existing->m_recvd_branch ?? null);
      if (empty($ho_branch)) {
        return $this->fail('That Head Office receipt is not attached to a branch, so its balance cannot be adjusted. Delete it and enter it again against the branch it belongs to.');
      }

      if ($rcvd_was_ho && $rcvd_now_ho) {
        $this->update_branch_ho_balance($ho_branch, $postData['m_recvd_amount'] - $postData['preamount']);
      } else {
        if ($rcvd_was_ho) {
          $this->update_branch_ho_balance($ho_branch, -$postData['preamount']);
        } else {
          $this->update_userbalance($postData['precust'], -$postData['preamount']);
        }
        if ($rcvd_now_ho) {
          $this->update_branch_ho_balance($ho_branch, +$postData['m_recvd_amount']);
        } else {
          $this->update_userbalance($postData['m_recvd_customer'], +$postData['m_recvd_amount']);
        }
      }
      return true;
    }

    if ($postData['m_recvd_type'] == 1) {
      if ($postData['m_recvd_account'] == 1) {
        if (!$isSameCustomer) {
          $this->update_cust_balance($postData['precust'], $postData['preamount']);
          $this->update_cust_balance($postData['m_recvd_customer'], ($postData['m_recvd_amount'] * (-1)));
        } else {
          $this->update_cust_balance($postData['m_recvd_customer'], ($postData['m_recvd_amount'] - $postData['preamount']) * (-1));
        }
      } elseif ($postData['m_recvd_account'] == 8) {
        if (!$isSameCustomer) {
          $this->update_userbalance($postData['precust'], $postData['preamount']);
          $this->update_userbalance($postData['m_recvd_customer'], ($postData['m_recvd_amount'] * (-1)));
        } else {
          $this->update_userbalance($postData['m_recvd_customer'], ($postData['m_recvd_amount'] - $postData['preamount']) * (-1));
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
      $crateMapping = [20 => 'm_cust_10bal', 13 => 'm_cust_20bal', 14 => 'm_cust_25bal'];
      if (isset($crateMapping[$postData['m_recvd_crate']])) {
        $field = $crateMapping[$postData['m_recvd_crate']];
        if (!$isSameCustomer) {
          $this->db->set($field, "$field + " . (int)$postData['preqty'], FALSE)->where('m_cust_id', $postData['precust'])->update('master_customer_tbl');
          $this->db->set($field, "$field - " . (int)$postData['m_recvd_qty'], FALSE)->where('m_cust_id', $postData['m_recvd_customer'])->update('master_customer_tbl');
        } else {
          $this->db->set($field, "$field - " . ((int)$postData['m_recvd_qty'] - (int)$postData['preqty']), FALSE)->where('m_cust_id', $postData['m_recvd_customer'])->update('master_customer_tbl');
        }
      }
    }
    return true;
  }

  public function delete_recieved_data($branch_id = null)
  {
    $delete_id = $this->input->post('delete_id');
    $this->db->where('m_recvd_voucher', $delete_id);
    $this->where_branch('m_recvd_branch', $branch_id);
    $res_list  = $this->db->get('master_recieved_tbl')->result();

    if (empty($res_list)) {
      return $this->fail('Receipt "' . $delete_id . '" was not found. It may already have been deleted, or it belongs to a different branch.');
    }

    if (!empty($res_list)) {
      $crate_mapping = [20 => 'm_cust_10bal', 13 => 'm_cust_20bal', 14 => 'm_cust_25bal'];
      foreach ($res_list as $value) {
        if ($value->m_recvd_type == 1) {
          if ($value->m_recvd_account == 4 && (int) $value->m_recvd_customer === 0) {
            // Head Office: undo the raise the insert made to the branch's own
            // balance. Customer 0 would no-op through update_userbalance().
            $this->update_branch_ho_balance($value->m_recvd_branch, -$value->m_recvd_amount);
          } elseif ($value->m_recvd_account == 1) {
            $this->update_cust_balance($value->m_recvd_customer, $value->m_recvd_amount);
          } elseif ($value->m_recvd_account == 8) {
            $this->update_userbalance($value->m_recvd_customer, $value->m_recvd_amount);
          } elseif (in_array($value->m_recvd_account, [2, 3, 4, 6])) {
            $this->update_userbalance($value->m_recvd_customer, $value->m_recvd_amount * (-1));
          }
        } elseif ($value->m_recvd_type == 2) {
          if (isset($crate_mapping[$value->m_recvd_crate])) {
            $this->db->set($crate_mapping[$value->m_recvd_crate], "{$crate_mapping[$value->m_recvd_crate]} + {$value->m_recvd_qty}", FALSE)
              ->where('m_cust_id', $value->m_recvd_customer)->update('master_customer_tbl');
          }
        }
      }
    }
    $this->db->where('m_recvd_voucher', $delete_id);
    $this->where_branch('m_recvd_branch', $branch_id);
    return $this->db->delete('master_recieved_tbl');
  }

  // ===================== paid payment/crate =======================//

  public function get_payment_list($type, $from_date, $to_date, $scustomer, $payment_account = '', $payment_method = '', $search_in = '', $order_by = '', $branch_id = null)
  {
    $this->where_branch('master_payment_tbl.m_payment_branch', $branch_id);
    $this->db->select('master_payment_tbl.*,(CASE WHEN m_payment_account = 2 || m_payment_account = 7 THEN mgt.m_group_name ELSE mut.m_user_name END) as m_user_name,(CASE WHEN m_payment_account = 2 || m_payment_account = 7 THEN mgt.m_group_number ELSE m_user_mobile END) as m_user_mobile,crate.m_itgrp_title,m_payment_account as account_type,method.m_group_name as method_name');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_payment_tbl.m_payment_crate', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_payment_tbl.m_payment_method', 'left');

    if (!empty($from_date))        $this->db->where('m_payment_date>=', $from_date);
    if (!empty($to_date))          $this->db->where('m_payment_date<=', $to_date);
    if (!empty($payment_account))  $this->db->where('m_payment_account', $payment_account);
    if (!empty($payment_method))   $this->db->where('m_payment_method', $payment_method);
    if (!empty($scustomer))        $this->db->where_in('m_payment_supplier', $scustomer);
    if (!empty($search_in)) {
      // Bound like() - see the note in get_user_list() (BUG-029).
      $this->db->group_start()
        ->like('method.m_group_name', $search_in)
        ->or_like('mgt.m_group_name', $search_in)
        ->or_like('mut.m_user_name', $search_in)
        ->or_like('mut.m_user_mobile', $search_in)
        ->group_end();
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

  public function get_payment_detail($type, $voucher, $branch_id = null)
  {
    $this->where_branch('master_payment_tbl.m_payment_branch', $branch_id);
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
    $branch            = $this->branch_id($this->input->post('m_payment_branch'));
    $m_payment_type    = $this->input->post('m_payment_type');
    $m_payment_date    = $this->input->post('m_payment_date');
    $m_payment_method  = $this->input->post('m_payment_method');
    $m_payment_account = $this->input->post('m_payment_account');

    if ($this->date_is_locked($m_payment_date)) {
      return $this->fail($this->locked_date_message($m_payment_date));
    }

    // Account 8 (Branch) is Head Office paying one of its branches. The view
    // only hides the option; this is what actually stops a branch user from
    // posting it against another branch.
    if ($m_payment_account == 8 && $this->session->userdata('user_type') != 8) {
      return $this->fail('Only Head Office can record a payment to a branch.');
    }

    // A payment to Head Office is an ordinary Supplier payment whose party is
    // the Head Office pseudo-row (id 0). It has no balance of its own, so the
    // amount comes off the paying BRANCH's m_user_balance - which means we
    // have to know which branch. A branch user supplies that implicitly
    // through branch_id(); Head Office itself (0) is not a payer.
    if ($m_payment_account == 1 && $m_payment_type == 1 && empty($branch)
      && in_array('0', array_map('strval', (array) $this->input->post('m_payment_supplier')), true)) {
      return $this->fail('Only a branch can record a payment to Head Office. Head Office cannot pay itself.');
    }

    // m_payment_method is NOT NULL. The Method dropdown is branch-scoped, so a
    // branch with no cash/bank account of its own renders it empty and posts
    // nothing - which used to surface as a raw database error page with the
    // INSERT statement printed on it.
    if ($m_payment_type == 1 && ($m_payment_method === null || $m_payment_method === '')) {
      return $this->fail('Choose a payment method. If the Method list is empty, this branch has no cash or bank account set up yet - add one under Master before recording payments.');
    }

    if ($m_payment_type == 1) {
      $m_payment_supplier = $this->input->post('m_payment_supplier');
      $m_payment_amount   = $this->input->post('m_payment_amount');
      $m_payment_remark   = $this->input->post('m_payment_remark');

      $this->db->where('m_payment_type', $m_payment_type);
      $this->where_branch('m_payment_branch', $branch);
      $last_id = $this->db->order_by('m_payment_id', 'desc')->get('master_payment_tbl')->row();
      $vlastid = empty($last_id) ? 0 : $last_id->m_payment_id;

      $duplicates = 0;
      foreach ($m_payment_supplier as $cou => $supplier) {
        if ($m_payment_amount[$cou] == 0) continue;

        $this->db->where(['m_payment_supplier' => $supplier, 'm_payment_method' => $m_payment_method, 'm_payment_amount' => $m_payment_amount[$cou], 'm_payment_date' => $m_payment_date, 'm_payment_type' => $m_payment_type]);
        $this->where_branch('m_payment_branch', $branch);
        $exists = $this->db->get('master_payment_tbl')->row();

        if (!$exists) {
          $voucher_no  = date('d') . $vlastid . $m_payment_account . $m_payment_type;
          $insert_data = [
            "m_payment_supplier" => $supplier,
            "m_payment_voucher"  => $voucher_no,
            "m_payment_method"   => $m_payment_method,
            "m_payment_account"  => $m_payment_account,
            "m_payment_amount"   => $m_payment_amount[$cou],
            "m_payment_remark"   => $m_payment_remark[$cou],
            "m_payment_date"     => $m_payment_date,
            "m_payment_type"     => $m_payment_type,
            "m_payment_branch"   => $branch ?? 0,
            "m_payment_added_by" => $this->session->userdata('user_id'),
            "m_payment_added_on" => date('Y-m-d H:i'),
          ];
          $res = $this->db->insert('master_payment_tbl', $this->no_nulls($insert_data));
          if ($m_payment_account == 8) {
            // Head Office paying a branch: the branch received money, so it
            // owes Head Office MORE. Reverse of a supplier payment, because a
            // branch is a debtor rather than a creditor.
            $this->update_branch_ho_balance($supplier, +$m_payment_amount[$cou]);
          } elseif ($m_payment_account == 1 && (int) $supplier === 0) {
            // Head Office: settles part of what this branch owes it. Same sign
            // as the supplier arm below, just carried on the branch's row.
            $this->update_branch_ho_balance($branch, -$m_payment_amount[$cou]);
          } elseif (!in_array($m_payment_account, [2, 7])) {
            $this->update_userbalance($supplier, -$m_payment_amount[$cou]);
          }
        } else {
          $duplicates++;
        }
      }
    } else {
      $uniqut             = $this->input->post('uniqut');
      $m_payment_qty      = $this->input->post('m_payment_qty');
      $m_payment_crate    = $this->input->post('m_payment_crate');
      $m_payment_supplier = $this->input->post('m_payment_supplier');
      $m_payment_remark   = $this->input->post('m_payment_remark');
      $crate_mapping      = [20 => 'm_user_10bal', 13 => 'm_user_20bal', 14 => 'm_user_25bal'];

      $this->db->where('m_payment_type', $m_payment_type);
      $this->where_branch('m_payment_branch', $branch);
      $last_id = $this->db->order_by('m_payment_id', 'desc')->get('master_payment_tbl')->row();
      $vlastid = empty($last_id) ? 0 : $last_id->m_payment_id;

      foreach ($m_payment_supplier as $cau => $supplier) {
        $voucher_no = date('d') . $vlastid . $supplier . $m_payment_type;
        foreach ($m_payment_crate[$supplier . $uniqut[$cau]] as $cou => $crate) {
          if ($m_payment_qty[$supplier . $uniqut[$cau]][$cou] == 0) continue;
          $insert_data = [
            "m_payment_supplier" => $supplier,
            "m_payment_qty"      => $m_payment_qty[$supplier . $uniqut[$cau]][$cou],
            "m_payment_crate"    => $crate,
            "m_payment_remark"   => $m_payment_remark[$cau],
            "m_payment_voucher"  => $voucher_no,
            "m_payment_date"     => $m_payment_date,
            "m_payment_type"     => $m_payment_type,
            "m_payment_branch"   => $branch ?? 0,
            "m_payment_added_by" => $this->session->userdata('user_id'),
            "m_payment_added_on" => date('Y-m-d H:i'),
          ];
          $res = $this->db->insert('master_payment_tbl', $this->no_nulls($insert_data));
          if (isset($crate_mapping[$crate])) {
            $this->db->set($crate_mapping[$crate], "{$crate_mapping[$crate]} - {$m_payment_qty[$supplier .$uniqut[$cau]][$cou]}", FALSE)
              ->where('m_user_id', $supplier)->update('master_users_tbl');
          }
        }
      }
    }
    if (!isset($res)) {
      if (!empty($duplicates)) {
        return $this->fail('Nothing was saved - an identical payment is already recorded for that account, amount and date. Change something on the line, or delete the existing entry first.');
      }
      return $this->fail('Nothing was saved - every line had an amount of 0. Enter an amount against at least one line and save again.');
    }
    return $res;
  }

  public function update_payment_data()
  {
    $branch      = $this->branch_id($this->input->post('m_payment_branch'));
    $postData    = $this->input->post();
    $userId      = $this->session->userdata('user_id');
    $currentDate = date('Y-m-d H:i');

    $insert_data = [
      'm_payment_supplier'   => $postData['m_payment_supplier'],
      'm_payment_date'       => $postData['m_payment_date'],
      'm_payment_remark'     => $postData['m_payment_remark'],
      'm_payment_updated_by' => $userId,
      'm_payment_updated_on' => $currentDate,
    ];
    if ($postData['m_payment_type'] == 1) {
      $insert_data['m_payment_method'] = $postData['m_payment_method'];
      $insert_data['m_payment_amount'] = $postData['m_payment_amount'];
    } else {
      $insert_data['m_payment_qty']   = $postData['m_payment_qty'];
      $insert_data['m_payment_crate'] = $postData['m_payment_crate'];
    }

    // The insert path refuses a locked-period date; this one used to let
    // an existing entry be dated back into one.
    if ($this->date_is_locked($postData['m_payment_date'] ?? null)) {
      return $this->fail($this->locked_date_message($postData['m_payment_date']));
    }

    // Confirm the row is really there, and in this branch, BEFORE anything
    // else. db->update() reports success even when its WHERE matched no
    // rows, and the balance adjustments further down used to run
    // regardless - so a stale id, or a branch override that doesn't match
    // the row's own branch, moved a balance without changing the payment
    // it was supposed to be correcting.
    $this->db->where('m_payment_id', $postData['m_payment_id']);
    $this->where_branch('m_payment_branch', $branch);
    $existing = $this->db->get('master_payment_tbl')->row();
    if (empty($existing)) {
      return $this->fail('That payment could not be found, so nothing was changed. It may have been deleted, or it belongs to a different branch - reload the list and try again.');
    }

    $this->db->where('m_payment_id', $postData['m_payment_id']);
    $this->where_branch('m_payment_branch', $branch);
    $this->db->update('master_payment_tbl', $this->no_nulls($insert_data));

    $isBalanceUpdateRequired = !in_array($postData['m_payment_account'], [2, 7]);
    $isSameCustomer          = ($postData['m_payment_supplier'] == $postData['precust']);

    // Account 8 is Head Office paying one of its branches. The party is a real
    // branch row, so an edit can swap which branch was paid - move the old
    // amount off the previous one before charging the new one.
    if ($postData['m_payment_account'] == 8 && $postData['m_payment_type'] == 1) {
      // Same sign as the insert. A branch swap moves the old amount off the
      // previous branch and the new amount onto the new one.
      if ($postData['m_payment_supplier'] != $postData['precust']) {
        $this->update_branch_ho_balance($postData['precust'], -$postData['preamount']);
        $this->update_branch_ho_balance($postData['m_payment_supplier'], +$postData['m_payment_amount']);
      } else {
        $this->update_branch_ho_balance($postData['m_payment_supplier'], $postData['m_payment_amount'] - $postData['preamount']);
      }
      $isBalanceUpdateRequired = false;
    }

    $was_ho = ((int) $postData['precust'] === 0);
    $now_ho = ((int) $postData['m_payment_supplier'] === 0);
    if ($postData['m_payment_account'] == 1 && $postData['m_payment_type'] == 1 && ($was_ho || $now_ho)) {
      // The edit modal posts no m_payment_branch, so $branch is null for a
      // superadmin. Take it off the row being edited instead - that is the
      // branch whose balance the original insert moved.
      $ho_branch = $branch ?: ($existing->m_payment_branch ?? null);
      if (empty($ho_branch)) {
        return $this->fail('That Head Office payment is not attached to a branch, so its balance cannot be adjusted. Delete it and enter it again against the paying branch.');
      }

      if ($was_ho && $now_ho) {
        $this->update_branch_ho_balance($ho_branch, ($postData['m_payment_amount'] - $postData['preamount']) * (-1));
      } else {
        // Swapped across the Head Office boundary: give the old party back
        // what it was charged, then charge the new one.
        if ($was_ho) {
          $this->update_branch_ho_balance($ho_branch, +$postData['preamount']);
        } else {
          $this->update_userbalance($postData['precust'], +$postData['preamount']);
        }
        if ($now_ho) {
          $this->update_branch_ho_balance($ho_branch, -$postData['m_payment_amount']);
        } else {
          $this->update_userbalance($postData['m_payment_supplier'], -$postData['m_payment_amount']);
        }
      }
      $isBalanceUpdateRequired = false;
    }

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
      $crateMapping = [20 => 'm_user_10bal', 13 => 'm_user_20bal', 14 => 'm_user_25bal'];
      if (isset($crateMapping[$postData['m_payment_crate']])) {
        $field   = $crateMapping[$postData['m_payment_crate']];
        $qtyDiff = (int)$postData['m_payment_qty'] - (int)$postData['preqty'];
        if (!$isSameCustomer) {
          $this->db->set($field, "$field + " . (int)$postData['preqty'], FALSE)->where('m_user_id', $postData['precust'])->update('master_users_tbl');
          $this->db->set($field, "$field - " . (int)$postData['m_payment_qty'], FALSE)->where('m_user_id', $postData['m_payment_supplier'])->update('master_users_tbl');
        } else {
          $this->db->set($field, "$field - $qtyDiff", FALSE)->where('m_user_id', $postData['m_payment_supplier'])->update('master_users_tbl');
        }
      }
    }
    return true;
  }

  public function delete_payment_data($branch_id = null)
  {
    $delete_id = $this->input->post('delete_id');
    $this->db->where('m_payment_voucher', $delete_id);
    $this->where_branch('m_payment_branch', $branch_id);
    $res_list  = $this->db->get('master_payment_tbl')->result();

    if (empty($res_list)) {
      return $this->fail('Payment "' . $delete_id . '" was not found. It may already have been deleted, or it belongs to a different branch.');
    }

    if (!empty($res_list)) {
      $crate_mapping = [20 => 'm_user_10bal', 13 => 'm_user_20bal', 14 => 'm_user_25bal'];
      foreach ($res_list as $value) {
        if ($value->m_payment_type == 1 && $value->m_payment_account == 8) {
          // Undo the raise: the branch no longer received that money.
          $this->update_branch_ho_balance($value->m_payment_supplier, -$value->m_payment_amount);
        } elseif ($value->m_payment_type == 1 && $value->m_payment_account == 1 && (int) $value->m_payment_supplier === 0) {
          // Head Office: the amount came off the branch, so it goes back on the
          // branch. Passing supplier 0 to update_userbalance() no-ops silently.
          $this->update_branch_ho_balance($value->m_payment_branch, $value->m_payment_amount);
        } elseif ($value->m_payment_type == 1 && !in_array($value->m_payment_account, [2, 7])) {
          $this->update_userbalance($value->m_payment_supplier, $value->m_payment_amount);
        } elseif ($value->m_payment_type == 2) {
          if (isset($crate_mapping[$value->m_payment_crate])) {
            $this->db->set($crate_mapping[$value->m_payment_crate], "{$crate_mapping[$value->m_payment_crate]} + {$value->m_payment_qty}", FALSE)
              ->where('m_user_id', $value->m_payment_supplier)->update('master_users_tbl');
          }
        }
      }
    }
    $this->db->where('m_payment_voucher', $delete_id);
    $this->where_branch('m_payment_branch', $branch_id);
    return $this->db->delete('master_payment_tbl');
  }

  // ===================== voucher =======================//

  public function get_voucher_list($type, $from_date, $to_date, $scustomer, $search_in = '', $order_by = '', $branch_id = null)
  {
    $this->where_branch('master_voucher_tbl.m_voucher_branch', $branch_id);
    $this->db->select('master_voucher_tbl.*,(CASE WHEN m_voucher_account = 3 THEN mgt.m_group_name WHEN m_voucher_account = 1 THEN mct.m_cust_name ELSE mut.m_user_name END) as m_user_name,(CASE WHEN m_voucher_account = 3 THEN mgt.m_group_number WHEN m_voucher_account = 1 THEN mct.m_cust_mobile ELSE mut.m_user_mobile END) as m_user_mobile,m_voucher_account as account_type');
    $this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_voucher_tbl.m_voucher_accountid', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_voucher_tbl.m_voucher_accountid', 'left');
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_voucher_tbl.m_voucher_accountid', 'left');

    if (!empty($from_date)) $this->db->where('m_voucher_date>=', $from_date);
    if (!empty($to_date))   $this->db->where('m_voucher_date<=', $to_date);
    if (!empty($type))      $this->db->where('m_voucher_type', $type);
    if (!empty($scustomer)) $this->db->where_in('m_voucher_accountid', $scustomer);
    if (!empty($search_in)) {
      // Bound like() - see the note in get_user_list() (BUG-029).
      $this->db->group_start()
        ->like('mct.m_cust_name', $search_in)
        ->or_like('mgt.m_group_name', $search_in)
        ->or_like('mut.m_user_name', $search_in)
        ->or_like('mut.m_user_mobile', $search_in)
        ->or_like('mct.m_cust_mobile', $search_in)
        ->group_end();
    }
    $this->db->order_by('m_voucher_date', !empty($order_by) ? $order_by : 'desc');
    return $this->db->get('master_voucher_tbl')->result();
  }

  public function get_voucher_detail($voucher, $branch_id = null)
  {
    $this->where_branch('master_voucher_tbl.m_voucher_branch', $branch_id);
    $this->db->select('master_voucher_tbl.*,(CASE WHEN m_voucher_account = 3 THEN mgt.m_group_name WHEN m_voucher_account = 1 THEN mct.m_cust_name ELSE mut.m_user_name END) as m_user_name,(CASE WHEN m_voucher_account = 3 THEN mgt.m_group_number WHEN m_voucher_account = 1 THEN mct.m_cust_mobile ELSE mut.m_user_mobile END) as m_user_mobile,m_voucher_account as account_type');
    $this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_voucher_tbl.m_voucher_accountid', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_voucher_tbl.m_voucher_accountid', 'left');
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_voucher_tbl.m_voucher_accountid', 'left');
    $this->db->where('m_voucher_id', $voucher);
    return $this->db->get('master_voucher_tbl')->row();
  }

  public function insert_voucher_data()
  {
    $branch              = $this->branch_id($this->input->post('m_voucher_branch'));
    $postData            = $this->input->post();
    $userId              = $this->session->userdata('user_id');
    $currentDate         = date('Y-m-d H:i');
    $m_voucher_accountid = $postData['m_voucher_accountid'];
    $m_voucher_account   = $postData['m_voucher_account'];
    $insertBatch         = [];

    if ($this->date_is_locked($postData['m_voucher_date'] ?? null)) {
      return $this->fail($this->locked_date_message($postData['m_voucher_date']));
    }

    // See insert_payment_data - the view-level gate is not a security control.
    if ($m_voucher_account == 8 && $this->session->userdata('user_type') != 8) {
      return $this->fail('Only Head Office can raise a voucher against a branch.');
    }

    // See insert_payment_data: a voucher against Head Office moves the branch's
    // own balance, so it has to know which branch.
    if ($m_voucher_account == 2 && empty($branch)
      && in_array('0', array_map('strval', (array) $m_voucher_accountid), true)) {
      return $this->fail('Only a branch can raise a voucher against Head Office. Head Office cannot raise one against itself.');
    }

    foreach ($m_voucher_accountid as $index => $accountId) {
      if ($postData['m_voucher_amount'][$index] == 0) continue;

      $voucherAmount = $postData['m_voucher_amount'][$index];
      $voucherType   = $postData['m_voucher_type'][$index];
      $voucherRemark = $postData['m_voucher_remark'][$index];

      $insertBatch[] = [
        "m_voucher_accountid" => $accountId,
        "m_voucher_account"   => $m_voucher_account,
        "m_voucher_amount"    => $voucherAmount,
        "m_voucher_remark"    => $voucherRemark,
        "m_voucher_date"      => $postData['m_voucher_date'],
        "m_voucher_type"      => $voucherType,
        "m_voucher_status"    => 1,
        "m_voucher_branch"    => $branch ?? 0,
        "m_voucher_added_by"  => $userId,
        "m_voucher_added_on"  => $currentDate,
      ];

      if ($m_voucher_account == 8) {
        // Head Office raising a voucher against a branch. Same signs as the
        // branch's own Head Office vouchers: credit raises the debt, debit
        // settles it.
        $this->update_branch_ho_balance($accountId, $voucherType == 1 ? +$voucherAmount : -$voucherAmount);
      } elseif ($m_voucher_account == 2 && (int) $accountId === 0) {
        // Head Office has no account row of its own - what a voucher against
        // it changes is how much the branch owes Head Office, carried on the
        // branch's m_user_balance. Signs follow the supplier case: credit
        // raises the debt, debit settles it.
        $this->update_branch_ho_balance($branch, $voucherType == 1 ? +$voucherAmount : -$voucherAmount);
      } elseif ($voucherType == 1) {
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
      $this->db->insert_batch('master_voucher_tbl', $this->no_nulls($insertBatch));
      return true;
    }
    return $this->fail('Nothing was saved - every line had an amount of 0. Enter an amount against at least one line and save again.');
  }

  public function update_voucher_data()
  {
    $branch      = $this->branch_id($this->input->post('m_voucher_branch'));
    $postData    = $this->input->post();
    $userId      = $this->session->userdata('user_id');
    $currentDate = date('Y-m-d H:i');

    $updateData = [
      'm_voucher_amount'     => $postData['m_voucher_amount'],
      'm_voucher_type'       => $postData['m_voucher_type'],
      'm_voucher_date'       => $postData['m_voucher_date'],
      'm_voucher_accountid'  => $postData['m_voucher_accountid'],
      'm_voucher_remark'     => $postData['m_voucher_remark'],
      'm_voucher_updated_by' => $userId,
      'm_voucher_updated_on' => $currentDate,
    ];

    // The insert path refuses a locked-period date; this one used to let
    // an existing entry be dated back into one.
    if ($this->date_is_locked($postData['m_voucher_date'] ?? null)) {
      return $this->fail($this->locked_date_message($postData['m_voucher_date']));
    }

    // Confirm the row is really there, and in this branch, BEFORE anything
    // else. db->update() reports success even when its WHERE matched no
    // rows, and the balance adjustments further down used to run
    // regardless - so a stale id, or a branch override that doesn't match
    // the row's own branch, moved a balance without changing the voucher
    // it was supposed to be correcting.
    $this->db->where('m_voucher_id', $postData['m_voucher_id']);
    $this->where_branch('m_voucher_branch', $branch);
    $existing = $this->db->get('master_voucher_tbl')->row();
    if (empty($existing)) {
      return $this->fail('That voucher could not be found, so nothing was changed. It may have been deleted, or it belongs to a different branch - reload the list and try again.');
    }

    $this->db->where('m_voucher_id', $postData['m_voucher_id']);
    $this->where_branch('m_voucher_branch', $branch);
    $this->db->update('master_voucher_tbl', $this->no_nulls($updateData));

    $isSameCustomer = ($postData['m_voucher_accountid'] == $postData['precust']);
    $balanceChange  = $postData['m_voucher_amount'] - $postData['preamount'];

    // Account 8 is a voucher Head Office raised against a branch. Signed the
    // way the insert signed it, and able to swap which branch it applies to.
    if ($postData['m_voucher_account'] == 8) {
      $sign = ($postData['m_voucher_type'] == 1) ? 1 : -1;
      if (!$isSameCustomer) {
        $this->update_branch_ho_balance($postData['precust'], -$sign * $postData['preamount']);
        $this->update_branch_ho_balance($postData['m_voucher_accountid'], $sign * $postData['m_voucher_amount']);
      } else {
        $this->update_branch_ho_balance($postData['m_voucher_accountid'], $sign * $balanceChange);
      }
      return true;
    }

    $vch_was_ho = ((int) $postData['precust'] === 0);
    $vch_now_ho = ((int) $postData['m_voucher_accountid'] === 0);
    if ($postData['m_voucher_account'] == 2 && ($vch_was_ho || $vch_now_ho)) {
      // See update_payment_data: the edit form posts no m_voucher_branch, so
      // fall back to the branch recorded on the row itself.
      $ho_branch = $branch ?: ($existing->m_voucher_branch ?? null);
      if (empty($ho_branch)) {
        return $this->fail('That Head Office voucher is not attached to a branch, so its balance cannot be adjusted. Delete it and enter it again against the branch it belongs to.');
      }

      $vch_sign = ($postData['m_voucher_type'] == 1) ? 1 : -1;
      if ($vch_was_ho && $vch_now_ho) {
        $this->update_branch_ho_balance($ho_branch, $vch_sign * $balanceChange);
      } else {
        if ($vch_was_ho) {
          $this->update_branch_ho_balance($ho_branch, -$vch_sign * $postData['preamount']);
        } else {
          $this->update_userbalance($postData['precust'], -$vch_sign * $postData['preamount']);
        }
        if ($vch_now_ho) {
          $this->update_branch_ho_balance($ho_branch, $vch_sign * $postData['m_voucher_amount']);
        } else {
          $this->update_userbalance($postData['m_voucher_accountid'], $vch_sign * $postData['m_voucher_amount']);
        }
      }
      return true;
    }

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
    return true;
  }

  public function delete_voucher_data($branch_id = null)
  {
    $delete_id = $this->input->post('delete_id');
    $this->db->where('m_voucher_id', $delete_id);
    $this->where_branch('m_voucher_branch', $branch_id);
    $res  = $this->db->get('master_voucher_tbl')->row();

    if (empty($res)) {
      return $this->fail('Voucher "' . $delete_id . '" was not found. It may already have been deleted, or it belongs to a different branch.');
    }

    if ($res->m_voucher_account == 8) {
      $this->update_branch_ho_balance($res->m_voucher_accountid, $res->m_voucher_type == 1 ? -$res->m_voucher_amount : +$res->m_voucher_amount);
    } else if ($res->m_voucher_account == 2 && (int) $res->m_voucher_accountid === 0) {
      // Head Office: undo whatever the insert did to the branch's balance -
      // credit raised it, debit lowered it, so reverse the sign.
      $this->update_branch_ho_balance($res->m_voucher_branch, $res->m_voucher_type == 1 ? -$res->m_voucher_amount : +$res->m_voucher_amount);
    } else if ($res->m_voucher_type == 1 && $res->m_voucher_account != 1 && $res->m_voucher_account != 3) {
      $this->update_userbalance($res->m_voucher_accountid, ($res->m_voucher_amount * (-1)));
    } else if ($res->m_voucher_type == 1 && $res->m_voucher_account == 1) {
      $this->update_cust_balance($res->m_voucher_accountid, $res->m_voucher_amount);
    } else if ($res->m_voucher_type == 2 && $res->m_voucher_account == 1) {
      $this->update_cust_balance($res->m_voucher_accountid, ($res->m_voucher_amount * (-1)));
    } else if ($res->m_voucher_type == 2 && $res->m_voucher_account != 1 && $res->m_voucher_account != 3) {
      $this->update_userbalance($res->m_voucher_accountid, ($res->m_voucher_amount));
    }
    $this->db->where('m_voucher_id', $this->input->post('delete_id'));
    $this->where_branch('m_voucher_branch', $branch_id);
    return $this->db->delete('master_voucher_tbl');
  }

  // ==========================Stock List===========================//

  public function get_avilable_item($itemid = '', $group = '', $branch_id = null)
  {
    $this->db->select('m_item_id,m_item_name,m_item_crate,m_item_fright,m_item_price,m_purcs_price,m_purcs_available,m_purcs_lot,m_purcs_id,unit.m_itgrp_title as m_unit_name,crate.m_itgrp_title as m_crate_name,m_purcs_date,m_user_trademark')
      ->join('master_item_tbl', 'master_item_tbl.m_item_id = master_purchase_tbl.m_purcs_item')
      ->join('master_itemgroup_tbl crate', 'crate.m_itgrp_id = master_item_tbl.m_item_crate', 'left')
      ->join('master_itemgroup_tbl unit', 'unit.m_itgrp_id = master_item_tbl.m_item_unit', 'left')
      ->join('master_users_tbl mut', 'mut.m_user_id = master_purchase_tbl.m_purcs_suplier', 'left');
    $this->db->where('m_purcs_available >', 0);
    $this->where_branch('master_purchase_tbl.m_purcs_branch', $branch_id);
    if (!empty($itemid)) $this->db->where('m_item_id', $itemid);
    if ($group == 1)     $this->db->group_by('m_item_id');
    return $this->db->order_by('m_item_name')->get('master_purchase_tbl')->result();
  }

  private function get_lot_available_qty($item, $lot, $branch_id = null)
  {
    $this->db->select('m_purcs_available as qty');
    $this->db->where(['m_purcs_item' => $item, 'm_purcs_id' => $lot]);
    $this->where_branch('m_purcs_branch', $branch_id);
    $row = $this->db->get('master_purchase_tbl')->row();
    return (float)($row->qty ?? 0);
  }

  // ==========================Balance Updaters===========================//

  public function update_cust_balance($id = '', $amt = '', $qty = '', $itemID = '', $purID = '', $branch_id = null)
  {
    if (!empty($purID) && !empty($qty)) {
      $this->db->set('m_purcs_available', 'm_purcs_available - ' . (int)$qty, FALSE)
        ->where('m_purcs_id', $purID);
      $this->where_branch('m_purcs_branch', $branch_id);
      $this->db->update('master_purchase_tbl');
    }

    if (!empty($itemID)) {
      $itemDtl = $this->db->select('m_itgrp_title')
        ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_item_tbl.m_item_crate', 'left')
        ->where('m_item_id', $itemID)->get('master_item_tbl')->row();

      $balanceFields = ['10 KG' => 'm_cust_10bal', '20 KG' => 'm_cust_20bal', '25 KG' => 'm_cust_25bal'];
      if (!empty($itemDtl->m_itgrp_title) && isset($balanceFields[$itemDtl->m_itgrp_title])) {
        $this->db->set($balanceFields[$itemDtl->m_itgrp_title], $balanceFields[$itemDtl->m_itgrp_title] . ' + ' . (float)$qty, FALSE)
          ->where('m_cust_id', $id);
        $this->where_branch('m_cust_branch', $branch_id);
        $this->db->update('master_customer_tbl');
      }
    }

    if (!empty($amt) && !empty($id)) {
      $this->db->set('m_cust_balance', 'm_cust_balance + ' . (float)$amt, FALSE)
        ->where('m_cust_id', $id);
      $this->where_branch('m_cust_branch', $branch_id);
      $this->db->update('master_customer_tbl');
    }
    return true;
  }

  public function update_userbalance($id = '', $amt = '', $qty = '', $itemID = '', $branch_id = null)
  {
    if (!empty($itemID)) {
      $itemDtl = $this->db->select('m_itgrp_title')
        ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_item_tbl.m_item_crate', 'left')
        ->where('m_item_id', $itemID)->get('master_item_tbl')->row();

      $balanceFields = ['10 KG' => 'm_user_10bal', '20 KG' => 'm_user_20bal', '25 KG' => 'm_user_25bal'];
      if (!empty($itemDtl->m_itgrp_title) && isset($balanceFields[$itemDtl->m_itgrp_title])) {
        $this->db->set($balanceFields[$itemDtl->m_itgrp_title], $balanceFields[$itemDtl->m_itgrp_title] . ' + ' . (float)$qty, FALSE)
          ->where('m_user_id', $id);
        $this->where_branch('m_user_branch', $branch_id);
        $this->db->update('master_users_tbl');
      }
    }

    if (!empty($amt) && !empty($id)) {
      $this->db->set('m_user_balance', 'm_user_balance + ' . (float)$amt, FALSE)
        ->where('m_user_id', $id);
      $this->where_branch('m_user_branch', $branch_id);
      $this->db->update('master_users_tbl');
    }
    return true;
  }

  // ===================== Reports / Billing =======================//

  public function get_bill_data($to_date, $group = '', $branch_id = null)
  {
    $result = array();

    $sql = "
        SELECT DISTINCT c.m_cust_mobile, s.m_sale_customer AS customer_id
        FROM master_sales_tbl s
        JOIN master_customer_tbl c ON s.m_sale_customer = c.m_cust_id
        WHERE s.m_sale_date = ? " . $this->branch_sql('s.m_sale_branch', $branch_id);
    $binds = [$to_date];
    if (!empty($group)) {
      $sql .= " AND c.m_cust_group = ? ";
      $binds[] = $group;
    }
    $sql .= " UNION
        SELECT DISTINCT c.m_cust_mobile, r.m_recvd_customer AS customer_id
        FROM master_recieved_tbl r
        JOIN master_customer_tbl c ON r.m_recvd_customer = c.m_cust_id
        WHERE r.m_recvd_date = ?
        AND r.m_recvd_account IN ('0', '1') " . $this->branch_sql('r.m_recvd_branch', $branch_id);
    $binds[] = $to_date;
    if (!empty($group)) {
      $sql .= " AND c.m_cust_group = ? ";
      $binds[] = $group;
    }

    $query = $this->db->query($sql, $binds)->result();
    if (empty($query)) return null;

    foreach ($query as $value) {
      $customer_id = $value->customer_id;
      $opening     = $this->get_opening_balance($customer_id, $to_date);

      $this->db->select('m_sale_spo, SUM(m_sale_qty) AS total_qty, SUM(m_sale_total) AS sub_total, SUM(m_sale_crate) AS total_crate, (m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) AS total_expense')
        ->where('m_sale_customer', $customer_id)->where('m_sale_date', $to_date);
      $this->where_branch('m_sale_branch', $branch_id);
      $sale_data = $this->db->group_by('m_sale_spo')->get('master_sales_tbl')->result();

      $total_sqty   = array_sum(array_column($sale_data, 'total_qty'));
      $sub_total    = array_sum(array_column($sale_data, 'sub_total'));
      $total_expense = array_sum(array_column($sale_data, 'total_expense'));
      $grand_total  = $sub_total + $total_expense;

      $this->db->select('m_sale_spo, m_sale_qty, m_sale_total, m_sale_price, m_item_name, m_item_fright, m_sale_customer, unit.m_itgrp_title AS unitname, m_item_crate,m_sale_crate')
        ->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
        ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left')
        ->where('m_sale_customer', $customer_id)->where('m_sale_date', $to_date);
      $this->where_branch('m_sale_branch', $branch_id);
      $sale_items = $this->db->order_by('m_item_name')->get('master_sales_tbl')->result();

      $this->db->select_sum('m_recvd_amount', 'total_received')
        ->where('m_recvd_customer', $customer_id)->where('m_recvd_account', 1)->where('m_recvd_type', 1)->where('m_recvd_date', $to_date);
      $this->where_branch('m_recvd_branch', $branch_id);
      $amount_received = $this->db->get('master_recieved_tbl')->row();

      $crate_received = $this->db->select("m_itgrp_id, m_itgrp_title, COALESCE((SELECT SUM(m_recvd_qty) FROM master_recieved_tbl WHERE m_recvd_customer = " . $this->db->escape($customer_id) . " AND m_recvd_date = " . $this->db->escape($to_date) . " AND m_recvd_type = 2 AND m_recvd_crate = master_itemgroup_tbl.m_itgrp_id " . $this->branch_sql('s.m_recvd_branch', $branch_id) . "), 0) AS total_qty")
        ->where('m_itgrp_type', 3)->order_by('m_itgrp_title')->get('master_itemgroup_tbl')->result();

      $this->db->select('sum(m_voucher_amount) as tamountcdt')
        ->where('m_voucher_accountid', $customer_id)->where('m_voucher_account', 1)->where('m_voucher_type', 1)->where('m_voucher_status', 1)->where('m_voucher_date', $to_date);
      $this->where_branch('m_voucher_branch', $branch_id);
      $vouch_amtcdrt = $this->db->get('master_voucher_tbl')->row();

      $this->db->select("m_recvd_voucher")
        ->where('m_recvd_customer', $customer_id)->where_in('m_recvd_account', ['0', '1'])->where('m_recvd_date', $to_date);
      $this->where_branch('m_recvd_branch', $branch_id);
      $receipt_no = $this->db->get('master_recieved_tbl')->row();

      if (!empty($sale_items) || !empty($amount_received) || !empty($crate_received)) {
        $result[] = (object)[
          'opening'        => $opening,
          'invoice_no'     => !empty($sale_data) ? $sale_data[0]->m_sale_spo : ($receipt_no->m_recvd_voucher ?? null),
          'total_sqty'     => $total_sqty,
          'sub_total'      => $sub_total,
          'total_expense'  => $total_expense,
          'grand_total'    => $grand_total,
          'sale_data'      => $sale_items,
          'total_receive'  => $amount_received->total_received ?? 0,
          'total_discount' => $vouch_amtcdrt->tamountcdt ?? 0,
          'crate_data'     => $crate_received,
        ];
      }
    }
    return $result;
  }

  public function get_cust_day_summary($cust_id, $to_date, $branch_id = null)
  {
    $this->db->select('m_cust_id, m_cust_name,m_cust_hndiname, m_cust_mobile,m_cust_address,m_cust_balance,m_cust_10bal,m_cust_20bal,m_cust_25bal')
      ->where('m_cust_id', $cust_id);
    $this->where_branch('m_cust_branch', $branch_id);
    $cust_detail = $this->db->get('master_customer_tbl')->row();

    $total_sqty    = 0;
    $sub_total     = 0;
    $total_expense = 0;
    $grand_total   = 0;

    $this->db->select('m_sale_spo,SUM(m_sale_qty) AS tqty, SUM(m_sale_total) AS sub_total, SUM(m_sale_crate) AS tcrate, (m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) AS texpense')
      ->where('m_sale_customer', $cust_id)->where('m_sale_date', $to_date);
    $this->where_branch('m_sale_branch', $branch_id);
    $saletotal = $this->db->group_by('m_sale_spo')->get('master_sales_tbl')->result();
    if (!empty($saletotal)) {
      foreach ($saletotal as $value) {
        $total_sqty    += $value->tqty;
        $sub_total     += $value->sub_total;
        $total_expense += $value->texpense;
      }
      $grand_total += $sub_total + $total_expense;
    }

    $this->db->select('m_sale_spo, m_sale_qty, m_sale_total, m_sale_price, m_item_name, m_item_fright, m_sale_customer, unit.m_itgrp_title AS unitname,m_item_crate')
      ->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
      ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left')
      ->where('m_sale_customer', $cust_id)->where('m_sale_date', $to_date);
    $this->where_branch('m_sale_branch', $branch_id);
    $saleitems = $this->db->order_by('m_item_name')->get('master_sales_tbl')->result();

    $this->db->select('sum(m_recvd_amount) as total_recieve')
      ->where('m_recvd_customer', $cust_id)->where('m_recvd_account', 1)->where('m_recvd_type', 1)->where('m_recvd_date', $to_date);
    $this->where_branch('m_recvd_branch', $branch_id);
    $amountrcvdquery = $this->db->get('master_recieved_tbl')->row();

    $this->db->select('sum(m_voucher_amount) as tamountcdt')
      ->where('m_voucher_accountid', $cust_id)->where('m_voucher_account', 1)->where('m_voucher_type', 1)->where('m_voucher_status', 1)->where('m_voucher_date', $to_date);
    $this->where_branch('m_voucher_branch', $branch_id);
    $vouch_amtcdrt = $this->db->get('master_voucher_tbl')->row();

    $cratercvdquery = $this->db->select("m_itgrp_id,m_itgrp_title, COALESCE((SELECT SUM(m_recvd_qty) FROM master_recieved_tbl WHERE m_recvd_customer = '$cust_id' AND m_recvd_date = '$to_date' AND m_recvd_type = 2 AND m_recvd_crate = master_itemgroup_tbl.m_itgrp_id" . $this->branch_sql('m_recvd_branch', $branch_id) . "), 0) AS total_qty")
      ->where('m_itgrp_type', 3)->order_by('m_itgrp_title')->get('master_itemgroup_tbl')->result();

    $this->db->select('m_recvd_voucher')->where('m_recvd_customer', $cust_id)->where_in('m_recvd_account', [0, 1])->where('m_recvd_date', $to_date);
    $this->where_branch('m_recvd_branch', $branch_id);
    $recipt_no = $this->db->get('master_recieved_tbl')->row();

    return (!empty($saleitems) || !empty($recipt_no)) ? (object)[
      'cust_detail'    => $cust_detail,
      'invoice_no'     => !empty($saletotal) ? $saletotal[0]->m_sale_spo : $recipt_no->m_recvd_voucher,
      'total_sqty'     => $total_sqty,
      'sub_total'      => $sub_total,
      'total_expense'  => $total_expense,
      'grand_total'    => $grand_total,
      'sale_data'      => $saleitems,
      'total_recieve'  => $amountrcvdquery->total_recieve,
      'total_discount' => $vouch_amtcdrt->tamountcdt,
      'crate_data'     => $cratercvdquery,
    ] : null;
  }

  public function get_custid_by_date($to_date, $branch_id = null)
  {
    $sql = "
        SELECT DISTINCT c.m_cust_mobile, s.m_sale_customer AS customer_id
        FROM master_sales_tbl s
        JOIN master_customer_tbl c ON s.m_sale_customer = c.m_cust_id
        WHERE s.m_sale_date = ? " . $this->branch_sql('s.m_sale_branch', $branch_id) . "
        UNION
        SELECT DISTINCT c.m_cust_mobile, r.m_recvd_customer AS customer_id
        FROM master_recieved_tbl r
        JOIN master_customer_tbl c ON r.m_recvd_customer = c.m_cust_id
        WHERE r.m_recvd_date = ? " . $this->branch_sql('r.m_recvd_branch', $branch_id) . "
        AND (r.m_recvd_account = '1' OR r.m_recvd_account = '0')
    ";
    return $this->db->query($sql, [$to_date, $to_date])->result_array();
  }

  public function get_last_saledate($cust_id, $branch = null)
  {
    $sql    = "SELECT sale.last_sale_date, rec.last_recvd_date
              FROM master_customer_tbl mc
              LEFT JOIN (
                  SELECT m_sale_customer, MAX(m_sale_date) as last_sale_date
                  FROM master_sales_tbl WHERE 1=1 " . $this->branch_sql('m_sale_branch', $branch) . "
                  GROUP BY m_sale_customer
              ) as sale ON sale.m_sale_customer = mc.m_cust_id
              LEFT JOIN (
                  SELECT m_recvd_customer, MAX(m_recvd_date) as last_recvd_date
                  FROM master_recieved_tbl
                  WHERE (m_recvd_account = 0 OR m_recvd_account = 1) " . $this->branch_sql('m_recvd_branch', $branch) . "
                  GROUP BY m_recvd_customer
              ) as rec ON rec.m_recvd_customer = mc.m_cust_id
              WHERE mc.m_cust_id = ?";
    $query = $this->db->query($sql, [$cust_id])->result();
    return !empty($query) ? $query[0] : null;
  }

  public function get_custid_by_last_sale($days, $group, $branch_id = null)
  {
    $last_ago = date('Y-m-d', strtotime("-{$days} days"));

    $group_condition = "";
    if ($group === 'o') {
      $group_condition = " AND mc.m_cust_group = 0 ";
    } elseif (!empty($group)) {
      $group_condition = " AND mc.m_cust_group = ? ";
    }

    $sql = "
        SELECT 
            mc.m_cust_id, mc.m_cust_name, mc.m_cust_hndiname, mc.m_cust_mobile,
            mc.m_cust_balance, mc.m_cust_10bal, mc.m_cust_20bal, mc.m_cust_25bal,
            mg.m_group_name, sale.last_sale_date, rec.last_recvd_date
        FROM master_customer_tbl mc
        LEFT JOIN (
            SELECT m_sale_customer, MAX(m_sale_date) AS last_sale_date
            FROM master_sales_tbl WHERE 1=1 " . $this->branch_sql('m_sale_branch', $branch_id) . "
            GROUP BY m_sale_customer
        ) sale ON sale.m_sale_customer = mc.m_cust_id
        LEFT JOIN (
            SELECT m_recvd_customer, MAX(m_recvd_date) AS last_recvd_date
            FROM master_recieved_tbl
            WHERE m_recvd_account IN (0,1) " . $this->branch_sql('m_recvd_branch', $branch_id) . "
            GROUP BY m_recvd_customer
        ) rec ON rec.m_recvd_customer = mc.m_cust_id
        LEFT JOIN master_group_tbl mg ON mg.m_group_id = mc.m_cust_group
        WHERE 1=1 " . $this->branch_sql('mc.m_cust_branch', $branch_id) . "
        AND (
            sale.last_sale_date IS NULL OR sale.last_sale_date <= ?
            OR rec.last_recvd_date IS NULL OR rec.last_recvd_date <= ?
        )
        {$group_condition}
    ";

    $params = [$last_ago, $last_ago];
    if ($group !== 'o' && !empty($group)) $params[] = $group;

    $customers = $this->db->query($sql, $params)->result();
    $result    = [];

    foreach ($customers as $customer) {
      $balance_amount = (float)($customer->m_cust_balance ?? 0);
      $balance_crate  = (float)(($customer->m_cust_25bal + $customer->m_cust_20bal + $customer->m_cust_10bal) ?? 0);
      if ($balance_amount <= 0 && $balance_crate <= 0) continue;

      $result[] = [
        'm_cust_id'           => $customer->m_cust_id,
        'm_cust_name'         => $customer->m_cust_name,
        'm_cust_hndiname'     => $customer->m_cust_hndiname,
        'm_cust_mobile'       => $customer->m_cust_mobile,
        'm_group_name'        => $customer->m_group_name,
        'last_recvd_date'     => $customer->last_recvd_date,
        'last_sale_date'      => $customer->last_sale_date,
        'total_balance'       => (int)$balance_amount,
        'total_crate_balance' => (int)$balance_crate,
      ];
    }
    return $result;
  }

  // ===================== System setting =======================//

  public function update_profile()
  {
    $update_data = array(
      "m_user_name"     => $this->input->post('m_user_name'),
      "m_user_loginid"  => $this->input->post('m_user_loginid'),
      "m_user_mobile"   => $this->input->post('m_user_mobile'),
      "m_user_image"    => $this->input->post('pre_m_user_image'),
    );

    // Only touch the password if a new one was actually submitted - otherwise
    // saving the profile form would blank out the existing credential.
    $newPassword = $this->input->post('m_user_password');
    if (!empty($newPassword)) {
      $update_data['m_user_password']     = password_hash($newPassword, PASSWORD_DEFAULT);
      $update_data['m_user_password_enc'] = encrypt_password_for_admin($newPassword);
    }

    if (!empty($_FILES['m_user_image']['name'])) {
      $config['upload_path']   = 'uploads/users/';
      $config['allowed_types'] = 'jpg|jpeg|png';
      $config['remove_spaces'] = TRUE;
      $config['file_name']     = $_FILES['m_user_image']['name'];
      $this->load->library('upload', $config);
      $this->upload->initialize($config);
      if ($this->upload->do_upload('m_user_image')) {
        $uploadData = $this->upload->data();
        if (!empty($update_data['m_user_image']) && file_exists($config['upload_path'] . $update_data['m_user_image'])) {
          unlink($config['upload_path'] . $update_data['m_user_image']);
        }
        $update_data['m_user_image'] = $uploadData['file_name'];
      }
    }

    $this->db->where('m_user_id', $this->session->userdata('user_id'));
    return $this->db->update('master_users_tbl', $this->no_nulls($update_data));
  }

  public function get_application_settings()
  {
    return $this->db->get('application_settings')->result();
  }

  // Superadmin-only "view password" feature - caller must already have
  // checked user_type == 8 before calling this.
  public function get_date_lock_password_enc()
  {
    return $this->db->select('date_lock_password_enc')
      ->get('application_settings')->row();
  }

  public function update_application_settings()
  {
    // Logo/icon upload handling (unchanged from original)
    $m_app_logo = $this->_upload_setting_image('m_app_logo', 'applogo');
    $m_app_icon = $this->_upload_setting_image('m_app_icon', 'appfavicon');
    $m_app_black_logo = $this->_upload_setting_image('m_app_black_logo', 'app_black_logo');
    $m_app_white_logo = $this->_upload_setting_image('m_app_white_logo', 'app_white_logo');

    // Refuse the whole save rather than storing the other settings and quietly
    // dropping the image the user picked.
    if (!empty($this->upload_errors)) {
      return $this->fail('Image upload failed - no settings were saved. ' . implode(' ', $this->upload_errors));
    }

    $data = array(
      "m_app_name"          => $this->input->post('m_app_name'),
      "m_app_title"         => $this->input->post('m_app_title'),
      "m_app_email"         => $this->input->post('m_app_mail'),
      "date_lock_enabled"   => $this->input->post('date_lock_enabled'),
      "m_app_mobile"        => $this->input->post('m_app_contact'),
      "m_app_alt_mobile"    => $this->input->post('m_app_alt_contact'),
      "m_app_address"       => $this->input->post('m_app_address'),
      "m_app_fb"            => $this->input->post('m_app_fesbook'),
      "m_app_insta"         => $this->input->post('m_app_instagram'),
      "m_app_youtube"       => $this->input->post('m_app_youtude'),
      "m_app_linkedin"      => $this->input->post('m_app_linkedin'),
      "m_app_whatsapp"      => $this->input->post('m_app_whatsapp'),
      "m_app_twitter"       => $this->input->post('m_app_twitter'),
      "m_app_logo"          => $m_app_logo,
      "m_app_icon"          => $m_app_icon,
      "m_app_black_logo"    => $m_app_black_logo,
      "m_app_white_logo"    => $m_app_white_logo,
    );

    // Only touch the date-lock password if a new one was actually submitted -
    // otherwise saving settings would blank out the existing one.
    $newLockPassword = $this->input->post('date_lock_password');
    if (!empty($newLockPassword)) {
      $data['date_lock_password']     = password_hash($newLockPassword, PASSWORD_DEFAULT);
      $data['date_lock_password_enc'] = encrypt_password_for_admin($newLockPassword);
    }

    $this->db->update('application_settings', $this->no_nulls($data));

    $update_data = array(
      "m_user_loginid" => $this->input->post('m_admin_login_id'),
    );

    // Only touch the password if a new one was actually submitted - otherwise
    // saving settings would blank out the superadmin's existing credential.
    $newAdminPass = $this->input->post('m_admin_pass');
    if (!empty($newAdminPass)) {
      $update_data['m_user_password']     = password_hash($newAdminPass, PASSWORD_DEFAULT);
      $update_data['m_user_password_enc'] = encrypt_password_for_admin($newAdminPass);
    }

    $this->db->where('m_user_id', $this->session->userdata('user_id'))->update('master_users_tbl', $this->no_nulls($update_data));
    return true;
  }

  /**
   * Reasons any image upload in this request was rejected, so the caller can
   * say which one and why. A failed upload used to fall through to the old
   * filename silently, and the user was told the settings saved fine while
   * their new logo had been discarded.
   */
  private $upload_errors = array();

  /** Helper to reduce upload repetition in update_application_settings */
  private function _upload_setting_image($field, $fallback_post)
  {
    if (!empty($_FILES[$field]['name'])) {
      $config = [
        'file_name'    => $_FILES[$field]['name'],
        'upload_path'  => 'uploads/',
        'allowed_types' => 'jpg|jpeg|png',
        'remove_spaces' => TRUE,
      ];
      $this->load->library('upload', $config);
      $this->upload->initialize($config);
      if ($this->upload->do_upload($field)) {
        return $this->upload->data()['file_name'];
      }
      $this->upload_errors[] = $field . ': ' . trim(strip_tags($this->upload->display_errors('', '')));
    }
    return $this->input->post($fallback_post);
  }

  public function update_cust_bal_cron($cust_id, $from_date, $type, $branch_id = null)
  {
    // NOTE: assign the *result* of get(), not the query builder. Inserting
    // where_branch() between the builder call and get() previously left these
    // variables holding the CI_DB driver object while the rows were discarded,
    // which made this whole cron fatal on its first customer (BUG-010).
    $this->db->select('m_cust_opening,m_cust_crateOP')
      ->where('m_cust_id', $cust_id);
    $this->where_branch('m_cust_branch', $branch_id);
    $opening_bal = $this->db->get('master_customer_tbl')->row();

    if (empty($opening_bal)) {
      return false;
    }

    if ($type == 1) {
      $sub_total     = 0;
      $total_expense = 0;
      $grand_total   = 0;

      if (!empty($from_date)) $this->db->where('m_sale_date <=', $from_date);
      $this->db->select('sum(m_sale_total) as sub_total,(m_sale_comm+m_sale_fright+m_sale_hamali+m_sale_others) as texpense')
        ->where('m_sale_customer', $cust_id);
      $this->where_branch('m_sale_branch', $branch_id);
      $salequery = $this->db->group_by('m_sale_spo')->get('master_sales_tbl')->result();
      if (!empty($salequery)) {
        foreach ($salequery as $key) {
          $sub_total     += $key->sub_total;
          $total_expense += $key->texpense;
          $grand_total   += ($key->sub_total + $key->texpense);
        }
      }

      if (!empty($from_date)) $this->db->where('m_recvd_date <=', $from_date);
      $this->db->select('sum(m_recvd_amount) as tamountrcvd')
        ->where('m_recvd_customer', $cust_id)->where('m_recvd_account', 1)->where('m_recvd_type', 1);
      $this->where_branch('m_recvd_branch', $branch_id);
      $amountrcvdquery = $this->db->get('master_recieved_tbl')->result();

      if (!empty($from_date)) $this->db->where('m_voucher_date <=', $from_date);
      $this->db->select('sum(m_voucher_amount) as tamountcdt')
        ->where('m_voucher_accountid', $cust_id)->where('m_voucher_account', 1)->where('m_voucher_type', 1)->where('m_voucher_status', 1);
      $this->where_branch('m_voucher_branch', $branch_id);
      $vouch_amtcdrt = $this->db->get('master_voucher_tbl')->result();

      if (!empty($from_date)) $this->db->where('m_voucher_date <=', $from_date);
      $this->db->select('sum(m_voucher_amount) as tamountdbt')
        ->where('m_voucher_accountid', $cust_id)->where('m_voucher_account', 1)->where('m_voucher_type', 2)->where('m_voucher_status', 1);
      $this->where_branch('m_voucher_branch', $branch_id);
      $vouch_amtdbt = $this->db->get('master_voucher_tbl')->result();

      // SUM() over an empty set yields a single row of NULLs, so these are
      // coalesced rather than assumed present.
      $tamountdbt  = isset($vouch_amtdbt[0]->tamountdbt) ? (float) $vouch_amtdbt[0]->tamountdbt : 0;
      $tamountrcvd = isset($amountrcvdquery[0]->tamountrcvd) ? (float) $amountrcvdquery[0]->tamountrcvd : 0;
      $tamountcdt  = isset($vouch_amtcdrt[0]->tamountcdt) ? (float) $vouch_amtcdrt[0]->tamountcdt : 0;

      $balance_amt = $opening_bal->m_cust_opening + (($grand_total + $tamountdbt) - ($tamountrcvd + $tamountcdt));
      $this->db->set('m_cust_balance', $balance_amt)->where('m_cust_id', $cust_id);
      $this->where_branch('m_cust_branch', $branch_id);
      $this->db->update('master_customer_tbl');
    } else if ($type == 2) {
      $balanceFields    = ['10 KG' => 'm_cust_10bal', '20 KG' => 'm_cust_20bal', '25 KG' => 'm_cust_25bal'];
      $all_crates       = $this->Master_model->all_itemgroup(3);
      $openin_crate_bal = explode(',', $opening_bal->m_cust_crateOP);

      foreach ($all_crates as $key) {
        $crattype_bal = 0;
        if (isset($balanceFields[$key->m_itgrp_title])) {
          $index        = array_search($key->m_itgrp_title, array_keys($balanceFields));
          $crattype_bal = isset($openin_crate_bal[$index]) ? (int)$openin_crate_bal[$index] : 0;
        }

        if (!empty($from_date)) $this->db->where('m_sale_date <=', $from_date);
        $this->db->select('sum(m_sale_crate) as tcrate,m_itgrp_title')
          ->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
          ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
          ->where('m_sale_customer', $cust_id)->where('m_item_crate', $key->m_itgrp_id);
        $this->where_branch('master_sales_tbl.m_sale_branch', $branch_id);
        $crategiven = $this->db->group_by('m_item_crate')->get('master_sales_tbl')->row();

        if (!empty($from_date)) $this->db->where('m_recvd_date <=', $from_date);
        $this->db->select('sum(m_recvd_qty) as tcrateqty,m_itgrp_title')
          ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left')
          ->where('m_recvd_customer', $cust_id)->where('m_recvd_type', 2)->where('m_recvd_crate', $key->m_itgrp_id);
        $this->where_branch('master_recieved_tbl.m_recvd_branch', $branch_id);
        $cratercvdquery = $this->db->group_by('m_recvd_crate')->get('master_recieved_tbl')->row();

        $createbalance = (int)$crattype_bal + (($crategiven ? $crategiven->tcrate : 0) - ($cratercvdquery ? $cratercvdquery->tcrateqty : 0));

        if (!empty($key->m_itgrp_title) && isset($balanceFields[$key->m_itgrp_title])) {
          $this->db->set($balanceFields[$key->m_itgrp_title], $createbalance)->where('m_cust_id', $cust_id);
          $this->where_branch('m_cust_branch', $branch_id);
          $this->db->update('master_customer_tbl');
        }
      }
    }
    return true;
  }
}
