<?php date_default_timezone_set('Asia/Kolkata');

class Report_model extends CI_model
{


  //==========================Stock List===========================//

  // public function get_purchase_item($from = '', $to = '', $item = '', $lot_id = "", $lot = '', $bal = '')
  // {
  //   $this->db->select('sum(m_purcs_qty) as itemqty,m_purcs_id,m_purcs_lot,m_purcs_spo,m_purcs_item,m_purcs_price,m_purcs_date,m_purcs_weight,m_item_name,m_item_price,m_item_fright,crate.m_itgrp_title as cratetype,m_purcs_user,unit.m_itgrp_title as unitname,supp.m_user_trademark');
  //   $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_purchase_tbl.m_purcs_item', 'left')
  //     ->join('master_users_tbl as supp', 'supp.m_user_id = master_purchase_tbl.m_purcs_suplier', 'left')
  //     ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
  //     ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
  //     ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');
  //   if ($bal != 1) {
  //     $this->db->where('m_purcs_available >', 0);
  //   }
  //   if (!empty($item)) {
  //     $this->db->where('m_purcs_item', $item);
  //   }
  //   if (!empty($from)) {
  //     $this->db->where('m_purcs_date >=', $from);
  //   }
  //   if (!empty($to)) {
  //     $this->db->where('m_purcs_date <=', $to);
  //   }
  //   if (!empty($lot_id)) {
  //     $this->db->where('m_purcs_id', $lot_id);
  //   }
  //   $this->db->order_by('m_item_name');
  //   $this->db->order_by('m_purcs_date', 'desc');
  //   if ($lot == 1) {
  //     $this->db->group_by('m_purcs_id');
  //   }
  //   $this->db->group_by('m_purcs_item');

  //   return $this->db->get('master_purchase_tbl')->result();
  // }

  // public function get_issue_stock($type, $from = '', $to = '', $item = '', $lot = '')
  // {
  //   $this->db->select('sum(si_issue_qty) as itemqty,si_issue_item,si_issue_price,si_issue_date,si_issue_weight,m_item_name,crate.m_itgrp_title as cratetype,si_issue_user,unit.m_itgrp_title as unitname');
  //   $this->db->join('master_item_tbl mit', 'mit.m_item_id = staff_itemissue_tbl.si_issue_item', 'left')
  //     ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
  //     ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
  //     ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');

  //   $this->db->where('si_issue_type', $type);
  //   if (!empty($item)) {
  //     $this->db->where('si_issue_item', $item);
  //   }
  //   if (!empty($from)) {
  //     $this->db->where('si_issue_date >=', $from);
  //   }
  //   if (!empty($to)) {
  //     $this->db->where('si_issue_date <=', $to);
  //   }
  //   if (!empty($lot)) {
  //     $this->db->where('si_issue_lotno', $lot);
  //   }
  //   $this->db->where('si_issue_status', 1);
  //   $this->db->order_by('m_item_name');
  //   $this->db->order_by('si_issue_date', 'desc');
  //   $this->db->group_by('si_issue_item');

  //   return $this->db->get('staff_itemissue_tbl')->result();
  // }

  // public function get_admin_sale($from = '', $to = '', $item = '', $lot = '')
  // {
  //   $this->db->select('sum(m_sale_qty) as itemqty,m_sale_item,m_sale_price,m_sale_date,m_sale_weight,m_item_name,crate.m_itgrp_title as cratetype,m_sale_user,unit.m_itgrp_title as unitname');
  //   $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
  //     ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
  //     ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
  //     ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');
  //   $this->db->where('m_sale_added_by', 1);
  //   if (!empty($item)) {
  //     $this->db->where('m_sale_item', $item);
  //   }
  //   if (!empty($from)) {
  //     $this->db->where('m_sale_date >=', $from);
  //   }
  //   if (!empty($to)) {
  //     $this->db->where('m_sale_date <=', $to);
  //   }
  //   if (!empty($lot)) {
  //     $this->db->where('m_sale_lot', $lot);
  //   }
  //   $this->db->order_by('m_item_name');
  //   $this->db->order_by('m_sale_date', 'desc');
  //   $this->db->group_by('m_sale_item');

  //   return $this->db->get('master_sales_tbl')->result();
  // }

  // public function get_lotwise_item($to = '', $item = '', $uniq = '')
  // {
  //   $result = array();

  //   $item_purchase = $this->get_purchase_item(null, $to, $item, null, 1);

  //   if (!empty($item_purchase)) {

  //     foreach ($item_purchase as $key) {
  //       $item_purchase_open = $this->get_purchase_item(null, date('Y-m-d', strtotime($to . '-1day')), $key->m_purcs_item, $key->m_purcs_id);
  //       $item_issue_open = $this->get_issue_stock(1, null, date('Y-m-d', strtotime($to . '-1day')), $key->m_purcs_item, $key->m_purcs_id);
  //       $item_return_open = $this->get_issue_stock(2, null, date('Y-m-d', strtotime($to . '-1day')), $key->m_purcs_item, $key->m_purcs_id);
  //       $item_sale_open = $this->get_admin_sale(null, date('Y-m-d', strtotime($to . '-1day')), $key->m_purcs_item, $key->m_purcs_id);
  //       $item_issue_count = $this->get_issue_stock(1, null, $to, $key->m_purcs_item, $key->m_purcs_id);
  //       $item_return_count = $this->get_issue_stock(2, null, $to, $key->m_purcs_item, $key->m_purcs_id);
  //       $item_sale_count = $this->get_admin_sale(null, $to, $key->m_purcs_item, $key->m_purcs_id);

  //       if (isset($item_purchase_open[0])) {
  //         $open_pur_weight = $item_purchase_open[0]->m_purcs_weight;
  //         $open_pur_qty = $item_purchase_open[0]->itemqty;
  //       } else {
  //         $open_pur_weight = 0;
  //         $open_pur_qty = 0;
  //       }

  //       if (isset($item_issue_open[0])) {
  //         $open_issue_weight = $item_issue_open[0]->si_issue_weight;
  //         $open_issue_qty = $item_issue_open[0]->itemqty;
  //       } else {
  //         $open_issue_weight = 0;
  //         $open_issue_qty = 0;
  //       }

  //       if (isset($item_return_open[0])) {
  //         $open_retun_weight = $item_return_open[0]->si_issue_weight;
  //         $open_retun_qty = $item_return_open[0]->itemqty;
  //       } else {
  //         $open_retun_weight = 0;
  //         $open_retun_qty = 0;
  //       }

  //       if (isset($item_sale_open[0])) {
  //         $open_sale_weight = $item_sale_open[0]->m_sale_weight;
  //         $open_sale_qty = $item_sale_open[0]->itemqty;
  //       } else {
  //         $open_sale_weight = 0;
  //         $open_sale_qty = 0;
  //       }

  //       $open_balance_weight = ($open_pur_weight + $open_retun_weight - $open_issue_weight - $open_sale_weight);
  //       $open_balance_qty = ($open_pur_qty + $open_retun_qty - $open_issue_qty - $open_sale_qty);


  //       $pur_weight = $key->m_purcs_weight;
  //       $pur_qty = $key->itemqty;


  //       if (isset($item_issue_count[0])) {
  //         $issue_weight = $item_issue_count[0]->si_issue_weight;
  //         $issue_qty = $item_issue_count[0]->itemqty;
  //       } else {
  //         $issue_weight = 0;
  //         $issue_qty = 0;
  //       }

  //       if (isset($item_return_count[0])) {
  //         $retun_weight = $item_return_count[0]->si_issue_weight;
  //         $retun_qty = $item_return_count[0]->itemqty;
  //       } else {
  //         $retun_weight = 0;
  //         $retun_qty = 0;
  //       }

  //       if (isset($item_sale_count[0])) {
  //         $sale_weight = $item_sale_count[0]->m_sale_weight;
  //         $sale_qty = $item_sale_count[0]->itemqty;
  //       } else {
  //         $sale_weight = 0;
  //         $sale_qty = 0;
  //       }

  //       $balance_weight = ($pur_weight + $retun_weight - $issue_weight - $sale_weight);
  //       $balance_qty = ($pur_qty + $retun_qty - $issue_qty - $sale_qty);

  //       $this->db->set('m_purcs_available', $balance_qty)->where('m_purcs_id', $key->m_purcs_id)->update('master_purchase_tbl');

  //       $res = array(
  //         "m_item_id" => $key->m_purcs_item,
  //         "m_item_name" => $key->m_item_name,
  //         "m_item_price" => $key->m_item_price,
  //         "m_item_fright" => $key->m_item_fright,
  //         "m_purcs_spo" => $key->m_purcs_spo,
  //         "m_purcs_date" => date('d/m', strtotime($key->m_purcs_date)),
  //         "m_user_trademark" => $key->m_user_trademark,
  //         "m_purcs_qty" => $key->itemqty,
  //         "cratetype" => $key->cratetype ?: '',
  //         "unitname" => $key->unitname ?: '',
  //         "m_purcs_id" => $key->m_purcs_id ?: '',
  //         "m_purcs_lot" => $key->m_purcs_lot ?: '',
  //         "balance_weight" => $balance_weight,
  //         "balance_qty" => $balance_qty,
  //         "opening_qty" => $open_balance_qty == 0 ? $key->itemqty : $open_balance_qty,
  //         "closing_qty" => $balance_qty,

  //       );
  //       if ($balance_qty > 0) {
  //         $result[] = $res;
  //       }
  //     }
  //   }

  //   if ($uniq == 1) {
  //     return $this->unique_multidimensional_array($result, 'm_item_id');
  //   } else {
  //     return $result;
  //   }
  // }

  // public function get_lotwise_item2($to = '', $item = '', $uniq = '')
  // {
  //   $result = array();

  //   $item_purchase = $this->get_purchase_item(null, $to, $item, null, 1, 1);

  //   if (!empty($item_purchase)) {

  //     foreach ($item_purchase as $key) {
  //       $item_purchase_open = $this->get_purchase_item(null, date('Y-m-d', strtotime($to . '-1day')), $key->m_purcs_item, $key->m_purcs_id);
  //       $item_issue_open = $this->get_issue_stock(1, null, date('Y-m-d', strtotime($to . '-1day')), $key->m_purcs_item, $key->m_purcs_id);
  //       $item_return_open = $this->get_issue_stock(2, null, date('Y-m-d', strtotime($to . '-1day')), $key->m_purcs_item, $key->m_purcs_id);
  //       $item_sale_open = $this->get_admin_sale(null, date('Y-m-d', strtotime($to . '-1day')), $key->m_purcs_item, $key->m_purcs_id);
  //       $item_issue_count = $this->get_issue_stock(1, null, $to, $key->m_purcs_item, $key->m_purcs_id);
  //       $item_return_count = $this->get_issue_stock(2, null, $to, $key->m_purcs_item, $key->m_purcs_id);
  //       $item_sale_count = $this->get_admin_sale(null, $to, $key->m_purcs_item, $key->m_purcs_id);

  //       if (isset($item_purchase_open[0])) {
  //         $open_pur_weight = $item_purchase_open[0]->m_purcs_weight;
  //         $open_pur_qty = $item_purchase_open[0]->itemqty;
  //       } else {
  //         $open_pur_weight = 0;
  //         $open_pur_qty = 0;
  //       }

  //       if (isset($item_issue_open[0])) {
  //         $open_issue_weight = $item_issue_open[0]->si_issue_weight;
  //         $open_issue_qty = $item_issue_open[0]->itemqty;
  //       } else {
  //         $open_issue_weight = 0;
  //         $open_issue_qty = 0;
  //       }

  //       if (isset($item_return_open[0])) {
  //         $open_retun_weight = $item_return_open[0]->si_issue_weight;
  //         $open_retun_qty = $item_return_open[0]->itemqty;
  //       } else {
  //         $open_retun_weight = 0;
  //         $open_retun_qty = 0;
  //       }

  //       if (isset($item_sale_open[0])) {
  //         $open_sale_weight = $item_sale_open[0]->m_sale_weight;
  //         $open_sale_qty = $item_sale_open[0]->itemqty;
  //       } else {
  //         $open_sale_weight = 0;
  //         $open_sale_qty = 0;
  //       }

  //       $open_balance_weight = ($open_pur_weight + $open_retun_weight - $open_issue_weight - $open_sale_weight);
  //       $open_balance_qty = ($open_pur_qty + $open_retun_qty - $open_issue_qty - $open_sale_qty);


  //       $pur_weight = $key->m_purcs_weight;
  //       $pur_qty = $key->itemqty;


  //       if (isset($item_issue_count[0])) {
  //         $issue_weight = $item_issue_count[0]->si_issue_weight;
  //         $issue_qty = $item_issue_count[0]->itemqty;
  //       } else {
  //         $issue_weight = 0;
  //         $issue_qty = 0;
  //       }

  //       if (isset($item_return_count[0])) {
  //         $retun_weight = $item_return_count[0]->si_issue_weight;
  //         $retun_qty = $item_return_count[0]->itemqty;
  //       } else {
  //         $retun_weight = 0;
  //         $retun_qty = 0;
  //       }

  //       if (isset($item_sale_count[0])) {
  //         $sale_weight = $item_sale_count[0]->m_sale_weight;
  //         $sale_qty = $item_sale_count[0]->itemqty;
  //       } else {
  //         $sale_weight = 0;
  //         $sale_qty = 0;
  //       }

  //       $balance_weight = ($pur_weight + $retun_weight - $issue_weight - $sale_weight);
  //       $balance_qty = ($pur_qty + $retun_qty - $issue_qty - $sale_qty);

