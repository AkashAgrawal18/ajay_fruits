<?php date_default_timezone_set('Asia/Kolkata');

class Api_Model extends CI_model
{



	public function check_mobile($mobile)
	{
		$this->db->select('m_user_id');
		$this->db->where("m_user_loginid", $mobile);
		$sql = $this->db->get("master_users_tbl");
		return $sql->result();
	}


	public function user_login($mobile, $password)
	{
		$this->db->select('m_user_id,m_user_loginid');
		$this->db->where('m_user_loginid', $mobile);
		$this->db->where('m_user_password', $password);
		$this->db->where('m_user_login_allow', 1);
		$this->db->where('m_user_status', 1);
		$this->db->where_in('m_user_type', 1);

		$res = $this->db->get('master_users_tbl')->result();
		return $res;
	}

	public function user_details($user_id)
	{
		$this->db->select('m_user_id,m_user_name,m_user_mobile,m_user_image,m_user_remark,m_user_pan_no,m_user_accountno,m_user_address,m_user_adharno,m_user_trademark,m_user_contractPerd,m_user_added_on,m_user_design,m_state_name,m_city_name,m_user_login_allow,m_user_password,m_user_group');
		// $this->db->join('master_designation_tbl', 'master_designation_tbl.m_desig_id = master_users_tbl.m_emp_design', 'left');
		$this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_users_tbl.m_user_state', 'left');
		$this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = master_users_tbl.m_user_city', 'left');
		$this->db->where("m_user_id", $user_id);
		$sql = $this->db->get("master_users_tbl");
		return $sql->result();
	}



	public function get_all_city()
	{
		$this->db->select('master_city_tbl.*,m_state_name');
		$this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_city_tbl.m_city_state', 'left');
		$this->db->order_by('m_city_name');
		return $this->db->get('master_city_tbl')->result();
	}

	public function get_all_state()
	{
		$this->db->select('*');
		$this->db->order_by('m_state_name');
		return $this->db->get('master_state_tbl')->result();
	}

	public function get_payment_methods()
	{
		$this->db->select('m_group_id,m_group_name,m_group_type,m_group_status,m_group_remark');
		$this->db->where('(m_group_type = 3 OR m_group_type = 4)');
		$this->db->where('(m_group_id = 16 OR m_group_id = 17)');
		$this->db->where('m_group_status', 1);
		return $this->db->get('master_group_tbl')->result();
	}

	public function get_user_customers($user_group)
	{
		$result = array();
		$this->db->select('m_cust_id,m_cust_name,m_cust_hndiname,m_cust_mobile,m_cust_image,m_cust_remark,m_cust_pan_no,m_cust_accountno,m_state_name,m_city_name,m_cust_address,m_cust_adharno,m_cust_trademark,m_cust_contractPerd,m_cust_status,m_cust_added_on,m_group_name,m_cust_group,m_cust_balance,m_cust_10bal,m_cust_20bal,m_cust_25bal');
		$this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = mct.m_cust_state', 'left');
		$this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = mct.m_cust_city', 'left');
		$this->db->join('master_group_tbl', 'master_group_tbl.m_group_id = mct.m_cust_group');
		$this->db->where_in('m_cust_group', explode(',', $user_group));
		$this->db->order_by('m_cust_name');
		$custo_list = $this->db->get('master_customer_tbl mct')->result();

		if (!empty($custo_list)) {
			foreach ($custo_list as $key) {
				// $get_ledge = $this->get_customer_balance($key->m_cust_id);
				$res = array(
					"m_cust_id" => $key->m_cust_id,
					"m_cust_name" => $key->m_cust_name,
					"m_cust_hndiname" => $key->m_cust_hndiname,
					"m_cust_mobile" => $key->m_cust_mobile,
					"m_cust_image" => $key->m_cust_image,
					"m_cust_remark" => $key->m_cust_remark,
					"m_cust_pan_no" => $key->m_cust_pan_no,
					"m_cust_accountno" => $key->m_cust_accountno,
					"m_state_name" => $key->m_state_name,
					"m_city_name" => $key->m_city_name,
					"m_cust_address" => $key->m_cust_address,
					"m_cust_adharno" => $key->m_cust_adharno,
					"m_cust_trademark" => $key->m_cust_trademark,
					"m_cust_contractPerd" => $key->m_cust_contractPerd,
					"m_cust_status" => $key->m_cust_status,
					"m_cust_added_on" => $key->m_cust_added_on,
					"m_group_name" => $key->m_group_name,
					"m_cust_grou" => $key->m_cust_group,
					// "total_given_amount" => (int)$get_ledge['grand_total'],
					// "total_recieved_amount" => (int)$get_ledge['amount_rcvd'],
					"total_balance" => (int)$key->m_cust_balance,
					"crates" => [
						[
							"name" => "10 KG",
							"balance" => $key->m_cust_10bal
						],
						[
							"name" => "20 KG",
							"balance" => $key->m_cust_20bal
						],
						[
							"name" => "25 KG",
							"balance" => $key->m_cust_25bal
						]
					],
					// "crate_given" => (int)($get_ledge['crate_given']),
					// "crate_recieved" => (int)($get_ledge['crate_recieved']),
					"balance_crate" => (int)($key->m_cust_10bal + $key->m_cust_20bal + $key->m_cust_25bal),
					// "crates" => $get_ledge['crateitems'],
				);

				$result[] = $res;
			}
		}
		return $result;
	}

	function get_crate_balance($cust_id)
	{

		$opening_bal = $this->db->select('m_cust_opening,m_cust_crateOP')->where('m_cust_id', $cust_id)->get('master_customer_tbl')->row();

		$all_crates = $this->all_itemgroup(3);
		$openin_crate_bal = explode(',', $opening_bal->m_cust_crateOP);
		foreach ($all_crates as $itect) {
			$crateledger = $this->get_crate_ledger($itect->m_itgrp_id, $cust_id);

			if ($itect->m_itgrp_title == '10 KG') {
				$crattype_bal = isset($openin_crate_bal[0]) ? $openin_crate_bal[0] : 0;
			} else if ($itect->m_itgrp_title == '20 KG') {
				$crattype_bal = isset($openin_crate_bal[2]) ? $openin_crate_bal[2] : 0;
			} else if ($itect->m_itgrp_title == '25 KG') {
				$crattype_bal = isset($openin_crate_bal[1]) ? $openin_crate_bal[1] : 0;
			}

			$res = array(
				'name' => $itect->m_itgrp_title,
				'recived' => (int)$crateledger['crate_rcvd'],
				'given' => (int)$crateledger['crate_given'],
				'balance' => ((int)$crattype_bal + (int)$crateledger['crate_given'] - (int)$crateledger['crate_rcvd']),
			);
			$result[] = $res;
		}
		return $result;
	}

	public function get_user_items()
	{
		$this->db->select('si_issue_spo,si_issue_trackno,si_issue_lotno,si_issue_qty,si_issue_date,si_issue_weight,m_item_name,m_item_fright,group.m_itgrp_title as groupname,crate.m_itgrp_title as cratetype,si_issue_user,unit.m_itgrp_title as unitname');
		$this->db->join('master_item_tbl mit', 'mit.m_item_id = staff_itemissue_tbl.si_issue_item', 'left')
			->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
			->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
			->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');
		$this->db->where('si_issue_user', $this->input->post('user_id'));
		$this->db->where('si_issue_type', 1);
		$this->db->where('si_issue_status', 1);
		$this->db->order_by('m_item_name');
		return $this->db->get('staff_itemissue_tbl')->result();
	}


	public function get_user_sales($user_id, $fdate)
	{
		$this->db->select('m_sale_spo,m_sale_trackno,sum(m_sale_qty) as total_qty,sum(m_sale_total) as sub_total,m_sale_date,sum(m_sale_weight) as total_weight,sum(m_sale_crate) as total_crate,m_sale_comrate,m_sale_comm,m_sale_fright,m_sale_hamali,m_sale_others,(m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) as total_expense,m_sale_note,m_sale_user,m_cust_name,m_cust_hndiname,m_cust_mobile,m_cust_address');
		$this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_sales_tbl.m_sale_customer', 'left');

		$this->db->where('m_sale_user', $user_id);
		$this->db->where('m_sale_date', $fdate);
		$this->db->order_by('m_sale_date', 'desc');
		$this->db->group_by('m_sale_spo');
		return $this->db->get('master_sales_tbl')->result();
	}

	public function get_sale_details()
	{

		$this->db->select('m_sale_spo,m_sale_trackno,sum(m_sale_qty) as total_qty,sum(m_sale_total) as sub_total,m_sale_date,sum(m_sale_weight) as total_weight,sum(m_sale_crate) as total_crate,m_sale_comrate,m_sale_comm,m_sale_fright,m_sale_hamali,m_sale_others,(m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) as total_expense,m_sale_note,m_sale_user,m_sale_customer,m_cust_name,m_cust_hndiname,m_cust_mobile,m_cust_address,m_group_name');
		$this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_sales_tbl.m_sale_customer', 'left');
		$this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = mct.m_cust_state', 'left');
		$this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = mct.m_cust_city', 'left');
		$this->db->join('master_group_tbl', 'master_group_tbl.m_group_id = mct.m_cust_group');
		$this->db->where('m_sale_user', $this->input->post('user_id'));
		$this->db->where('m_sale_spo', $this->input->post('m_sale_spo'));
		$this->db->group_by('m_sale_spo');
		$sale_datil = $this->db->get('master_sales_tbl')->result();

		$this->db->select('m_sale_spo,m_sale_qty,m_sale_price,m_sale_total,m_sale_date,m_sale_weight,m_sale_crate,m_item_name,(CASE WHEN group.m_itgrp_title IS NULL THEN "" ELSE group.m_itgrp_title END ) AS groupname,(CASE WHEN crate.m_itgrp_title IS NULL THEN "" ELSE crate.m_itgrp_title END ) AS cratetype,(CASE WHEN unit.m_itgrp_title IS NULL THEN "" ELSE unit.m_itgrp_title END ) AS unitname');
		$this->db->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
			->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
			->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
			->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');
		$this->db->where('m_sale_user', $this->input->post('user_id'));
		$this->db->where('m_sale_spo', $this->input->post('m_sale_spo'));

		$sale_items =  $this->db->get('master_sales_tbl')->result();

		if (!empty($sale_datil)) {
			$res = array(
				"m_sale_date"    => $sale_datil[0]->m_sale_date,
				"m_sale_trackno"    => $sale_datil[0]->m_sale_trackno,
				"m_sale_customer"    => $sale_datil[0]->m_sale_customer,
				"m_sale_comrate"    => $sale_datil[0]->m_sale_comrate,
				"m_sale_comm"    => $sale_datil[0]->m_sale_comm,
				"m_sale_fright"    => $sale_datil[0]->m_sale_fright,
				"m_sale_hamali"    => $sale_datil[0]->m_sale_hamali,
				"m_sale_others"    => $sale_datil[0]->m_sale_others,
				"m_sale_note"    => $sale_datil[0]->m_sale_note,
				"m_sale_user"    => $sale_datil[0]->m_sale_user,
				"total_qty"    => $sale_datil[0]->total_qty,
				"sub_total"    => $sale_datil[0]->sub_total,
				"total_expense"    => $sale_datil[0]->total_expense,
				"net_total"    => (string)($sale_datil[0]->total_expense + $sale_datil[0]->sub_total),
				"total_weight"    => $sale_datil[0]->total_weight,
				"total_crate"    => $sale_datil[0]->total_crate,
				"m_cust_name"    => $sale_datil[0]->m_cust_name,
				"m_cust_hndiname"    => $sale_datil[0]->m_cust_hndiname,
				"m_cust_mobile"    => $sale_datil[0]->m_cust_mobile,
				"m_cust_address"    => $sale_datil[0]->m_cust_address,
				"m_group_name"    => $sale_datil[0]->m_group_name,
				"m_sale_items" => $sale_items,
			);

			$result[] = $res;
		}
		return $result;
	}


