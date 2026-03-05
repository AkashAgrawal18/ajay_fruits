<!-- ========================Header==============Fix========= -->
<?php $this->view('head'); ?>
<?php $this->view('header'); ?>
<style>
    .modal-dialog {
        width: 80% !important;
        margin: 30px auto;
    }

    .modal {
        --bs-modal-width: 80%;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .printTableDiv {
            overflow: visible !important;
            height: 100vh !important;
        }

        .actionth {
            display: none;
        }
    }
</style>
<!-- ========== Page Content ========== -->
<section class="py-1" style="background: #bbf;">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-6 d-flex">
                <?php if ($type == 2) {
                    echo '<h6 class="m-0 text-white">
                    <a href="' . base_url('Sales/recieved_list/2') . '" class="btn btn-secondary btn-sm p-1">Crate Recieved</a>
                    <a href="' . base_url('Sales/payment_list/2') . '" class="btn btn-primary btn-sm p-1">Crate Return</a>
                 </h6>';
                } ?>

                <h6 class="<?php if ($type == 2) {
                                echo 'mt-2 mx-2';
                            } else {
                                echo 'm-0';
                            } ?> text-white">
                    Home >> <span class="text-primary"><?= $pagename ?></span>
                </h6>
            </div>
            <div class="col-6 text-end">
                <button onclick="history.back()" class="btn btn-danger btn-sm">
                    <i class="bi bi-box-arrow-left me-2"></i>Exit
                </button>
            </div>
        </div>
    </div>
