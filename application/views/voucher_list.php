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

                <h6 class="m-0 text-white">
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

        <div class="row justify-content-evenly g-2 mb-2">
            <div class="col-12">
                <form action="<?= base_url('Sales/voucher_list/') . $type ?>" method="POST" class="row align-items-center">

                    <div class="col-2">
                        <div class="form-group">
                            <label for="">From date</label>
                            <input type="date" name="from_date" id="from_date" class="form-control" value="<?= $from_date ?>">
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="">To date</label>
                            <input type="date" name="to_date" id="to_date" class="form-control" value="<?= $to_date ?>">
                        </div>
                    </div>
                    <div class="col-4">
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
                                <option value="0" <?= (isset($branch_id) && (string) $branch_id === '0') ? 'selected' : '' ?>>Head Office</option>
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
                        <button class="btn btn-info btn-sm" type="submit"><i class="bi bi-search mx-1"></i> Search</button>
                        <a class="btn btn-danger btn-sm" href="<?= base_url('Sales/voucher_list/') . $type ?>"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#myAddModal" class="btn btn-primary btn-sm" title="Add New">Add New</button>
                        <button class="btn btn-success btn-sm" type="button" onclick="printcustomtable()"><i class="bi bi-printer me-2"></i>Print</button>

                    </div>
                </form>
            </div>
        </div>

        <div class="row justify-content-evenly g-0">
            <div class="col-12">
                <div class="table-responsive bg-light printTableDiv" style="height: 64vh;">
                    <table id="voucher_tbl" class="table table-striped table-bordered dt-responsive nowra w-100">
                        <thead>
                            <tr>
                                <th>Sno</th>
                                <th>Date</th>
                                <th>Account Type</th>
                                <th>Account Name</th>
                                <th>Amount Type</th>
                                <th>Amount</th>
                                <th>Remark</th>
                                <th class="actionth">Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $sumtamt = 0;
                            if (!empty($all_value)) {
                                foreach ($all_value as $value) {
                                    $sumtamt += $value->m_voucher_amount;

                            ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($value->m_voucher_date)); ?></td>
                                        <?php
                                        switch ($value->account_type) {
                                            case 1:
                                                $account_type = 'Customer';
                                                break;
                                            case 2:
                                                $account_type = 'Supplier';
                                                break;
                                            case 3:
                                                $account_type = 'Expense';
                                                break;
                                            case 4:
                                                $account_type = 'Loader';
                                                break;
                                            case 5:
                                                $account_type = 'Staff';
                                                break;
                                            case 6:
                                                $account_type = 'General';
                                                break;
                                            case 7:
                                                $account_type = 'Investment';
                                                break;
                                        }

                                        ?>
                                        <td><?php echo $account_type; ?></td>
                                        <td><?php echo $value->m_user_name; ?></td>
                                        <td><?= $value->m_voucher_type == 1 ? "Credit" : "Debit"; ?></td>
                                        <td><?php echo money2($value->m_voucher_amount); ?></td>
                                        <td><?php echo $value->m_voucher_remark  ?></td>

                                        <td class="actionth">

                                            <button type="button" class="btn btn-primary btn-sm p-1 me-1 myeditpayModal" data-value="<?php echo $value->m_voucher_id; ?>" data-bs-toggle="modal" data-bs-target="#myeditModal<?php echo $value->m_voucher_id; ?>" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                            <button class="btn btn-danger btn-action btn-sm p-1 me-1 delete-voucher" data-value="<?php echo $value->m_voucher_id; ?>" title="Delete"><i class="bi bi-trash"></i></button>
                                            <!-- view Modal start -->
                                            <div id="myeditModal<?php echo $value->m_voucher_id; ?>" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header justify-content-between">
                                                            <h4 class="modal-title">Edit Voucher </h4>
                                                            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form method="POST" action="<?php echo site_url('Sales/update_voucher_data') ?>" enctype="multipart/form-data">
                                                            <div class="modal-body">
                                                                <div class="row g-3">
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label>Date</label>
                                                                            <input type="hidden" name="m_voucher_id" value="<?= $value->m_voucher_id ?>">
                                                                            <input type="date" name="m_voucher_date" class="form-control" value="<?= $value->m_voucher_date ?>">
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label><?= $account_type ?> Name </label>
                                                                            <input type="hidden" id="m_voucher_account<?= $value->m_voucher_id ?>" name="m_voucher_account" value="<?= $value->m_voucher_account ?>">
                                                                            <input type="hidden" id="m_voucher_cust<?= $value->m_voucher_id ?>" name="m_voucher_accountid" value="<?= $value->m_voucher_accountid ?>">
                                                                            <input type="hidden" id="precust<?= $value->m_voucher_id ?>" name="precust" value="<?= $value->m_voucher_accountid ?>">

                                                                            <input list="m_supplier_list<?= $value->m_voucher_id ?>" data-value="<?= $value->m_voucher_id ?>" placeholder="Add Account" class="form-control editaccoutin" value="<?= $value->m_user_name ?>">

                                                                            <datalist id="m_supplier_list<?= $value->m_voucher_id ?>">

                                                                            </datalist>

                                                                        </div>


                                                                    </div>

                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label>Amount <span class="text-danger">*</span></label>
                                                                            <input type="hidden" name="preamount" value="<?= $value->m_voucher_amount ?>">
                                                                            <input type="text" name="m_voucher_amount" id="m_voucher_amount" class="form-control" required="" value="<?= $value->m_voucher_amount ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">
                                                                            <label>Amount Type</label>
                                                                            <input name="m_voucher_type" type="hidden" id="m_voucher_type" value="<?= $value->m_voucher_type ?>">
                                                                            <input class="form-control" value="<?= $value->m_voucher_type == 1 ?"Credit":"Debit"?>" readonly>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label>Remark</label>
                                                                            <textarea name="m_voucher_remark" id="m_voucher_remark" class="form-control"><?= $value->m_voucher_remark ?></textarea>
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
                                <th colspan="5">Total</th>
                                <th>₹<?= $sumtamt ?></th>
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
                <h4 class="modal-title">Add New Voucher</h4>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <form method="POST" action="<?php echo site_url('Sales/insert_voucher_data') ?>" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Accounts</label>
                                        <select name="m_voucher_account" id="m_voucher_account" class="form-control" required>
                                            <option value="">Select Account Type</option>
                                            <option value="1">Customer</option>
                                            <option value="2">Supplier</option>
                                            <option value="3">Expense</option>
                                            <option value="4">Loader</option>
                                            <option value="5">Staff</option>
                                            <option value="6">General</option>
                                            <option value="7">Investment</option>

                                        </select>
                                    </div>

                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Date<span class="text-danger">*</span></label>
                                        <input type="date" name="m_voucher_date" id="m_voucher_date" class="form-control" required="" value="<?= date('Y-m-d') ?>">
                                    </div>
                                </div>
                                <?php if ($this->session->userdata('user_type') == 8) { ?>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Branch</label>
                                        <select name="m_voucher_branch" id="m_voucher_branch" class="form-control">
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
                                        <th width="20%" id="selet_label">Supplier Name</th>
                                        <th width="15%">Amount</th>
                                        <th width="10%">Amount Type</th>
                                        <th>Remark</th>
                                        <th width="5%"></th>

                                    </thead>
                                    <tbody id="tableblock">

                                    </tbody>
                                </table>

                                <input list="m_supplier_list" id="item_serch_inp" placeholder="Add Account" class="form-control" style="width: 50%; margin-bottom:5px;">

                                <datalist id="m_supplier_list">
                                    <option value=""> Select Accounts Type First </option>
                                </datalist>

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

<!--- Add model end above-->


<!-- ========================Footer================Fix======= -->
<?php $this->view('footer'); ?>
<?php $this->view('js/sale_js') ?>
<?php $this->view('js/custom_js'); ?>

<script>
    // let x = $('#rowunt').val();

    $(document).ready(function(e) {

        $(document).on('change', '#m_voucher_account', function() {
            let acct_type = $(this).val();
            get_account_list(acct_type,'')
        });

        // Live branch cascading: re-scope the currently selected account
        // type's picker when the modal's own Branch select changes.
        $(document).on('change', '#m_voucher_branch', function() {
            let acct_type = $('#m_voucher_account').val();
            if (acct_type) {
                get_account_list(acct_type, '')
            }
        });

        $(document).on('click', '.myeditpayModal', function() {
            let filed_id = $(this).data('value');
            let acct_type = $('#m_voucher_account'+filed_id).val();
            get_account_list(acct_type,filed_id)
        });

        let incount = 0;
        $(document).on('change', '#item_serch_inp', function() {
            var custid = $("#m_supplier_list option[value='" + $(this).val() + "']").attr('data-id')
            var custname = $("#m_supplier_list option[value='" + $(this).val() + "']").attr('data-name')
            var custmobile = $("#m_supplier_list option[value='" + $(this).val() + "']").attr('data-mobile')

            incount++

            addrow(incount)
            $('#m_voucher_accountid' + incount).val(custid);
            $('#cust_name' + incount).html(custname + ' | ' + custmobile);

            $(this).val('');
        });

        let incot = 0;


        $(document).on('change', '.editaccoutin', function() {
            var acid = $(this).data('value');
            var custid = $("#m_supplier_list" + acid + " option[value='" + $(this).val() + "']").attr('data-id')
            var custname = $("#m_supplier_list" + acid + " option[value='" + $(this).val() + "']").attr('data-name')
            var custmobile = $("#m_supplier_list" + acid + " option[value='" + $(this).val() + "']").attr('data-mobile')
            $('#m_voucher_cust' + acid).val(custid);

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
                                            <td> <input type="hidden" name="m_voucher_accountid[]" id="m_voucher_accountid` + x + `" value="">
                                            <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="m_voucher_amount[]" id="m_voucher_amount` + x + `" class="form-control"></td>
                                            <td>  <select name="m_voucher_type[]" id="m_voucher_type" class="form-control">
                                    <option value="1">Credit</option>
                                    <option value="2">Debit</option>
                                </select> </td>
                                            <td><input type="text" name="m_voucher_remark[]" id="m_voucher_remark` + x + `" class="form-control"></td>
                                                            <td>  <button type="button" class="btn btn-danger px-1 py-0 removerow" data-count="` + x + `" title="Delete"><i class="bi bi-trash"></i></button></td>
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
                url: "<?php echo site_url('Sales/get_vourcher_accounts'); ?>",
                data: {
                    acct_type,
                    branch_id: $('#m_voucher_branch').val()
                },
                dataType: "JSON",
                success: function(data) {
                    // The rotated CSRF token in this response is picked up
                    // globally by the ajaxSuccess hook in footer.php.
                    if (data != '') {
                        $('#m_supplier_list'+filed_id).empty()

                        $.each(data.list, function(i, item) {
                            if (data.listtype == 1) {
                                $('#m_supplier_list'+filed_id).append(`<option value="${item.m_cust_name} | ${item.m_cust_mobile}" data-name="${item.m_cust_name }" data-id="${item.m_cust_id }" data-mobile="${item.m_cust_mobile }">${item.m_cust_name} | ${item.m_cust_mobile }</option>`);
                            } else if (data.listtype == 2) {
                                $('#m_supplier_list'+filed_id).append(`<option value="${item.m_user_name} | ${item.m_user_mobile}" data-name="${item.m_user_name }" data-id="${item.m_user_id }" data-mobile="${item.m_user_mobile }">${item.m_user_name} | ${item.m_user_mobile }</option>`);
                            } else {
                                $('#m_supplier_list'+filed_id).append(`<option value="${item.m_group_name} | ${item.m_group_number}" data-name="${item.m_group_name }" data-id="${item.m_group_id }" data-mobile="${item.m_group_number }">${item.m_group_name} | ${item.m_group_number }</option>`);
                            }

                        })
                    } else {
                        $('#m_supplier_list'+filed_id).empty()
                    }
                }
            });

     }

</script>
<!-- ========================Footer================Fix======= -->