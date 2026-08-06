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
                    <a href="' . base_url('Sales/recieved_list/2') . '" class="btn btn-primary btn-sm p-1">Crate Recieved</a>
                    <a href="' . base_url('Sales/payment_list/2') . '" class="btn btn-secondary btn-sm p-1">Crate Return</a>
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

        <form action="<?= base_url('Sales/recieved_list/') . $type ?>" method="POST" class="row align-items-center">
            <div class="row justify-content-end mb-2">

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
                        <label for="">Line</label>
                        <select name="group_id" id="group_id" class="form-select select2">
                            <option value="">All Line</option>
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
                <?php if ($type == 1) { ?>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="recvd_account">Account Type</label>
                            <select name="recvd_account" id="recvd_account" class="form-select select2">
                                <option value="">All Account</option>
                                <option value="1" <?= $recvd_account == 1 ? 'selected' : '' ?>>Customer </option>
                                <option value="2" <?= $recvd_account == 2 ? 'selected' : '' ?>>General </option>
                                <option value="3" <?= $recvd_account == 3 ? 'selected' : '' ?>>Investment </option>
                                <option value="4" <?= $recvd_account == 4 ? 'selected' : '' ?>>Supplier </option>
                                <option value="5" <?= $recvd_account == 5 ? 'selected' : '' ?>>Expense </option>
                                <option value="6" <?= $recvd_account == 6 ? 'selected' : '' ?>>Loader </option>
                                <option value="7" <?= $recvd_account == 7 ? 'selected' : '' ?>>Bank </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="recvd_method">Method</label>
                            <select name="recvd_method" id="recvd_method" class="form-select select2">
                                <option value="">All Method</option>
                                <?php if (!empty($paymode_lst)) {
                                    foreach ($paymode_lst as $kry) {
                                        if ($recvd_method == $kry->m_group_id) {
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
                <?php if ($this->session->userdata('user_type') == 8) { ?>
                <div class="col-2">
                    <div class="form-group">
                        <label for="">Branch</label>
                        <select name="branch_id" id="branch_id" class="form-select select2">
                            <option value="">All Branches</option>
                            <?php if (!empty($branch_list)) {
                                foreach ($branch_list as $branch) {
                                    $selected = ($branch_id == $branch->m_user_id) ? 'selected' : '';
                            ?>
                                    <option value="<?= $branch->m_user_id ?>" <?= $selected ?>><?= $branch->m_user_name ?></option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                </div>
                <?php } ?>

                <div class="col-3 <?= $type == 1 ? 'mt-2' : 'mt-4' ?>">
                    <button class="btn btn-info btn-sm" type="submit"><i class="bi bi-search mx-1"></i> Search</button>
                    <a class="btn btn-danger btn-sm" href="<?= base_url('Sales/recieved_list/') . $type ?>"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#myAddModal" class="btn btn-primary btn-sm" title="Add New">Add New</button>
                    <button class="btn btn-success btn-sm" type="button" onclick="printcustomtable()"><i class="bi bi-printer me-2"></i>Print</button>

                </div>
            </div>

        </form>

        <div class="row justify-content-evenly g-0">
            <div class="col-12">
                <div class="table-responsive bg-light printTableDiv" style="height: 64vh;">
                    <table id="recieved_tbl" class="table table-striped table-bordered dt-responsive nowra w-100">
                        <thead>
                            <tr>
                                <th>SNO</th>
                                <th>DATE</th>

                                <?php if ($type == 1) { ?>
                                    <th>ACCOUNT TYPE</th>
                                    <th>CUSTOMER NAME</th>
                                    <th>CUSTOMER CONTACT</th>
                                    <th>VOUCHER</th>
                                    <th>AMOUNT CASH</th>
                                    <th>AMOUNT BANK</th>
                                <?php }
                                if ($type == 2) { ?>
                                    <th>CUSTOMER NAME</th>
                                    <th>CUSTOMER CONTACT</th>
                                    <th>CRATE TYPE</th>
                                    <th>QUANTITY</th>
                                <?php } ?>
                                <th>REMARK</th>
                                <th>RECEIVER</th>
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
                                    //   echo '<pre>'; print_r($value); die;
                                    $sumtqty += $value->m_recvd_qty;

                            ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($value->m_recvd_date)); ?></td>

                                        <?php if ($type == 1) {
                                            switch ($value->m_recvd_account) {
                                                case '1':
                                                    $account_type = 'Customer';
                                                    break;
                                                case '2':
                                                    $account_type = 'General';
                                                    break;
                                                case '3':
                                                    $account_type = 'Investment';
                                                    break;
                                                case '4':
                                                    $account_type = 'Supplier';
                                                    break;
                                                case '5':
                                                    $account_type = 'Expense';
                                                    break;
                                                case '6':
                                                    $account_type = 'Loader';
                                                    break;
                                                case '7':
                                                    $account_type = 'Bank';
                                                    break;

                                                default:
                                                    $account_type = '';
                                            }
                                            if (stripos($value->method_name, 'cash') !== false) {
                                                $cashamt = $value->m_recvd_amount;
                                                $bankamt = 0;
                                                $sumcashamt += $value->m_recvd_amount;
                                            } else {
                                                $sumbankamt += $value->m_recvd_amount;
                                                $bankamt = $value->m_recvd_amount;
                                                $cashamt = 0;
                                            }
                                        ?>

                                            <td><?php echo $account_type; ?></td>
                                            <td><?php echo $value->m_cust_name; ?></td>
                                            <td><?php echo $value->m_cust_mobile; ?></td>
                                            <td><?php echo $value->m_recvd_voucher; ?></td>
                                            <td><?php echo $cashamt ?: ''; ?></td>
                                            <td><?php echo $bankamt ?: ''; ?></td>
                                        <?php }
                                        if ($type == 2) {
                                            $account_type = 'Customer'; ?>
                                            <td><?php echo $value->m_cust_name; ?></td>
                                            <td><?php echo $value->m_cust_mobile; ?></td>
                                            <td><?php echo $value->m_itgrp_title; ?></td>
                                            <td><?php echo $value->m_recvd_qty; ?></td>
                                        <?php } ?>
                                        <td><?php echo $value->m_recvd_remark  ?></td>
                                        <td><?php echo $value->m_user_name ?: 'Admin'; ?></td>


                                        <td class="actionth">
                                            <?php if ($type == 1) {
                                                echo '<a href="' . base_url('Sales/payment_bill_print/') . $value->m_recvd_voucher . '" class="btn btn-success btn-sm p-1 me-1" target="blank" title="Print Recipt"><i class="bi bi-printer"></i></a>
                                            ';
                                            } else {
                                                echo '<a href="' . base_url('Sales/crate_bill_print/') . $value->m_recvd_voucher . '" class="btn btn-success btn-sm p-1 me-1" target="blank" title="Print Recipt"><i class="bi bi-printer"></i></a>
                                                ';
                                            } ?>
                                            <button type="button" class="btn btn-primary btn-sm p-1 me-1 myeditModal" data-value="<?php echo $value->m_recvd_id; ?>" data-bs-toggle="modal" data-bs-target="#myeditModal<?php echo $value->m_recvd_id; ?>" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                            <button class="btn btn-danger btn-action btn-sm delete-revied p-1 me-1" data-value="<?php echo $value->m_recvd_voucher; ?>" title="Delete"><i class="bi bi-trash"></i></button>
                                            <!-- view Modal start -->
                                            <div id="myeditModal<?php echo $value->m_recvd_id; ?>" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header justify-content-between">
                                                            <h4 class="modal-title">Edit <?= $type == 1 ? 'Receipt' : 'Crate Recieved' ?></h4>
                                                            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form method="POST" action="<?php echo site_url('Sales/update_recieved_data') ?>" enctype="multipart/form-data">
                                                            <div class="modal-body">
                                                                <div class="row g-3">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Date</label>
                                                                            <input type="hidden" name="m_recvd_id" value="<?= $value->m_recvd_id ?>">
                                                                            <input type="hidden" name="m_recvd_type" value="<?= $type ?>">
                                                                            <input type="date" max="<?= date('Y-m-d') ?>" name="m_recvd_date" class="form-control" value="<?= $value->m_recvd_date ?>">
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label><?= $account_type ?> Name </label>
                                                                            <input type="hidden" id="m_recvd_account<?= $value->m_recvd_id ?>" name="m_recvd_account" value="<?= $value->m_recvd_account ?>">
                                                                            <input type="hidden" id="precust<?= $value->m_recvd_id ?>" name="precust" value="<?= $value->m_recvd_customer ?>">
                                                                            <input type="hidden" id="m_recvd_cust<?= $value->m_recvd_id ?>" name="m_recvd_customer" value="<?= $value->m_recvd_customer ?>">

                                                                            <input list="m_customer_list<?= $value->m_recvd_id ?>" data-value="<?= $value->m_recvd_id ?>" placeholder="Add Items" class="form-control editaccoutin" style="width: 50%; margin-bottom:5px;" value="<?= $value->m_cust_name ?>">

                                                                            <datalist id="m_customer_list<?= $value->m_recvd_id ?>">
                                                                                <?php
                                                                                if (!empty($custo_list)) {
                                                                                    foreach ($custo_list as $vat) {

                                                                                ?>
                                                                                        <option value="<?= $vat->m_cust_name . ' | ' . $vat->m_cust_mobile; ?>" data-name="<?= $vat->m_cust_name ?>" data-id="<?= $vat->m_cust_id ?>" data-mobile="<?= $vat->m_cust_mobile ?>"><?= $vat->m_cust_name . ' | ' . $vat->m_cust_mobile; ?>
                                                                                    <?php
                                                                                    }
                                                                                }

                                                                                    ?>
                                                                            </datalist>

                                                                        </div>


                                                                    </div>
                                                                    <?php if ($type == 1) { ?>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Amount <span class="text-danger">*</span></label>
                                                                                <input type="hidden" name="preamount" value="<?= $value->m_recvd_amount ?>">
                                                                                <input type="text" name="m_recvd_amount" id="m_recvd_amount" class="form-control" required="" value="<?= $value->m_recvd_amount ?>">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Method</label>
                                                                                <select name="m_recvd_method" id="m_recvd_method" class="form-control">
                                                                                    <?php if (!empty($paymode_lst)) {
                                                                                        foreach ($paymode_lst as $kry) {
                                                                                            if ($value->m_recvd_method == $kry->m_group_id) {
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
                                                                                        <input type="hidden" name="preqty" id="preqty" value="<?= $value->m_recvd_qty ?>">
                                                                                        <input type="hidden" name="m_recvd_crate" id="m_recvd_crate" value="<?= $value->m_recvd_crate ?>">
                                                                                        <input type="text" name="m_recvd_qty" id="m_recvd_qty" class="form-control" value="<?= $value->m_recvd_qty ?>" placeholder="Enter <?= $value->m_itgrp_title ?> Qty">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    <?php } ?>

                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label>Remark</label>
                                                                            <textarea name="m_recvd_remark" id="m_recvd_remark" class="form-control"><?= $value->m_recvd_remark ?></textarea>
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
                                    <th colspan="4">Total</th>
                                    <th>₹<?= $sumcashamt ?></th>
                                    <th>₹<?= $sumbankamt ?></th>
                                    <th>₹<?= ($sumbankamt + $sumcashamt) ?></th>
                                    <th colspan="2"></th>
                                <?php }
                                if ($type == 2) { ?>
                                    <th colspan="3">Total</th>
                                    <th><?= $sumtqty ?></th>
                                    <th colspan="3"></th>
                                <?php } ?>

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
                <h4 class="modal-title"><?= $type == 1 ? 'RECEIPT' : 'CRATE RECIEVED' ?></h4>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <form method="POST" action="<?php echo site_url('Sales/insert_recieved_data') ?>" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">

                        <?php if ($type == 1) { ?>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Accounts</label>
                                            <select name="m_recvd_account" id="m_recvd_account" class="form-control">
                                                <option value="1">Customer</option>
                                                <option value="2">General</option>
                                                <option value="3">Investment</option>
                                                <option value="4">Supplier</option>
                                                <option value="5">Expense</option>
                                                <option value="6">Loader</option>
                                                <option value="7">Bank</option>
                                                <option value="8">Branch</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Date<span class="text-danger">*</span></label>
                                            <input type="hidden" name="m_recvd_type" id="m_recvd_type" value="<?= $type ?>">
                                            <input type="date" max="<?= date('Y-m-d') ?>" name="m_recvd_date" id="m_recvd_date" class="form-control" required="" value="<?= date('Y-m-d') ?>">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Method</label>
                                            <select name="m_recvd_method" id="m_recvd_method" class="form-control">
                                                <?php if (!empty($paymode_lst)) {
                                                    foreach ($paymode_lst as $kry) {
                                                        echo '<option value="' . $kry->m_group_id . '">' . $kry->m_group_name . '</option>';
                                                    }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php if ($this->session->userdata('user_type') == 8) { ?>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>Branch</label>
                                            <select name="m_recvd_branch" id="m_recvd_branch" class="form-control">
                                                <option value="0" <?= empty($branch_id) ? 'selected' : '' ?>>Head Office</option>
                                                <?php if (!empty($branch_list)) {
                                                    foreach ($branch_list as $branch) {
                                                        $selected = ($branch_id == $branch->m_user_id) ? 'selected' : '';
                                                        echo '<option value="' . $branch->m_user_id . '" ' . $selected . '>' . $branch->m_user_name . '</option>';
                                                    }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php } ?>

                                </div>
                            </div>

                            <div class="col-md-12 mb-2">

                                <div class="table-responsive">

                                    <table class="table table-striped table-bordered dt-responsive nowra">
                                        <thead>
                                            <th width="5%">Sn</th>
                                            <th width="20%" id="selet_label">Customer Name</th>
                                            <th width="8%"></th>
                                            <th width="15%">Amount</th>
                                            <th width="10%">Balance</th>
                                            <th>Remark</th>
                                            <th width="25%">Agent</th>
                                            <th width="5%"></th>
                                        </thead>
                                        <tbody id="tableblock">

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" class="text-end">Total</th>
                                                <th id="total_amount">0</th>
                                                <th colspan="4"></th>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <input list="m_customer_list" id="item_serch_inp" placeholder="Add Items" class="form-control" style="width: 50%; margin-bottom:5px;">

                                    <datalist id="m_customer_list">
                                        <?php
                                        if (!empty($custo_list)) {
                                            foreach ($custo_list as $vat) {

                                        ?>
                                                <option value="<?= $vat->m_cust_name . ' | ' . $vat->m_cust_mobile ?>" data-name="<?= $vat->m_cust_name ?>" data-balance="<?= money2($vat->m_cust_balance) ?>" data-id="<?= $vat->m_cust_id ?>" data-mobile="<?= $vat->m_cust_mobile ?>"><?= $vat->m_cust_name . ' | ' . $vat->m_cust_mobile; ?>
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
                                                    <input type="hidden" name="m_recvd_type" id="m_recvd_type" value="<?= $type ?>">
                                                    <input type="date" max="<?= date('Y-m-d') ?>" name="m_recvd_date" id="m_recvd_date" class="form-control" required="" value="<?= date('Y-m-d') ?>">
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <?php if ($this->session->userdata('user_type') == 8) { ?>
                                    <div class="col-3">
                                        <div class="row">
                                            <div class="col-4">
                                                <label>Branch</label>
                                            </div>
                                            <div class="col-8">
                                                <div class="form-group">
                                                    <select name="m_recvd_branch" id="m_recvd_branch" class="form-control">
                                                        <option value="0" <?= empty($branch_id) ? 'selected' : '' ?>>Head Office</option>
                                                        <?php if (!empty($branch_list)) {
                                                            foreach ($branch_list as $branch) {
                                                                $selected = ($branch_id == $branch->m_user_id) ? 'selected' : '';
                                                                echo '<option value="' . $branch->m_user_id . '" ' . $selected . '>' . $branch->m_user_name . '</option>';
                                                            }
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>

                                    <div class="col-12">

                                        <table class="table table-striped table-bordered dt-responsive nowra">
                                            <thead>
                                                <th width="5%">Sn</th>
                                                <th width="20%" id="selet_label">Customer Name</th>
                                                <?php if (!empty($crate_lst)) {
                                                    foreach ($crate_lst as $kry) {
                                                        echo '<th width="10%">' . $kry->m_itgrp_title . '</th>';
                                                    }
                                                }
                                                ?>
                                                <th>Remark</th>
                                                <th width="20%">Agent</th>
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

                                                    <th colspan="3"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="col-6">

                                        <input list="cm_customer_list" id="citem_serch_inp" placeholder="Add customer" class="form-control">

                                        <datalist id="cm_customer_list">
                                            <?php
                                            if (!empty($custo_list)) {
                                                foreach ($custo_list as $vat) {

                                            ?>
                                                    <option value="<?= $vat->m_cust_name . ' | ' . $vat->m_cust_mobile; ?>" data-name="<?= $vat->m_cust_name ?>" data-id="<?= $vat->m_cust_id ?>" data-mobile="<?= $vat->m_cust_mobile ?>"><?= $vat->m_cust_name . ' | ' . $vat->m_cust_mobile; ?>
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
            // balances are doubles, so plain subtraction leaves a long float
            // tail on screen; show the money value at two decimals
            $(`#newbalance_filed${countke}`).html((oldbal - amutn).toFixed(2));
            calculateTotalAmount();
        });

        $(document).on('input', '.m_recvd_qty', function() {
            calculateCrateTotal();
        });

        $(document).on('change', '#m_recvd_account', function() {
            let acct_type = $(this).val();
            get_account_list(acct_type, '')
        });

        // Live branch cascading on the modal's own Branch select.
        // type=2 Crate Received uses this static customer datalist; type=1
        // Receipt uses the account-type-driven picker, refreshed in the
        // `then` callback so it runs AFTER this response (and its rotated
        // CSRF token) has landed rather than racing it.
        BranchCascade.bind('#m_recvd_branch', [{
            listType: 'customer',
            target: '#cm_customer_list',
            mode: 'datalist',
            valueFn: function(c) { return c.m_cust_name + ' | ' + c.m_cust_mobile; },
            attrsFn: function(c) {
                return {
                    name: c.m_cust_name,
                    id: c.m_cust_id,
                    mobile: c.m_cust_mobile
                };
            }
        }], "<?php echo site_url('Master/branch_scoped_options'); ?>", {
            then: function() {
                var acct_type = $('#m_recvd_account').val();
                if (acct_type) {
                    get_account_list(acct_type, '');
                }
            }
        });

        $(document).on('click', '.myeditModal', function() {
            let filed_id = $(this).data('value');
            let acct_type = $('#m_recvd_account' + filed_id).val();
            get_account_list(acct_type, filed_id)
        });

        let incount = 0;
        $(document).on('change', '#item_serch_inp', function() {
            var custid = $("#m_customer_list option[value='" + $(this).val() + "']").attr('data-id')
            var custname = $("#m_customer_list option[value='" + $(this).val() + "']").attr('data-name')
            var custmobile = $("#m_customer_list option[value='" + $(this).val() + "']").attr('data-mobile')
            var custbal = $("#m_customer_list option[value='" + $(this).val() + "']").attr('data-balance')

            incount++

            addrow(incount)
            $('#m_recvd_customer' + incount).val(custid);
            $('#cust_name' + incount).html(custname + ' | ' + custmobile);
            $('#balance_filed' + incount).html(custbal);
            $(this).val('');
        });

        let incot = 0;
        $(document).on('change', '#citem_serch_inp', function() {
            var custid = $("#cm_customer_list option[value='" + $(this).val() + "']").attr('data-id')
            var custname = $("#cm_customer_list option[value='" + $(this).val() + "']").attr('data-name')
            var custmobile = $("#cm_customer_list option[value='" + $(this).val() + "']").attr('data-mobile')

            incot++

            addcraterow(incot, custid)
            $('#m_recvd_customer' + incot).val(custid);
            $('#cust_name' + incot).html(custname + ' | ' + custmobile);

            $(this).val('');
        });

        $(document).on('change', '.editaccoutin', function() {
            var acid = $(this).data('value');
            var custid = $("#m_customer_list" + acid + " option[value='" + $(this).val() + "']").attr('data-id')
            var custname = $("#m_customer_list" + acid + " option[value='" + $(this).val() + "']").attr('data-name')
            var custmobile = $("#m_customer_list" + acid + " option[value='" + $(this).val() + "']").attr('data-mobile')
            $('#m_recvd_cust' + acid).val(custid);

            // alert(custid);
            // alert($('#m_recvd_cust' + acid).val());
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
                                            <td> <input type="hidden" name="m_recvd_customer[]" id="m_recvd_customer` + x + `" value="">
                                            <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="m_recvd_amount[]" id="m_recvd_amount` + x + `" class="form-control recdAmt" data-count="` + x + `"></td>
                                            <td id="newbalance_filed` + x + `"></td>
                                            <td><input type="text" name="m_recvd_remark[]" id="m_recvd_remark` + x + `" class="form-control"></td>
                                            <td><select name="m_recvd_user[]" id="m_recvd_user` + x + `" class="form-control">
                                            <option value="">Select agent</option>
                                            <?php
                                            if (!empty($user_list)) {
                                                foreach ($user_list as $vat) {

                                            ?>
                                                <option value="<?= $vat->m_user_id ?>" ><?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?></option>
                                            <?php
                                                }
                                            }

                                            ?>
                                            </select></td>
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
                                                    <input type="hidden" name="m_recvd_crate[` + cuid + y + `][]" id="m_recvd_crate` + y + `" value="<?= $kry->m_itgrp_id ?>">
                                                    <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="m_recvd_qty[` + cuid + y + `][]" id="m_recvd_qty` + y + `" class="form-control m_recvd_qty" placeholder="<?= $kry->m_itgrp_title ?> Qty" value="0">
                                                    </td>  
                            <?php  }
                                            } ?>
                                            <td><input type="hidden" name="m_recvd_customer[]" id="m_recvd_customer` + y + `" value=""><input type="hidden" name="uniqut[]" id="uniqut` + y + `" value="` + y + `"><input type="teyt" name="m_recvd_remark[]" id="m_recvd_remark` + y + `" class="form-control"></td>
                                            <td><select name="m_recvd_user[]" id="m_recvd_user` + y + `" class="form-control">
                                            <option value="">Select agent</option>
                                            <?php
                                            if (!empty($user_list)) {
                                                foreach ($user_list as $vat) {

                                            ?>
                                                <option value="<?= $vat->m_user_id ?>" ><?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?></option>
                                            <?php
                                                }
                                            }

                                            ?>
                                            </select></td>
                                                            <td>  <button type="button" class="btn btn-danger py-1 py-0 removerow" data-count="` + y + `" title="Delete"><i class="bi bi-trash"></i></button></td>
                                                            </tr>`);

    }


    var accountListRequests = {};

    function get_account_list(acct_type, filed_id) {
        // Abort a same-target request still in flight so a slower, stale
        // response (e.g. from the account type or branch picked a moment
        // ago) can't land after a newer one and overwrite it.
        if (accountListRequests[filed_id]) {
            accountListRequests[filed_id].abort();
        }

        accountListRequests[filed_id] = $.ajax({
            type: "POST",
            url: "<?php echo site_url('Sales/get_reciept_accounts'); ?>",
            data: {
                acct_type,
                branch_id: $('#m_recvd_branch').val()
            },
            dataType: "JSON",
            success: function(data) {
                // The rotated CSRF token in this response is picked up
                // globally by the ajaxSuccess hook in footer.php.
                if (data != '') {
                    $('#m_customer_list' + filed_id).empty()

                    $.each(data.list, function(i, item) {
                        if (data.listtype == 1) {
                            $('#m_customer_list' + filed_id).append(`<option value="${item.m_cust_name} | ${item.m_cust_mobile}" data-name="${item.m_cust_name }" data-id="${item.m_cust_id }" data-mobile="${item.m_cust_mobile }">${item.m_cust_name} | ${item.m_cust_mobile }</option>`);
                        } else if (data.listtype == 2) {
                            $('#m_customer_list' + filed_id).append(`<option value="${item.m_user_name} | ${item.m_user_mobile}" data-name="${item.m_user_name }" data-id="${item.m_user_id }" data-mobile="${item.m_user_mobile }">${item.m_user_name} | ${item.m_user_mobile }</option>`);
                        } else {
                            $('#m_customer_list' + filed_id).append(`<option value="${item.m_group_name} | ${item.m_group_number}" data-name="${item.m_group_name }" data-id="${item.m_group_id }" data-mobile="${item.m_group_number }">${item.m_group_name} | ${item.m_group_number }</option>`);
                        }

                    })
                } else {
                    $('#m_customer_list' + filed_id).empty()
                }
            }
        });

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

            $(`.m_recvd_qty`).each(function() {
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