  //       $this->db->set('m_purcs_available', $balance_qty)->where('m_purcs_id', $key->m_purcs_id)->update('master_purchase_tbl');

  //       $res = array(
  //         "m_item_id" => $key->m_purcs_item,
  //         "m_item_name" => $key->m_item_name,
  //         "m_item_price" => $key->m_item_price,
  //         "m_item_fright" => $key->m_item_fright,
  //         "m_purcs_spo" => $key->m_purcs_spo,
  //         "m_purcs_date" => date('d/m', strtotime($key->m_purcs_date)),
  //         "m_user_trademark" => $key->m_user_trademark,
  //         "m_purcs_qty" => $key->itemqty,
  //         "cratetype" => $key->cratetype ?: '',
  //         "unitname" => $key->unitname ?: '',
  //         "m_purcs_id" => $key->m_purcs_id ?: '',
  //         "m_purcs_lot" => $key->m_purcs_lot ?: '',
  //         "balance_weight" => $balance_weight,
  //         "balance_qty" => $balance_qty,
  //         "opening_qty" => $open_balance_qty == 0 ? $key->itemqty : $open_balance_qty,
  //         "closing_qty" => $balance_qty,

  //       );
  //       if ($balance_qty > 0) {
  //         $result[] = $res;
  //       }
  //     }
  //   }

  //   if ($uniq == 1) {
  //     return $this->unique_multidimensional_array($result, 'm_item_id');
  //   } else {
  //     return $result;
  //   }
  // }


