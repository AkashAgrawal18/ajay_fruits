<?php include("head.php"); ?>
<?php include("header.php"); ?>
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
                <button onclick="history.back()" class="btn btn-danger btn-sm">
                    <i class="bi bi-box-arrow-left me-2"></i>Exit
                </button>
            </div>
        </div>
    </div>
</section>
<section class="py-3 d-flex" style="background:#f3f3ff;min-height:75vh;">
    <div class="container-fluid">

        <form method="post" action="#" id="frm-add-custgrp">

            <?php if (!empty($edit_value)) {

                $custgrp_name = $edit_value->m_custgrp_name;
                $custgrp_code = $edit_value->m_custgrp_code;
                $custgrp_user = $edit_value->m_custgrp_user;
            } else {
                $custgrp_name = '';
                $custgrp_code = '';
                $custgrp_user = '';
            } ?>


            <div class="row mb-1 g-3">
                <div class="col-9">
                    <div class="row mb-1 g-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Group Name<span class="text-danger">*</span></label>
                                <input type="text" name="m_custgrp_name" id="m_custgrp_name" class="form-control" placeholder="Enter Group Name" value="<?= $custgrp_name ?>">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Staff Name <span class="text-danger">*</span></label>

                                <select name="m_custgrp_user" id="m_custgrp_user" class="form-select select2" required autofocus>
                                    <option value="">--Select--</option>
                                    <?php
                                    if (!empty($staff_list)) {
                                        foreach ($staff_list as $vat) {

                                            if ($custgrp_user == $vat->m_user_id) {
                                                $option1 = "selected";
                                            } else {
                                                $option1 = "";
                                            }

                                    ?>
                                            <option value="<?php echo $vat->m_user_id; ?>" <?= $option1 ?>><?= $vat->m_user_name; ?>
                                        <?php
                                        }
                                    }

                                        ?>

                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Add Customer <span class="text-danger">*</span></label>
                                <!-- <button type="button" class="btn btn-sm btn-primary pull-right p-1 mb-1" onclick="addrow()">Add row</button> -->
                                <input list="items_datalist" id="item_serch_inp" placeholder="Add Items" class="form-control">

                                <datalist id="items_datalist">
                                    <?php
                                    if (!empty($cust_list)) {
                                        foreach ($cust_list as $Vitem) {
                                            // if ($edit_value->s_item_itemid == $Vitem->m_item_id) {
                                            //     $op = 'selected';
                                            // } else {
                                            //     $op = '';
                                            // }
                                    ?>
                                            <option value="<?php echo $Vitem->m_cust_name; ?>" data-custid="<?= $Vitem->m_cust_id ?>" data-address="<?= $Vitem->m_cust_address ?>" data-mobile="<?= $Vitem->m_cust_mobile ?>"><?php echo $Vitem->m_cust_name; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </datalist>
                            </div>
                        </div>

                        <div class="col-md-12 mb-2">

                            <div class="table-responsive">

                                <table class="table table-striped table-bordered dt-responsive nowra">
                                    <thead>
                                        <th>Sn</th>
                                        <th>Customer Name</th>
                                        <th>Mobile No.</th>
                                        <th>Address</th>
                                        <th></th>
                                    </thead>
                                    <tbody id="tableblock">
                                        <?php if (!empty($id)) {
                                            $cou = 1;
                                            // foreach ($edit_value as $kry) {
                                            //     $cou++;
                                        ?>
                                            <tr>
                                                <td id="rowcount<?= $cou ?>"><input type="hidden" name="m_custgrp_id[]" id="m_custgrp_id<?= $cou ?>" value="<?= $edit_value->m_custgrp_id ?>"><input type="hidden" name="m_custgrp_customer[]" id="m_custgrp_customer" value="<?= $edit_value->m_custgrp_customer ?>"><?= $cou ?></td>
                                                <td id="cust_name<?= $cou ?>"><?= $edit_value->m_cust_name ?></td>
                                                <td id="cust_mobile<?= $cou ?>"><?= $edit_value->m_cust_mobile ?></td>
                                                <td id="cust_address<?= $cou ?>"><?= $edit_value->m_cust_address ?></td>
                                                <td> <button type="button" class="btn btn-danger p-1 del-custgrp-item" data-value="<?php echo $edit_value->m_custgrp_id; ?>" title="Delete"><i class="bi bi-trash"></i></button></td>
                                            </tr>
                                        <?php // }
                                            echo '<input type="hidden" id="rowunt" value="' . $cou . '">';
                                        }  ?>
                                        <input type="hidden" id="rowunt" value="0">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-3 text-center">
                    <img src="<?= base_url('assets/imgs/addUser.svg') ?>" alt="" class="w-100">
                    <div class="row pt-3">
                        <div class="col-6">
                            <a href="<?= base_url('Accounts/custgrp_list') ?>" class="btn btn-primary btn-md w-100 mb-3">Group List</a>
                        </div>
                        <div class="col-6">
                            <a href="<?php echo site_url('Accounts/custgrp_list') ?>" class="btn btn-danger btn-md w-100 mb-3">Cancel</a>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success btn-lg w-100" id="btn-add-custgrp">Save Details</button>
                        </div>
                    </div>
                </div>

            </div>

        </form>

    </div>
</section>

<!-- ========== Page Content ========== -->
<?php include("footer.php"); ?>
<?php $this->view('js/user_js') ?>

<script>
    // let x = $('#rowunt').val();

    $(document).ready(function(e) {

        let incount = $('#rowunt').val();
        $(document).on('change', '#item_serch_inp', function() {
            var custid = $("#items_datalist option[value='" + $(this).val() + "']").attr('data-custid');
            var custname = $(this).val();
            var mobile = $("#items_datalist option[value='" + $(this).val() + "']").attr('data-mobile');
            var address = $("#items_datalist option[value='" + $(this).val() + "']").attr('data-address');
            incount++

            addrow(incount)
            $('#cust_name' + incount).html(custname);
            $('#cust_mobile' + incount).html(mobile);
            $('#cust_address' + incount).html(address);
            $('#m_custgrp_customer' + incount).val(custid);

            $(this).val('');
        });

        $(document).on("click", '.removerow', function() {
            var count = $(this).data('count');

            $('#rowcot' + count).remove();
            calculate_function(count)
        });


    });


    function addrow(x) {
        // x++;
        $('#tableblock').append(` <tr id="rowcot` + x + `">
                                                            <td id="rowcount` + x + `"><input type="hidden" name="m_custgrp_customer[]" id="m_custgrp_customer` + x + `" value="">
                                                            <input type="hidden" name="m_custgrp_id[]" id="m_custgrp_id` + x + `" value="">` + x + `</td>
                                            <td id="cust_name` + x + `"></td>
                                            <td id="cust_mobile` + x + `"></td>
                                            <td id="cust_address` + x + `"></td>
                                                            <td>  <button type="button" class="btn btn-danger p-1 removerow" data-count="` + x + `" title="Delete"><i class="bi bi-trash"></i></button></td>
                                                            </tr>`);

    }
</script>