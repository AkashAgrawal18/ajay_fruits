<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AK Fruits Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .bill-area {
            width: 100%;
            border: 1px solid #000;
            padding: 10px;
        }

        .center-text {
            text-align: center;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background-color: #4A4A4A;
            color: white;
            border: 1px solid black;
            text-align: left;
        }

        .table td {
            border: 1px solid black;
            text-align: left;
        }

        .border-none {
            border: none !important;
        }

        .bill-total p {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <?php if (!empty($data)) { ?>
        <section class="bill-area">
            <div>
                <table>
                    <tr>
                        <td>Billing Details</td>
                        <td style="text-align:right;"><strong>Bill Of Supply</strong></td>
                    </tr>
                    <tr>
                        <td><?= $data->cust_detail->m_cust_name ?></td>
                        <td style="text-align:right;"><strong>Ajay Khushwaha & Company</strong></td>
                    </tr>
                    <tr>
                        <td><?= $data->cust_detail->m_cust_mobile ?></td>
                        <td style="text-align:right;"><strong>9273333005,9273444495</strong></td>
                    </tr>
                    <tr>
                        <td><?= $data->cust_detail->m_cust_address ?></td>
                        <td style="text-align:right;"><strong>Thanod- Anjora District- Durg,(C.G) 491001</strong></td>
                    </tr>
                    <tr>
                        <td><strong> <?= !empty($data->sale_data) ? 'Invoice' : 'Recipt' ?> No:</strong> <?= $data->invoice_no ?></td>
                        <td style="text-align:right;"><strong>Date:</strong> <?= date('d/m/Y', strtotime($sal_date)) ?></td>
                    </tr>
                </table>
            </div>
            <?php $cret10 = $cret20 = $cret25 = 0;
            if (!empty($data->sale_data)) { ?>
                <div>
                    <table class="table">
                        <thead style="background-color: gray;">
                            <tr>
                                <th>ITEM</th>
                                <th>QTY</th>
                                <th>Rate</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($data->sale_data as $key) {
                                if ($key->m_item_crate == 20) {
                                    $cret10 += $key->m_sale_qty;
                                } else if ($key->m_item_crate == 13) {
                                    $cret20 += $key->m_sale_qty;
                                } else if ($key->m_item_crate == 14) {
                                    $cret25 += $key->m_sale_qty;
                                }
                                echo '<tr>
                        <td>' . $key->m_item_name . '</td>
                        <td>' . $key->m_sale_qty . ' ' . $key->unitname . '</td>
                        <td>' . $key->m_sale_price . '</td>
                        <td>' . $key->m_sale_total . '</td>
                    </tr>';
                            } ?>
                            <tr>
                                <td colspan="4" class="border-none"></td>
                            </tr>
                            <tr>
                                <td class="border-none"><strong>Total Qty</strong></td>
                                <td class="border-none"><strong><?= $data->total_sqty ?></strong></td>
                                <td class="border-none"><strong>Total</strong></td>
                                <td class="border-none"><strong><?= $data->sub_total ?></strong></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="border-none"></td>
                                <td class="border-none"><strong>Freight</strong></td>
                                <td><strong><?= $data->total_expense ?></strong></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="border-none"></td>
                                <td class="border-none"><strong>Final Amount</strong></td>
                                <td><strong><?= $data->grand_total ?></strong></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            <?php }
            $balanceFields = [
                '10 KG' => $cret10,
                '20 KG' => $cret20,
                '25 KG' => $cret25
            ]; ?>

            <div>
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Amount</th>
                            <?php foreach ($data->crate_data as $kry) {
                                echo '<th>' . $kry->m_itgrp_title . '</th>';
                            } ?>
                        </tr>

                    </thead>
                    <tbody>
                        <tr>
                            <td>Old Balance</td>
                            <td><?= $customer_old_balance['balance_amount'] ?></td>
                            <?php foreach ($customer_old_balance['crateitems'] as $kry) {
                                echo '<td>' . $kry['balance'] . '</td>';
                            } ?>
                        </tr>
                        <tr>
                            <td>Today Sale</td>
                            <td><?= $data->grand_total ?></td>
                            <td><?= $cret10 ?></td>
                            <td><?= $cret20 ?></td>
                            <td><?= $cret25 ?></td>

                        </tr>
                        <tr>
                            <td>Today Receive</td>
                            <td><?= $data->total_recieve ?></td>
                            <?php foreach ($data->crate_data as $kry) {
                                echo '<td>' . $kry->total_qty . '</td>';
                            } ?>
                        </tr>
                        <tr>
                            <td>Discount</td>
                            <td><?= $data->total_discount ?></td>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <td>Total Balance</td>
                            <td><?= ($customer_old_balance['balance_amount'] + $data->grand_total - $data->total_recieve - $data->total_discount) ?></td>
                            <?php foreach ($customer_old_balance['crateitems'] as $cau => $kry) {

                                if ($kry['name'] == $data->crate_data[$cau]->m_itgrp_title) {
                                    echo '<td>' . ($kry['balance'] +  $balanceFields[$kry['name']] - $data->crate_data[$cau]->total_qty) . '</td>';
                                }
                            } ?>
                        </tr>

                    </tbody>
                </table>
            </div>

            <div>
                <h4>Bank Details</h4>
                <table>
                    <tr>
                        <td><strong>Account Name</strong></td>
                        <td><strong>Ajay Khushwaha & Company</strong></td>
                        <td rowspan="4">
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Bank Name</strong></td>
                        <td><strong>ICICIBANK,BRANCH-ANJORA</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Account Number</strong></td>
                        <td><strong>328405000160</strong></td>
                    </tr>
                    <tr>
                        <td><strong>IFSC CODE</strong></td>
                        <td><strong>ICIC0003284</strong></td>
                    </tr>
                </table>

            </div>
            <div>
                <h4>Terms & Conditions</h4>
                <table>
                    <tr>
                        <td>1)If you do not deposit empty carat for a time, then its amount will be added to your account.</td>
                    </tr>
                    <tr>
                        <td>2)Goods once sold cannot be taken back.</td>
                    </tr>
                    <tr>
                        <td>3)Interest @21% will be charged on bills ,if not paid within stipulated period.</td>
                    </tr>
                    <tr>
                        <td>4)Subject to Durg jurisdiction.</td>
                    </tr>

                </table>

            </div>

        </section>
    <?php } else { ?>
        <section class="bill-area">
            <div>
                <table>
                    <tr>
                        <td>Billing Details</td>
                        <td style="text-align:right;"><strong>Bill Of Supply</strong></td>
                    </tr>
                    <tr>
                        <td><?= $customer_old_balance['cust_name'] ?></td>
                        <td style="text-align:right;"><strong>Ajay Khushwaha & Company</strong></td>
                    </tr>
                    <tr>
                        <td><?= $customer_old_balance['cust_mobile'] ?></td>
                        <td style="text-align:right;"><strong>9273333005,9273444495</strong></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="text-align:right;"><strong>Thanod- Anjora District- Durg,(C.G) 491001</strong></td>
                    </tr>

                </table>
            </div>

            <div>

                <p>Balance Amount : <?= $customer_old_balance['balance_amount'] ?></p>

                <?php foreach ($customer_old_balance['crateitems'] as $kry) {
                    echo '<p>' . $kry['name'] . "- " . $kry['balance'] . '</p>';
                } ?>

            </div>

            <div>
                <h4>Bank Details</h4>
                <table>
                    <tr>
                        <td><strong>Account Name</strong></td>
                        <td><strong>Ajay Khushwaha & Company</strong></td>
                        <td rowspan="4">
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Bank Name</strong></td>
                        <td><strong>ICICIBANK,BRANCH-ANJORA</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Account Number</strong></td>
                        <td><strong>328405000160</strong></td>
                    </tr>
                    <tr>
                        <td><strong>IFSC CODE</strong></td>
                        <td><strong>ICIC0003284</strong></td>
                    </tr>
                </table>

            </div>
            <div>
                <h4>Terms & Conditions</h4>
                <table>
                    <tr>
                        <td>1)If you do not deposit empty carat for a time, then its amount will be added to your account.</td>
                    </tr>
                    <tr>
                        <td>2)Goods once sold cannot be taken back.</td>
                    </tr>
                    <tr>
                        <td>3)Interest @21% will be charged on bills ,if not paid within stipulated period.</td>
                    </tr>
                    <tr>
                        <td>4)Subject to Durg jurisdiction.</td>
                    </tr>

                </table>

            </div>

        </section>
    <?php } ?>
</body>

</html>