  public function get_lotwise_item($to = '', $item = '', $uniq = '')
  {
    $result = [];
    $open_date = date('Y-m-d', strtotime($to . ' -1 day'));

    // 1️⃣ Get all purchases till $to (lot wise)
    $this->db->select('
        m_purcs_id,
        m_purcs_item,
        m_purcs_lot,
        m_purcs_spo,
        m_purcs_date,
        m_purcs_weight,
        SUM(m_purcs_qty) as pur_qty,
        mit.m_item_name,
        mit.m_item_price,
        mit.m_item_fright,
        crate.m_itgrp_title as cratetype,
        unit.m_itgrp_title as unitname,
        supp.m_user_trademark
    ');
    $this->db->from('master_purchase_tbl');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_purchase_tbl.m_purcs_item', 'left');
    $this->db->join('master_users_tbl as supp', 'supp.m_user_id = master_purchase_tbl.m_purcs_suplier', 'left');
    $this->db->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left');
    $this->db->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');
    $this->db->where('m_purcs_date <=', $to);

    if (!empty($item)) {
      $this->db->where('m_purcs_item', $item);
    }
    $this->db->order_by('mit.m_item_name');
    $this->db->order_by('m_purcs_date', 'desc');
    $this->db->group_by('m_purcs_id');
    $purchases = $this->db->get()->result();
    if (empty($purchases))
      return [];

    // 2️⃣ Get all issue + return aggregated till $to
    $issue_data = $this->get_issue_aggregate($to);
    $issue_open_data = $this->get_issue_aggregate($open_date);

    // 3️⃣ Get all sales aggregated till $to
    $sale_data = $this->get_sale_aggregate($to);
    $sale_open_data = $this->get_sale_aggregate($open_date);

    foreach ($purchases as $key) {

      $pur_qty = $key->pur_qty;
      $pur_weight = $key->m_purcs_weight;

      $issue_qty = $issue_data[$key->m_purcs_id]['issue_qty'] ?? 0;
      $return_qty = $issue_data[$key->m_purcs_id]['return_qty'] ?? 0;
      $sale_qty = $sale_data[$key->m_purcs_id] ?? 0;

      $open_issue_qty = $issue_open_data[$key->m_purcs_id]['issue_qty'] ?? 0;
      $open_return_qty = $issue_open_data[$key->m_purcs_id]['return_qty'] ?? 0;
      $open_sale_qty = $sale_open_data[$key->m_purcs_id] ?? 0;

      $open_balance_qty = ($pur_qty + $open_return_qty - $open_issue_qty - $open_sale_qty);
      $balance_qty = ($pur_qty + $return_qty - $issue_qty - $sale_qty);

      // update available stock
      $this->db->where('m_purcs_id', $key->m_purcs_id)
        ->update('master_purchase_tbl', ['m_purcs_available' => $balance_qty]);

      if ($balance_qty > 0) {
        $result[] = [
          "m_item_id" => $key->m_purcs_item,
          "m_item_name" => $key->m_item_name,
          "m_item_price" => $key->m_item_price,
          "m_item_fright" => $key->m_item_fright,
          "m_purcs_spo" => $key->m_purcs_spo,
          "m_purcs_date" => date('d/m', strtotime($key->m_purcs_date)),
          "m_user_trademark" => $key->m_user_trademark,
          "m_purcs_qty" => $pur_qty,
          "cratetype" => $key->cratetype ?? '',
          "unitname" => $key->unitname ?? '',
          "m_purcs_id" => $key->m_purcs_id,
          "m_purcs_lot" => $key->m_purcs_lot,
          "balance_weight" => $pur_weight,
          "balance_qty" => $balance_qty,
          "opening_qty" => $open_balance_qty == 0 ? $pur_qty : $open_balance_qty,
          "closing_qty" => $balance_qty,
        ];
      }
    }

    return ($uniq == 1)
      ? $this->unique_multidimensional_array($result, 'm_item_name')
      : $result;
  }

  private function get_issue_aggregate($date)
  {
    $this->db->select("
        si_issue_lotno,
        SUM(CASE WHEN si_issue_type = 1 THEN si_issue_qty ELSE 0 END) as issue_qty,
        SUM(CASE WHEN si_issue_type = 2 THEN si_issue_qty ELSE 0 END) as return_qty
    ");
    $this->db->from('staff_itemissue_tbl');
    $this->db->where('si_issue_status', 1);
    $this->db->where('si_issue_date <=', $date);
    $this->db->group_by('si_issue_lotno');

    $rows = $this->db->get()->result();

    $data = [];
    foreach ($rows as $r) {
      $data[$r->si_issue_lotno] = [
        'issue_qty' => $r->issue_qty,
        'return_qty' => $r->return_qty
      ];
    }
    return $data;
  }

  private function get_sale_aggregate($date)
  {
    $this->db->select("m_sale_lot, SUM(m_sale_qty) as sale_qty");
    $this->db->from('master_sales_tbl');
    $this->db->where('m_sale_added_by', 1);
    $this->db->where('m_sale_date <=', $date);
    $this->db->group_by('m_sale_lot');

    $rows = $this->db->get()->result();

    $data = [];
    foreach ($rows as $r) {
      $data[$r->m_sale_lot] = $r->sale_qty;
    }
    return $data;
  }

  public function get_item_stock_list($from = '', $to = '', $item = '')
  {
    $result = [];

    $all_items = $this->Master_model->get_all_item($item);
    if (empty($all_items))
      return [];

    // 1️⃣ Purchase Aggregate
    $purchase_data = $this->get_purchase_aggregate($from, $to);

    // 2️⃣ Issue Aggregate (type 1)
    $issue_data = $this->get_issue_aggregate_itemwise(1, $from, $to);

    // 3️⃣ Return Aggregate (type 2)
    $return_data = $this->get_issue_aggregate_itemwise(2, $from, $to);

    // 4️⃣ Admin Sale Aggregate
    $sale_data = $this->get_sale_aggregate_itemwise($from, $to);

    foreach ($all_items as $key) {

      $item_id = $key->m_item_id;

      $pur = $purchase_data[$item_id] ?? ['qty' => 0, 'weight' => 0, 'price' => 0, 'date' => null];
      $iss = $issue_data[$item_id] ?? ['qty' => 0, 'weight' => 0];
      $ret = $return_data[$item_id] ?? ['qty' => 0, 'weight' => 0];
      $sale = $sale_data[$item_id] ?? ['qty' => 0, 'weight' => 0];

      $balance_weight = ($pur['weight'] + $ret['weight'] - $iss['weight'] - $sale['weight']);
      $balance_qty = ($pur['qty'] + $ret['qty'] - $iss['qty'] - $sale['qty']);

      $last_updated = $pur['date'] ?? '';

      $result[] = [
        "m_item_id" => $item_id,
        "m_item_name" => $key->m_item_name,
        "m_issue_price" => $pur['price'],
        "cratetype" => $key->cratetype ?? '',
        "unitname" => $key->unitname ?? '',
        "groupname" => $key->groupname ?? '',
        "balance_weight" => $balance_weight,
        "balance_qty" => $balance_qty,
        "last_updated" => $last_updated ? date('d-m-Y', strtotime($last_updated)) : '',
      ];
    }

    return $result;
  }

  private function get_purchase_aggregate($from, $to)
  {
    $this->db->select("
        m_purcs_item,
        SUM(m_purcs_qty) as qty,
        SUM(m_purcs_weight) as weight,
        MAX(m_purcs_price) as price,
        MAX(m_purcs_date) as date
    ");
    $this->db->from('master_purchase_tbl');

    if (!empty($from))
      $this->db->where('m_purcs_date >=', $from);
    if (!empty($to))
      $this->db->where('m_purcs_date <=', $to);

    $this->db->group_by('m_purcs_item');

    $rows = $this->db->get()->result();

    $data = [];
    foreach ($rows as $r) {
      $data[$r->m_purcs_item] = [
        'qty' => $r->qty,
        'weight' => $r->weight,
        'price' => $r->price,
        'date' => $r->date
      ];
    }
    return $data;
  }

  private function get_issue_aggregate_itemwise($type, $from, $to)
  {
    $this->db->select("
        si_issue_item,
        SUM(si_issue_qty) as qty,
        SUM(si_issue_weight) as weight
    ");
    $this->db->from('staff_itemissue_tbl');
    $this->db->where('si_issue_type', $type);
    $this->db->where('si_issue_status', 1);

    if (!empty($from))
      $this->db->where('si_issue_date >=', $from);
    if (!empty($to))
      $this->db->where('si_issue_date <=', $to);

    $this->db->group_by('si_issue_item');

    $rows = $this->db->get()->result();

    $data = [];
    foreach ($rows as $r) {
      $data[$r->si_issue_item] = [
        'qty' => $r->qty,
        'weight' => $r->weight
      ];
    }
    return $data;
  }
  private function get_sale_aggregate_itemwise($from, $to)
  {
    $this->db->select("
        m_sale_item,
        SUM(m_sale_qty) as qty,
        SUM(m_sale_weight) as weight
    ");
    $this->db->from('master_sales_tbl');
    $this->db->where('m_sale_added_by', 1);

    if (!empty($from))
      $this->db->where('m_sale_date >=', $from);
    if (!empty($to))
      $this->db->where('m_sale_date <=', $to);

    $this->db->group_by('m_sale_item');

    $rows = $this->db->get()->result();

    $data = [];
    foreach ($rows as $r) {
      $data[$r->m_sale_item] = [
        'qty' => $r->qty,
        'weight' => $r->weight
      ];
    }
    return $data;
  }

  // public function get_issue_itemsale($issue_spo)
  // {

  //   $issued_item = $this->db->select('si_issue_qty,si_issue_item,si_issue_lotno,si_issue_date,si_issue_weight,si_issue_user')->where('si_issue_spo', $issue_spo)->where('si_issue_type', 1)->where('si_issue_status', 1)->get('staff_itemissue_tbl')->result();

  //   if (!empty($issued_item)) {
  //     foreach ($issued_item as $key) {

  //       $item_return_count = $this->db->select('sum(si_issue_qty) as itemqty,si_issue_item')->where('si_issue_lotno', $key->si_issue_lotno)->where('si_issue_item', $key->si_issue_item)->where('si_issue_user', $key->si_issue_user)->where('si_issue_type', 2)->where('si_issue_date', $key->si_issue_date)->where('si_issue_status', 1)->group_by('si_issue_item')->get('staff_itemissue_tbl')->result();

  //       $item_sale_count = $this->db->select('sum(m_sale_qty) as itemqty,m_sale_item')->where('m_sale_lot', $key->si_issue_lotno)->where('m_sale_item', $key->si_issue_item)->where('m_sale_user', $key->si_issue_user)->where('m_sale_date', $key->si_issue_date)->group_by('m_sale_item')->get('master_sales_tbl')->result();

  //       if (isset($item_return_count[0])) {
  //         $retun_qty = $item_return_count[0]->itemqty;
  //       } else {
  //         $retun_qty = 0;
  //       }

  //       if (isset($item_sale_count[0])) {
  //         $sale_qty = $item_sale_count[0]->itemqty;
  //       } else {
  //         $sale_qty = 0;
  //       }

  //       $balance_qty = ($key->si_issue_qty - $sale_qty - $retun_qty);
  //       // echo '#'; print_r($balance_qty);
  //       if ($balance_qty > 0) {
  //         return 2;
  //       } else {
  //         return 3;
  //       }
  //     }
  //   } else {
  //     $res = 4;
  //   }

  //   return $res;
  // }

  public function get_issue_itemsale($issue_spo = '', $issue_id = '')
  {

    if (!empty($issue_spo)) {
      $this->db->where('si_issue_spo', $issue_spo);
    }
    if (!empty($issue_id)) {
      $this->db->where('si_issue_id', $issue_id);
    }

    $issued_item = $this->db->select('si_issue_qty,si_issue_item,si_issue_lotno,si_issue_date,si_issue_weight,si_issue_user')->where('si_issue_type', 1)->where('si_issue_status', 1)->get('staff_itemissue_tbl')->result();

    // if (!empty($issued_item)) {
    //   return [
    //     'status' => 4,
    //     'total_sale_qty' => 0,
    //     'total_sale_amount' => 0,
    //     'total_balance_qty' => 0
    //   ];
    // }

    $total_sale_qty = 0;
    $total_sale_amount = 0;
    $total_balance_qty = 0;
    if (!empty($issued_item)) {
      foreach ($issued_item as $item) {

        $return_row = $this->db
          ->select('SUM(si_issue_qty) AS qty')
          ->where('si_issue_lotno', $item->si_issue_lotno)
          ->where('si_issue_item', $item->si_issue_item)
          ->where('si_issue_user', $item->si_issue_user)
          ->where('si_issue_type', 2)
          ->where('si_issue_date', $item->si_issue_date)
          ->where('si_issue_status', 1)
          ->get('staff_itemissue_tbl')
          ->row();

        $return_qty = (float) ($return_row->qty ?? 0);

        $sale_row = $this->db
          ->select('
                SUM(m_sale_qty) AS qty,
                SUM(m_sale_total + m_sale_fright) AS amount
            ')
          ->where('m_sale_lot', $item->si_issue_lotno)
          ->where('m_sale_item', $item->si_issue_item)
          ->where('m_sale_user', $item->si_issue_user)
          ->where('m_sale_date', $item->si_issue_date)
          ->get('master_sales_tbl')
          ->row();

        $sale_qty = (float) ($sale_row->qty ?? 0);
        $sale_amt = (float) ($sale_row->amount ?? 0);

        $balance_qty = (float) $item->si_issue_qty - $sale_qty - $return_qty;

        $total_sale_qty += $sale_qty;
        $total_sale_amount += $sale_amt;
        $total_balance_qty += $balance_qty;
      }

      return [
        'status' => ($total_balance_qty > 0) ? 2 : 3,
        'total_sale_qty' => $total_sale_qty,
        'total_sale_amount' => $total_sale_amount,
        'total_balance_qty' => $total_balance_qty
      ];

    }
  }

  public function get_customer_amount_ledger($cust_id)
  {
    $result = array();
    $salequery = $this->db->select('(sum(m_sale_total) + m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) as Tamount,m_sale_date,m_sale_note,m_user_name,m_user_mobile,1 as type')
      ->join('master_users_tbl mut', 'mut.m_user_id = master_sales_tbl.m_sale_user', 'left')->where('m_sale_customer', $cust_id)->group_by('m_sale_spo')->get('master_sales_tbl')->result();

    if (!empty($salequery)) {
      foreach ($salequery as $key) {
        $result[] = $key;
      }
    }

    $amountrcvdquery = $this->db->select('m_recvd_amount as Tamount,m_recvd_date as m_sale_date,m_recvd_remark as m_sale_note,m_user_name,m_user_mobile,2 as type')
      ->where('m_recvd_customer', $cust_id)->join('master_users_tbl mut', 'mut.m_user_id = master_recieved_tbl.m_recvd_user', 'left')->where('m_recvd_account', 1)->where('m_recvd_type', 1)->get('master_recieved_tbl')->result();
    if (!empty($amountrcvdquery)) {
      foreach ($amountrcvdquery as $keey) {
        $result[] = $keey;
      }
    }

    usort($result, function ($a, $b) {
      return strcmp($a->m_sale_date, $b->m_sale_date);
    });

    return $result;
  }

  public function get_customer_crate_ledger($cust_id)
  {
    $result = array();
    $salequery = $this->db->select('sum(m_sale_crate) as tcrateqty,m_itgrp_title,m_sale_date,m_sale_note,m_user_name,m_user_mobile,1 as type')->join('master_users_tbl mut', 'mut.m_user_id = master_sales_tbl.m_sale_user', 'left')->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item')->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate')->where('m_sale_customer', $cust_id)->group_by('m_sale_spo')->group_by('m_item_crate')->get('master_sales_tbl')->result();


    if (!empty($salequery)) {
      foreach ($salequery as $key) {
        $result[] = $key;
      }
    }

    $amountrcvdquery = $this->db->select('m_recvd_qty as tcrateqty,m_itgrp_title,m_recvd_date as m_sale_date,m_recvd_remark as m_sale_note,m_user_name,m_user_mobile,2 as type')->where('m_recvd_customer', $cust_id)->join('master_users_tbl mut', 'mut.m_user_id = master_recieved_tbl.m_recvd_user', 'left')->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left')->where('m_recvd_type', 2)->get('master_recieved_tbl')->result();
    if (!empty($amountrcvdquery)) {
      foreach ($amountrcvdquery as $keey) {
        $result[] = $keey;
      }
    }

    usort($result, function ($a, $b) {
      return strcmp($a->m_sale_date, $b->m_sale_date);
    });

    return $result;
  }

  public function customer_detailed_leger($from_date, $todate, $customers)
  {
    $sql1 = array();
    $this->db->where('m_sale_date >=', $from_date);
    $this->db->where('m_sale_date <=', $todate);
    $salequery = $this->db->select('m_sale_spo,m_sale_date,sum(m_sale_qty) as tqty,sum(m_sale_crate) as tcrate,sum(m_sale_total) as sub_total,(m_sale_comm+m_sale_fright+m_sale_hamali+m_sale_others) as texpense,m_sale_note')->where('m_sale_customer', $customers)->group_by('m_sale_spo')->get('master_sales_tbl')->result();

    if (!empty($salequery)) {
      foreach ($salequery as $key) {
        $this->db->select('m_sale_spo,m_sale_qty,m_sale_price,m_sale_total,m_sale_date,m_sale_weight,m_sale_crate,m_item_name,(CASE WHEN group.m_itgrp_title IS NULL THEN "" ELSE group.m_itgrp_title END ) AS groupname,(CASE WHEN crate.m_itgrp_title IS NULL THEN "" ELSE crate.m_itgrp_title END ) AS cratetype,(CASE WHEN unit.m_itgrp_title IS NULL THEN "" ELSE unit.m_itgrp_title END ) AS unitname');
        $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
          ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
          ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
          ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');
        $sale_items = $this->db->where('m_sale_spo', $key->m_sale_spo)->get('master_sales_tbl')->result();

        $res = array(
          'date' => $key->m_sale_date,
          'recipt_no' => $key->m_sale_spo,
          'particular' => $sale_items,
          'debited' => $key->sub_total + $key->texpense,
          'expense' => $key->texpense,
          'note' => $key->m_sale_note,
          'total_qty' => $key->tqty,
          'total_crate' => $key->tcrate,
          'type' => 3,
        );
        $sql1[] = $res;
      }
    }

    $this->db->where('m_recvd_date >=', $from_date);
    $this->db->where('m_recvd_date <=', $todate);
    $cratequery = $this->db->select('sum(m_recvd_qty) as tqty,m_recvd_voucher,m_recvd_date,m_recvd_remark')->where('m_recvd_type', 2)->where('m_recvd_customer', $customers)->group_by('m_recvd_voucher')->get('master_recieved_tbl')->result();

    if (!empty($cratequery)) {
      foreach ($cratequery as $keey) {
        $crate_list = $this->db->select('m_recvd_crate as m_crate_id,crate.m_itgrp_title as m_crate_name,m_recvd_qty')->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left')->where('m_recvd_voucher', $keey->m_recvd_voucher)->where('m_recvd_type', 2)->order_by('m_itgrp_id')->get('master_recieved_tbl')->result();

        $rees = array(
          'date' => $keey->m_recvd_date,
          'recipt_no' => $keey->m_recvd_voucher,
          'particular' => $crate_list,
          'debited' => '',
          'expense' => '',
          'note' => $keey->m_recvd_remark,
          'total_qty' => $keey->tqty,
          'total_crate' => '',
          'type' => 2,
        );
        $sql1[] = $rees;
      }
    }

    $this->db->where('m_recvd_date >=', $from_date);
    $this->db->where('m_recvd_date <=', $todate);

    $this->db->join('master_group_tbl method', 'method.m_group_id = master_recieved_tbl.m_recvd_method', 'left');
    $cashquery = $this->db->select('sum(m_recvd_amount) as tamont,m_recvd_method,m_group_name as method_name,m_recvd_voucher,m_recvd_date,m_recvd_remark')->where('m_recvd_account', 1)->where('m_recvd_type', 1)->where('m_recvd_customer', $customers)->group_by('m_recvd_voucher')->get('master_recieved_tbl')->result();

    if (!empty($cashquery)) {
      foreach ($cashquery as $krey) {
        $cash_list = $this->db->select('m_recvd_crate as m_crate_id,m_recvd_method,m_recvd_amount')->where('m_recvd_voucher', $krey->m_recvd_voucher)->where('m_recvd_account', 1)->where('m_recvd_type', 1)->get('master_recieved_tbl')->result();

        $rres = array(
          'date' => $krey->m_recvd_date,
          'recipt_no' => $krey->m_recvd_voucher,
          'particular' => $cash_list,
          'debited' => $krey->tamont,
          'expense' => $krey->method_name,
          'note' => $krey->m_recvd_remark,
          'total_qty' => '',
          'total_crate' => '',
          'type' => 1,
        );
        $sql1[] = $rres;
      }
    }


    $voucherquery = $this->db->select('m_voucher_id,m_voucher_amount as tamont,m_voucher_type,m_voucher_date,m_voucher_remark')->where('m_voucher_date >=', $from_date)->where('m_voucher_date <=', $todate)->where('m_voucher_account', 1)->where('m_voucher_accountid', $customers)->get('master_voucher_tbl')->result();

    if (!empty($voucherquery)) {
      foreach ($voucherquery as $krey) {

        $rres = array(
          'date' => $krey->m_voucher_date,
          'recipt_no' => $krey->m_voucher_id,
          'particular' => '',
          'debited' => $krey->tamont,
          'expense' => $krey->m_voucher_type,
          'note' => $krey->m_voucher_remark,
          'total_qty' => '',
          'total_crate' => '',
          'type' => 4,
        );
        $sql1[] = $rres;
      }
    }

    $names = array();
    foreach ($sql1 as $my_object) {
      $names[] = $my_object['date']; //any object field
    }
    array_multisort($names, SORT_ASC, $sql1);

    // echo '<pre>';
    // print_r($sql1);
    // die;
    return $sql1;
  }


  public function get_sup_crate_ledger($crate_id, $sup_id, $from_date = '', $today = '')
  {

    if ($today == 1) {
      if (!empty($from_date)) {
        $this->db->where('m_purcs_date', $from_date);
      }
    } else {
      if (!empty($from_date)) {
        $this->db->where('m_purcs_date <=', $from_date);
      }
    }


    $crategiven = $this->db->select('sum(m_purcs_crate) as tcrate,m_itgrp_title')->join('master_item_tbl mit', 'mit.m_item_id = master_purchase_tbl.m_purcs_item', 'left')->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')->where('m_purcs_suplier', $sup_id)->where('m_item_crate', $crate_id)->group_by('m_item_crate')->get('master_purchase_tbl')->result();

    // print_r($crategiven); 

    if ($today == 1) {
      if (!empty($from_date)) {
        $this->db->where('m_payment_date', $from_date);
      }
    } else {
      if (!empty($from_date)) {
        $this->db->where('m_payment_date <=', $from_date);
      }
    }


    $cratercvdquery = $this->db->select('sum(m_payment_qty) as tcrateqty,m_itgrp_title')->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_payment_tbl.m_payment_crate', 'left')->where('m_payment_supplier', $sup_id)->where('m_payment_type', 2)->where('m_payment_crate', $crate_id)->group_by('m_payment_crate')->get('master_payment_tbl')->result();
    $result = array(
      "crate_rcvd" => $cratercvdquery ? $cratercvdquery[0]->tcrateqty : 0,
      "crate_given" => $crategiven ? $crategiven[0]->tcrate : 0,
      "crate_balance" => (($crategiven ? $crategiven[0]->tcrate : 0) - ($cratercvdquery ? $cratercvdquery[0]->tcrateqty : 0)),
    );
    return $result;
  }


  public function get_sup_opening_balance($sup_id, $from_date)
  {

    $opening_bal = $this->Main_model->get_user_dtl($sup_id);

    $sub_total = 0;
    $total_expense = 0;
    $grand_total = 0;
    $crate_total = 0;
    $total_given = 0;
    $total_recieved = 0;

    if (!empty($from_date)) {
      $this->db->where('m_purcs_date <=', $from_date);
    }

    //   if(!empty($to_date)){
    //     $this->db->where('m_purcs_date >', $to_date);
    //  }

    $salequery = $this->db->select('sum(m_purcs_qty) as tqty,sum(m_purcs_total) as sub_total,sum(m_purcs_crate) as tcrate,sum(m_purcs_total) as sub_total,(m_purcs_comm + m_purcs_fright + m_purcs_hamali + m_purcs_charity + m_purcs_packaging + m_purcs_loading + m_purcs_advance + m_purcs_others) as texpense')->where('m_purcs_suplier', $sup_id)->group_by('m_purcs_spo')->get('master_purchase_tbl')->result();
    if (!empty($salequery)) {
      foreach ($salequery as $key) {
        $sub_total += $key->sub_total;
        $total_expense += $key->texpense;
        $grand_total += ($key->sub_total + $key->texpense);
      }
    }

    if (!empty($from_date)) {
      $this->db->where('m_payment_date <=', $from_date);
    }
    $amountrcvdquery = $this->db->select('sum(m_payment_amount) as tamountrcvd')->where('m_payment_supplier', $sup_id)->where('m_payment_type', 1)->where('m_payment_account', 1)->get('master_payment_tbl')->result();

    if (!empty($from_date)) {
      $this->db->where('m_recvd_date <=', $from_date);
    }
    $amountpaidquery = $this->db->select('sum(m_recvd_amount) as tamountrcvd')->where('m_recvd_customer', $sup_id)->where('m_recvd_type', 1)->where('m_recvd_account', 4)->get('master_recieved_tbl')->result();


    if (!empty($from_date)) {
      $this->db->where('m_voucher_date <=', $from_date);
    }

    $vouch_amtcdrt = $this->db->select('sum(m_voucher_amount) as tamountcdt')->where('m_voucher_accountid', $sup_id)->where('m_voucher_account', 2)->where('m_voucher_type', 1)->where('m_voucher_status', 1)->get('master_voucher_tbl')->result();
    if (!empty($from_date)) {
      $this->db->where('m_voucher_date <=', $from_date);
    }

    $vouch_amtdbt = $this->db->select('sum(m_voucher_amount) as tamountdbt')->where('m_voucher_accountid', $sup_id)->where('m_voucher_account', 2)->where('m_voucher_type', 2)->where('m_voucher_status', 1)->get('master_voucher_tbl')->result();

    $balance_amt = ($opening_bal->m_user_opening * (-1)) + (($grand_total + $vouch_amtcdrt[0]->tamountcdt) - ($amountrcvdquery[0]->tamountrcvd + $vouch_amtdbt[0]->tamountdbt) + $amountpaidquery[0]->tamountrcvd);


    $result = array(
      "sub_total" => $sub_total,
      "total_expense" => $total_expense,
      "grand_total" => $grand_total,
      "amount_rcvd" => $amountrcvdquery[0]->tamountrcvd ?: 0,
      "amount_paid" => $amountpaidquery[0]->tamountrcvd ?: 0,
      "balance_amount" => $balance_amt,
    );

    $all_crates = $this->Master_model->all_itemgroup(3);
    $openin_crate_bal = explode(',', $opening_bal->m_user_crateOP);

    foreach ($all_crates as $key) {
      $crateledger = $this->get_sup_crate_ledger($key->m_itgrp_id, $sup_id, $from_date);
      $crate_total += ((int) $crateledger['crate_given'] - (int) $crateledger['crate_rcvd']);

      $total_given += (int) $crateledger['crate_given'];
      $total_recieved += (int) $crateledger['crate_rcvd'];

      if ($key->m_itgrp_title == '10 KG') {
        $crattype_bal = isset($openin_crate_bal[0]) ? $openin_crate_bal[0] : 0;
      } else if ($key->m_itgrp_title == '20 KG') {
        $crattype_bal = isset($openin_crate_bal[1]) ? $openin_crate_bal[1] : 0;
      } else if ($key->m_itgrp_title == '25 KG') {
        $crattype_bal = isset($openin_crate_bal[2]) ? $openin_crate_bal[2] : 0;
      }

      $res = array(
        'name' => $key->m_itgrp_title,
        'recived' => (int) $crateledger['crate_rcvd'],
        'given' => (int) $crateledger['crate_given'],
        'balance' => ((int) $crattype_bal + (int) $crateledger['crate_given'] - (int) $crateledger['crate_rcvd']),
      );
      $result['crateitems'][] = $res;
    }

    $result['crate_given'] = $total_given;
    $result['crate_recieved'] = $total_recieved;
    $result['balance_crate'] = array_sum(explode(',', $opening_bal->m_user_crateOP)) + $crate_total;
    // print_r($result['balance_crate']);
    // die ;

    return $result;
  }

  public function supplier_detailed_leger($from_date, $todate, $supplier)
  {
    $sql1 = array();
    $this->db->where('m_purcs_date >=', $from_date);
    $this->db->where('m_purcs_date <=', $todate);
    $salequery = $this->db->select('m_purcs_spo,m_purcs_date,sum(m_purcs_qty) as tqty,sum(m_purcs_crate) as tcrate,sum(m_purcs_total) as sub_total,(m_purcs_comm + m_purcs_fright + m_purcs_hamali + m_purcs_charity + m_purcs_packaging + m_purcs_loading + m_purcs_advance + m_purcs_others) as texpense,m_purcs_note')->where('m_purcs_suplier', $supplier)->group_by('m_purcs_spo')->get('master_purchase_tbl')->result();

    if (!empty($salequery)) {
      foreach ($salequery as $key) {
        $this->db->select('m_purcs_spo,m_purcs_qty,m_purcs_price,m_purcs_total,m_purcs_date,m_purcs_weight,m_purcs_crate,m_item_name,(CASE WHEN group.m_itgrp_title IS NULL THEN "" ELSE group.m_itgrp_title END ) AS groupname,(CASE WHEN crate.m_itgrp_title IS NULL THEN "" ELSE crate.m_itgrp_title END ) AS cratetype,(CASE WHEN unit.m_itgrp_title IS NULL THEN "" ELSE unit.m_itgrp_title END ) AS unitname,m_purcs_comm,m_purcs_comrate,m_purcs_fright,m_purcs_hamali,m_purcs_charity,m_purcs_packaging,m_purcs_loading,m_purcs_advance,m_purcs_others,m_purcs_truckno');
        $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_purchase_tbl.m_purcs_item', 'left')
          ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
          ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
          ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left');
        $sale_items = $this->db->where('m_purcs_spo', $key->m_purcs_spo)->get('master_purchase_tbl')->result();

        $res = array(
          'date' => $key->m_purcs_date,
          'recipt_no' => $key->m_purcs_spo,
          'particular' => $sale_items,
          'debited' => $key->sub_total + $key->texpense,
          'expense' => $key->texpense,
          'note' => $key->m_purcs_note,
          'total_qty' => $key->tqty,
          'total_crate' => $key->tcrate,
          'type' => 3,
        );
        $sql1[] = $res;
      }
    }

    $this->db->where('m_payment_date >=', $from_date);
    $this->db->where('m_payment_date <=', $todate);
    $cratequery = $this->db->select('sum(m_payment_qty) as tqty,m_payment_voucher,m_payment_date,m_payment_remark')->where('m_payment_type', 2)->where('m_payment_supplier', $supplier)->group_by('m_payment_voucher')->get('master_payment_tbl')->result();

    if (!empty($cratequery)) {
      foreach ($cratequery as $keey) {
        $crate_list = $this->db->select('m_payment_crate as m_crate_id,crate.m_itgrp_title as m_crate_name,m_payment_qty')->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_payment_tbl.m_payment_crate', 'left')->where('m_payment_voucher', $keey->m_payment_voucher)->where('m_payment_type', 2)->order_by('m_itgrp_id')->get('master_payment_tbl')->result();

        $rees = array(
          'date' => $keey->m_payment_date,
          'recipt_no' => $keey->m_payment_voucher,
          'particular' => $crate_list,
          'debited' => '',
          'expense' => '',
          'note' => $keey->m_payment_remark,
          'total_qty' => $keey->tqty,
          'total_crate' => '',
          'type' => 2,
        );
        $sql1[] = $rees;
      }
    }

    $this->db->where('m_payment_date >=', $from_date);
    $this->db->where('m_payment_date <=', $todate);

    $this->db->join('master_group_tbl method', 'method.m_group_id = master_payment_tbl.m_payment_method', 'left');
    $cashquery = $this->db->select('sum(m_payment_amount) as tamont,m_payment_method,m_group_name as method_name,m_payment_voucher,m_payment_date,m_payment_remark')->where('m_payment_type', 1)->where('m_payment_account', 1)->where('m_payment_supplier', $supplier)->group_by('m_payment_voucher')->get('master_payment_tbl')->result();

    if (!empty($cashquery)) {
      foreach ($cashquery as $krey) {
        $cash_list = $this->db->select('m_payment_crate as m_crate_id,m_payment_method,m_payment_amount')->where('m_payment_voucher', $krey->m_payment_voucher)->where('m_payment_type', 1)->where('m_payment_account', 1)->get('master_payment_tbl')->result();

        $rres = array(
          'date' => $krey->m_payment_date,
          'recipt_no' => $krey->m_payment_voucher,
          'particular' => $cash_list,
          'debited' => $krey->tamont,
          'expense' => $krey->method_name,
          'note' => $krey->m_payment_remark,
          'total_qty' => 1,
          'total_crate' => '',
          'type' => 1,
        );
        $sql1[] = $rres;
      }
    }

    $this->db->where('m_recvd_date >=', $from_date);
    $this->db->where('m_recvd_date <=', $todate);

    $this->db->join('master_group_tbl method', 'method.m_group_id = master_recieved_tbl.m_recvd_method', 'left');
    $cashquery = $this->db->select('sum(m_recvd_amount) as tamont,m_recvd_method,m_group_name as method_name,m_recvd_voucher,m_recvd_date,m_recvd_remark')->where('m_recvd_type', 1)->where('m_recvd_account', 4)->where('m_recvd_customer', $supplier)->group_by('m_recvd_voucher')->get('master_recieved_tbl')->result();

    if (!empty($cashquery)) {
      foreach ($cashquery as $krey) {
        $cash_list = $this->db->select('m_recvd_crate as m_crate_id,m_recvd_method,m_recvd_amount')->where('m_recvd_voucher', $krey->m_recvd_voucher)->where('m_recvd_type', 1)->where('m_recvd_account', 4)->get('master_recieved_tbl')->result();

        $rres = array(
          'date' => $krey->m_recvd_date,
          'recipt_no' => $krey->m_recvd_voucher,
          'particular' => $cash_list,
          'debited' => $krey->tamont,
          'expense' => $krey->method_name,
          'note' => $krey->m_recvd_remark,
          'total_qty' => 2,
          'total_crate' => '',
          'type' => 1,
        );
        $sql1[] = $rres;
      }
    }

    $voucherquery = $this->db->select('m_voucher_id,m_voucher_amount as tamont,m_voucher_type,m_voucher_date,m_voucher_remark')->where('m_voucher_date >=', $from_date)->where('m_voucher_date <=', $todate)->where('m_voucher_account', 2)->where('m_voucher_accountid', $supplier)->get('master_voucher_tbl')->result();

    if (!empty($voucherquery)) {
      foreach ($voucherquery as $krey) {

        $rres = array(
          'date' => $krey->m_voucher_date,
          'recipt_no' => $krey->m_voucher_id,
          'particular' => '',
          'debited' => $krey->tamont,
          'expense' => $krey->m_voucher_type,
          'note' => $krey->m_voucher_remark,
          'total_qty' => '',
          'total_crate' => '',
          'type' => 4,
        );
        $sql1[] = $rres;
      }
    }


    $names = array();
    foreach ($sql1 as $my_object) {
      $names[] = $my_object['date']; //any object field
    }
    array_multisort($names, SORT_ASC, $sql1);

    // echo '<pre>';
    // print_r($sql1);
    // die;
    return $sql1;
  }

  public function cash_bank_balance($pagetype, $todate, $method, $opening_bal)
  {

    $total_balance = $opening_bal;

    if (!empty($todate)) {
      $this->db->where('m_recvd_date <=', $todate);
    }


    $cashquery = $this->db->select('sum(m_recvd_amount) as tamont')->where('m_recvd_method', $method)->get('master_recieved_tbl')->result();
    if (!empty($cashquery)) {
      $total_balance += $cashquery[0]->tamont;
    }

    if (!empty($todate)) {
      $this->db->where('m_exp_date <=', $todate);
    }

    $expenseQuery = $this->db->select('sum(m_exp_amount) as tamont')->where('m_exp_name', 83)->get('master_expenses_tbl')->result();

    if ($pagetype == 1 && !empty($expenseQuery)) {
      $total_balance -= $expenseQuery[0]->tamont;
    }

    if (!empty($todate)) {
      $this->db->where('m_payment_date <=', $todate);
    }

    if ($pagetype == 2) {
      $this->db->select('(CASE WHEN m_payment_account = 7 && m_payment_method = ' . $method . ' THEN "2" WHEN m_payment_account = 7 THEN "1" ELSE "2" END) as type')->where("CASE WHEN m_payment_account = 7 && m_payment_method = '$method' THEN m_payment_method = '$method' WHEN m_payment_account = 7 THEN m_payment_supplier = '$method' ELSE m_payment_method = '$method' END");
    } else {
      $this->db->select('"2" as type')->where('m_payment_method', $method);
    }

    $payquery = $this->db->select('m_payment_amount as tamont')->get('master_payment_tbl')->result();


    if (!empty($payquery)) {
      foreach ($payquery as $krrey) {
        if ($krrey->type == 2) {
          $total_balance -= $krrey->tamont;
        } else {
          $total_balance += $krrey->tamont;
        }
      }
    }

    return $total_balance;
  }

  public function cash_bank_leger($pagetype, $from_date, $todate, $method, $balance = '')
  {

    $opening_bal = $this->Master_model->get_edit_group($method);

    $total_balance = $opening_bal->m_group_opening;
    $sql1 = array();

    if (!empty($from_date)) {
      $this->db->where('m_exp_date >=', $from_date);
    }

    if (!empty($todate)) {
      $this->db->where('m_exp_date <=', $todate);
    }

    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_expenses_tbl.m_exp_user', 'left');

    $expenseQuery = $this->db->select('m_exp_id as id,m_exp_amount as tamont,"16" as method_id,"Cash" as method_name,m_exp_amount as recipt_no,m_exp_date as date,m_exp_remark as note,"Line Expense" as csname,mut.m_user_name as user,"" as city,"3" as type')->where('m_exp_name', 83)->get('master_expenses_tbl')->result();

    if ($pagetype == 1 && !empty($expenseQuery)) {
      foreach ($expenseQuery as $krey) {
        $total_balance -= $krey->tamont;
        $sql1[] = $krey;
      }
    }

    if (!empty($from_date)) {
      $this->db->where('m_recvd_date >=', $from_date);
    }

    if (!empty($todate)) {
      $this->db->where('m_recvd_date <=', $todate);
    }

    $this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_users_tbl mutt', 'mutt.m_user_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_recieved_tbl.m_recvd_user', 'left');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = mct.m_cust_city', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_recieved_tbl.m_recvd_method', 'left');

    if ($pagetype == 2) {
      $this->db->select('(CASE WHEN m_recvd_account = 7 && m_recvd_method = ' . $method . ' THEN "2" ELSE "1" END) as type')->where("CASE WHEN m_recvd_account = 7 && m_recvd_method = '$method' THEN m_recvd_method = '$method' WHEN m_recvd_account = 7 THEN m_recvd_customer = '$method' ELSE m_recvd_method = '$method' END");
    } else {
      $this->db->select('"1" as type')->where('m_recvd_method', $method);
    }
    $cashquery = $this->db->select('m_recvd_id as id,m_recvd_amount as tamont,m_recvd_method as method_id,method.m_group_name as method_name,m_recvd_voucher as recipt_no,m_recvd_date as date,m_recvd_remark as note,(CASE WHEN m_recvd_account = 1 THEN mct.m_cust_name WHEN m_recvd_account = 7 && m_recvd_method = ' . $method . ' THEN mgt.m_group_name WHEN m_recvd_account = 7 THEN method.m_group_name ELSE mutt.m_user_name END) as csname,mut.m_user_name as user,m_city_name as city')->get('master_recieved_tbl')->result();


    if (!empty($cashquery)) {
      foreach ($cashquery as $krey) {
        $total_balance += $krey->tamont;
        $sql1[] = $krey;
        // echo "<br> + ". $total_balance;
      }
    }

    if (!empty($todate)) {
      $this->db->where('m_payment_date <=', $todate);
    }
    if (!empty($from_date)) {
      $this->db->where('m_payment_date >=', $from_date);
    }


    if ($pagetype == 2) {
      $this->db->select('(CASE WHEN m_payment_account = 7 && m_payment_method = ' . $method . ' THEN "2" WHEN m_payment_account = 7 THEN "1" ELSE "2" END) as type')->where("CASE WHEN m_payment_account = 7 && m_payment_method = '$method' THEN m_payment_method = '$method' WHEN m_payment_account = 7 THEN m_payment_supplier = '$method' ELSE m_payment_method = '$method' END");
    } else {
      $this->db->select('"2" as type')->where('m_payment_method', $method);
    }
    $this->db->join('master_users_tbl mct', 'mct.m_user_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_payment_tbl.m_payment_user', 'left');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = mct.m_user_city', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_payment_tbl.m_payment_method', 'left');
    $payquery = $this->db->select('m_payment_id as id,m_payment_amount as tamont,m_payment_method as method_id,method.m_group_name as method_name,m_payment_voucher as recipt_no,m_payment_date as date,m_payment_remark as note,(CASE WHEN m_payment_account = 2 THEN mgt.m_group_name WHEN m_payment_account = 7 && m_payment_method = ' . $method . ' THEN mgt.m_group_name WHEN m_payment_account = 7 THEN method.m_group_name ELSE mct.m_user_name END) as csname,mut.m_user_name as user,m_city_name as city')->get('master_payment_tbl')->result();


    if (!empty($payquery)) {
      foreach ($payquery as $krrey) {
        if ($krrey->type == 2) {
          $total_balance -= $krrey->tamont;
        } else {
          $total_balance += $krrey->tamont;
        }

        $sql1[] = $krrey;
        // echo "<br> ".$krrey->tamont ." - ". $total_balance;
      }
    }
    // die ;
    $names = array();
    foreach ($sql1 as $my_object) {
      $names[] = $my_object->date; //any object field
    }
    array_multisort($names, SORT_ASC, $sql1);

    // echo '<pre>';
    // print_r($sql1);
    // die;

    if ($balance == 1) {
      return $total_balance;
    } else {
      return $sql1;
    }
  }

  public function general_invest_leger($from_date, $todate, $account_name, $balance = '')
  {

    $opening_bal = $this->Main_model->get_user_dtl($account_name);

    $total_balance = $opening_bal->m_user_opening;
    $sql1 = array();
    if (!empty($from_date)) {
      $this->db->where('m_recvd_date >=', $from_date);
    }

    if (!empty($todate)) {
      $this->db->where('m_recvd_date <=', $todate);
    }

    $this->db->join('master_users_tbl mutt', 'mutt.m_user_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_recieved_tbl.m_recvd_user', 'left');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = mutt.m_user_city', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_recieved_tbl.m_recvd_method', 'left');
    $cashquery = $this->db->select('m_recvd_id as id,m_recvd_amount as tamont,m_recvd_method as method_id,method.m_group_name as method_name,m_recvd_voucher as recipt_no,m_recvd_date as date,m_recvd_remark as note,mutt.m_user_name as csname,mut.m_user_name as user,"1" as type,m_city_name as city')->where('(m_recvd_account = 2 OR m_recvd_account = 3)')->where('m_recvd_customer', $account_name)->get('master_recieved_tbl')->result();

    if (!empty($cashquery)) {
      foreach ($cashquery as $krey) {
        $total_balance -= $krey->tamont;
        $sql1[] = $krey;
      }
    }

    if (!empty($todate)) {
      $this->db->where('m_payment_date <=', $todate);
    }
    if (!empty($from_date)) {
      $this->db->where('m_payment_date >=', $from_date);
    }

    $this->db->join('master_users_tbl mct', 'mct.m_user_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_payment_tbl.m_payment_user', 'left');
    $this->db->join('master_city_tbl', 'master_city_tbl.m_city_id = mct.m_user_city', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_payment_tbl.m_payment_method', 'left');
    $payquery = $this->db->select('m_payment_id as id,m_payment_amount as tamont,m_payment_method as method_id,method.m_group_name as method_name,m_payment_voucher as recipt_no,m_payment_date as date,m_payment_remark as note,(CASE WHEN m_payment_account = 2 THEN mgt.m_group_name ELSE mct.m_user_name END) as csname,mut.m_user_name as user,"2" as type,m_city_name as city')->where('(m_payment_account = 5 OR m_payment_account = 6)')->where('m_payment_supplier', $account_name)->get('master_payment_tbl')->result();

    if (!empty($payquery)) {
      foreach ($payquery as $krrey) {
        $total_balance += $krrey->tamont;
        $sql1[] = $krrey;
      }
    }

    if (!empty($todate)) {
      $this->db->where('m_voucher_date <=', $todate);
    }
    if (!empty($from_date)) {
      $this->db->where('m_voucher_date >=', $from_date);
    }


    $voucherquery = $this->db->select('m_voucher_id as id,m_voucher_amount as tamont,m_voucher_type as method_id,"" as method_name,m_voucher_id as recipt_no,m_voucher_date as date,m_voucher_remark as note,"" as csname,"" as user,"3" as type,"" as city')->where('(m_voucher_account = 6 OR m_voucher_account = 7)')->where('m_voucher_accountid', $account_name)->get('master_voucher_tbl')->result();

    if (!empty($voucherquery)) {
      foreach ($voucherquery as $krey) {
        if ($krey->type == 1) {
          $total_balance += $krey->tamont;
        } else {
          $total_balance -= $krey->tamont;
        }

        $sql1[] = $krey;
      }
    }

    $names = array();
    foreach ($sql1 as $my_object) {
      $names[] = $my_object->date; //any object field
    }
    array_multisort($names, SORT_ASC, $sql1);

    // echo '<pre>';
    // print_r($sql1);
    // die;

    if ($balance == 1) {
      return $total_balance;
    } else {
      return $sql1;
    }
  }

  public function fright_ledger($from_date, $todate, $account_name, $group, $balance = '')
  {

    $opening_bal = $this->Master_model->get_edit_group($account_name);
    $total_balance = $opening_bal->m_group_opening;
    $sql1 = array();
    if (!empty($from_date)) {
      $this->db->where('m_sale_date >=', $from_date);
    }

    if (!empty($todate)) {
      $this->db->where('m_sale_date <=', $todate);
    }

    $this->db->select('m_sale_id as id,"" as method_name,m_sale_spo as recipt_no,Group_concat(m_sale_qty,"*",mit.m_item_fright) as note,m_sale_date as date,m_sale_fright as tamont,"1" as type');
    $this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_sales_tbl.m_sale_customer', 'left');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left');
    $this->db->where('mct.m_cust_group', $group);
    $this->db->group_by('m_sale_spo');
    $sale_datil = $this->db->get('master_sales_tbl')->result();

    // echo '<pre>'; print_r($sale_datil); die;

    if (!empty($sale_datil)) {
      foreach ($sale_datil as $krey) {
        $total_balance += $krey->tamont;
        $sql1[] = $krey;
      }
    }

    if (!empty($todate)) {
      $this->db->where('m_payment_date <=', $todate);
    }
    if (!empty($from_date)) {
      $this->db->where('m_payment_date >=', $from_date);
    }

    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_payment_tbl.m_payment_user', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_payment_tbl.m_payment_method', 'left');
    $payquery = $this->db->select('m_payment_id as id,m_payment_amount as tamont,method.m_group_name as method_name,m_payment_voucher as recipt_no,m_payment_date as date,m_payment_remark as note,"2" as type')
      ->where('m_payment_account', 2)->where('m_payment_supplier', $account_name)->get('master_payment_tbl')->result();

    if (!empty($payquery)) {
      foreach ($payquery as $krrey) {
        $total_balance -= $krrey->tamont;
        $sql1[] = $krrey;
      }
    }

    $names = array();
    foreach ($sql1 as $my_object) {
      $names[] = $my_object->date; //any object field
    }
    array_multisort($names, SORT_ASC, $sql1);

    // echo '<pre>';
    // print_r($sql1);
    // die;

    if ($balance == 1) {
      return $total_balance;
    } else {
      return $sql1;
    }
  }

  public function staffcomm_ledger($from_date, $todate, $account_name, $balance = '')
  {

    $opening_bal = $this->Main_model->get_user_dtl($account_name);
    $total_balance = $opening_bal->m_user_opening;
    $sql1 = array();
    if (!empty($from_date)) {
      $this->db->where('m_sale_date >=', $from_date);
    }

    if (!empty($todate)) {
      $this->db->where('m_sale_date <=', $todate);
    }

    $this->db->select('m_sale_id as id,"" as method_name,m_sale_spo as recipt_no,Group_concat(m_sale_qty,"*",mit.m_item_comm) as note,m_sale_date as date,Group_concat(m_sale_qty * mit.m_item_comm) as tamont,"1" as type');
    $this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_sales_tbl.m_sale_customer', 'left');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left');
    $this->db->where('m_sale_user', $account_name);
    $this->db->group_by('m_sale_spo');
    $sale_datil = $this->db->get('master_sales_tbl')->result();

    // echo '<pre>'; print_r($sale_datil); die;

    if (!empty($sale_datil)) {
      foreach ($sale_datil as $krey) {
        $total_balance += array_sum(explode(',', $krey->tamont));
        $sql1[] = $krey;
      }
    }

    if (!empty($todate)) {
      $this->db->where('m_payment_date <=', $todate);
    }
    if (!empty($from_date)) {
      $this->db->where('m_payment_date >=', $from_date);
    }


    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_payment_tbl.m_payment_method', 'left');
    $payquery = $this->db->select('m_payment_id as id,m_payment_amount as tamont,method.m_group_name as method_name,m_payment_voucher as recipt_no,m_payment_date as date,m_payment_remark as note,"2" as type')
      ->where('m_payment_account', 4)->where('m_payment_supplier', $account_name)->get('master_payment_tbl')->result();

    if (!empty($payquery)) {
      foreach ($payquery as $krrey) {
        $total_balance -= $krrey->tamont;
        $sql1[] = $krrey;
      }
    }

    $names = array();
    foreach ($sql1 as $my_object) {
      $names[] = $my_object->date; //any object field
    }
    array_multisort($names, SORT_ASC, $sql1);

    // echo '<pre>';
    // print_r($sql1);
    // die;

    if ($balance == 1) {
      return $total_balance;
    } else {
      return $sql1;
    }
  }

  public function expense_leger($from_date, $todate, $account_name, $balance = '')
  {

    $opening_bal = $this->Master_model->get_edit_group($account_name);
    $total_balance = $opening_bal->m_group_opening;
    $sql1 = array();
    if (!empty($from_date)) {
      $this->db->where('m_exp_date >=', $from_date);
    }

    if (!empty($todate)) {
      $this->db->where('m_exp_date <=', $todate);
    }

    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_expenses_tbl.m_exp_user', 'left');

    $cashquery = $this->db->select('m_exp_id as id,m_exp_amount as tamont,m_exp_method as method_id,m_exp_accno as method_name,m_exp_voucher as recipt_no,m_exp_date as date,m_exp_remark as note,"" as csname,mut.m_user_name as user,"1" as type')->where('m_exp_name', $account_name)->get('master_expenses_tbl')->result();

    if (!empty($cashquery)) {
      foreach ($cashquery as $krey) {
        $total_balance += $krey->tamont;
        $sql1[] = $krey;
      }
    }

    if (!empty($todate)) {
      $this->db->where('m_payment_date <=', $todate);
    }
    if (!empty($from_date)) {
      $this->db->where('m_payment_date >=', $from_date);
    }

    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_payment_tbl.m_payment_supplier', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_payment_tbl.m_payment_user', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_payment_tbl.m_payment_method', 'left');
    $payquery = $this->db->select('m_payment_id as id,m_payment_amount as tamont,m_payment_method as method_id,method.m_group_name as method_name,m_payment_voucher as recipt_no,m_payment_date as date,m_payment_remark as note,mgt.m_group_name as csname,mut.m_user_name as user,"2" as type')->where('m_payment_account', 2)->where('m_payment_supplier', $account_name)->get('master_payment_tbl')->result();

    if (!empty($payquery)) {
      foreach ($payquery as $krrey) {
        $total_balance -= $krrey->tamont;
        $sql1[] = $krrey;
      }
    }

    if (!empty($todate)) {
      $this->db->where('m_recvd_date <=', $todate);
    }
    if (!empty($from_date)) {
      $this->db->where('m_recvd_date >=', $from_date);
    }

    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_recieved_tbl.m_recvd_customer', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_recieved_tbl.m_recvd_user', 'left');
    $this->db->join('master_group_tbl method', 'method.m_group_id = master_recieved_tbl.m_recvd_method', 'left');
    $payquery = $this->db->select('m_recvd_id as id,m_recvd_amount as tamont,m_recvd_method as method_id,method.m_group_name as method_name,m_recvd_voucher as recipt_no,m_recvd_date as date,m_recvd_remark as note,mgt.m_group_name as csname,mut.m_user_name as user,"1" as type')->where('m_recvd_account', 5)->where('m_recvd_customer', $account_name)->get('master_recieved_tbl')->result();

    if (!empty($payquery)) {
      foreach ($payquery as $krrey) {
        $total_balance += $krrey->tamont;
        $sql1[] = $krrey;
      }
    }


    if (!empty($todate)) {
      $this->db->where('m_voucher_date <=', $todate);
    }
    if (!empty($from_date)) {
      $this->db->where('m_voucher_date >=', $from_date);
    }
    $voucherquery = $this->db->select('m_voucher_id as id,m_voucher_amount as tamont,m_voucher_type as method_id,"" as method_name,m_voucher_id as recipt_no,m_voucher_date as date,m_voucher_remark as note,"" as csname,"" as user,"3" as type,"" as city')->where('(m_voucher_account = 3)')->where('m_voucher_accountid', $account_name)->get('master_voucher_tbl')->result();

    if (!empty($voucherquery)) {
      foreach ($voucherquery as $krey) {
        if ($krey->type == 1) {
          $total_balance += $krey->tamont;
        } else {
          $total_balance -= $krey->tamont;
        }

        $sql1[] = $krey;
      }
    }

    $names = array();
    foreach ($sql1 as $my_object) {
      $names[] = $my_object->date; //any object field
    }
    array_multisort($names, SORT_ASC, $sql1);

    // echo '<pre>';
    // print_r($sql1);
    // die;

    if ($balance == 1) {
      return $total_balance;
    } else {
      return $sql1;
    }
  }