	public function customer_details($cust_id)
	{
		$this->db->select('m_cust_id,m_cust_name,m_cust_hndiname,m_cust_mobile,m_cust_image,m_cust_remark,m_cust_pan_no,m_cust_accountno,m_state_name,m_city_name,m_cust_address,m_cust_adharno,m_cust_trademark,m_cust_contractPerd,m_cust_status,m_cust_added_on,m_group_name');
		// $this->db->join('master_designation_tbl', 'master_designation_tbl.m_desig_id = master_users_tbl.m_emp_design', 'left');
		$this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_customer_tbl.m_cust_state', 'left');
		$this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = master_customer_tbl.m_cust_city', 'left');
		$this->db->join('master_group_tbl', 'master_group_tbl.m_group_id = master_customer_tbl.m_cust_group');
		$this->db->where("m_cust_id", $cust_id);
		$sql = $this->db->get("master_customer_tbl");
		return $sql->result();
	}

	public function get_all_items()
	{
		$res = $this->db->select('master_item_tbl.*,(CASE WHEN group.m_itgrp_title IS NULL THEN "" ELSE group.m_itgrp_title END ) AS groupname,(CASE WHEN crate.m_itgrp_title IS NULL THEN "" ELSE crate.m_itgrp_title END ) AS cratetype,(CASE WHEN unit.m_itgrp_title IS NULL THEN "" ELSE unit.m_itgrp_title END ) AS unitname')
			->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = master_item_tbl.m_item_group', 'left')
			->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_item_tbl.m_item_crate', 'left')
			->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = master_item_tbl.m_item_unit', 'left')
			->get('master_item_tbl')->result();
		return $res;
	}

	public function insert_sale()
	{

		// $issue_id = $this->input->post('m_sale_id');
		$sales = $this->input->post('m_sale_item');
		$issue_qty = $this->input->post('m_sale_qty');
		$issue_weight = $this->input->post('m_sale_weight');
		$issue_crate = $this->input->post('m_sale_crate');
		$issue_price = $this->input->post('m_sale_price');
		$m_sale_lot = $this->input->post('m_sale_lot');
		// $m_sale_issueid = $this->input->post('m_sale_issueid');

		$sale_dtl = $this->db->select('m_sale_spo')->order_by('m_sale_id', 'desc')->group_by('m_sale_spo')->get('master_sales_tbl')->result();
		if (!empty($sale_dtl)) {
			$spo_coun = explode('/', $sale_dtl[0]->m_sale_spo);
			$sale_spo = ((int)$spo_coun[0] + 1) . '/' . date('dm', strtotime($this->input->post('m_sale_date')));
		} else {
			$sale_spo = '1/' . date('dm', strtotime($this->input->post('m_sale_date')));
		}
		$saleTotalAmt = 0;
		foreach ($sales as $key => $cau) {
			if (!empty($issue_weight[$key])) {
				$subtotal = $issue_price[$key] * $issue_weight[$key];
			} else {
				$subtotal = $issue_price[$key] * $issue_qty[$key];
			}
			$saleTotalAmt += (float)$subtotal;

			$insert_data = array(
				"m_sale_date"    => $this->input->post('m_sale_date'),
				"m_sale_trackno"    => $this->input->post('m_sale_trackno'),
				"m_sale_customer"    => $this->input->post('m_sale_customer'),
				"m_sale_comrate"    => $this->input->post('m_sale_comrate'),
				"m_sale_comm"    => $this->input->post('m_sale_comm'),
				"m_sale_fright"    => $this->input->post('m_sale_fright'),
				"m_sale_hamali"    => $this->input->post('m_sale_hamali'),
				"m_sale_others"    => $this->input->post('m_sale_others'),
				"m_sale_note"    => $this->input->post('m_sale_note'),
				"m_sale_user"    => $this->input->post('user_id'),
				"m_sale_item"    => $cau,
				"m_sale_qty"    => $issue_qty[$key],
				"m_sale_weight"    => $issue_weight[$key],
				"m_sale_crate"    => $issue_crate[$key],
				"m_sale_price"    => $issue_price[$key],
				"m_sale_lot"    => $m_sale_lot[$key],
				// "m_sale_issueid"    => $m_sale_issueid[$key],
				"m_sale_total"    => $subtotal,
			);
			$insert_data['m_sale_added_by'] = $this->input->post('user_id');
			$insert_data['m_sale_spo'] = $sale_spo;
			$insert_data['m_sale_added_on'] = date('Y-m-d H:i');
			$this->db->insert('master_sales_tbl', $insert_data);
			$this->update_cust_balance($this->input->post('m_sale_customer'), null, $issue_qty[$key], $cau);
			$res = 1;
		}
		if (!empty($res)) {
			$saleTotalAmt += ((float)$this->input->post('m_sale_comm') + (float)$this->input->post('m_sale_fright') + (float)$this->input->post('m_sale_hamali') + (float)$this->input->post('m_sale_others'));
			$this->update_cust_balance($this->input->post('m_sale_customer'), $saleTotalAmt);
			// $this->send_sale_sms($sale_spo);
			return $sale_spo;
		} else {
			return $res;
		}
	}

	public function insert_payment_recieved()
	{
		$check = $this->db->where('m_recvd_customer', $this->input->post('m_recvd_customer'))->where('m_recvd_method', $this->input->post('m_recvd_method'))->where('m_recvd_amount', $this->input->post('m_recvd_amount'))->where('m_recvd_date', $this->input->post('m_recvd_date'))->where('m_recvd_type', 1)->order_by('m_recvd_id', 'desc')->get('master_recieved_tbl')->row();

		if (!empty($check)) {
			return 'dupli';
		}

		$last_id = $this->db->select('m_recvd_id')->where('m_recvd_type', 1)->order_by('m_recvd_id', 'desc')->get('master_recieved_tbl')->row();

		$vlastid = empty($last_id) ? 0 : $last_id->m_recvd_id;

		$voucher_no = date('d') . $vlastid . '1' . '1';
		$insert_data = array(
			"m_recvd_user"    => $this->input->post('user_id'),
			"m_recvd_customer"    => $this->input->post('m_recvd_customer'),
			// "m_recvd_voucher"    => $this->input->post('m_recvd_voucher') ?: '',
			"m_recvd_method"    => $this->input->post('m_recvd_method') ?: 1,
			"m_recvd_amount"    => $this->input->post('m_recvd_amount'),
			"m_recvd_remark"    => $this->input->post('m_recvd_remark'),
			"m_recvd_date"    => $this->input->post('m_recvd_date'),
			"m_recvd_type"    => 1,
			"m_recvd_account"    => 1,

		);
		$insert_data['m_recvd_voucher'] = $voucher_no;
		$insert_data['m_recvd_added_by'] = $this->input->post('user_id');
		$insert_data['m_recvd_added_on'] = date('Y-m-d H:i');
		$res = $this->db->insert('master_recieved_tbl', $insert_data);
		$this->update_cust_balance($this->input->post('m_recvd_customer'), ($this->input->post('m_recvd_amount') * (-1)));
		$m_voucher_amount = $this->input->post('m_voucher_amount');

		if (!empty($m_voucher_amount)) {
			$insert_data = array(
				"m_voucher_accountid"    => $this->input->post('m_recvd_customer'),
				"m_voucher_account"    => 1,
				"m_voucher_amount"    => $m_voucher_amount,
				"m_voucher_remark"    => $this->input->post('m_recvd_remark'),
				"m_voucher_date"    => $this->input->post('m_recvd_date'),
				"m_voucher_type"    => 1,
				"m_voucher_status"    => 1,

			);
			$insert_data['m_voucher_added_by'] = $this->input->post('user_id');
			$insert_data['m_voucher_added_on'] = date('Y-m-d H:i');
			$res1 = $this->db->insert('master_voucher_tbl', $insert_data);
			$this->update_cust_balance($this->input->post('m_recvd_customer'), ($m_voucher_amount) * (-1));
		}


		if (!empty($res)) {
			// $this->send_paymentreicvd_sms($voucher_no, $this->input->post('m_recvd_customer'), $this->input->post('m_recvd_amount'));

			return $voucher_no;
		} else {
			return $res;
		}
	}

	public function insert_crate_recived()
	{
		$last_id = $this->db->select('m_recvd_id')->where('m_recvd_type', 2)->order_by('m_recvd_id', 'desc')->get('master_recieved_tbl')->row();

		$vlastid = empty($last_id) ? 0 : $last_id->m_recvd_id;
		$voucher_no = date('d') . $vlastid . '1' . '2';

		$m_recvd_qty = $this->input->post('m_recvd_qty');
		$m_recvd_crate = $this->input->post('m_recvd_crate');
		$crate_mapping = [
			20 => 'm_cust_10bal',
			13 => 'm_cust_20bal',
			14 => 'm_cust_25bal'
		];
		foreach ($m_recvd_crate as $cou => $kky) {
			if ($m_recvd_qty[$cou] != 0) {
				$insert_data = array(
					"m_recvd_user"    => $this->input->post('user_id'),
					"m_recvd_customer"    => $this->input->post('m_recvd_customer'),
					"m_recvd_qty"    => $m_recvd_qty[$cou],
					"m_recvd_crate"    => $kky,
					"m_recvd_remark"    => $this->input->post('m_recvd_remark'),
					"m_recvd_date"    => $this->input->post('m_recvd_date'),
					"m_recvd_type"    => 2,

				);
				$insert_data['m_recvd_voucher'] = $voucher_no;
				$insert_data['m_recvd_added_by'] = $this->input->post('user_id');
				$insert_data['m_recvd_added_on'] = date('Y-m-d H:i');
				$res =	$this->db->insert('master_recieved_tbl', $insert_data);
				if (isset($crate_mapping[$kky])) {
					$this->db->set($crate_mapping[$kky], "$crate_mapping[$kky] - $m_recvd_qty[$cou]", FALSE)
						->where('m_cust_id', $this->input->post('m_recvd_customer'))
						->update('master_customer_tbl');
				}
			}
		}

		if (!empty($res)) {
			// $this->send_cratereicvd_sms($voucher_no, $this->input->post('m_recvd_customer'));
			return $voucher_no;
		} else {
			return $res;
		}
	}

