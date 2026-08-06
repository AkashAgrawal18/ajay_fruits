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

    .select2-container--open {
        z-index: 10000 !important;
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
                <?php if ($this->session->userdata('user_type') == 8) { ?>
                    <a class="btn btn-primary btn-sm" href="<?= base_url('Sales/add_purchase' . (!empty($branch_id) ? '?branch_id=' . $branch_id : '')) ?>"><i class="bi bi-person-plus-fill"></i> Add New</a>
                <?php } ?>
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
                <form action="<?= base_url('Sales/purchase_list') ?>" method="POST" class="row align-items-center">
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
                            <label for="">Branch</label>
                            <select name="branch_id" id="branch_id" class="form-select select2" <?= (!empty($branch_locked)) ? 'disabled' : '' ?>>
                                <option value="">All Branches</option>
                                <?php if (!empty($branch_list)) {
                                    foreach ($branch_list as $branch) {
                                        $selected = ($branch_id == $branch->m_user_id) ? 'selected' : '';
                                ?>
                                        <option value="<?= $branch->m_user_id ?>" <?= $selected ?>><?= $branch->m_user_name ?></option>
                                <?php }
                                } ?>
                            </select>
                            <?php if (!empty($branch_locked)) { ?>
                                <!-- disabled select POST me value nahi bhejta, isliye hidden field zaroori -->
                                <input type="hidden" name="branch_id" value="<?= $branch_id ?>">
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="">Supplier </label>
                            <select name="suppiler_id" id="suppiler_id" class="form-select select2">
                                <option value="">All Supplier</option>
                                <?php
                                if (!empty($suplier_list)) {
                                    foreach ($suplier_list as $vat) {

                                        if ($suppiler_id == $vat->m_user_id) {
                                            $option1 = "selected";
                                        } else {
                                            $option1 = "";
                                        }

                                ?>
                                        <option value="<?php echo $vat->m_user_id; ?>" <?= $option1 ?>><?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?>
                                    <?php
                                    }
                                }

                                    ?>

                            </select>
                        </div>
                    </div>
                    <?php if ($this->session->userdata('user_type') == 8) { ?>
                        <div class="col-1">
                            <div class="form-group">
                                <label for="">Type</label>
                                <select name="type_pur" id="type_pur" class="form-select select2">
                                    <option value="">All</option>
                                    <option value="1" <?= ($type_pur == 1) ? 'selected' : '' ?>>Purchase</option>
                                    <option value="2" <?= ($type_pur == 2) ? 'selected' : '' ?>>Transfer</option>
                                </select>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-3 mt-4">
                        <button class="btn btn-info btn-sm" type="submit"><i class="bi bi-search mx-1"></i> Search</button>
                        <a class="btn btn-danger btn-sm" href="<?= base_url('Sales/purchase_list') ?>"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
                        <button class="btn btn-success btn-sm" type="button" onclick="printcustomtable()"><i class="bi bi-printer me-2"></i>Print</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row justify-content-evenly g-0">
            <div class="col-12">
                <div class="table-responsive bg-light printTableDiv" style="height: 64vh;">
                    <table id="purchase_tbl" class="my_custom_datatable table table-striped table-bordered dt-responsive nowra w-100">
                        <thead>
                            <tr>
                                <th>SNO</th>
                                <th>DATE</th>
                                <th>SUPPLIER NAME</th>
                                <th>PURCHASE NUMBER</th>
                                <th>TOTAL QTY</th>
                                <th>BILL AMT</th>
                                <th>EXPENSES</th>
                                <th>NET BILL AMT</th>
                                <th>TRANSPORT</th>
                                <th>CREATED BY</th>
                                <th width="10%">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $tolqty = 0;
                            $tolamt = 0;
                            $tolexp = 0;
                            $tolntamt = 0;
                            if (!empty($all_value)) {
                                foreach ($all_value as $value) {
                                    $tolqty += $value->tqty;
                                    $tolamt += $value->total_amount;
                                    $tolexp += $value->total_expense;
                                    $tolntamt = ($value->total_amount + $value->total_expense);
                                    $new_date = date("d-m-Y", strtotime($value->m_purcs_date));
                            ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $new_date; ?></td>
                                        <td><?php echo $value->supplier_name; ?></td>
                                        <td><?php echo $value->m_purcs_spo; ?></td>
                                        <td><?php echo $value->tqty; ?></td>
                                        <td><?php echo $value->total_amount; ?></td>
                                        <td><?php echo $value->total_expense; ?></td>
                                        <td><?php echo $value->total_amount + $value->total_expense; ?></td>
                                        <td><?php echo $value->m_purcs_truckno; ?></td>
                                        <td><?php echo $value->m_user_name ?: 'Admin'; ?></td>
                                        <td class="wd-30">
                                            <div class="d-flex">
                                                <button type="button" class="btn btn-primary btn-sm p-1 me-1" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $value->m_purcs_spo; ?>" title="View"><i class="bi bi-eye"></i></button>
                                                <!-- view Modal start -->
                                                <div class="modal fade" id="viewModal<?php echo $value->m_purcs_spo; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="row" style="display: contents;">
                                                                    <div class="col-md-10">
                                                                        <h4 class="modal-title"> Purchase Details (<?php echo $value->m_purcs_spo; ?>)</h4>
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
                                                                                    Name : <b><?php echo $value->supplier_name; ?></b>
                                                                                </div>
                                                                                <div class="col-md-6 pd-5 text-end">
                                                                                    Contact No : <b><?php echo $value->supplier_mobile; ?></b>
                                                                                </div>

                                                                                <div class="col-md-6 pd-5">
                                                                                    City : <b><?php echo $value->m_city_name; ?></b>
                                                                                </div>
                                                                                <div class="col-md-6 pd-5 text-end">
                                                                                    State: <b><?php echo $value->m_state_name; ?></b>
                                                                                </div>

                                                                                <div class="col-md-12 pd-5">
                                                                                    Address : <b><?php echo $value->supplier_address; ?></b>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="card p-3">
                                                                            <div class="row " style="margin: 0px;">
                                                                                <div class="col-md-6 pd-5">
                                                                                    Voucher No : <b><?php echo $value->m_purcs_spo; ?></b>
                                                                                </div>

                                                                                <div class="col-md-6 pd-5 text-end">
                                                                                    Truck Number : <b><?php echo $value->m_purcs_truckno; ?></b>
                                                                                </div>

                                                                                <div class="col-md-6 pd-5 ">
                                                                                    Purchase Date : <b><?php echo date('d-m-Y', strtotime($value->m_purcs_date)); ?></b>
                                                                                </div>
                                                                                <div class="col-md-6 pd-5 text-end">
                                                                                    Recieved BY : <b><?php echo $value->m_user_name ?: 'Admin'; ?></b>
                                                                                </div>

                                                                                <div class="col-md-12 pd-5">
                                                                                    Note : <b><?php echo $value->m_purcs_note; ?></b>
                                                                                </div>
                                                                            </div>
                                                                        </div>


                                                                    </div>

                                                                    <div class="col-md-12 mt-3">
                                                                        <label>List of Items Purchased</label>
                                                                        <div class="table-responsive">
                                                                            <table class="table table-striped table-bordered">
                                                                                <thead>
                                                                                    <th>Sn</th>
                                                                                    <th>Item Name</th>
                                                                                    <th>LOT NO</th>
                                                                                    <th>Quantity</th>
                                                                                    <th>Unit</th>
                                                                                    <th>Weight (KG)</th>
                                                                                    <th>Price</th>
                                                                                    <th>Crate</th>
                                                                                    <th>Total</th>
                                                                                </thead>
                                                                                <tbody id="modal_body_contant">

                                                                                    <?php
                                                                                    $item_list =  $this->db->select('master_purchase_tbl.*,m_item_name,group.m_itgrp_title as groupname,crate.m_itgrp_title as cratetype,unit.m_itgrp_title as unitname')
                                                                                        ->join('master_item_tbl mit', 'mit.m_item_id = master_purchase_tbl.m_purcs_item', 'left')
                                                                                        ->join('master_itemgroup_tbl as group', 'group.m_itgrp_id = mit.m_item_group', 'left')
                                                                                        ->join('master_itemgroup_tbl as crate', 'crate.m_itgrp_id = mit.m_item_crate', 'left')
                                                                                        ->join('master_itemgroup_tbl as unit', 'unit.m_itgrp_id = mit.m_item_unit', 'left')
                                                                                        ->where('m_purcs_spo', $value->m_purcs_spo)->order_by('m_item_name')->get('master_purchase_tbl')->result();

                                                                                    if (!empty($item_list)) {
                                                                                        foreach ($item_list as $cua => $key) {

                                                                                            echo '<tr>
                                                                                    <td>' . ($cua + 1) . '</td>
                                                                                    <td>' . $key->m_item_name . ' </td>
                                                                                    <td>' . $key->m_purcs_lot . ' </td>
                                                                                    <td>' . $key->m_purcs_qty . ' </td>
                                                                                    <td>' . $key->unitname . '</td>
                                                                                    <td>' . $key->m_purcs_weight . '</td>
                                                                                    <td>' . money2($key->m_purcs_price) . '</td>
                                                                                    <td>' . $key->cratetype . '</td>
                                                                                    <td>' . money2($key->m_purcs_total) . '</td>
                                                                                   
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
                                                                                    <td>bill</td>
                                                                                    <td><?= money2($value->m_purcs_comm) ?></td>
                                                                                    <td><?= money2($value->m_purcs_comm) ?></td>

                                                                                    <td>Fright</td>
                                                                                    <td>Bill</td>
                                                                                    <td><?= money2($value->m_purcs_fright) ?></td>
                                                                                    <td><?= money2($value->m_purcs_fright) ?></td>

                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Hamali</td>
                                                                                    <td>Bill</td>
                                                                                    <td><?= money2($value->m_purcs_hamali) ?></td>
                                                                                    <td><?= money2($value->m_purcs_hamali) ?></td>

                                                                                    <td>Charity</td>
                                                                                    <td>Bill</td>
                                                                                    <td><?= money2($value->m_purcs_charity) ?></td>
                                                                                    <td><?= money2($value->m_purcs_charity) ?></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Packaging</td>
                                                                                    <td>Bill</td>
                                                                                    <td><?= money2($value->m_purcs_packaging) ?></td>
                                                                                    <td><?= money2($value->m_purcs_packaging) ?></td>

                                                                                    <td>Loading</td>
                                                                                    <td>Bill</td>
                                                                                    <td><?= money2($value->m_purcs_loading) ?></td>
                                                                                    <td><?= money2($value->m_purcs_loading) ?></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Advance</td>
                                                                                    <td>Bill</td>
                                                                                    <td><?= money2($value->m_purcs_advance) ?></td>
                                                                                    <td><?= money2($value->m_purcs_advance) ?></td>

                                                                                    <td>Other</td>
                                                                                    <td>Bill</td>
                                                                                    <td><?= money2($value->m_purcs_others) ?></td>
                                                                                    <td><?= money2($value->m_purcs_others) ?></td>
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

                                                <?php if ($this->session->userdata('user_type') == 8) { ?>
                                                    <!-- view modal end -->
                                                    <?php if ($value->m_purcs_type != 2) { ?>
                                                        <a href="<?php echo base_url('Sales/add_purchase?id=') . $value->m_purcs_spo; ?>" class="btn btn-info btn-sm p-1 me-1" title="Edit" data-toggle="tooltip"><i class="bi bi-pencil-square"></i></a>
                                                        <button class="btn btn-danger btn-sm delete-purchase p-1" data-value="<?php echo $value->m_purcs_spo; ?>" title="Delete" data-toggle="tooltip"><i class="bi bi-trash"></i></button>
                                                    <?php } else { ?>
                                                        <button class="btn btn-danger btn-sm delete-transfer p-1" data-value="<?php echo $value->m_purcs_spo; ?>" title="Delete Transfer" data-toggle="tooltip"><i class="bi bi-trash"></i></button>
                                                    <?php } ?>
                                                <?php } ?>
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
                            <th colspan="4">TOTAL</th>
                            <th><?= $tolqty ?></th>
                            <th>₹<?= $tolamt ?></th>
                            <th>₹<?= $tolexp ?></th>
                            <th>₹<?= $tolntamt ?></th>
                            <th colspan="3"></th>
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
<script>
    $(document).ready(function() {
        $("#purchase_tbl").on("click", ".delete-transfer", function() {
            var clkbtn = $(this);
            clkbtn.prop('disabled', true);
            var dlt_id = $(this).data('value');

            swal({
                title: "Are you sure?",
                text: "Once deleted, the stock will be restored to Head Office and the branch balance will be reversed!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo site_url('Transfer/delete_transfer'); ?>",
                        data: {
                            delete_id: dlt_id
                        },
                        dataType: "JSON",
                        success: function(data) {
                            if (data.status == 'success') {
                                swal(data.message, {
                                    icon: "success",
                                    timer: 1000
                                });
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            } else {
                                clkbtn.prop('disabled', false);
                                swal(data.message, {
                                    icon: "error"
                                });
                            }
                        }
                    });
                } else {
                    clkbtn.prop('disabled', false);
                }
            });
        });
    });
</script>
<!-- ========================Footer================Fix======= -->