<?php defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class Reports extends CI_Controller
{
    public function index()
    {
        echo "Welcome";
    }

    public function account_ledger()
    {
        if ($this->session->userdata('is_cust_in') == true) {
            if (empty($this->session->userdata('cust_id'))) {
                redirect('CustLogin');
            }
        } else {
            $data = $this->login_details();
        }
        $data['pagename'] = "Account Ledger";
        $data['branch_id'] = $this->session->userdata('is_cust_in') == true ? null : $this->input->post('branch_id');
        $data['all_crates'] = $this->Master_model->all_itemgroup(3);
        $data['cust_dtl'] = $this->Main_model->get_cust_active_list('', $data['branch_id']);
        $data['supl_dtl'] = $this->Main_model->get_active_user_list(2, $data['branch_id']);
        $data['expense_lst'] = $this->Master_model->get_all_active_group(2, $data['branch_id']);
        $data['bank_lst'] = $this->Master_model->get_all_active_group(3, $data['branch_id']);
        $data['cash_lst'] = $this->Master_model->get_all_active_group(4, $data['branch_id']);
        $data['agent_dtl'] = $this->Main_model->get_active_user_list(1, $data['branch_id']);
        $data['general_lst'] = $this->Main_model->get_active_user_list(4, $data['branch_id']);
        $data['investement_lst'] = $this->Main_model->get_active_user_list(5, $data['branch_id']);
        if ($this->session->userdata('is_cust_in') != true) {
            $data['branch_list'] = $this->Main_model->get_user_list(9);
        }
        $data['pagetype'] = 3;
        $this->load->view('balance_report', $data);
    }
    public function reports_list()
    {
        $data = $this->login_details();
        $data['pagename'] = "Reports List";
        $data['all_crates'] = $this->Master_model->all_itemgroup(3);
        $data['cust_dtl'] = $this->Main_model->get_cust_active_list();
        $data['supl_dtl'] = $this->Main_model->get_active_user_list(2);
        $data['expense_lst'] = $this->Master_model->get_all_active_group(2);
        $data['bank_lst'] = $this->Master_model->get_all_active_group(3);
        $data['cash_lst'] = $this->Master_model->get_all_active_group(4);
        $data['agent_dtl'] = $this->Main_model->get_active_user_list(1);
        $data['branch_list'] = $this->Main_model->get_user_list(9);
        $data['pagetype'] = 2;
        $this->load->view('balance_report', $data);
    }

    public function balance_report()
    {
        $data = $this->login_details();
        $data['pagename'] = "Balance Report";
        $data['branch_id'] = $this->input->post('branch_id');
        $data['all_crates'] = $this->Master_model->all_itemgroup(3, $data['branch_id']);
        $data['cust_dtl'] = $this->Main_model->get_cust_active_list('', $data['branch_id']);
        $data['supl_dtl'] = $this->Main_model->get_active_user_list(2, $data['branch_id']);
        $data['expense_lst'] = $this->Master_model->get_all_active_group(2, $data['branch_id']);
        $data['bank_lst'] = $this->Master_model->get_all_active_group(3, $data['branch_id']);
        $data['cash_lst'] = $this->Master_model->get_all_active_group(4, $data['branch_id']);
        $data['agent_dtl'] = $this->Main_model->get_active_user_list(1, $data['branch_id']);
        $data['branch_list'] = $this->Main_model->get_user_list(9);

        $data['pagetype'] = 1;
        $this->load->view('balance_report', $data);
    }

    public function cust_blncrate_report()
    {
        $data = $this->login_details();
        $data['pagename'] = "Crate Balance Report";
        $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d');
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');
        $data['agent'] = $this->input->post('agent');
        $data['cratetype'] = $this->input->post('cratetype');
        $data['orderby'] = $this->input->post('orderby');
        $branch_id = $this->input->post('branch_id');

        if ($data['agent'] == 'o') {
            $agent_name = 'Admin';
        } else {
            $agent_dtl = $this->Main_model->get_user_group_dtl($data['agent'], $branch_id);
            $agent_name = row_val($agent_dtl, 'm_user_name', 'All/Admin');
        }

        $data['subhead'] = '<div class="col-4">
        <h4 class="m-0">Agent- ' . $agent_name . '</h4>

        </div>
        <div class="col-8 text-end">
        <h4 class="fw-bold">Customer Statements ' . date('d/m/Y', strtotime($data['from_date'])) . ' TO ' . date('d/m/Y', strtotime($data['todate'])) . '</h4>
        </div>';

        $data['tableheader'] = array('Sno', 'Customer Name', 'Opening Balance', 'Total Billing', 'Total Recipt', 'Net Balance');
        $data['all_crates'] = $this->Master_model->all_itemgroup(3, $branch_id);

        foreach ($data['all_crates'] as $craty) {
            $data['tableheader'][] = $craty->m_itgrp_title;
        }
        $data['tableheader'][] = 'Total Crate';
        $data['tableheader'][] = 'Last Bill';
        $data['tableheader'][] = 'Last Recipt';
        $all_cust = $this->Main_model->get_cust_list(null, null, null, $data['orderby'], $data['agent'], $branch_id);
        $tbody = '';
        $sum_ope = 0;
        $sum_bill = 0;
        $sum_recipt = 0;
        $sum_netamt = 0;
        $sum_10 = 0;
        $sum_20 = 0;
        $sum_25 = 0;
        $sum_tocr = 0;
        if (!empty($all_cust)) {

            // Last bill / last receipt for every customer up front: two queries
            // instead of two per customer. Same rule as before - the date of the
            // highest id row, not MAX(date).
            $last_bill_map = $this->Report_model->last_sale_date_map();
            $last_rcpt_map = $this->Report_model->last_receipt_date_map();
            $bal_updates = array();

            // Opening and closing figures for every customer in twelve grouped
            // queries, replacing two get_opening_balance() calls per customer
            // (~12 queries each). Same arithmetic, assembled by snapshot_row().
            $open_snap  = $this->Report_model->balance_snapshot(date('Y-m-d', strtotime($data['from_date'] . '-1day')), $branch_id);
            $close_snap = $this->Report_model->balance_snapshot(date('Y-m-d', strtotime($data['todate'])), $branch_id);
            $crate_types = $this->Master_model->all_itemgroup(3);

            foreach ($all_cust as $key => $cust) {
                $last_bill_date = $last_bill_map[$cust->m_cust_id] ?? null;
                $last_recipt = $last_rcpt_map[$cust->m_cust_id] ?? null;
                $lastbilldate =  !empty($last_bill_date) ? date('d-m-Y', strtotime($last_bill_date)) : '';
                $lastrecptdate =  !empty($last_recipt) ? date('d-m-Y', strtotime($last_recipt)) : '';

                $opening_balance = $this->Report_model->snapshot_row($open_snap, $cust, $crate_types);
                $closing_balance = $this->Report_model->snapshot_row($close_snap, $cust, $crate_types);
                $cratedata = '';
                // Cached balances are written back below in one batch rather
                // than four UPDATEs per customer; seed from the current row so
                // every batch entry carries the same columns.
                $upd = array(
                    'm_cust_id'    => $cust->m_cust_id,
                    'm_cust_10bal' => $cust->m_cust_10bal,
                    'm_cust_20bal' => $cust->m_cust_20bal,
                    'm_cust_25bal' => $cust->m_cust_25bal,
                );
                foreach ($closing_balance['crateitems'] as $craty) {

                    if ($craty['name'] == '10 KG') {
                        $sum_10 += $craty['balance'];
                        $upd['m_cust_10bal'] = $craty['balance'];
                    } else if ($craty['name'] == '20 KG') {
                        $sum_20 += $craty['balance'];
                        $upd['m_cust_20bal'] = $craty['balance'];
                    } else if ($craty['name'] == '25 KG') {
                        $sum_25 += $craty['balance'];
                        $upd['m_cust_25bal'] = $craty['balance'];
                    }

                    $cratedata .= '<td>' . $craty['balance'] . '</td>';
                }


                $sum_ope += $opening_balance['balance_amount'];
                $sum_bill += ($closing_balance['grand_total'] - $opening_balance['grand_total']);
                $sum_recipt += ($closing_balance['amount_rcvd'] - $opening_balance['amount_rcvd']);
                $sum_netamt += $closing_balance['balance_amount'];
                $sum_tocr += $closing_balance['balance_crate'];

                $upd['m_cust_balance'] = $closing_balance['balance_amount'];
                $bal_updates[] = $upd;

                $tbody .= '<tr>
        <td>' . ($key + 1) . '</td>
        <td>' . $cust->m_cust_name . '-' . $cust->m_cust_mobile . '</td>
        <td>' . money2($opening_balance['balance_amount']) . '</td>
        <td>' . money2($closing_balance['grand_total'] - $opening_balance['grand_total']) . '</td>
        <td>' . money2($closing_balance['amount_rcvd'] - $opening_balance['amount_rcvd']) . '</td>
        <td>' . money2($closing_balance['balance_amount']) . '</td>
       ' . $cratedata . '
        <td>' . $closing_balance['balance_crate'] . '</td>
        <td>' . $lastbilldate  . '</td>
        <td>' . $lastrecptdate . '</td>

        </tr>';
            }

            if (!empty($bal_updates)) {
                $this->db->update_batch('master_customer_tbl', $bal_updates, 'm_cust_id', 100);
            }
        }

        $tfoot = '<tr>
        <th colspan="2">Total</th>
        <th>' .  money2($sum_ope) . '</th>
        <th>' .  money2($sum_bill) . '</th>
        <th>' .  money2($sum_recipt) . '</th>
        <th>' .  money2($sum_netamt) . '</th>
        <th>' .  $sum_10 . '</th>
        <th>' .  $sum_20 . '</th>
        <th>' .  $sum_25 . '</th>
        <th>' . $sum_tocr . '</th>
        <th colspan="2"></th>
         </tr>';
        $data['Mainarray'] =  $tbody;
        $data['tablefoot'] =  $tfoot;

        $this->load->view('print_report_list', $data);
    }



    public function supplier_blncrate_report()
    {
        $data = $this->login_details();
        $data['pagename'] = "Crate Balance Report";
        $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d');
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');
        $data['agent'] = $this->input->post('agent');
        $data['cratetype'] = $this->input->post('cratetype');
        $data['orderby'] = $this->input->post('orderby');
        $branch_id = $this->input->post('branch_id');
        $data['subhead'] = '<div class="col-12">
        <h4 class="text-center">Supplier Statements From ' . date('d-m-Y', strtotime($data['from_date'])) . ' To ' . date('d-m-Y', strtotime($data['todate'])) . '</h4>
    </div>';

        $data['tableheader'] = array('Sno', 'Suppiler Name', 'Opening Balance', 'Total Billing', 'Total Payment', 'Net Balance');
        $data['all_crates'] = $this->Master_model->all_itemgroup(3, $branch_id);

        foreach ($data['all_crates'] as $craty) {
            $data['tableheader'][] = $craty->m_itgrp_title;
        }
        $data['tableheader'][] = 'Total Crate';
        $all_cust = $this->Main_model->get_user_list(2, null, null, null, $data['orderby'], null, $branch_id);
        $tbody = '';
        $sum_ope = 0;
        $sum_bill = 0;
        $sum_recipt = 0;
        $sum_netamt = 0;
        $sum_10 = 0;
        $sum_20 = 0;
        $sum_25 = 0;
        $sum_tocr = 0;
        if (!empty($all_cust)) {

            foreach ($all_cust as $key => $cust) {
                $opening_balance = $this->Report_model->get_sup_opening_balance($cust->m_user_id, date('Y-m-d', strtotime($data['from_date'] . '-1day')), $branch_id);
                $closing_balance = $this->Report_model->get_sup_opening_balance($cust->m_user_id, date('Y-m-d', strtotime($data['todate'])), $branch_id);
                $cratedata = '';

                foreach ($closing_balance['crateitems'] as $craty) {
                    if ($craty['name'] == '10 KG') {
                        $sum_10 += $craty['balance'];
                        $this->db->set('m_user_10bal', $craty['balance'])->where('m_user_id', $cust->m_user_id)->update('master_users_tbl');
                    } else if ($craty['name'] == '20 KG') {
                        $sum_20 += $craty['balance'];
                        $this->db->set('m_user_20bal', $craty['balance'])->where('m_user_id', $cust->m_user_id)->update('master_users_tbl');
                    } else if ($craty['name'] == '25 KG') {
                        $sum_25 += $craty['balance'];
                        $this->db->set('m_user_25bal', $craty['balance'])->where('m_user_id', $cust->m_user_id)->update('master_users_tbl');
                    }

                    $cratedata .= '<td>' . $craty['balance'] . '</td>';
                }

                $this->db->set('m_user_balance', $closing_balance['balance_amount'])->where('m_user_id', $cust->m_user_id)->update('master_users_tbl');

                $sum_ope += $opening_balance['balance_amount'];
                $sum_bill += ($closing_balance['grand_total'] - $opening_balance['grand_total']);
                $sum_recipt += ($closing_balance['amount_rcvd'] - $opening_balance['amount_rcvd']);
                $sum_netamt += $closing_balance['balance_amount'];
                $sum_tocr += $closing_balance['balance_crate'];


                $tbody .= '<tr>
        <td>' . ($key + 1) . '</td>
        <td>' . $cust->m_user_name . '-' . $cust->m_user_mobile . '</td>
        <td>' . money2($opening_balance['balance_amount']) . '</td>
        <td>' . money2($closing_balance['grand_total'] - $opening_balance['grand_total']) . '</td>
        <td>' . money2($closing_balance['amount_rcvd'] - $opening_balance['amount_rcvd']) . '</td>
        <td>' . money2($closing_balance['balance_amount']) . '</td>
       ' . $cratedata . '
        <td>' . $closing_balance['balance_crate'] . '</td>
       
        </tr>';
            }
        }

        $tfoot = '<tr>
        <th colspan="2">Total</th>
        <th>' .  money2($sum_ope) . '</th>
        <th>' .  money2($sum_bill) . '</th>
        <th>' .  money2($sum_recipt) . '</th>
        <th>' .  money2($sum_netamt) . '</th>
        <th>' .  $sum_10 . '</th>
        <th>' .  $sum_20 . '</th>
        <th>' .  $sum_25 . '</th>
        <th>' . $sum_tocr . '</th>
        <th colspan="2"></th>
         </tr>';
        $data['Mainarray'] =  $tbody;
        $data['tablefoot'] =  $tfoot;


        $this->load->view('print_report_list', $data);
    }


    public function credit_sale()
    {
        $data = $this->login_details();
        $data['pagename'] = "Credit Sale Report";
        $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d');
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');
        $data['customers'] = $this->input->post('customers');
        $branch_id = $this->input->post('branch_id');

        $summary = $this->input->post('summary');

        $data['orderby'] = 'asc';
        $data['subhead'] = '<div class="col-12">
        <h4 class="text-center">Credit Sales Report From ' . date('d-m-Y', strtotime($data['from_date'])) . ' To ' . date('d-m-Y', strtotime($data['todate'])) . '</h4>
         </div>';
        if ($summary == 1) {
            $data['tableheader'] = array('Sno', 'Date', 'Customer Name', 'Sale No', 'Total Qty', 'Net Total');
        } else {
            $data['tableheader'] = array('Sno', 'Date', 'Customer Name', 'Sale No', 'Total Qty', 'Total Amount', 'Commission', 'Fright', 'Hamali', 'Other', 'Net Total');
        }

        $all_value = $this->Main_model->sales_group($data['from_date'], $data['todate'], $data['customers'], null, null, $data['orderby'], null, $branch_id);
        $tbody = '';
        $filedfoot = '';
        $sum_tqty = 0;
        $sum_tamt = 0;
        $sum_comm = 0;
        $sum_frigh = 0;
        $sum_ham = 0;
        $sum_oth = 0;
        $sum_gndamt = 0;
        if (!empty($all_value)) {

            foreach ($all_value as $key => $cust) {
                $sum_tqty += $cust->tqty;
                $sum_tamt += $cust->total_amount;
                $sum_comm += $cust->m_sale_comm;
                $sum_frigh += $cust->m_sale_fright;
                $sum_ham += $cust->m_sale_hamali;
                $sum_oth += $cust->m_sale_others;
                $sum_gndamt += ($cust->total_amount + $cust->total_expense);
                if ($summary == 2) {
                    $filed = ' <td>' . $cust->total_amount . '</td> 
          <td>' . $cust->m_sale_comm . '</td> 
          <td>' . $cust->m_sale_fright . '</td> 
          <td>' . $cust->m_sale_hamali . '</td> 
          <td>' . $cust->m_sale_others . '</td>';

                    $filedfoot = ' <th>' . $sum_tamt . '</th> 
          <th>' . $sum_comm . '</th> 
          <th>' . $sum_frigh . '</th> 
          <th>' . $sum_ham . '</th> 
          <th>' . $sum_oth . '</th>';
                } else {
                    $filed = '';
                    $filedfoot = '';
                }

                $tbody .= '<tr>
        <td>' . ($key + 1) . '</td>
        <td>' . date('d-m-Y', strtotime($cust->m_sale_date)) . '</td>
        <td>' . $cust->m_cust_name . '</td>
        <td>' . $cust->m_sale_spo . '</td>
        <td>' . $cust->tqty . '</td>
        ' . $filed . '
        <td>' . ($cust->total_amount + $cust->total_expense) . '</td>
         </tr>';
            }
        }

        $tfoot = '<tr>
        <th colspan="4">Total</th>
        <th>' .  $sum_tqty . '</th>
        ' . $filedfoot . '
        <th>' . $sum_gndamt . '</th>
         </tr>';
        $data['Mainarray'] =  $tbody;

        $data['tablefoot'] =  $tfoot;

        $this->load->view('print_report_list', $data);
    }

    public function staff_report()
    {
        $data = $this->login_details();
        $data['pagename'] = "Staff Sale Report";
        $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d');
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');
        $data['agent'] = $this->input->post('agent');
        $summary = $this->input->post('summary');
        $data['orderby'] = $this->input->post('orderby');

        $agent_dtl = $this->Main_model->get_user_dtl($data['agent']);
        $all_value = $this->Report_model->sales_item_group($data['from_date'], $data['todate'], $data['agent']);
        $data['subhead'] = '<div class="col-12">
        <h4 class="text-center"> ' . row_val($agent_dtl, 'm_user_name', 'All/Admin') . ' Sale Report From ' . date('d-m-Y', strtotime($data['from_date'])) . ' To ' . date('d-m-Y', strtotime($data['todate'])) . '</h4>
         </div>';

        if ($summary == 1) {
            $data['tableheader'] = array('Sno', 'Item Name', 'Unit', 'Total Qty', 'Total Weight', 'Net Total');
        } else {
            $data['tableheader'] = array('Sno', 'Item Name', 'Unit', 'Total Qty', 'Total Weight', 'Net Total');
        }

        $tbody = '';
        $sum_tqty = 0;
        $sum_twhgt = 0;
        $sum_gndamt = 0;
        if (!empty($all_value)) {

            foreach ($all_value as $key => $cust) {

                //         if ($summary == 2) {
                //             $filed = ' <td>' . $cust->total_amount . '</td> 
                //   <td>' . $cust->m_sale_comm . '</td> 
                //   <td>' . $cust->m_sale_fright . '</td> 
                //   <td>' . $cust->m_sale_hamali . '</td> 
                //   <td>' . $cust->m_sale_others . '</td>';
                //         } else {
                //             $filed = '';
                //         }

                $sum_tqty += $cust->tqty;
                $sum_twhgt += $cust->twght;
                $sum_gndamt += $cust->total_amount;

                $tbody .= '<tr>
        <td>' . ($key + 1) . '</td>
        <td>' . $cust->m_item_name . '</td>
        <td>' . $cust->m_itgrp_title . '</td>
   
        <td>' . $cust->tqty . '</td>
        <td>' . $cust->twght . '</td>
        <td>' . $cust->total_amount . '</td>
         </tr>';
            }
        }

        $tfoot = '<tr>
        <th colspan="3">Total</th>
        <th>' . $sum_tqty . '</th>
        <th>' . $sum_twhgt . '</th>
        <th>' . $sum_gndamt . '</th>
         </tr>';
        $data['Mainarray'] =  $tbody;

        $data['tablefoot'] =  $tfoot;

        $this->load->view('print_report_list', $data);
    }

    public function phone_book()
    {
        $data = $this->login_details();
        $data['pagename'] = "Phone Book";
        $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d');
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');
        $data['agent'] = $this->input->post('agent');
        $data['cratetype'] = $this->input->post('cratetype');
        $data['orderby'] = $this->input->post('orderby');

        $agent_dtl = $this->Main_model->get_user_dtl($data['agent']);
        $agent_name = row_val($agent_dtl, 'm_user_name', 'All/Admin');
        $data['subhead'] = '<div class="col-8">
        <h4 class="m-0">Agent- ' . $agent_name . '</h4>
       
        </div>
        <div class="col-4 text-end">
        <h4 class="fw-bold">Phone Book ' . date('d-m-Y') . '</h4>
        </div>';

        $data['tableheader'] = array('Sno', 'Customer Name', 'Phone Number');

        $all_cust = $this->Main_model->get_cust_list(null, null, null, $data['orderby'], $agent_dtl->m_user_group);
        $tbody = '';
        if (!empty($all_cust)) {

            foreach ($all_cust as $key => $cust) {

                $tbody .= '<tr>
        <td>' . ($key + 1) . '</td>
        <td>' . $cust->m_cust_name . '</td>
        <td>' . $cust->m_cust_mobile . '</td>
        
        </tr>';
            }
        }

        $data['Mainarray'] =  $tbody;
        $data['tablefoot'] =  '';

        $this->load->view('print_report_list', $data);
    }

    public function crate_recieve($type)
    {
        $data = $this->login_details();
        if ($type == 1) {
            $data['pagename'] = "Recipt Cash/Ac Report";
            $data['tableheader'] = array('Sno', 'Date', 'Account Name', 'Receipt No', 'Method', 'Net Total');
            $hidden = '';
        } else {
            $data['pagename'] = "Crate Recieve Report";
            $data['tableheader'] = array('Sno', 'Date', 'Account Name', 'Receipt No', 'Total Qty');
            $hidden = 'class="d-none"';
        }
        $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d');
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');
        $data['customers'] = $this->input->post('customers');
        $branch_id = $this->input->post('branch_id');

        $data['orderby'] = 'asc';
        $data['subhead'] = '<div class="col-12">
        <h4 class="text-center">' . $data['pagename'] . ' From ' . date('d-m-Y', strtotime($data['from_date'])) . ' To ' . date('d-m-Y', strtotime($data['todate'])) . '</h4>
        </div>';


        $all_value = $this->Main_model->get_received_list($type, $data['from_date'], $data['todate'], $data['customers'], null, null, null, null, $data['orderby'], $branch_id);
        $tbody = '';
        $sum_tqty = 0;
        $sum_gndamt = 0;
        if (!empty($all_value)) {

            foreach ($all_value as $key => $cust) {
                $sum_tqty += $cust->m_recvd_qty;
                $sum_gndamt += $cust->m_recvd_amount;

                if ($type == 1) {
                    $filed = '<td>' . $cust->method_name . '</td>
      
        <td>' .  $cust->m_recvd_amount . '</td>';
                } else {
                    $filed = '<td>' . $cust->m_recvd_qty . '</td>';
                }

                $tbody .= '<tr>
        <td>' . ($key + 1) . '</td>
        <td>' . date('d-m-Y', strtotime($cust->m_recvd_date)) . '</td>
        <td>' . $cust->m_cust_name . '</td>
        <td>' . $cust->m_recvd_voucher . '</td>'
                    . $filed . '
         </tr>';
            }
        }

        $tfoot = '<tr>
        <th colspan="4">Total</th>
        <th>' .  $sum_tqty . '</th>
        <th ' . $hidden . '>' . $sum_gndamt . '</th>
         </tr>';

        $data['Mainarray'] =  $tbody;

        $data['tablefoot'] =  $tfoot;

        $this->load->view('print_report_list', $data);
    }

    public function payment_report($type)
    {
        $data = $this->login_details();
        if ($type == 1) {
            $data['pagename'] = "Payment Cash/Ac Report";
            $data['tableheader'] = array('Sno', 'Date', 'Account Name', 'Voucher No', 'Method', 'Cash', 'Account', 'Net Total');
        } else {
            $data['pagename'] = "Crate Issue Report";
            $data['tableheader'] = array('Sno', 'Date', 'Account Name', 'Voucher No', 'Total Qty');
        }
        $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d');
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');
        $data['suppiler'] = $this->input->post('suppiler');
        $branch_id = $this->input->post('branch_id');

        $data['orderby'] = $this->input->post('orderby');
        $data['subhead'] = '<div class="col-12">
        <h4 class="text-center">' . $data['pagename'] . ' From ' . date('d-m-Y', strtotime($data['from_date'])) . ' To ' . date('d-m-Y', strtotime($data['todate'])) . '</h4>
        </div>';

        $all_value = $this->Main_model->get_payment_list($type, $data['from_date'], $data['todate'], $data['suppiler'], null, null, null, $data['orderby'], $branch_id);
        $tbody = '';
        $sum_tqty = 0;
        $sum_gndamt = 0;
        $sum_cash = 0;
        $sum_acc = 0;
        if (!empty($all_value)) {

            foreach ($all_value as $key => $cust) {
                $sum_tqty += $cust->m_payment_qty;
                $sum_gndamt += $cust->m_payment_amount;


                if ($type == 1) {
                    if ($cust->m_payment_method == 16) {
                        $cashac = '<td>' .  $cust->m_payment_amount . '</td>
                    <td></td>';
                        $sum_cash += $cust->m_payment_amount;
                    } else {
                        $cashac =   '<td></td>
                    <td>' .  $cust->m_payment_amount . '</td>';
                        $sum_acc += $cust->m_payment_amount;
                    }
                    $filed = '<td>' . $cust->method_name . '</td>' .
                        $cashac
                        . '<td>' .  $cust->m_payment_amount . '</td>';

                    $tfoot = '<tr>
                        <th colspan="5">Total</th>
                        <th>' . $sum_cash . '</th>
                        <th>' . $sum_acc . '</th>
                        <th>' . $sum_gndamt . '</th>
                         </tr>';
                } else {
                    $filed = '<td>' . $cust->m_payment_qty . '</td>';

                    $tfoot = '<tr>
                    <th colspan="4">Total</th>
                    <th>' .  $sum_tqty . '</th>
                     </tr>';
                }



                $tbody .= '<tr>
        <td>' . ($key + 1) . '</td>
        <td>' . date('d-m-Y', strtotime($cust->m_payment_date)) . '</td>
        <td>' . $cust->m_user_name . '</td>
        <td>' . $cust->m_payment_voucher . '</td>'
                    . $filed . '
         </tr>';
            }
        }


        $data['Mainarray'] =  $tbody;

        $data['tablefoot'] =  $tfoot;


        $this->load->view('print_report_list', $data);
    }


    public function purchase_report()
    {
        $data = $this->login_details();
        $data['pagename'] = "Purchase Report";
        $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d');
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');
        // $data['customers'] = $this->input->post('customers');
        $data['customers'] = '';
        $branch_id = $this->input->post('branch_id');

        $summary = $this->input->post('summary');

        $data['orderby'] = 'asc';
        $data['subhead'] = '<div class="col-12">
        <h4 class="text-center">Purchase Report From ' . date('d-m-Y', strtotime($data['from_date'])) . ' To ' . date('d-m-Y', strtotime($data['todate'])) . '</h4>
        </div>';
        if ($summary == 1) {
            $data['tableheader'] = array('Sno', 'Date', 'Supplier Name', 'Voucher No', 'Total Qty', 'Net Total');
        } else {
            $data['tableheader'] = array('Sno', 'Date', 'Supplier Name', 'Voucher No', 'Total Qty', 'Total Amount', 'Commission', 'Fright', 'Hamali', 'Other', 'Net Total');
        }

        $all_value = $this->Main_model->purchase_group($data['from_date'], $data['todate'], $data['customers'], $data['orderby'], $branch_id);
        $tbody = '';
        $filedfoot = '';
        $sum_tqty = 0;
        $sum_tamt = 0;
        $sum_comm = 0;
        $sum_frigh = 0;
        $sum_ham = 0;
        $sum_oth = 0;
        $sum_gndamt = 0;
        if (!empty($all_value)) {

            foreach ($all_value as $key => $cust) {

                $sum_tqty += $cust->tqty;
                $sum_tamt += $cust->total_amount;
                $sum_comm += $cust->m_purcs_comm;
                $sum_frigh += $cust->m_purcs_fright;
                $sum_ham += $cust->m_purcs_hamali;
                $sum_oth += ($cust->m_purcs_others + $cust->m_purcs_charity + $cust->m_purcs_packaging + $cust->m_purcs_loading + $cust->m_purcs_advance);
                $sum_gndamt += ($cust->total_amount + $cust->total_expense);

                if ($summary == 2) {
                    $filed = ' <td>' . $cust->total_amount . '</td> 
          <td>' . $cust->m_purcs_comm . '</td> 
          <td>' . $cust->m_purcs_fright . '</td> 
          <td>' . $cust->m_purcs_hamali . '</td> 
          <td>' . ($cust->m_purcs_others + $cust->m_purcs_charity + $cust->m_purcs_packaging + $cust->m_purcs_loading + $cust->m_purcs_advance) . '</td>';

                    $filedfoot = ' <th>' . $sum_tamt . '</th> 
          <th>' . $sum_comm . '</th> 
          <th>' . $sum_frigh . '</th> 
          <th>' . $sum_ham . '</th> 
          <th>' . $sum_oth . '</th>';
                } else {
                    $filed = '';
                    $filedfoot = '';
                }

                $tbody .= '<tr>
        <td>' . ($key + 1) . '</td>
        <td>' . date('d-m-Y', strtotime($cust->m_purcs_date)) . '</td>
        <td>' . $cust->supplier_name . '</td>
        <td>' . $cust->m_purcs_spo . '</td>
        <td>' . $cust->tqty . '</td>
        ' . $filed . '
        <td>' . ($cust->total_amount + $cust->total_expense) . '</td>
         </tr>';
            }
        }

        $tfoot = '<tr>
        <th colspan="4">Total</th>
        <th>' .  $sum_tqty . '</th>
        ' . $filedfoot . '
        <th>' . $sum_gndamt . '</th>
         </tr>';
        $data['Mainarray'] =  $tbody;

        $data['tablefoot'] =  $tfoot;


        $this->load->view('print_report_list', $data);
    }

    public function customer_cash_ledger()
    {
        if ($this->session->userdata('is_cust_in') == true) {
            if (empty($this->session->userdata('cust_id'))) {
                redirect('CustLogin');
            }
        } else {
            $data = $this->login_details();
        }
        $data['pagename'] = "Customer Cash Ledger";
        $data['exporttype'] = 2;
        $data['pagelink'] = "export_customer_cash_ledger";

        $data['account_name'] = $this->input->post('account_name');
        $branch_id = $this->session->userdata('is_cust_in') == true ? null : $this->input->post('branch_id');
        $cust_dtl = $this->Main_model->get_cust_dtl($data['account_name']);

        $data['from_date'] = $this->input->post('from_date') ?: row_val($cust_dtl, 'm_cust_added_on', date('Y-m-d'));
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');
        // $summary = $this->input->post('summary');
        $data['orderby'] = $this->input->post('orderby');
        $data['subhead'] = '<div class="col-6">
                                <h4 class="m-0"><strong>' . row_val($cust_dtl, 'm_cust_name') . '</strong></h4>
                                <h4>' . row_val($cust_dtl, 'm_city_name') . '</h4>
                            </div>
                            <div class="col-6 text-end">
                                <h4 class="m-0"></h4>
                                <h4 class="fw-bold"> ' . date('d/m/Y', strtotime($data['from_date'])) . ' TO ' . date('d/m/Y', strtotime($data['todate'])) . '</h4>
                            </div>';

        $data['tableheader'] = '<tr><th scope="col">Sno</th>
                                    <th scope="col">DATE</th>
                                    <th scope="col">PARTICULARE</th>
                                    <th scope="col">DEBIT</th>
                                    <th scope="col">CREDIT</th>
                                    <th scope="col">BALANCE</th>
                                </tr>';

        $all_value = $this->Report_model->customer_detailed_leger($data['from_date'], $data['todate'], $data['account_name'], $branch_id);
        $opening_balance = $this->Main_model->get_opening_balance($data['account_name'], date('Y-m-d', strtotime($data['from_date'] . '-1day')), $branch_id);
        $closing_balance = $this->Main_model->get_opening_balance($data['account_name'], $data['todate'], $branch_id);
        $balance = $opening_balance['balance_amount'];

        if ($balance > 0) {
            $total_debit = $balance;
            $total_credit = 0;
        } else {
            $total_debit = 0;
            $total_credit = $balance;
        }

        // $total_debit = 0;
        // $total_credit = 0;

        $tbody = '<tr>
        <th scope="row"></th>
        <th scope="row">' . date('d/m/Y', strtotime($data['from_date'])) . '</th>
        <th> Opening Balance </th>
        <th>' . $total_debit . '</th>
        <th>' . abs($total_credit) . '</th>
        <th>' . $balance . '</th>
        </tr>';
        if (!empty($all_value)) {
            foreach ($all_value as $contt => $key) {
                $tbody .= '<tr>
                            <th scope="row">' . ($contt + 1) . '</th>
                            <th scope="row">' . date('d/m/Y', strtotime($key['date'])) . '</th>';
                if ($key['type'] == 1) {
                    $total_credit += $key['debited'];
                    $balance -= $key['debited'];
                    $tbody .= '<td class="">
                                    <div class="d-flex bd-highlight">
                                        <div class="flex-grow-1 bd-highlight">
                                        Recipt No. ' . $key['recipt_no'] . '
                                        </div>
                                        <div class="bd-highlight">' . $key['expense'] . '</div>
                                    </div>
                                    <p class="m-0">Remark :-' . $key['note'] . '</p>
                                </td>
                                <td></td>
                                <td>' . $key['debited'] . '</td>
                                <td>' . $balance . '</td>';
                } else if ($key['type'] == 2) {
                    $text = '';
                    foreach ($key['particular'] as $ket) {
                        $text .= $ket->m_crate_name . ' : ' . $ket->m_recvd_qty . ', ';
                    }
                    $tbody .= '<td class="">
                                    <div class="d-flex bd-highlight">
                                        <div class="flex-grow-1 bd-highlight">
                                            <p class="m-0">Create Receive No. ' . $key['recipt_no'] . '</p>
                                            <p class="m-0">Remark :-' . $key['note'] . '</p>
                                        </div>
                                        <div class="bd-highlight">
                                            <p class="m-0 text-end">Qty = ' . $key['total_qty'] . '</p>
                                            <p class="m-0 text-end">' . $text . '</p>
                                        </div>
                                    </div>
                                </td>
                                <td>' . $key['debited'] . '</td>
                                <td></td> 
                                <td>' . $balance . '</td>';
                } else  if ($key['type'] == 4) {
                    if ($key['expense'] == 1) {
                        $total_credit += $key['debited'];
                        $balance -= $key['debited'];
                        $amtdb = '';
                        $amtcdt = $key['debited'];
                    } else {
                        $total_debit += $key['debited'];
                        $balance += $key['debited'];
                        $amtdb = $key['debited'];
                        $amtcdt = '';
                    }

                    $tbody .= '<td class="">
                                    <div class="d-flex bd-highlight">
                                        <div class="flex-grow-1 bd-highlight">
                                        Voucher No. ' . $key['recipt_no'] . '
                                        </div>
                                        <div class="bd-highlight">' . $key['note'] . '</div>
                                    </div>
                                </td>
                                <td>' . $amtdb . '</td>
                                <td>' .  $amtcdt . '</td>
                                <td>' . $balance . '</td>';
                } else {

                    $total_debit += $key['debited'];
                    $balance += $key['debited'];
                    $tbody .= ' <td class="">
                                    <div class="d-flex bd-highlight">
                                        <div class="flex-grow-1 bd-highlight">
                                            <p class="m-0">Credit Sale No. ' . $key['recipt_no'] . '</p>';
                    foreach ($key['particular'] as $ket) {
                        $tbody .= '<p class="m-0">' . $ket->m_item_name . '</p>';
                    }

                    $tbody .= '</div>
                                    <div class="bd-highlight">
                                        <p class="m-0 text-end">Qty:' . $key['total_qty'] . ', Expense = ₹' . $key['expense'] . '</p>';
                    foreach ($key['particular'] as $ket) {
                        $tbody .= ' <p class="m-0 text-end">' . $ket->m_sale_qty . ' ' . $ket->unitname . ' @ ' . $ket->m_sale_price . ' = ' . $ket->m_sale_total . '</p>';
                    }

                    $tbody .= '</div>
                                    </div>
                                    <p class="m-0">Note :-' . $key['note'] . '</p>
                                </td>
                                <td>' . $key['debited'] . '</td>
                                <td></td> 
                                <td>' . $balance . '</td>';
                }
                $tbody .= '</tr>';
            }
        }

        $tbody .= ' <tr>
        <th colspan="3"></th>
        <th>' . $total_debit . '</th>
        <th>' . $total_credit . '</th>
        <th></th>
        </tr>';

        $data['Mainarray'] =  $tbody;

        $crtabi = '';
        if (count($closing_balance['crateitems']) > 0) {
            foreach ($closing_balance['crateitems'] as $kryr) {
                $crtabi .=  $kryr['name'] . ' : ' . $kryr['balance'] . ',';
            }
        }

        $data['mainfooter'] = '<div class="">
        <table class="table border-0 m-0">
            <tbody>
                <tr>
                    <td colspan="2" class="border-0">
                        <p class="m-0">Balance Create : ' . $closing_balance['balance_crate'] . '</p>
                        <p class="m-0">' . $crtabi . '</p>
                    </td>
                    <td colspan="2">
                        <div class="text-end">
                            <h4><strong>CLOSING BALANCE: ' .  $closing_balance['balance_amount'] . '</strong></h4>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        </div>';

        //  echo '<pre>';
        // print_r($data['cust_dtl']);
        // die;

        $this->load->view('afc_account', $data);
    }

    public function export_customer_cash_ledger()
    {

        $account_name = $this->input->post('account_name');
        $branch_id = $this->input->post('branch_id');
        $cust_dtl = $this->Main_model->get_cust_dtl($account_name);

        $from_date = $this->input->post('from_date') ?: row_val($cust_dtl, 'm_cust_added_on', date('Y-m-d'));
        $todate = $this->input->post('to_date') ?: date('Y-m-d');


        $all_value = $this->Report_model->customer_detailed_leger($from_date, $todate, $account_name, $branch_id);
        $opening_balance = $this->Main_model->get_opening_balance($account_name, date('Y-m-d', strtotime($from_date . '-1day')), $branch_id);
        $closing_balance = $this->Main_model->get_opening_balance($account_name, $todate, $branch_id);
        $balance = $opening_balance['balance_amount'];

        if ($balance > 0) {
            $total_debit = $balance;
            $total_credit = 0;
        } else {
            $total_debit = 0;
            $total_credit = $balance;
        }

        $subArray[] = date('d/m/Y', strtotime($from_date));
        $subArray[] = "";
        $subArray[] = "Opening Balance";
        $subArray[] = "";
        $subArray[] = "";
        $subArray[] = "";
        $subArray[] = "";
        $subArray[] = "";
        $subArray[] = "";
        $subArray[] = "";
        $subArray[] = $balance;

        $data[] = $subArray;

        if (!empty($all_value)) {
            foreach ($all_value as $key) {

                if ($key['type'] == 1) {
                    $subArray = array();
                    $total_credit += $key['debited'];
                    $balance -= $key['debited'];

                    $subArray[] = date('d/m/Y', strtotime($key['date']));
                    $subArray[] = $key['recipt_no'];
                    $subArray[] = "";
                    $subArray[] = "";
                    $subArray[] = "";
                    $subArray[] = "";
                    $subArray[] = "";
                    $subArray[] = date('d/m/Y', strtotime($key['date']));
                    $subArray[] = $key['debited'];
                    $subArray[] = $key['expense'];
                    $subArray[] = $balance;


                    $data[] = $subArray;
                } else if ($key['type'] == 2) {
                    $subArray = array();
                    $cratename = "";
                    $crateqty = "";
                    foreach ($key['particular'] as $ket) {
                        $cratename .= $ket->m_crate_name . ', ';
                        $crateqty .= $ket->m_recvd_qty . ', ';
                    }

                    $subArray[] = date('d/m/Y', strtotime($key['date']));
                    $subArray[] = $key['recipt_no'];
                    $subArray[] = $cratename;
                    $subArray[] = $crateqty; //$key['total_qty']
                    $subArray[] = "";
                    $subArray[] = "";
                    $subArray[] = "";
                    $subArray[] = "";
                    $subArray[] = "";
                    $subArray[] = "";
                    $subArray[] = $balance;

                    $data[] = $subArray;
                } else  if ($key['type'] == 4) {
                    $subArray = array();
                    if ($key['expense'] == 1) {
                        $total_credit += $key['debited'];
                        $balance -= $key['debited'];
                        $amtdb = '';
                        $amtcdt = $key['debited'];
                    } else {
                        $total_debit += $key['debited'];
                        $balance += $key['debited'];
                        $amtdb = $key['debited'];
                        $amtcdt = '';
                    }

                    $subArray[] = date('d/m/Y', strtotime($key['date']));
                    $subArray[] = $key['recipt_no'];
                    $subArray[] = "";
                    $subArray[] = "";
                    $subArray[] = "";
                    $subArray[] = ""; //$key['note']
                    $subArray[] = $amtdb;
                    $subArray[] = date('d/m/Y', strtotime($key['date']));
                    $subArray[] = $amtcdt;
                    $subArray[] = "Discount";
                    $subArray[] = $balance;


                    $data[] = $subArray;
                } else {

                    $total_debit += $key['debited'];
                    $balance += $key['debited'];

                    foreach ($key['particular'] as $ket) {
                        $subArray = array();
                        $subArray[] = date('d/m/Y', strtotime($key['date']));
                        $subArray[] = $key['recipt_no'];
                        $subArray[] = $ket->m_item_name;
                        $subArray[] = $ket->m_sale_qty;
                        $subArray[] = $ket->m_sale_price;
                        $subArray[] = $ket->m_sale_total;
                        $subArray[] = $key['debited'];
                        $subArray[] = "";
                        $subArray[] = "";
                        $subArray[] = "";
                        $subArray[] = $balance;
                        $data[] = $subArray;
                    }
                }
            }
        }

        //  echo "<pre>" ;   print_r($data) ; die ;
        $fileName = row_val($cust_dtl, 'm_cust_name', 'customer') . '_ledger_' . date('ymds') . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/csv; ");
        $report = $data;
        $file = fopen('php://output', 'w');

        $header = array(
            "Date",
            "Chalan No",
            "Item",
            "No. of Item",
            "Rate",
            "Total",
            "Billing Amt",
            "Date",
            "Recipt",
            "Mode",
            "Balance",
        );


        fputcsv($file, $header);
        foreach ($report as $line) {
            fputcsv($file, $line);
        }
        fclose($file);

        exit;
    }

    public function customer_crate_ledger()
    {
        if ($this->session->userdata('is_cust_in') == true) {
            if (empty($this->session->userdata('cust_id'))) {
                redirect('CustLogin');
            }
        } else {
            $data = $this->login_details();
        }
        $data['exporttype'] = 1;
        $data['pagelink'] = "";
        $data['pagename'] = "Customer Crate Ledger";

        $data['account_name'] = $this->input->post('account_name');
        $branch_id = $this->session->userdata('is_cust_in') == true ? null : $this->input->post('branch_id');
        $cust_dtl = $this->Main_model->get_cust_dtl($data['account_name']);
        $data['from_date'] = $this->input->post('from_date') ?: row_val($cust_dtl, 'm_cust_added_on', date('Y-m-d'));
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');

        // $summary = $this->input->post('summary');

        $data['orderby'] = $this->input->post('orderby');

        $data['subhead'] = '<div class="col-6">
                                <h4 class="m-0"><strong>' . row_val($cust_dtl, 'm_cust_name') . '</strong></h4>
                                <h4>' . row_val($cust_dtl, 'm_city_name') . '</h4>
                            </div>
                            <div class="col-6 text-end">
                                <h4 class="m-0"><strong>Crate Statement</strong></h4>
                                <h4 class="fw-bold"> ' . date('d/m/Y', strtotime($data['from_date'])) . ' TO ' . date('d/m/Y', strtotime($data['todate'])) . '</h4>
                            </div>';

        $data['tableheader'] = '<tr>
            <th scope="col">Sno</th>
            <th scope="col">DATE</th>
            <th scope="col">PARTICULARE</th>
            <th scope="col">In</th>
            <th scope="col">Out</th>
            <th scope="col">BALANCE</th>
        </tr>';

        $all_value = $this->Report_model->customer_detailed_leger($data['from_date'], $data['todate'], $data['account_name'], $branch_id);
        $opening_balance = $this->Main_model->get_opening_balance($data['account_name'], date('Y-m-d', strtotime($data['from_date'] . '-1day')), $branch_id);
        $closing_balance = $this->Main_model->get_opening_balance($data['account_name'], $data['todate'], $branch_id);


        $balance = $opening_balance['balance_crate'];
        if ($balance > 0) {
            $total_in = 0;
            $total_out = $balance;
        } else {
            $total_in = $balance;
            $total_out = 0;
        }
        // $total_in = 0;
        // $total_out = 0;

        $tbody = '<tr>
        <th scope="row"></th>
        <th scope="row">' . date('d/m/Y', strtotime($data['from_date'])) . '</th>
        <th> Opening Balance </th>
        <th>' . $total_in . '</th>
        <th>' . $total_out . '</th>
        <th>' . $balance . '</th>
        </tr>';
        if (!empty($all_value)) {
            foreach ($all_value as $contnum => $key) {
                $tbody .= '<tr>';
                if ($key['type'] == 2) {
                    $total_in += $key['total_qty'];
                    $balance -= $key['total_qty'];
                    $text = '';
                    foreach ($key['particular'] as  $ket) {
                        $text .= $ket->m_crate_name . ' : ' . $ket->m_recvd_qty . ', ';
                    }
                    $tbody .= '<th scope="row">' . ($contnum + 1) . '</th>
                            <th scope="row">' . date('d/m/Y', strtotime($key['date'])) . '</th>
                            <td class="">
                                <div class="d-flex bd-highlight">
                                    <div class="flex-grow-1 bd-highlight">
                                        <p class="m-0">Create Receive No. ' . $key['recipt_no'] . '</p>
                                        <p class="m-0 ">' . $text . '</p>
                                    </div>
                                </div>
                            </td>
                            <td>' . $key['total_qty'] . '</td>
                            <td></td> 
                            <td>' . $balance . '</td>';
                } else if ($key['type'] == 3) {
                    $txext = '';
                    foreach ($key['particular'] as $ket) {
                        if ($ket->cratetype != '') {
                            $txext .= $ket->cratetype . ' : ' . $ket->m_sale_crate . ', ';
                        }
                    }

                    $total_out += $key['total_crate'];

                    $balance += $key['total_crate'];
                    $tbody .= '
                    <th scope="row">' . ($contnum + 1) . '</th>
                    <th scope="row">' . date('d/m/Y', strtotime($key['date'])) . '</th>
                    <td class="">
                        <div class="d-flex bd-highlight">
                            <div class="flex-grow-1 bd-highlight">
                                <p class="m-0">Credit Sale No. ' . $key['recipt_no'] . '</p>
                                <p class="m-0 ">' . $txext . '</p>
                            </div>
                        </div>
                    </td>
                    <td></td>
                    <td>' . $key['total_crate'] . '</td> 
                    <td>' . $balance . '</td>';
                }

                $tbody .= '</tr>';
            }
        }

        $tbody .= ' <tr>
        <th colspan="3"></th>
        <th>' . $total_in . '</th>
        <th>' . $total_out . '</th>
        <th></th>
        </tr>';

        $data['Mainarray'] =  $tbody;

        $crtabi = '';
        if (count($closing_balance['crateitems']) > 0) {
            foreach ($closing_balance['crateitems'] as $kryr) {
                $crtabi .=  $kryr['name'] . ' : ' . $kryr['balance'] . ',';
            }
        }

        $data['mainfooter'] = '<div class="">
        <table class="table border-0 m-0">
        <tbody>
            <tr>
            <td colspan="2" class="border-0">
            <div class="text-end" >
                <p class="m-0">Balance Create : ' . $closing_balance['balance_crate'] . '</p>
                <p class="m-0">' . $crtabi . '</p>
                </div>
                </td>
                     
                </tr>
            </tbody>
            </table>
        </div>';

        //  echo '<pre>';
        // print_r($data['cust_dtl']);
        // die;

        $this->load->view('afc_account', $data);
    }

    public function supplier_cash_ledger()
    {
        $data = $this->login_details();
        $data['exporttype'] = 1;
        $data['pagelink'] = "";
        $data['pagename'] = "Supplier Cash Ledger";

        $data['suppiler'] = $this->input->post('account_name');
        $branch_id = $this->input->post('branch_id');

        // Head Office is a supplier a branch trades with, but it has no
        // master_users_tbl row - its account is the branch's own balance, and
        // its debits are stock transfers, which carry the ORIGINAL supplier's
        // id rather than 0. The branch ledger already assembles exactly that:
        // transfers, then receipt / payment / voucher history. Send it there
        // rather than computing the same figures a second way and risking the
        // two disagreeing.
        if ($this->session->userdata('user_type') == 9 && (string) $data['suppiler'] === '0') {
            $this->branch_ledger($this->session->userdata('user_id'));
            return;
        }

        $supplier_dtl = $this->Main_model->get_user_dtl($data['suppiler']);

        $data['from_date'] = $this->input->post('from_date') ?: row_val($supplier_dtl, 'm_user_added_on', date('Y-m-d'));
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');

        // $summary = $this->input->post('summary');

        $data['orderby'] = $this->input->post('orderby');

        $data['subhead'] = '<div class="col-6">
                                <h4 class="m-0"><strong>' . row_val($supplier_dtl, 'm_user_name') . '</strong></h4>
                                <h4>' . row_val($supplier_dtl, 'm_city_name') . '</h4>
                            </div>
                            <div class="col-6 text-end">
                                <h4 class="m-0"><strong>Account Datail</strong></h4>
                                <h4 class="fw-bold"> ' . date('d/m/Y', strtotime($data['from_date'])) . ' TO ' . date('d/m/Y', strtotime($data['todate'])) . '</h4>
                            </div>';

        $data['tableheader'] = '<tr>
            <th scope="col">Sno</th>
            <th scope="col">DATE</th>
            <th scope="col">PARTICULARE</th>
            <th scope="col">DEBIT</th>
            <th scope="col">CREDIT</th>
            <th scope="col">BALANCE</th>
        </tr>';

        $all_value = $this->Report_model->supplier_detailed_leger($data['from_date'], $data['todate'], $data['suppiler'], $branch_id);
        $opening_balance = $this->Report_model->get_sup_opening_balance($data['suppiler'], date('Y-m-d', strtotime($data['from_date'] . '-1day')), $branch_id);
        $closing_balance = $this->Report_model->get_sup_opening_balance($data['suppiler'], $data['todate'], $branch_id);

        $balance = $opening_balance['balance_amount'];

        if ($balance > 0) {
            $total_debit = $balance;
            $total_credit = 0;
        } else {
            $total_debit = 0;
            $total_credit = $balance;
        }

        $tbody = '<tr>
        <th scope="row"></th>
        <th scope="row">' . date('d/m/Y', strtotime($data['from_date'])) . '</th>
        <th> Opening Balance </th>
        <th>' . $total_credit . '</th>
        <th>' . $total_debit . '</th>
        <th>' . $balance . '</th>
        </tr>';
        if (!empty($all_value)) {
            foreach ($all_value as $contnum => $key) {
                $tbody .= '<tr>
                <th scope="row">' . ($contnum + 1) . '</th>
                <th scope="row">' . date('d/m/Y', strtotime($key['date'])) . '</th>';
                if ($key['type'] == 1) {
                    if ($key['total_qty'] == 1) {
                        $total_credit += $key['debited'];
                        $balance -= $key['debited'];
                        $tbody .= '<td class="">
                        <div class="d-flex bd-highlight">
                            <div class="flex-grow-1 bd-highlight">
                            Payment No. ' . $key['recipt_no'] . '
                            </div>
                            <div class="bd-highlight">' . $key['expense'] . '</div>
                        </div>
                        <p class="m-0">Note :-' . $key['note'] . '</p>
                        </td>
                        <td>' . $key['debited'] . '</td>
                        <td></td>
                        <td>' . $balance . '</td>';
                    } else {
                        $total_debit += $key['debited'];
                        $balance += $key['debited'];
                        $tbody .= '<td class="">
                            <div class="d-flex bd-highlight">
                                <div class="flex-grow-1 bd-highlight">
                                Recipt No. ' . $key['recipt_no'] . '
                                </div>
                                <div class="bd-highlight">' . $key['expense'] . '</div>
                            </div>
                            <p class="m-0">Note :-' . $key['note'] . '</p>
                        </td>
                        <td>' . $key['debited'] . '</td>
                        <td></td>
                        <td>' . $balance . '</td>';
                    }
                } else if ($key['type'] == 2) {
                    $text = '';
                    foreach ($key['particular'] as $ket) {
                        $text .= $ket->m_crate_name . ' : ' . $ket->m_payment_qty . ', ';
                    }
                    $tbody .= '<td class="">
                        <div class="d-flex bd-highlight">
                            <div class="flex-grow-1 bd-highlight">
                                <p class="m-0">Create Issue No. ' . $key['recipt_no'] . '</p>
                            </div>
                            <div class="bd-highlight">
                                <p class="m-0 text-end">Qty = ' . $key['total_qty'] . '</p>
                                <p class="m-0 text-end">' . $text . '</p>
                            </div>
                        </div>
                        <p class="m-0">Note :-' . $key['note'] . '</p>
                    </td>
                    <td></td> 
                    <td>' . $key['debited'] . '</td>
                    <td>' . $balance . '</td>';
                } else  if ($key['type'] == 4) {
                    if ($key['expense'] == 1) {
                        $total_credit += $key['debited'];
                        $balance += $key['debited'];
                        $amtdb = '';
                        $amtcdt = $key['debited'];
                    } else {
                        $total_debit += $key['debited'];
                        $balance -= $key['debited'];
                        $amtdb = $key['debited'];
                        $amtcdt = '';
                    }

                    $tbody .= '<td class="">
                        <div class="d-flex bd-highlight">
                            <div class="flex-grow-1 bd-highlight">
                            Voucher No. ' . $key['recipt_no'] . '
                            </div>
                            <div class="bd-highlight">' . $key['note'] . '</div>
                        </div>
                    </td>
                    <td>' . $amtdb . '</td>
                    <td>' .  $amtcdt . '</td>
                    <td>' . $balance . '</td>';
                } else {

                    $total_debit += $key['debited'];

                    $balance += $key['debited'];
                    $tbody .= ' <td class="">
                    <div class="d-flex bd-highlight">
                        <div class="flex-grow-1 bd-highlight">
                            <p class="m-0">Challan No. ' . $key['recipt_no'] . '</p>';
                    foreach ($key['particular'] as $ket) {
                        $tbody .= '<p class="m-0">' . $ket->m_item_name . '</p>';
                    }
                    $exptext = '';
                    if (!empty($key['particular'][0]->m_purcs_comm)) {
                        $exptext .= ' Commission: ' . $key['particular'][0]->m_purcs_comm;
                    }
                    if (!empty($key['particular'][0]->m_purcs_fright)) {
                        $exptext .= ' Fright: ' . $key['particular'][0]->m_purcs_fright;
                    }
                    if (!empty($key['particular'][0]->m_purcs_hamali)) {
                        $exptext .= ' Hamali: ' . $key['particular'][0]->m_purcs_hamali;
                    }
                    if (!empty($key['particular'][0]->m_purcs_charity)) {
                        $exptext .= ' Charity: ' . $key['particular'][0]->m_purcs_charity;
                    }
                    if (!empty($key['particular'][0]->m_purcs_packaging)) {
                        $exptext .= ' Packaging: ' . $key['particular'][0]->m_purcs_packaging;
                    }
                    if (!empty($key['particular'][0]->m_purcs_loading)) {
                        $exptext .= ' Loading: ' . $key['particular'][0]->m_purcs_loading;
                    }
                    if (!empty($key['particular'][0]->m_purcs_advance)) {
                        $exptext .= ' Advance: ' . $key['particular'][0]->m_purcs_advance;
                    }
                    if (!empty($key['particular'][0]->m_purcs_others)) {
                        $exptext .= ' Others: ' . $key['particular'][0]->m_purcs_others;
                    }
                    $tbody .= '<p class="m-0">' . $exptext . '</p></div>
                        <div class="bd-highlight">
                            <p class="m-0 text-end"> ' . $key['particular'][0]->m_purcs_truckno . '   Qty:' . $key['total_qty'] . '</p>';
                    foreach ($key['particular'] as $ket) {
                        $tbody .= ' <p class="m-0 text-end">' . $ket->m_purcs_qty . ' ' . $ket->unitname . ' ' . $ket->m_purcs_weight . 'Kg @ ' . $ket->m_purcs_price . ' = ' . $ket->m_purcs_total . '</p>';
                    }
                    $exptot = $key['expense'] ?: '';
                    $tbody .= '<p class="m-0">' . $exptot . '</p></div>
                        </div>
                         <p class="m-0">Note :-' . $key['note'] . '</p>
                    </td>
                    <td></td> 
                    <td>' . $key['debited'] . '</td>
                    <td>' . $balance . '</td>';
                }

                $tbody .= '</tr>';
            }
        }

        $tbody .= ' <tr>
        <th colspan="3"></th>
        <th>' . $total_credit . '</th>
        <th>' . $total_debit . '</th>
        <th></th>
        </tr>';

        $data['Mainarray'] =  $tbody;

        $crtabi = '';
        if (count($closing_balance['crateitems']) > 0) {
            foreach ($closing_balance['crateitems'] as $kryr) {
                $crtabi .=  $kryr['name'] . ' : ' . $kryr['balance'] . ',';
            }
        }

        $data['mainfooter'] = '<div class="">
        <table class="table border-0 m-0">
        <tbody>
            <tr>
            <td colspan="2" class="border-0">
                <p class="m-0">Balance Create : ' . $closing_balance['balance_crate'] . '</p>
                <p class="m-0">' . $crtabi . '</p>
                </td>
           <td colspan="2">
                    <div class="text-end">
                    <h4><strong>CLOSING BALANCE: ' . $closing_balance['balance_amount'] . '</strong></h4>
                    </div>
                </td>
                </tr>
            </tbody>
            </table>
        </div>';

        //  echo '<pre>';
        // print_r($data['cust_dtl']);
        // die;

        $this->load->view('afc_account', $data);
    }

    public function supplier_crate_ledger()
    {
        $data = $this->login_details();
        $data['exporttype'] = 1;
        $data['pagelink'] = "";
        $data['pagename'] = "Supplier Crate Statement";
        $data['suppiler'] = $this->input->post('account_name');
        $branch_id = $this->input->post('branch_id');

        // Head Office has no master_users_tbl row, so get_user_dtl() returns
        // null and the subhead below would fatal on it. Stock transfers carry
        // no crates either, so there is nothing this statement could show.
        if ((string) $data['suppiler'] === '0') {
            show_error('Head Office stock transfers do not carry crates, so there is no crate statement for Head Office. Use the Supplier Ledger for the Head Office account instead.', 404);
            return;
        }

        $supplier_dtl = $this->Main_model->get_user_dtl($data['suppiler']);
        $data['from_date'] = $this->input->post('from_date') ?: row_val($supplier_dtl, 'm_user_added_on', date('Y-m-d'));
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');
        // $summary = $this->input->post('summary');
        $data['orderby'] = $this->input->post('orderby');
        $data['subhead'] = '<div class="col-6">
                <h4 class="m-0"><strong>' . row_val($supplier_dtl, 'm_user_name') . '</strong></h4>
                <h4>' . row_val($supplier_dtl, 'm_city_name') . '</h4>
            </div>
            <div class="col-6 text-end">
                <h4 class="m-0"><strong>Crate Statement</strong></h4>
                <h4 class="fw-bold"> ' . date('d/m/Y', strtotime($data['from_date'])) . ' TO ' . date('d/m/Y', strtotime($data['todate'])) . '</h4>
            </div>';

        $data['tableheader'] = '<tr>
            <th scope="col">Sno</th>
            <th scope="col">DATE</th>
            <th scope="col">PARTICULARE</th>
            <th scope="col">In</th>
            <th scope="col">Out</th>
            <th scope="col">BALANCE</th>
        </tr>';

        $all_value =  $this->Report_model->supplier_detailed_leger($data['from_date'], $data['todate'], $data['suppiler'], $branch_id);
        $opening_balance =  $this->Report_model->get_sup_opening_balance($data['suppiler'], date('Y-m-d', strtotime($data['from_date'] . '-1day')), $branch_id);
        $closing_balance =  $this->Report_model->get_sup_opening_balance($data['suppiler'], $data['todate'], $branch_id);

        $balance = $opening_balance['balance_crate'];
        if ($balance > 0) {
            $total_in = $balance;
            $total_out = 0;
        } else {
            $total_in = 0;
            $total_out = $balance;
        }

        // $total_in = 0;
        // $total_out = 0;

        $tbody = '<tr>
        <th scope="row"></th>
        <th scope="row">' . date('d/m/Y', strtotime($data['from_date'])) . '</th>
        <th> Opening Balance </th>
        <th>' . $total_in . '</th>
        <th>' . $total_out . '</th>
        <th>' . $balance . '</th>
        </tr>';
        if (!empty($all_value)) {
            foreach ($all_value as $contnum => $key) {
                $tbody .= '<tr>
                    <th scope="row">' . ($contnum + 1) . '</th>
                    <th scope="row">' . date('d/m/Y', strtotime($key['date'])) . '</th>';
                if ($key['type'] == 2) {
                    $total_out += $key['total_qty'];
                    $balance -= $key['total_qty'];
                    $text = '';
                    foreach ($key['particular'] as  $ket) {
                        $text .= $ket->m_crate_name . ' : ' . $ket->m_payment_qty . ', ';
                    }
                    $tbody .= '
                    <td class="">
                        <div class="d-flex bd-highlight">
                        <div class="flex-grow-1 bd-highlight">
                            <p class="m-0">Create Issue No. ' . $key['recipt_no'] . '</p>
                            <p class="m-0 ">' . $text . '</p>
                        </div>
                        </div>
                    </td>
                    <td></td> 
                    <td>' . $key['total_qty'] . '</td>
                     <td>' . $balance . '</td>';
                } else if ($key['type'] == 3) {
                    $txext = '';
                    foreach ($key['particular'] as $ket) {
                        if ($ket->cratetype != '') {
                            $txext .= $ket->cratetype . ' : ' . $ket->m_purcs_crate . ', ';
                        }
                    }

                    $total_in += $key['total_crate'];

                    $balance += $key['total_crate'];
                    $tbody .= '<td class="">
                        <div class="d-flex bd-highlight">
                        <div class="flex-grow-1 bd-highlight">
                            <p class="m-0">Challan No. ' . $key['recipt_no'] . '</p>
                            <p class="m-0 ">' . $txext . '</p>
                            </div>
                        </div>
                        </td>
                        <td>' . $key['total_crate'] . '</td> 
                        <td></td>
                        <td>' . $balance . '</td>';
                }
                $tbody .= '</tr>';
            }
        }

        $tbody .= ' <tr>
        <th colspan="3"></th>
        <th>' . $total_in . '</th>
        <th>' . $total_out . '</th>
        <th></th>
        </tr>';

        $data['Mainarray'] =  $tbody;

        $crtabi = '';
        if (count($closing_balance['crateitems']) > 0) {
            foreach ($closing_balance['crateitems'] as $kryr) {
                $crtabi .=  $kryr['name'] . ' : ' . $kryr['balance'] . ',';
            }
        }

        $data['mainfooter'] = '<div class="">
        <table class="table border-0 m-0">
        <tbody>
            <tr>
                <td colspan="2" class="border-0">
                    <div class="text-end" >
                        <p class="m-0">Balance Create : ' . $closing_balance['balance_crate'] . '</p>
                        <p class="m-0">' . $crtabi . '</p>
                    </div>
                </td>
            </tr>
        </tbody>
        </table>
        </div>';

        //  echo '<pre>';
        // print_r($data['cust_dtl']);
        // die;

        $this->load->view('afc_account', $data);
    }

    public function cash_ledger($pagetype)
    {
        $data = $this->login_details();
        $data['exporttype'] = 1;
        $data['pagelink'] = "";
        $data['account_name'] = $this->input->post('account_name');
        $branch_id = $this->input->post('branch_id');
        $data['from_date'] = $this->input->post('from_date') ?: '';
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');
        // $summary = $this->input->post('summary');
        $data['orderby'] = $this->input->post('orderby');
        $exp_dtl = $this->Master_model->get_edit_group($data['account_name']);
        $data['pagename'] = $pagetype == 1 ? "Cash Ledger" : "Bank Ledger";
        $headname = row_val($exp_dtl, 'm_group_name', 'All');
        $opening =  $this->Report_model->cash_bank_leger($pagetype, null, date('Y-m-d', strtotime($data['from_date'] . '-1day')), $data['account_name'], 1, $branch_id);
        $all_value =  $this->Report_model->cash_bank_leger($pagetype, $data['from_date'], $data['todate'], $data['account_name'], '', $branch_id);

        $data['subhead'] = '<div class="col-6">
                                <h4 class="m-0"><strong>' . $headname . '</strong></h4>
                                <h4>Bhilai</h4>
                            </div>
                            <div class="col-6 text-end">
                                <h4 class="m-0"><strong>' . $data['pagename'] . '</strong></h4>
                                <h4 class="fw-bold"> ' . date('d/m/Y', strtotime($data['from_date'])) . ' TO ' . date('d/m/Y', strtotime($data['todate'])) . '</h4>
                            </div>';

        $data['tableheader'] = '<tr>
            <th scope="col">Sno</th>
            <th scope="col">DATE</th>
            <th scope="col">PARTICULARE</th>
            <th scope="col">Debit</th>
            <th scope="col">Credit</th>
            <th scope="col">BALANCE</th>
        </tr>';

        // $total_in = 0;
        // $total_out = 0;
        $balance = $opening;

        if ($balance > 0) {
            $total_in = $balance;
            $total_out = 0;
        } else {
            $total_in = 0;
            $total_out = $balance;
        }

        $tbody = '<tr>
            <th scope="row"></th>
            <th scope="row">' . date('d/m/Y', strtotime($data['from_date'])) . '</th>
            <th> Opening Balance </th>
            <th>' . $total_in . '</th>
            <th>' . abs($total_out) . '</th>
            <th>' . $opening . '</th>
        </tr>';

        if (!empty($all_value)) {
            foreach ($all_value as $contnum => $key) {
                $tbody .= '<tr>
                <th scope="row">' . ($contnum + 1) . '</th>
                <th scope="row">' . date('d/m/Y', strtotime($key->date)) . '</th>';
                if ($key->type == 1) {
                    $total_in += $key->tamont;
                    $balance += $key->tamont;
                    $tbody .= '<td class="">
                        <div class="d-flex bd-highlight">
                            <div class="flex-grow-1 bd-highlight">
                                <p class="m-0">Recipt No. ' . $key->recipt_no . '</p>
                                <p class="m-0">Remark :-' . $key->note . '</p>
                            </div>
                            <div class="flex-grow-1 bd-highlight text-end">
                                <p class="m-0">' . $key->user . ' - ' . $key->csname . '</p>
                                <p class="m-0">' . $key->city . '</p>
                            </div>
                        </div>
                    </td>
                    <td>' . $key->tamont . '</td>
                    <td></td> 
                    <td>' . $balance . '</td>';
                } else if ($key->type == 2) {
                    $total_out += $key->tamont;
                    $balance -= $key->tamont;
                    $tbody .= '<td class="">
                        <div class="d-flex bd-highlight">
                            <div class="flex-grow-1 bd-highlight">
                                <p class="m-0">Payment No. ' .  $key->recipt_no . '</p>
                                <p class="m-0">Remark :-' . $key->note . '</p>
                            </div>
                            <div class="flex-grow-1 bd-highlight text-end">
                                <p class="m-0">' . $key->csname . '</p>
                                <p class="m-0">' . $key->city . '</p>
                            </div>
                        </div>
                    </td>
                    <td></td>
                    <td>' . $key->tamont . '</td> 
                    <td>' . $balance . '</td>';
                } else if ($key->type == 3) {
                    $total_out += $key->tamont;
                    $balance -= $key->tamont;
                    $tbody .= '<td class="">
                        <div class="d-flex bd-highlight">
                            <div class="flex-grow-1 bd-highlight">
                                <p class="m-0">Expense No. ' .  $key->recipt_no . '</p>
                                <p class="m-0">Remark :-' . $key->note . '</p>
                            </div>
                            <div class="flex-grow-1 bd-highlight text-end">
                                <p class="m-0">' . $key->csname . '</p>
                                <p class="m-0">' . $key->user . '</p>
                            </div>
                        </div>
                    </td>
                    <td></td>
                    <td>' . $key->tamont . '</td> 
                    <td>' . $balance . '</td>';
                }

                $tbody .= '</tr>';
            }
        }

        $tbody .= ' <tr>
        <th colspan="3"></th>
        <th>' . $total_in . '</th>
        <th>' . $total_out . '</th>
        <th></th>
        </tr>';

        $data['Mainarray'] =  $tbody;
        $data['mainfooter'] = '';

        //  echo '<pre>';
        // print_r($data['cust_dtl']);
        // die;

        $this->load->view('afc_account', $data);
    }

    public function general_leger($pagetype)
    {
        $data = $this->login_details();
        $data['exporttype'] = 1;
        $data['pagelink'] = "";
        $data['account_name'] = $this->input->post('account_name');
        $branch_id = $this->input->post('branch_id');
        $data['from_date'] = $this->input->post('from_date') ?: '';
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');
        // $summary = $this->input->post('summary');
        $data['orderby'] = $this->input->post('orderby');
        $exp_dtl = $this->Main_model->get_user_dtl($data['account_name']);
        $data['pagename'] = $pagetype == 1 ? "General Ledger" : "Investment Ledger";
        $data['pagename'] = "";
        $headname = row_val($exp_dtl, 'm_user_name', 'All');
        $opening =  $this->Report_model->general_invest_leger(null, date('Y-m-d', strtotime($data['from_date'] . '-1day')), $data['account_name'], 1, $branch_id);
        $all_value =  $this->Report_model->general_invest_leger($data['from_date'], $data['todate'], $data['account_name'], '', $branch_id);
        $data['subhead'] = '<div class="col-6">
                <h4 class="m-0"><strong>' . $headname . '</strong></h4>
                <h4>Bhilai</h4>
            </div>
            <div class="col-6 text-end">
                <h4 class="m-0"><strong>' . $data['pagename'] . '</strong></h4>
                <h4 class="fw-bold"> ' . date('d/m/Y', strtotime($data['from_date'])) . ' TO ' . date('d/m/Y', strtotime($data['todate'])) . '</h4>
            </div>';

        $data['tableheader'] = '<tr>
            <th scope="col">Sno</th>
            <th scope="col">DATE</th>
            <th scope="col">PARTICULARE</th>
            <th scope="col">Debit</th>
            <th scope="col">Credit</th>
            <th scope="col">BALANCE</th>
        </tr>';

        $balance = $opening;

        if ($balance > 0) {
            $total_in = 0;
            $total_out = $balance;
        } else {
            $total_in = $balance;
            $total_out = 0;
        }

        // $total_in = 0;
        // $total_out = 0;

        $tbody = '<tr>
        <th scope="row"></th>
        <th scope="row">' . date('d/m/Y', strtotime($data['from_date'])) . '</th>
        <th> Opening Balance </th>
        <th>' . $total_out . '</th>
        <th>' . $total_in . '</th>
        <th>' . $opening . '</th>
        </tr>';

        if (!empty($all_value)) {
            foreach ($all_value as $contnum => $key) {
                $tbody .= '<tr><th scope="row">' . ($contnum + 1) . '</th>
                    <th scope="row">' . date('d/m/Y', strtotime($key->date)) . '</th>';
                if ($key->type == 1) {
                    $total_in += $key->tamont;
                    $balance -= $key->tamont;
                    $tbody .= '<td class="">
                        <div class="d-flex bd-highlight">
                            <div class="flex-grow-1 bd-highlight">
                                <p class="m-0">Recipt No. ' . $key->recipt_no . '</p>
                                <p class="m-0">Remark :-' . $key->note . '</p>
                            </div>
                            <div class="flex-grow-1 bd-highlight text-end">
                                <p class="m-0">' . $key->user . ' - ' . $key->csname . '</p>
                                <p class="m-0">' . $key->method_name . '</p>
                            </div>
                        </div>
                    </td>
                    <td></td> 
                    <td>' . $key->tamont . '</td>
                    <td>' . $balance . '</td>';
                } else if ($key->type == 2) {
                    $total_out += $key->tamont;
                    $balance += $key->tamont;
                    $tbody .= '<td class="">
                        <div class="d-flex bd-highlight">
                            <div class="flex-grow-1 bd-highlight">
                                <p class="m-0">Payment No. ' .  $key->recipt_no . '</p>
                                <p class="m-0">Remark :-' . $key->note . '</p>
                            </div>
                            <div class="flex-grow-1 bd-highlight text-end">
                                <p class="m-0">' . $key->csname . '</p>
                                <p class="m-0">' . $key->method_name . '</p>
                            </div>
                        </div>
              
                    </td>
                    <td>' . $key->tamont . '</td> 
                    <td></td>
                    <td>' . $balance . '</td>';
                } else  if ($key->type == 3) {
                    if ($key->method_id == 1) {
                        $total_out += $key->tamont;
                        $balance -= $key->tamont;
                        $amtdb = '';
                        $amtcdt = $key->tamont;
                    } else {
                        $total_in += $key->tamont;
                        $balance += $key->tamont;
                        $amtdb = $key->tamont;
                        $amtcdt = '';
                    }

                    $tbody .= '<td class="">
                        <div class="d-flex bd-highlight">
                            <div class="flex-grow-1 bd-highlight">
                                Voucher No. ' . $key->recipt_no . '
                            </div>
                            <div class="bd-highlight">' . $key->note . '</div>
                        </div>
                    </td>
                    <td>' . $amtdb . '</td>
                    <td>' .  $amtcdt . '</td>
                    <td>' . $balance . '</td>';
                }

                $tbody .= '</tr>';
            }
        }

        $tbody .= ' <tr>
        <th colspan="3"></th>
        <th>' . $total_out . '</th>
        <th>' . $total_in . '</th>
        <th></th>
        </tr>';

        $data['Mainarray'] =  $tbody;

        $data['mainfooter'] = '';

        //  echo '<pre>';
        // print_r($data['cust_dtl']);
        // die;

        $this->load->view('afc_account', $data);
    }

    public function expense_ledger()
    {
        $data = $this->login_details();
        $data['pagename'] = "Expense Ledger";
        $data['exporttype'] = 1;
        $data['pagelink'] = "";
        $data['expid'] = $this->input->post('account_name');
        $branch_id = $this->input->post('branch_id');
        $exp_dtl = $this->Master_model->get_edit_group($data['expid']);
        $data['from_date'] = $this->input->post('from_date') ?: '';
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');
        // $summary = $this->input->post('summary');
        $data['orderby'] = $this->input->post('orderby');
        $data['subhead'] = '<div class="col-6">
                    <h4 class="m-0"><strong>' . row_val($exp_dtl, 'm_group_name', 'All') . '</strong></h4>
                    <h4>Bhilai</h4>
                </div>
                <div class="col-6 text-end">
                    <h4 class="m-0"><strong>' . $data['pagename'] . '</strong></h4>
                    <h4 class="fw-bold"> ' . date('d/m/Y', strtotime($data['from_date'])) . ' TO ' . date('d/m/Y', strtotime($data['todate'])) . '</h4>
                </div>';

        $data['tableheader'] = '<tr>
            <th scope="col">Sno</th>
            <th scope="col">DATE</th>
            <th scope="col">PARTICULARE</th>
            <th scope="col">Debit</th>
            <th scope="col">Credit</th>
            <th scope="col">BALANCE</th>
        </tr>';

        $opening =  $this->Report_model->expense_leger(null, $data['from_date'], $data['expid'], 1, $branch_id);
        $all_value =  $this->Report_model->expense_leger($data['from_date'], $data['todate'], $data['expid'], '', $branch_id);

        $balance = $opening;

        if ($balance > 0) {
            $total_in = $balance;
            $total_out = 0;
        } else {
            $total_in = 0;
            $total_out = $balance;
        }

        $tbody = '<tr>
        <th scope="row"></th>
        <th scope="row">' . date('d/m/Y', strtotime($data['from_date'])) . '</th>
        <th> Opening Balance </th>
        <th>' . $total_out . '</th>
        <th>' . $total_in . '</th>
        <th>' . $opening . '</th>
        </tr>';

        if (!empty($all_value)) {
            foreach ($all_value as $contnum => $key) {
                $tbody .= '<tr><th scope="row">' . ($contnum + 1) . '</th>
                        <th scope="row">' . date('d/m/Y', strtotime($key->date)) . '</th>';
                if ($key->type == 1) {
                    $total_in += $key->tamont;
                    $balance += $key->tamont;

                    if (row_val($exp_dtl, 'm_group_id') == 83) {
                        $name = '<p class="m-0">Line: ' . $key->user . '</p>';
                    } else {
                        $name = '<p class="m-0">Ref Bill: ' . $key->method_name . '</p>';
                    }

                    $tbody .= '<td class="">
                            <div class="d-flex bd-highlight">
                                <div class="flex-grow-1 bd-highlight">
                                    <p class="m-0">Credit Note No. ' . $key->recipt_no . '</p>
                                    <p class="m-0">Remark :-' . $key->note . '</p>
                                </div>
                                <div class="flex-grow-1 bd-highlight text-end">
                                    ' . $name . '
                                </div>
                            </div>
                        </td>
                        <td></td> 
                        <td>' . $key->tamont . '</td>
                        <td>' . $balance . '</td>';
                } else if ($key->type == 2) {
                    $total_out += $key->tamont;
                    $balance -= $key->tamont;
                    $tbody .= '<td class="">
                            <div class="d-flex bd-highlight">
                                <div class="flex-grow-1 bd-highlight">
                                    <p class="m-0">Payment No. ' .  $key->recipt_no . '</p>
                                </div>
                                <div class="flex-grow-1 bd-highlight text-end">
                                    <p class="m-0">' . $key->method_name . '</p>
                                </div>
                            </div>
                            <p class="m-0">Remark :-' . $key->note . '</p>
                        </td>
                        <td>' . $key->tamont . '</td> 
                        <td></td>
                        <td>' . abs($balance) . '</td>';
                } else  if ($key->type == 3) {
                    if ($key->method_id == 1) {
                        $total_out += $key->tamont;
                        $balance -= $key->tamont;
                        $amtdb = '';
                        $amtcdt = $key->tamont;
                    } else {
                        $total_in += $key->tamont;
                        $balance += $key->tamont;
                        $amtdb = $key->tamont;
                        $amtcdt = '';
                    }

                    $tbody .= '<td class="">
                        <div class="d-flex bd-highlight">
                            <div class="flex-grow-1 bd-highlight">
                                Voucher No. ' . $key->recipt_no . '
                            </div>
                            <div class="bd-highlight">' . $key->note . '</div>
                        </div>
                    </td>
                    <td>' . $amtdb . '</td>
                    <td>' .  $amtcdt . '</td>
                    <td>' . $balance . '</td>';
                }
                $tbody .= '</tr>';
            }
        }

        $tbody .= '<tr>
        <th colspan="3"></th>
        <th>' . $total_out . '</th>
        <th>' . $total_in . '</th>
        <th></th>
        </tr>';

        $data['Mainarray'] =  $tbody;

        $data['mainfooter'] = '';

        //  echo '<pre>';
        // print_r($data['cust_dtl']);
        // die;

        $this->load->view('afc_account', $data);
    }

    public function voucher_ledger($pgtype)
    {
        $data = $this->login_details();
        $data['pagename'] = $pgtype == 1 ? "Credit Voucher Ledger" : "Debit Voucher Ledger";
        $data['exporttype'] = 1;
        $data['pagelink'] = "";
        // $data['expid'] = $this->input->post('account_name');
        // $exp_dtl = $this->Master_model->get_edit_group($data['expid']);
        $branch_id = $this->input->post('branch_id');
        $data['from_date'] = $this->input->post('from_date') ?: '';
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');
        // $summary = $this->input->post('summary');
        // $data['orderby'] = $this->input->post('orderby');

        $data['subhead'] = '
            <div class="col-12 text-center">
                <h4 class="m-0"><strong>' . $data['pagename'] . '</strong></h4>
                <h4 class="fw-bold"> ' . date('d/m/Y', strtotime($data['from_date'])) . ' TO ' . date('d/m/Y', strtotime($data['todate'])) . '</h4>
            </div>';

        $data['tableheader'] = '<tr>
            <th scope="col">Sno</th>
            <th scope="col">DATE</th>
            <th scope="col">PARTICULARE</th>
            <th scope="col">Amount</th>
        </tr>';

        $opening =  $this->Report_model->voucher_leger($pgtype, null, $data['from_date'], 1, $branch_id);
        $all_value =  $this->Report_model->voucher_leger($pgtype, $data['from_date'], $data['todate'], '', $branch_id);

        $balance = $opening;

        // if ($balance > 0) {
        //     $total_in = $balance;
        //     $total_out = 0;
        // } else {
        //     $total_in = 0;
        //     $total_out = $balance;
        // }


        $tbody = '<tr>
            <th scope="row"></th>
            <th scope="row">' . date('d/m/Y', strtotime($data['from_date'])) . '</th>
            <th> Opening Balance </th>
            <th>' . $opening . '</th>
        </tr>';

        if (!empty($all_value)) {
            foreach ($all_value as $contnum => $key) {
                // if ($key->type == 1) {
                // $total_in += $key->tamont;
                $balance += $key->tamont;
                $tbody .= '<tr><th scope="row">' . ($contnum + 1) . '</th>
                        <th scope="row">' . date('d/m/Y', strtotime($key->date)) . '</th>
                        <td class="">
                            <div class="d-flex bd-highlight">
                                <div class="flex-grow-1 bd-highlight">
                                    <p class="m-0">' . $key->csname . '</p>
                                    <p class="m-0">Remark :-' . $key->note . '</p>
                                </div>
                                <div class="flex-grow-1 bd-highlight text-end">
                                    <p class="m-0">Contact :-' . $key->csmobile . '</p>
                                    <p class="m-0">VHNo. ' . $key->recipt_no . '</p>
                                </div>
                            </div>
                        </td>
                        <td>' . $key->tamont . '</td>
                    </tr>';
                //         } else if ($key->type == 2) {
                //             $total_out += $key->tamont;
                //             $balance -= $key->tamont;
                //             $tbody .= '<tr>
                //         <th scope="row">' . date('d/m/Y', strtotime($key->date)) . '</th>
                //              <td class="">
                //         <div class="d-flex bd-highlight">
                //         <div class="flex-grow-1 bd-highlight">
                //             <p class="m-0">Payment No. ' .  $key->recipt_no . '</p>

                //             </div>
                //             <div class="flex-grow-1 bd-highlight text-end">
                //             <p class="m-0">' . $key->method_name . '</p>

                //         </div>
                //         </div>
                //         <p class="m-0">Remark :-' . $key->note . '</p>
                //     </td>
                //     <td>' . $key->tamont . '</td> 
                //     <td></td>
                //     <td>' . abs($balance) . '</td></tr>';
                //         } else  if ($key->type == 3) {
                //             if ($key->method_id == 1) {
                //                 $total_out += $key->tamont;
                //                 $balance -= $key->tamont;
                //                 $amtdb = '';
                //                 $amtcdt = $key->tamont;
                //             } else {
                //                 $total_in += $key->tamont;
                //                 $balance += $key->tamont;
                //                 $amtdb = $key->tamont;
                //                 $amtcdt = '';
                //             }

                //             $tbody .= '<td class="">
                // <div class="d-flex bd-highlight">
                // <div class="flex-grow-1 bd-highlight">
                // Voucher No. ' . $key->recipt_no . '
                // </div>
                // <div class="bd-highlight">' . $key->note . '</div>
                // </div>
                // </td>
                // <td>' . $amtdb . '</td>
                //     <td>' .  $amtcdt . '</td>
                //  <td>' . $balance . '</td>';
                // }
            }
        }

        $tbody .= ' <tr>
            <th colspan="3">Total </th>
            <th>' . $balance . '</th>
        </tr>';

        $data['Mainarray'] =  $tbody;

        $data['mainfooter'] = '';

        //  echo '<pre>';
        // print_r($data['cust_dtl']);
        // die;

        $this->load->view('afc_account', $data);
    }

    public function fright_ledger()
    {
        $data = $this->login_details();
        $data['pagename'] = "Fright Ledger";
        $data['exporttype'] = 1;
        $data['pagelink'] = "";
        $data['expid'] = $this->input->post('account_name');
        $branch_id = $this->input->post('branch_id');
        $exp_dtl = $this->Master_model->get_edit_group($data['expid']);
        $data['from_date'] = $this->input->post('from_date') ?: '';
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');
        // $summary = $this->input->post('summary');
        $data['orderby'] = $this->input->post('orderby');
        $data['subhead'] = '<div class="col-6">
                    <h4 class="m-0"><strong>' . row_val($exp_dtl, 'm_group_name', 'All') . '</strong></h4>
                    <h4>Bhilai</h4>
                </div>
                <div class="col-6 text-end">
                    <h4 class="m-0"><strong>' . $data['pagename'] . '</strong></h4>
                    <h4 class="fw-bold"> ' . date('d/m/Y', strtotime($data['from_date'])) . ' TO ' . date('d/m/Y', strtotime($data['todate'])) . '</h4>
                </div>';

        $data['tableheader'] = '<tr>
            <th scope="col">Sno</th>
            <th scope="col">DATE</th>
            <th scope="col">PARTICULARE</th>
            <th scope="col">Debit</th>
            <th scope="col">Credit</th>
            <th scope="col">BALANCE</th>
        </tr>';

        $opening = $this->Report_model->fright_ledger(null, $data['from_date'], $data['expid'], $exp_dtl->m_group_group, 1, $branch_id);
        $all_value =  $this->Report_model->fright_ledger($data['from_date'], $data['todate'], $data['expid'], $exp_dtl->m_group_group, '', $branch_id);

        $balance = $opening;

        if ($balance > 0) {
            $total_in = $balance;
            $total_out = 0;
        } else {
            $total_in = 0;
            $total_out = $balance;
        }

        $tbody = '<tr>
        <th scope="row"></th>
        <th scope="row">' . date('d/m/Y', strtotime($data['from_date'])) . '</th>
        <th> Opening Balance </th>
        <th>' . $total_out . '</th>
        <th>' . $total_in . '</th>
        <th>' . $opening . '</th>
        </tr>';

        if (!empty($all_value)) {
            foreach ($all_value as $contnum => $key) {

                if ($key->type == 1) {
                    $total_in += $key->tamont;
                    $balance += $key->tamont;
                    $tbody .= '<tr><th scope="row">' . ($contnum + 1) . '</th>
                            <th scope="row">' . date('d/m/Y', strtotime($key->date)) . '</th>
                            <td class="">
                                <div class="d-flex bd-highlight">
                                    <div class="flex-grow-1 bd-highlight">
                                        <p class="m-0">Reciving Fright No. ' . $key->recipt_no . '</p>
                                        <p class="m-0">' . $key->note . '</p>
                                    </div>
                                </div>
                            </td>
                            <td></td> 
                            <td>' . $key->tamont . '</td>
                            <td>' . $balance . '</td></tr>';
                } else if ($key->type == 2) {
                    $total_out += $key->tamont;
                    $balance -= $key->tamont;
                    $tbody .= '<tr><th scope="row">' . ($contnum + 1) . '</th>
                        <th scope="row">' . date('d/m/Y', strtotime($key->date)) . '</th>
                        <td class="">
                            <div class="d-flex bd-highlight">
                                <div class="flex-grow-1 bd-highlight">
                                    <p class="m-0">Payment No. ' .  $key->recipt_no . '</p>
                                </div>
                                <div class="flex-grow-1 bd-highlight text-end">
                                    <p class="m-0">' . $key->method_name . '</p>
                                </div>
                            </div>
                            <p class="m-0">Remark :-' . $key->note . '</p>
                        </td>
                        <td>' . $key->tamont . '</td> 
                        <td></td>
                        <td>' . abs($balance) . '</td></tr>';
                }
            }
        }

        $tbody .= ' <tr>
        <th colspan="3"></th>
        <th>' . $total_out . '</th>
        <th>' . $total_in . '</th>
        <th></th>
        </tr>';

        $data['Mainarray'] =  $tbody;

        $data['mainfooter'] = '';

        //  echo '<pre>';
        // print_r($data['cust_dtl']);
        // die;

        $this->load->view('afc_account', $data);
    }

    public function staffcomm_ledger()
    {
        $data = $this->login_details();
        $data['pagename'] = "Staff Commission Ledger";
        $data['exporttype'] = 1;
        $data['pagelink'] = "";
        $data['expid'] = $this->input->post('account_name');
        $branch_id = $this->input->post('branch_id');
        $exp_dtl = $this->Main_model->get_user_dtl($data['expid']);
        $data['from_date'] = $this->input->post('from_date') ?: '';
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');
        // $summary = $this->input->post('summary');
        $data['orderby'] = $this->input->post('orderby');
        $data['subhead'] = '<div class="col-6">
                        <h4 class="m-0"><strong>' . row_val($exp_dtl, 'm_user_name', 'All') . '</strong></h4>
                        <h4>' . row_val($exp_dtl, 'm_city_name') . '</h4>
                    </div>
                    <div class="col-6 text-end">
                        <h4 class="m-0"><strong>' . $data['pagename'] . '</strong></h4>
                        <h4 class="fw-bold"> ' . date('d/m/Y', strtotime($data['from_date'])) . ' TO ' . date('d/m/Y', strtotime($data['todate'])) . '</h4>
                    </div>';

        $data['tableheader'] = '<tr>
            <th scope="col">Sno</th>
            <th scope="col">DATE</th>
            <th scope="col">PARTICULARE</th>
            <th scope="col">Debit</th>
            <th scope="col">Credit</th>
            <th scope="col">BALANCE</th>
        </tr>';

        $opening =  $this->Report_model->staffcomm_ledger(null, $data['from_date'], $data['expid'], 1, $branch_id);
        $all_value =  $this->Report_model->staffcomm_ledger($data['from_date'], $data['todate'], $data['expid'], '', $branch_id);
        $balance = $opening;
        if ($balance > 0) {
            $total_in = $balance;
            $total_out = 0;
        } else {
            $total_in = 0;
            $total_out = $balance;
        }


        $tbody = '<tr>
        <th scope="row"></th>
        <th scope="row">' . date('d/m/Y', strtotime($data['from_date'])) . '</th>
        <th> Opening Balance </th>
        <th>' . $total_out . '</th>
        <th>' . $total_in . '</th>
        <th>' . $opening . '</th>
        </tr>';

        if (!empty($all_value)) {
            foreach ($all_value as $contnum => $key) {

                if ($key->type == 1) {
                    $total_in += array_sum(explode(',', $key->tamont));
                    $balance += array_sum(explode(',', $key->tamont));
                    $tbody .= '<tr><th scope="row">' . ($contnum + 1) . '</th>
                    <th scope="row">' . date('d/m/Y', strtotime($key->date)) . '</th>
                    <td class="">
                        <div class="d-flex bd-highlight">
                            <div class="flex-grow-1 bd-highlight">
                                <p class="m-0">Reciving Commission No. ' . $key->recipt_no . '</p>
                                <p class="m-0">' . $key->note . '</p>
                            </div>
                        </div>
                    </td>
                    <td></td> 
                    <td>' . array_sum(explode(',', $key->tamont)) . '</td>
                    <td>' . $balance . '</td></tr>';
                } else if ($key->type == 2) {
                    $total_out += $key->tamont;
                    $balance -= $key->tamont;
                    $tbody .= '<tr><th scope="row">' . ($contnum + 1) . '</th>
                    <th scope="row">' . date('d/m/Y', strtotime($key->date)) . '</th>
                    <td class="">
                        <div class="d-flex bd-highlight">
                            <div class="flex-grow-1 bd-highlight">
                                <p class="m-0">Payment No. ' .  $key->recipt_no . '</p>
                            </div>
                            <div class="flex-grow-1 bd-highlight text-end">
                                <p class="m-0">' . $key->method_name . '</p>
                            </div>
                        </div>
                        <p class="m-0">Remark :-' . $key->note . '</p>
                    </td>
                    <td>' . $key->tamont . '</td> 
                    <td></td>
                    <td>' . abs($balance) . '</td></tr>';
                }
            }
        }

        $tbody .= ' <tr>
        <th colspan="3"></th>
        <th>' . $total_out . '</th>
        <th>' . $total_in . '</th>
        <th></th>
        </tr>';

        $data['Mainarray'] =  $tbody;

        $data['mainfooter'] = '';

        //  echo '<pre>';
        // print_r($data['cust_dtl']);
        // die;

        $this->load->view('afc_account', $data);
    }

    public function turck_report()
    {
        $data = $this->login_details();
        $data['pagename'] = "Truck Report";
        $data['suppiler'] = $this->input->post('suppiler');
        $data['from_date'] = $this->input->post('from_date');
        $data['to_date'] = $this->input->post('to_date') ?: date('Y-m-d');

        // $data['all_items'] = $this->Master_model->get_all_item();
        $data['all_value'] = $this->Report_model->get_truck_report($data['from_date'], $data['to_date'], $data['suppiler']);
        $this->load->view('truck_report', $data);
    }

    public function staff_performance_new_report()
    {

        $data = $this->login_details();
        $data['pagename'] = "Staff Performance Report";
        $data['exporttype'] = 1;
        $data['pagelink'] = "";
        $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d');
        $data['todate'] = $this->input->post('to_date') ?: date('Y-m-d');
        $data['agent'] = $this->input->post('agent');
        $data['cratetype'] = $this->input->post('cratetype');
        $data['orderby'] = $this->input->post('orderby');
        $data['report_type'] = $this->input->post('report_type');
        $agent_dtl = $this->Main_model->get_user_dtl($data['agent']);
        $agent_name = row_val($agent_dtl, 'm_user_name', 'All/Admin');
        $data['subhead'] = '<div class="col-12 mt-2">
                                <h3 class="text-center"> ' . $agent_name . ' Performance Report From ' . date('d-m-Y', strtotime($data['from_date'])) . ' To ' . date('d-m-Y', strtotime($data['todate'])) . '</h3>
                            </div>';

        $all_data = $this->Report_model->get_staff_performance_new_report($data['from_date'], $data['todate'], $data['agent'], $agent_dtl->m_user_group, $data['report_type']);
        // echo '<pre>';
        // print_r($all_data);
        // die;
        $tbody = '';
        $thead = '';
        $tfoot = '';
        $ter = '';
        $sumqty = 0;
        $sumtol = 0;
        $sumexp = 0;
        $sumnttl = 0;
        if ($data['report_type'] == 1) {
            $thead .= '<tr>
                <th rowspan="2">Sno</th>
                <th rowspan="2">Date</th>
                <th rowspan="2">Customer Name</th>
                <th rowspan="2">Sale No</th>';
            if (!empty($all_data['items'])) {
                foreach ($all_data['items'] as $head) {
                    $thead .= '<th colspan="2">' . $head . '</th>';
                    $ter .= '<th>Qty</th><th>Rate</th>';
                }
            }
            $thead .= '<th rowspan="2">Total</th><th rowspan="2">Expense</th><th rowspan="2">Net Total</th></tr><tr>' . $ter . '</tr>';

            if (!empty($all_data['data'])) {

                foreach ($all_data['data'] as $cua => $salei) {
                    $salitem = '';
                    $sitem = !empty($salei->sale_itemname) ? explode(',', $salei->sale_itemname) : [];
                    $sqty =  !empty($salei->sale_qty) ? explode(',', $salei->sale_qty) : [];
                    $sprice =  !empty($salei->sale_price) ? explode(',', $salei->sale_price) : [];
                    $stotal =  !empty($salei->sale_total) ? explode(',', $salei->sale_total) : [];
                    $sctype = !empty($salei->sale_cratetype) ? explode(',', $salei->sale_cratetype) : [];
                    $sunit =  !empty($salei->sale_unitname) ? explode(',', $salei->sale_unitname) : [];

                    $sumtol += $salei->sub_total;
                    $sumexp += $salei->total_expense;
                    $sumnttl += ($salei->sub_total + $salei->total_expense);

                    $tbody .= '<tr>
                        <td>' . ($cua + 1) . '</td>
                        <td>' . date('d-m-Y', strtotime($salei->m_sale_date)) . '</td>
                        <td>' . $salei->m_cust_name . '</td>
                        <td>' . $salei->m_sale_spo . '</td>';
                    if (!empty($all_data['items'])) {
                        foreach ($all_data['items'] as $head) {
                            $tsit = '';
                            if (!empty($sitem)) {
                                foreach ($sitem as $cou => $kry) {
                                    if ($head == $kry) {
                                        $tsit = '<td>' . $sqty[$cou] . '</td><td>' .  $sprice[$cou]  . '</td>';
                                        $sumqty += $sqty[$cou];
                                        break;
                                    } else {
                                        $tsit = '<td></td><td></td>';
                                    }
                                }
                            }
                            $tbody .= $tsit;
                        }
                    }
                    $tbody .= '<td>' . $salei->sub_total . '</td><td>' . $salei->total_expense . '</td><td>' . ($salei->sub_total + $salei->total_expense) . '</td></tr>';
                }
            }

            $tfoot .= '<tr><th Colspan="4">Total</th><th colspan="' . (!empty($all_data['items']) ? count($all_data['items']) : 0 * 2) . '">' . $sumqty . '</th><th>₹' . $sumtol . '</th><th>₹' . $sumexp . '</th><th>₹' . $sumnttl . '</th></tr>';
        } else  if ($data['report_type'] == 2) {
            $thead = '<tr>
                <th>Sno</th>
                <th>Date</th>
                <th>Customer Name</th>
                <th>Receipt No</th>';
            if (!empty($all_data['items'])) {
                foreach ($all_data['items'] as $head) {
                    $thead .= '<th>' . $head . '</th>';
                }
            }
            $thead .= '</tr>';

            if (!empty($all_data['data'])) {
                foreach ($all_data['data'] as $cua => $carev) {
                    $tbody .= '<tr>
                        <td>' . ($cua + 1) . '</td>
                        <td>' . date('d-m-Y', strtotime($carev->m_recvd_date)) . '</td>
                        <td>' . $carev->m_cust_name . '</td>
                        <td>' . $carev->m_recvd_voucher . '</td>';
                    if (!empty($all_data['items'])) {
                        foreach ($all_data['items'] as $head) {
                            $tsit = '';
                            if ($head == $carev->method_name) {
                                $tsit = '<td>' . $carev->m_recvd_amount . '</td>';
                            } else {
                                $tsit = '<td></td>';
                            }
                            $tbody .= $tsit;
                        }
                    }
                    $tbody .= '</tr>';
                }
            }

            // $tfoot .= '<tr>
            // <th Colspan="4">Total</th>';
            // if (!empty($all_data['items'])) {
            //     foreach ($all_data['items'] as $head) {
            //         $tfoot .= '<th>' . $head . '</th>';
            //     }
            // }
            // $tfoot .= '</tr>';

        } else  if ($data['report_type'] == 3) {
            $thead = '<tr>
                <th>Sno</th>
                <th>Date</th>
                <th>Customer Name</th>
                <th>Receipt No</th>';
            if (!empty($all_data['items'])) {
                foreach ($all_data['items'] as $head) {
                    $thead .= '<th>' . $head . '</th>';
                }
            }
            $thead .= '</tr>';

            if (!empty($all_data['data'])) {
                foreach ($all_data['data'] as $cua => $carev) {
                    $sitem = explode(',', $carev->crate_name);
                    $sqty =  explode(',', $carev->crate_qty);
                    $tbody .= '<tr>
                        <td>' . ($cua + 1) . '</td>
                        <td>' . date('d-m-Y', strtotime($carev->m_recvd_date)) . '</td>
                        <td>' . $carev->m_cust_name . '</td>
                        <td>' . $carev->m_recvd_voucher . '</td>';
                    if (!empty($all_data['items'])) {
                        foreach ($all_data['items'] as $head) {
                            $tsit = '';
                            if (!empty($sitem)) {
                                foreach ($sitem as $cou => $kry) {
                                    if ($head == $kry) {
                                        $tsit = '<td>' . $sqty[$cou] . '</td>';
                                        break;
                                    } else {
                                        $tsit = '<td></td>';
                                    }
                                }
                            }
                            $tbody .= $tsit;
                        }
                    }
                    $tbody .= '</tr>';
                }
            }
        }

        $data['tableheader'] = $thead;
        $data['Mainarray'] =  $tbody;
        $data['tablefoot'] = $tfoot;
        $data['mainfooter'] = '';


        $this->load->view('afc_account', $data);
    }

    public function staff_daily_summary()
    {

        $data = $this->login_details();

        $date = date('Y-m-d', strtotime('2026-02-01'));
        $staff_id = 3;
        $all_data = $this->Report_model->get_staff_daily_customer_report($staff_id, $date);
        echo '<pre>';
        print_r($all_data);
    }

    //==========================Stock List===========================//
    public function stock_list()
    {
        $data = $this->login_details();
        $data['pagename'] = "All Item Stock";
        $data['item_id'] = $this->input->post('item_id');
        $data['from_date'] = $this->input->post('from_date');
        $data['to_date'] = $this->input->post('to_date') ?: date('Y-m-d');
        $data['branch_id'] = $this->input->post('branch_id');

        $data['branch_list'] = $this->Main_model->get_user_list(9);
        $data['all_items'] = $this->Master_model->get_all_item('', $data['branch_id']);
        $data['all_value'] = $this->Report_model->get_item_stock_list($data['from_date'], $data['to_date'], $data['item_id'], $data['branch_id']);
        $this->load->view('stock_list', $data);
    }

    public function lotwise_item()
    {
        $data = $this->login_details();
        $data['pagename'] = "Lotwise items";
        $data['item_id'] = $this->input->post('item_id');
        // $data['from_date'] = $this->input->post('from_date');
        $data['to_date'] = $this->input->post('to_date') ?: date('Y-m-d');
        $data['branch_id'] = $this->input->post('branch_id');
        $data['cust_dtl'] = $this->Main_model->get_cust_active_list('', $data['branch_id']);
        $data['agent_dtl'] = $this->Main_model->get_active_user_list(1, $data['branch_id']);
        $data['all_items'] = $this->Master_model->get_all_item('', $data['branch_id']);
        $data['branch_list'] = $this->Main_model->get_user_list(9);
        $data['all_value'] = $this->Report_model->get_lotwise_item($data['to_date'], $data['item_id'], '', $data['branch_id']);
        //    echo '<pre>'; print_r($data['all_value']); die ;
        $this->load->view('lotwise_list', $data);
    }

    // public function lotwise_item_check()
    // {
    //     $data = $this->login_details();
    //     $data['pagename'] = "Lotwise items";
    //     $data['item_id'] = null;
    //     $data['to_date'] = date('Y-m-d');
    //     $data['cust_dtl'] = $this->Main_model->get_cust_active_list();
    //     $data['agent_dtl'] = $this->Main_model->get_active_user_list(1);
    //     $data['all_items'] = $this->Master_model->get_all_item();
    //     $data['all_value'] = $this->Report_model->get_lotwise_item2($data['to_date'], $data['item_id']);
    //     $this->load->view('lotwise_list', $data);
    // }

    public function get_lotwise_item()
    {
        $data['item_id'] = $this->input->post('item_id');

        $all_value = $this->Main_model->get_avilable_item($data['item_id']);
        echo json_encode($all_value);
        //  echo '<pre>'; print_r($data['all_value']); die ;
    }

    public function lotwise_sales_list()
    {
        $all_value = $this->Report_model->lotwise_sales_list($this->input->post('purid'), $this->input->post('item_id'));
        // echo '<pre>'; print_r($all_value); die ;
        echo json_encode($all_value);
    }

    /**
     * Backs the lot-wise modal on the truck report (see truck_report.php).
     * Named for the URL that view has always posted to.
     */
    public function purchase_sales_list()
    {
        if ($this->ajax_login() === false) {
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            show_404();
            return;
        }

        echo json_encode($this->Report_model->truck_challan_lines(
            $this->input->post('purspo'),
            $this->input->post('funtype')
        ));
    }

    public function get_agent_balance_stock()
    {
        $all_value = $this->Api_Model->get_user_balance_stock($this->input->post('user_id'), $this->input->post('datein'));
        // echo '<pre>'; print_r($all_value); die ;
        echo json_encode($all_value);
    }
    //==========================Stock List===========================//

    public function transfer_ledger()
    {
        $data = $this->login_details();
        $user_type = $this->session->userdata('user_type');

        // Sirf 8 aur 9 hi is report ko access kar sakte hain
        if (!in_array($user_type, [8, 9])) {
            show_error('Access Denied', 403);
            return;
        }

        $data['pagename']   = "Stock Transfer Ledger";
        $data['exporttype'] = 1;
        $data['pagelink']   = "export_transfer_ledger";

        $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d', strtotime('-30 days'));
        $data['todate']    = $this->input->post('to_date') ?: date('Y-m-d');

        // ===== Access control logic =====
        if ($user_type == 9) {
            // Branch user -> sirf apni branch ke transfers (aane/jaane wale dono)
            $branch_id = $this->session->userdata('user_id');
            $branch_name = $this->session->userdata('user_name'); // ya DB se le lo agar session me nahi hai
        } else {
            // Super Admin -> every branch, unless the Ledger page's Branch
            // filter picked one. '0' is Head Office and is a real choice, so
            // test against '' rather than using empty().
            $posted_branch = $this->input->post('branch_id');
            if ($posted_branch !== null && $posted_branch !== '') {
                $branch_id   = (int) $posted_branch;
                $branch_row  = $branch_id === 0 ? null : $this->Main_model->get_branch_dtl($branch_id);
                $branch_name = $branch_id === 0
                    ? 'Head Office'
                    : (!empty($branch_row) ? $branch_row->m_user_name : 'Branch ' . $branch_id);
            } else {
                $branch_id   = null;
                $branch_name = 'All Branches';
            }
        }

        $data['subhead'] = '<div class="col-6">
                            <h4 class="m-0"><strong>Stock Transfer Ledger</strong></h4>
                            <h4>' . $branch_name . '</h4>
                        </div>
                        <div class="col-6 text-end">
                            <h4 class="m-0"></h4>
                            <h4 class="fw-bold">' . date('d/m/Y', strtotime($data['from_date'])) . ' TO ' . date('d/m/Y', strtotime($data['todate'])) . '</h4>
                        </div>';

        $data['tableheader'] = '<tr>
                                <th scope="col">Sno</th>
                                <th scope="col">DATE</th>
                                <th scope="col">ITEM</th>
                                <th scope="col">LOT NO</th>
                                <th scope="col">QTY</th>
                                <th scope="col">ISSUE RATE</th>
                                <th scope="col">AMOUNT</th>
                                <th scope="col">FROM BRANCH</th>
                                <th scope="col">TO BRANCH</th>
                                <th scope="col">TRANSFER REF</th>
                                <th scope="col">ADDED BY</th>
                            </tr>';

        $all_value = $this->Report_model->transfer_ledger_data($data['from_date'], $data['todate'], $branch_id);

        $tbody = '';
        $total_qty = 0;
        $total_amount = 0;

        if (!empty($all_value)) {
            foreach ($all_value as $i => $row) {
                $total_qty += $row->m_purcs_qty;
                $total_amount += $row->m_purcs_total;
                $tbody .= '<tr>
                <th scope="row">' . ($i + 1) . '</th>
                <th scope="row">' . date('d/m/Y', strtotime($row->m_purcs_date)) . '</th>
                <td>' . $row->m_item_name . '</td>
                <td>' . $row->m_purcs_lot . '</td>
                <td>' . $row->m_purcs_qty . '</td>
                <td>' . $row->issue_rate . '</td>
                <td>' . $row->m_purcs_total . '</td>
                <td>' . ($row->from_branch_name ?: 'Main') . '</td>
                <td>' . ($row->to_branch_name ?: 'Main') . '</td>
                <td>' . $row->m_purcs_spo . '</td>
                <td>' . ($row->added_by_name ?: 'Admin') . '</td>
            </tr>';
            }
        } else {
            $tbody = '<tr><td colspan="11" class="text-center">No transfer records found</td></tr>';
        }

        $data['Mainarray']  = $tbody;
        $data['tablefoot']  = '<tr><th colspan="4">TOTAL</th><th>' . $total_qty . '</th><th></th><th>' . $total_amount . '</th><th colspan="4"></th></tr>';
        $data['mainfooter'] = '<div class="text-end mt-2"><h4><strong>Total Transfers: ' . count($all_value) . '</strong> | Total Value: ₹' . $total_amount . '</h4></div>';

        $this->load->view('afc_account', $data);
    }

    // Super Admin: outstanding balance summary across all branches
    public function branch_outstanding()
    {
        $data = $this->login_details();
        if ($this->session->userdata('user_type') != 8) {
            show_error('Access Denied', 403);
            return;
        }

        $data['pagename']   = "Branch Outstanding";
        $data['exporttype'] = 1;
        $data['pagelink']   = "";

        $data['subhead'] = '<div class="col-6">
                                <h4 class="m-0"><strong>Branch Outstanding</strong></h4>
                            </div>
                            <div class="col-6 text-end">
                                <h4 class="m-0"><strong>As on ' . date('d/m/Y') . '</strong></h4>
                            </div>';

        $data['tableheader'] = '<tr>
                                <th scope="col">Sno</th>
                                <th scope="col">BRANCH</th>
                                <th scope="col">MOBILE</th>
                                <th scope="col">OUTSTANDING BALANCE</th>
                                <th scope="col">ACTION</th>
                            </tr>';

        $all_value = $this->Report_model->branch_outstanding_list();
        $tbody = '';
        $total_balance = 0;

        if (!empty($all_value)) {
            foreach ($all_value as $i => $row) {
                $total_balance += $row->m_user_balance;
                $tbody .= '<tr>
                <th scope="row">' . ($i + 1) . '</th>
                <td>' . $row->m_user_name . '</td>
                <td>' . $row->m_user_mobile . '</td>
                <td>' . number_format($row->m_user_balance, 2) . '</td>
                <td><a class="btn btn-primary btn-sm no-print" href="' . base_url('Reports/branch_ledger/' . $row->m_user_id) . '">View Ledger</a></td>
            </tr>';
            }
        } else {
            $tbody = '<tr><td colspan="5" class="text-center">No branches found</td></tr>';
        }

        $data['Mainarray']  = $tbody;
        $data['tablefoot']  = '<tr><th colspan="3">TOTAL</th><th>' . number_format($total_balance, 2) . '</th><th></th></tr>';
        $data['mainfooter'] = '<div class="text-end mt-2"><h4><strong>Total Outstanding: ₹' . number_format($total_balance, 2) . '</strong></h4></div>';

        $this->load->view('afc_account', $data);
    }

    // Branch Ledger: Issue Bills (debit, branch owes more) vs Payments Received (credit)
    public function branch_ledger($id = null)
    {
        $data = $this->login_details();
        $user_type = $this->session->userdata('user_type');

        if (!in_array($user_type, [8, 9])) {
            show_error('Access Denied', 403);
            return;
        }

        if ($user_type == 9) {
            $branch_id = $this->session->userdata('user_id');
        } else {
            $branch_id = $id ?: $this->input->post('account_name');
        }

        if (empty($branch_id)) {
            redirect('Reports/branch_outstanding');
            return;
        }

        // Unscoped on purpose - a branch account is not filed under itself,
        // so the scoped lookup found nothing when a branch user opened their
        // own ledger and every read off $branch_dtl warned on null.
        $branch_dtl = $this->Main_model->get_branch_dtl($branch_id);
        if (empty($branch_dtl)) {
            show_error('No branch account found for this selection, so there is no branch ledger to show.', 404);
            return;
        }

        $data['pagename']   = "Branch Ledger";
        $data['exporttype'] = 1;
        $data['pagelink']   = "";

        $data['from_date'] = $this->input->post('from_date') ?: date('Y-m-d', strtotime('-30 days'));
        $data['todate']    = $this->input->post('to_date') ?: date('Y-m-d');

        $data['subhead'] = '<div class="col-6">
                                <h4 class="m-0"><strong>' . $branch_dtl->m_user_name . '</strong></h4>
                            </div>
                            <div class="col-6 text-end">
                                <h4 class="m-0"><strong>Branch Ledger</strong></h4>
                                <h4 class="fw-bold">' . date('d/m/Y', strtotime($data['from_date'])) . ' TO ' . date('d/m/Y', strtotime($data['todate'])) . '</h4>
                            </div>';

        $data['tableheader'] = '<tr>
                                <th scope="col">Sno</th>
                                <th scope="col">DATE</th>
                                <th scope="col">PARTICULARS</th>
                                <th scope="col">DEBIT (Issued)</th>
                                <th scope="col">CREDIT (Paid)</th>
                                <th scope="col">BALANCE</th>
                            </tr>';

        $opening_balance = $this->Report_model->get_branch_opening_balance($branch_id, $data['from_date']);
        $balance = $opening_balance;

        $rows = [];
        foreach ($this->Report_model->branch_ledger_bills($branch_id, $data['from_date'], $data['todate']) as $b) {
            $rows[] = ['date' => $b->m_purcs_date, 'particular' => 'Issue Bill No. ' . $b->m_purcs_spo . ' (Qty: ' . $b->tqty . ')', 'debit' => (float) $b->tamount, 'credit' => 0];
        }
        // Already normalised to {date, particular, debit, credit} - it merges
        // three sources (Head Office receipts, branch payments to Head Office,
        // and Head Office vouchers) and labels each with where it came from.
        foreach ($this->Report_model->branch_ledger_payments($branch_id, $data['from_date'], $data['todate']) as $p) {
            $rows[] = $p;
        }

        usort($rows, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        $tbody = '<tr>
            <th scope="row"></th>
            <th scope="row">' . date('d/m/Y', strtotime($data['from_date'])) . '</th>
            <th>Opening Balance</th>
            <th>' . ($opening_balance > 0 ? number_format($opening_balance, 2) : '') . '</th>
            <th>' . ($opening_balance < 0 ? number_format(abs($opening_balance), 2) : '') . '</th>
            <th>' . number_format($opening_balance, 2) . '</th>
        </tr>';

        $total_debit = 0;
        $total_credit = 0;

        foreach ($rows as $i => $row) {
            $balance += $row['debit'] - $row['credit'];
            $total_debit += $row['debit'];
            $total_credit += $row['credit'];
            $tbody .= '<tr>
                <th scope="row">' . ($i + 1) . '</th>
                <th scope="row">' . date('d/m/Y', strtotime($row['date'])) . '</th>
                <td>' . $row['particular'] . '</td>
                <td>' . ($row['debit'] ? number_format($row['debit'], 2) : '') . '</td>
                <td>' . ($row['credit'] ? number_format($row['credit'], 2) : '') . '</td>
                <td>' . number_format($balance, 2) . '</td>
            </tr>';
        }

        $data['Mainarray']  = $tbody;
        $data['tablefoot']  = '<tr><th colspan="3">TOTAL</th><th>' . number_format($total_debit, 2) . '</th><th>' . number_format($total_credit, 2) . '</th><th>' . number_format($balance, 2) . '</th></tr>';
        $data['mainfooter'] = '<div class="text-end mt-2"><h4><strong>Closing Balance: ₹' . number_format($balance, 2) . ' (Branch owes Head Office)</strong></h4></div>';

        $this->load->view('afc_account', $data);
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