	public function insert_expense()
	{

		$m_exp_amount = $this->input->post('m_exp_amount');
		$voucher_no =  83 . '/' . $this->input->post('user_id') . '/' . date('dms');
		if (!empty($m_exp_amount)) {
			$insertt_data = array(

				"m_exp_type"    => 1,
				"m_exp_name"    => 83,
				"m_exp_method"    => 1,
				"m_exp_amount"    => $m_exp_amount,
				"m_exp_remark"    => $this->input->post('m_exp_remark'),
				"m_exp_date"    => date('Y-m-d'),
				"m_exp_user"    => $this->input->post('user_id'),
				"m_exp_status"    => 1,

			);

			$check = $this->db->where('m_exp_user', $this->input->post('user_id'))->where('m_exp_added_by', $this->input->post('user_id'))->where('m_exp_date', date('Y-m-d'))->where('m_exp_name', 83)->get('master_expenses_tbl')->row();

			if (!empty($check)) {
				return $this->db->where('m_exp_id', $check->m_exp_id)->update('master_expenses_tbl', $insertt_data);
			} else {
				$insertt_data['m_exp_voucher'] = $voucher_no;
				$insertt_data['m_exp_added_by'] = $this->input->post('user_id');
				$insertt_data['m_exp_added_on'] = date('Y-m-d H:i');
				return $this->db->insert('master_expenses_tbl', $insertt_data);
			}
		}
	}

	public function all_itemgroup($type, $order_by = '')
	{
		// if (!empty($order_by)) {
		$this->db->order_by('m_itgrp_title');
		// }
		$res = $this->db->where('m_itgrp_type', $type)->get('master_itemgroup_tbl')->result();
		return $res;
	}

	public function get_user_payment_received($user_id, $fdate)
	{
		$result = array();
		$this->db->select('m_recvd_user,m_recvd_customer,m_recvd_amount,m_recvd_method,m_recvd_remark,m_recvd_date,m_recvd_type,m_recvd_added_by,m_recvd_added_on,m_cust_name,m_cust_hndiname,m_cust_mobile,m_cust_address,mgt.m_group_name,mgt.m_group_type');
		$this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_recieved_tbl.m_recvd_customer', 'left');
		$this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_recieved_tbl.m_recvd_method', 'left');
		$this->db->where('m_recvd_user', $user_id);
		$this->db->where('m_recvd_type', 1);
		$this->db->where('m_recvd_account', 1);
		$this->db->where('m_recvd_date', $fdate);
		$this->db->order_by('m_recvd_date', 'desc');
		$sql = $this->db->get('master_recieved_tbl')->result();

		if (!empty($sql)) {
			foreach ($sql as $kky) {
				$cusrbln =	$this->get_customer_balance($kky->m_recvd_customer);

				$res = array(
					"m_recvd_user" => $kky->m_recvd_user,
					"m_recvd_customer" => $kky->m_recvd_customer,
					"m_recvd_amount" => $kky->m_recvd_amount,
					"m_recvd_method" => $kky->m_recvd_method,
					"method_name" => $kky->m_group_name,
					"method_type" => $kky->m_group_type,
					"m_recvd_remark" => $kky->m_recvd_remark,
					"m_recvd_date" => $kky->m_recvd_date,
					"m_recvd_type" => $kky->m_recvd_type,
					"m_recvd_added_by" => $kky->m_recvd_added_by,
					"m_recvd_added_on" => $kky->m_recvd_added_on,
					"m_cust_name" => $kky->m_cust_name,
					"m_cust_hndiname" => $kky->m_cust_hndiname,
					"m_cust_mobile" => $kky->m_cust_mobile,
					"m_cust_address" => $kky->m_cust_address,
					"total_balance" => (string)$cusrbln['total_balance'],
				);

				$result[] = $res;
			}
		}
		return $result;
	}

	public function get_user_crate_received($user_id, $fdate)
	{
		$result = array();
		$this->db->select('m_recvd_user,m_recvd_customer,m_recvd_qty,m_recvd_voucher,m_recvd_crate,m_recvd_remark,m_recvd_date,m_recvd_type,m_recvd_added_by,m_recvd_added_on,crate.m_itgrp_title as cratetype,m_cust_name,m_cust_hndiname,m_cust_mobile,m_cust_address');
		$this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_recieved_tbl.m_recvd_customer', 'left')
			->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left');
		$this->db->where('m_recvd_user', $user_id);
		$this->db->where('m_recvd_type', 2);
		$this->db->where('m_recvd_date', $fdate);
		$this->db->group_by('m_recvd_voucher');
		$this->db->order_by('m_recvd_date', 'desc');
		$sql = $this->db->get('master_recieved_tbl')->result();

		if (!empty($sql)) {
			foreach ($sql as $kky) {
				$cusrbln =	$this->get_customer_balance($kky->m_recvd_customer);
				$crate_list = $this->db->select('m_recvd_crate as m_crate_id,crate.m_itgrp_title as m_crate_name,m_recvd_qty')->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left')->where('m_recvd_voucher', $kky->m_recvd_voucher)->where('m_recvd_type', 2)->order_by('m_itgrp_id')->get('master_recieved_tbl')->result();

				$res = array(
					"m_recvd_user" => $kky->m_recvd_user,
					"m_recvd_customer" => $kky->m_recvd_customer,
					"m_recvd_voucher" => $kky->m_recvd_voucher,
					"m_recvd_remark" => $kky->m_recvd_remark,
					"m_recvd_date" => $kky->m_recvd_date,
					"m_recvd_type" => $kky->m_recvd_type,
					"m_recvd_added_by" => $kky->m_recvd_added_by,
					"m_recvd_added_on" => $kky->m_recvd_added_on,
					"m_cust_name" => $kky->m_cust_name,
					"m_cust_hndiname" => $kky->m_cust_hndiname,
					"m_cust_mobile" => $kky->m_cust_mobile,
					"m_cust_address" => $kky->m_cust_address,
					"total_balance" => $cusrbln['balance_crate'],
					"m_crate_list" => $crate_list,
				);
				$result[] = $res;
			}
		}
		return $result;
	}


	public function get_customer_balance($cust_id)
	{
		$opening_bal = $this->db->select('m_cust_opening,m_cust_crateOP')->where('m_cust_id', $cust_id)->get('master_customer_tbl')->row();

		$sub_total = 0;
		$total_expense = 0;
		$grand_total = 0;
		$crate_total = 0;
		$total_given = 0;
		$total_recieved = 0;
		$salequery = $this->db->select('sum(m_sale_qty) as tqty,sum(m_sale_total) as sub_total,sum(m_sale_crate) as tcrate,(m_sale_comm+m_sale_fright+m_sale_hamali+m_sale_others) as texpense')->where('m_sale_customer', $cust_id)->group_by('m_sale_spo')->get('master_sales_tbl')->result();
		if (!empty($salequery)) {
			foreach ($salequery as $key) {
				$sub_total += $key->sub_total;
				$total_expense +=  $key->texpense;
				$grand_total += ($key->sub_total + $key->texpense);
			}
		}

		$amountrcvdquery = $this->db->select('sum(m_recvd_amount) as tamountrcvd')->where('m_recvd_customer', $cust_id)->where('m_recvd_account', 1)->where('m_recvd_type', 1)->get('master_recieved_tbl')->result();

		$vouch_amtcdrt = $this->db->select('sum(m_voucher_amount) as tamountcdt')->where('m_voucher_accountid', $cust_id)->where('m_voucher_account', 1)->where('m_voucher_type', 1)->where('m_voucher_status', 1)->get('master_voucher_tbl')->result();

		$vouch_amtdbt = $this->db->select('sum(m_voucher_amount) as tamountdbt')->where('m_voucher_accountid', $cust_id)->where('m_voucher_account', 1)->where('m_voucher_type', 2)->where('m_voucher_status', 1)->get('master_voucher_tbl')->result();

		$balance_amt = $opening_bal->m_cust_opening + (($grand_total + $vouch_amtdbt[0]->tamountdbt) - ($amountrcvdquery[0]->tamountrcvd + $vouch_amtcdrt[0]->tamountcdt));


		$result = array(
			"sub_total" => $sub_total,
			"total_expense" => $total_expense,
			"grand_total" => $grand_total,
			"amount_rcvd" => $amountrcvdquery[0]->tamountrcvd ?: 0,
			"total_balance" => $balance_amt,
		);

		$all_crates = $this->all_itemgroup(3);
		$openin_crate_bal = explode(',', $opening_bal->m_cust_crateOP);
		foreach ($all_crates as $key) {
			$crateledger = $this->get_crate_ledger($key->m_itgrp_id, $cust_id);
			$crate_total += ((int)$crateledger['crate_given'] - (int)$crateledger['crate_rcvd']);

			$total_given += (int)$crateledger['crate_given'];
			$total_recieved += (int)$crateledger['crate_rcvd'];

			if ($key->m_itgrp_title == '10 KG') {
				$crattype_bal = isset($openin_crate_bal[0]) ? $openin_crate_bal[0] : 0;
			} else if ($key->m_itgrp_title == '20 KG') {
				$crattype_bal = isset($openin_crate_bal[1]) ? $openin_crate_bal[1] : 0;
			} else if ($key->m_itgrp_title == '25 KG') {
				$crattype_bal = isset($openin_crate_bal[2]) ? $openin_crate_bal[2] : 0;
			}

			$res = array(
				'name' => $key->m_itgrp_title,
				'recived' => (int)$crateledger['crate_rcvd'],
				'given' => (int)$crateledger['crate_given'],
				'balance' => ((int)$crattype_bal + (int)$crateledger['crate_given'] - (int)$crateledger['crate_rcvd']),
			);
			$result['crateitems'][] = $res;
		}

		$result['crate_given'] = $total_given;
		$result['crate_recieved'] = $total_recieved;
		$result['balance_crate'] = array_sum(explode(',', $opening_bal->m_cust_crateOP)) +  $crate_total;

		return $result;
	}

	public function get_crate_ledger($crate_id, $cust_id)
	{
		$crategiven = $this->db->select('sum(m_sale_crate) as tcrate,m_itgrp_title')->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')->where('m_sale_customer', $cust_id)->where('m_item_crate', $crate_id)->group_by('m_item_crate')->get('master_sales_tbl')->result();

		$cratercvdquery = $this->db->select('sum(m_recvd_qty) as tcrateqty,m_itgrp_title')->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left')->where('m_recvd_customer', $cust_id)->where('m_recvd_type', 2)->where('m_recvd_crate', $crate_id)->group_by('m_recvd_crate')->get('master_recieved_tbl')->result();
		$result = array(
			"crate_rcvd" => $cratercvdquery ? $cratercvdquery[0]->tcrateqty : 0,
			"crate_given" => $crategiven ? $crategiven[0]->tcrate : 0,
			"crate_balance" => (($crategiven ? $crategiven[0]->tcrate : 0) - ($cratercvdquery ? $cratercvdquery[0]->tcrateqty : 0))
		);
		return $result;
	}

