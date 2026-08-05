<?php date_default_timezone_set('Asia/Kolkata');
class Login_model extends CI_model
{

  private function branch_id()
  {
    return (int) $this->session->userdata('branch_id');
  }

  public function validate_user()
  {
    // Admin login is global — no branch filter at login time
    // (branch is set into session after successful login)
    $pass = $this->input->post('login_pass');
    $this->db->select('m_user_id,m_user_type,m_user_name,m_user_branch,m_user_password');
    $this->db->where('m_user_loginid', $this->input->post('login_id'));
    $this->db->where('m_user_login_allow', 1);
    $this->db->where('m_user_status', 1);
    $row = $this->db->get('master_users_tbl')->row();

    if (!empty($row) && password_verify($pass, $row->m_user_password)) {
      return [$row];
    }
    return false;
  }

  public function validate_customer()
  {
    // Customer login is branch-scoped
    $pass = $this->input->post('login_pass');
    $this->db->select('m_cust_id,m_cust_name,m_cust_branch,m_cust_password');
    $this->db->where('m_cust_loginid', $this->input->post('login_id'));
    $this->db->where('m_cust_status', 1);
    $this->db->where('m_cust_branch', $this->branch_id());
    $row = $this->db->get('master_customer_tbl')->row();

    if (!empty($row) && password_verify($pass, $row->m_cust_password)) {
      return [$row];
    }
    return false;
  }

  public function user_details()
  {
    $this->db->where('m_user_id', $this->session->userdata('user_id'));
    return $this->db->get('master_users_tbl')->row();
  }

  public function get_user_profile_details()
  {
    $this->db->where('m_user_id', $this->session->userdata('user_id'));
    return $this->db->get('master_users_tbl')->result();
  }

  // ===================== URL shortener =======================//
  // shorten_urls has m_shorturl_branch — scope all URL lookups to branch.

  public function save_new_alias($long_url, $short_url)
  {
    $data = array(
      'short_url'         => $short_url,
      'long_url'          => $long_url,
      'created'           => date('Y-m-d H:i:s'),
      'm_shorturl_branch' => $this->branch_id(),
    );
    $this->db->insert('shorten_urls', $data);
  }

  public function alias_from_url($url)
  {
    $query = $this->db->select('short_url')
      ->where('long_url', $url)
      ->where('m_shorturl_branch', $this->branch_id())
      ->get('shorten_urls')->row();

    if (!empty($query)) return $query->short_url;
  }

  public function does_alias_exist($short_url)
  {
    $query = $this->db->select('id')
      ->where('short_url', $short_url)
      ->where('m_shorturl_branch', $this->branch_id())
      ->get('shorten_urls')->row();

    return !empty($query);
  }

  public function get_long_url($short_url)
  {
    $query = $this->db->select('long_url')
      ->where('short_url', $short_url)
      ->where('m_shorturl_branch', $this->branch_id())
      ->get('shorten_urls')->row();

    if (!empty($query)) {
      redirect($query->long_url);
      return;
    }

    // This model backs routes.php's 404_override, so every unmatched URL in
    // the application lands here. Redirecting to the current URL - as this
    // previously did - turned every 404 into an infinite redirect loop
    // (BUG-020). Serve a real 404 instead.
    show_404();
  }
}