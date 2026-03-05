<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <title>AK Fruits</title>
    <style>
        .bill-area {
            width: 200px;
            /* background-color: #d4cbcb; */
            margin: auto;
            padding: 10px 5px;
        }

        .bill-area h1 {
            font-size: 13px;
            text-align: center;
            margin: 0;

        }

        .bill-area h3 {
            font-size: 10px;
            text-align: center;
            margin: 0;

        }

        .bill-area h4 {
            font-size: 8px;
            text-align: center;
            margin: 0;

        }

        p {
            font-size: 10px;
        }

        th,
        td {
            font-size: 9px;
            padding: 3px !important;
            border-bottom: 1px dashed !important;
        }

        .Border-0 {
            border-bottom: 0 !important;
        }

        .border-none {
            border-bottom: 0 !important;
        }

        .ramesha {
            border-bottom: 1px dashed;
        }
    </style>
</head>

<body>
    <?php if ($pgtype == 1) {
        if (!empty($data)) {
            foreach ($data as $val) { ?>

                <section class="bill-area">
                    <div class="row">
                        <div class="col-12">
                            <h1>AK</h1>
                            <h3>AJAY KUSHWAHA & COMPANY</h3>
                            <h3>BRANCH - DURG & NAGPUR</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <p class="text-center mb-1 mt-3 fw-bold">SALE</p>
                        </div>
                        <div class="col-12 d-flex justify-content-between">
                            <p><b>Bill No:</b> <?= $val->invoice_no ?></p>
                            <p><b>Date:</b> <?= date('d/m/Y', strtotime($sal_date)) ?></p>
                        </div>
                    </div>
                    <p class="m-0 fw-bold mb-2"><?= strtoupper($val->opening['cust_name']) ?></p>

                    <?php $cret10 = $cret20 = $cret25 = $total_crate = 0;
                    if (!empty($val->sale_data)) { ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ITEM</th>
                                    <th>QTY</th>
                                    <th>Rate</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($val->sale_data as $key) {
                                    if ($key->m_item_crate == 20) {
                                        $cret10 += $key->m_sale_qty;
                                    } else if ($key->m_item_crate == 13) {
                                        $cret20 += $key->m_sale_qty;
                                    } else if ($key->m_item_crate == 14) {
                                        $cret25 += $key->m_sale_qty;
                                    }
                                    $total_crate += $key->m_sale_crate;
                                    echo '<tr>
                                        <td>' . $key->m_item_name . '</td>
                                        <td>' . $key->m_sale_qty . ' ' . $key->unitname . '</td>
                                        <td>' . $key->m_sale_price . '</td>
                                        <td>' . $key->m_sale_total . '</td>
                                    </tr>';
                                } ?>


                                <tr>
                                    <td colspan="2" class="text-end Border-0"><?= $val->total_sqty ?></td>
                                    <td class="Border-0">Total</td>
                                    <td class="Border-0"><?= $val->sub_total ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-end Border-0"></td>
                                    <td class="Border-0">Fright</td>
                                    <td class="Border-0"><?= $val->total_expense ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-end"></td>
                                    <td>Amount</td>
                                    <td><?= ($val->grand_total) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php }
                    $balanceFields = [
                        '10 KG' => $cret10,
                        '20 KG' => $cret20,
                        '25 KG' => $cret25
                    ]; ?>
                    <div class="bill-total">

                        <p class="m-0">Old Balance: <?= $val->opening['balance_amount'] ?></p>
                        <p class="m-0">Today Sale: <?= $val->grand_total ?></p>
                        <p class="m-0">Today Receive: <?= $val->total_receive ?></p>
                        <p class="m-0">Today Discount: <?= $val->total_discount ?></p>
                        <p class="m-0">Total Balance:<?= ($val->opening['balance_amount'] + $val->grand_total - $val->total_receive - $val->total_discount) ?></p>

                    </div>
                    <div class="bill-total">
                        <p class="m-0">Old Crate: <?= $val->opening['balance_crate'] ?> Crates (
                            <?php foreach ($val->opening['crateitems'] as $kry) {
                                if ($kry['balance'] != 0) {
                                    echo $kry['name'] . '-' . $kry['balance'] . ',';
                                }
                            } ?>)</p>
                        <p class="m-0">Today Crate: <?= $total_crate ?> Crates (
                            10 Kg- <?= $cret10 ?> ,20 Kg- <?= $cret20 ?> 25 Kg- <?= $cret25 ?>)</p>
                        <p class="m-0">Today Receive Crate: <?= array_sum(array_column($val->crate_data, 'total_qty')) ?> Crates (
                            <?php foreach ($val->crate_data as $kry) {
                                echo $kry->m_itgrp_title . '-' . $kry->total_qty . ',';
                            } ?>)</p>
                        <p>Total Balance Crates: <?= ($val->opening['balance_crate'] + $total_crate - array_sum(array_column($val->crate_data, 'total_qty'))) ?> Crates (
                            <?php foreach ($val->opening['crateitems'] as $cau => $kry) {

                                if ($kry['name'] == $val->crate_data[$cau]->m_itgrp_title) {
                                    echo  $kry['name'] . '-' . ($kry['balance'] +  $balanceFields[$kry['name']] - $val->crate_data[$cau]->total_qty) . ',';
                                }
                            } ?> )</p>

                    </div>
                    </div>
                </section>
        <?php  }
        }
    } else { ?>
        <section class="bill-area">
            <div class="row">
                <div class="col-12">
                    <h1>AK</h1>
                    <h3>AJAY KUSHWAHA & COMPANY</h3>
                    <h3>BRANCH - DURG & NAGPUR</h3>
                    <!-- <h1><? // = get_settings('m_app_name') 
                                ?></h1> -->
                    <!-- <h3>PH No. -<? // = get_settings('m_app_mobile') 
                                        ?></h3>
                <h4><? // = get_settings('m_app_address') 
                    ?></h4> -->
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <p class="text-center mb-1 mt-3 fw-bold">SALE</p>
                </div>
                <div class="col-5 p-0 text-center">
                    <p>Bill No: <?= $edit_value[0]->m_sale_spo ?></p>
                </div>
                <div class="col-4 p-0">
                    <p><?= date('d/m/Y', strtotime($edit_value[0]->m_sale_date)) ?></p>
                </div>
                <div class="col-3 p-0">
                    <p><?= date('h:i A', strtotime($edit_value[0]->m_sale_added_on)) ?></p>
                </div>
            </div>
            <p class="m-0 fw-bold"><?= strtoupper($edit_value[0]->m_cust_name) ?></p>


            <table class="table">
                <thead>
                    <tr>
                        <th>ITEM</th>
                        <th>QTY</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_qty = 0;
                    $total_amount = 0;
                    $total_crate = 0;
                    $expense = ($edit_value[0]->m_sale_comm + $edit_value[0]->m_sale_fright + $edit_value[0]->m_sale_hamali + $edit_value[0]->m_sale_others);
                    foreach ($edit_value as $key) {
                        $total_qty += $key->m_sale_qty;
                        $total_amount += $key->m_sale_total;
                        $total_crate += $key->m_sale_crate;
                        echo '<tr>
                        <td>' . $key->m_item_name . '</td>
                        <td>' . $key->m_sale_qty . ' ' . $key->unitname . '</td>
                        <td>' . $key->m_sale_price . '</td>
                        <td>' . $key->m_sale_total . '</td>
                    </tr>';
                    } ?>


                    <tr>
                        <td colspan="2" class="text-end Border-0"><?= $total_qty ?></td>
                        <td class="Border-0">Total</td>
                        <td class="Border-0"><?= $total_amount ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-end Border-0"></td>
                        <td class="Border-0">Fright</td>
                        <td class="Border-0"><?= $expense ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-end"></td>
                        <td>Amount</td>
                        <td><?= ($total_amount + $expense) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="border-none p-0">Old Balance: <?= $customer_old_balance['balance_amount'] ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="border-none p-0">Today Sale: <?= $customer_today_balance['grand_total'] ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="border-none p-0">Today Receive: <?= $customer_today_balance['amount_rcvd'] ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="border-none p-0">Total Balance:<?= ($customer_old_balance['balance_amount'] + $customer_today_balance['balance_amount']) ?></td>
                    </tr>
                </tbody>
            </table>
            <div class="bill-total">
                <p class="m-0">Old Crate: <?= $customer_old_balance['balance_crate'] ?> Crates (
                    <?php foreach ($customer_old_balance['crateitems'] as $kry) {
                        if ($kry['balance'] != 0) {
                            echo $kry['name'] . '-' . $kry['balance'] . ',';
                        }
                    } ?>)</p>
                <p class="m-0">Today Crate: <?= $customer_today_balance['crate_given'] ?> Crates (
                    <?php foreach ($customer_today_balance['crateitems'] as $kry) {
                        if ($kry['given'] != 0) {
                            echo $kry['name'] . '-' . $kry['given'] . ',';
                        }
                    } ?>)</p>
                <p class="m-0">Today Receive Crate: <?= $customer_today_balance['crate_recieved'] ?> Crates (
                    <?php foreach ($customer_today_balance['crateitems'] as $kry) {
                        if ($kry['recived'] != 0) {
                            echo $kry['name'] . '-' . $kry['recived'] . ',';
                        }
                    } ?>)</p>
                <p>Total Balance Crates: <?= ($customer_old_balance['balance_crate'] + $customer_today_balance['balance_crate']) ?> Crates (
                    <?php foreach ($customer_old_balance['crateitems'] as $cau => $kry) {

                        if ($kry['name'] == $customer_today_balance['crateitems'][$cau]['name']) {
                            echo $kry['name'] . '-' . ($kry['balance'] +  $customer_today_balance['crateitems'][$cau]['balance']) . ',';
                        }
                    } ?>)</p>

            </div>
            </div>
        </section>
    <?php } ?>
</body>

</html>