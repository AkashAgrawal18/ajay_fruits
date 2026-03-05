<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truck Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

</head>
<style>
    #salelistModal {

        th,
        td {
            text-align: center;
            width: auto;
        }

        thead,
        tfoot,
        thead th,
        tfoot th {
            background: #0000000d !important;
            color: #000000c4 !important;
            font-size: 14px;
            line-height: 18px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }
    }

    th,
    td {
        text-align: center;
        width: 25%;
    }

    .tb th {
        padding: 2px !important;
    }

    .tb td {
        padding: 2px !important;
    }

    body {
        overflow-x: hidden;
    }

    .tb2 td {
        width: 33.33%;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .bill-area .container {
            max-width: 100%;
            margin: 0px;
            width: 100%;

        }
    }
</style>

<body>

    <div class="rr">
        <section class="py-1" style="background: #bbf;">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h6 class="m-0 text-white">
                            Home >> <span class="text-primary"><?= $pagename ?></span>
                        </h6>
                    </div>
                    <div class="col-6 text-end">
                        <!-- <button type="button" data-bs-toggle="modal" data-bs-target="#myAddModal" class="btn btn-primary btn-sm" title="Add New">Add New</button> -->
                        <a onclick="printcustomdiv()" class="btn btn-success btn-sm">
                            <i class="bi bi-printer me-2"></i>Print
                        </a>
                        <a onclick="window.history.go(-1)" class="btn btn-danger btn-sm">
                            <i class="bi bi-box-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="printDiv">
        <h4 class="text-center mt-5">AK</h4>
        <h5 class="text-center">TRUCK REPORT - FROM <?= date('d/m/Y', strtotime($from_date)) ?> To <?= date('d/m/Y', strtotime($to_date)) ?></h5>

        <?php if (!empty($all_value)) {
            foreach ($all_value as $key) { ?>
                <table class="table table-bordered border-dark mt-5 marg">
                    <thead>
                        <tr>
                            <th></th>
                            <th>PURCHASE</th>
                            <th>SALES</th>
                            <th>EXPENSE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-start">Date :</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-start"> <?= date('d/m/Y', strtotime($key['Pdate'])) ?> </h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-start">Challan No :</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-start"><?= $key['Challan_no'] ?></h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-start">Truck No :</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-start"><?= $key['truck_no'] ?></h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-start">Supplier :</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-start"><?= $key['Supplier_name'] ?></h6>
                                    </div>

                                </div>
                            </td>
                            <td onclick="lotwise_sale_fun(`<?= $key['Challan_no'] ?>`,1)">
                                <div class="row">
                                    <div class="col-md-4 ">
                                        <h6 class="text-start">QTY </h6>
                                    </div>
                                    <div class="col-md-8 text-end">
                                        <h6> <?= $key['pur_qty'] ?></h6>
                                    </div>
                                    <div class="col-md-4 ">
                                        <h6 class="text-start">WEIGHT </h6>
                                    </div>
                                    <div class="col-md-8 text-end">
                                        <h6> <?= $key['pur_weight'] ?> Kg.</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-start">TOTAL AMOUNT </h6>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6>₹ <?= $key['pur_amount'] ?></h6>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <h6 class="text-start">Commission </h6>
                                    </div>
                                    <div class="col-md-6 text-end mt-4">
                                        <h6>₹ <?= $key['pur_comm'] ?></h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-start">Freight </h6>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6>₹ <?= $key['pur_fright'] ?></h6>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="text-start">Hamali </h6>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6>₹ <?= $key['pur_hamali'] ?></h6>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="text-start">Charity </h6>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6>₹ <?= $key['pur_charity'] ?></h6>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="text-start">Packaging </h6>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6> ₹ <?= $key['pur_packaging'] ?></h6>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="text-start">Loading </h6>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6>₹ <?= $key['pur_loading'] ?></h6>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="text-start">Advance</h6>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6>₹ <?= $key['pur_advance'] ?></h6>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="text-start">Others </h6>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6>₹ <?= $key['pur_others'] ?></h6>
                                    </div>
                                    <hr>
                                    <div class="col-md-6">
                                        <h6 class="text-start">NET PURCHASE </h6>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6>₹ <?= $key['pur_netamount'] ?></h6>
                                    </div>
                                </div>
                            </td>
                            <td onclick="lotwise_sale_fun(`<?= $key['Challan_no'] ?>`,2)">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h6 class="text-start">QTY </h6>
                                    </div>
                                    <div class="col-md-8 text-end">
                                        <h6> <?= $key['sale_qty'] ?></h6>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="text-start">WEIGHT </h6>
                                    </div>
                                    <div class="col-md-8 text-end">
                                        <h6> <?= $key['sale_weight'] ?> Kg.</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-start">TOTAL AMOUNT </h6>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6>₹ <?= $key['sale_amount'] ?></h6>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <h6 class="text-start">Commission </h6>
                                    </div>
                                    <div class="col-md-6 text-end mt-4">
                                        <h6>₹ <?= $key['m_sale_comm'] ?></h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-start">Freight </h6>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6>₹ 0<? //= $key['m_sale_fright'] 
                                                ?></h6>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="text-start">Hamali </h6>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6>₹ <?= $key['m_sale_hamali'] ?></h6>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="text-start">Others </h6>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6>₹ <?= $key['m_sale_others'] ?></h6>
                                    </div>

                                    <div class="col-md-6">
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6> 0.00</h6>
                                    </div>

                                    <div class="col-md-6 text-start">
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6>0.00</h6>
                                    </div>

                                    <div class="col-md-6 text-start">
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6>0.00</h6>
                                    </div>

                                    <div class="col-md-6 text-start">
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6>0.00</h6>
                                    </div>
                                    <hr>
                                    <div class="col-md-6 text-start">
                                        <h6>NET SALES </h6>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6>₹ <?= ($key['sale_netamount'] + $key['saleexp'] - $key['m_sale_fright']) ?></h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="row">
                                    <?php $totexp = 0;
                                    if (!empty($key['internal_expense'])) {

                                        foreach ($key['internal_expense'] as $exp) {
                                            $totexp += $exp->m_exp_amount ?>
                                            <div class="col-md-8 text-start">
                                                <h6><?= $exp->expense_name ?> </h6>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <h6>₹ <?= $exp->m_exp_amount ?> </h6>
                                            </div>
                                    <?php }
                                    } ?>

                                    <!-- <div class="col-md-4 text-start">
                                    <h6>LABOUR ACCOUNT </h6>
                                </div>
                                <div class="col-md-8 text-end">
                                    <h6> 1000</h6>
                                </div>
                                <div class="col-md-6 text-start">
                                    <h6>ANOTHER TRANSPORTER</h6>
                                </div>
                                <div class="col-md-6 text-end">
                                    <h6> 5000.00</h6>
                                </div> -->
                                </div>
                                <table class="table table-bordered border-dark mt-5 tb2">
                                    <thead class="tb">
                                        <tr>
                                            <th colspan="3">PROFIT & LOSS</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tb">
                                        <tr>
                                            <td class="text-center fw-bold"></td>
                                            <td class="text-center fw-bold">PURCHASE</td>
                                            <td class="text-center fw-bold">₹ <?= $key['pur_netamount'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center fw-bold">+</td>
                                            <td class="text-center fw-bold">EXPENSES</td>
                                            <td class="text-center fw-bold">₹ <?= $totexp ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center fw-bold">=</td>
                                            <td class="text-center fw-bold">TOTAL</td>
                                            <td class="text-center fw-bold">₹ <?= ($key['pur_netamount'] + $totexp) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center fw-bold">-</td>
                                            <td class="text-center fw-bold">SALES</td>
                                            <td class="text-center fw-bold">₹ <?= ($key['sale_netamount'] + $key['saleexp'] - $key['m_sale_fright']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center fw-bold">=</td>
                                            <td class="text-center fw-bold"></td>
                                            <td class="text-center fw-bold">₹ <?= (($key['sale_netamount'] + $key['saleexp'] - $key['m_sale_fright']) - ($key['pur_netamount'] + $totexp)) ?></td>
                                        </tr>
                                    </tbody>
                                </table>

                            </td>
                        </tr>

                    </tbody>
                </table>
        <?php }
        } ?>
    </div>

    <!-- view Modal start -->
    <div class="modal fade" id="salelistModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="row" style="display: contents;">
                        <div class="col-md-7">
                            <h4 class="modal-title"></h4>
                        </div>
                        <div class="col-md-5" style="text-align: end;">
                            <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-close"></i></button>
                        </div>
                    </div>
                </div>


                <div class="modal-body printDiv" style="word-break: break-all">

                    <div class="row bodydiv" id="viewsalesdiv">
                        <div class="col-md-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead id="tablehead">

                                    </thead>
                                    <tbody id="tablebody">

                                    </tbody>
                                    <tfoot id="tablefoot">

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

</body>

</html>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>

<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

<script>
    function lotwise_sale_fun(purspo, funtype) {

        $.ajax({
            url: "<?php echo site_url('Reports/purchase_sales_list'); ?>",
            type: "POST",
            data: {
                purspo,
                funtype
            },
            dataType: "JSON",
            success: function(data) {

                let qtytotal = 0;
                let wgttotal = 0;
                let nettotal = 0;

                $('#tablebody').empty();
                $.each(data, function(i, item) {

                    if (funtype == 1) {

                        qtytotal += parseFloat(item.m_purcs_qty);
                        wgttotal += parseFloat(item.m_purcs_weight);
                        nettotal += parseFloat(item.m_purcs_total);

                        $('#tablebody').append(`
                      <tr>
                                                             
                                                              <td>${item.m_item_name}</td>
                                                              <td>${item.m_purcs_lot} (${item.m_purcs_id})</td>
                                                              <td>${item.m_purcs_qty}</td>
                                                              <td>${item.m_purcs_weight}</td>
                                                              <td>${item.m_purcs_price}</td>
                                                              <td>${item.m_purcs_total}</td>
                                                           <td><a href="<?php echo base_url('Sales/add_purchase?id=') ?>${item.m_purcs_spo}" class="btn btn-info btn-sm p-1" title="Edit" data-toggle="tooltip">Edit</a></td>
                                                          </tr>
                                 `);
                                 $('#tablefoot').html(`
                                                      <th colspan="2">Total</th>
                                                      <th>${qtytotal}</th>
                                                      <th>${wgttotal}</th>
                                                      <th></th>
                                                      <th>${nettotal}</th>
                                                      <th></th>
            `);
                    } else {

                        qtytotal += parseFloat(item.m_sale_qty);
                        nettotal += parseFloat(item.m_sale_total);

                        $('#tablebody').append(`
                      <tr>
                                                              <td>${item.m_sale_spo}</td>
                                                              <td>${item.m_sale_date}</td>
                                                              <td>${item.m_cust_name}</td>
                                                              <td>${item.m_item_name}</td>
                                                              <td>${item.m_sale_lot}</td>
                                                              <td>${item.m_sale_qty}</td>
                                                              <td>${item.m_sale_price}</td>
                                                              <td>${item.m_sale_total}</td>
                                                              <td><a href="<?php echo base_url('Sales/add_sales?id=') ?>${item.m_sale_spo}" class="btn btn-info btn-sm p-1" title="Edit" data-toggle="tooltip">Edit</a></td>
                                                          </tr>
                      `);
                      $('#tablefoot').html(`
                                                      <th colspan="5">Total</th>
                                                      <th>${qtytotal}</th>
                                                      <th></th>
                                                      <th>${nettotal}</th>
                                                      <th></th>
            `);
                    }

                });

            }

        });

        if (funtype == 1) {
            $('.modal-title').html(`Purchase Details (${purspo})`);

            $('#tablehead').html(`
             <th>Item</th>
             <th>LotNo</th>
             <th>Quantity</th>
             <th>Weight</th>
             <th>Rate</th>
             <th>Total</th>
             <th></th>
           `);
           

        } else {
            $('.modal-title').html(`Sale Details (${purspo})`);

            $('#tablehead').html(`
             <th>SaleNo</th>
             <th>Date</th>
             <th>Customer</th>
             <th>Item</th>
             <th>LotNo</th>
             <th>Qty</th>
             <th>Rate</th>
             <th>Total</th>
             <th></th>
           `);
           


        }


        $('#salelistModal').modal('show');

    }

    function preventBack() {
        window.history.forward();
    }
    setTimeout("preventBack()", 0);
    window.onunload = function() {
        null
    };

    function printcustomdiv() {
        printDiv = ".printDiv"; // id of the div you want to print
        $("*").addClass("no-print");
        $(printDiv + " *").removeClass("no-print");
        $(printDiv).removeClass("no-print");

        parent = $(printDiv).parent();
        while ($(parent).length) {
            $(parent).removeClass("no-print");
            parent = $(parent).parent();
        }
        window.print();

    }
</script>