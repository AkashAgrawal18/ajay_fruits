<?php defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class Master extends CI_Controller
{
	//=========================Profile============================//

	public function index()
	{
	 echo "Welcome";
	}
	//========================= Item_group ===========================//

	public function Item_group($id = '')
	{
		$data = $this->login_details();
		$data['pagename'] = "Item Group list";
		$data['type'] = 1;
		$data['id'] = $id;
		$data['edit_value'] = $this->Master_model->get_edit_itemgroup($data['id']);
		$data['all_value'] = $this->Master_model->all_itemgroup($data['type']);
		$this->load->view('item_master_list', $data);
	}

	public function Item_unit($id = '')
	{
		$data = $this->login_details();
		$data['pagename'] = "Item Unit list";
		$data['type'] = 2;
		$data['id'] = $id;
		$data['id'] = $id;
		$data['edit_value'] = $this->Master_model->get_edit_itemgroup($data['id']);
		$data['all_value'] = $this->Master_model->all_itemgroup($data['type']);
		$this->load->view('item_master_list', $data);
	}

	public function Item_crate($id = '')
	{
		$data = $this->login_details();
		$data['pagename'] = "Item Crate list";
		$data['type'] = 3;
		$data['id'] = $id;
		$data['id'] = $id;
		$data['edit_value'] = $this->Master_model->get_edit_itemgroup($data['id']);
		$data['all_value'] = $this->Master_model->all_itemgroup($data['type']);
		$this->load->view('item_master_list', $data);
	}

	public function insert_itemgroup()
	{

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
		$data['edit_value'] = $this->Master_model->get_edit_item($data['id']);
		$data['group_lst'] = $this->Master_model->all_active_itemgroup(1);
		$data['unit_lst'] = $this->Master_model->all_active_itemgroup(2);
		$data['crate_lst'] = $this->Master_model->all_active_itemgroup(3);
		$data['all_value'] = $this->Master_model->get_all_item();
		$this->load->view('item_list', $data);
	}

	public function insert_item()
	{

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
	$data['all_value'] = $this->Master_model->get_all_group($data['type']);
	$data['edit_value'] = $this->Master_model->get_edit_group($data['id']);
	$this->load->view('group_list', $data);
}

public function expense_account_list()
{
	$data = $this->login_details();
	$data['pagename'] = "Expense Account";
	$data['type'] = 2;
	$data['id'] = $this->input->get('id');
	$data['group_dtl'] = $this->Master_model->get_all_group(1);
	$data['all_value'] = $this->Master_model->get_all_group($data['type']);
	$data['edit_value'] = $this->Master_model->get_edit_group($data['id']);
	$this->load->view('group_list', $data);
}
public function bank_account_list()
{
	$data = $this->login_details();
	$data['pagename'] = "Bank Account List";
	$data['type'] = 3;
	$data['id'] = $this->input->get('id');
	$data['all_value'] = $this->Master_model->get_all_group($data['type']);
	$data['edit_value'] = $this->Master_model->get_edit_group($data['id']);
	$this->load->view('group_list', $data);
}
public function cash_account_list()
{
	$data = $this->login_details();
	$data['pagename'] = "Cash Account List";
	$data['type'] = 4;
	$data['id'] = $this->input->get('id');
	$data['all_value'] = $this->Master_model->get_all_group($data['type']);
	$data['edit_value'] = $this->Master_model->get_edit_group($data['id']);
	$this->load->view('group_list', $data);
}

public function insert_group()
{
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


	//========================= perm ===========================//

	public function perm_list()
	{
		$data = $this->login_details();
		$data['pagename'] = "All Permission list";
		$data['id'] = $this->input->get('id');
		$data['edit_value'] = $this->Master_model->get_edit_perm($data['id']);
		$data['all_value'] = $this->Master_model->all_perm();
		$this->load->view('permission_list', $data);
	}

	public function insert_perm()
	{

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($data = $this->Master_model->insert_perm()) {

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

	public function delete_perm()
	{

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($data = $this->Master_model->delete_perm()) {
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
	//========================= perm ===========================//


	//========================= userperm ===========================//

	public function userperm_list()
	{
		$data = $this->login_details();
		$data['designid'] = $this->input->get('id');
		$data['user_dtl'] = $this->Master_model->get_edit_design($data['designid']);
		$data['edit_value'] = $this->Master_model->get_userperm_userId($data['designid']);
		$data['all_value'] = $this->Master_model->all_active_perm();
		$data['pagename'] = "All Permission for " . $data['user_dtl']->m_design_name;
		$this->load->view('user_permission_list', $data);
	}

	public function insert_userperm()
	{

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($data = $this->Master_model->insert_userperm()) {

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


	//========================= userperm ===========================//

	//========================= designation ===========================//

	public function designation_list()
	{
		$data = $this->login_details();
		$data['pagename'] = "Designation List";
		$data['id'] = $this->input->get('id');
		$data['all_value'] = $this->Master_model->get_all_design();
		$data['edit_value'] = $this->Master_model->get_edit_design($data['id']);

		$this->load->view('designation_list', $data);
	}

	public function insert_design()
	{

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($data = $this->Master_model->insert_design()) {

				if ($data == 1) {
					$info = array(
						'status' => 'success',
						'message' => 'Designation has been Added successfully!'
					);
				} else if ($data == 2) {
					$info = array(
						'status' => 'success',
						'message' => 'Designation Updated Successfully'
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

	public function delete_design()
	{

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if ($data = $this->Master_model->delete_design()) {
				$info = array(
					'status' => 'success',
					'message' => 'Designation has been Deleted successfully!'
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
	//========================= designation =========================

	
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
        if (isset($is_user_in) || $is_user_in == true) {
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
		if (isset($is_user_in) || $is_user_in == true) {
			return true;
		} else {
			echo json_encode(array('status' => 'error', 'message' => 'You are not Logged in Now!! Please login again.'));
			return false;
		}
	}
	//=====================/Login Validation======================//

	//========================/Profile============================//
}
