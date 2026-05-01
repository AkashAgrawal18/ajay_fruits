<?php include("head.php"); ?>
<?php include("header.php"); ?>
<!-- ========== Page Content ========== -->
<section class="py-1" style="background: #bbf;">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-6">
                <h6 class="m-0 text-white">
                    Dashboard
                </h6>
            </div>
            <div class="col-6 text-end">

                <a href="<?= base_url('Welcome/send_balance_sms') ?>" class="btn btn-info btn-sm">Send Balance Reminder SMS</a>
                <button onclick="history.back()" class="btn btn-danger btn-sm">
                    <i class="bi bi-box-arrow-left me-2"></i>Exit
                </button>
            </div>
        </div>
    </div>
</section>

<!-- <section class="m-2 d-flex align-items-center">
    <div class="row align-items-stretch g-4 py-2 w-100"> -->

<!-- <div class="col-9">
            <div class="row">
                <?php // if (isset($dashcounts->account_dels)) {
                //         foreach ($dashcounts->account_dels as $key => $value) {
                //             echo '<div class="col-2">
                //  <a href="#" class="text-decoration-none">
                // <div class="card card-cover overflow-hidden text-white bg-dark rounded-4 shadow-lg" style="background: linear-gradient(135deg, #7a7af9,#080808);">
                //    <div class="d-flex flex-column pt-4 pb-3 text-center px-3 text-white text-shadow-1" style="min-height: 7.2rem; max-height: 7.5rem;">
                //        <h6 class="fw-bold">₹ ' . $value->opening_bal . '</h6>
                //            <small class="fw-bold mb-0">' . $value->acct_name . '</small>
                //              </div>
                //          </div>
                //      </a>
                // </div>
                // ';
                //         }
                //     } 
                ?>


            </div>
        </div> -->