	// public function get_customer_crateledger($cust_id)
	// {
	// 	$all_crates = $this->all_itemgroup(3);
	// 	$total_given = 0;
	// 	$total_recieved = 0;
	// 	foreach ($all_crates as $key) {
	// 		$crateledger = $this->get_crate_ledger($key->m_itgrp_id, $cust_id);
	// 		$result['recived-' . $key->m_itgrp_title] = (int)$crateledger['crate_rcvd'];
	// 		$result['given-' . $key->m_itgrp_title] = (int)$crateledger['crate_given'];

	// 		$total_given += (int)$crateledger['crate_given'];
	// 		$total_recieved += (int)$crateledger['crate_rcvd'];
	// 	}
	// 	$result['total_recived'] = $total_recieved;
	// 	$result['total_given'] = $total_given;
	// 	return $result;
	// }

	public function get_user_today_stock($user_id, $pre_date = '', $upto = '', $is_count = '')
	{
		if (!empty($pre_date)) {
			$pr_date = $pre_date;
		} else {
			$pr_date = date('Y-m-d');
		}
		$this->db->join('master_item_tbl mit', 'mit.m_item_id = staff_itemissue_tbl.si_issue_item', 'left')
			->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
			->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
			->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');
		$this->db->where('si_issue_user', $user_id);
		$this->db->where('si_issue_type', 1);
		$this->db->where('si_issue_status', 1);
		if (!empty($upto)) {
			$this->db->where('si_issue_date <=', $pr_date);
		} else {
			$this->db->where('si_issue_date', $pr_date);
		}
		$this->db->order_by('m_item_name');

		if (!empty($is_count)) {
			$this->db->select('si_issue_id,si_issue_trackno,sum(si_issue_qty) as itemqty,si_issue_item,si_issue_lotno,si_issue_price,si_issue_date,si_issue_weight,m_item_name,m_item_fright,crate.m_itgrp_title as cratetype,si_issue_user,unit.m_itgrp_title as unitname');
			$this->db->group_by('si_issue_lotno');
			$this->db->group_by('si_issue_item');
		} else {
			$this->db->select('si_issue_id,si_issue_spo,si_issue_trackno,si_issue_qty,si_issue_lotno,si_issue_item,si_issue_price,si_issue_date,si_issue_weight,m_item_name,m_item_fright,(CASE WHEN group.m_itgrp_title IS NULL THEN "" ELSE group.m_itgrp_title END ) AS groupname,(CASE WHEN crate.m_itgrp_title IS NULL THEN "" ELSE crate.m_itgrp_title END ) AS cratetype,si_issue_user,(CASE WHEN unit.m_itgrp_title IS NULL THEN "" ELSE unit.m_itgrp_title END ) AS unitname');
		}
		return $this->db->get('staff_itemissue_tbl')->result();
	}


	public function get_user_balance_stock($user_id, $pre_date = '', $upto = '')
	{
		$result = array();
		$item_count = $this->get_user_today_stock($user_id, $pre_date, null, 1);
		if (!empty($pre_date)) {
			$pr_date = $pre_date;
		} else {
			$pr_date = date('Y-m-d');
		}
		if (!empty($item_count)) {
			$j = 0;
			$k = 0;
			foreach ($item_count as $key) {
				$this->db->select('sum(m_sale_qty) as m_sale_qty,m_sale_total,sum(m_sale_weight) as m_sale_weight,m_sale_crate,( m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) as totalexpanse,m_sale_note,m_cust_name,m_cust_hndiname,m_cust_mobile')
					->join('master_customer_tbl mct', 'mct.m_cust_id = master_sales_tbl.m_sale_customer', 'left')
					->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
					->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
					->where('m_sale_user', $user_id)->where('m_sale_item', $key->si_issue_item)->where('m_sale_lot', $key->si_issue_lotno);
				if (!empty($upto)) {
					$this->db->where('m_sale_date <=', $pr_date);
				} else {
					$this->db->where('m_sale_date', $pr_date);
				}
				$sale_data = $this->db->order_by('m_item_name')->group_by('m_sale_item')->get('master_sales_tbl')->result();

				if (!empty($upto)) {
					$this->db->where('si_issue_date <=', $pr_date);
				} else {
					$this->db->where('si_issue_date', $pr_date);
				}

				$total_return = $this->db->select('sum(si_issue_qty) as itemqty,sum(si_issue_weight) as itmwgt,sum(si_issue_total) as total_amount,sum(si_issue_crate) as total_crate')->where('si_issue_type', 2)->where('si_issue_user', $user_id)->where('si_issue_item', $key->si_issue_item)->where('si_issue_lotno', $key->si_issue_lotno)->where('si_issue_status', 1)->group_by('si_issue_item')->get('staff_itemissue_tbl')->result();

				if (isset($total_return[0])) {
					$retuen_weight = $total_return[0]->itmwgt;
					$retuen_qty = $total_return[0]->itemqty;
					$k++;
				} else {
					$retuen_weight = 0;
					$retuen_qty = 0;
				}

				if (isset($sale_data[0])) {
					$sale_weight = $sale_data[0]->m_sale_weight;
					$sale_qty = $sale_data[0]->m_sale_qty;
					$j++;
				} else {
					$sale_weight = 0;
					$sale_qty = 0;
				}

				$res = array(
					"m_item_id" => $key->si_issue_item,
					"m_item_name" => $key->m_item_name,
					"m_item_fright" => $key->m_item_fright,
					"si_issue_trackno" => $key->si_issue_trackno,
					"si_issue_id" => $key->si_issue_id,
					"si_issue_lotno" => $key->si_issue_lotno,
					"m_issue_price" => $key->si_issue_price,
					"cratetype" => $key->cratetype ?: '',
					"unitname" => $key->unitname ?: '',
					"total_weight_issue" => $key->si_issue_weight,
					"total_sale_weight" => $sale_weight,
					"total_return_weight" => (string)$retuen_weight,
					"balance_weight" => ($key->si_issue_weight - $sale_weight - $retuen_weight),
					"total_qty_issue" => $key->itemqty,
					"total_sale_qty" => $sale_qty,
					"total_return_qty" => (string)$retuen_qty,
					"balance_qty" => ($key->itemqty - $sale_qty - $retuen_qty),

				);
				$result[] = $res;
			}
			// die;
		}
		return $result;
	}

	public function todays_stats($user_id, $fdate)
	{
		$user_group = $this->user_details($user_id)[0]->m_user_group;
		$res = array();
		$total_stock = $this->db->select('sum(si_issue_qty) as itemqty,sum(si_issue_total) as total_amount,sum(si_issue_crate) as total_crate')->where('si_issue_type', 1)->where('si_issue_user', $user_id)->where('si_issue_date', $fdate)->where('si_issue_status', 1)->get('staff_itemissue_tbl')->result();

		$total_return = $this->db->select('sum(si_issue_qty) as itemqty,sum(si_issue_total) as total_amount,sum(si_issue_crate) as total_crate')->where('si_issue_type', 2)->where('si_issue_user', $user_id)->where('si_issue_date', $fdate)->where('si_issue_status', 1)->get('staff_itemissue_tbl')->result();

		$total_sale = $this->db->select('sum(m_sale_qty) as saleqty,sum(m_sale_total) as total_saleamt,sum(m_sale_crate) as total_salecrate')->where('m_sale_user', $user_id)->where('m_sale_date', $fdate)->get('master_sales_tbl')->result();

		$total_crate_recived = $this->db->select('sum(m_recvd_qty) as recievd_crate')->where('m_recvd_user', $user_id)->where('m_recvd_date', $fdate)->where('m_recvd_type', 2)->get('master_recieved_tbl')->result();
		$total_payment_recived = $this->db->select('sum(m_recvd_amount) as recievd_amount')->where('m_recvd_user', $user_id)->where('m_recvd_date', $fdate)->where('m_recvd_account', 1)->where('m_recvd_type', 1)->get('master_recieved_tbl')->result();

		$cust_list = $this->get_user_customers($user_group);
		$cust_outstand = 0;
		$crate10_outstand = 0;
		$crate20_outstand = 0;
		$crate25_outstand = 0;
		if (!empty($cust_list)) {
			foreach ($cust_list as $key) {
				$cust_outstand += $key['total_balance'];
				foreach ($key['crates'] as $cctd) {
					if ($cctd['name'] == '10 KG') {
						$crate10_outstand += $cctd['balance'];
					} else if ($cctd['name'] == '20 KG') {
						$crate20_outstand += $cctd['balance'];
					} else if ($cctd['name'] == '25 KG') {
						$crate25_outstand += $cctd['balance'];
					}
				}
			}
		}
		// echo '<pre>';
		// print_r($cust_list);
		// die;

		$res = array(
			// "total_qty_issue" => $total_stock[0]->itemqty ?: "0",
			// "total_issue_amount" => $total_stock[0]->total_amount,
			// "total_crate_issue" => $total_stock[0]->total_crate,
			// "total_qty_sale" => $total_sale[0]->saleqty ?: "0",
			// "total_amount_sale" => $total_sale[0]->total_saleamt ?: "0",
			"total_qty_issue" => (string)($total_stock[0]->itemqty - $total_sale[0]->saleqty - $total_return[0]->itemqty),
			"total_crate_recived" => $total_crate_recived[0]->recievd_crate ?: "0",
			"total_payment_recived" => $total_payment_recived[0]->recievd_amount ?: "0",
			"cash_outstanding" => (string)$cust_outstand,
			"crate10_outstand" => (string)$crate10_outstand,
			"crate20_outstand" => (string)$crate20_outstand,
			"crate25_outstand" => (string)$crate25_outstand,

		);

		return $res;
	}

	public function insert_return_item($user_id, $from_date)
	{
		$res = $this->get_user_balance_stock($user_id, $from_date);

		$issue_dtl = $this->db->select('si_issue_spo')->where('si_issue_type', 2)->order_by('si_issue_id', 'desc')->group_by('si_issue_spo')->get('staff_itemissue_tbl')->result();
		if (!empty($issue_dtl)) {
			$spo_coun = isset($issue_dtl[0]->si_issue_spo) ? explode('/', $issue_dtl[0]->si_issue_spo) : array();
			$issue_spo = 'R/' . ($spo_coun[1] + 1) . '/' . date('dm', strtotime($from_date));
		} else {
			$issue_spo = 'R/1/' . date('dm', strtotime($from_date));
		}

		foreach ($res as $key => $cau) {
			// print_r($cau['m_item_name']);
			if ($cau['balance_qty'] != 0) {
				$insert_data = array(
					"si_issue_date"    => $from_date ?: date('Y-m-d'),
					// "si_issue_trackno"    => $this->input->post('si_issue_trackno'),
					"si_issue_type"    => 2,
					"si_issue_user"    => $user_id,
					"si_issue_item"    => $cau['m_item_id'],
					"si_issue_lotno"    => $cau['si_issue_lotno'],
					"si_issue_qty"    => $cau['balance_qty'],
					"si_issue_weight"    => $cau['balance_weight'],
					"si_issue_crate"    => $cau['m_item_name'],
					"si_issue_price"    => $cau['m_issue_price'],
					"si_issue_total"    => ($cau['balance_qty'] * $cau['m_issue_price']),

				);

				$insert_data['si_issue_status'] = 1;
				$insert_data['si_issue_added_by'] = $user_id;
				$insert_data['si_issue_spo'] = $issue_spo;
				$insert_data['si_issue_added_on'] = date('Y-m-d H:i');
				$this->db->insert('staff_itemissue_tbl', $insert_data);
				$this->update_cust_balance(null, null, ($cau['balance_qty'] * (-1)),  $cau['m_item_id'], $cau['si_issue_lotno']);
				$res = 1;
			}
		}
		// die;
		return $res;
	}