  public function voucher_leger($type, $from_date, $todate, $balance = '')
  {

    // $opening_bal = $this->Master_model->get_edit_group($account_name);
    $total_balance = 0;
    $sql1 = array();

    if (!empty($todate)) {
      $this->db->where('m_voucher_date <=', $todate);
    }
    if (!empty($from_date)) {
      $this->db->where('m_voucher_date >=', $from_date);
    }
    $this->db->join('master_group_tbl mgt', 'mgt.m_group_id = master_voucher_tbl.m_voucher_accountid', 'left');
    $this->db->join('master_users_tbl mut', 'mut.m_user_id = master_voucher_tbl.m_voucher_accountid', 'left');
    $this->db->join('master_customer_tbl mct', 'mct.m_cust_id = master_voucher_tbl.m_voucher_accountid', 'left');
    $voucherquery = $this->db->select('m_voucher_id as id,m_voucher_amount as tamont,m_voucher_type as type,m_voucher_id as recipt_no,m_voucher_date as date,m_voucher_remark as note,(CASE WHEN m_voucher_account = 3 THEN mgt.m_group_name WHEN m_voucher_account = 1 THEN mct.m_cust_name ELSE mut.m_user_name END) as csname,(CASE WHEN m_voucher_account = 3 THEN mgt.m_group_number WHEN m_voucher_account = 1 THEN mct.m_cust_mobile ELSE mut.m_user_mobile END) as csmobile')->where('m_voucher_type', $type)->order_by('m_voucher_date')->get('master_voucher_tbl')->result();

    if (!empty($voucherquery)) {
      foreach ($voucherquery as $krey) {
        if ($krey->type == 1) {
          $total_balance += $krey->tamont;
        } else {
          $total_balance -= $krey->tamont;
        }

        $sql1[] = $krey;
      }
    }

    // $names = array();
    // foreach ($sql1 as $my_object) {
    //   $names[] = $my_object->date; //any object field
    // }
    // array_multisort($names, SORT_ASC, $sql1);

    // echo '<pre>';
    // print_r($sql1);
    // die;

    if ($balance == 1) {
      return $total_balance;
    } else {
      return $sql1;
    }
  }

