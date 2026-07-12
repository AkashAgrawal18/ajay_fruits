<!-- ========================Header==============Fix========= -->
<?php $this->view('head'); ?>
<?php $this->view('header'); ?>
<style>
    .modal-dialog {
        width: 90% !important;
        margin: 30px auto;
    }

    .modal {
        --bs-modal-width: 90%;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .printTableDiv {
            overflow: visible !important;
            height: 100vh !important;
        }

        .modal-dialog {
            width: 100% !important;
            margin: 0px auto;
        }

        .modal {

            --bs-modal-width: 100%;
        }

        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            color: var(--bs-modal-color);
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: var(--bs-modal-border-width) solid rgb(0 0 0 / 0%);
            border-radius: var(--bs-modal-border-radius);
            outline: 0;
        }

    }
</style>
<!-- ========== Page Content ========== -->
<section class="py-1" style="background: #bbf;">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-6">
                <h6 class="m-0 text-white">
                    Home >> <span class="text-primary"><?= $pagename ?></span>
                </h6>
            </div>
            <div class="col-6 text-end">
                <a class="btn btn-primary btn-sm" href="<?= base_url('Sales/add_sales') ?>"><i class="bi bi-person-plus-fill"></i> Add New</a>
                <button onclick="history.back()" class="btn btn-danger btn-sm">
                    <i class="bi bi-box-arrow-left me-2"></i>Exit
                </button>
            </div>
        </div>
    </div>