	public function insert_customer()
	{

		$data = array(

			"m_cust_name" => $this->input->post('m_cust_name'),
			"m_cust_mobile" => $this->input->post('m_cust_mobile'),
			"m_cust_group" => $this->input->post('m_cust_group'),
			"m_cust_status" => 1,

		);

		$data['m_cust_added_by'] = $this->input->post('user_id');
		$data['m_cust_added_on'] = date('Y-m-d H:i:s');
		$this->db->insert('master_customer_tbl', $data);

		$cust_id = $this->db->insert_id();
		return $cust_id;
	}

	////=================================================== managar apis =============================================////

	public function insert_issue_item()
	{

		//   $issue_id = $this->input->post('si_issue_id');
		$issue_item = $this->input->post('si_issue_item');
		$issue_lotno = $this->input->post('si_issue_lotno');
		$issue_qty = $this->input->post('si_issue_qty');
		$issue_weight = $this->input->post('si_issue_weight');
		$issue_crate = $this->input->post('si_issue_crate');
		$issue_price = $this->input->post('si_issue_price');
		$issue_total = $this->input->post('si_issue_total');

		$issue_dtl = $this->db->select('si_issue_spo')->where('si_issue_type', 1)->order_by('si_issue_id', 'desc')->group_by('si_issue_spo')->get('staff_itemissue_tbl')->result();
		if (!empty($issue_dtl)) {
			$spo_coun = explode('/', $issue_dtl[0]->si_issue_spo);
			$issue_spo = ((int)$spo_coun[0] + 1) . '/' . date('dm', strtotime($this->input->post('si_issue_date')));
		} else {
			$issue_spo = '1/' . date('dm', strtotime($this->input->post('si_issue_date')));
		}

		foreach ($issue_item as $key => $cau) {

			$insert_data = array(
				"si_issue_date"    => $this->input->post('si_issue_date'),
				"si_issue_trackno"    => $this->input->post('si_issue_trackno'),
				"si_issue_type"    => 1,
				"si_issue_user"    => $this->input->post('si_issue_user'),
				"si_issue_item"    => $cau,
				"si_issue_qty"    => $issue_qty[$key],
				"si_issue_lotno"    => $issue_lotno[$key],
				"si_issue_weight"    => $issue_weight[$key],
				"si_issue_crate"    => $issue_crate[$key],
				"si_issue_price"    => $issue_price[$key],
				"si_issue_total"    => $issue_total[$key],

			);

			// if (!empty($issue_id[$key])) {

			//   $this->db->where('si_issue_id', $issue_id[$key])->update('staff_itemissue_tbl', $insert_data);
			//   $res = 2;
			// } else {
			$insert_data['si_issue_status'] = 1;
			$insert_data['si_issue_added_by'] = $this->input->post('user_id');
			$insert_data['si_issue_spo'] = $issue_spo;
			$insert_data['si_issue_added_on'] = date('Y-m-d H:i');
			$res = $this->db->insert('staff_itemissue_tbl', $insert_data);
			$this->update_cust_balance(null, null, $issue_qty[$key], $cau, $issue_lotno[$key]);
			//   $res = 1;
			// }
		}
		return $res;
	}

	public function insert_purchase()
	{

		// $issue_id = $this->input->post('m_purcs_id');
		$purchase = $this->input->post('m_purcs_item');
		$issue_qty = $this->input->post('m_purcs_qty');
		$issue_weight = $this->input->post('m_purcs_weight');
		$issue_crate = $this->input->post('m_purcs_crate');
		// $issue_price = $this->input->post('m_purcs_price');
		// $m_purcs_total = $this->input->post('m_purcs_total');
		$m_purcs_lot = $this->input->post('m_purcs_lot');

		$supp_tm = $this->db->select('m_user_trademark')->where('m_user_type', 2)->where('m_user_id', $this->input->post('m_purcs_suplier'))->get('master_users_tbl')->row();
		$purchase_dtl = $this->db->select('m_purcs_spo')->order_by('m_purcs_id', 'desc')->group_by('m_purcs_spo')->get('master_purchase_tbl')->result();
		if (!empty($purchase_dtl)) {
			$spo_coun = explode('/', $purchase_dtl[0]->m_purcs_spo);
			$purcs_spo = $supp_tm->m_user_trademark . '/' . ($spo_coun[1] + 1) . '/' . date('d/m', strtotime($this->input->post('m_purcs_date')));
		} else {
			$purcs_spo = $supp_tm->m_user_trademark . '/1/' . date('d/m', strtotime($this->input->post('m_purcs_date')));
		}

		foreach ($purchase as $key => $cau) {

			$insert_data = array(
				"m_purcs_date"    => $this->input->post('m_purcs_date'),
				"m_purcs_suplier"    => $this->input->post('m_purcs_suplier'),
				// "m_purcs_comrate"    => $this->input->post('m_purcs_comrate'),
				// "m_purcs_comm"    => $this->input->post('m_purcs_comm'),
				// "m_purcs_fright"    => $this->input->post('m_purcs_fright'),
				// "m_purcs_hamali"    => $this->input->post('m_purcs_hamali'),
				// "m_purcs_charity"    => $this->input->post('m_purcs_charity'),
				// "m_purcs_packaging"    => $this->input->post('m_purcs_packaging'),
				// "m_purcs_loading"    => $this->input->post('m_purcs_loading'),
				// "m_purcs_advance"    => $this->input->post('m_purcs_advance'),
				// "m_purcs_others"    => $this->input->post('m_purcs_others'),
				"m_purcs_note"    => $this->input->post('m_purcs_note'),
				"m_purcs_truckno"    => $this->input->post('m_purcs_truckno'),
				"m_purcs_user"    => $this->input->post('user_id'),
				"m_purcs_item"    => $cau,
				"m_purcs_qty"    => $issue_qty[$key],
				"m_purcs_weight"    => $issue_weight[$key],
				"m_purcs_crate"    => $issue_crate[$key],
				"m_purcs_lot"    => $m_purcs_lot[$key],
				"m_purcs_available" => $issue_qty[$key],
				// "m_purcs_total"    => $m_purcs_total[$key],

			);

			$insert_data['m_purcs_spo'] = $purcs_spo;
			$insert_data['m_purcs_added_by'] = $this->input->post('user_id');

			$insert_data['m_purcs_added_on'] = date('Y-m-d H:i');
			$res = $this->db->insert('master_purchase_tbl', $insert_data);
			$this->update_userbalance($this->input->post('m_purcs_suplier'), null, $issue_qty[$key], $cau); //new change:end
		}

		return $res;
	}

	public function get_all_agents()
	{

		$this->db->select('m_user_id,m_user_name,m_user_mobile,m_user_pan_no,m_user_accountno,m_user_address,m_user_adharno,m_user_trademark,m_user_contractPerd,m_user_added_on,m_user_design,m_state_name,m_city_name,m_user_login_allow,m_user_password,m_user_group');
		$this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = master_users_tbl.m_user_city', 'left');
		$this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_users_tbl.m_user_state', 'left');
		$this->db->where('m_user_type', 1);
		$this->db->where('m_user_design', 1);
		return $this->db->get('master_users_tbl')->result();
	}

	public function get_all_supplier()
	{

		$this->db->select('m_user_id,m_user_name,m_user_mobile,m_user_pan_no,m_user_accountno,m_user_address,m_user_adharno,m_user_trademark,m_user_contractPerd,m_user_added_on,m_state_name,m_city_name');
		$this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = master_users_tbl.m_user_city', 'left');
		$this->db->join('master_state_tbl', 'master_state_tbl.m_state_id = master_users_tbl.m_user_state', 'left');
		$this->db->where('m_user_type', 2);
		return $this->db->get('master_users_tbl')->result();
	}

	public function get_item_issue_list($user_id, $from_date, $todate, $agent)
	{
		if (!empty($from_date)) {
			$this->db->where('DATE_FORMAT(si_issue_date,"%Y-%m-%d")>=', $from_date);
		}
		if (!empty($todate)) {
			$this->db->where('DATE_FORMAT(si_issue_date,"%Y-%m-%d")<=', $todate);
		}

		if (!empty($agent)) {
			$this->db->where('si_issue_user', $agent);
		}
		$this->db->select('si_issue_spo,si_issue_trackno,si_issue_date,si_issue_user,m_user_name,m_user_mobile,sum(si_issue_qty) as tqty,sum(si_issue_weight) as twght,sum(si_issue_total) as tamount');
		$this->db->join('master_users_tbl mut', 'mut.m_user_id = staff_itemissue_tbl.si_issue_user', 'left');
		$this->db->where('si_issue_added_by', $user_id);
		$this->db->where('si_issue_status', 1);
		$this->db->order_by('si_issue_date', 'desc');
		$this->db->group_by('si_issue_spo');
		$this->db->group_by('si_issue_date');
		$this->db->group_by('si_issue_user');
		return $this->db->get('staff_itemissue_tbl')->result();
	}

	public function get_purchase_list($user_id, $from_date, $todate, $supplier)
	{

		if (!empty($from_date)) {
			$this->db->where('DATE_FORMAT(m_purcs_date,"%Y-%m-%d")>=', $from_date);
		}
		if (!empty($todate)) {
			$this->db->where('DATE_FORMAT(m_purcs_date,"%Y-%m-%d")<=', $todate);
		}

		if (!empty($supplier)) {
			$this->db->where('m_purcs_suplier', $supplier);
		}

		$this->db->select('m_purcs_spo,m_purcs_truckno,m_purcs_date,m_purcs_suplier,mut.m_user_name as supplier_name,mut.m_user_mobile as supplier_mobile,sum(m_purcs_qty) as tqty,sum(m_purcs_weight) as twght,sum(m_purcs_total) as total_amount,m_purcs_comm,m_purcs_fright,m_purcs_hamali,m_purcs_charity,m_purcs_packaging,m_purcs_loading,m_purcs_advance,m_purcs_others,(m_purcs_comm + m_purcs_fright + m_purcs_hamali + m_purcs_charity + m_purcs_packaging + m_purcs_loading + m_purcs_advance + m_purcs_others) as total_expense');
		$this->db->join('master_users_tbl mut', 'mut.m_user_id = master_purchase_tbl.m_purcs_suplier', 'left')
			->join('master_users_tbl', 'master_users_tbl.m_user_id = master_purchase_tbl.m_purcs_user', 'left');
		$this->db->where('m_purcs_added_by', $user_id);
		$this->db->order_by('m_purcs_date', 'desc');
		$this->db->group_by('m_purcs_spo');
		$this->db->group_by('m_purcs_date');

		return $this->db->get('master_purchase_tbl')->result();
	}

