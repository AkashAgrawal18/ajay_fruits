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
                <p class="text-center mb-1 mt-3 fw-bold"><?= $type == 1 ? 'CASH RECIPT' : 'CRATE RECIPT' ?></p>
            </div>
            <div class="col-5 p-0 text-center">
                <p>Recipt No: <?= $edit_value[0]->m_recvd_voucher ?></p>
            </div>
            <div class="col-4 p-0">
                <p><?= date('d/m/Y', strtotime($edit_value[0]->m_recvd_date)) ?></p>
            </div>
            <div class="col-3 p-0">
                <p><?= date('h:i A', strtotime($edit_value[0]->m_recvd_added_on)) ?></p>
            </div>
        </div>
        <p class="m-0 ">Customer Name: <b><?= strtoupper($edit_value[0]->m_cust_name) ?></b></p>

        <table class="table">
            <thead>
                <tr>
                    <?php if ($type == 1) {
                        echo '<th class="Border-0">S.No</th>
                            <th class="Border-0">Amount</th>
                            <th class="Border-0 text-center" colspan="3">Method</th>';
                    } else {
                        echo '<th>Crate Type</th>
                        <th class="text-center">Quantity</th>';
                    } ?>

                </tr>
            </thead>
            <?php if ($type == 1) { ?>
                <tbody>

                    <tr>
                        <td>1.</td>
                        <td> ₹ <?= $edit_value[0]->m_recvd_amount ?></td>
                        <td colspan="2" class="text-center"><?= $edit_value[0]->m_group_name ?></td>

                    </tr>
                    <tr>
                        <td colspan="1" class="text-end">Amount</td>
                        <td> ₹ <?= $edit_value[0]->m_recvd_amount ?></td>
                        <td></td>
                        <td class=""></td>
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
                        <td colspan="4" class="border-none p-0">Total Discount : <?= $customer_today_balance['discount_amt'] ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="border-none p-0">Total Balance:<?= ($customer_old_balance['balance_amount'] + $customer_today_balance['balance_amount']) ?></td>
                    </tr>
                </tbody>
            <?php } else { ?>

                <tbody>

                    <?php
                    $total_qty = 0;
                    foreach ($edit_value as $key) {
                        $total_qty += $key->m_recvd_qty;
                        echo ' <tr>
                                        <td>' . $key->m_itgrp_title . '</td>
                                        <td>' . $key->m_recvd_qty . '</td>
       
                                        </tr>';
                    }
                    ?>

                    <tr>

                        <td colspan="1" class=""><span>Total: </span><?= $total_qty ?></td>
                        <td class=""></td>

                    </tr>

                </tbody>

            <?php } ?>

        </table>
        <?php if ($type == 2) { ?>
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
        <?php } ?>
        </div>
    </section>
</body>

</html>