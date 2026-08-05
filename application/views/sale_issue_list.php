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
                <a class="btn btn-primary btn-sm" href="<?= base_url('Sales/add_issue_item' . (!empty($branch_id) ? '?branch_id=' . $branch_id : '')) ?>"><i
                        class="bi bi-person-plus-fill"></i> Add New</a>
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
                <form action="<?= base_url('Sales/issue_item_list') ?>" method="POST" class="row align-items-center">

                    <div class="col-2">
                        <div class="form-group">
                            <label for="">From date</label>
                            <input type="date" max="<?= date('Y-m-d') ?>" name="from_date" id="from_date"
                                class="form-control" value="<?= $from_date ?>">
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="">To date</label>
                            <input type="date" max="<?= date('Y-m-d') ?>" name="to_date" id="to_date"
                                class="form-control" value="<?= $to_date ?>">
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="">Staff </label>
                            <select name="staff_id" id="staff_id" class="form-select select2">
                                <option value="">All Staff</option>
                                <?php
                                if (!empty($staff_list)) {
                                    foreach ($staff_list as $vat) {

                                        if ($staff_id == $vat->m_user_id) {
                                            $option1 = "selected";
                                        } else {
                                            $option1 = "";
                                        }

                                        ?>
                                        <option value="<?php echo $vat->m_user_id; ?>" <?= $option1 ?>>
                                            <?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?>
                                            <?php
                                    }
                                }

                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="">Lot Wise Search</label>
                            <input type="text" name="lot_no" id="lot_no" class="form-control" value="<?= $lot_no ?>">
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
                    <div class="col-4 mt-4">
                        <button class="btn btn-info btn-sm" type="submit"><i class="bi bi-search mx-1"></i>
                            Search</button>
                        <a class="btn btn-danger btn-sm" href="<?= base_url('Sales/issue_item_list') ?>"><i
                                class="bi bi-arrow-clockwise"></i> Refresh</a>
                        <button class="btn btn-success btn-sm" type="button" onclick="printcustomtable()"><i
                                class="bi bi-printer me-2"></i>Print</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row justify-content-evenly g-0">
            <div class="col-12">
                <div class="table-responsive bg-light printTableDiv" style="height: 64vh;">
                    <table id="item_issue_tbl"
                        class="my_custom_datatable table table-striped table-bordered dt-responsive nowra w-100">
                        <thead>
                            <tr>
                                <th>SNO</th>
                                <th>DATE</th>
                                <th>INDICATOR</th>
                                <th>STAFF NAME</th>
                                <th>LORRY NO</th>
                                <th>ISSUE QTY</th>
                                <th>SALE QTY</th>
                                <th>PENDING QTY</th>
                                <th>TOTAL AMOUNT</th>
                                <th width="10%">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $sumtqty = 0;
                            $sumtSaleqty = 0;
                            $sumtPenqty = 0;
                            $sumtamt = 0;
                            if (!empty($all_value)) {
                                foreach ($all_value as $value) {
                                    if (!empty($lot_no)) {
                                        $indicator = $this->Report_model->get_issue_itemsale(null,$value->si_issue_id);
                                    } else {
                                        $indicator = $this->Report_model->get_issue_itemsale($value->si_issue_spo);
                                    }
                                    if ($value->si_issue_type == 2) {
                                        $badge = '<span class="badge btn btn-danger">return</span>';
                                    } else if ($indicator['status'] == 2) {
                                        $badge = '<span class="badge btn btn-warning">Stock Pending</span>';
                                    } else if ($indicator['status'] == 3) {
                                        $badge = '<span class="badge btn btn-success">Completed</span>';
                                    }
                                    $sumtqty += $value->tqty;
                                    $sumtamt += isset($indicator) ? $indicator['total_sale_amount'] : 0;
                                    $sumtSaleqty += isset($indicator) ? $indicator['total_sale_qty'] : 0;
                                    $sumtPenqty += isset($indicator) ? $indicator['total_balance_qty'] : 0;
                                    $new_date = date("d-m-Y", strtotime($value->si_issue_date));
                                    ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $new_date; ?></td>
                                        <td><?= $badge ?></td>
                                        <td><?php echo $value->m_user_name; ?></td>
                                        <td><?php echo $value->si_issue_trackno; ?></td>
                                        <td><?php echo $value->tqty; ?></td>
                                        <td><?php echo isset($indicator) ? $indicator['total_sale_qty'] : 0; ?></td>
                                        <td><?php echo isset($indicator) ? $indicator['total_balance_qty'] : 0; ?></td>
                                        <td><?php echo isset($indicator) ? $indicator['total_sale_amount'] : 0; ?></td>

                                        <td class="wd-30">
                                            <div class="d-flex">

                                                <button type="button" class="btn btn-primary btn-sm p-1 me-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewModal<?php echo $value->si_issue_spo; ?>"
                                                    title="View"><i class="bi bi-eye"></i></button>
                                                <!-- view Modal start -->
                                                <div class="modal fade" id="viewModal<?php echo $value->si_issue_spo; ?>"
                                                    tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="row" style="display: contents;">
                                                                    <div class="col-md-10">
                                                                        <h4 class="modal-title"> Issue Details
                                                                            (<?php echo $value->si_issue_spo; ?>)</h4>
                                                                    </div>
                                                                    <div class="col-md-2" style="text-align: end;">
                                                                        <a onclick="printcustomdiv()"
                                                                            class="btn btn-success btn-sm">
                                                                            <i class="bi bi-printer me-2"></i>Print
                                                                        </a>
                                                                        <button type="button" class="btn-close btn-danger"
                                                                            data-bs-dismiss="modal" aria-label="Close"><i
                                                                                class="bi bi-close"></i></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body printDiv" style="word-break: break-all">

                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="card p-3">
                                                                            <div class="row " style="margin: 0px;">
                                                                                <div class="col-md-6 pd-5">
                                                                                    Name :
                                                                                    <b><?php echo $value->m_user_name; ?></b>
                                                                                </div>
                                                                                <div class="col-md-6 pd-5 text-end">
                                                                                    Contact No :
                                                                                    <b><?php echo $value->m_user_mobile; ?></b>
                                                                                </div>

                                                                                <div class="col-md-6 pd-5">
                                                                                    State/City :
                                                                                    <b><?php echo $value->m_city_name . '/' . $value->m_state_name; ?></b>
                                                                                </div>
                                                                                <div class="col-md-6 pd-5 text-end">
                                                                                    Group:
                                                                                    <b><?php echo $value->m_group_name; ?></b>
                                                                                </div>

                                                                                <div class="col-md-12 pd-5">
                                                                                    Address :
                                                                                    <b><?php echo $value->m_user_address; ?></b>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="card p-3">
                                                                            <div class="row " style="margin: 0px;">
                                                                                <div class="col-md-6 pd-5">
                                                                                    Voucher No :
                                                                                    <b><?php echo $value->si_issue_spo; ?></b>
                                                                                </div>

                                                                                <div class="col-md-6 pd-5 text-end">
                                                                                    Truck Number :
                                                                                    <b><?php echo $value->si_issue_trackno; ?></b>
                                                                                </div>

                                                                                <div class="col-md-6 pd-5 ">
                                                                                    Issue Date :
                                                                                    <b><?php echo date('d-m-Y', strtotime($value->si_issue_date)); ?></b>
                                                                                </div>
                                                                                <div class="col-md-6 pd-5 text-end">
                                                                                    Issue BY :
                                                                                    <b><?php echo $value->issuebyname ?: 'Admin'; ?></b>
                                                                                </div>

                                                                                <!-- <div class="col-md-12 pd-5">
                                                                            Remark : <b><?php echo $value->si_issue_remark; ?></b>
                                                                        </div> -->
                                                                            </div>
                                                                        </div>


                                                                    </div>

                                                                    <div class="col-md-12 mt-3">
                                                                        <label>List of Items Issued</label>
                                                                        <div class="table-responsive">
                                                                            <table class="table table-striped table-bordered">
                                                                                <thead>
                                                                                    <th>Sn</th>
                                                                                    <th>Item Name</th>
                                                                                    <th>LOT Number</th>
                                                                                    <th>Quantity</th>
                                                                                    <th>Unit</th>
                                                                                    <th>Weight (KG)</th>
                                                                                    <th>Price</th>
                                                                                    <th>Crate</th>
                                                                                    <th>Total</th>
                                                                                </thead>
                                                                                <tbody id="modal_body_contant">

                                                                                    <?php
                                                                                    $item_list = $this->Main_model->get_edit_item_issue($value->si_issue_spo);

                                                                                    if (!empty($item_list)) {
                                                                                        foreach ($item_list as $cua => $key) {

                                                                                            echo '<tr>
                                                                                    <td>' . ($cua + 1) . '</td>
                                                                                    <td>' . $key->m_item_name . ' </td>
                                                                                    <td>' . $key->si_issue_lotno . ' | ' . $key->pur_lotno . ' </td>
                                                                                    <td>' . $key->si_issue_qty . ' </td>
                                                                                    <td>' . $key->unitname . '</td>
                                                                                    <td>' . $key->si_issue_weight . '</td>
                                                                                    <td>' . $key->si_issue_price . '</td>
                                                                                    <td>' . $key->cratetype . '</td>
                                                                                    <td>' . $key->si_issue_total . '</td>
                                                                                   
                                                                                </tr>';
                                                                                        }
                                                                                    }
                                                                                    ?>

                                                                                </tbody>
                                                                                <tfoot>
                                                                                    <tr>
                                                                                        <th colspan="3">Total</th>

                                                                                        <th><?= $value->tqty ?></th>
                                                                                        <th></th>
                                                                                        <th><?= $value->twght ?></th>
                                                                                        <th></th>
                                                                                        <th><?= $value->tcrate ?></th>
                                                                                        <th><?= $value->tamount ?></th>

                                                                                    </tr>
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

                                                <?php // if ($logged_user_type == 1 || has_perm($logged_user_id, $pageperm, $pageperm, 'Edit')) { 
                                                        ?>
                                                <a href="<?php echo base_url('Sales/add_issue_item?id=') . $value->si_issue_spo; ?>"
                                                    class="btn btn-info btn-sm p-1 me-1" title="Edit" data-toggle="tooltip"><i
                                                        class="bi bi-pencil-square"></i></a>
                                                <?php //}
                                                        // if ($logged_user_type == 1 || has_perm($logged_user_id, $pageperm, $pageperm, 'Delete')) { 
                                                        ?>
                                                <button class="btn btn-danger btn-sm delete-item_issue p-1"
                                                    data-value="<?php echo $value->si_issue_spo; ?>" title="Delete"
                                                    data-toggle="tooltip"><i class="bi bi-trash"></i></button>
                                                <?php // } 
                                                        ?>
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
                                <th colspan="5">Total</th>
                                <th><?= $sumtqty ?></th>
                                <th><?= $sumtSaleqty ?></th>
                                <th><?= $sumtPenqty ?></th>
                                <th>₹<?= $sumtamt ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========== Page Content ========== -->


<!-- ========================Footer================Fix======= -->
<?php $this->view('footer'); ?>
<?php $this->view('js/sale_js') ?>
<?php $this->view('js/custom_js'); ?>
<!-- ========================Footer================Fix======= -->