</section>
<section class="py-4 d-flex align-items-center" style="background:#f3f3ff;min-height:70vh;">
    <div class="container-fluid">

        <div class="row justify-content-evenly g-2 mb-2">
            <div class="col-12">
                <form action="<?= base_url('Sales/sales_list') ?>" method="POST" class="row align-items-center">

                    <div class="col-2">
                        <div class="form-group">
                            <label for="">From date</label>
                            <input type="date" max="<?= date('Y-m-d') ?>" name="from_date" id="from_date" class="form-control" value="<?= $from_date ?>">
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="">To date</label>
                            <input type="date" max="<?= date('Y-m-d') ?>" name="to_date" id="to_date" class="form-control" value="<?= $to_date ?>">
                        </div>
                    </div>

                    <div class="col-2">
                        <div class="form-group">
                            <label for="">Group</label>
                            <select name="group_id" id="group_id" class="form-select select2">
                                <option value="">All Group</option>
                                <?php
                                if (!empty($group_dtl)) {
                                    foreach ($group_dtl as $vat) {

                                        if ($group_id == $vat->m_group_id) {
                                            $option1 = "selected";
                                        } else {
                                            $option1 = "";
                                        }

                                ?>
                                        <option value="<?php echo $vat->m_group_id; ?>" <?= $option1 ?>><?= $vat->m_group_name; ?>
                                    <?php
                                    }
                                }

                                    ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="">Branch</label>
                            <select name="branchs_id" id="branchs_id" class="form-select select2">
                                <option value="">All Branches</option>
                                <?php if (!empty($branch_list)) {
                                    foreach ($branch_list as $branch) {
                                        $selected = ($branchs_id == $branch->m_user_id) ? 'selected' : '';
                                ?>
                                        <option value="<?= $branch->m_user_id ?>" <?= $selected ?>><?= $branch->m_user_name ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="">Search By Name</label>
                            <input type="text" name="search_in" id="search_in" class="form-control" placeholder="Enter Name,Mobile..." value="<?= $search_in ?>">
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="">Lot Wise Search</label>
                            <input type="text" name="lot_no" id="lot_no" class="form-control" value="<?= $lot_no ?>">
                        </div>
                    </div>
                    <div class="col-4 mt-2">
                        <button class="btn btn-info btn-sm" type="submit"><i class="bi bi-search mx-1"></i> Search</button>
                        <a class="btn btn-danger btn-sm" href="<?= base_url('Sales/sales_list') ?>"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
                        <button class="btn btn-success btn-sm" type="button" onclick="printcustomtable()"><i class="bi bi-printer me-2"></i>Print</button>
                        <button class="btn btn-warning btn-sm" type="submit" value="3" name="print_bill"><i class="bi bi-printer"></i> Print Bill</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row justify-content-evenly g-0">
            <div class="col-12">
                <div class="table-responsive bg-light printTableDiv" style="height: 64vh;">
                    <table id="sales_tbl" class="table table-striped table-bordered dt-responsive nowra w-100">
                        <thead>
                            <tr>
                                <th>SNO</th>
                                <th>DATE</th>
                                <th>CUSTOMER NAME</th>
                                <th>CUSTOMER CONTACT</th>
                                <th>TOTAL QTY</th>
                                <th>BILL AMT</th>
                                <th>TOTAL EXPENSE</th>
                                <th>NET BILL AMT</th>
                                <th>SALE INCHARGE</th>
                                <th class="actionth" width="10%">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $sumtqty = 0;
                            $sumtamt = 0;
                            $sumtexp = 0;
                            $sumtnetto = 0;
                            if (!empty($all_value)) {
                                foreach ($all_value as $value) {
                                    $sumtqty += $value->tqty;
                                    $sumtamt += $value->total_amount;
                                    $sumtexp += $value->total_expense;
                                    $sumtnetto += ($value->total_amount + $value->total_expense);
                                    $new_date = date("d-m-Y", strtotime($value->m_sale_date));
                            ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $new_date; ?></td>
                                        <td><?php echo $value->m_cust_name; ?></td>
                                        <td><?php echo $value->m_cust_mobile; ?></td>
                                        <td><?php echo $value->tqty; ?></td>
                                        <td><?php echo $value->total_amount; ?></td>
                                        <td><?php echo $value->total_expense; ?></td>
                                        <td><?php echo $value->total_amount + $value->total_expense; ?></td>
                                        <td><?php echo $value->m_user_name ?: 'Admin'; ?></td>

                                        <td class="wd-30 actionth">
                                            <div class="d-flex">
                                                <a href="<?= base_url('Sales/bill_print?id=') . $value->m_sale_spo ?>" class="btn btn-success btn-sm p-1 me-1" target="blank" title="Print Recipt"><i class="bi bi-printer"></i></a>
                                                <button type="button" class="btn btn-primary btn-sm p-1 me-1" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $value->m_sale_spo; ?>" title="View"><i class="bi bi-eye"></i></button>
                                                <!-- view Modal start -->
                                                <div class="modal fade" id="viewModal<?php echo $value->m_sale_spo; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="row" style="display: contents;">
                                                                    <div class="col-md-10">
                                                                        <h4 class="modal-title"> Sale Details (<?php echo $value->m_sale_spo; ?>)</h4>
                                                                    </div>
                                                                    <div class="col-md-2" style="text-align: end;">
                                                                        <a onclick="printcustomdiv()" class="btn btn-success btn-sm">
                                                                            <i class="bi bi-printer me-2"></i>Print
                                                                        </a>
                                                                        <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-close"></i></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body printDiv" style="word-break: break-all">

                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="card p-3">
                                                                            <div class="row " style="margin: 0px;">
                                                                                <div class="col-md-6 pd-5">
                                                                                    Name : <b><?php echo $value->m_cust_name; ?></b>
                                                                                </div>
                                                                                <div class="col-md-6 pd-5 text-end">
                                                                                    Contact No : <b><?php echo $value->m_cust_mobile; ?></b>
                                                                                </div>

                                                                                <div class="col-md-6 pd-5">
                                                                                    State/City : <b><?php echo $value->m_city_name . '/' . $value->m_state_name; ?></b>
                                                                                </div>
                                                                                <div class="col-md-6 pd-5 text-end">
                                                                                    Group: <b><?php echo $value->m_group_name; ?></b>
                                                                                </div>

                                                                                <div class="col-md-12 pd-5">
                                                                                    Address : <b><?php echo $value->m_cust_address; ?></b>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="card p-3">
                                                                            <div class="row " style="margin: 0px;">
                                                                                <div class="col-md-6 pd-5">
                                                                                    Voucher No : <b><?php echo $value->m_sale_spo; ?></b>
                                                                                </div>

                                                                                <div class="col-md-6 pd-5 text-end">
                                                                                    Truck Number : <b><?php echo $value->m_sale_trackno; ?></b>
                                                                                </div>

                                                                                <div class="col-md-6 pd-5 ">
                                                                                    Sale Date : <b><?php echo date('d-m-Y', strtotime($value->m_sale_date)); ?></b>
                                                                                </div>
                                                                                <div class="col-md-6 pd-5 text-end">
                                                                                    Sale BY : <b><?php echo $value->m_user_name ?: 'Admin'; ?></b>
                                                                                </div>

                                                                                <div class="col-md-12 pd-5">
                                                                                    Note : <b><?php echo $value->m_sale_note; ?></b>
                                                                                </div>
                                                                            </div>
                                                                        </div>


                                                                    </div>

                                                                    <div class="col-md-12 mt-3">
                                                                        <label>List of Items Sale</label>
                                                                        <div class="table-responsive">
                                                                            <table class="table table-striped table-bordered">
                                                                                <thead>
                                                                                    <th>Sn</th>
                                                                                    <th>Item Name</th>
                                                                                    <th>Lot No</th>
                                                                                    <th>Quantity</th>
                                                                                    <th>Unit</th>
                                                                                    <th>Weight (KG)</th>
                                                                                    <th>Price</th>
                                                                                    <th>Crate</th>
                                                                                    <th>Total</th>
                                                                                </thead>
                                                                                <tbody id="modal_body_contant">

                                                                                    <?php
                                                                                    $item_list =  $this->Main_model->get_edit_sales($value->m_sale_spo);

                                                                                    if (!empty($item_list)) {
                                                                                        foreach ($item_list as $cua => $key) {

                                                                                            echo '<tr>
                                                                                    <td>' . ($cua + 1) . '</td>
                                                                                    <td>' . $key->m_item_name . ' </td>
                                                                                    <td>' . $key->m_sale_lot . ' | ' . $key->pur_lotno . ' </td>
                                                                                    <td>' . $key->m_sale_qty . ' </td>
                                                                                    <td>' . $key->unitname . '</td>
                                                                                    <td>' . $key->m_sale_weight . '</td>
                                                                                    <td>' . $key->m_sale_price . '</td>
                                                                                    <td>' . $key->cratetype . '</td>
                                                                                    <td>' . $key->m_sale_total . '</td>
                                                                                   
                                                                                </tr>';
                                                                                        }
                                                                                    }
                                                                                    ?>

                                                                                </tbody>
                                                                                <tfoot>
                                                                                    <tr>
                                                                                        <th colspan="2">Total</th>

                                                                                        <th><?= $value->tqty ?></th>
                                                                                        <th></th>
                                                                                        <th><?= $value->twght ?></th>
                                                                                        <th></th>
                                                                                        <th><?= $value->tcreate ?></th>
                                                                                        <th><?= $value->total_amount ?></th>

                                                                                    </tr>
                                                                                </tfoot>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-8 mt-2">
                                                                        <table class="table table-striped table-bordered dt-responsive nowra">
                                                                            <thead>
                                                                                <th>Expenses</th>
                                                                                <th>Per</th>
                                                                                <th>Rate</th>
                                                                                <th>Amount</th>
                                                                                <th>Expenses</th>
                                                                                <th>Per</th>
                                                                                <th>Rate</th>
                                                                                <th>Amount</th>

                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td>Commission</td>
                                                                                    <td>Percent</td>
                                                                                    <td><?= $value->m_sale_comrate ?></td>
                                                                                    <td><?= $value->m_sale_comm ?></td>

                                                                                    <td>Fright</td>
                                                                                    <td>Bill</td>
                                                                                    <td><?= $value->m_sale_fright ?></td>
                                                                                    <td><?= $value->m_sale_fright ?></td>

                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Hamali</td>
                                                                                    <td>Bill</td>
                                                                                    <td><?= $value->m_sale_hamali ?></td>
                                                                                    <td><?= $value->m_sale_hamali ?></td>

                                                                                    <td>Other</td>
                                                                                    <td>Bill</td>
                                                                                    <td><?= $value->m_sale_others ?></td>
                                                                                    <td><?= $value->m_sale_others ?></td>
                                                                                </tr>

                                                                            </tbody>

                                                                        </table>
                                                                    </div>
                                                                    <div class="col-md-4 mt-2" style=" font-weight: bold; font-size: inherit; line-height: 2rem;">
                                                                        <div class="form-group">
                                                                            <label>Total Amount : </label>
                                                                            <label><?= $value->total_amount ?> </label>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Expenses : </label>
                                                                            <label><?= $value->total_expense ?> </label>
                                                                        </div>
                                                                        <hr style="margin: 0.1rem 0;">
                                                                        <div class="form-group">
                                                                            <label>Net Total : </label>
                                                                            <label><?= ($value->total_amount + $value->total_expense) ?> </label>
                                                                        </div>
                                                                        <hr style="margin: 0.1rem 0;">
                                                                    </div>
                                                                </div>

                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- view modal end -->
                                                <a href="<?php echo base_url('Sales/add_sales?id=') . $value->m_sale_spo; ?>" class="btn btn-info btn-sm p-1 me-1" title="Edit" data-toggle="tooltip"><i class="bi bi-pencil-square"></i></a>
                                                <button class="btn btn-danger btn-sm delete-sales p-1" data-value="<?php echo $value->m_sale_spo; ?>" title="Delete" data-toggle="tooltip"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                            <?php
                                    $i++;
                                }
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">Total</th>
                                <th><?= $sumtqty ?></th>
                                <th>₹<?= $sumtamt ?></th>
                                <th>₹<?= $sumtexp ?></th>
                                <th>₹<?= $sumtnetto ?></th>
                                <th colspan="2"></th>

                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========== Page Content ========== -->

<!---import model-->

<div id="myImportModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header justify-content-between">
                <h4 class="modal-title">Import From Excel State Data</h4>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <form method="POST" action="<?php echo site_url('Master/import_state_itemgroup') ?>" enctype="multipart/form-data">
                <div class="modal-body">

                    <p><b>Criteria</b>
                        <br>1. Your Excel data should be in the format below! The first row of your Excel needs a column header as in the example table! Also make sure your file is UTF-8 to avoid unnecessary encoding problems.

                        <br>2. If you're trying to rectangle the date column, make sure the format is formatted in Y-m-d (2021-01-01).

                        <br>3. Import only 1000 data at a time.
                    </p>
                    <hr>
                    <input type="file" name="import_file">
                </div>
                <div class="modal-footer justify-content-between">

                    <a href="<?php echo base_url('uploads/City_sample.xlsx') ?>" download class="btn btn-warning btn-sm"><i class="fa fa-download"></i> Download Sample</a>
                    <div>
                        <input type="submit" class="btn btn-success btn-sm" value="Import">
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>


                </div>
            </form>
        </div>
    </div>
</div>

<!---import model end above-->

<!-- ========================Footer================Fix======= -->
<?php $this->view('footer'); ?>
<?php $this->view('js/sale_js') ?>
<?php $this->view('js/custom_js'); ?>
<!-- ========================Footer================Fix======= -->