  function sales_item_group($from_date, $todate, $agent)
  {

    if (!empty($from_date)) {
      $this->db->where('DATE_FORMAT(m_sale_date,"%Y-%m-%d")>=', $from_date);
    }
    if (!empty($todate)) {
      $this->db->where('DATE_FORMAT(m_sale_date,"%Y-%m-%d")<=', $todate);
    }

    if (!empty($agent)) {
      $this->db->where_in('m_sale_user', $agent);
    }

    $this->db->select('sum(m_sale_qty) as tqty,sum(m_sale_weight) as twght,m_item_name,sum(m_sale_total) as total_amount,unit.m_itgrp_title,m_user_name');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
      ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left')
      ->join('master_users_tbl', 'master_users_tbl.m_user_id = master_sales_tbl.m_sale_user', 'left');

    $this->db->order_by('m_item_name');
    $this->db->group_by('m_sale_item');

    // $this->db->group_by('m_sale_user');
    return $this->db->get('master_sales_tbl')->result();
  }

  public function lotwise_sales_list($lotno, $item_id)
  {
    $this->db->select('master_sales_tbl.*,m_item_name,m_cust_name,m_cust_mobile,');
    $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
      ->join('master_customer_tbl mut', 'mut.m_cust_id = master_sales_tbl.m_sale_customer', 'left');
    $this->db->where('m_sale_lot', $lotno);
    $this->db->where('m_sale_item', $item_id);
    $this->db->order_by('m_item_name');
    $this->db->order_by('m_sale_date');
    return $this->db->get('master_sales_tbl')->result();
  }