	function get_purchase_items($id)
	{
		$this->db->select('m_purcs_id,m_purcs_qty,m_purcs_date,m_purcs_weight,m_item_name,m_item_fright,(CASE WHEN group.m_itgrp_title IS NULL THEN "" ELSE group.m_itgrp_title END ) AS groupname,(CASE WHEN crate.m_itgrp_title IS NULL THEN "" ELSE crate.m_itgrp_title END ) AS cratetype,(CASE WHEN unit.m_itgrp_title IS NULL THEN "" ELSE unit.m_itgrp_title END ) AS unitname,m_purcs_total');
		$this->db->join('master_item_tbl mit', 'mit.m_item_id = master_purchase_tbl.m_purcs_item', 'left')
			->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
			->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
			->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');
		$this->db->where('m_purcs_spo', $id);
		$this->db->order_by('m_item_name');
		return $this->db->get('master_purchase_tbl')->result();
	}



	public function get_purchase_detail($id)
	{
		$result = array();
		$this->db->select('m_purcs_spo,m_purcs_truckno,m_purcs_date,m_purcs_suplier,mut.m_user_name as supplier_name,mut.m_user_mobile as supplier_mobile,sum(m_purcs_qty) as tqty,sum(m_purcs_weight) as twght,sum(m_purcs_total) as total_amount,m_purcs_comm,m_purcs_fright,m_purcs_hamali,m_purcs_charity,m_purcs_packaging,m_purcs_loading,m_purcs_advance,m_purcs_others,(m_purcs_comm + m_purcs_fright + m_purcs_hamali + m_purcs_charity + m_purcs_packaging + m_purcs_loading + m_purcs_advance + m_purcs_others) as total_expense');
		$this->db->join('master_users_tbl mut', 'mut.m_user_id = master_purchase_tbl.m_purcs_suplier', 'left')
			->join('master_users_tbl', 'master_users_tbl.m_user_id = master_purchase_tbl.m_purcs_user', 'left');
		$this->db->where('m_purcs_spo', $id);
		$this->db->group_by('m_purcs_spo');
		$purchse_detail = $this->db->get('master_purchase_tbl')->result();
		if (!empty($purchse_detail)) {
			$res = array(
				'm_purcs_spo' => $purchse_detail[0]->m_purcs_spo,
				'm_purcs_truckno' => $purchse_detail[0]->m_purcs_truckno,
				'm_purcs_date' => $purchse_detail[0]->m_purcs_date,
				'm_purcs_suplier' => $purchse_detail[0]->m_purcs_suplier,
				'supplier_name' => $purchse_detail[0]->supplier_name,
				'supplier_mobile' => $purchse_detail[0]->supplier_mobile,
				'total_quantity' => $purchse_detail[0]->tqty,
				'total_weight' => $purchse_detail[0]->twght,
				'total_amount' => $purchse_detail[0]->total_amount,
				'm_purcs_comm' => $purchse_detail[0]->m_purcs_comm,
				'm_purcs_fright' => $purchse_detail[0]->m_purcs_fright,
				'm_purcs_hamali' => $purchse_detail[0]->m_purcs_hamali,
				'm_purcs_charity' => $purchse_detail[0]->m_purcs_charity,
				'm_purcs_packaging' => $purchse_detail[0]->m_purcs_packaging,
				'm_purcs_loading' => $purchse_detail[0]->m_purcs_loading,
				'm_purcs_advance' => $purchse_detail[0]->m_purcs_advance,
				'm_purcs_others' => $purchse_detail[0]->m_purcs_others,
				'total_expense' => $purchse_detail[0]->total_expense,
				'items' => $this->get_purchase_items($id),
			);

			$result[] = $res;
		}
		return $result;
	}

	function get_issue_items($id)
	{
		$this->db->select('si_issue_id,si_issue_qty,si_issue_date,si_issue_weight,m_item_name,m_item_fright,(CASE WHEN group.m_itgrp_title IS NULL THEN "" ELSE group.m_itgrp_title END ) AS groupname,(CASE WHEN crate.m_itgrp_title IS NULL THEN "" ELSE crate.m_itgrp_title END ) AS cratetype,(CASE WHEN unit.m_itgrp_title IS NULL THEN "" ELSE unit.m_itgrp_title END ) AS unitname,si_issue_total');
		$this->db->join('master_item_tbl mit', 'mit.m_item_id = staff_itemissue_tbl.si_issue_item', 'left')
			->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
			->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
			->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');
		$this->db->where('si_issue_spo', $id);
		$this->db->where('si_issue_status', 1);
		$this->db->order_by('m_item_name');
		return $this->db->get('staff_itemissue_tbl')->result();
	}

	public function get_issue_detail($id)
	{
		$result = array();
		$this->db->select('si_issue_spo,si_issue_trackno,si_issue_date,si_issue_user,master_users_tbl.m_user_name as agent_name,master_users_tbl.m_user_mobile as agent_mobile,sum(si_issue_qty) as tqty,sum(si_issue_weight) as twght,sum(si_issue_total) as total_amount');
		$this->db->join('master_users_tbl', 'master_users_tbl.m_user_id = staff_itemissue_tbl.si_issue_user', 'left');
		$this->db->where('si_issue_spo', $id);
		$this->db->where('si_issue_status', 1);
		$this->db->group_by('si_issue_spo');
		$issue_detail = $this->db->get('staff_itemissue_tbl')->result();
		if (!empty($issue_detail)) {
			$res = array(
				'si_issue_spo' => $issue_detail[0]->si_issue_spo,
				'si_issue_trackno' => $issue_detail[0]->si_issue_trackno,
				'si_issue_date' => $issue_detail[0]->si_issue_date,
				'si_issue_user' => $issue_detail[0]->si_issue_user,
				'agent_name' => $issue_detail[0]->agent_name,
				'agent_mobile' => $issue_detail[0]->agent_mobile,
				'total_quantity' => $issue_detail[0]->tqty,
				'total_weight' => $issue_detail[0]->twght,
				'total_amount' => $issue_detail[0]->total_amount,
				'items' => $this->get_issue_items($id),
			);

			$result[] = $res;
		}
		return $result;
	}

	public function get_purchase_item($from = '', $to = '', $item = '', $lot_id = "", $lot = '')
	{
		$this->db->select('sum(m_purcs_qty) as itemqty,m_purcs_id,m_purcs_lot,m_purcs_spo,m_purcs_item,m_purcs_price,m_purcs_date,m_purcs_weight,m_item_name,crate.m_itgrp_title as cratetype,m_purcs_user,unit.m_itgrp_title as unitname,supp.m_user_trademark,m_item_desc,m_item_group,m_item_unit,m_item_crate,m_item_code,m_item_fright,m_item_comm,m_item_status,m_item_added_on,group.m_itgrp_title as groupname');
		$this->db->join('master_item_tbl mit', 'mit.m_item_id = master_purchase_tbl.m_purcs_item', 'left')
			->join('master_users_tbl as supp', 'supp.m_user_id = master_purchase_tbl.m_purcs_suplier', 'left')
			->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
			->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
			->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');

		if (!empty($item)) {
			$this->db->where('m_purcs_item', $item);
		}
		if (!empty($from)) {
			$this->db->where('m_purcs_date >=', $from);
		}
		if (!empty($to)) {
			$this->db->where('m_purcs_date <=', $to);
		}
		if (!empty($lot_id)) {
			$this->db->where('m_purcs_id', $lot_id);
		}
		$this->db->order_by('m_item_name');
		$this->db->order_by('m_purcs_date', 'desc');
		if ($lot == 1) {
			$this->db->group_by('m_purcs_id');
		}
		$this->db->group_by('m_purcs_item');

		return $this->db->get('master_purchase_tbl')->result();
	}

	public function get_issue_stock($type, $from = '', $to = '', $item = '', $lot = '')
	{
		$this->db->select('sum(si_issue_qty) as itemqty,si_issue_item,si_issue_price,si_issue_date,si_issue_weight,m_item_name,crate.m_itgrp_title as cratetype,si_issue_user,unit.m_itgrp_title as unitname');
		$this->db->join('master_item_tbl mit', 'mit.m_item_id = staff_itemissue_tbl.si_issue_item', 'left')
			->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
			->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
			->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');

		$this->db->where('si_issue_type', $type);
		if (!empty($item)) {
			$this->db->where('si_issue_item', $item);
		}
		if (!empty($from)) {
			$this->db->where('si_issue_date >=', $from);
		}
		if (!empty($to)) {
			$this->db->where('si_issue_date <=', $to);
		}
		if (!empty($lot)) {
			$this->db->where('si_issue_lotno', $lot);
		}
		$this->db->where('si_issue_status', 1);
		$this->db->order_by('m_item_name');
		$this->db->order_by('si_issue_date', 'desc');
		$this->db->group_by('si_issue_item');

		return $this->db->get('staff_itemissue_tbl')->result();
	}

	public function get_admin_sale($from = '', $to = '', $item = '', $lot = '')
	{
		$this->db->select('sum(m_sale_qty) as itemqty,m_sale_item,m_sale_price,m_sale_date,m_sale_weight,m_item_name,crate.m_itgrp_title as cratetype,m_sale_user,unit.m_itgrp_title as unitname');
		$this->db->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
			->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
			->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
			->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');
		$this->db->where('m_sale_added_by', 1);
		if (!empty($item)) {
			$this->db->where('m_sale_item', $item);
		}
		if (!empty($from)) {
			$this->db->where('m_sale_date >=', $from);
		}
		if (!empty($to)) {
			$this->db->where('m_sale_date <=', $to);
		}
		if (!empty($lot)) {
			$this->db->where('m_sale_lot', $lot);
		}
		$this->db->order_by('m_item_name');
		$this->db->order_by('m_sale_date', 'desc');
		$this->db->group_by('m_sale_item');

		return $this->db->get('master_sales_tbl')->result();
	}