<!-- <div class="col-3">
            <div class="row">
                <div class="col-6">
                    <a href="<? //= base_url('Reports/supplier_blncrate_report') 
                                ?>" class="text-decoration-none">
                        <div class="card card-cover overflow-hidden text-white bg-dark rounded-4 shadow-lg" style="background: linear-gradient(135deg, #7a7af9,#080808);">
                            <div class="d-flex flex-column pt-4 pb-3 text-center px-3 text-white text-shadow-1" style="min-height: 7.2rem; max-height: 7.5rem;">
                                <h6 class="fw-bold">₹ <? //= $dashcounts->spcash_outstan 
                                                        ?></h6>
                                    <smallIND_money_format( class="fw-bold) mb-0">Total Payable</smallIND_money_format>
                                    <small class="mb-0">To Supplier</small>

                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-6">
                    <a href="<? //= base_url('Reports/cust_blncrate_report') 
                                ?>" class="text-decoration-none">
                        <div class="card card-cover overflow-hidden text-white bg-dark rounded-4 shadow-lg" style="background: linear-gradient(135deg, #7a7af9,#080808);">
                            <div class="d-flex flex-column pt-4 pb-3 text-center px-3 text-white text-shadow-1" style="min-height: 7.2rem; max-height: 7.5rem;">
                                <h6 class="fw-bold">₹ <? //= $dashcounts->cash_outstan 
                                                        ?></h6>
                                    <small class="fw-bold mb-0" style="font-size: 0.99rem;">Total Receivable</small>
                                    <small class="mb-0">From Customer</small>

                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div> -->

<!-- </div>

</section> -->

<section class="m-2 d-flex align-items-center">
    <div class="row align-items-stretch g-4 py-2 w-100">
        <div class="col-md-2">
            <a href="<?= base_url('Sales/send_individual_statement') ?>" class="text-decoration-none">
                <div class="card card-cover text-white" style="background: linear-gradient(135deg, #a6a6ff, #010081);">
                    <div class="d-flex flex-column text-center text-white py-2">
                        <h6 class="fw-bold mb-0">Send Statement</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="<?= base_url('Sales/send_bill_indiviouly') ?>" class="text-decoration-none">
                <div class="card card-cover text-white" style="background: linear-gradient(135deg, #a6a6ff, #010081);">
                    <div class="d-flex flex-column text-center text-white py-2">
                        <h6 class="fw-bold mb-0">Send Summary</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="<?= base_url('Sales/Reminder_list') ?>" class="text-decoration-none">
                <div class="card card-cover text-white" style="background: linear-gradient(135deg, #a6a6ff, #010081);">
                    <div class="d-flex flex-column text-center text-white py-2">
                        <h6 class="fw-bold mb-0">Outstanding Reminder</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="<?= base_url('Sales/purchase_item_list') ?>" class="text-decoration-none">
                <div class="card card-cover text-white" style="background: linear-gradient(135deg, #a6a6ff, #010081);">
                    <div class="d-flex flex-column text-center text-white py-2">
                        <h6 class="fw-bold mb-0">Stock In & Out report </h6>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>

<section class="py-4 d-flex align-items-center" style="background:#f3f3ff;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <div class="card mypiecard" style="padding:10px">
                    <h4>Accounts Balance</h4>
                    <hr style="margin-top: 10px;margin-bottom: 10px;width: 100%;border-top: 1px solid #727272;">

                    <canvas id="mypieChart"></canvas>
                </div>
            </div>

            <div class="col-md-9">

                <div class="row">
                    <div class="col-3">
                        <h5>Agent Day Summary</h5>
                    </div>
                    <div class="col-2 mb-2">
                        <form action="<?= base_url('Welcome') ?>" method="post">
                            <input type="date" name='date' id="date_in" class="form-control" onchange="this.form.submit()" value="<?= $date ?>">
                        </form>
                    </div>
                </div>

                <div class="table-responsive bg-light">

                    <form action="<?= base_url('Reports/cust_blncrate_report') ?>" id="frm-drttr" method="post">
                        <input type="hidden" name='from_date' class="form-control" value="<?= $date ?>">
                        <input type="hidden" name='to_date' class="form-control" value="<?= $date ?>">
                        <input type="hidden" name='agent' id="agent_in" value="o">
                    </form>

                    <table id="group_tbl" class="my_custom_datatable table table-striped table-bordered dt-responsive nowra">
                        <thead>
                            <tr class="text-center">

                                <th colspan="3" class="text-start">Date : <?= date('d-m-Y', strtotime($date)) ?></th>
                                <!-- <th colspan="3">Crate Outstanding</th> -->
                                <th colspan="3">Total Issue</th>
                                <th colspan="3">Total Sale</th>
                                <th colspan="2">Total Collection</th>
                                <th>Return Item</th>


                            </tr>
                            <tr>
                                <th>Group</th>
                                <th>Agent</th>
                                <th>Outstanding</th>
                                <!-- <th>10kg</th>
                        <th>20kg</th>
                        <th>25kg</th> -->
                                <th>Qty</th>
                                <th>Amt</th>
                                <th>Crate</th>
                                <th>Qty</th>
                                <th>Amt</th>
                                <th>Crate</th>
                                <th>Cash Recieved</th>
                                <th>Crate Recieved</th>
                                <th>Qty</th>


                            </tr>
                        </thead>
                        <tbody>

                            <?php

                            $sum_cash_outstanding = 0;
                            // $sum_crt10_outstand = 0;
                            // $sum_crt20_outstand = 0;
                            // $sum_crt25_outstand = 0;
                            $sum_issue_qty = 0;
                            $sum_issue_amt = 0;
                            $sum_issue_crate = 0;
                            $sum_sale_qty = 0;
                            $sum_sale_amt = 0;
                            $sum_sale_crate = 0;
                            $sum_cash_collected = 0;
                            $sum_crate_recieved = 0;
                            $sum_return_qty = 0;

                            if (!empty($day_report)) {
                                foreach ($day_report as $value) {

                                    $sum_cash_outstanding += $value->cash_outstanding;
                                    // $sum_crt10_outstand += $value->crt10_outstand;
                                    // $sum_crt20_outstand += $value->crt20_outstand;
                                    // $sum_crt25_outstand += $value->crt25_outstand;
                                    $sum_issue_qty += $value->issue_qty;
                                    $sum_issue_amt += $value->issue_amt;
                                    $sum_issue_crate += $value->issue_crate;
                                    $sum_sale_qty += $value->sale_qty;
                                    $sum_sale_amt += $value->sale_amt;
                                    $sum_sale_crate += $value->sale_crate;
                                    $sum_cash_collected += $value->cash_collected;
                                    $sum_crate_recieved += $value->crate_recieved;
                                    $sum_return_qty += $value->return_qty;

                            ?>
                                    <tr>
                                        <td class="tdbtnoutstd" data-group="<?= $value->user_group ?>"><?= $value->group_name ?></td>
                                        <td class="tdbtnoutstd" data-group="<?= $value->user_group ?>"><?= $value->staff_name ?></td>
                                        <td class="tdbtnoutstd" data-group="<?= $value->user_group ?>">₹<?= IND_money_format($value->cash_outstanding) ?></td>
                                        <!-- <td class="tdbtnoutstd" data-group="<? //$value->user_group 
                                                                                    ?>"><? // $value->crt10_outstand 
                                                                                        ?></td>
                                <td class="tdbtnoutstd" data-group="<? //$value->user_group 
                                                                    ?>"><? // $value->crt20_outstand 
                                                                        ?></td>
                                <td class="tdbtnoutstd" data-group="<? //$value->user_group 
                                                                    ?>"><? // $value->crt25_outstand 
                                                                        ?></td> -->
                                        <td class="tdbtnclick" data-userid="<?= $value->user_id ?>" data-group="<?= $value->group_name ?>"><?= $value->issue_qty ?></td>
                                        <td class="tdbtnclick" data-userid="<?= $value->user_id ?>" data-group="<?= $value->group_name ?>">₹<?= IND_money_format($value->issue_amt) ?></td>
                                        <td class="tdbtnclick" data-userid="<?= $value->user_id ?>" data-group="<?= $value->group_name ?>"><?= $value->issue_crate ?></td>
                                        <td class="tdbtnclick" data-userid="<?= $value->user_id ?>" data-group="<?= $value->group_name ?>"><?= $value->sale_qty ?></td>
                                        <td>₹<?= IND_money_format($value->sale_amt) ?></td>
                                        <td><?= $value->sale_crate ?></td>
                                        <td>₹<?= IND_money_format($value->cash_collected) ?></td>
                                        <td><?= $value->crate_recieved ?></td>
                                        <td class="tdbtnclick" data-userid="<?= $value->user_id ?>" data-group="<?= $value->group_name ?>"><?= $value->return_qty ?></td>
                                    </tr>
                            <?php

                                }
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>

                                <th colspan="2">Total</th>
                                <th>₹<?= IND_money_format($sum_cash_outstanding) ?></th>
                                <!-- <th><? // $sum_crt10_outstand 
                                            ?></th>
                        <th><? // $sum_crt20_outstand 
                            ?></th>
                        <th><? // $sum_crt25_outstand 
                            ?></th> -->
                                <th><?= $sum_issue_qty ?></th>
                                <th>₹<?= IND_money_format($sum_issue_amt) ?></th>
                                <th><?= $sum_issue_crate ?></th>
                                <th><?= $sum_sale_qty ?></th>
                                <th>₹<?= IND_money_format($sum_sale_amt) ?></th>
                                <th><?= $sum_sale_crate ?></th>
                                <th>₹<?= IND_money_format($sum_cash_collected) ?></th>
                                <th><?= $sum_crate_recieved ?></th>
                                <th><?= $sum_return_qty ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- view Modal start -->
<div class="modal fade" id="stfdyrptModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row" style="display: contents;">
                    <div class="col-md-5">
                        <h4 class="modal-title">Staff Day Report</h4>
                    </div>
                    <div class="col-md-7" style="text-align: end;">
                        <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-close"></i></button>
                    </div>
                </div>
            </div>

            <div class="modal-body" style="word-break: break-all">

                <h5 id="headitem"></h5>
                <div class="row bodydiv">
                    <div class="col-md-12 mt-3">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <th>Sn</th>
                                    <th>Item Name</th>
                                    <th>Lot No</th>
                                    <th>Issue Qty</th>
                                    <th>Sale Qty</th>
                                    <th>Balance Qty</th>
                                    <th>Return Qty</th>

                                </thead>
                                <tbody id="modalissuebody">

                                </tbody>
                                <tfoot id="tablemodalfoot">

                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>


            </div>

        </div>
    </div>
</div>
<!-- view modal end -->

<!-- ========== Page Content ========== -->
<?php include("footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).on('click', '.tdbtnoutstd', function() {
        var grop = $(this).data('group');

        $('#agent_in').val(grop);
        $('#frm-drttr').submit();

    });
    $(document).on('click', '.tdbtnclick', function() {
        var grop = $(this).data('group');
        var user_id = $(this).data('userid');
        var datein = '<?= $date ?>';
        $.ajax({
            url: "<?php echo site_url('Reports/get_agent_balance_stock'); ?>",
            type: "POST",
            data: {
                user_id,
                datein
            },
            dataType: "JSON",
            success: function(data) {
                // console.log(data);
                $('#headitem').html('Date:- ' + datein + ' Staff :- ' + grop)

                var sum_qty_issue = 0;
                var sum_sale_qty = 0;
                var sum_balance_qty = 0;
                var sum_return_qty = 0;
                $('#modalissuebody').empty();
                $.each(data, function(i, item) {
                    sum_qty_issue += parseFloat(item.total_qty_issue);
                    sum_sale_qty += parseFloat(item.total_sale_qty);
                    sum_balance_qty += parseFloat(item.balance_qty);
                    sum_return_qty += parseFloat(item.total_return_qty);
                    $('#modalissuebody').append(`
                  <tr>
                                                          <td>` + (i + 1) + `</td>
                                                          <td>` + item.m_item_name + `</td>
                                                          <td>` + item.si_issue_lotno + `</td>
                                                          <td>` + item.total_qty_issue + `</td>
                                                          <td>` + item.total_sale_qty + `</td>
                                                          <td>` + item.balance_qty + `</td>
                                                          <td>` + item.total_return_qty + `</td>
                                                      </tr>
                  `);
                });

                $('#tablemodalfoot').html(`
                      <tr>
                                                              <th colspan="3">Total</th>
                
                                                              <th>` + sum_qty_issue + `</th>
                                                              <th>` + sum_sale_qty + `</th>
                                                              <th>` + sum_balance_qty + `</th>
                                                              <th>` + sum_return_qty + `</th>
                                                          </tr>
                      `);
                $('#stfdyrptModal').modal('show');
            }
        });


    });



    var pieData = JSON.parse(`<?php print_r($pie_data); ?>`);

    new Chart(document.getElementById("mypieChart"), {
        type: 'pie',
        data: {

            labels: pieData.label,
            datasets: [{
                backgroundColor: ["#51EAEA", "#FCDDB0",
                    "#FF9D76", "#FB3569"
                ],

                data: pieData.data,
            }]
        },
        options: {
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            // console.log(pieData.total[0]);
                            if (label) {
                                label += ': ';
                            }
                            label += ' Total: ₹';
                            label += pieData.data[context.dataIndex].toLocaleString("hi-IN");
                            label += ', Today: ₹';
                            label += pieData.today[context.dataIndex].toLocaleString("hi-IN");

                            return label;
                        }
                    }
                }
            }

        }
    });
</script>