  public function purchase_sales_list($spono, $type)
  {
    $result = array();
    $purdetail = $this->Main_model->get_edit_purchase($spono);

    if (!empty($purdetail)) {
      foreach ($purdetail as $purdtl) {

        $this->db->select('master_sales_tbl.*,m_item_name,m_cust_name,m_cust_mobile,');
        $this->db->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
          ->join('master_customer_tbl mut', 'mut.m_cust_id = master_sales_tbl.m_sale_customer', 'left');
        $this->db->where('m_sale_lot', $purdtl->m_purcs_id);
        $this->db->where('m_sale_item', $purdtl->m_purcs_item);
        $this->db->order_by('m_item_name');
        $this->db->order_by('m_sale_date');
        $saledetail = $this->db->get('master_sales_tbl')->result();
        if (!empty($saledetail)) {
          foreach ($saledetail as $sldtl) {
            $result[] = $sldtl;
          }
        }
      }
    }

    if ($type == 1) {
      return $purdetail;
    } else {
      return $result;
    }
  }


  public function get_truck_report($fromdate, $todate, $supplier = '')
  {
    $result = array();

    $all_purchase = $this->Main_model->purchase_group($fromdate, $todate, $supplier);
    if (!empty($all_purchase)) {
      foreach ($all_purchase as $cau) {
        $purchase_detail = $this->Main_model->get_edit_purchase($cau->m_purcs_spo);
        $internal_exp = $this->Main_model->get_purchase_expense($cau->m_purcs_spo);
        $Tsaleqty = 0;
        $Tsaleweight = 0;
        $Tsaletotal = 0;
        $sale_comm = 0;
        $sale_fright = 0;
        $sale_hamali = 0;
        $sale_others = 0;
        $saleexp = 0;
        $sal_spo = array();
        if (!empty($purchase_detail)) {

          foreach ($purchase_detail as $key) {
            $sale_datail = $this->db->select('sum(m_sale_qty) as saleqty,sum(m_sale_weight) as saleweight,sum(m_sale_total) as saletotal,Group_CONCAT(m_sale_spo) as m_sale_spo')->where('m_sale_lot', $key->m_purcs_id)->where('m_sale_item', $key->m_purcs_item)->get('master_sales_tbl')->result();
            if (!empty($sale_datail)) {
              $sal_spo[] = $sale_datail[0]->m_sale_spo;
              $Tsaleqty += $sale_datail[0]->saleqty;
              $Tsaleweight += $sale_datail[0]->saleweight;
              $Tsaletotal += $sale_datail[0]->saletotal;
            }
          }
        }

        $get_spo = !empty($sal_spo) ? implode(',', $sal_spo) : '';
        $sale_spo_uni = array_unique(explode(',', $get_spo));

        if (!empty($sale_spo_uni)) {
          foreach ($sale_spo_uni as $kry) {
            $sale_expense = $this->db->select('m_sale_comm,m_sale_fright,m_sale_hamali,m_sale_others,(m_sale_comm+m_sale_fright+m_sale_hamali+m_sale_others) as saleexp')->where('m_sale_spo', $kry)->group_by('m_sale_spo')->get('master_sales_tbl')->result();

            if (!empty($sale_expense)) {
              $sale_comm += $sale_expense[0]->m_sale_comm;
              $sale_fright += $sale_expense[0]->m_sale_fright;
              $sale_hamali += $sale_expense[0]->m_sale_hamali;
              $sale_others += $sale_expense[0]->m_sale_others;
              $saleexp += $sale_expense[0]->saleexp;
            }
          }
        }



        $res = array(
          "Pdate" => $cau->m_purcs_date,
          "Challan_no" => $cau->m_purcs_spo,
          "truck_no" => $cau->m_purcs_truckno,
          "Supplier_name" => $cau->supplier_name,
          "pur_qty" => $cau->tqty,
          "pur_amount" => $cau->total_amount,
          "pur_weight" => $cau->twght,
          "pur_netamount" => ($cau->total_expense + $cau->total_amount),
          "pur_comm" => $cau->m_purcs_comm,
          "pur_fright" => $cau->m_purcs_fright,
          "pur_hamali" => $cau->m_purcs_hamali,
          "pur_charity" => $cau->m_purcs_charity,
          "pur_packaging" => $cau->m_purcs_packaging,
          "pur_loading" => $cau->m_purcs_loading,
          "pur_advance" => $cau->m_purcs_advance,
          "pur_others" => $cau->m_purcs_others,
          "sale_qty" => $Tsaleqty,
          "sale_weight" => $Tsaleweight,
          "sale_amount" => $Tsaletotal,
          "sale_netamount" => $Tsaletotal,
          "m_sale_comm" => $sale_comm,
          "m_sale_fright" => $sale_fright,
          "m_sale_hamali" => $sale_hamali,
          "m_sale_others" => $sale_others,
          "saleexp" => $saleexp,
          "internal_expense" => $internal_exp,
        );

        $result[] = $res;
      }

      // echo "<pre>";
      // print_r($result);
      // die;
    }
    return $result;
  }

  // public function get_staff_performance_new_report($from_date, $to_date, $staff_id, $staff_group, $report_type)
  // {

  //   $main_result = array();
  //   $item_name = '';
  //   $cust_list = $this->Main_model->get_cust_list(null, null, null, null, $staff_group);
  //   if (!empty($cust_list)) {
  //     foreach ($cust_list as $key) {

  //       if (!empty($from_date)) {
  //         $this->db->where('DATE_FORMAT(m_sale_date,"%Y-%m-%d")>=', $from_date);
  //       }
  //       if (!empty($to_date)) {
  //         $this->db->where('DATE_FORMAT(m_sale_date,"%Y-%m-%d")<=', $to_date);
  //       }

  //       $sale_datil = $this->db->select('m_sale_spo,m_sale_trackno,sum(m_sale_qty) as total_qty,sum(m_sale_total) as sub_total,m_sale_date,sum(m_sale_weight) as total_weight,sum(m_sale_crate) as total_crate,m_sale_comrate,m_sale_comm,m_sale_fright,m_sale_hamali,m_sale_others,(m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) as total_expense,m_sale_note,m_sale_user,m_sale_customer,Group_concat(m_sale_qty) as sale_qty,Group_concat(m_sale_price) as sale_price,Group_concat(m_sale_total) as sale_total,Group_concat(m_sale_weight) as sale_weight,Group_concat(m_sale_crate) as sale_crate,Group_concat(m_item_name) as sale_itemname,Group_concat(crate.m_itgrp_title) as sale_cratetype,Group_concat(unit.m_itgrp_title) as sale_unitname,m_cust_name,m_cust_id,m_cust_mobile')
  //         ->join('master_customer_tbl mut', 'mut.m_cust_id = master_sales_tbl.m_sale_customer', 'left')
  //         ->join('master_item_tbl mit', 'mit.m_item_id = master_sales_tbl.m_sale_item', 'left')
  //         ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
  //         ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left')
  //         ->where('m_sale_user', $staff_id)->where('m_sale_customer', $key->m_cust_id)->order_by('m_sale_date')->group_by('m_sale_spo')->get('master_sales_tbl')->result();



