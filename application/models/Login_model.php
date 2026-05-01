<?php date_default_timezone_set('Asia/Kolkata');
class Login_model extends CI_model
{

	public function validate_user()
	{
		$pass = $this->input->post('login_pass');

		$this->db->select('m_admin_id,m_admin_type,m_admin_name,m_admin_img');

		$this->db->where('m_admin_login_id', $this->input->post('login_id'));

		$this->db->where('m_admin_pass', $pass);
		$this->db->where('m_admin_login_allowed', 1);
		$this->db->where('m_admin_status', 1);
		$sql = $this->db->get('master_admin_tbl');



		if ($sql->num_rows() == 1) {
			return $sql->result();
		} else {
			return false;
		}
	}


	public function validate_customer()
	{
		$pass = $this->input->post('login_pass');

		$this->db->select('m_cust_id,m_cust_name');

		$this->db->where('m_cust_loginid', $this->input->post('login_id'));

		$this->db->where('m_cust_password', $pass);
		// $this->db->where('m_cust_login_allowed', 1);
		$this->db->where('m_cust_status', 1);
		$sql = $this->db->get('master_customer_tbl');

		if ($sql->num_rows() == 1) {
			return $sql->result();
		} else {
			return false;
		}
	}



	public function user_details()
	{

		// $this->db->select('m_admin_id, m_admin_name, m_admin_img');

		$this->db->where('m_admin_id', $this->session->userdata('user_id'));

		return $this->db->get('master_admin_tbl')->row();
	}



	public function get_user_profile_details()
	{

		// $this->db->select('m_admin_id, m_admin_name, m_admin_login_id, m_admin_email, m_admin_pass, m_admin_contact, m_admin_img');

		$this->db->where('m_admin_id', $this->session->userdata('user_id'));

		return $this->db->get('master_admin_tbl')->result();
	}

	//===========================/Login===========================//

	public function save_new_alias($long_url, $short_url)
	{
		$data = array('short_url' => $short_url, 'long_url' => $long_url, 'created' => date('Y-m-d H:i:s'));
		$this->db->insert('shorten_urls', $data);
	}

	public function alias_from_url($url)
	{

		$query = $this->db->select('short_url')->where('long_url', $url)->get('shorten_urls')->row();

		if (!empty($query)) {
			return $query->short_url;
		}
	}

	public function does_alias_exist($short_url)
	{

		$query = $this->db->select('id')->where('short_url', $short_url)->get('shorten_urls')->row();

		if (!empty($query)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function get_long_url($short_url)
	{
		$query = $this->db->select('long_url')->where('short_url', $short_url)->get('shorten_urls')->row();
		if (!empty($query)) {
			redirect($query->long_url);
		} else {
			if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
				$link = "https";
			} else {
				$link = "http";
			}
			$link .= "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			redirect($link);
		}
	}
	//===========================/Login===========================//

}