</section>
<section class="py-4 d-flex align-items-center" style="background:#f3f3ff;min-height:70vh;">
    <div class="container-fluid">

        <form action="<?= base_url('Sales/payment_list/') . $type ?>" method="POST" class="row align-items-center">
            <div class="row justify-content-start mb-2">
                <div class="col-3">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">From date</label>
                                <input type="date" max="<?= date('Y-m-d') ?>" name="from_date" id="from_date" class="form-control" value="<?= $from_date ?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">To date</label>
                                <input type="date" max="<?= date('Y-m-d') ?>" name="to_date" id="to_date" class="form-control" value="<?= $to_date ?>">
                            </div>
                        </div>

                    </div>
                </div>

                <?php if ($type == 1) { ?>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="payment_account">Account Type</label>
                            <select name="payment_account" id="payment_account" class="form-select select2">
                                <option value="">All Account</option>
                                <option value="1" <?= $payment_account == 1 ? 'selected' : '' ?>>Supplier </option>
                                <option value="2" <?= $payment_account == 2 ? 'selected' : '' ?>>Expense </option>
                                <option value="3" <?= $payment_account == 3 ? 'selected' : '' ?>>Loader </option>
                                <option value="4" <?= $payment_account == 4 ? 'selected' : '' ?>>Staff </option>
                                <option value="5" <?= $payment_account == 5 ? 'selected' : '' ?>>General </option>
                                <option value="6" <?= $payment_account == 6 ? 'selected' : '' ?>>Investment </option>
                                <option value="7" <?= $payment_account == 7 ? 'selected' : '' ?>>Bank </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="payment_method">Method</label>
                            <select name="payment_method" id="payment_method" class="form-select select2">
                                <option value="">All Method</option>
                                <?php if (!empty($paymode_lst)) {
                                    foreach ($paymode_lst as $kry) {
                                        if ($payment_method == $kry->m_group_id) {
                                            $op = 'selected';
                                        } else {
                                            $op = '';
                                        }
                                        echo '<option value="' . $kry->m_group_id . '" ' . $op . '>' . $kry->m_group_name . '</option>';
                                    }
                                } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?>
                <div class="<?= $type == 1 ? 'col-2' : 'col-3' ?>">
                    <div class="form-group">
                        <label for="">Search By Name</label>
                        <input type="text" name="search_in" id="search_in" class="form-control" placeholder="Enter Name,Mobile..." value="<?= $search_in ?>">
                    </div>
                </div>

                <div class="col-3 mt-4">
                    <button class="btn btn-info btn-sm" type="submit"><i class="bi bi-search mx-1"></i> Search</button>
                    <a class="btn btn-danger btn-sm" href="<?= base_url('Sales/payment_list/') . $type ?>"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#myAddModal" class="btn btn-primary btn-sm" title="Add New">Add New</button>
                    <button class="btn btn-success btn-sm" type="button" onclick="printcustomtable()"><i class="bi bi-printer me-2"></i>Print</button>
                </div>
            </div>

        </form>


        <div class="row justify-content-evenly g-0">
            <div class="col-12">
                <div class="table-responsive bg-light printTableDiv" style="height: 64vh;">
                    <table id="payment_tbl" class="my_custom_datatable table table-striped table-bordered dt-responsive nowra w-100">
                        <thead>
                            <tr>
                                <th>SNO</th>
                                <th>DATE</th>

                                <?php if ($type == 1) { ?>
                                    <th>ACCOUNT TYPE</th>
                                    <th>ACCOUNT NAME</th>
                                    <th>VOUCHER NO</th>
                                    <th>AMOUNT CASH</th>
                                    <th>AMOUNT BANK</th>
                                <?php }
                                if ($type == 2) { ?>
                                    <th>SUPPLIER NAME</th>
                                    <th>SUPPLIER CONTACT</th>
                                    <th>CRATE TYPE</th>
                                    <th>QUANTITY</th>
                                <?php } ?>
                                <th>REMARK</th>
                                <th class="actionth">ACTION</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $sumtqty = 0;
                            $sumcashamt = 0;
                            $sumbankamt = 0;
                            if (!empty($all_value)) {
                                foreach ($all_value as $value) {
                                    $sumtqty += $value->m_payment_qty;
                            ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($value->m_payment_date)); ?></td>

                                        <?php if ($type == 1) {

                                            switch ($value->account_type) {
                                                case 1:
                                                    $account_type = 'Supplier';
                                                    break;
                                                case 2:
                                                    $account_type = 'Expense';
                                                    break;
                                                case 3:
                                                    $account_type = 'Loader';
                                                    break;
                                                case 4:
                                                    $account_type = 'Staff';
                                                    break;
                                                case 5:
                                                    $account_type = 'General';
                                                    break;
                                                case 6:
                                                    $account_type = 'Investment';
                                                    break;
                                                case 7:
                                                    $account_type = 'Bank';
                                                    break;
                                            }

                                            if (stripos($value->method_name, 'cash') !== false) {
                                                $cashamt = $value->m_payment_amount;
                                                $bankamt = 0;
                                                $sumcashamt += $value->m_payment_amount;
                                            } else {
                                                $sumbankamt += $value->m_payment_amount;
                                                $bankamt = $value->m_payment_amount;
                                                $cashamt = 0;
                                            }

                                        ?>
                                            <td><?php echo $account_type; ?></td>
                                            <td><?php echo $value->m_user_name; ?></td>
                                            <td><?php echo $value->m_payment_voucher; ?></td>
                                            <td><?php echo $cashamt; ?></td>
                                            <td><?php echo $bankamt; ?></td>
                                        <?php }
                                        if ($type == 2) {
                                            $account_type = 'Supplier'; ?>
                                            <td><?php echo $value->m_user_name; ?></td>
                                            <td><?php echo $value->m_user_mobile; ?></td>
                                            <td><?php echo $value->m_itgrp_title; ?></td>
                                            <td><?php echo $value->m_payment_qty; ?></td>
                                        <?php } ?>
                                        <td><?php echo $value->m_payment_remark  ?></td>

                                        <td class="actionth">

                                            <button type="button" class="btn btn-primary btn-sm p-1 me-1 myeditpayModal" data-value="<?php echo $value->m_payment_id; ?>" data-bs-toggle="modal" data-bs-target="#myeditModal<?php echo $value->m_payment_id; ?>" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                            <button class="btn btn-danger btn-action btn-sm p-1 me-1 delete-payment" data-value="<?php echo $value->m_payment_voucher; ?>" title="Delete"><i class="bi bi-trash"></i></button>
                                            <!-- view Modal start -->
                                            <div id="myeditModal<?php echo $value->m_payment_id; ?>" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header justify-content-between">
                                                            <h4 class="modal-title">Edit <?= $type == 1 ? 'PAYMENT' : 'CRATE ISSUE' ?></h4>
                                                            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form method="POST" action="<?php echo site_url('Sales/update_payment_data') ?>" enctype="multipart/form-data">
                                                            <div class="modal-body">
                                                                <div class="row g-3">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Date</label>
                                                                            <input type="hidden" name="m_payment_id" value="<?= $value->m_payment_id ?>">
                                                                            <input type="hidden" name="m_payment_type" value="<?= $type ?>">
                                                                            <input type="date" max="<?= date('Y-m-d') ?>" name="m_payment_date" class="form-control" value="<?= $value->m_payment_date ?>">
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label><?= $account_type ?> Name </label>
                                                                            <input type="hidden" id="m_payment_account<?= $value->m_payment_id ?>" name="m_payment_account" value="<?= $value->m_payment_account ?>">
                                                                            <input type="hidden" id="precust<?= $value->m_payment_id ?>" name="precust" value="<?= $value->m_payment_supplier ?>">
                                                                            <input type="hidden" id="m_payment_cust<?= $value->m_payment_id ?>" name="m_payment_supplier" value="<?= $value->m_payment_supplier ?>">

                                                                            <input list="m_supplier_list<?= $value->m_payment_id ?>" data-value="<?= $value->m_payment_id ?>" placeholder="Add Items" class="form-control editaccoutin" style="width: 50%; margin-bottom:5px;" value="<?= $value->m_user_name ?>">

                                                                            <datalist id="m_supplier_list<?= $value->m_payment_id ?>">
                                                                                <?php
                                                                                if (!empty($supplier_list)) {
                                                                                    foreach ($supplier_list as $vat) {

                                                                                ?>
                                                                                        <option value="<?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?>" data-name="<?= $vat->m_user_name ?>" data-id="<?= $vat->m_user_id ?>" data-mobile="<?= $vat->m_user_mobile ?>"><?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?></option>
                                                                                <?php
                                                                                    }
                                                                                }

                                                                                ?>
                                                                            </datalist>

                                                                        </div>


                                                                    </div>
                                                                    <!-- <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label><?= $account_type ?> Name </label>
                                                                            <input type="text" class="form-control" value="<?= $value->m_user_name ?>" readonly>
                                                                        </div>
                                                                    </div> -->


                                                                    <?php if ($type == 1) { ?>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Amount <span class="text-danger">*</span></label>
                                                                                <input type="hidden" name="preamount" value="<?= $value->m_payment_amount ?>">
                                                                                <input type="text" name="m_payment_amount" id="m_payment_amount" class="form-control" required="" value="<?= $value->m_payment_amount ?>">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Method</label>
                                                                                <select name="m_payment_method" id="m_payment_method" class="form-control">
                                                                                    <?php if (!empty($paymode_lst)) {
                                                                                        foreach ($paymode_lst as $kry) {
                                                                                            if ($value->m_payment_method == $kry->m_group_id) {
                                                                                                $op = 'selected';
                                                                                            } else {
                                                                                                $op = '';
                                                                                            }
                                                                                            echo '<option value="' . $kry->m_group_id . '" ' . $op . '>' . $kry->m_group_name . '</option>';
                                                                                        }
                                                                                    } ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                    <?php } else {

                                                                    ?>
                                                                        <div class="col-md-12">
                                                                            <div class="row">
                                                                                <div class="col-4">
                                                                                    <label><?= $value->m_itgrp_title ?> Crate</label>
                                                                                </div>
                                                                                <div class="col-8">
                                                                                    <div class="form-group">
                                                                                        <input type="hidden" name="m_payment_crate" id="m_payment_crate" value="<?= $value->m_payment_crate ?>">
                                                                                        <input type="text" name="m_payment_qty" id="m_payment_qty" class="form-control" value="<?= $value->m_payment_qty ?>" placeholder="Enter <?= $value->m_itgrp_title ?> Qty">
                                                                                        <input type="hidden" name="preqty" id="preqty" value="<?= $value->m_payment_qty ?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    <?php } ?>

                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label>Remark</label>
                                                                            <textarea name="m_payment_remark" id="m_payment_remark" class="form-control"><?= $value->m_payment_remark ?></textarea>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <div class="modal-footer justify-content-between">
                                                                <div>
                                                                    <input type="submit" class="btn btn-success btn-sm" value="Submit">
                                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                                                                </div>


                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- view modal end -->
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
                                <th colspan="2"></th>
                                <?php if ($type == 1) { ?>
                                    <th colspan="3">Total</th>
                                    <th>₹<?= $sumcashamt ?></th>
                                    <th>₹<?= $sumbankamt ?></th>
                                <?php }
                                if ($type == 2) { ?>
                                    <th colspan="3">Total</th>
                                    <th><?= $sumtqty ?></th>

                                <?php } ?>
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

<!---Add model-->

<div id="myAddModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header justify-content-between">
                <h4 class="modal-title"><?= $type == 1 ? 'PAYMENT' : 'CRATE ISSUE' ?></h4>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <form method="POST" action="<?php echo site_url('Sales/insert_payment_data') ?>" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <?php if ($type == 1) { ?>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Accounts</label>
                                            <select name="m_payment_account" id="m_payment_account" class="form-control">
                                                <option value="1">Supplier</option>
                                                <option value="2">Expense</option>
                                                <option value="3">Loader</option>
                                                <option value="4">Staff</option>
                                                <option value="5">General</option>
                                                <option value="6">Investment</option>
                                                <option value="7">Bank</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Date<span class="text-danger">*</span></label>
                                            <input type="hidden" name="m_payment_type" id="m_payment_type" value="<?= $type ?>">
                                            <input type="date" max="<?= date('Y-m-d') ?>" name="m_payment_date" id="m_payment_date" class="form-control" required="" value="<?= date('Y-m-d') ?>">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Method</label>
                                            <select name="m_payment_method" id="m_payment_method" class="form-control">
                                                <?php if (!empty($paymode_lst)) {
                                                    foreach ($paymode_lst as $kry) {
                                                        echo '<option value="' . $kry->m_group_id . '">' . $kry->m_group_name . '</option>';
                                                    }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-12 mb-2">

                                <div class="table-responsive">


                                    <table class="table table-striped table-bordered dt-responsive nowra">
                                        <thead>
                                            <th width="5%">Sn</th>
                                            <th width="20%" id="selet_label">Supplier Name</th>
                                            <th width="8%"></th>
                                            <th width="15%">Amount</th>
                                            <th width="10%">Balance</th>
                                            <th>Remark</th>
                                            <th width="5%"></th>

                                        </thead>
                                        <tbody id="tableblock">

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" class="text-end">Total</th>
                                                <th id="total_amount">0</th>
                                                <th colspan="3"></th>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <input list="m_supplier_list" id="item_serch_inp" placeholder="Add Supplier" class="form-control" style="width: 50%; margin-bottom:5px;">

                                    <datalist id="m_supplier_list">
                                        <?php
                                        if (!empty($supplier_list)) {
                                            foreach ($supplier_list as $vat) {

                                        ?>
                                                <option value="<?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?>" data-name="<?= $vat->m_user_name ?>" data-balance="<?= $vat->m_user_balance ?>" data-id="<?= $vat->m_user_id ?>" data-mobile="<?= $vat->m_user_mobile ?>"><?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?></option>
                                        <?php
                                            }
                                        }

                                        ?>
                                    </datalist>

                                </div>
                            </div>

                        <?php } else { ?>

                            <div class="col-md-12 mb-2">


                                <div class="row justify-content-between mb-2 g-3">

                                    <div class="col-3">
                                        <div class="row">
                                            <div class="col-3">
                                                <label>Date<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-9">
                                                <div class="form-group">
                                                    <input type="hidden" name="m_payment_type" id="m_payment_type" value="<?= $type ?>">
                                                    <input type="date" name="m_payment_date" id="m_payment_date" class="form-control" required="" value="<?= date('Y-m-d') ?>">
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-12">
                                        <table class="table table-striped table-bordered dt-responsive nowra">
                                            <thead>
                                                <th width="5%">Sn</th>
                                                <th width="20%" id="selet_label">Supplier Name</th>
                                                <?php if (!empty($crate_lst)) {
                                                    foreach ($crate_lst as $kry) {
                                                        echo '<th width="10%">' . $kry->m_itgrp_title . '</th>';
                                                    }
                                                }
                                                ?>
                                                <th>Remark</th>
                                                <th width="5%"></th>

                                            </thead>
                                            <tbody id="cratetableblock">

                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2" class="text-end">Total Qty</th>

                                                    <?php foreach ($crate_lst as $kry) { ?>
                                                        <th class="crate-total" data-crate="<?= $kry->m_itgrp_id ?>">0</th>
                                                    <?php } ?>

                                                    <th colspan="2"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="col-6">
                                        <input list="cm_supplier_list" id="citem_serch_inp" placeholder="Add supplier" class="form-control">

                                        <datalist id="cm_supplier_list">
                                            <?php
                                            if (!empty($supplier_list)) {
                                                foreach ($supplier_list as $vat) {

                                            ?>
                                                    <option value="<?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?>" data-name="<?= $vat->m_user_name ?>" data-id="<?= $vat->m_user_id ?>" data-balance="<?= $vat->m_user_balance ?>" data-mobile="<?= $vat->m_user_mobile ?>"><?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?>
                                                <?php
                                                }
                                            }

                                                ?>
                                        </datalist>
                                    </div>
                                </div>
                            </div>

                        <?php } ?>

                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <div>
                        <input type="submit" class="btn btn-success btn-sm" value="Submit">
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>


                </div>
            </form>
        </div>
    </div>
</div>

<!--- Add model end above-->


<!-- ========================Footer================Fix======= -->
<?php $this->view('footer'); ?>
<?php $this->view('js/sale_js') ?>
<?php $this->view('js/custom_js'); ?>

<script>
    // let x = $('#rowunt').val();

    $(document).ready(function(e) {
        $(document).on('change', '.recdAmt', function() {
            let countke = $(this).data('count');
            let amutn = $(this).val();
            let oldbal = parseFloat($(`#balance_filed${countke}`).html());
            $(`#newbalance_filed${countke}`).html((oldbal - amutn));
            calculateTotalAmount();
        });

        $(document).on('input', '.m_payment_qty', function() {
            calculateCrateTotal();
        });

        let incount = 0;
        $(document).on('change', '#item_serch_inp', function() {
            var custid = $("#m_supplier_list option[value='" + $(this).val() + "']").attr('data-id')
            var custname = $("#m_supplier_list option[value='" + $(this).val() + "']").attr('data-name')
            var custmobile = $("#m_supplier_list option[value='" + $(this).val() + "']").attr('data-mobile')
            var custbal = $("#m_supplier_list option[value='" + $(this).val() + "']").attr('data-balance')

            incount++

            addrow(incount)
            $('#m_payment_supplier' + incount).val(custid);
            $('#cust_name' + incount).html(custname + ' | ' + custmobile);
            $('#balance_filed' + incount).html(custbal);
            $(this).val('');
        });

        let incot = 0;
        $(document).on('change', '#citem_serch_inp', function() {
            var custid = $("#cm_supplier_list option[value='" + $(this).val() + "']").attr('data-id')
            var custname = $("#cm_supplier_list option[value='" + $(this).val() + "']").attr('data-name')
            var custmobile = $("#cm_supplier_list option[value='" + $(this).val() + "']").attr('data-mobile')

            incot++

            addcraterow(incot, custid)
            $('#m_payment_supplier' + incot).val(custid);
            $('#cust_name' + incot).html(custname + ' | ' + custmobile);

            $(this).val('');
        });

        $(document).on('change', '.editaccoutin', function() {
            var acid = $(this).data('value');
            var custid = $("#m_supplier_list" + acid + " option[value='" + $(this).val() + "']").attr('data-id')
            var custname = $("#m_supplier_list" + acid + " option[value='" + $(this).val() + "']").attr('data-name')
            var custmobile = $("#m_supplier_list" + acid + " option[value='" + $(this).val() + "']").attr('data-mobile')
            $('#m_payment_cust' + acid).val(custid);
        });

        $(document).on("click", '.removerow', function() {
            var count = $(this).data('count');

            $('#rowcot' + count).remove();

        });

    });


    function addrow(x) {
        // x++;
        $('#tableblock').append(`<tr id="rowcot` + x + `">
        <td id="rowcount` + x + `">` + x + `</td>
                                           <td id="cust_name` + x + `"></td>
                                              <td id="balance_filed` + x + `"></td>
                                            <td> <input type="hidden" name="m_payment_supplier[]" id="m_payment_supplier` + x + `" value="">
                                            <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="m_payment_amount[]" id="m_payment_amount` + x + `" data-count="` + x + `" class="form-control recdAmt"></td>
                                              <td id="newbalance_filed` + x + `"></td>
                                            <td><input type="text" name="m_payment_remark[]" id="m_payment_remark` + x + `" class="form-control"></td>
                                                            <td>  <button type="button" class="btn btn-danger px-1 py-0 removerow" data-count="` + x + `" title="Delete"><i class="bi bi-trash"></i></button></td>
                                                            </tr>`);

    }

    function addcraterow(y, cuid) {
        // y++;
        $('#cratetableblock').append(`<tr id="rowcot` + y + `">
        <td id="rowcount` + y + `">` + y + `</td>
                                            <td id="cust_name` + y + `"></td>
                                            <?php if (!empty($crate_lst)) {
                                                foreach ($crate_lst as $kry) {
                                            ?>
                                                    <td>
                                                     <input type="hidden" name="m_payment_crate[` + cuid + y + `][]" id="m_payment_crate` + y + `" value="<?= $kry->m_itgrp_id ?>">
                                                    <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="m_payment_qty[` + cuid + y + `][]" id="m_payment_qty` + y + `" class="form-control m_payment_qty" placeholder="<?= $kry->m_itgrp_title ?> Qty" value="0">
                                                    </td> 
                            <?php  }
                                            } ?>
                                            <td><input type="hidden" name="m_payment_supplier[]" id="m_payment_supplier` + y + `" value=""><input type="hidden" name="uniqut[]" id="uniqut` + y + `" value="` + y + `"><input type="teyt" name="m_payment_remark[]" id="m_payment_remark` + y + `" class="form-control"></td>
                                                            <td>  <button type="button" class="btn btn-danger py-1 py-0 removerow" data-count="` + y + `" title="Delete"><i class="bi bi-trash"></i></button></td>
                                                            </tr>`);

    }


    function calculateTotalAmount() {
        let total = 0;

        $('.recdAmt').each(function() {
            let val = parseFloat($(this).val());
            if (!isNaN(val)) {
                total += val;
            }
        });

        $('#total_amount').html(total.toFixed(2));
    }

    function calculateCrateTotal() {

        $('.crate-total').each(function() {
            let crateId = $(this).data('crate');
            let total = 0;

            $(`.m_payment_qty`).each(function() {
                if ($(this).prev('input').val() == crateId) {
                    let val = parseFloat($(this).val());
                    if (!isNaN(val)) {
                        total += val;
                    }
                }
            });

            $(this).html(total);
        });
    }
</script>
<!-- ========================Footer================Fix======= -->