	public function get_item_avil_lot($from = '', $to = '', $item = '')
	{
		$result = array();
		if (!empty($item)) {
			$item_purchase = $this->get_purchase_item($from, $to, $item, null, 1);
		}


		if (!empty($item_purchase)) {

			foreach ($item_purchase as $key) {
				$item_issue_count = $this->get_issue_stock(1, $from, $to, $key->m_purcs_item, $key->m_purcs_id);
				$item_return_count = $this->get_issue_stock(2, $from, $to, $key->m_purcs_item, $key->m_purcs_id);
				$item_sale_count = $this->get_admin_sale($from, $to, $key->m_purcs_item, $key->m_purcs_id);

				$pur_weight = $key->m_purcs_weight;
				$pur_qty = $key->itemqty;


				if (isset($item_issue_count[0])) {
					$issue_weight = $item_issue_count[0]->si_issue_weight;
					$issue_qty = $item_issue_count[0]->itemqty;
				} else {
					$issue_weight = 0;
					$issue_qty = 0;
				}

				if (isset($item_return_count[0])) {
					$retun_weight = $item_return_count[0]->si_issue_weight;
					$retun_qty = $item_return_count[0]->itemqty;
				} else {
					$retun_weight = 0;
					$retun_qty = 0;
				}
				// $balance_weight =  ($pur_weight + $retun_weight -  $issue_weight);
				// $balance_qty =  ($pur_qty + $retun_qty -  $issue_qty);

				if (isset($item_sale_count[0])) {
					$sale_weight = $item_sale_count[0]->m_sale_weight;
					$sale_qty = $item_sale_count[0]->itemqty;
				} else {
					$sale_weight = 0;
					$sale_qty = 0;
				}

				$balance_weight =  ($pur_weight + $retun_weight -  $issue_weight - $sale_weight);
				$balance_qty =  ($pur_qty + $retun_qty -  $issue_qty - $sale_qty);


				$res = array(
					"m_item_id" => $key->m_purcs_item,
					"m_item_name" => $key->m_item_name,
					"m_purcs_spo" => $key->m_purcs_spo,
					"m_purcs_date" => date('d/m', strtotime($key->m_purcs_date)),
					"m_user_trademark" => $key->m_user_trademark,
					"m_purcs_qty" => $key->itemqty,
					"m_purcs_id" => $key->m_purcs_id,
					"m_purcs_lot" => $key->m_purcs_lot,
					"balance_weight" => (string)$balance_weight,
					"balance_qty" => (string)$balance_qty,

				);
				if ($balance_qty > 0) {
					$result[] = $res;
				}
			}
		}
		return $result;
	}

	public function get_avil_items($to = '', $item = '')
	{
		$result = array();
		$i = 0;
		$key_array = array();


		$item_purchase = $this->get_purchase_item(null, $to, $item, null, 1);

		if (!empty($item_purchase)) {

			foreach ($item_purchase as $key) {
				$item_purchase_open = $this->get_purchase_item(null, date('Y-m-d', strtotime($to . '-1day')), $key->m_purcs_item, $key->m_purcs_id);
				$item_issue_open = $this->get_issue_stock(1, null, date('Y-m-d', strtotime($to . '-1day')), $key->m_purcs_item, $key->m_purcs_id);
				$item_return_open = $this->get_issue_stock(2, null,  date('Y-m-d', strtotime($to . '-1day')), $key->m_purcs_item, $key->m_purcs_id);
				$item_sale_open = $this->get_admin_sale(null,  date('Y-m-d', strtotime($to . '-1day')), $key->m_purcs_item, $key->m_purcs_id);
				$item_issue_count = $this->get_issue_stock(1, null, $to, $key->m_purcs_item, $key->m_purcs_id);
				$item_return_count = $this->get_issue_stock(2, null, $to, $key->m_purcs_item, $key->m_purcs_id);
				$item_sale_count = $this->get_admin_sale(null, $to, $key->m_purcs_item, $key->m_purcs_id);

				if (isset($item_purchase_open[0])) {
					$open_pur_weight = $item_purchase_open[0]->m_purcs_weight;
					$open_pur_qty = $item_purchase_open[0]->itemqty;
				} else {
					$open_pur_weight = 0;
					$open_pur_qty = 0;
				}

				if (isset($item_issue_open[0])) {
					$open_issue_weight = $item_issue_open[0]->si_issue_weight;
					$open_issue_qty = $item_issue_open[0]->itemqty;
				} else {
					$open_issue_weight = 0;
					$open_issue_qty = 0;
				}

				if (isset($item_return_open[0])) {
					$open_retun_weight = $item_return_open[0]->si_issue_weight;
					$open_retun_qty = $item_return_open[0]->itemqty;
				} else {
					$open_retun_weight = 0;
					$open_retun_qty = 0;
				}

				if (isset($item_sale_open[0])) {
					$open_sale_weight = $item_sale_open[0]->m_sale_weight;
					$open_sale_qty = $item_sale_open[0]->itemqty;
				} else {
					$open_sale_weight = 0;
					$open_sale_qty = 0;
				}

				$open_balance_weight =  ($open_pur_weight + $open_retun_weight -  $open_issue_weight -  $open_sale_weight);
				$open_balance_qty =  ($open_pur_qty + $open_retun_qty -  $open_issue_qty - $open_sale_qty);


				$pur_weight = $key->m_purcs_weight;
				$pur_qty = $key->itemqty;


				if (isset($item_issue_count[0])) {
					$issue_weight = $item_issue_count[0]->si_issue_weight;
					$issue_qty = $item_issue_count[0]->itemqty;
				} else {
					$issue_weight = 0;
					$issue_qty = 0;
				}

				if (isset($item_return_count[0])) {
					$retun_weight = $item_return_count[0]->si_issue_weight;
					$retun_qty = $item_return_count[0]->itemqty;
				} else {
					$retun_weight = 0;
					$retun_qty = 0;
				}

				if (isset($item_sale_count[0])) {
					$sale_weight = $item_sale_count[0]->m_sale_weight;
					$sale_qty = $item_sale_count[0]->itemqty;
				} else {
					$sale_weight = 0;
					$sale_qty = 0;
				}

				$balance_weight =  ($pur_weight + $retun_weight -  $issue_weight - $sale_weight);
				$balance_qty =  ($pur_qty + $retun_qty -  $issue_qty - $sale_qty);


				$res =  array(
					"m_item_id" => $key->m_purcs_item,
					"m_item_name" => $key->m_item_name,
					// "m_item_price" => $key->m_purcs_price,
					"m_item_price" => '',
					"m_item_fright" => $key->m_item_fright,
					"m_item_desc" => $key->m_item_desc,
					"m_item_group" => $key->m_item_group,
					"m_item_unit" => $key->m_item_unit,
					"m_item_crate" => $key->m_item_crate,
					"m_item_code" => $key->m_item_code,
					"m_item_comm" => $key->m_item_comm,
					"m_item_status" => $key->m_item_status,
					"m_item_added_on" => $key->m_item_added_on,
					"groupname" => $key->groupname,
					"m_purcs_spo" => $key->m_purcs_spo,
					"m_purcs_date" => date('d/m', strtotime($key->m_purcs_date)),
					"m_user_trademark" => $key->m_user_trademark,
					"m_purcs_qty" => $key->itemqty,
					"cratetype" => $key->cratetype ?: '',
					"unitname" => $key->unitname ?: '',
					"m_purcs_lot" => $key->m_purcs_lot ?: '',
					"balance_weight" => $balance_weight,
					"balance_qty" => $balance_qty,
					"opening_qty" => $open_balance_qty == 0 ? $key->itemqty : $open_balance_qty,
					"closing_qty" =>  $balance_qty,

				);
				if ($balance_qty > 0) {
					if (!in_array($key->m_purcs_item, $key_array)) {
						$key_array[$i] = $key->m_purcs_item;
						$result[] = $res;
					}
					$i++;
					// $result[] = $res;
				}
			}
		}
		return $result;
		//   return $this->Report_model->unique_multidimensional_array($result, 'm_item_id');

	}

	public function get_avilable_item($to = '', $itemid = '', $group = '')
	{
		$this->db->select('m_item_id,m_item_name,m_item_crate,m_item_fright,m_item_price,m_purcs_price,m_purcs_available as balance_qty,m_purcs_lot,m_purcs_id,(CASE WHEN crate.m_itgrp_title IS NULL THEN "" ELSE crate.m_itgrp_title END ) AS cratetype,(CASE WHEN unit.m_itgrp_title IS NULL THEN "" ELSE unit.m_itgrp_title END ) AS unitname,(CASE WHEN group.m_itgrp_title IS NULL THEN "" ELSE group.m_itgrp_title END ) AS groupname,m_purcs_date,m_user_trademark')
			->join('master_item_tbl', 'master_item_tbl.m_item_id = master_purchase_tbl.m_purcs_item')
			->join('master_itemgroup_tbl group', 'group.m_itgrp_id = master_item_tbl.m_item_group', 'left')
			->join('master_itemgroup_tbl crate', 'crate.m_itgrp_id = master_item_tbl.m_item_crate', 'left')
			->join('master_itemgroup_tbl unit', 'unit.m_itgrp_id = master_item_tbl.m_item_unit', 'left')
			->join('master_users_tbl mut', 'mut.m_user_id = master_purchase_tbl.m_purcs_suplier', 'left');
		$this->db->where('m_purcs_available >', 0);

		if (!empty($itemid)) {
			$this->db->where('m_item_id', $itemid);
		}
		if (!empty($to)) {
			$this->db->where('m_purcs_date <=', $to);
		}
		if ($group == 1) {
			$this->db->group_by('m_item_id');
		}
		return $this->db->order_by('m_item_name')->get('master_purchase_tbl')->result();
	}

	public function get_sale_detail($m_sale_spo)
	{
		$this->db->select('m_sale_spo,sum(m_sale_qty) as total_qty,sum(m_sale_total) as sub_total,m_sale_date,sum(m_sale_weight) as total_weight,sum(m_sale_crate) as total_crate,m_sale_comrate,m_sale_comm,m_sale_fright,m_sale_hamali,m_sale_others,(m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) as total_expense,m_sale_note,m_sale_user,m_sale_customer,group_concat(mit.m_item_name,"-", m_sale_qty ,"*",m_sale_price) as item_detail');
		$this->db->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left');

		$this->db->where('m_sale_spo', $m_sale_spo);
		$this->db->group_by('m_sale_spo');

		$sale_datil = $this->db->get('master_sales_tbl')->result();

		return $sale_datil;
	}

	public function update_cust_balance($id = '', $amt = '', $qty = '', $itemID = '', $purID = '')
	{
		if (!empty($purID) && !empty($qty)) {
			$this->db->set('m_purcs_available', 'm_purcs_available - ' . (int)$qty, FALSE)
				->where('m_purcs_id', $purID)->update('master_purchase_tbl');
		}

		if (!empty($itemID)) {
			$itemDtl = $this->db->select('m_itgrp_title')
				->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_item_tbl.m_item_crate', 'left')
				->where('m_item_id', $itemID)->get('master_item_tbl')->row();

			$balanceFields = [
				'10 KG' => 'm_cust_10bal',
				'20 KG' => 'm_cust_20bal',
				'25 KG' => 'm_cust_25bal'
			];

			if (!empty($itemDtl->m_itgrp_title) && isset($balanceFields[$itemDtl->m_itgrp_title])) {
				$this->db->set($balanceFields[$itemDtl->m_itgrp_title], $balanceFields[$itemDtl->m_itgrp_title] . ' + ' . (float)$qty, FALSE);
				$this->db->where('m_cust_id', $id)->update('master_customer_tbl');
			}
		}

		if (!empty($amt) && !empty($id)) {
			$this->db->set('m_cust_balance', 'm_cust_balance + ' . (float)$amt, FALSE)
				->where('m_cust_id', $id)->update('master_customer_tbl');
		}

		return true;
	}