  //       $this->db->where('m_recvd_date >=', $from_date);
  //       $this->db->where('m_recvd_date <=', $to_date);
  //       $cratequery = $this->db->select('sum(m_recvd_qty) as tqty,m_recvd_voucher,m_recvd_date,m_recvd_remark,Group_concat(m_recvd_crate) as crate_id,Group_concat(crate.m_itgrp_title) as crate_name,Group_concat(m_recvd_qty) as crate_qty,m_cust_name,m_cust_id,m_cust_mobile')
  //         ->join('master_customer_tbl mut', 'mut.m_cust_id = master_recieved_tbl.m_recvd_customer', 'left')->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left')->where('m_recvd_type', 2)->where('m_recvd_user', $staff_id)->where('m_recvd_customer', $key->m_cust_id)->group_by('m_recvd_voucher')->order_by('m_recvd_date')->get('master_recieved_tbl')->result();

  //       $this->db->where('m_recvd_date >=', $from_date);
  //       $this->db->where('m_recvd_date <=', $to_date);

  //       $cashquery = $this->db->select('m_recvd_amount,m_recvd_method,m_group_name as method_name,m_recvd_voucher,m_recvd_date,m_recvd_remark,m_cust_name,m_cust_id,m_cust_mobile')
  //         ->join('master_customer_tbl mut', 'mut.m_cust_id = master_recieved_tbl.m_recvd_customer', 'left')->join('master_group_tbl method', 'method.m_group_id = master_recieved_tbl.m_recvd_method', 'left')->where('m_recvd_user', $staff_id)->where('m_recvd_account', 1)->where('m_recvd_type', 1)->where('m_recvd_customer', $key->m_cust_id)->order_by('m_recvd_date')->get('master_recieved_tbl')->result();

  //       if ($report_type == 1) {
  //         if (!empty($sale_datil)) {
  //           foreach ($sale_datil as $key) {
  //             $item_name .= $key->sale_itemname . ',';
  //             $main_result[] = $key;
  //           }
  //         }
  //       } else if ($report_type == 2) {
  //         if (!empty($cashquery)) {
  //           foreach ($cashquery as $key) {
  //             $item_name .= $key->method_name . ',';
  //             $main_result[] = $key;
  //           }
  //         }
  //       } else {
  //         if (!empty($cratequery)) {
  //           foreach ($cratequery as $key) {
  //             $item_name .= $key->crate_name . ',';
  //             $main_result[] = $key;
  //           }
  //         }
  //       }
  //     }
  //   }

  //   $result = array(
  //     'items' => $item_name == '' ? '' : array_unique(array_filter(explode(',', $item_name))),
  //     'data' => $main_result,
  //   );
  //   return $result;

  //   // echo '<pre>';
  //   // print_r($result);
  //   // die;

