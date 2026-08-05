<?php defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class Master extends CI_Controller
{
	//=========================Profile============================//

	public function index()
	{
	 echo "Welcome";
	}

	// Superadmin-only "live" branch cascading: when a form's Branch select
	// changes, JS calls this once to refetch every sibling dropdown
	// (customer/staff/supplier/item/group...) that needs re-scoping, without
	// a page reload.
	//
	// Deliberately takes list_types[] (plural) and answers them all in ONE
	// response. It used to be one request per dropdown, which broke on the
	// second branch change: csrf_regenerate=TRUE rotates the CSRF token on
	// every accepted POST, so three parallel requests produced three
	// different new tokens and the client could not know which one the
	// browser's cookie ended up holding - the next change then failed the
	// CSRF check with a 403 and silently updated nothing (BUG-027).
	// One request == one rotation == one unambiguous token, and it's a
	// single round trip instead of three.
	public function branch_scoped_options()
	{
		// Resolved after csrf_verify() has already rotated them during
		// bootstrap, so this is the token the browser's new cookie holds.
		// Always returned - including on the not-logged-in path below - so
		// the client is never left holding a stale token it can't recover from.
		$response = array(
			'csrf_token_name'  => $this->security->get_csrf_token_name(),
			'csrf_token_value' => $this->security->get_csrf_hash(),
		);

		if ($_SERVER["REQUEST_METHOD"] != "POST") {
			show_404();
			return;
		}

		if ($this->session->userdata('is_user_in') != true) {
			$response['status']  = 'error';
			$response['message'] = 'You are not Logged in Now!! Please login again.';
			echo json_encode($response);
			return;
		}

		$branch_id  = $this->input->post('branch_id');
		$group_type = $this->input->post('group_type');
		$types      = $this->input->post('list_types');

		if (empty($types)) {
			// Single-type callers still work.
			$types = array($this->input->post('list_type'));
		}
		if (!is_array($types)) {
			$types = array($types);
		}

		$lists = array();
		foreach ($types as $type) {
			$lists[$type] = $this->_branch_scoped_list($type, $branch_id, $group_type);
		}

		$response['status'] = 'success';
		$response['lists']  = $lists;
		echo json_encode($response);
	}

	/**
	 * Resolves one dropdown's rows for a branch. Every model method here is
	 * already branch-parameterized - this only picks which one to call.
	 */
	private function _branch_scoped_list($type, $branch_id, $group_type = null)
	{
		switch ($type) {
			case 'customer':
				// get_cust_list() (not get_cust_active_list()) so the result
				// includes m_cust_address, needed by add_customer_group.php's picker.
				return $this->Main_model->get_cust_list(null, null, null, null, null, $branch_id);
			case 'staff':
				return $this->Main_model->get_active_user_list(1, $branch_id);
			case 'supplier':
				return $this->Main_model->get_active_user_list(2, $branch_id);
			case 'loader':
				return $this->Main_model->get_active_user_list(3, $branch_id);
			case 'general':
				return $this->Main_model->get_active_user_list(4, $branch_id);
			case 'investment':
				return $this->Main_model->get_active_user_list(5, $branch_id);
			case 'item':
				return $this->Main_model->get_avilable_item(null, 1, $branch_id);
			case 'group':
				return $this->Master_model->get_all_group($group_type ?: 1, $branch_id);
			default:
				return array();
		}
	}
	//========================= Item_group ===========================//

	public function Item_group($id = '')
	{
		$data = $this->login_details();
		$data['pagename'] = "Item Group list";
		$data['type'] = 1;
		$data['id'] = $id;
		$data['branch_id'] = $this->input->post('branch_id');
		$data['branch_list'] = $this->Main_model->get_user_list(9);
		$data['edit_value'] = $this->Master_model->get_edit_itemgroup($data['id']);
		$data['all_value'] = $this->Master_model->all_itemgroup($data['type'], $data['branch_id']);
		$this->load->view('item_master_list', $data);
	}

	public function Item_unit($id = '')
	{
		$data = $this->login_details();
		$data['pagename'] = "Item Unit list";
		$data['type'] = 2;
		$data['id'] = $id;
		$data['branch_id'] = $this->input->post('branch_id');
		$data['branch_list'] = $this->Main_model->get_user_list(9);
		$data['edit_value'] = $this->Master_model->get_edit_itemgroup($data['id']);
		$data['all_value'] = $this->Master_model->all_itemgroup($data['type'], $data['branch_id']);
		$this->load->view('item_master_list', $data);
	}

	public function Item_crate($id = '')
	{
		$data = $this->login_details();
		$data['pagename'] = "Item Crate list";
		$data['type'] = 3;
		$data['id'] = $id;
		$data['branch_id'] = $this->input->post('branch_id');
		$data['branch_list'] = $this->Main_model->get_user_list(9);
		$data['edit_value'] = $this->Master_model->get_edit_itemgroup($data['id']);
		$data['all_value'] = $this->Master_model->all_itemgroup($data['type'], $data['branch_id']);
		$this->load->view('item_master_list', $data);
	}

	public function insert_itemgroup()
	{
		if ($this->ajax_login() === false) {
			return;
		}
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($data = $this->Master_model->insert_itemgroup()) {

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
						'message' => $data,
					);
				}
			} else {
				$info = array(
					'status' => 'error',
					'message' => 'Some problem Occurred!! please try again'
				);
			}
			echo json_encode($info);
		}
	}

	public function delete_itemgroup()
	{
		if ($this->ajax_login() === false) {
			return;
		}
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($data = $this->Master_model->delete_itemgroup()) {
				$info = array(
					'status' => 'success',
					'message' => 'Data has been Deleted successfully!'
				);
			} else {
				$info = array(
					'status' => 'error',
					'message' => 'Some problem Occurred!! please try again'
				);
			}
			echo json_encode($info);
		}
	}
	//========================= Item_group ===========================//

	

	//========================= item ===========================//

	public function item_list($id='')
	{
		$data = $this->login_details();
		$data['pagename'] = "All items list";
		$data['id'] = $id;
		$data['branch_id'] = $this->input->post('branch_id');
		$data['branch_list'] = $this->Main_model->get_user_list(9);
		$data['edit_value'] = $this->Master_model->get_edit_item($data['id']);
		$form_branch_id = !empty($data['edit_value']) ? $data['edit_value']->m_item_branch : $data['branch_id'];
		$data['group_lst'] = $this->Master_model->all_active_itemgroup(1, $form_branch_id);
		$data['unit_lst'] = $this->Master_model->all_active_itemgroup(2, $form_branch_id);
		$data['crate_lst'] = $this->Master_model->all_active_itemgroup(3, $form_branch_id);
		$data['all_value'] = $this->Master_model->get_all_item('', $data['branch_id']);
		$this->load->view('item_list', $data);
	}

	public function insert_item()
	{
		if ($this->ajax_login() === false) {
			return;
		}
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($data = $this->Master_model->insert_item()) {

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
						'message' => $data,
					);
				}
			} else {
				$info = array(
					'status' => 'error',
					'message' => 'Some item Occurred!! please try again'
				);
			}
			echo json_encode($info);
		}
	}

	public function delete_item()
	{
		if ($this->ajax_login() === false) {
			return;
		}
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($data = $this->Master_model->delete_item()) {
				$info = array(
					'status' => 'success',
					'message' => 'Data has been Deleted successfully!'
				);
			} else {
				$info = array(
					'status' => 'error',
					'message' => 'Some item Occurred!! please try again'
				);
			}
			echo json_encode($info);
		}
	}

	public function import_items()
	{
		$this->require_login();
		//$salon_id = $this->session->userdata('s_id');
		if (isset($_FILES['import_file'])) {
			require_once "Simplexlsx.class.php";
			$xlsx = new SimpleXLSX($_FILES['import_file']['tmp_name']);
			list($cols, $rows) = $xlsx->dimension();
			$i = 0;
			foreach ($xlsx->rows() as $row) {
				$i++;
				if ($i != 1) {
					$checkGroup = $this->db->select('m_itgrp_id')->where('m_itgrp_type',1)->where('m_itgrp_title', $row[2])->get('master_itemgroup_tbl')->row();
					$checkUnit = $this->db->select('m_itgrp_id')->where('m_itgrp_type',2)->where('m_itgrp_title', $row[3])->get('master_itemgroup_tbl')->row();
					$checkCrate = $this->db->select('m_itgrp_id')->where('m_itgrp_type',3)->where('m_itgrp_title', $row[4])->get('master_itemgroup_tbl')->row();
					
					
					$checkcity = $this->db->where("m_item_group",$checkGroup->m_itgrp_id)->where("m_item_unit",$checkUnit->m_itgrp_id)->where("m_item_crate",$checkCrate->m_itgrp_id)->where('m_item_name', $row[1])->get('master_item_tbl')->result();
					if (empty($checkcity)) {
						$data = array(
							"m_item_name" => $row[1],
							"m_item_group" => $checkGroup->m_itgrp_id ?:'',
							"m_item_unit" => $checkUnit->m_itgrp_id ?:'',
							"m_item_crate" => $checkCrate->m_itgrp_id ?:'',
							"m_item_price" => 1,
							"m_item_status" => 1,
							"m_item_added_on" => date('Y-m-d H:i'),

						);
						$insert = $this->db->insert('master_item_tbl', $data);
					}
				}
			}
			echo "<script> alert('import Successfull'); </script>";
			redirect('Master/state_list');
		} else {
			echo "<script> alert('Import is wrong'); </script>";
		}
	}


	//========================= item ===========================//

