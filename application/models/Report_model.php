<?php date_default_timezone_set('Asia/Kolkata');

class Report_model extends CI_model
{

  // ========================== Branch helpers ==========================//

  /**
   * Returns true if the current user is a superadmin.
   */
  private function is_superadmin()
  {
    return $this->session->userdata('user_type') == 8;
  }

  private function branch_users()
  {
    return $this->db->select('m_user_id')->where('m_user_type', 9)->where('m_user_status', 1)->get('master_users_tbl')->result();
  }
  /**
   * See Main_model::branch_id() for the rationale. Only a type-9 branch account
   * is scoped by its own user id; applying that to every non-superadmin filters
   * ordinary users against a non-existent branch (BUG-002/BUG-003).
   */
  private function branch_id($override = null)
  {
    $type = $this->session->userdata('user_type');

    if ($type == 9) {
      return (int) $this->session->userdata('user_id');
    }

    if ($type == 8) {
      return ($override !== null && $override !== '') ? (int) $override : null;
    }

    return null;
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

  // ========================== Stock / Lot ==========================//

  public function get_lotwise_item($to = '', $item = '', $uniq = '', $branch_id = null)
  {
    $result    = [];
    $open_date = date('Y-m-d', strtotime($to . ' -1 day'));

    $this->db->select('
        m_purcs_id, m_purcs_item, m_purcs_lot, m_purcs_spo, m_purcs_date,
        m_purcs_weight, SUM(m_purcs_qty) as pur_qty,
        mit.m_item_name, mit.m_item_price, mit.m_item_fright,
        crate.m_itgrp_title as cratetype, unit.m_itgrp_title as unitname,
        supp.m_user_trademark
    ');
    $this->db->from('master_purchase_tbl');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_purchase_tbl.m_purcs_item', 'left');
    $this->db->join('master_users_tbl as supp', 'supp.m_user_id = master_purchase_tbl.m_purcs_suplier', 'left');
    $this->db->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left');
    $this->db->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');
    $this->db->where('m_purcs_date <=', $to);
    $this->where_branch('master_purchase_tbl.m_purcs_branch', $branch_id);
    if (!empty($item)) $this->db->where('m_purcs_item', $item);
    $this->db->order_by('mit.m_item_name');
    $this->db->order_by('m_purcs_date', 'desc');
    $this->db->group_by('m_purcs_id');
    $purchases = $this->db->get()->result();
    if (empty($purchases)) return [];

    // Resolve the actual branch value once so private helpers receive a
    // consistent, already-resolved integer (or null for all-branches).
    $resolved_branch = $this->branch_id($branch_id);

    $issue_data      = $this->get_issue_aggregate($to, $resolved_branch);
    $issue_open_data = $this->get_issue_aggregate($open_date, $resolved_branch);
    $sale_data       = $this->get_sale_aggregate($to, $resolved_branch);
    $sale_open_data  = $this->get_sale_aggregate($open_date, $resolved_branch);

    foreach ($purchases as $key) {
      $pur_qty    = $key->pur_qty;
      $pur_weight = $key->m_purcs_weight;

      $issue_qty    = $issue_data[$key->m_purcs_id]['issue_qty']  ?? 0;
      $return_qty   = $issue_data[$key->m_purcs_id]['return_qty'] ?? 0;
      $sale_qty     = $sale_data[$key->m_purcs_id]               ?? 0;

      $open_issue_qty  = $issue_open_data[$key->m_purcs_id]['issue_qty']  ?? 0;
      $open_return_qty = $issue_open_data[$key->m_purcs_id]['return_qty'] ?? 0;
      $open_sale_qty   = $sale_open_data[$key->m_purcs_id]                ?? 0;

      $open_balance_qty = ($pur_qty + $open_return_qty - $open_issue_qty - $open_sale_qty);
      $balance_qty      = ($pur_qty + $return_qty - $issue_qty - $sale_qty);

      $this->db->where('m_purcs_id', $key->m_purcs_id);
      $this->where_branch('m_purcs_branch', $branch_id);
      $this->db->update('master_purchase_tbl', ['m_purcs_available' => $balance_qty]);

      if ($balance_qty > 0) {
        $result[] = [
          "m_item_id"        => $key->m_purcs_item,
          "m_item_name"      => $key->m_item_name,
          "m_item_price"     => $key->m_item_price,
          "m_item_fright"    => $key->m_item_fright,
          "m_purcs_spo"      => $key->m_purcs_spo,
          "m_purcs_date"     => date('d/m', strtotime($key->m_purcs_date)),
          "m_user_trademark" => $key->m_user_trademark,
          "m_purcs_qty"      => $pur_qty,
          "cratetype"        => $key->cratetype ?? '',
          "unitname"         => $key->unitname ?? '',
          "m_purcs_id"       => $key->m_purcs_id,
          "m_purcs_lot"      => $key->m_purcs_lot,
          "balance_weight"   => $pur_weight,
          "balance_qty"      => $balance_qty,
          "opening_qty"      => $open_balance_qty == 0 ? $pur_qty : $open_balance_qty,
          "closing_qty"      => $balance_qty,
        ];
      }
    }

    return ($uniq == 1)
      ? $this->unique_multidimensional_array($result, 'm_item_name')
      : $result;
  }

  /**
   * Aggregate issue/return quantities per lot up to $date.
   *
   * $branch is always a pre-resolved value (int or null) — callers must
   * pass $this->branch_id(...) rather than a raw override.
   */
  private function get_issue_aggregate($date, $branch)
  {
    $this->db->select("
        si_issue_lotno,
        SUM(CASE WHEN si_issue_type = 1 THEN si_issue_qty ELSE 0 END) as issue_qty,
        SUM(CASE WHEN si_issue_type = 2 THEN si_issue_qty ELSE 0 END) as return_qty
    ");
    $this->db->from('staff_itemissue_tbl');
    $this->db->where('si_issue_status', 1);
    $this->db->where('si_issue_date <=', $date);
    if ($branch !== null) $this->db->where('si_issue_branch', $branch);
    $this->db->group_by('si_issue_lotno');
    $rows = $this->db->get()->result();

    $data = [];
    foreach ($rows as $r) {
      $data[$r->si_issue_lotno] = [
        'issue_qty'  => $r->issue_qty,
        'return_qty' => $r->return_qty,
      ];
    }
    return $data;
  }

  /**
   * Aggregate sale quantities per lot up to $date.
   *
   * $branch is always a pre-resolved value (int or null).
   */
  private function get_sale_aggregate($date, $branch)
  {

    $branchUsers = $this->branch_users();
    if ($this->is_superadmin() && !empty($branchUsers)) {
      $branchUserIds = array_column($branchUsers, 'm_user_id');
      $this->db->where_in('m_sale_added_by', $branchUserIds);
    } else {
      $this->db->where('m_sale_added_by', $this->session->userdata('user_id'));
    }
    $this->db->select("m_sale_lot, SUM(m_sale_qty) as sale_qty");
    $this->db->from('master_sales_tbl');
    if ($branch !== null) $this->db->where('m_sale_branch', $branch);
    $this->db->where('m_sale_date <=', $date);
    $this->db->group_by('m_sale_lot');
    $rows = $this->db->get()->result();

    $data = [];
    foreach ($rows as $r) {
      $data[$r->m_sale_lot] = $r->sale_qty;
    }
    return $data;
  }

  /**
   * Every figure the crate-balance report needs, for ALL customers, as of one
   * cut-off date - in six grouped queries instead of ~12 per customer.
   *
   * Each query is the set-based form of the matching per-customer query in
   * Main_model::get_opening_balance()/get_crate_ledger(), so the arithmetic
   * that the caller then performs is unchanged.
   *
   * Returns array(cust_id => array(
   *   'grand_total','sub_total','total_expense','amount_rcvd',
   *   'vouch_cdt','vouch_dbt','crates' => array(crate_id => array('given','rcvd'))
   * ))
   */
  public function balance_snapshot($upto_date, $branch_id = null)
  {
    $branch = $this->branch_id($branch_id);
    $bw = ($branch !== null);
    $out = array();

    $row = function ($cid) use (&$out) {
      if (!isset($out[$cid])) {
        $out[$cid] = array(
          'sub_total' => 0, 'total_expense' => 0, 'grand_total' => 0,
          'amount_rcvd' => 0, 'vouch_cdt' => 0, 'vouch_dbt' => 0,
          'crates' => array(),
        );
      }
      return $cid;
    };

    // --- sales: inner group by SPO (header expenses are per-SPO), then by customer.
    // Grouped by (customer, SPO), not SPO alone: a handful of SPOs carry rows for
    // more than one customer, and the per-customer query this replaces filtered
    // by customer before grouping. Grouping by SPO alone would hand one such
    // SPO's whole total to whichever customer MySQL happened to pick.
    $sql = 'SELECT cid, SUM(sub_total) st, SUM(texpense) te FROM (
              SELECT m_sale_customer cid, SUM(m_sale_total) sub_total,
                     (m_sale_comm+m_sale_fright+m_sale_hamali+m_sale_others) texpense
              FROM master_sales_tbl
              WHERE 1=1' . (!empty($upto_date) ? ' AND m_sale_date <= ' . $this->db->escape($upto_date) : '')
      . ($bw ? ' AND m_sale_branch = ' . (int) $branch : '') . '
              GROUP BY m_sale_customer, m_sale_spo) t GROUP BY cid';
    foreach ($this->db->query($sql)->result() as $r) {
      $row($r->cid);
      $out[$r->cid]['sub_total']     = (float) $r->st;
      $out[$r->cid]['total_expense'] = (float) $r->te;
      $out[$r->cid]['grand_total']   = (float) $r->st + (float) $r->te;
    }

    // --- receipts
    $sql = 'SELECT m_recvd_customer cid, SUM(m_recvd_amount) amt
            FROM master_recieved_tbl
            WHERE m_recvd_account = 1 AND m_recvd_type = 1'
      . (!empty($upto_date) ? ' AND m_recvd_date <= ' . $this->db->escape($upto_date) : '')
      . ($bw ? ' AND m_recvd_branch = ' . (int) $branch : '') . ' GROUP BY cid';
    foreach ($this->db->query($sql)->result() as $r) {
      $row($r->cid);
      $out[$r->cid]['amount_rcvd'] = (float) $r->amt;
    }

    // --- vouchers, credit (type 1) and debit (type 2) in one pass
    $sql = 'SELECT m_voucher_accountid cid, m_voucher_type vt, SUM(m_voucher_amount) amt
            FROM master_voucher_tbl
            WHERE m_voucher_account = 1 AND m_voucher_status = 1
              AND m_voucher_type IN (1,2)'
      . (!empty($upto_date) ? ' AND m_voucher_date <= ' . $this->db->escape($upto_date) : '')
      . ($bw ? ' AND m_voucher_branch = ' . (int) $branch : '') . ' GROUP BY cid, vt';
    foreach ($this->db->query($sql)->result() as $r) {
      $row($r->cid);
      $out[$r->cid][$r->vt == 1 ? 'vouch_cdt' : 'vouch_dbt'] = (float) $r->amt;
    }

    // --- crates given (sales)
    $sql = 'SELECT s.m_sale_customer cid, mit.m_item_crate crate, SUM(s.m_sale_crate) q
            FROM master_sales_tbl s
            LEFT JOIN master_item_tbl mit ON mit.m_item_id = s.m_sale_item
            WHERE 1=1' . (!empty($upto_date) ? ' AND s.m_sale_date <= ' . $this->db->escape($upto_date) : '')
      . ($bw ? ' AND s.m_sale_branch = ' . (int) $branch : '') . ' GROUP BY cid, crate';
    foreach ($this->db->query($sql)->result() as $r) {
      $row($r->cid);
      $out[$r->cid]['crates'][$r->crate]['given'] = (int) $r->q;
    }

    // --- crates received
    $sql = 'SELECT m_recvd_customer cid, m_recvd_crate crate, SUM(m_recvd_qty) q
            FROM master_recieved_tbl
            WHERE m_recvd_type = 2'
      . (!empty($upto_date) ? ' AND m_recvd_date <= ' . $this->db->escape($upto_date) : '')
      . ($bw ? ' AND m_recvd_branch = ' . (int) $branch : '') . ' GROUP BY cid, crate';
    foreach ($this->db->query($sql)->result() as $r) {
      $row($r->cid);
      $out[$r->cid]['crates'][$r->crate]['rcvd'] = (int) $r->q;
    }

    return $out;
  }

  /**
   * Shapes one balance_snapshot() entry into the same array get_opening_balance()
   * returns, so callers keep doing the identical arithmetic on it.
   *
   * $cust must carry m_cust_opening and m_cust_crateOP (get_cust_list selects *).
   * $all_crates is Master_model::all_itemgroup(3), fetched once by the caller.
   */
  public function snapshot_row($snapshot, $cust, $all_crates)
  {
    $s = $snapshot[$cust->m_cust_id] ?? array(
      'sub_total' => 0, 'total_expense' => 0, 'grand_total' => 0,
      'amount_rcvd' => 0, 'vouch_cdt' => 0, 'vouch_dbt' => 0, 'crates' => array(),
    );

    $result = array(
      'cust_name'       => $cust->m_cust_name,
      'm_cust_hndiname' => $cust->m_cust_hndiname ?? '',
      'cust_mobile'     => $cust->m_cust_mobile,
      'sub_total'       => $s['sub_total'],
      'total_expense'   => $s['total_expense'],
      'grand_total'     => $s['grand_total'],
      'amount_rcvd'     => $s['amount_rcvd'] ?: 0,
      'balance_amount'  => $cust->m_cust_opening
        + (($s['grand_total'] + $s['vouch_dbt']) - ($s['amount_rcvd'] + $s['vouch_cdt'])),
    );

    $openin_crate_bal = explode(',', (string) $cust->m_cust_crateOP);
    $crate_total = $total_given = $total_recieved = 0;
    $result['crateitems'] = array();

    foreach ($all_crates as $key) {
      $given = (int) ($s['crates'][$key->m_itgrp_id]['given'] ?? 0);
      $rcvd  = (int) ($s['crates'][$key->m_itgrp_id]['rcvd'] ?? 0);
      $crate_total    += ($given - $rcvd);
      $total_given    += $given;
      $total_recieved += $rcvd;

      if ($key->m_itgrp_title == '10 KG') {
        $crattype_bal = $openin_crate_bal[0] ?? 0;
      } else if ($key->m_itgrp_title == '20 KG') {
        $crattype_bal = $openin_crate_bal[1] ?? 0;
      } else if ($key->m_itgrp_title == '25 KG') {
        $crattype_bal = $openin_crate_bal[2] ?? 0;
      } else {
        $crattype_bal = 0;
      }

      $result['crateitems'][] = array(
        'name'    => $key->m_itgrp_title,
        'recived' => $rcvd,
        'given'   => $given,
        'balance' => ((int) $crattype_bal + $given - $rcvd),
      );
    }

    $result['crate_given']    = $total_given;
    $result['crate_recieved'] = $total_recieved;
    $result['balance_crate']  = array_sum(explode(',', (string) $cust->m_cust_crateOP)) + $crate_total;

    return $result;
  }

  /**
   * customer id => date of that customer's most recent sale.
   * Matches the per-customer "order by m_sale_id desc, take first" rule the
   * crate-balance report used to run once per customer.
   */
  public function last_sale_date_map()
  {
    $sql = 'SELECT s.m_sale_customer AS cid, s.m_sale_date AS d
            FROM master_sales_tbl s
            JOIN (SELECT MAX(m_sale_id) AS mid FROM master_sales_tbl
                  GROUP BY m_sale_customer) t ON t.mid = s.m_sale_id';
    $map = array();
    foreach ($this->db->query($sql)->result() as $r) {
      $map[$r->cid] = $r->d;
    }
    return $map;
  }

  /** customer id => date of that customer's most recent receipt (account 1). */
  public function last_receipt_date_map()
  {
    $sql = 'SELECT r.m_recvd_customer AS cid, r.m_recvd_date AS d
            FROM master_recieved_tbl r
            JOIN (SELECT MAX(m_recvd_id) AS mid FROM master_recieved_tbl
                  WHERE m_recvd_account = 1
                  GROUP BY m_recvd_customer) t ON t.mid = r.m_recvd_id';
    $map = array();
    foreach ($this->db->query($sql)->result() as $r) {
      $map[$r->cid] = $r->d;
    }
    return $map;
  }

  public function get_item_stock_list($from = '', $to = '', $item = '', $branch_id = null)
  {
    $branch    = $this->branch_id($branch_id);   // resolved once
    $result    = [];
    $all_items = $this->Master_model->get_all_item($item, $branch);
    if (empty($all_items)) return [];

    $purchase_data = $this->get_purchase_aggregate($from, $to, $branch);
    $issue_data    = $this->get_issue_aggregate_itemwise(1, $from, $to, $branch);
    $return_data   = $this->get_issue_aggregate_itemwise(2, $from, $to, $branch);
    $sale_data     = $this->get_sale_aggregate_itemwise($from, $to, $branch);

    foreach ($all_items as $key) {
      $item_id = $key->m_item_id;
      $pur     = $purchase_data[$item_id] ?? ['qty' => 0, 'weight' => 0, 'price' => 0, 'date' => null];
      $iss     = $issue_data[$item_id]    ?? ['qty' => 0, 'weight' => 0];
      $ret     = $return_data[$item_id]   ?? ['qty' => 0, 'weight' => 0];
      $sale    = $sale_data[$item_id]     ?? ['qty' => 0, 'weight' => 0];

      $result[] = [
        "m_item_id"      => $item_id,
        "m_item_name"    => $key->m_item_name,
        "m_issue_price"  => $pur['price'],
        "cratetype"      => $key->cratetype ?? '',
        "unitname"       => $key->unitname ?? '',
        "groupname"      => $key->groupname ?? '',
        "balance_weight" => ($pur['weight'] + $ret['weight'] - $iss['weight'] - $sale['weight']),
        "balance_qty"    => ($pur['qty'] + $ret['qty'] - $iss['qty'] - $sale['qty']),
        "last_updated"   => $pur['date'] ? date('d-m-Y', strtotime($pur['date'])) : '',
      ];
    }
    return $result;
  }

  private function get_purchase_aggregate($from, $to, $branch)
  {
    $this->db->select("m_purcs_item, SUM(m_purcs_qty) as qty, SUM(m_purcs_weight) as weight, MAX(m_purcs_price) as price, MAX(m_purcs_date) as date");
    $this->db->from('master_purchase_tbl');
    if ($branch !== null) $this->db->where('m_purcs_branch', $branch);
    if (!empty($from)) $this->db->where('m_purcs_date >=', $from);
    if (!empty($to))   $this->db->where('m_purcs_date <=', $to);
    $this->db->group_by('m_purcs_item');
    $rows = $this->db->get()->result();

    $data = [];
    foreach ($rows as $r) {
      $data[$r->m_purcs_item] = [
        'qty'    => $r->qty,
        'weight' => $r->weight,
        'price'  => $r->price,
        'date'   => $r->date,
      ];
    }
    return $data;
  }

  private function get_issue_aggregate_itemwise($type, $from, $to, $branch)
  {
    $this->db->select("si_issue_item, SUM(si_issue_qty) as qty, SUM(si_issue_weight) as weight");
    $this->db->from('staff_itemissue_tbl');
    $this->db->where('si_issue_type', $type);
    $this->db->where('si_issue_status', 1);
    if ($branch !== null) $this->db->where('si_issue_branch', $branch);
    if (!empty($from)) $this->db->where('si_issue_date >=', $from);
    if (!empty($to))   $this->db->where('si_issue_date <=', $to);
    $this->db->group_by('si_issue_item');
    $rows = $this->db->get()->result();

    $data = [];
    foreach ($rows as $r) {
      $data[$r->si_issue_item] = ['qty' => $r->qty, 'weight' => $r->weight];
    }
    return $data;
  }

  private function get_sale_aggregate_itemwise($from, $to, $branch)
  {
    $this->db->select("m_sale_item, SUM(m_sale_qty) as qty, SUM(m_sale_weight) as weight");
    $this->db->from('master_sales_tbl');
    if ($branch !== null) $this->db->where('m_sale_branch', $branch);
    if (!empty($from)) $this->db->where('m_sale_date >=', $from);
    if (!empty($to))   $this->db->where('m_sale_date <=', $to);
    $this->db->group_by('m_sale_item');
    $rows = $this->db->get()->result();

    $data = [];
    foreach ($rows as $r) {
      $data[$r->m_sale_item] = ['qty' => $r->qty, 'weight' => $r->weight];
    }
    return $data;
  }

  // ========================== Issue item sale ==========================//

  public function get_issue_itemsale($issue_spo = '', $issue_id = '')
  {
    $branch = $this->branch_id();   // resolved once; null = superadmin all-branches
    if (!empty($issue_spo)) $this->db->where('si_issue_spo', $issue_spo);
    if (!empty($issue_id))  $this->db->where('si_issue_id', $issue_id);

    $this->db->select('si_issue_qty,si_issue_item,si_issue_lotno,si_issue_date,si_issue_weight,si_issue_user')
      ->where('si_issue_type', 1)
      ->where('si_issue_status', 1);
    if ($branch !== null) $this->db->where('si_issue_branch', $branch);
    $issued_item = $this->db->get('staff_itemissue_tbl')->result();

    $total_sale_qty    = 0;
    $total_sale_amount = 0;
    $total_balance_qty = 0;

    if (!empty($issued_item)) {
      foreach ($issued_item as $item) {
        $return_query = $this->db->select('SUM(si_issue_qty) AS qty')
          ->where('si_issue_lotno', $item->si_issue_lotno)
          ->where('si_issue_item', $item->si_issue_item)
          ->where('si_issue_user', $item->si_issue_user)
          ->where('si_issue_type', 2)
          ->where('si_issue_date', $item->si_issue_date)
          ->where('si_issue_status', 1);
        if ($branch !== null) $this->db->where('si_issue_branch', $branch);
        $return_row = $this->db->get('staff_itemissue_tbl')->row();
        $return_qty = (float)($return_row->qty ?? 0);

        $sale_query = $this->db->select('SUM(m_sale_qty) AS qty, SUM(m_sale_total + m_sale_fright) AS amount')
          ->where('m_sale_lot', $item->si_issue_lotno)
          ->where('m_sale_item', $item->si_issue_item)
          ->where('m_sale_user', $item->si_issue_user)
          ->where('m_sale_date', $item->si_issue_date);
        if ($branch !== null) $this->db->where('m_sale_branch', $branch);
        $sale_row = $this->db->get('master_sales_tbl')->row();
        $sale_qty = (float)($sale_row->qty ?? 0);
        $sale_amt = (float)($sale_row->amount ?? 0);

        $balance_qty = (float)$item->si_issue_qty - $sale_qty - $return_qty;

        $total_sale_qty    += $sale_qty;
        $total_sale_amount += $sale_amt;
        $total_balance_qty += $balance_qty;
      }

      return [
        'status'            => ($total_balance_qty > 0) ? 2 : 3,
        'total_sale_qty'    => $total_sale_qty,
        'total_sale_amount' => $total_sale_amount,
        'total_balance_qty' => $total_balance_qty,
      ];
    }
  }

  // ========================== Customer ledger ==========================//

  public function get_customer_amount_ledger($cust_id)
  {
    $branch = $this->branch_id();
    $result = [];

    $this->db->select('(sum(m_sale_total) + m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) as Tamount,m_sale_date,m_sale_note,m_user_name,m_user_mobile,1 as type')
      ->join('master_users_tbl mut', 'mut.m_user_id = master_sales_tbl.m_sale_user', 'left')
      ->where('m_sale_customer', $cust_id);
    if ($branch !== null) $this->db->where('master_sales_tbl.m_sale_branch', $branch);
    $salequery = $this->db->group_by('m_sale_spo')->get('master_sales_tbl')->result();
    foreach ($salequery as $key) $result[] = $key;

    $this->db->select('m_recvd_amount as Tamount,m_recvd_date as m_sale_date,m_recvd_remark as m_sale_note,m_user_name,m_user_mobile,2 as type')
      ->where('m_recvd_customer', $cust_id)
      ->join('master_users_tbl mut', 'mut.m_user_id = master_recieved_tbl.m_recvd_user', 'left')
      ->where('m_recvd_account', 1)
      ->where('m_recvd_type', 1);
    if ($branch !== null) $this->db->where('master_recieved_tbl.m_recvd_branch', $branch);
    $amountrcvdquery = $this->db->get('master_recieved_tbl')->result();
    foreach ($amountrcvdquery as $keey) $result[] = $keey;

    usort($result, fn($a, $b) => strcmp($a->m_sale_date, $b->m_sale_date));
    return $result;
  }

  public function get_customer_crate_ledger($cust_id)
  {
    $branch = $this->branch_id();
    $result = [];

    $this->db->select('sum(m_sale_crate) as tcrateqty,m_itgrp_title,m_sale_date,m_sale_note,m_user_name,m_user_mobile,1 as type')
      ->join('master_users_tbl mut', 'mut.m_user_id = master_sales_tbl.m_sale_user', 'left')
      ->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate')
      ->where('m_sale_customer', $cust_id);
    if ($branch !== null) $this->db->where('master_sales_tbl.m_sale_branch', $branch);
    $salequery = $this->db->group_by('m_sale_spo')->group_by('m_item_crate')->get('master_sales_tbl')->result();
    foreach ($salequery as $key) $result[] = $key;

    $this->db->select('m_recvd_qty as tcrateqty,m_itgrp_title,m_recvd_date as m_sale_date,m_recvd_remark as m_sale_note,m_user_name,m_user_mobile,2 as type')
      ->where('m_recvd_customer', $cust_id)
      ->join('master_users_tbl mut', 'mut.m_user_id = master_recieved_tbl.m_recvd_user', 'left')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left')
      ->where('m_recvd_type', 2);
    if ($branch !== null) $this->db->where('master_recieved_tbl.m_recvd_branch', $branch);
    $amountrcvdquery = $this->db->get('master_recieved_tbl')->result();
    foreach ($amountrcvdquery as $keey) $result[] = $keey;

    usort($result, fn($a, $b) => strcmp($a->m_sale_date, $b->m_sale_date));
    return $result;
  }

  public function customer_detailed_leger($from_date, $todate, $customers, $branch_id = null)
  {
    $branch = $this->branch_id($branch_id);
    $sql1   = [];

    // Sales
    $this->db->where('m_sale_date >=', $from_date)->where('m_sale_date <=', $todate)
      ->select('m_sale_spo,m_sale_date,sum(m_sale_qty) as tqty,sum(m_sale_crate) as tcrate,sum(m_sale_total) as sub_total,(m_sale_comm+m_sale_fright+m_sale_hamali+m_sale_others) as texpense,m_sale_note')
      ->where('m_sale_customer', $customers);
    if ($branch !== null) $this->db->where('m_sale_branch', $branch);
    $salequery = $this->db->group_by('m_sale_spo')->get('master_sales_tbl')->result();

    foreach ($salequery as $key) {
      $this->db->select('m_sale_spo,m_sale_qty,m_sale_price,m_sale_total,m_sale_date,m_sale_weight,m_sale_crate,m_item_name,(CASE WHEN group.m_itgrp_title IS NULL THEN "" ELSE group.m_itgrp_title END) AS groupname,(CASE WHEN crate.m_itgrp_title IS NULL THEN "" ELSE crate.m_itgrp_title END) AS cratetype,(CASE WHEN unit.m_itgrp_title IS NULL THEN "" ELSE unit.m_itgrp_title END) AS unitname')
        ->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
        ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
        ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
        ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left')
        ->where('m_sale_spo', $key->m_sale_spo);
      if ($branch !== null) $this->db->where('m_sale_branch', $branch);
      $sale_items = $this->db->get('master_sales_tbl')->result();

      $sql1[] = [
        'date'        => $key->m_sale_date,
        'recipt_no'   => $key->m_sale_spo,
        'particular'  => $sale_items,
        'debited'     => $key->sub_total + $key->texpense,
        'expense'     => $key->texpense,
        'note'        => $key->m_sale_note,
        'total_qty'   => $key->tqty,
        'total_crate' => $key->tcrate,
        'type'        => 3,
      ];
    }

    // Crate received
    $this->db->where('m_recvd_date >=', $from_date)->where('m_recvd_date <=', $todate)
      ->select('sum(m_recvd_qty) as tqty,m_recvd_voucher,m_recvd_date,m_recvd_remark')
      ->where('m_recvd_type', 2)->where('m_recvd_customer', $customers);
    if ($branch !== null) $this->db->where('m_recvd_branch', $branch);
    $cratequery = $this->db->group_by('m_recvd_voucher')->get('master_recieved_tbl')->result();
    foreach ($cratequery as $keey) {
      $this->db->select('m_recvd_crate as m_crate_id,crate.m_itgrp_title as m_crate_name,m_recvd_qty')
        ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left')
        ->where('m_recvd_voucher', $keey->m_recvd_voucher)->where('m_recvd_type', 2);
      if ($branch !== null) $this->db->where('m_recvd_branch', $branch);
      $crate_list = $this->db->order_by('m_itgrp_id')->get('master_recieved_tbl')->result();
      $sql1[] = [
        'date'        => $keey->m_recvd_date,
        'recipt_no'   => $keey->m_recvd_voucher,
        'particular'  => $crate_list,
        'debited'     => '',
        'expense'     => '',
        'note'        => $keey->m_recvd_remark,
        'total_qty'   => $keey->tqty,
        'total_crate' => '',
        'type'        => 2,
      ];
    }

    // Cash received
    $this->db->where('m_recvd_date >=', $from_date)->where('m_recvd_date <=', $todate);
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_recieved_tbl.m_recvd_method', 'left');
    $this->db->select('sum(m_recvd_amount) as tamont,m_recvd_method,m_group_name as method_name,m_recvd_voucher,m_recvd_date,m_recvd_remark')
      ->where('m_recvd_account', 1)->where('m_recvd_type', 1)->where('m_recvd_customer', $customers);
    if ($branch !== null) $this->db->where('master_recieved_tbl.m_recvd_branch', $branch);
    $cashquery = $this->db->group_by('m_recvd_voucher')->get('master_recieved_tbl')->result();
    foreach ($cashquery as $krey) {
      $this->db->select('m_recvd_crate as m_crate_id,m_recvd_method,m_recvd_amount')
        ->where('m_recvd_voucher', $krey->m_recvd_voucher)
        ->where('m_recvd_account', 1)->where('m_recvd_type', 1);
      if ($branch !== null) $this->db->where('m_recvd_branch', $branch);
      $cash_list = $this->db->get('master_recieved_tbl')->result();
      $sql1[] = [
        'date'        => $krey->m_recvd_date,
        'recipt_no'   => $krey->m_recvd_voucher,
        'particular'  => $cash_list,
        'debited'     => $krey->tamont,
        'expense'     => $krey->method_name,
        'note'        => $krey->m_recvd_remark,
        'total_qty'   => '',
        'total_crate' => '',
        'type'        => 1,
      ];
    }

    // Vouchers
    $this->db->select('m_voucher_id,m_voucher_amount as tamont,m_voucher_type,m_voucher_date,m_voucher_remark')
      ->where('m_voucher_date >=', $from_date)->where('m_voucher_date <=', $todate)
      ->where('m_voucher_account', 1)->where('m_voucher_accountid', $customers);
    if ($branch !== null) $this->db->where('m_voucher_branch', $branch);
    $voucherquery = $this->db->get('master_voucher_tbl')->result();
    foreach ($voucherquery as $krey) {
      $sql1[] = [
        'date'        => $krey->m_voucher_date,
        'recipt_no'   => $krey->m_voucher_id,
        'particular'  => '',
        'debited'     => $krey->tamont,
        'expense'     => $krey->m_voucher_type,
        'note'        => $krey->m_voucher_remark,
        'total_qty'   => '',
        'total_crate' => '',
        'type'        => 4,
      ];
    }

    $names = array_column($sql1, 'date');
    array_multisort($names, SORT_ASC, $sql1);
    return $sql1;
  }

  // ========================== Supplier ledger ==========================//

  public function get_sup_crate_ledger($crate_id, $sup_id, $from_date = '', $today = '', $branch_id = null)
  {
    $branch = $this->branch_id($branch_id);

    if ($today == 1) {
      if (!empty($from_date)) $this->db->where('m_purcs_date', $from_date);
    } else {
      if (!empty($from_date)) $this->db->where('m_purcs_date <=', $from_date);
    }
    $this->db->select('sum(m_purcs_crate) as tcrate,m_itgrp_title')
      ->join('master_item_tbl mit', 'mit.m_item_id = master_purchase_tbl.m_purcs_item', 'left')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
      ->where('m_purcs_suplier', $sup_id)->where('m_item_crate', $crate_id);
    if ($branch !== null) $this->db->where('master_purchase_tbl.m_purcs_branch', $branch);
    $crategiven = $this->db->group_by('m_item_crate')->get('master_purchase_tbl')->result();

    if ($today == 1) {
      if (!empty($from_date)) $this->db->where('m_payment_date', $from_date);
    } else {
      if (!empty($from_date)) $this->db->where('m_payment_date <=', $from_date);
    }
    $this->db->select('sum(m_payment_qty) as tcrateqty,m_itgrp_title')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_payment_tbl.m_payment_crate', 'left')
      ->where('m_payment_supplier', $sup_id)->where('m_payment_type', 2)->where('m_payment_crate', $crate_id);
    if ($branch !== null) $this->db->where('master_payment_tbl.m_payment_branch', $branch);
    $cratercvdquery = $this->db->group_by('m_payment_crate')->get('master_payment_tbl')->result();

    return [
      "crate_rcvd"    => $cratercvdquery ? $cratercvdquery[0]->tcrateqty : 0,
      "crate_given"   => $crategiven     ? $crategiven[0]->tcrate         : 0,
      "crate_balance" => (($crategiven     ? $crategiven[0]->tcrate         : 0)
        - ($cratercvdquery ? $cratercvdquery[0]->tcrateqty  : 0)),
    ];
  }

  public function get_sup_opening_balance($sup_id, $from_date, $branch_id = null)
  {
    $branch      = $this->branch_id($branch_id);
    $opening_bal = $this->Main_model->get_user_dtl($sup_id);

    $sub_total = $total_expense = $grand_total = $crate_total = $total_given = $total_recieved = 0;

    if (!empty($from_date)) $this->db->where('m_purcs_date <=', $from_date);
    $this->db->select('sum(m_purcs_qty) as tqty,sum(m_purcs_total) as sub_total,sum(m_purcs_crate) as tcrate,(m_purcs_comm + m_purcs_fright + m_purcs_hamali + m_purcs_charity + m_purcs_packaging + m_purcs_loading + m_purcs_advance + m_purcs_others) as texpense')
      ->where('m_purcs_suplier', $sup_id);
    if ($branch !== null) $this->db->where('m_purcs_branch', $branch);
    $salequery = $this->db->group_by('m_purcs_spo')->get('master_purchase_tbl')->result();
    foreach ($salequery as $key) {
      $sub_total     += $key->sub_total;
      $total_expense += $key->texpense;
      $grand_total   += ($key->sub_total + $key->texpense);
    }

    if (!empty($from_date)) $this->db->where('m_payment_date <=', $from_date);
    $this->db->select('sum(m_payment_amount) as tamountrcvd')
      ->where('m_payment_supplier', $sup_id)->where('m_payment_type', 1)->where('m_payment_account', 1);
    if ($branch !== null) $this->db->where('m_payment_branch', $branch);
    $amountrcvdquery = $this->db->get('master_payment_tbl')->result();

    if (!empty($from_date)) $this->db->where('m_recvd_date <=', $from_date);
    $this->db->select('sum(m_recvd_amount) as tamountrcvd')
      ->where('m_recvd_customer', $sup_id)->where('m_recvd_type', 1)->where('m_recvd_account', 4);
    if ($branch !== null) $this->db->where('m_recvd_branch', $branch);
    $amountpaidquery = $this->db->get('master_recieved_tbl')->result();

    if (!empty($from_date)) $this->db->where('m_voucher_date <=', $from_date);
    $this->db->select('sum(m_voucher_amount) as tamountcdt')
      ->where('m_voucher_accountid', $sup_id)->where('m_voucher_account', 2)->where('m_voucher_type', 1)
      ->where('m_voucher_status', 1);
    if ($branch !== null) $this->db->where('m_voucher_branch', $branch);
    $vouch_amtcdrt = $this->db->get('master_voucher_tbl')->result();

    if (!empty($from_date)) $this->db->where('m_voucher_date <=', $from_date);
    $this->db->select('sum(m_voucher_amount) as tamountdbt')
      ->where('m_voucher_accountid', $sup_id)->where('m_voucher_account', 2)->where('m_voucher_type', 2)
      ->where('m_voucher_status', 1);
    if ($branch !== null) $this->db->where('m_voucher_branch', $branch);
    $vouch_amtdbt = $this->db->get('master_voucher_tbl')->result();

    $balance_amt = ($opening_bal->m_user_opening * (-1))
      + (($grand_total + $vouch_amtcdrt[0]->tamountcdt)
        - ($amountrcvdquery[0]->tamountrcvd + $vouch_amtdbt[0]->tamountdbt)
        + $amountpaidquery[0]->tamountrcvd);

    $result = [
      "sub_total"      => $sub_total,
      "total_expense"  => $total_expense,
      "grand_total"    => $grand_total,
      "amount_rcvd"    => $amountrcvdquery[0]->tamountrcvd  ?: 0,
      "amount_paid"    => $amountpaidquery[0]->tamountrcvd  ?: 0,
      "balance_amount" => $balance_amt,
    ];

    $all_crates       = $this->Master_model->all_itemgroup(3);
    $openin_crate_bal = explode(',', $opening_bal->m_user_crateOP);
    foreach ($all_crates as $key) {
      $crateledger    = $this->get_sup_crate_ledger($key->m_itgrp_id, $sup_id, $from_date);
      $crate_total   += ((int)$crateledger['crate_given'] - (int)$crateledger['crate_rcvd']);
      $total_given   += (int)$crateledger['crate_given'];
      $total_recieved += (int)$crateledger['crate_rcvd'];
      if ($key->m_itgrp_title == '10 KG') {
        $crattype_bal = $openin_crate_bal[0] ?? 0;
      } elseif ($key->m_itgrp_title == '20 KG') {
        $crattype_bal = $openin_crate_bal[1] ?? 0;
      } elseif ($key->m_itgrp_title == '25 KG') {
        $crattype_bal = $openin_crate_bal[2] ?? 0;
      } else {
        $crattype_bal = 0;
      }
      $result['crateitems'][] = [
        'name'    => $key->m_itgrp_title,
        'recived' => (int)$crateledger['crate_rcvd'],
        'given'   => (int)$crateledger['crate_given'],
        'balance' => ((int)$crattype_bal + (int)$crateledger['crate_given'] - (int)$crateledger['crate_rcvd']),
      ];
    }
    $result['crate_given']    = $total_given;
    $result['crate_recieved'] = $total_recieved;
    $result['balance_crate']  = array_sum(explode(',', $opening_bal->m_user_crateOP)) + $crate_total;
    return $result;
  }

  public function supplier_detailed_leger($from_date, $todate, $supplier, $branch_id = null)
  {
    $branch = $this->branch_id($branch_id);
    $sql1   = [];

    // Purchases
    $this->db->where('m_purcs_date >=', $from_date)->where('m_purcs_date <=', $todate)
      ->select('m_purcs_spo,m_purcs_date,sum(m_purcs_qty) as tqty,sum(m_purcs_crate) as tcrate,sum(m_purcs_total) as sub_total,(m_purcs_comm + m_purcs_fright + m_purcs_hamali + m_purcs_charity + m_purcs_packaging + m_purcs_loading + m_purcs_advance + m_purcs_others) as texpense,m_purcs_note')
      ->where('m_purcs_suplier', $supplier);
    if ($branch !== null) $this->db->where('m_purcs_branch', $branch);
    $salequery = $this->db->group_by('m_purcs_spo')->get('master_purchase_tbl')->result();
    foreach ($salequery as $key) {
      $this->db->select('m_purcs_spo,m_purcs_qty,m_purcs_price,m_purcs_total,m_purcs_date,m_purcs_weight,m_purcs_crate,m_item_name,(CASE WHEN group.m_itgrp_title IS NULL THEN "" ELSE group.m_itgrp_title END) AS groupname,(CASE WHEN crate.m_itgrp_title IS NULL THEN "" ELSE crate.m_itgrp_title END) AS cratetype,(CASE WHEN unit.m_itgrp_title IS NULL THEN "" ELSE unit.m_itgrp_title END) AS unitname,m_purcs_comm,m_purcs_comrate,m_purcs_fright,m_purcs_hamali,m_purcs_charity,m_purcs_packaging,m_purcs_loading,m_purcs_advance,m_purcs_others,m_purcs_truckno')
        ->join('master_item_tbl mit', 'mit.m_item_id = master_purchase_tbl.m_purcs_item', 'left')
        ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
        ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
        ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left')
        ->where('m_purcs_spo', $key->m_purcs_spo);
      if ($branch !== null) $this->db->where('m_purcs_branch', $branch);
      $sale_items = $this->db->get('master_purchase_tbl')->result();
      $sql1[] = [
        'date'        => $key->m_purcs_date,
        'recipt_no'   => $key->m_purcs_spo,
        'particular'  => $sale_items,
        'debited'     => $key->sub_total + $key->texpense,
        'expense'     => $key->texpense,
        'note'        => $key->m_purcs_note,
        'total_qty'   => $key->tqty,
        'total_crate' => $key->tcrate,
        'type'        => 3,
      ];
    }

    // Crate payments
    $this->db->where('m_payment_date >=', $from_date)->where('m_payment_date <=', $todate)
      ->select('sum(m_payment_qty) as tqty,m_payment_voucher,m_payment_date,m_payment_remark')
      ->where('m_payment_type', 2)->where('m_payment_supplier', $supplier);
    if ($branch !== null) $this->db->where('m_payment_branch', $branch);
    $cratequery = $this->db->group_by('m_payment_voucher')->get('master_payment_tbl')->result();
    foreach ($cratequery as $keey) {
      $this->db->select('m_payment_crate as m_crate_id,crate.m_itgrp_title as m_crate_name,m_payment_qty')
        ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_payment_tbl.m_payment_crate', 'left')
        ->where('m_payment_voucher', $keey->m_payment_voucher)->where('m_payment_type', 2);
      if ($branch !== null) $this->db->where('m_payment_branch', $branch);
      $crate_list = $this->db->order_by('m_itgrp_id')->get('master_payment_tbl')->result();
      $sql1[] = [
        'date'        => $keey->m_payment_date,
        'recipt_no'   => $keey->m_payment_voucher,
        'particular'  => $crate_list,
        'debited'     => '',
        'expense'     => '',
        'note'        => $keey->m_payment_remark,
        'total_qty'   => $keey->tqty,
        'total_crate' => '',
        'type'        => 2,
      ];
    }

    // Cash payments
    $this->db->where('m_payment_date >=', $from_date)->where('m_payment_date <=', $todate);
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_payment_tbl.m_payment_method', 'left');
    $this->db->select('sum(m_payment_amount) as tamont,m_payment_method,m_group_name as method_name,m_payment_voucher,m_payment_date,m_payment_remark')
      ->where('m_payment_type', 1)->where('m_payment_account', 1)->where('m_payment_supplier', $supplier);
    if ($branch !== null) $this->db->where('master_payment_tbl.m_payment_branch', $branch);
    $cashquery = $this->db->group_by('m_payment_voucher')->get('master_payment_tbl')->result();
    foreach ($cashquery as $krey) {
      $this->db->select('m_payment_crate as m_crate_id,m_payment_method,m_payment_amount')
        ->where('m_payment_voucher', $krey->m_payment_voucher)
        ->where('m_payment_type', 1)->where('m_payment_account', 1);
      if ($branch !== null) $this->db->where('m_payment_branch', $branch);
      $cash_list = $this->db->get('master_payment_tbl')->result();
      $sql1[] = [
        'date'        => $krey->m_payment_date,
        'recipt_no'   => $krey->m_payment_voucher,
        'particular'  => $cash_list,
        'debited'     => $krey->tamont,
        'expense'     => $krey->method_name,
        'note'        => $krey->m_payment_remark,
        'total_qty'   => 1,
        'total_crate' => '',
        'type'        => 1,
      ];
    }

    // Received from supplier (account 4)
    $this->db->where('m_recvd_date >=', $from_date)->where('m_recvd_date <=', $todate);
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_recieved_tbl.m_recvd_method', 'left');
    $this->db->select('sum(m_recvd_amount) as tamont,m_recvd_method,m_group_name as method_name,m_recvd_voucher,m_recvd_date,m_recvd_remark')
      ->where('m_recvd_type', 1)->where('m_recvd_account', 4)->where('m_recvd_customer', $supplier);
    if ($branch !== null) $this->db->where('master_recieved_tbl.m_recvd_branch', $branch);
    $rcvdquery = $this->db->group_by('m_recvd_voucher')->get('master_recieved_tbl')->result();
    foreach ($rcvdquery as $krey) {
      $this->db->select('m_recvd_crate as m_crate_id,m_recvd_method,m_recvd_amount')
        ->where('m_recvd_voucher', $krey->m_recvd_voucher)
        ->where('m_recvd_type', 1)->where('m_recvd_account', 4);
      if ($branch !== null) $this->db->where('m_recvd_branch', $branch);
      $cash_list = $this->db->get('master_recieved_tbl')->result();
      $sql1[] = [
        'date'        => $krey->m_recvd_date,
        'recipt_no'   => $krey->m_recvd_voucher,
        'particular'  => $cash_list,
        'debited'     => $krey->tamont,
        'expense'     => $krey->method_name,
        'note'        => $krey->m_recvd_remark,
        'total_qty'   => 2,
        'total_crate' => '',
        'type'        => 1,
      ];
    }

    // Vouchers
    $this->db->select('m_voucher_id,m_voucher_amount as tamont,m_voucher_type,m_voucher_date,m_voucher_remark')
      ->where('m_voucher_date >=', $from_date)->where('m_voucher_date <=', $todate)
      ->where('m_voucher_account', 2)->where('m_voucher_accountid', $supplier);
    if ($branch !== null) $this->db->where('m_voucher_branch', $branch);
    $voucherquery = $this->db->get('master_voucher_tbl')->result();
    foreach ($voucherquery as $krey) {
      $sql1[] = [
        'date'        => $krey->m_voucher_date,
        'recipt_no'   => $krey->m_voucher_id,
        'particular'  => '',
        'debited'     => $krey->tamont,
        'expense'     => $krey->m_voucher_type,
        'note'        => $krey->m_voucher_remark,
        'total_qty'   => '',
        'total_crate' => '',
        'type'        => 4,
      ];
    }

    $names = array_column($sql1, 'date');
    array_multisort($names, SORT_ASC, $sql1);
    return $sql1;
  }

  // ========================== Cash/Bank ==========================//

  public function cash_bank_balance($pagetype, $todate, $method, $opening_bal)
  {
    $branch        = $this->branch_id();
    $total_balance = $opening_bal;

    if (!empty($todate)) $this->db->where('m_recvd_date <=', $todate);
    $this->db->select('sum(m_recvd_amount) as tamont')->where('m_recvd_method', $method);
    if ($branch !== null) $this->db->where('m_recvd_branch', $branch);
    $cashquery = $this->db->get('master_recieved_tbl')->result();
    if (!empty($cashquery)) $total_balance += $cashquery[0]->tamont;

    if (!empty($todate)) $this->db->where('m_exp_date <=', $todate);
    $this->db->select('sum(m_exp_amount) as tamont')->where('m_exp_name', 83);
    if ($branch !== null) $this->db->where('m_exp_branch', $branch);
    $expenseQuery = $this->db->get('master_expenses_tbl')->result();
    if ($pagetype == 1 && !empty($expenseQuery)) $total_balance -= $expenseQuery[0]->tamont;

    if (!empty($todate)) $this->db->where('m_payment_date <=', $todate);
    if ($pagetype == 2) {
      $this->db->select("(CASE WHEN m_payment_account = 7 && m_payment_method = $method THEN '2' WHEN m_payment_account = 7 THEN '1' ELSE '2' END) as type")
        ->where("CASE WHEN m_payment_account = 7 && m_payment_method = '$method' THEN m_payment_method = '$method' WHEN m_payment_account = 7 THEN m_payment_supplier = '$method' ELSE m_payment_method = '$method' END");
    } else {
      $this->db->select('"2" as type')->where('m_payment_method', $method);
    }
    $this->db->select('m_payment_amount as tamont');
    if ($branch !== null) $this->db->where('m_payment_branch', $branch);
    $payquery = $this->db->get('master_payment_tbl')->result();
    foreach ($payquery as $krrey) {
      if ($krrey->type == 2) $total_balance -= $krrey->tamont;
      else                   $total_balance += $krrey->tamont;
    }

    return $total_balance;
  }

  public function cash_bank_leger($pagetype, $from_date, $todate, $method, $balance = '', $branch_id = null)
  {
    $branch      = $this->branch_id($branch_id);
    $opening_bal = $this->Master_model->get_edit_group($method);
    $total_balance = $opening_bal->m_group_opening;
    $sql1 = [];

    // Expenses
    if (!empty($from_date)) $this->db->where('m_exp_date >=', $from_date);
    if (!empty($todate))    $this->db->where('m_exp_date <=', $todate);
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_expenses_tbl.m_exp_user', 'left');
    $this->db->select('m_exp_id as id,m_exp_amount as tamont,"16" as method_id,"Cash" as method_name,m_exp_amount as recipt_no,m_exp_date as date,m_exp_remark as note,"Line Expense" as csname,mut.m_user_name as user,"" as city,"3" as type')
      ->where('m_exp_name', 83);
    if ($branch !== null) $this->db->where('m_exp_branch', $branch);
    $expenseQuery = $this->db->get('master_expenses_tbl')->result();
    if ($pagetype == 1 && !empty($expenseQuery)) {
      foreach ($expenseQuery as $krey) {
        $total_balance -= $krey->tamont;
        $sql1[] = $krey;
      }
    }

    // Received
    if (!empty($from_date)) $this->db->where('m_recvd_date >=', $from_date);
    if (!empty($todate))    $this->db->where('m_recvd_date <=', $todate);
    $this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_users_tbl mutt', 'mutt.m_user_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_recieved_tbl.m_recvd_user', 'left');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = mct.m_cust_city', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_recieved_tbl.m_recvd_method', 'left');
    if ($pagetype == 2) {
      $this->db->select("(CASE WHEN m_recvd_account = 7 && m_recvd_method = $method THEN '2' ELSE '1' END) as type")
        ->where("CASE WHEN m_recvd_account = 7 && m_recvd_method = '$method' THEN m_recvd_method = '$method' WHEN m_recvd_account = 7 THEN m_recvd_customer = '$method' ELSE m_recvd_method = '$method' END");
    } else {
      $this->db->select('"1" as type')->where('m_recvd_method', $method);
    }
    $this->db->select("m_recvd_id as id,m_recvd_amount as tamont,m_recvd_method as method_id,method.m_group_name as method_name,m_recvd_voucher as recipt_no,m_recvd_date as date,m_recvd_remark as note,(CASE WHEN m_recvd_account = 1 THEN mct.m_cust_name WHEN m_recvd_account = 7 && m_recvd_method = $method THEN mgt.m_group_name WHEN m_recvd_account = 7 THEN method.m_group_name ELSE mutt.m_user_name END) as csname,mut.m_user_name as user,m_city_name as city");
    if ($branch !== null) $this->db->where('master_recieved_tbl.m_recvd_branch', $branch);
    $cashquery = $this->db->get('master_recieved_tbl')->result();
    foreach ($cashquery as $krey) {
      $total_balance += $krey->tamont;
      $sql1[] = $krey;
    }

    // Payments
    if (!empty($todate))    $this->db->where('m_payment_date <=', $todate);
    if (!empty($from_date)) $this->db->where('m_payment_date >=', $from_date);
    if ($pagetype == 2) {
      $this->db->select("(CASE WHEN m_payment_account = 7 && m_payment_method = $method THEN '2' WHEN m_payment_account = 7 THEN '1' ELSE '2' END) as type")
        ->where("CASE WHEN m_payment_account = 7 && m_payment_method = '$method' THEN m_payment_method = '$method' WHEN m_payment_account = 7 THEN m_payment_supplier = '$method' ELSE m_payment_method = '$method' END");
    } else {
      $this->db->select('"2" as type')->where('m_payment_method', $method);
    }
    $this->db->join('master_users_tbl mct', 'mct.m_user_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_payment_tbl.m_payment_user', 'left');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = mct.m_user_city', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_payment_tbl.m_payment_method', 'left');
    $this->db->select("m_payment_id as id,m_payment_amount as tamont,m_payment_method as method_id,method.m_group_name as method_name,m_payment_voucher as recipt_no,m_payment_date as date,m_payment_remark as note,(CASE WHEN m_payment_account = 2 THEN mgt.m_group_name WHEN m_payment_account = 7 && m_payment_method = $method THEN mgt.m_group_name WHEN m_payment_account = 7 THEN method.m_group_name ELSE mct.m_user_name END) as csname,mut.m_user_name as user,m_city_name as city");
    if ($branch !== null) $this->db->where('master_payment_tbl.m_payment_branch', $branch);
    $payquery = $this->db->get('master_payment_tbl')->result();
    foreach ($payquery as $krrey) {
      if ($krrey->type == 2) $total_balance -= $krrey->tamont;
      else                   $total_balance += $krrey->tamont;
      $sql1[] = $krrey;
    }

    $names = array_column((array)$sql1, 'date');
    array_multisort($names, SORT_ASC, $sql1);

    return ($balance == 1) ? $total_balance : $sql1;
  }

  // ========================== General / Invest ledger ==========================//

  public function general_invest_leger($from_date, $todate, $account_name, $balance = '', $branch_id = null)
  {
    $branch        = $this->branch_id($branch_id);
    $opening_bal   = $this->Main_model->get_user_dtl($account_name);
    $total_balance = $opening_bal->m_user_opening;
    $sql1 = [];

    if (!empty($from_date)) $this->db->where('m_recvd_date >=', $from_date);
    if (!empty($todate))    $this->db->where('m_recvd_date <=', $todate);
    $this->db->join('master_users_tbl mutt', 'mutt.m_user_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_recieved_tbl.m_recvd_user', 'left');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = mutt.m_user_city', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_recieved_tbl.m_recvd_method', 'left');
    $this->db->select('m_recvd_id as id,m_recvd_amount as tamont,m_recvd_method as method_id,method.m_group_name as method_name,m_recvd_voucher as recipt_no,m_recvd_date as date,m_recvd_remark as note,mutt.m_user_name as csname,mut.m_user_name as user,"1" as type,m_city_name as city')
      ->where('(m_recvd_account = 2 OR m_recvd_account = 3)')->where('m_recvd_customer', $account_name);
    if ($branch !== null) $this->db->where('master_recieved_tbl.m_recvd_branch', $branch);
    $cashquery = $this->db->get('master_recieved_tbl')->result();
    foreach ($cashquery as $krey) {
      $total_balance -= $krey->tamont;
      $sql1[] = $krey;
    }

    if (!empty($todate))    $this->db->where('m_payment_date <=', $todate);
    if (!empty($from_date)) $this->db->where('m_payment_date >=', $from_date);
    $this->db->join('master_users_tbl mct', 'mct.m_user_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_payment_tbl.m_payment_user', 'left');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = mct.m_user_city', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_payment_tbl.m_payment_method', 'left');
    $this->db->select('m_payment_id as id,m_payment_amount as tamont,m_payment_method as method_id,method.m_group_name as method_name,m_payment_voucher as recipt_no,m_payment_date as date,m_payment_remark as note,(CASE WHEN m_payment_account = 2 THEN mgt.m_group_name ELSE mct.m_user_name END) as csname,mut.m_user_name as user,"2" as type,m_city_name as city')
      ->where('(m_payment_account = 5 OR m_payment_account = 6)')->where('m_payment_supplier', $account_name);
    if ($branch !== null) $this->db->where('master_payment_tbl.m_payment_branch', $branch);
    $payquery = $this->db->get('master_payment_tbl')->result();
    foreach ($payquery as $krrey) {
      $total_balance += $krrey->tamont;
      $sql1[] = $krrey;
    }