  // }

public function get_staff_performance_new_report($from_date, $to_date, $staff_id, $staff_group, $report_type)
{
    // Get all customer IDs in ONE query
    $cust_list = $this->Main_model->get_cust_list(null, null, null, null, $staff_group);

    if (empty($cust_list)) {
        return ['items' => '', 'data' => []];
    }

    // Extract all customer IDs as a flat array for WHERE IN
    $cust_ids = array_column((array) $cust_list, 'm_cust_id');

    // Run ONE query for the required report type (no loop)
    switch ($report_type) {
        case 1:
            [$main_result, $name_field] = $this->_query_sales_bulk($from_date, $to_date, $staff_id, $cust_ids);
            break;
        case 2:
            [$main_result, $name_field] = $this->_query_cash_bulk($from_date, $to_date, $staff_id, $cust_ids);
            break;
        default:
            [$main_result, $name_field] = $this->_query_crate_bulk($from_date, $to_date, $staff_id, $cust_ids);
            break;
    }

    // Collect and deduplicate item names
    $all_names = [];
    foreach ($main_result as $row) {
        if (!empty($row->$name_field)) {
            foreach (explode(',', $row->$name_field) as $name) {
                $name = trim($name);
                if ($name !== '') $all_names[$name] = true;
            }
        }
    }

    return [
        'items' => empty($all_names) ? '' : array_keys($all_names),
        'data'  => $main_result,
    ];
}

// ─── Single bulk query for Sales ────────────────────────────────────────────

private function _query_sales_bulk($from_date, $to_date, $staff_id, array $cust_ids)
{
    if (!empty($from_date)) $this->db->where('DATE_FORMAT(m_sale_date,"%Y-%m-%d") >=', $from_date);
    if (!empty($to_date))   $this->db->where('DATE_FORMAT(m_sale_date,"%Y-%m-%d") <=', $to_date);

    $result = $this->db
        ->select('m_sale_spo, m_sale_trackno,
                  SUM(m_sale_qty)    AS total_qty,
                  SUM(m_sale_total)  AS sub_total,
                  m_sale_date,
                  SUM(m_sale_weight) AS total_weight,
                  SUM(m_sale_crate)  AS total_crate,
                  m_sale_comrate, m_sale_comm, m_sale_fright, m_sale_hamali, m_sale_others,
                  (m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) AS total_expense,
                  m_sale_note, m_sale_user, m_sale_customer,
                  GROUP_CONCAT(m_sale_qty)              AS sale_qty,
                  GROUP_CONCAT(m_sale_price)            AS sale_price,
                  GROUP_CONCAT(m_sale_total)            AS sale_total,
                  GROUP_CONCAT(m_sale_weight)           AS sale_weight,
                  GROUP_CONCAT(m_sale_crate)            AS sale_crate,
                  GROUP_CONCAT(m_item_name)             AS sale_itemname,
                  GROUP_CONCAT(crate.m_itgrp_title)     AS sale_cratetype,
                  GROUP_CONCAT(unit.m_itgrp_title)      AS sale_unitname,
                  m_cust_name, m_cust_id, m_cust_mobile')
        ->join('master_customer_tbl mut',       'mut.m_cust_id    = master_sales_tbl.m_sale_customer', 'left')
        ->join('master_item_tbl mit',           'mit.m_item_id    = master_sales_tbl.m_sale_item',     'left')
        ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate',                 'left')
        ->join('master_itemgroup_tbl as unit',  'unit.m_itgrp_id  = mit.m_item_unit',                  'left')
        ->where('m_sale_user', $staff_id)
        ->where_in('m_sale_customer', $cust_ids)   // ← WHERE IN replaces the loop
        ->order_by('m_sale_date')
        ->group_by('m_sale_spo')
        ->get('master_sales_tbl')
        ->result();

    return [$result, 'sale_itemname'];
}

// ─── Single bulk query for Crate returns ────────────────────────────────────

private function _query_crate_bulk($from_date, $to_date, $staff_id, array $cust_ids)
{
    $result = $this->db
        ->select('SUM(m_recvd_qty) AS tqty,
                  m_recvd_voucher, m_recvd_date, m_recvd_remark,
                  GROUP_CONCAT(m_recvd_crate)        AS crate_id,
                  GROUP_CONCAT(crate.m_itgrp_title)  AS crate_name,
                  GROUP_CONCAT(m_recvd_qty)           AS crate_qty,
                  m_cust_name, m_cust_id, m_cust_mobile')
        ->join('master_customer_tbl mut',       'mut.m_cust_id    = master_recieved_tbl.m_recvd_customer', 'left')
        ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate',    'left')
        ->where('m_recvd_date >=',  $from_date)
        ->where('m_recvd_date <=',  $to_date)
        ->where('m_recvd_type',     2)
        ->where('m_recvd_user',     $staff_id)
        ->where_in('m_recvd_customer', $cust_ids)  // ← WHERE IN replaces the loop
        ->group_by('m_recvd_voucher')
        ->order_by('m_recvd_date')
        ->get('master_recieved_tbl')
        ->result();

    return [$result, 'crate_name'];
}

// ─── Single bulk query for Cash received ────────────────────────────────────

private function _query_cash_bulk($from_date, $to_date, $staff_id, array $cust_ids)
{
    $result = $this->db
        ->select('m_recvd_amount, m_recvd_method,
                  m_group_name AS method_name,
                  m_recvd_voucher, m_recvd_date, m_recvd_remark,
                  m_cust_name, m_cust_id, m_cust_mobile')
        ->join('master_customer_tbl mut', 'mut.m_cust_id     = master_recieved_tbl.m_recvd_customer', 'left')
        ->join('master_group_tbl method', 'method.m_group_id = master_recieved_tbl.m_recvd_method',   'left')
        ->where('m_recvd_date >=',   $from_date)
        ->where('m_recvd_date <=',   $to_date)
        ->where('m_recvd_user',      $staff_id)
        ->where('m_recvd_account',   1)
        ->where('m_recvd_type',      1)
        ->where_in('m_recvd_customer', $cust_ids)  // ← WHERE IN replaces the loop
        ->order_by('m_recvd_date')
        ->get('master_recieved_tbl')
        ->result();

    return [$result, 'method_name'];
}

public function get_staff_daily_customer_report($staff_id, $date)
{
    // ── 1. Get staff group ───────────────────────────────────────────────────
    $staff = $this->db
        ->select('m_user_group')
        ->where('m_user_id', $staff_id)
        ->get('master_users_tbl')
        ->row();

    if (empty($staff)) {
        return ['crate_types' => [], 'grand' => [], 'data' => []];
    }

    $cust_list = $this->Main_model->get_cust_list(null, null, null, null, $staff->m_user_group);

    if (empty($cust_list)) {
        return ['crate_types' => [], 'grand' => [], 'data' => []];
    }

    $cust_ids = array_column((array) $cust_list, 'm_cust_id');

    // ── 2. Sales ─────────────────────────────────────────────────────────────
    $sales = $this->db
        ->select('
            m_sale_customer,
            m_cust_name,
            SUM(m_sale_total)   AS sale_total,
            SUM(m_sale_fright)  AS sale_fright
        ')
        ->join('master_customer_tbl mut', 'mut.m_cust_id = master_sales_tbl.m_sale_customer', 'left')
        ->where('m_sale_date',  $date)
        ->where('m_sale_user',  $staff_id)
        ->where_in('m_sale_customer', $cust_ids)
        ->group_by('m_sale_customer')
        ->get('master_sales_tbl')
        ->result();

    // ── 3. Cash received ─────────────────────────────────────────────────────
    $cash = $this->db
        ->select('m_recvd_customer, SUM(m_recvd_amount) AS cash_received')
        ->where('m_recvd_date',    $date)
        ->where('m_recvd_user',    $staff_id)
        ->where('m_recvd_account', 1)
        ->where('m_recvd_type',    1)
        ->where_in('m_recvd_customer', $cust_ids)
        ->group_by('m_recvd_customer')
        ->get('master_recieved_tbl')
        ->result();

    // ── 4. Crate returns ─────────────────────────────────────────────────────
    $crates = $this->db
        ->select('
            m_recvd_customer,
            crate.m_itgrp_title AS crate_type,
            SUM(m_recvd_qty)    AS crate_qty
        ')
        ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = master_recieved_tbl.m_recvd_crate', 'left')
        ->where('m_recvd_date', $date)
        ->where('m_recvd_type', 2)
        ->where('m_recvd_user', $staff_id)
        ->where_in('m_recvd_customer', $cust_ids)
        ->group_by('m_recvd_customer, m_recvd_crate')
        ->get('master_recieved_tbl')
        ->result();

    // ── 5. Build lookup maps + collect ACTIVE customer IDs ──────────────────
    $sales_map = [];
    foreach ($sales as $row) {
        $sales_map[$row->m_sale_customer] = $row;
    }

    $cash_map = [];
    foreach ($cash as $row) {
        $cash_map[$row->m_recvd_customer] = (float) $row->cash_received;
    }

    $crate_map       = [];
    $all_crate_types = [];
    foreach ($crates as $row) {
        $type = trim($row->crate_type);
        $crate_map[$row->m_recvd_customer][$type] = (float) $row->crate_qty;
        $all_crate_types[$type] = true;
    }
    $all_crate_types = array_keys($all_crate_types);
    sort($all_crate_types);

    // ── Key change: only customers with activity today ───────────────────────
    $active_cust_ids = array_unique(array_merge(
        array_keys($sales_map),
        array_keys($cash_map),
        array_keys($crate_map)
    ));

    if (empty($active_cust_ids)) {
        return ['crate_types' => [], 'grand' => [], 'data' => []];
    }

    // ── 6. Build output rows (only active customers) ─────────────────────────
    $data  = [];
    $grand = [
        'sale_total'    => 0,
        'cash_received' => 0,
        'net_balance'   => 0,
        'crate_totals'  => array_fill_keys($all_crate_types, 0),
        'total_crates'  => 0,
    ];

    // Index cust_list by ID for O(1) lookup
    $cust_map = [];
    foreach ($cust_list as $cust) {
        $cust_map[$cust->m_cust_id] = $cust;
    }

    foreach ($active_cust_ids as $cid) {
        $cust        = $cust_map[$cid] ?? null;
        $sale_row    = $sales_map[$cid] ?? null;

        $old_balance   = $cust ? (float) $cust->m_cust_balance : 0;
        $sale_total    = $sale_row ? (float) $sale_row->sale_total  : 0;
        $sale_fright   = $sale_row ? (float) $sale_row->sale_fright : 0;
        $cash_received = $cash_map[$cid] ?? 0;
        $crate_row     = $crate_map[$cid] ?? [];
        $total_crate   = array_sum($crate_row);

        $row_total   = $old_balance + $sale_total + $sale_fright;
        $net_balance = $row_total - $cash_received;

        $grand['sale_total']    += ($sale_total + $sale_fright);
        $grand['cash_received'] += $cash_received;
        $grand['net_balance']   += $net_balance;
        $grand['total_crates']  += $total_crate;
        foreach ($all_crate_types as $ct) {
            $grand['crate_totals'][$ct] += $crate_row[$ct] ?? 0;
        }

        $data[] = (object) [
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

    return [
        'crate_types' => $all_crate_types,
        'grand'       => $grand,
        'data'        => $data,
    ];
}

  public function dashboard_staff_summary($date)
  {
    $result = array();
    $group_lst = $this->Master_model->get_all_active_group(1);

    $cust_outst = $this->db->select('sum(m_cust_balance) as amount_bnl')->where('m_cust_group', 0)->get('master_customer_tbl')->row();

    $sale_datil = $this->db->select('sum(m_sale_qty) as total_qty,sum(m_sale_total) as sub_total,sum(m_sale_crate) as total_crate,(m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) as total_expense')->where('m_sale_date', $date)->where('m_sale_added_by', 1)->where('m_sale_user', 0)->get('master_sales_tbl')->row();


    $cratequery = $this->db->select('sum(m_recvd_qty) as tqty')
      ->where('m_recvd_date', $date)->where('m_recvd_type', 2)->where('m_recvd_added_by', 1)->where('m_recvd_user', 0)->get('master_recieved_tbl')->row();


    $cashquery = $this->db->select('sum(m_recvd_amount) as total_recieved')->where('m_recvd_date', $date)->where('m_recvd_added_by', 1)->where('m_recvd_user', 0)->where('m_recvd_account', 1)->where('m_recvd_type', 1)->get('master_recieved_tbl')->row();


    $res = (object) array(
      'user_id' => '',
      'user_group' => 'o',
      'group_name' => 'Other',
      'staff_name' => 'Admin',
      'cash_outstanding' => $cust_outst->amount_bnl,
      'issue_qty' => 0,
      'issue_amt' => 0,
      'issue_crate' => 0,
      'sale_qty' => $sale_datil->total_qty,
      'sale_amt' => $sale_datil->sub_total,
      'sale_crate' => $sale_datil->total_crate,
      'return_qty' => 0,
      'crate_recieved' => $cratequery->tqty,
      'cash_collected' => $cashquery->total_recieved,
    );

    $result[] = $res;


    if (!empty($group_lst)) {
      foreach ($group_lst as $grp) {

        $cash_outstan = $this->db->select('sum(m_cust_balance) as amount_bnl')->where('m_cust_group', $grp->m_group_id)->get('master_customer_tbl')->row();

        $staff_detail = $this->db->select('m_user_id,m_user_name,m_user_mobile,m_user_contractPerd,m_user_group')->where("FIND_IN_SET('$grp->m_group_id', m_user_group)")->get('master_users_tbl')->row();

        if (!empty($staff_detail)) {
          $issue_datil = $this->db->select('sum(si_issue_qty) as total_qty,sum(si_issue_total) as sub_total,sum(si_issue_crate) as total_crate')->where('si_issue_date', $date)->where('si_issue_type', 1)->where('si_issue_user', $staff_detail->m_user_id)->get('staff_itemissue_tbl')->result();

          $return_datil = $this->db->select('sum(si_issue_qty) as total_qty,sum(si_issue_total) as sub_total,sum(si_issue_crate) as total_crate')->where('si_issue_date', $date)->where('si_issue_type', 2)->where('si_issue_user', $staff_detail->m_user_id)->get('staff_itemissue_tbl')->result();

          $sale_datil = $this->db->select('sum(m_sale_qty) as total_qty,sum(m_sale_total) as sub_total,sum(m_sale_crate) as total_crate,(m_sale_comm + m_sale_fright + m_sale_hamali + m_sale_others) as total_expense')->where('m_sale_date', $date)->where('m_sale_user', $staff_detail->m_user_id)->get('master_sales_tbl')->result();


          $cratequery = $this->db->select('sum(m_recvd_qty) as tqty')
            ->where('m_recvd_date', $date)->where('m_recvd_type', 2)->where('m_recvd_user', $staff_detail->m_user_id)->get('master_recieved_tbl')->result();


          $cashquery = $this->db->select('sum(m_recvd_amount) as total_recieved')->where('m_recvd_date', $date)->where('m_recvd_user', $staff_detail->m_user_id)->where('m_recvd_account', 1)->where('m_recvd_type', 1)->get('master_recieved_tbl')->result();


          $res = (object) array(
            'user_id' => $staff_detail->m_user_id,
            'user_group' => $staff_detail->m_user_group,
            'group_name' => $staff_detail->m_user_name,
            'staff_name' => $staff_detail->m_user_contractPerd,
            'cash_outstanding' => $cash_outstan->amount_bnl,
            // 'crt10_outstand' => $crate10_outstand,
            // 'crt20_outstand' => $crate20_outstand,
            // 'crt25_outstand' => $crate25_outstand,
            'issue_qty' => $issue_datil[0]->total_qty,
            'issue_amt' => $issue_datil[0]->sub_total,
            'issue_crate' => $issue_datil[0]->total_crate,
            'sale_qty' => $sale_datil[0]->total_qty,
            'sale_amt' => $sale_datil[0]->sub_total,
            'sale_crate' => $sale_datil[0]->total_crate,
            'return_qty' => $return_datil[0]->total_qty,
            'crate_recieved' => $cratequery[0]->tqty,
            'cash_collected' => $cashquery[0]->total_recieved,
          );


          $result[] = $res;
        }
      }
    }
    // die ;
    return $result;
  }

  public function get_cust_CashBal($cust_id, $from_date, $opening_bal)
  {

    // $opening_bal = $this->get_cust_dtl($cust_id);

    $sub_total = 0;
    $total_expense = 0;
    $grand_total = 0;

    if (!empty($from_date)) {
      $this->db->where('m_sale_date <=', $from_date);
    }

    $salequery = $this->db->select('sum(m_sale_total) as sub_total,(m_sale_comm+m_sale_fright+m_sale_hamali+m_sale_others) as texpense')->where('m_sale_customer', $cust_id)->group_by('m_sale_spo')->get('master_sales_tbl')->result();
    if (!empty($salequery)) {
      foreach ($salequery as $key) {
        $sub_total += $key->sub_total;
        $total_expense += $key->texpense;
        $grand_total += ($key->sub_total + $key->texpense);
      }
    }

    if (!empty($from_date)) {
      $this->db->where('m_recvd_date <=', $from_date);
    }
    $amountrcvdquery = $this->db->select('sum(m_recvd_amount) as tamountrcvd')->where('m_recvd_customer', $cust_id)->where('m_recvd_account', 1)->where('m_recvd_type', 1)->get('master_recieved_tbl')->result();

    if (!empty($from_date)) {
      $this->db->where('m_voucher_date <=', $from_date);
    }
    $vouch_amtcdrt = $this->db->select('sum(m_voucher_amount) as tamountcdt')->where('m_voucher_accountid', $cust_id)->where('m_voucher_account', 1)->where('m_voucher_type', 1)->where('m_voucher_status', 1)->get('master_voucher_tbl')->result();
    if (!empty($from_date)) {
      $this->db->where('m_voucher_date <=', $from_date);
    }
    $vouch_amtdbt = $this->db->select('sum(m_voucher_amount) as tamountdbt')->where('m_voucher_accountid', $cust_id)->where('m_voucher_account', 1)->where('m_voucher_type', 2)->where('m_voucher_status', 1)->get('master_voucher_tbl')->result();

    $balance_amt = $opening_bal + (($grand_total + $vouch_amtdbt[0]->tamountdbt) - ($amountrcvdquery[0]->tamountrcvd + $vouch_amtcdrt[0]->tamountcdt));

    $result = array(
      "sub_total" => $sub_total,
      "total_expense" => $total_expense,
      "grand_total" => $grand_total,
      "amount_rcvd" => $amountrcvdquery[0]->tamountrcvd ?: 0,
      "balance_amount" => $balance_amt,
    );

    return $result;
  }

  public function get_cust_CrateBal($cust_id, $from_date, $opening_bal)
  {

    $crate_total = 0;
    $total_given = 0;
    $total_recieved = 0;

    $all_crates = $this->Master_model->all_itemgroup(3);
    $openin_crate_bal = explode(',', $opening_bal);
    foreach ($all_crates as $key) {
      $crateledger = $this->Main_model->get_crate_ledger($key->m_itgrp_id, $cust_id, $from_date);
      $crate_total += ((int) $crateledger['crate_given'] - (int) $crateledger['crate_rcvd']);

      $total_given += (int) $crateledger['crate_given'];
      $total_recieved += (int) $crateledger['crate_rcvd'];

      if ($key->m_itgrp_title == '10 KG') {
        $crattype_bal = isset($openin_crate_bal[0]) ? $openin_crate_bal[0] : 0;
      } else if ($key->m_itgrp_title == '20 KG') {
        $crattype_bal = isset($openin_crate_bal[1]) ? $openin_crate_bal[1] : 0;
      } else if ($key->m_itgrp_title == '25 KG') {
        $crattype_bal = isset($openin_crate_bal[2]) ? $openin_crate_bal[2] : 0;
      }

      $res = array(
        'name' => $key->m_itgrp_title,
        'recived' => (int) $crateledger['crate_rcvd'],
        'given' => (int) $crateledger['crate_given'],
        'balance' => ((int) $crattype_bal + (int) $crateledger['crate_given'] - (int) $crateledger['crate_rcvd']),
      );
      $result['crateitems'][] = $res;
    }

    $result['crate_given'] = $total_given;
    $result['crate_recieved'] = $total_recieved;
    $result['balance_crate'] = array_sum(explode(',', $opening_bal->m_cust_crateOP)) + $crate_total;

    return $result;
  }

  public function dashboard_counts($date)
  {

    $cust_lst = $this->db->select('sum(m_cust_balance) as amount_bnl')->get('master_customer_tbl')->row();
    $suppiler_lst = $this->db->select('sum(m_user_balance) as sp_outstd')->where('m_user_type', 2)->get('master_users_tbl')->row();

    $accounts_lst = $this->db->select('m_group_id,m_group_name,m_group_type,m_group_opening')->where('m_group_type', 3)->or_where('m_group_type', 4)->get('master_group_tbl')->result();

    $account_dels = array();
    $spcash_outstan = $suppiler_lst->sp_outstd;
    $cash_outstan = $cust_lst->amount_bnl;

    if (!empty($accounts_lst)) {

      foreach ($accounts_lst as $cbact) {

        if ($cbact->m_group_type == 3) {
          $opening_bal = $this->cash_bank_balance(2, date('Y-m-d', strtotime($date . '-1day')), $cbact->m_group_id, $cbact->m_group_opening);
        } else {
          $opening_bal = $this->cash_bank_balance(1, date('Y-m-d', strtotime($date . '-1day')), $cbact->m_group_id, $cbact->m_group_opening);
        }

        $resar = (object) array(
          'acct_id' => $cbact->m_group_id,
          'acct_name' => $cbact->m_group_name,
          'opening_bal' => IND_money_format(round($opening_bal, 2)),
        );
        $account_dels[] = $resar;
      }
    }


    $res = (object) array(
      'spcash_outstan' => IND_money_format($spcash_outstan),
      'cash_outstan' => IND_money_format($cash_outstan),
      'account_dels' => $account_dels,

    );


    return $res;
  }


  public function get_piechart_data($date)
  {
    $accounts_lst = $this->db->select('m_group_id,m_group_name,m_group_type,m_group_opening')->where('m_group_type', 3)->or_where('m_group_type', 4)->get('master_group_tbl')->result();

    if (!empty($accounts_lst)) {

      foreach ($accounts_lst as $key => $cbact) {

        if ($cbact->m_group_type == 3) {
          $opening_bal = $this->cash_bank_balance(2, date('Y-m-d', strtotime($date . '-1day')), $cbact->m_group_id, $cbact->m_group_opening);
          $clos_bal = $this->cash_bank_balance(2, $date, $cbact->m_group_id, $cbact->m_group_opening);
        } else {
          $opening_bal = $this->cash_bank_balance(1, date('Y-m-d', strtotime($date . '-1day')), $cbact->m_group_id, $cbact->m_group_opening);
          $clos_bal = $this->cash_bank_balance(1, $date, $cbact->m_group_id, $cbact->m_group_opening);
        }

        $data['label'][] = $cbact->m_group_name;
        $data['data'][] = round($clos_bal, 2);
        $data['today'][] = round(($clos_bal - $opening_bal), 2);
      }
    }

    // if (!empty($account_dels)) {
    //   foreach ($account_dels as $key) {

    //   
    //   }
    // }
    return json_encode($data);
  }

  function unique_multidimensional_array($array, $key)
  {
    $temp_array = array();
    $i = 0;
    $key_array = array();

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