//=========================/Group===========================//
public function group_list()
{
	$data = $this->login_details();
	$data['pagename'] = "Group List";
	$data['type'] = 1;
	$data['id'] = $this->input->get('id');
	$data['branch_id'] = $this->input->post('branch_id');
	$data['branch_list'] = $this->Main_model->get_user_list(9);
	$data['all_value'] = $this->Master_model->get_all_group($data['type'], $data['branch_id']);
	$data['edit_value'] = $this->Master_model->get_edit_group($data['id']);
	$this->load->view('group_list', $data);
}

public function expense_account_list()
{
	$data = $this->login_details();
	$data['pagename'] = "Expense Account";
	$data['type'] = 2;
	$data['id'] = $this->input->get('id');
	$data['branch_id'] = $this->input->post('branch_id');
	$data['branch_list'] = $this->Main_model->get_user_list(9);
	$data['group_dtl'] = $this->Master_model->get_all_group(1, $data['branch_id']);
	$data['all_value'] = $this->Master_model->get_all_group($data['type'], $data['branch_id']);
	$data['edit_value'] = $this->Master_model->get_edit_group($data['id']);
	$this->load->view('group_list', $data);
}
public function bank_account_list()
{
	$data = $this->login_details();
	$data['pagename'] = "Bank Account List";
	$data['type'] = 3;
	$data['id'] = $this->input->get('id');
	$data['branch_id'] = $this->input->post('branch_id');
	$data['branch_list'] = $this->Main_model->get_user_list(9);
	$data['all_value'] = $this->Master_model->get_all_group($data['type'], $data['branch_id']);
	$data['edit_value'] = $this->Master_model->get_edit_group($data['id']);
	$this->load->view('group_list', $data);
}
public function cash_account_list()
{
	$data = $this->login_details();
	$data['pagename'] = "Cash Account List";
	$data['type'] = 4;
	$data['id'] = $this->input->get('id');
	$data['branch_id'] = $this->input->post('branch_id');
	$data['branch_list'] = $this->Main_model->get_user_list(9);
	$data['all_value'] = $this->Master_model->get_all_group($data['type'], $data['branch_id']);
	$data['edit_value'] = $this->Master_model->get_edit_group($data['id']);
	$this->load->view('group_list', $data);
}