	public function update_userbalance($id = '', $amt = '', $qty = '', $itemID = '')
	{
		if (!empty($itemID)) {
			$itemDtl = $this->db->select('m_itgrp_title')
				->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_item_tbl.m_item_crate', 'left')
				->where('m_item_id', $itemID)->get('master_item_tbl')->row();

			$balanceFields = [
				'10 KG' => 'm_user_10bal',
				'20 KG' => 'm_user_20bal',
				'25 KG' => 'm_user_25bal'
			];

			if (!empty($itemDtl->m_itgrp_title) && isset($balanceFields[$itemDtl->m_itgrp_title])) {
				$this->db->set($balanceFields[$itemDtl->m_itgrp_title], $balanceFields[$itemDtl->m_itgrp_title] . ' + ' . (float)$qty, FALSE);
				$this->db->where('m_user_id', $id)->update('master_users_tbl');
			}
		}

		if (!empty($amt) && !empty($id)) {
			$this->db->set('m_user_balance', 'm_user_balance + ' . (float)$amt, FALSE)
				->where('m_user_id', $id)->update('master_users_tbl');
		}

		return true;
	}

	public function send_sale_sms($m_sale_spo)
	{

		$sale_data = $this->get_sale_detail($m_sale_spo);
		$cust_detail = $this->Main_model->get_opening_balance($sale_data[0]->m_sale_customer, date('Y-m-d'));

		$mobile = $cust_detail['cust_mobile'];

		$longURL = base_url('Sales/bill_print?id=' . $m_sale_spo);

		$shortURL = $this->shorten_url($longURL);
		if (!empty($shortURL)) {
			$message = "hello " . $cust_detail['cust_name'] . ",\n Your invoice no: " . $m_sale_spo . " for INR " . ($sale_data[0]->total_expense + $sale_data[0]->sub_total) . " has been created with Ajay Kushwaha & Company. See the invoice: https://ajayfruits.in/" . $shortURL . " -Ajay Kushwaha & Company";

			$templateid  = '1707170573986768740';
			// echo $message;
			// die;
			// $this->Api_Model->sendSms($message, $mobile, $templateid);
		}
	}


	public function get_agents_performance($date)
	{
		$data = array();
		$all_agents = $this->db->select('m_user_id,m_user_name,m_user_mobile,m_user_group')->where('m_user_type', 1)->where('m_user_design', 1)->get('master_users_tbl')->result();

		if (!empty($all_agents)) {
			foreach ($all_agents as $key) {
				$today_issue = $this->db->select('sum(si_issue_qty) as total_issue,group_concat(si_issue_item) as issue_items,group_concat(si_issue_qty) as issue_qty,group_concat(si_issue_lotno) as issue_lot,si_issue_user')->where('si_issue_date', $date)->where('si_issue_type', 1)->where('si_issue_status', 1)->where('si_issue_user', $key->m_user_id)->get('staff_itemissue_tbl')->row();
				$today_return = $this->db->select('sum(si_issue_qty) as total_return,group_concat(si_issue_item) as return_items,group_concat(si_issue_qty) as return_qty,group_concat(si_issue_lotno) as return_lot,si_issue_user')->where('si_issue_date', $date)->where('si_issue_type', 2)->where('si_issue_user', $key->m_user_id)->where('si_issue_status', 1)->get('staff_itemissue_tbl')->row();

				$today_sale = $this->db->select('sum(m_sale_qty) as total_sale,group_concat(m_sale_item) as sale_items,group_concat(m_sale_qty) as sale_qty,group_concat(m_sale_lot) as sale_lot,m_sale_user')->where('m_sale_date', $date)->where('m_sale_user', $key->m_user_id)->get('master_sales_tbl')->row();


				$today_collection = $this->db->select('sum(m_recvd_amount) as total_collection')->where('m_recvd_user', $key->m_user_id)->where('m_recvd_type', 1)->where('m_recvd_account', 1)->where('m_recvd_date', $date)->get('master_recieved_tbl')->row();

				$today_discount = $this->db->select('sum(m_voucher_amount) as total_discount')->where('m_voucher_added_by', $key->m_user_id)->where('m_voucher_type', 1)->where('m_voucher_account', 1)->where('m_voucher_date', $date)->get('master_voucher_tbl')->row();

				$today_exp = $this->db->select('m_exp_amount')->where('m_exp_user', $key->m_user_id)->where('m_exp_added_by', $key->m_user_id)->where('m_exp_date', $date)->where('m_exp_name', 83)->get('master_expenses_tbl')->row();
				$total_expense = isset($today_exp) ? $today_exp->m_exp_amount : 0;
				$today_crate_collection = $this->db->select('sum(m_recvd_qty) as total_crate,m_recvd_crate,crate.m_itgrp_title as cratetype')->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left')->where('m_recvd_user', $key->m_user_id)->where('m_recvd_type', 2)->where('m_recvd_date', $date)->group_by('m_recvd_crate')->get('master_recieved_tbl')->result();

				$res = (object)array(
					"m_user_id" => $key->m_user_id,
					"m_user_name" => $key->m_user_name,
					"m_user_mobile" => $key->m_user_mobile,
					"m_user_group" => $key->m_user_group,
					"total_issue" => $today_issue->total_issue ?: "0",
					"total_sale" => $today_sale->total_sale ?: "0",
					"total_return" => $today_return->total_return ?: "0",
					"balance_stock" => (string)($today_issue->total_issue - $today_sale->total_sale - $today_return->total_return),
					"total_discount" => $today_discount->total_discount ?: "0",
					"total_collection" => $today_collection->total_collection ?: "0",
					"total_expense" => (string)$total_expense,
					"collection_submit" => (string)($today_collection->total_collection - $total_expense),
					"total_crate_collection" => $today_crate_collection,
				);

				$data[] = $res;
			}
		}

		return $data;
	}

	public function send_cratereicvd_sms($voucher_no, $cust_id)
	{
		$cust_detail = $this->Main_model->get_opening_balance($cust_id, date('Y-m-d'));

		$longURL = base_url('Sales/crate_bill_print/' . $voucher_no);

		$shortURL = $this->shorten_url($longURL);

		$mobile = $cust_detail['cust_mobile'];

		$message = "hello " . $cust_detail['cust_name'] . ",\n Your receipt no: " . $voucher_no . " for more detail see the invoice: https://ajayfruits.in/" . $shortURL . " -Ajay Kushwaha & Company";

		$templateid  = '1707170610016597746';
		// echo $message ; 
		// $this->Api_Model->sendSms($message, $mobile, $templateid);
	}

	public function send_paymentreicvd_sms($voucher_no, $cust_id, $amount)
	{
		$cust_detail = $this->Main_model->get_opening_balance($cust_id, date('Y-m-d'));

		$longURL = base_url('Sales/payment_bill_print/' . $voucher_no);

		$shortURL = $this->shorten_url($longURL);

		$mobile = $cust_detail['cust_mobile'];

		$message = "hello " . $cust_detail['cust_name'] . ",\n Your Payment receipt no: " . $voucher_no . " for INR " . $amount . " has been created. Click https://ajayfruits.in/" . $shortURL . " to view the details -Ajay Kushwaha & Company";

		$templateid  = '1707170616108381542';
		// echo $message ; 
		// $this->Api_Model->sendSms($message, $mobile, $templateid);
	}

	public function send_balance_sms($cust_id)
	{

		$cust_detail = $this->Main_model->get_opening_balance($cust_id, date('Y-m-d'));
		$c10kg = '';
		$c20kg = '';
		$c25kg = '';
		if (!empty($cust_detail['crateitems'])) {
			foreach ($cust_detail['crateitems'] as $balcrt) {
				if ($balcrt['name'] == '10 KG') {
					$c10kg = $balcrt['balance'];
				} else if ($balcrt['name'] == '20 KG') {
					$c20kg = $balcrt['balance'];
				} else if ($balcrt['name'] == '25 KG') {
					$c25kg = $balcrt['balance'];
				}
			}
		}
		$mobile = $cust_detail['cust_mobile'];

		$message = $cust_detail['cust_name'] . " , Please clear your pending balance of INR " . $cust_detail['balance_amount'] . " and total crate balance of 10kg - " . $c10kg . " , 20kg - " . $c20kg . ", 25kg - " . $c25kg . " ASAP.\n -Ajay Kushwaha & Company";
		$templateid  = '1707170375027324717';
		// echo $message;
		// $this->Api_Model->sendSms($message, $mobile, $templateid);
	}

	public function sendSms($mgs, $mobile, $temp_id)
	{

		$userna = 'Ajay Kushwaha';
		$username = urlencode($userna);
		$message = urlencode($mgs);
		$sendername = 'AJAYFR';
		$smstype = 'TRANS';
		$numbers = $mobile;
		$apikey = '3f2f1020-d3ac-4b71-8027-040e44b12f4b';
		$peid = '1701170359226995597';
		$templateid  = $temp_id;


		$url = 'http://sms.bulksmsind.in/v2/sendSMS?username=' . $username . '&message=' . $message . '&sendername=' . $sendername . '&smstype=' . $smstype . '&numbers=' . $numbers . '&apikey=' . $apikey . '&peid=' . $peid . '&templateid=' . $templateid;
		// echo $url ;
		$contents = file_get_contents($url);
		return $contents;
	}

	public function RandomString($length)
	{
		$keys = array_merge(range(0, 9), range('0', '9'));
		$key = "";
		for ($i = 0; $i < $length; $i++) {
			$key .= $keys[mt_rand(0, count($keys) - 1)];
		}
		return $key;
	}

	public function shorten_url($longurl)
	{
		$short_url = "";
		$url = prep_url($longurl);
		$existing_alias = $this->Login_model->alias_from_url($url);

		if ($existing_alias == "") {
			$alias = $this->random_strings(6);
			while ($query = $this->Login_model->does_alias_exist($alias)) {
				$alias = $this->random_strings(6);
			}
			$this->Login_model->save_new_alias($url, $alias);
			$short_url = $alias;
		} else {
			$short_url = $existing_alias;
		}

		return $short_url;
	}

	function random_strings($length)
	{

		$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		return substr(
			str_shuffle($str_result),
			0,
			$length
		);
	}

	function send_whatsapp_message($mobile, $message)
	{

		$api_key = "895e94ca55a647c0ab22698999d0a3fc";
		$url = "https://web.cloudwhatsapp.com/wapp/api/send";

		// Prepare data
		$data = [
			"apikey" => $api_key,
			"mobile" => $mobile,
			"msg" => $message,
		];

		// cURL request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}
}