    if (!empty($todate))    $this->db->where('m_voucher_date <=', $todate);
    if (!empty($from_date)) $this->db->where('m_voucher_date >=', $from_date);
    $this->db->select('m_voucher_id as id,m_voucher_amount as tamont,m_voucher_type as method_id,"" as method_name,m_voucher_id as recipt_no,m_voucher_date as date,m_voucher_remark as note,"" as csname,"" as user,"3" as type,"" as city')
      ->where('(m_voucher_account = 6 OR m_voucher_account = 7)')->where('m_voucher_accountid', $account_name);
    if ($branch !== null) $this->db->where('m_voucher_branch', $branch);
    $voucherquery = $this->db->get('master_voucher_tbl')->result();
    foreach ($voucherquery as $krey) {
      if ($krey->type == 1) $total_balance += $krey->tamont;
      else                  $total_balance -= $krey->tamont;
      $sql1[] = $krey;
    }

    $names = array_column((array)$sql1, 'date');
    array_multisort($names, SORT_ASC, $sql1);
    return ($balance == 1) ? $total_balance : $sql1;
  }

  // ========================== Fright / Staff comm / Expense ledger ==========================//

  public function fright_ledger($from_date, $todate, $account_name, $group, $balance = '', $branch_id = null)
  {
    $branch        = $this->branch_id($branch_id);
    $opening_bal   = $this->Master_model->get_edit_group($account_name);
    $total_balance = $opening_bal->m_group_opening;
    $sql1 = [];

    if (!empty($from_date)) $this->db->where('m_sale_date >=', $from_date);
    if (!empty($todate))    $this->db->where('m_sale_date <=', $todate);
    $this->db->select('m_sale_id as id,"" as method_name,m_sale_spo as recipt_no,Group_concat(m_sale_qty,"*",mit.m_item_fright) as note,m_sale_date as date,m_sale_fright as tamont,"1" as type');
    $this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_sales_tbl.m_sale_customer', 'left');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left');
    $this->db->where('mct.m_cust_group', $group);
    if ($branch !== null) $this->db->where('master_sales_tbl.m_sale_branch', $branch);
    $this->db->group_by('m_sale_spo');
    $sale_datil = $this->db->get('master_sales_tbl')->result();
    foreach ($sale_datil as $krey) {
      $total_balance += $krey->tamont;
      $sql1[] = $krey;
    }

    if (!empty($todate))    $this->db->where('m_payment_date <=', $todate);
    if (!empty($from_date)) $this->db->where('m_payment_date >=', $from_date);
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_payment_tbl.m_payment_user', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_payment_tbl.m_payment_method', 'left');
    $this->db->select('m_payment_id as id,m_payment_amount as tamont,method.m_group_name as method_name,m_payment_voucher as recipt_no,m_payment_date as date,m_payment_remark as note,"2" as type')
      ->where('m_payment_account', 2)->where('m_payment_supplier', $account_name);
    if ($branch !== null) $this->db->where('m_payment_branch', $branch);
    $payquery = $this->db->get('master_payment_tbl')->result();
    foreach ($payquery as $krrey) {
      $total_balance -= $krrey->tamont;
      $sql1[] = $krrey;
    }

    $names = array_column((array)$sql1, 'date');
    array_multisort($names, SORT_ASC, $sql1);
    return ($balance == 1) ? $total_balance : $sql1;
  }

  public function staffcomm_ledger($from_date, $todate, $account_name, $balance = '', $branch_id = null)
  {
    $branch        = $this->branch_id($branch_id);
    $opening_bal   = $this->Main_model->get_user_dtl($account_name);
    $total_balance = $opening_bal->m_user_opening;
    $sql1 = [];

    if (!empty($from_date)) $this->db->where('m_sale_date >=', $from_date);
    if (!empty($todate))    $this->db->where('m_sale_date <=', $todate);
    $this->db->select('m_sale_id as id,"" as method_name,m_sale_spo as recipt_no,Group_concat(m_sale_qty,"*",mit.m_item_comm) as note,m_sale_date as date,Group_concat(m_sale_qty * mit.m_item_comm) as tamont,"1" as type');
    $this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_sales_tbl.m_sale_customer', 'left');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left');
    $this->db->where('m_sale_user', $account_name);
    if ($branch !== null) $this->db->where('master_sales_tbl.m_sale_branch', $branch);
    $this->db->group_by('m_sale_spo');
    $sale_datil = $this->db->get('master_sales_tbl')->result();
    foreach ($sale_datil as $krey) {
      $total_balance += array_sum(explode(',', $krey->tamont));
      $sql1[] = $krey;
    }

    if (!empty($todate))    $this->db->where('m_payment_date <=', $todate);
    if (!empty($from_date)) $this->db->where('m_payment_date >=', $from_date);
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_payment_tbl.m_payment_method', 'left');
    $this->db->select('m_payment_id as id,m_payment_amount as tamont,method.m_group_name as method_name,m_payment_voucher as recipt_no,m_payment_date as date,m_payment_remark as note,"2" as type')
      ->where('m_payment_account', 4)->where('m_payment_supplier', $account_name);
    if ($branch !== null) $this->db->where('m_payment_branch', $branch);
    $payquery = $this->db->get('master_payment_tbl')->result();
    foreach ($payquery as $krrey) {
      $total_balance -= $krrey->tamont;
      $sql1[] = $krrey;
    }

    $names = array_column((array)$sql1, 'date');
    array_multisort($names, SORT_ASC, $sql1);
    return ($balance == 1) ? $total_balance : $sql1;
  }

  public function expense_leger($from_date, $todate, $account_name, $balance = '', $branch_id = null)
  {
    $branch        = $this->branch_id($branch_id);
    $opening_bal   = $this->Master_model->get_edit_group($account_name);
    $total_balance = $opening_bal->m_group_opening;
    $sql1 = [];

    if (!empty($from_date)) $this->db->where('m_exp_date >=', $from_date);
    if (!empty($todate))    $this->db->where('m_exp_date <=', $todate);
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_expenses_tbl.m_exp_user', 'left');
    $this->db->select('m_exp_id as id,m_exp_amount as tamont,m_exp_method as method_id,m_exp_accno as method_name,m_exp_voucher as recipt_no,m_exp_date as date,m_exp_remark as note,"" as csname,mut.m_user_name as user,"1" as type')
      ->where('m_exp_name', $account_name);
    if ($branch !== null) $this->db->where('m_exp_branch', $branch);
    $cashquery = $this->db->get('master_expenses_tbl')->result();
    foreach ($cashquery as $krey) {
      $total_balance += $krey->tamont;
      $sql1[] = $krey;
    }

    if (!empty($todate))    $this->db->where('m_payment_date <=', $todate);
    if (!empty($from_date)) $this->db->where('m_payment_date >=', $from_date);
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_payment_tbl.m_payment_user', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_payment_tbl.m_payment_method', 'left');
    $this->db->select('m_payment_id as id,m_payment_amount as tamont,m_payment_method as method_id,method.m_group_name as method_name,m_payment_voucher as recipt_no,m_payment_date as date,m_payment_remark as note,mgt.m_group_name as csname,mut.m_user_name as user,"2" as type')
      ->where('m_payment_account', 2)->where('m_payment_supplier', $account_name);
    if ($branch !== null) $this->db->where('m_payment_branch', $branch);
    $payquery = $this->db->get('master_payment_tbl')->result();
    foreach ($payquery as $krrey) {
      $total_balance -= $krrey->tamont;
      $sql1[] = $krrey;
    }

    if (!empty($todate))    $this->db->where('m_recvd_date <=', $todate);
    if (!empty($from_date)) $this->db->where('m_recvd_date >=', $from_date);
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_recieved_tbl.m_recvd_user', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_recieved_tbl.m_recvd_method', 'left');
    $this->db->select('m_recvd_id as id,m_recvd_amount as tamont,m_recvd_method as method_id,method.m_group_name as method_name,m_recvd_voucher as recipt_no,m_recvd_date as date,m_recvd_remark as note,mgt.m_group_name as csname,mut.m_user_name as user,"1" as type')
      ->where('m_recvd_account', 5)->where('m_recvd_customer', $account_name);
    if ($branch !== null) $this->db->where('master_recieved_tbl.m_recvd_branch', $branch);
    $payquery2 = $this->db->get('master_recieved_tbl')->result();
    foreach ($payquery2 as $krrey) {
      $total_balance += $krrey->tamont;
      $sql1[] = $krrey;
    }

    if (!empty($todate))    $this->db->where('m_voucher_date <=', $todate);
    if (!empty($from_date)) $this->db->where('m_voucher_date >=', $from_date);
    $this->db->select('m_voucher_id as id,m_voucher_amount as tamont,m_voucher_type as method_id,"" as method_name,m_voucher_id as recipt_no,m_voucher_date as date,m_voucher_remark as note,"" as csname,"" as user,"3" as type,"" as city')
      ->where('(m_voucher_account = 3)')->where('m_voucher_accountid', $account_name);
    if ($branch !== null) $this->db->where('m_voucher_branch', $branch);
    $voucherquery = $this->db->get('master_voucher_tbl')->result();
    foreach ($voucherquery as $krey) {
      if ($krey->type == 1) $total_balance += $krey->tamont;
      else                  $total_balance -= $krey->tamont;
      $sql1[] = $krey;
    }

    $names = array_column((array)$sql1, 'date');
    array_multisort($names, SORT_ASC, $sql1);
    return ($balance == 1) ? $total_balance : $sql1;
  }

  public function voucher_leger($type, $from_date, $todate, $balance = '', $branch_id = null)
  {
    $branch        = $this->branch_id($branch_id);
    $total_balance = 0;
    $sql1          = [];

    if (!empty($todate))    $this->db->where('m_voucher_date <=', $todate);
    if (!empty($from_date)) $this->db->where('m_voucher_date >=', $from_date);
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_voucher_tbl.m_voucher_accountid', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_voucher_tbl.m_voucher_accountid', 'left');
    $this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_voucher_tbl.m_voucher_accountid', 'left');
    $this->db->select('m_voucher_id as id,m_voucher_amount as tamont,m_voucher_type as type,m_voucher_id as recipt_no,m_voucher_date as date,m_voucher_remark as note,(CASE WHEN m_voucher_account = 3 THEN mgt.m_group_name WHEN m_voucher_account = 1 THEN mct.m_cust_name ELSE mut.m_user_name END) as csname,(CASE WHEN m_voucher_account = 3 THEN mgt.m_group_number WHEN m_voucher_account = 1 THEN mct.m_cust_mobile ELSE mut.m_user_mobile END) as csmobile')
      ->where('m_voucher_type', $type);
    if ($branch !== null) $this->db->where('m_voucher_branch', $branch);
    $voucherquery = $this->db->order_by('m_voucher_date')->get('master_voucher_tbl')->result();
    foreach ($voucherquery as $krey) {
      if ($krey->type == 1) $total_balance += $krey->tamont;
      else                  $total_balance -= $krey->tamont;
      $sql1[] = $krey;
    }
    return ($balance == 1) ? $total_balance : $sql1;
  }

  // ========================== Sales reports ==========================//

  public function sales_item_group($from_date, $todate, $agent)
  {
    $branch = $this->branch_id();
    if (!empty($from_date)) $this->db->where('m_sale_date>=', $from_date);
    if (!empty($todate))    $this->db->where('m_sale_date<=', $todate);
    if (!empty($agent))     $this->db->where_in('m_sale_user', $agent);
    if ($branch !== null) $this->db->where('master_sales_tbl.m_sale_branch', $branch);
    $this->db->select('sum(m_sale_qty) as tqty,sum(m_sale_weight) as twght,m_item_name,sum(m_sale_total) as total_amount,unit.m_itgrp_title,m_user_name');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
      ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left')
      ->join('master_users_tbl', 'master_users_tbl.m_user_id = master_sales_tbl.m_sale_user', 'left');
    $this->db->order_by('m_item_name')->group_by('m_sale_item');
    return $this->db->get('master_sales_tbl')->result();
  }

  public function lotwise_sales_list($lotno, $item_id)
  {
    $branch = $this->branch_id();
    $this->db->select('master_sales_tbl.*,m_item_name,m_cust_name,m_cust_mobile')
      ->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
      ->join('master_customer_tbl mut', 'mut.m_cust_id = master_sales_tbl.m_sale_customer', 'left')
      ->where('m_sale_lot', $lotno)->where('m_sale_item', $item_id);
    if ($branch !== null) $this->db->where('m_sale_branch', $branch);
    return $this->db->order_by('m_item_name')->order_by('m_sale_date')
      ->get('master_sales_tbl')->result();
  }

  public function purchase_sales_list($spono, $type)
  {
    $branch    = $this->branch_id();
    $result    = [];
    $purdetail = $this->Main_model->get_edit_purchase($spono);

    if (!empty($purdetail)) {
      foreach ($purdetail as $purdtl) {
        $this->db->select('master_sales_tbl.*,m_item_name,m_cust_name,m_cust_mobile')
          ->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
          ->join('master_customer_tbl mut', 'mut.m_cust_id = master_sales_tbl.m_sale_customer', 'left')
          ->where('m_sale_lot', $purdtl->m_purcs_id)->where('m_sale_item', $purdtl->m_purcs_item);
        if ($branch !== null) $this->db->where('m_sale_branch', $branch);
        $saledetail = $this->db->order_by('m_item_name')->order_by('m_sale_date')
          ->get('master_sales_tbl')->result();
        foreach ($saledetail as $sldtl) $result[] = $sldtl;
      }
    }
    return ($type == 1) ? $purdetail : $result;
  }

  /**
   * A pre-escaped "col IN (...)" fragment, to be passed with $escape = FALSE.
   *
   * where_in() with a few thousand values builds a condition string long enough
   * that CI's identifier-protection regex in DB_query_builder::_compile_wh()
   * exceeds PCRE's compiled-pattern limit and logs "regular expression is too
   * large". Passing escape = FALSE short-circuits that path entirely, so the
   * values are escaped here instead.
   */
  private function in_clause($column, array $values, $numeric = false)
  {
    $values = array_values(array_unique($values));
    if (empty($values)) {
      return '1=0';
    }
    if ($numeric) {
      $vals = array_map('intval', $values);
    } else {
      $vals = array_map(array($this->db, 'escape'), $values);
    }
    return $column . ' IN (' . implode(',', $vals) . ')';
  }

  public function get_truck_report($fromdate, $todate, $supplier = '')
  {
    $branch       = $this->branch_id();
    $result       = [];
    $all_purchase = $this->Main_model->purchase_group($fromdate, $todate, $supplier);

    if (!empty($all_purchase)) {
      // Everything the loop needs, prefetched in four grouped queries. This
      // used to run two queries per purchase plus one per purchase line and one
      // per linked sale bill, which on the unbounded default range ran past
      // max_execution_time and returned a 500.
      $spos = array_values(array_unique(array_column((array) $all_purchase, 'm_purcs_spo')));

      // purchase lines, keyed by SPO
      $this->db->select('m_purcs_spo, m_purcs_id, m_purcs_item')->where($this->in_clause('m_purcs_spo', $spos), null, false);
      if ($branch !== null) $this->db->where('m_purcs_branch', $branch);
      $lines_by_spo = [];
      $lot_ids = [];
      foreach ($this->db->get('master_purchase_tbl')->result() as $r) {
        $lines_by_spo[$r->m_purcs_spo][] = $r;
        $lot_ids[] = $r->m_purcs_id;
      }

      // internal expenses, keyed by SPO
      $this->db->select('master_expenses_tbl.*, expense.m_group_name as expense_name')
        ->join('master_group_tbl as expense', 'expense.m_group_id = master_expenses_tbl.m_exp_name', 'left')
        ->where($this->in_clause('m_exp_accno', $spos), null, false);
      if ($branch !== null) $this->db->where('m_exp_branch', $branch);
      $exp_by_spo = [];
      foreach ($this->db->get('master_expenses_tbl')->result() as $r) {
        $exp_by_spo[$r->m_exp_accno][] = $r;
      }

      // sale totals per (lot, item)
      $sale_by_lot = [];
      if (!empty($lot_ids)) {
        $this->db->select('m_sale_lot, m_sale_item, sum(m_sale_qty) as saleqty, sum(m_sale_weight) as saleweight, sum(m_sale_total) as saletotal, Group_CONCAT(m_sale_spo) as m_sale_spo')
          ->where($this->in_clause('m_sale_lot', $lot_ids, true), null, false);
        if ($branch !== null) $this->db->where('m_sale_branch', $branch);
        foreach ($this->db->group_by('m_sale_lot, m_sale_item')->get('master_sales_tbl')->result() as $r) {
          $sale_by_lot[$r->m_sale_lot . '|' . $r->m_sale_item] = $r;
        }
      }

      // per-bill sale expenses, for every sale bill those lots fed
      $exp_by_sale_spo = [];
      $sale_spos = [];
      foreach ($sale_by_lot as $r) {
        foreach (explode(',', (string) $r->m_sale_spo) as $s) {
          if ($s !== '') $sale_spos[$s] = true;
        }
      }
      if (!empty($sale_spos)) {
        $this->db->select('m_sale_spo, m_sale_comm, m_sale_fright, m_sale_hamali, m_sale_others, (m_sale_comm+m_sale_fright+m_sale_hamali+m_sale_others) as saleexp')
          ->where($this->in_clause('m_sale_spo', array_keys($sale_spos)), null, false);
        if ($branch !== null) $this->db->where('m_sale_branch', $branch);
        foreach ($this->db->group_by('m_sale_spo')->get('master_sales_tbl')->result() as $r) {
          $exp_by_sale_spo[$r->m_sale_spo] = $r;
        }
      }

      foreach ($all_purchase as $cau) {
        $purchase_detail = $lines_by_spo[$cau->m_purcs_spo] ?? [];
        $internal_exp    = $exp_by_spo[$cau->m_purcs_spo] ?? [];
        $Tsaleqty = $Tsaleweight = $Tsaletotal = $sale_comm = $sale_fright = $sale_hamali = $sale_others = $saleexp = 0;
        $sal_spo  = [];

        foreach ($purchase_detail as $key) {
          $sale_datail = $sale_by_lot[$key->m_purcs_id . '|' . $key->m_purcs_item] ?? null;
          if ($sale_datail) {
            $sal_spo[]    = $sale_datail->m_sale_spo;
            $Tsaleqty    += $sale_datail->saleqty;
            $Tsaleweight += $sale_datail->saleweight;
            $Tsaletotal  += $sale_datail->saletotal;
          }
        }

        $sale_spo_uni = array_unique(explode(',', implode(',', $sal_spo)));
        foreach ($sale_spo_uni as $kry) {
          $sale_expense = $exp_by_sale_spo[$kry] ?? null;
          if ($sale_expense) {
            $sale_comm   += $sale_expense->m_sale_comm;
            $sale_fright += $sale_expense->m_sale_fright;
            $sale_hamali += $sale_expense->m_sale_hamali;
            $sale_others += $sale_expense->m_sale_others;
            $saleexp     += $sale_expense->saleexp;
          }
        }

        $result[] = [
          "Pdate"            => $cau->m_purcs_date,
          "Challan_no"       => $cau->m_purcs_spo,
          "truck_no"         => $cau->m_purcs_truckno,
          "Supplier_name"    => $cau->supplier_name,
          "pur_qty"          => $cau->tqty,
          "pur_amount"       => $cau->total_amount,
          "pur_weight"       => $cau->twght,
          "pur_netamount"    => ($cau->total_expense + $cau->total_amount),
          "pur_comm"         => $cau->m_purcs_comm,
          "pur_fright"       => $cau->m_purcs_fright,
          "pur_hamali"       => $cau->m_purcs_hamali,
          "pur_charity"      => $cau->m_purcs_charity,
          "pur_packaging"    => $cau->m_purcs_packaging,
          "pur_loading"      => $cau->m_purcs_loading,
          "pur_advance"      => $cau->m_purcs_advance,
          "pur_others"       => $cau->m_purcs_others,
          "sale_qty"         => $Tsaleqty,
          "sale_weight"      => $Tsaleweight,
          "sale_amount"      => $Tsaletotal,
          "sale_netamount"   => $Tsaletotal,
          "m_sale_comm"      => $sale_comm,
          "m_sale_fright"    => $sale_fright,
          "m_sale_hamali"    => $sale_hamali,
          "m_sale_others"    => $sale_others,
          "saleexp"          => $saleexp,
          "internal_expense" => $internal_exp,
        ];
      }
    }
    return $result;
  }

  // ========================== Staff performance ==========================//

  public function get_staff_performance_new_report($from_date, $to_date, $staff_id, $staff_group, $report_type)
  {
    $branch    = $this->branch_id();
    $cust_list = $this->Main_model->get_cust_list(null, null, null, null, $staff_group);
    if (empty($cust_list)) return ['items' => '', 'data' => []];

    $cust_ids = array_column((array)$cust_list, 'm_cust_id');

    switch ($report_type) {
      case 1:
        [$main_result, $name_field] = $this->_query_sales_bulk($from_date, $to_date, $staff_id, $cust_ids, $branch);
        break;
      case 2:
        [$main_result, $name_field] = $this->_query_cash_bulk($from_date, $to_date, $staff_id, $cust_ids, $branch);
        break;
      default:
        [$main_result, $name_field] = $this->_query_crate_bulk($from_date, $to_date, $staff_id, $cust_ids, $branch);
        break;
    }

    $all_names = [];
    foreach ($main_result as $row) {
      if (!empty($row->$name_field)) {
        foreach (explode(',', $row->$name_field) as $name) {
          $name = trim($name);
          if ($name !== '') $all_names[$name] = true;
        }
      }
    }
    return ['items' => empty($all_names) ? '' : array_keys($all_names), 'data' => $main_result];
  }

  private function _query_sales_bulk($from_date, $to_date, $staff_id, array $cust_ids, $branch)
  {
    if (!empty($from_date)) $this->db->where('m_sale_date >=', $from_date);
    if (!empty($to_date))   $this->db->where('m_sale_date <=', $to_date);
    $this->db->select('m_sale_spo, m_sale_trackno, SUM(m_sale_qty) AS total_qty, SUM(m_sale_total) AS sub_total, m_sale_date, SUM(m_sale_weight) AS total_weight, SUM(m_sale_crate) AS total_crate, m_sale_comrate, m_sale_comm, m_sale_fright, m_sale_hamali, m_sale_others, (m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) AS total_expense, m_sale_note, m_sale_user, m_sale_customer, GROUP_CONCAT(m_sale_qty) AS sale_qty, GROUP_CONCAT(m_sale_price) AS sale_price, GROUP_CONCAT(m_sale_total) AS sale_total, GROUP_CONCAT(m_sale_weight) AS sale_weight, GROUP_CONCAT(m_sale_crate) AS sale_crate, GROUP_CONCAT(m_item_name) AS sale_itemname, GROUP_CONCAT(crate.m_itgrp_title) AS sale_cratetype, GROUP_CONCAT(unit.m_itgrp_title) AS sale_unitname, m_cust_name, m_cust_id, m_cust_mobile')
      ->join('master_customer_tbl mut', 'mut.m_cust_id = master_sales_tbl.m_sale_customer', 'left')
      ->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
      ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left')
      ->where('m_sale_user', $staff_id)
      ->where_in('m_sale_customer', $cust_ids);
    if ($branch !== null) $this->db->where('master_sales_tbl.m_sale_branch', $branch);
    $result = $this->db->order_by('m_sale_date')->group_by('m_sale_spo')
      ->get('master_sales_tbl')->result();
    return [$result, 'sale_itemname'];
  }

  private function _query_crate_bulk($from_date, $to_date, $staff_id, array $cust_ids, $branch)
  {
    $this->db->select('SUM(m_recvd_qty) AS tqty, m_recvd_voucher, m_recvd_date, m_recvd_remark, GROUP_CONCAT(m_recvd_crate) AS crate_id, GROUP_CONCAT(crate.m_itgrp_title) AS crate_name, GROUP_CONCAT(m_recvd_qty) AS crate_qty, m_cust_name, m_cust_id, m_cust_mobile')
      ->join('master_customer_tbl mut', 'mut.m_cust_id = master_recieved_tbl.m_recvd_customer', 'left')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left')
      ->where('m_recvd_date >=', $from_date)->where('m_recvd_date <=', $to_date)
      ->where('m_recvd_type', 2)->where('m_recvd_user', $staff_id)
      ->where_in('m_recvd_customer', $cust_ids);
    if ($branch !== null) $this->db->where('m_recvd_branch', $branch);
    $result = $this->db->group_by('m_recvd_voucher')->order_by('m_recvd_date')
      ->get('master_recieved_tbl')->result();
    return [$result, 'crate_name'];
  }

  private function _query_cash_bulk($from_date, $to_date, $staff_id, array $cust_ids, $branch)
  {
    $this->db->select('m_recvd_amount, m_recvd_method, m_group_name AS method_name, m_recvd_voucher, m_recvd_date, m_recvd_remark, m_cust_name, m_cust_id, m_cust_mobile')
      ->join('master_customer_tbl mut', 'mut.m_cust_id = master_recieved_tbl.m_recvd_customer', 'left')
      ->join('master_group_tbl method', 'method.m_group_id = master_recieved_tbl.m_recvd_method', 'left')
      ->where('m_recvd_date >=', $from_date)->where('m_recvd_date <=', $to_date)
      ->where('m_recvd_user', $staff_id)->where('m_recvd_account', 1)->where('m_recvd_type', 1)
      ->where_in('m_recvd_customer', $cust_ids);
    if ($branch !== null) $this->db->where('m_recvd_branch', $branch);
    $result = $this->db->order_by('m_recvd_date')->get('master_recieved_tbl')->result();
    return [$result, 'method_name'];
  }

  public function get_staff_daily_customer_report($staff_id, $date)
  {
    $branch = $this->branch_id();
    $this->db->select('m_user_group')->where('m_user_id', $staff_id);
    if ($branch !== null) $this->db->where('m_user_branch', $branch);
    $staff = $this->db->get('master_users_tbl')->row();
    if (empty($staff)) return ['crate_types' => [], 'grand' => [], 'data' => []];

    $cust_list = $this->Main_model->get_cust_list(null, null, null, null, $staff->m_user_group);
    if (empty($cust_list)) return ['crate_types' => [], 'grand' => [], 'data' => []];
    $cust_ids = array_column((array)$cust_list, 'm_cust_id');

    $this->db->select('m_sale_customer, m_cust_name, SUM(m_sale_total) AS sale_total, SUM(m_sale_fright) AS sale_fright')
      ->join('master_customer_tbl mut', 'mut.m_cust_id = master_sales_tbl.m_sale_customer', 'left')
      ->where('m_sale_date', $date)->where('m_sale_user', $staff_id)
      ->where_in('m_sale_customer', $cust_ids);
    if ($branch !== null) $this->db->where('m_sale_branch', $branch);
    $sales = $this->db->group_by('m_sale_customer')->get('master_sales_tbl')->result();

    $this->db->select('m_recvd_customer, SUM(m_recvd_amount) AS cash_received')
      ->where('m_recvd_date', $date)->where('m_recvd_user', $staff_id)
      ->where('m_recvd_account', 1)->where('m_recvd_type', 1)
      ->where_in('m_recvd_customer', $cust_ids);
    if ($branch !== null) $this->db->where('m_recvd_branch', $branch);
    $cash = $this->db->group_by('m_recvd_customer')->get('master_recieved_tbl')->result();

    $this->db->select('m_recvd_customer, crate.m_itgrp_title AS crate_type, SUM(m_recvd_qty) AS crate_qty')
      ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left')
      ->where('m_recvd_date', $date)->where('m_recvd_type', 2)->where('m_recvd_user', $staff_id)
      ->where_in('m_recvd_customer', $cust_ids);
    if ($branch !== null) $this->db->where('m_recvd_branch', $branch);
    $crates = $this->db->group_by('m_recvd_customer, m_recvd_crate')->get('master_recieved_tbl')->result();

    $sales_map = [];
    foreach ($sales as $row) $sales_map[$row->m_sale_customer] = $row;
    $cash_map  = [];
    foreach ($cash as $row)  $cash_map[$row->m_recvd_customer] = (float)$row->cash_received;
    $crate_map = [];
    $all_crate_types = [];
    foreach ($crates as $row) {
      $type = trim($row->crate_type);
      $crate_map[$row->m_recvd_customer][$type] = (float)$row->crate_qty;
      $all_crate_types[$type] = true;
    }
    $all_crate_types = array_keys($all_crate_types);
    sort($all_crate_types);

    $active_cust_ids = array_unique(array_merge(array_keys($sales_map), array_keys($cash_map), array_keys($crate_map)));
    if (empty($active_cust_ids)) return ['crate_types' => [], 'grand' => [], 'data' => []];

    $data  = [];
    $grand = [
      'sale_total'    => 0,
      'cash_received' => 0,
      'net_balance'   => 0,
      'crate_totals'  => array_fill_keys($all_crate_types, 0),
      'total_crates'  => 0,
    ];
    $cust_map2 = [];
    foreach ($cust_list as $cust) $cust_map2[$cust->m_cust_id] = $cust;

    foreach ($active_cust_ids as $cid) {
      $cust          = $cust_map2[$cid] ?? null;
      $sale_row      = $sales_map[$cid] ?? null;
      $old_balance   = $cust ? (float)$cust->m_cust_balance : 0;
      $sale_total    = $sale_row ? (float)$sale_row->sale_total  : 0;
      $sale_fright   = $sale_row ? (float)$sale_row->sale_fright : 0;
      $cash_received = $cash_map[$cid] ?? 0;
      $crate_row     = $crate_map[$cid] ?? [];
      $total_crate   = array_sum($crate_row);
      $row_total     = $old_balance + $sale_total + $sale_fright;
      $net_balance   = $row_total - $cash_received;

      $grand['sale_total']    += ($sale_total + $sale_fright);
      $grand['cash_received'] += $cash_received;
      $grand['net_balance']   += $net_balance;
      $grand['total_crates']  += $total_crate;
      foreach ($all_crate_types as $ct) $grand['crate_totals'][$ct] += $crate_row[$ct] ?? 0;

      $data[] = (object)[
        'cust_id'       => $cid,
        'cust_name'     => $cust->m_cust_name ?? ($sale_row->m_cust_name ?? ''),
        'old_balance'   => $old_balance,
        'sale_total'    => $sale_total,
        'has_fright'    => $sale_fright > 0,
        'sale_fright'   => $sale_fright,
        'row_total'     => $row_total,
        'cash_received' => $cash_received,
        'net_balance'   => $net_balance,
        'crates'        => $crate_row,
        'total_crates'  => $total_crate,
      ];
    }
    return ['crate_types' => $all_crate_types, 'grand' => $grand, 'data' => $data];
  }

  // ========================== Dashboard ==========================//

  public function dashboard_staff_summary($date)
  {
    $branch    = $this->branch_id();
    $result    = [];
    $group_lst = $this->Master_model->get_all_active_group(1);

    $this->db->select('sum(m_cust_balance) as amount_bnl')->where('m_cust_group', 0);
    if ($branch !== null) $this->db->where('m_cust_branch', $branch);
    $cust_outst = $this->db->get('master_customer_tbl')->row();

    $branchUsers = $this->branch_users();
    if ($this->is_superadmin() && !empty($branchUsers)) {
      $branchUserIds = array_column($branchUsers, 'm_user_id');
      $this->db->where_in('m_sale_added_by', $branchUserIds);
    } else {
      $this->db->where('m_sale_added_by', $this->session->userdata('user_id'));
    }
    $this->db->select("SUM(m_sale_qty) AS total_qty,SUM(m_sale_total) AS sub_total,SUM(m_sale_crate) AS total_crate,SUM(m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) AS total_expense");
    $this->db->where('m_sale_date', $date)->where('m_sale_user', 0);
    if ($branch !== null) $this->db->where('m_sale_branch', $branch);
    $sale_detail = $this->db->get('master_sales_tbl')->row();

    $this->db->select('sum(m_recvd_qty) as tqty')
      ->where('m_recvd_date', $date)->where('m_recvd_type', 2)->where('m_recvd_added_by', 1)->where('m_recvd_user', 0);
    if ($branch !== null) $this->db->where('m_recvd_branch', $branch);
    $cratequery = $this->db->get('master_recieved_tbl')->row();

    $this->db->select('sum(m_recvd_amount) as total_recieved')
      ->where('m_recvd_date', $date)->where('m_recvd_added_by', 1)->where('m_recvd_user', 0)
      ->where('m_recvd_account', 1)->where('m_recvd_type', 1);
    if ($branch !== null) $this->db->where('m_recvd_branch', $branch);
    $cashquery = $this->db->get('master_recieved_tbl')->row();

    $result[] = (object)[
      'user_id'          => '',
      'user_group'       => 'o',
      'group_name'       => 'Other',
      'staff_name'       => 'Admin',
      'cash_outstanding' => $cust_outst->amount_bnl,
      'issue_qty'        => 0,
      'issue_amt'        => 0,
      'issue_crate'      => 0,
      'sale_qty'         => $sale_detail->total_qty,
      'sale_amt'         => $sale_detail->sub_total,
      'sale_crate'       => $sale_detail->total_crate,
      'return_qty'       => 0,
      'crate_recieved'   => $cratequery->tqty,
      'cash_collected'   => $cashquery->total_recieved,
    ];

    foreach ($group_lst as $grp) {
      $this->db->select('sum(m_cust_balance) as amount_bnl')->where('m_cust_group', $grp->m_group_id);
      if ($branch !== null) $this->db->where('m_cust_branch', $branch);
      $cash_outstan = $this->db->get('master_customer_tbl')->row();

      $this->db->select('m_user_id,m_user_name,m_user_mobile,m_user_contractPerd,m_user_group')
        ->where("FIND_IN_SET('$grp->m_group_id', m_user_group)");
      if ($branch !== null) $this->db->where('m_user_branch', $branch);
      $staff_detail = $this->db->get('master_users_tbl')->row();

      if (!empty($staff_detail)) {
        $this->db->select('sum(si_issue_qty) as total_qty,sum(si_issue_total) as sub_total,sum(si_issue_crate) as total_crate')
          ->where('si_issue_date', $date)->where('si_issue_type', 1)->where('si_issue_user', $staff_detail->m_user_id);
        if ($branch !== null) $this->db->where('si_issue_branch', $branch);
        $issue_datil = $this->db->get('staff_itemissue_tbl')->result();

        $this->db->select('sum(si_issue_qty) as total_qty,sum(si_issue_total) as sub_total,sum(si_issue_crate) as total_crate')
          ->where('si_issue_date', $date)->where('si_issue_type', 2)->where('si_issue_user', $staff_detail->m_user_id);
        if ($branch !== null) $this->db->where('si_issue_branch', $branch);
        $return_datil = $this->db->get('staff_itemissue_tbl')->result();

        $this->db->select('sum(m_sale_qty) as total_qty,sum(m_sale_total) as sub_total,sum(m_sale_crate) as total_crate,(m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) as total_expense')
          ->where('m_sale_date', $date)->where('m_sale_user', $staff_detail->m_user_id);
        if ($branch !== null) $this->db->where('m_sale_branch', $branch);
        $sale_datil2 = $this->db->get('master_sales_tbl')->result();

        $this->db->select('sum(m_recvd_qty) as tqty')
          ->where('m_recvd_date', $date)->where('m_recvd_type', 2)->where('m_recvd_user', $staff_detail->m_user_id);
        if ($branch !== null) $this->db->where('m_recvd_branch', $branch);
        $cratequery2 = $this->db->get('master_recieved_tbl')->result();

        $this->db->select('sum(m_recvd_amount) as total_recieved')
          ->where('m_recvd_date', $date)->where('m_recvd_user', $staff_detail->m_user_id)
          ->where('m_recvd_account', 1)->where('m_recvd_type', 1);
        if ($branch !== null) $this->db->where('m_recvd_branch', $branch);
        $cashquery2 = $this->db->get('master_recieved_tbl')->result();

        $result[] = (object)[
          'user_id'          => $staff_detail->m_user_id,
          'user_group'       => $staff_detail->m_user_group,
          'group_name'       => $staff_detail->m_user_name,
          'staff_name'       => $staff_detail->m_user_contractPerd,
          'cash_outstanding' => $cash_outstan->amount_bnl,
          'issue_qty'        => $issue_datil[0]->total_qty,
          'issue_amt'        => $issue_datil[0]->sub_total,
          'issue_crate'      => $issue_datil[0]->total_crate,
          'sale_qty'         => $sale_datil2[0]->total_qty,
          'sale_amt'         => $sale_datil2[0]->sub_total,
          'sale_crate'       => $sale_datil2[0]->total_crate,
          'return_qty'       => $return_datil[0]->total_qty,
          'crate_recieved'   => $cratequery2[0]->tqty,
          'cash_collected'   => $cashquery2[0]->total_recieved,
        ];
      }
    }
    return $result;
  }

  public function get_cust_CashBal($cust_id, $from_date, $opening_bal)
  {
    $branch    = $this->branch_id();
    $sub_total = $total_expense = $grand_total = 0;

    if (!empty($from_date)) $this->db->where('m_sale_date <=', $from_date);
    $this->db->select('sum(m_sale_total) as sub_total,(m_sale_comm+m_sale_fright+m_sale_hamali+m_sale_others) as texpense')
      ->where('m_sale_customer', $cust_id);
    if ($branch !== null) $this->db->where('m_sale_branch', $branch);
    $salequery = $this->db->group_by('m_sale_spo')->get('master_sales_tbl')->result();
    foreach ($salequery as $key) {
      $sub_total     += $key->sub_total;
      $total_expense += $key->texpense;
      $grand_total   += ($key->sub_total + $key->texpense);
    }

    if (!empty($from_date)) $this->db->where('m_recvd_date <=', $from_date);
    $this->db->select('sum(m_recvd_amount) as tamountrcvd')
      ->where('m_recvd_customer', $cust_id)->where('m_recvd_account', 1)->where('m_recvd_type', 1);
    if ($branch !== null) $this->db->where('m_recvd_branch', $branch);
    $amountrcvdquery = $this->db->get('master_recieved_tbl')->result();

    if (!empty($from_date)) $this->db->where('m_voucher_date <=', $from_date);
    $this->db->select('sum(m_voucher_amount) as tamountcdt')
      ->where('m_voucher_accountid', $cust_id)->where('m_voucher_account', 1)->where('m_voucher_type', 1)
      ->where('m_voucher_status', 1);
    if ($branch !== null) $this->db->where('m_voucher_branch', $branch);
    $vouch_amtcdrt = $this->db->get('master_voucher_tbl')->result();

    if (!empty($from_date)) $this->db->where('m_voucher_date <=', $from_date);
    $this->db->select('sum(m_voucher_amount) as tamountdbt')
      ->where('m_voucher_accountid', $cust_id)->where('m_voucher_account', 1)->where('m_voucher_type', 2)
      ->where('m_voucher_status', 1);
    if ($branch !== null) $this->db->where('m_voucher_branch', $branch);
    $vouch_amtdbt = $this->db->get('master_voucher_tbl')->result();

    return [
      "sub_total"      => $sub_total,
      "total_expense"  => $total_expense,
      "grand_total"    => $grand_total,
      "amount_rcvd"    => $amountrcvdquery[0]->tamountrcvd ?: 0,
      "balance_amount" => $opening_bal
        + (($grand_total + $vouch_amtdbt[0]->tamountdbt)
          - ($amountrcvdquery[0]->tamountrcvd + $vouch_amtcdrt[0]->tamountcdt)),
    ];
  }

  public function get_cust_CrateBal($cust_id, $from_date, $opening_bal)
  {
    $crate_total    = $total_given = $total_recieved = 0;
    $all_crates     = $this->Master_model->all_itemgroup(3);
    $openin_crate_bal = explode(',', $opening_bal);
    $result = [];

    foreach ($all_crates as $key) {
      $crateledger     = $this->Main_model->get_crate_ledger($key->m_itgrp_id, $cust_id, $from_date);
      $crate_total    += ((int)$crateledger['crate_given'] - (int)$crateledger['crate_rcvd']);
      $total_given    += (int)$crateledger['crate_given'];
      $total_recieved += (int)$crateledger['crate_rcvd'];
      if ($key->m_itgrp_title == '10 KG') {
        $crattype_bal = $openin_crate_bal[0] ?? 0;
      } elseif ($key->m_itgrp_title == '20 KG') {
        $crattype_bal = $openin_crate_bal[1] ?? 0;
      } elseif ($key->m_itgrp_title == '25 KG') {
        $crattype_bal = $openin_crate_bal[2] ?? 0;
      } else {
        $crattype_bal = 0;
      }
      $result['crateitems'][] = [
        'name'    => $key->m_itgrp_title,
        'recived' => (int)$crateledger['crate_rcvd'],
        'given'   => (int)$crateledger['crate_given'],
        'balance' => ((int)$crattype_bal + (int)$crateledger['crate_given'] - (int)$crateledger['crate_rcvd']),
      ];
    }
    $result['crate_given']    = $total_given;
    $result['crate_recieved'] = $total_recieved;
    $result['balance_crate']  = array_sum(explode(',', $opening_bal)) + $crate_total;
    return $result;
  }

  public function transfer_ledger_data($from_date, $todate, $branch_id = null)
  {
    $this->db->select('
        mp.m_purcs_id,
        mp.m_purcs_spo,
        mp.m_purcs_date,
        mp.m_purcs_qty,
        mp.m_purcs_weight,
        mp.m_purcs_price as issue_rate,
        mp.m_purcs_total,
        mp.m_purcs_lot,
        mp.m_purcs_branch as to_branch,
        mp.m_purcs_from_branch as from_branch,
        mp.m_purcs_ref_lot,
        mp.m_purcs_added_by,
        mp.m_purcs_added_on,
        mit.m_item_name,
        tobr.m_user_name as to_branch_name,
        frombr.m_user_name as from_branch_name,
        addedby.m_user_name as added_by_name
    ');
    $this->db->from('master_purchase_tbl mp');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = mp.m_purcs_item', 'left');
    $this->db->join('master_users_tbl tobr', 'tobr.m_user_id = mp.m_purcs_branch', 'left');
    $this->db->join('master_users_tbl frombr', 'frombr.m_user_id = mp.m_purcs_from_branch', 'left');
    $this->db->join('master_users_tbl addedby', 'addedby.m_user_id = mp.m_purcs_added_by', 'left');

    $this->db->where('mp.m_purcs_type', 2); // sirf transfer records

    if (!empty($from_date)) $this->db->where('mp.m_purcs_date >=', $from_date);
    if (!empty($todate))    $this->db->where('mp.m_purcs_date <=', $todate);

    // Super admin (branch_id null) => sab dikhega
    // Branch user => uski branch se related dono taraf (bheja gaya + mila hua)
    if (!empty($branch_id)) {
      $this->db->group_start();
      $this->db->where('mp.m_purcs_branch', $branch_id);
      $this->db->or_where('mp.m_purcs_from_branch', $branch_id);
      $this->db->group_end();
    }

    $this->db->order_by('mp.m_purcs_date', 'desc');
    $this->db->order_by('mp.m_purcs_id', 'desc');

    return $this->db->get()->result();
  }

  // ===================== Branch Ledger / Outstanding =======================//

  public function get_branch_opening_balance($branch_id, $before_date)
  {
    $bill_total = $this->db->select_sum('m_purcs_total')
      ->where('m_purcs_branch', $branch_id)
      ->where('m_purcs_type', 2)
      ->where('m_purcs_date <', $before_date)
      ->get('master_purchase_tbl')->row();

    $paid_total = $this->db->select_sum('m_recvd_amount')
      ->where('m_recvd_customer', $branch_id)
      ->where('m_recvd_account', 8)
      ->where('m_recvd_type', 1)
      ->where('m_recvd_date <', $before_date)
      ->get('master_recieved_tbl')->row();

    $bills = !empty($bill_total->m_purcs_total) ? (float) $bill_total->m_purcs_total : 0;
    $paid  = !empty($paid_total->m_recvd_amount) ? (float) $paid_total->m_recvd_amount : 0;

    return $bills - $paid;
  }

  public function branch_ledger_bills($branch_id, $from_date = '', $todate = '')
  {
    $this->db->select('m_purcs_spo, m_purcs_date, SUM(m_purcs_qty) as tqty, SUM(m_purcs_total) as tamount')
      ->where('m_purcs_branch', $branch_id)
      ->where('m_purcs_type', 2);
    if (!empty($from_date)) $this->db->where('m_purcs_date >=', $from_date);
    if (!empty($todate))    $this->db->where('m_purcs_date <=', $todate);
    $this->db->group_by('m_purcs_spo');
    $this->db->order_by('m_purcs_date');
    return $this->db->get('master_purchase_tbl')->result();
  }

  public function branch_ledger_payments($branch_id, $from_date = '', $todate = '')
  {
    $this->db->select('m_recvd_voucher, m_recvd_date, m_recvd_amount, m_recvd_remark')
      ->where('m_recvd_customer', $branch_id)
      ->where('m_recvd_account', 8)
      ->where('m_recvd_type', 1);
    if (!empty($from_date)) $this->db->where('m_recvd_date >=', $from_date);
    if (!empty($todate))    $this->db->where('m_recvd_date <=', $todate);
    $this->db->order_by('m_recvd_date');
    return $this->db->get('master_recieved_tbl')->result();
  }

  public function branch_outstanding_list()
  {
    $this->db->select('m_user_id, m_user_name, m_user_mobile, m_user_balance');
    $this->db->where('m_user_type', 9);
    $this->db->order_by('m_user_name');
    return $this->db->get('master_users_tbl')->result();
  }

  // ===================== Branch Ledger / Outstanding =======================//

  public function dashboard_counts($date)
  {
    $branch       = $this->branch_id();
    $this->db->select('sum(m_cust_balance) as amount_bnl');
    if ($branch !== null) $this->db->where('m_cust_branch', $branch);
    $cust_lst = $this->db->get('master_customer_tbl')->row();

    $this->db->select('sum(m_user_balance) as sp_outstd')->where('m_user_type', 2);
    if ($branch !== null) $this->db->where('m_user_branch', $branch);
    $suppiler_lst = $this->db->get('master_users_tbl')->row();

    $this->db->select('m_group_id,m_group_name,m_group_type,m_group_opening');
    if ($branch !== null) $this->db->where('m_group_branch', $branch);
    $this->db->where('m_group_type', 3)->or_where('m_group_type', 4);
    $accounts_lst = $this->db->get('master_group_tbl')->result();

    $account_dels = [];
    foreach ($accounts_lst as $cbact) {
      $pagetype    = ($cbact->m_group_type == 3) ? 2 : 1;
      $opening_bal = $this->cash_bank_balance($pagetype, date('Y-m-d', strtotime($date . '-1day')), $cbact->m_group_id, $cbact->m_group_opening);
      $account_dels[] = (object)[
        'acct_id'    => $cbact->m_group_id,
        'acct_name'  => $cbact->m_group_name,
        'opening_bal' => IND_money_format(round($opening_bal, 2)),
      ];
    }

    return (object)[
      'spcash_outstan' => IND_money_format($suppiler_lst->sp_outstd),
      'cash_outstan'   => IND_money_format($cust_lst->amount_bnl),
      'account_dels'   => $account_dels,
    ];
  }

  public function get_piechart_data($date)
  {
    $branch = $this->branch_id();
    $this->db->select('m_group_id,m_group_name,m_group_type,m_group_opening');
    if ($branch !== null) $this->db->where('m_group_branch', $branch);
    $this->db->where('m_group_type', 3)->or_where('m_group_type', 4);
    $accounts_lst = $this->db->get('master_group_tbl')->result();
    $data = [];
    foreach ($accounts_lst as $cbact) {
      $pagetype    = ($cbact->m_group_type == 3) ? 2 : 1;
      $opening_bal = $this->cash_bank_balance($pagetype, date('Y-m-d', strtotime($date . '-1day')), $cbact->m_group_id, $cbact->m_group_opening);
      $clos_bal    = $this->cash_bank_balance($pagetype, $date, $cbact->m_group_id, $cbact->m_group_opening);
      $data['label'][] = $cbact->m_group_name;
      $data['data'][]  = round($clos_bal, 2);
      $data['today'][] = round(($clos_bal - $opening_bal), 2);
    }
    return json_encode($data);
  }

  // ========================== Utilities ==========================//

  function unique_multidimensional_array($array, $key)
  {
    $temp_array = [];
    $i          = 0;
    $key_array  = [];
    foreach ($array as $val) {
      if (!in_array($val[$key], $key_array)) {
        $key_array[$i] = $val[$key];
        $temp_array[$i] = $val;
      }
      $i++;
    }
    return $temp_array;
  }
}