public function insert_group()
{
	if ($this->ajax_login() === false) {
		return;
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if ($data = $this->Master_model->insert_group()) {

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
			}
		} else {
			$info = array(
				'status' => 'error',
				'message' => 'Some problem Occurred!! please try again'
			);
		}
		echo json_encode($info);
	}
}

public function delete_group()
{
	if ($this->ajax_login() === false) {
		return;
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if ($data = $this->Master_model->delete_group()) {

			$info = array(
				'status' => 'success',
				'message' => 'Data has been Deleted successfully!'
			);
		} else {
			$info = array(
				'status' => 'error',
				'message' => 'Some problem Occurred!! please try again'
			);
		}
		echo json_encode($info);
	}
}

	//=========================/Group===========================//






















	//=========================/state===========================//
	public function state_list()
	{
		$data = $this->login_details();
		$data['pagename'] = "State List";
		$data['id'] = $this->input->get('id');
		$data['all_value'] = $this->Master_model->get_all_state();
		$data['edit_value'] = $this->Master_model->get_edit_state($data['id']);
		$this->load->view('State_list', $data);
	}

	public function insert_state()
	{
		if ($this->ajax_login() === false) {
			return;
		}
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($data = $this->Master_model->insert_state()) {

				if ($data == 1) {
					$info = array(
						'status' => 'success',
						'message' => 'State has been Added successfully!'
					);
				} else if ($data == 2) {
					$info = array(
						'status' => 'success',
						'message' => 'State Updated Successfully'
					);
				}
			} else {
				$info = array(
					'status' => 'error',
					'message' => 'Some problem Occurred!! please try again'
				);
			}
			echo json_encode($info);
		}
	}

	public function delete_state()
	{
		if ($this->ajax_login() === false) {
			return;
		}
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($data = $this->Master_model->delete_state()) {

				$info = array(
					'status' => 'success',
					'message' => 'State has been Deleted successfully!'
				);
			} else {
				$info = array(
					'status' => 'error',
					'message' => 'Some problem Occurred!! please try again'
				);
			}
			echo json_encode($info);
		}
	}

	public function import_state_city()
	{
		$this->require_login();
		//$salon_id = $this->session->userdata('s_id');
		if (isset($_FILES['import_file'])) {
			require_once "Simplexlsx.class.php";
			$xlsx = new SimpleXLSX($_FILES['import_file']['tmp_name']);
			list($cols, $rows) = $xlsx->dimension();
			$i = 0;
			foreach ($xlsx->rows() as $row) {
				$i++;
				if ($i != 1) {
					$checkState = $this->db->where('m_state_name', $row[1])->get('master_state_tbl')->result();
					if (empty($checkState)) {
						$s_data = array(
							"m_state_name" => $row[1],
							"m_state_country" => 1,
							"m_state_status" => 1,
							"m_state_added_on" => date('Y-m-d H:i'),
						);
						$this->db->insert('master_state_tbl', $s_data);
						$state_id = $this->db->insert_id();
					} else {
						$state_id = $checkState[0]->m_state_id;
					}
					$checkcity = $this->db->where('m_city_name', $row[2])->get('master_city_tbl')->result();
					if (empty($checkcity)) {
						$data = array(
							"m_city_name" => $row[2],
							"m_city_state" => $state_id,
							"m_city_country" => 1,
							"m_city_status" => 1,
							"m_city_added_on" => date('Y-m-d H:i'),

						);
						$insert = $this->db->insert('master_city_tbl', $data);
					}
				}
			}
			echo "<script> alert('import Successfull'); </script>";
			redirect('Master/state_list');
		} else {
			echo "<script> alert('Import is wrong'); </script>";
		}
	}

	//=========================/state===========================//

	//-------------------------- city ------------------------//
	public function city_list()
	{
		$data = $this->login_details();
		$data['pagename'] = "City List";
		$data['id'] = $this->input->get('id');
		$data['get_group'] = $this->Master_model->get_all_active_group(1);
		$data['get_active_state'] = $this->Master_model->get_active_state();
		$data['all_value'] = $this->Master_model->get_all_city();
		$data['edit_value'] = $this->Master_model->get_edit_city($data['id']);

		$this->load->view('City_list', $data);
	}

	public function insert_city()
	{
		if ($this->ajax_login() === false) {
			return;
		}
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($data = $this->Master_model->insert_city()) {

				if ($data == 1) {
					$info = array(
						'status' => 'success',
						'message' => 'City has been Added successfully!'
					);
				} else if ($data == 2) {
					$info = array(
						'status' => 'success',
						'message' => 'City Updated Successfully'
					);
				}
			} else {
				$info = array(
					'status' => 'error',
					'message' => 'Some problem Occurred!! please try again'
				);
			}
			echo json_encode($info);
		}
	}

	public function delete_city()
	{
		if ($this->ajax_login() === false) {
			return;
		}
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($data = $this->Master_model->delete_city()) {
				$info = array(
					'status' => 'success',
					'message' => 'City has been Deleted successfully!'
				);
			} else {
				$info = array(
					'status' => 'error',
					'message' => 'Some problem Occurred!! please try again'
				);
			}
			echo json_encode($info);
		}
	}



	//-------------------------- city ------------------------//


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
