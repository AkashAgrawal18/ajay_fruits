<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <title>Fruit Bill</title>
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

        .bill-area h6 {
            font-size: 10px;
            margin: 0px 0px 1px 4px;
            padding: 0.1rem;

        }

        .bill-area h2 {
            font-size: 11px;
            font-weight: bold;
            margin: 0px 0px 1px 4px;
            padding: 0.1rem;

        }

        p {
            font-size: 10px;
            margin: 0px 0px 1px 4px;
            padding: 0.1rem;
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
                <!-- <h1>AK</h1> -->
                <h3>AJAY KUSHWAHA & COMPANY</h3>
                <!-- <h3>BRANCH - DURG & NAGPUR</h3> -->
                <!-- <h1><? // = get_settings('m_app_name') 
                            ?></h1> -->
                <!-- <h3>PH No. -<? // = get_settings('m_app_mobile') 
                                    ?></h3>
                <h4><? // = get_settings('m_app_address') 
                    ?></h4> -->
            </div>
        </div>
        <h1 class="my-1 fw-bold"><?= $pagename ?></h1>
        <p class="mb-1">Agent: <b><?= $user_details[0]->m_user_name ?></b></p>
        <p class="mb-1">Date: <b><?php echo date('d-m-Y') ?></b></p>

        <?php if ($type == 1) { ?>

            <table class="table" id="myTable">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Cash</th>
                        <th>Account</th>
                    </tr>
                </thead>

                <tbody>

                    <?php
                    $cash_amt = 0;
                    $online_amt = 0;
                    if (!empty($all_value)) {
                        foreach ($all_value as $key) {
                            if ($key['method_type'] == 4) {
                                $cash_amt += $key['m_recvd_amount'];
                            } else if ($key['method_type'] == 3) {
                                $online_amt += $key['m_recvd_amount'];
                            }

                            $expense_amt = isset($expense) ? $expense->m_exp_amount : 0;
                            $balance_amt = ((float)$cash_amt - (float)$expense_amt);
                    ?>

                            <tr>
                                <td><?= $key['m_cust_name'] ?></td>
                                <td><?= $key['method_type'] == 4 ? $key['m_recvd_amount'] : 'X' ?></td>
                                <td><?= $key['method_type'] == 3 ? $key['m_recvd_amount'] : 'X'  ?></td>

                            </tr>

                    <?php }
                    } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td style='font-weight:bold'>Total</td>
                        <td style='font-weight:bold'><?= $cash_amt ?></td>
                        <td style='font-weight:bold'><?= $online_amt ?></td>
                    </tr>
                    <tr>
                        <td style='font-weight:bold'>Expense</td>
                        <td style='font-weight:bold'><?= $expense_amt ?></td>
                        <td style='font-weight:bold'></td>
                    </tr>
                    <tr>
                        <td style='font-weight:bold'>Total Balance</td>
                        <td style='font-weight:bold'><?= $balance_amt ?></td>
                        <td style='font-weight:bold'><?= $online_amt ?></td>
                    </tr>
                </tfoot>

            </table>

        <?php

        } else if ($type == 2) { ?>

            <table class="table" id="myTable">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <?php foreach ($crate_list as $kry) {
                            echo ' <th>' . $kry->m_itgrp_title . '</th>';
                        } ?>
                    </tr>
                </thead>

                <tbody>

                    <?php 
                    $c20 = 0;
                    $c25 = 0;
                    $c10 = 0;
                    if (!empty($all_value)) {
                        foreach ($all_value as $key) { ?>
                            <tr>
                                <td><?= $key['m_cust_name'] ?></td>

                                <?php $j = 0;
                                foreach ($crate_list as $kty) {

                                    if ($j < count($key['m_crate_list'])) {
                                        if ($kty->m_itgrp_id == $key['m_crate_list'][$j]->m_crate_id) {
                                            $recived_qty = $key['m_crate_list'][$j]->m_recvd_qty;
                                            $j++;
                                        } else {
                                            $recived_qty = 0;
                                        }
                                    } else {
                                        $recived_qty = 0;
                                    }

                                    if ($kty->m_itgrp_title == '10 KG') {
                                        $c10 += $recived_qty;
                                    } else if ($kty->m_itgrp_title == '20 KG') {
                                        $c20 += $recived_qty;
                                    } else if ($kty->m_itgrp_title == '25 KG') {
                                        $c25 += $recived_qty;
                                    }

                                    echo '<td>' . $recived_qty . '</td>';
                                } ?>
                            </tr>

                    <?php }
                    } ?>
                </tbody>
                <tfoot>
                    <td style='font-weight:bold'>Total</td>
                    <td style='font-weight:bold'><?= $c20 ?></td>
                    <td style='font-weight:bold'><?= $c25 ?></td>
                    <td style='font-weight:bold'><?= $c10 ?></td>
                </tfoot>

            </table>

            <?php  } else if ($type == 3) {
            if (!empty($all_value)) {
                foreach ($all_value as $key) { ?>
                    <div class="card">
                        <div class="row">
                            <div class="col-6 ">
                                <h6><b>Customer</b></h6>
                                <h6><?= $key->m_cust_name ?></h6>
                            </div>
                            <div class="col-6">
                                <h6><b>Mobile </b></h6>
                                <h2><?= $key->m_cust_mobile ?></h2>
                            </div>
                            <div class="col-12">
                                <h2><?= $key->m_cust_address ?></h2>
                            </div>
                            <div class="col-4">
                                <h2 class="text-center"><?= $key->total_qty ?></h2>
                                <h6 class="text-center"><b>T.Qty</b></h6>
                            </div>
                            <div class="col-4 p-0">
                                <h2 class="text-center">₹<?= $key->total_expense ?></h2>
                                <h6 class="text-center"><b>Expense</b></h6>
                            </div>
                            <div class="col-4 p-0">
                                <h2>₹<?= ($key->sub_total + $key->total_expense)  ?></h2>
                                <h6><b>Total</b></h6>
                            </div>

                        </div>
                    </div>
        <?php }
            }
        } ?>
    </section>
</body>
<script>
    // Function to calculate and update column totals
    // function calculateColumnTotals() {
    //     const table = document.getElementById('myTable');
    //     const tbody = table.querySelector('tbody');
    //     const tfoot = table.querySelector('tfoot');
    //     const columns = table.querySelectorAll('tbody tr td');

    //     const columnTotals = Array.from({
    //         length: columns.length
    //     }, () => 0);

    //     // Iterate through each row in the tbody
    //     tbody.querySelectorAll('tr').forEach((row) => {
    //         row.querySelectorAll('td').forEach((cell, columnIndex) => {
    //             // Parse cell content to a number (assuming numeric values)
    //             const cellValue = parseFloat(cell.textContent);

    //             if (!isNaN(cellValue)) {
    //                 // Add cell value to the respective column total
    //                 columnTotals[columnIndex] += cellValue;
    //             }
    //         });
    //     });

    //     // Update the tfoot row with the calculated column totals
    //     tfoot.querySelectorAll('td').forEach((totalCell, index) => {
    //         if (index != 0) {
    //             totalCell.textContent = columnTotals[index];
    //         }

    //     });
    // }

    // Call the function to calculate and update column totals
    // calculateColumnTotals();
</script>

</html>