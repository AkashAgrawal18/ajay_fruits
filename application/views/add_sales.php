<?php include("head.php"); ?>
<?php include("header.php"); ?>
<!-- ========== Page Content ========== -->

<style>
    table td {
        padding: 1px !important;
    }
</style>

<section class="py-1" style="background: #bbf;">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-6">
                <h6 class="m-0 text-white">
                    Home >> <span class="text-primary"><?= $pagename ?></span>
                </h6>
            </div>
            <div class="col-6 text-end">
                <a href="<?php echo site_url('Sales/sales_list') ?>" class="btn btn-sm btn-primary">Sales List </a>
                <button onclick="history.back()" class="btn btn-danger btn-sm">
                    <i class="bi bi-box-arrow-left me-2"></i>Exit
                </button>
            </div>
        </div>
    </div>
</section>
<section class="py-3 d-flex" style="background:#f3f3ff;min-height:75vh;">
    <div class="container-fluid">

        <form method="post" id="frm-add-sales">

            <?php if (!empty($edit_value)) {
                $sale_spo = $edit_value[0]->m_sale_spo;
                $sale_date = $edit_value[0]->m_sale_date;
                $sale_trackno = $edit_value[0]->m_sale_trackno;
                $sale_customer = $edit_value[0]->m_sale_customer;
                $sale_voucher = $edit_value[0]->m_sale_voucher;
                $sale_user = $edit_value[0]->m_sale_user;
                $sale_comm = $edit_value[0]->m_sale_comm;
                $sale_comrate = $edit_value[0]->m_sale_comrate;
                $sale_fright = $edit_value[0]->m_sale_fright;
                $sale_hamali = $edit_value[0]->m_sale_hamali;
                $sale_others = $edit_value[0]->m_sale_others;

                $sale_note = $edit_value[0]->m_sale_note;
            } else {
                $sale_spo = '';
                $sale_date = date('Y-m-d');
                $sale_trackno = '';
                $sale_customer = '';
                $sale_voucher = '';
                $sale_user = '';
                $sale_comm = 0;
                $sale_comrate = 0;
                $sale_fright = 0;
                $sale_hamali = 0;
                $sale_others = 0;
                $sale_note = '';
            } ?>


            <div class="row mb-1 g-3">
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Date<span class="text-danger">*</span></label>
                        <input type="hidden" name="m_sale_spo" id="m_sale_spo" value="<?= $sale_spo ?>">
                        <input type="hidden" name="precust" id="precust" value="<?= $sale_customer ?>">
                        <input type="hidden" name="m_sale_customer" id="m_sale_customer" value="<?= $sale_customer ?>">
                        <input type="date" max="<?= date('Y-m-d') ?>" name="m_sale_date" id="m_sale_date" class="form-control" required="" value="<?= $sale_date ?>">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label>Truck No<span class="text-danger">*</span></label>
                        <input type="text" name="m_sale_trackno" id="m_sale_trackno" class="form-control" placeholder="Enter Truck No" value="<?= $sale_trackno ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Voucher No</label>
                        <input type="text" name="m_sale_voucher" id="m_sale_voucher" class="form-control" placeholder="Enter Voucher No" value="<?= $sale_voucher ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Customer Name <span class="text-danger">*</span></label>
                        <select name="m_sale_customer" id="m_sale_customer" class="form-select select2" required autofocus>
                            <option value="">--Select--</option>
                            <?php
                            if (!empty($custo_list)) {
                                foreach ($custo_list as $vat) {

                                    if ($sale_customer == $vat->m_cust_id) {
                                        $option1 = "selected";
                                    } else {
                                        $option1 = "";
                                    }

                            ?>
                                    <option value="<?php echo $vat->m_cust_id; ?>" <?= $option1 ?>><?= $vat->m_cust_name . ' | ' . $vat->m_cust_mobile; ?>
                                <?php
                                }
                            }

                                ?>

                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label>Agent Name </label>
                        <select name="m_sale_user" id="m_sale_user" class="form-select select2">
                            <option value="">--Select--</option>
                            <?php
                            if (!empty($staff_list)) {
                                foreach ($staff_list as $vat) {

                                    if ($sale_user == $vat->m_user_id) {
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

                <div class="col-md-12 mb-2">

                    <div class="table-responsive">

                        <table class="table table-striped table-bordered dt-responsive nowra">
                            <thead>
                                <th>Sn</th>
                                <th>Item Name</th>
                                <th>Lot Number</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Weight</th>
                                <th>Price</th>
                                <th>Crate</th>
                                <th>Total</th>
                                <th></th>
                            </thead>
                            <tbody id="tableblock">
                                <?php if (!empty($id)) {
                                    $cou = 0;
                                    $pre_grandtotal = ($edit_value[0]->m_sale_comm + $edit_value[0]->m_sale_fright + $edit_value[0]->m_sale_hamali + $edit_value[0]->m_sale_others);
                                    foreach ($edit_value as $kry) {
                                        $pre_grandtotal += $kry->m_sale_total;
                                        $cou++;
                                ?>
                                        <tr>
                                            <td id="rowcount<?= $cou ?>"><?= $cou ?></td>
                                            <td id="item_name<?= $cou ?>"><?= $kry->m_item_name ?> <input type="hidden" name="m_sale_id[]" id="m_sale_id<?= $cou ?>" value="<?php echo $kry->m_sale_id; ?>">
                                                <input type="hidden" name="m_sale_item[]" id="m_sale_item<?= $cou ?>" value="<?php echo $kry->m_sale_item; ?>">
                                            </td>
                                            <td>
                                                <input type="hidden" name="m_sale_lot[]" id="m_sale_lot<?= $cou ?>" value="<?php echo $kry->m_sale_lot; ?>" readonly>
                                                <?= $kry->m_sale_lot; ?> | <?= $kry->pur_lotno; ?>
                                            </td>
                                            <td><input type="number" id="m_sale_qty<?= $cou ?>" name="m_sale_qty[]" class="prodqty calcuclass" data-count="<?= $cou ?>" style="width:80px" max="<?= ($kry->available_stock + $kry->m_sale_qty) ?>" value="<?php echo $kry->m_sale_qty; ?>">
                                                <input type="hidden" name="pre_item_qty[]" value="<?php echo $kry->m_sale_qty; ?>">
                                                <input type="hidden" name="item_fright[]" id="item_fright_rate<?= $cou ?>" value="<?= $kry->m_item_fright ?>">
                                                <input type="hidden" name="item_fright_total[]" id="item_fright_total<?= $cou ?>" class="pfrighttotal" value="<?= $kry->m_item_fright * $kry->m_sale_qty ?>">
                                            </td>
                                            </td>
                                            <td id="item_unit<?= $cou ?>"><?= $kry->unitname ?></td>
                                            <td><input type="text" id="m_sale_weight<?= $cou ?>" name="m_sale_weight[]" class="prodweight calcuclass" data-count="<?= $cou ?>" style="width:150px" value="<?php echo $kry->m_sale_weight; ?>"></td>
                                            <td><input type="text" id="m_sale_price<?= $cou ?>" name="m_sale_price[]" class="prodprice calcuclass" data-count="<?= $cou ?>" style="width:80px" value="<?php echo $kry->m_sale_price; ?>">
                                                <input type="hidden" id="m_sale_crate<?= $cou ?>" name="m_sale_crate[]" class="prodcrate" data-count="<?= $cou ?>" style="width:150px" value="<?php echo $kry->m_sale_crate; ?>">
                                            </td>
                                            <td id="item_crate<?= $cou ?>"><?= $kry->cratetype ?></td>
                                            <td><input type="number" id="s_item_nettotal<?= $cou ?>" name="m_sale_total[]" class="pnettotal" data-count="<?= $cou ?>" style="width:150px" value="<?php echo $kry->m_sale_total; ?>" readonly></td>
                                            <td> <button type="button" class="btn btn-danger px-1 py-0 del-sales-id" data-value="<?php echo $kry->m_sale_id; ?>" title="Delete"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                <?php }
                                    echo '<input type="hidden" id="rowunt" value="' . $cou . '">';
                                } ?>
                                <input type="hidden" id="rowunt" value="0">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">Total </td>
                                    <td id="qty_total"></td>
                                    <td></td>
                                    <td id="wgt_total"></td>
                                    <td></td>
                                    <td id="crate_total"></td>
                                    <td id="grand_total"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        <input type="hidden" id="pre_grand_total" name="pre_grand_total" value="<?= isset($pre_grandtotal) ? $pre_grandtotal : 0 ?>">
                        <input list="items_datalist" id="item_serch_inp" placeholder="Add Items" class="form-control" style="width: 50%; margin-bottom:5px;">

                        <datalist id="items_datalist">
                            <?php
                            if (!empty($item_list)) {
                                foreach ($item_list as $Vitem) {

                            ?>
                                    <option value="<?= $Vitem->m_item_name; ?>" data-itemid="<?= $Vitem->m_item_id ?>" data-price="<?= $Vitem->m_item_price ?>" data-ifright="<?= $Vitem->m_item_fright ?>" data-crate="<?= $Vitem->m_crate_name ?>" data-unit="<?= $Vitem->m_unit_name ?>"><?php echo $Vitem->m_item_name; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </datalist>


                    </div>
                </div>

                <div class="col-md-4">
                    <table class="table table-striped table-bordered dt-responsive nowra">
                        <thead>
                            <th>Expenses</th>
                            <th>Per</th>
                            <th>Rate</th>
                            <th>Amount</th>

                        </thead>
                        <tbody>
                            <tr>
                                <td>Commission</td>
                                <td>Percent</td>
                                <td>
                                    <input type="text" name="m_sale_comrate" id="m_sale_comrate" class="cal_exp" placeholder="Enter Commission rate" value="<?= $sale_comrate ?>">
                                    <input type="hidden" name="m_sale_comm" id="m_sale_comm" value="<?= $sale_comm ?>">
                                </td>
                                <td id="exp_comm"><?= $sale_comm ?></td>
                            </tr>
                            <tr>
                                <td>Fright/Labour</td>
                                <td>Bill</td>
                                <td><input type="text" name="m_sale_fright" id="m_sale_fright" class="cal_exp" placeholder="Enter Fright" value="<?= $sale_fright ?>"></td>
                                <td id="exp_fright"><?= $sale_fright ?></td>
                            </tr>
                            <tr>
                                <td>Hamali</td>
                                <td>Bill</td>
                                <td><input type="text" name="m_sale_hamali" id="m_sale_hamali" class="cal_exp" placeholder="Enter Hamali" value="<?= $sale_hamali ?>"></td>
                                <td id="exp_hamali"><?= $sale_hamali ?></td>
                            </tr>
                            <tr>
                                <td>Other</td>
                                <td>Bill</td>
                                <td><input type="text" name="m_sale_others" id="m_sale_others" class="cal_exp" placeholder="Enter Others" value="<?= $sale_others ?>"></td>
                                <td id="exp_other"><?= $sale_others ?></td>
                            </tr>

                        </tbody>

                    </table>

                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Note </label>
                        <input type="text" name="m_sale_note" id="m_sale_note" class="form-control" placeholder="Enter Note" value="<?= $sale_note ?>">
                    </div>
                    <div class="form-layout-submit text-end mt-2">
                        <button type="submit" id="btn-add-sales" class="btn btn-block btn-info">Save</button>
                        <a href="<?php echo site_url('Sales/sales_list') ?>" class="btn btn-block btn-danger">Cancel </a>

                    </div>
                </div>
                <div class="col-md-2 text-end fw-bold">

                    <div class="form-group">
                        <label>Total Amount : </label>
                        <label id="total_amount">0 </label>
                    </div>
                    <div class="form-group">
                        <label>Expenses : </label>
                        <label id="total_expenses">0 </label>
                    </div>
                    <hr style="margin: 0.1rem 0;">
                    <div class="form-group">
                        <label>Net Total : </label>
                        <label id="net_amount">0 </label>
                    </div>
                    <hr style="margin: 0.1rem 0;">
                </div>
            </div>

        </form>

    </div>
</section>

<!-- ========== Page Content ========== -->
<?php include("footer.php"); ?>
<?php $this->view('js/sale_js') ?>

<script>
    // let x = $('#rowunt').val();

    $(document).ready(function(e) {
        total_calculate_fun()
        calculate_expenses()
        let incount = $('#rowunt').val();
        $(document).on('change', '#item_serch_inp', function() {
            var price = $("#items_datalist option[value='" + $(this).val() + "']").attr('data-price')
            var unit = $("#items_datalist option[value='" + $(this).val() + "']").attr('data-unit')
            var crate = $("#items_datalist option[value='" + $(this).val() + "']").attr('data-crate')
            var itemid = $("#items_datalist option[value='" + $(this).val() + "']").attr('data-itemid')
            var frightrate = $("#items_datalist option[value='" + $(this).val() + "']").attr('data-ifright')
            var iname = $(this).val();
            incount++

            $.ajax({
                url: "<?php echo site_url('Reports/get_lotwise_item'); ?>",
                type: "POST",
                data: {
                    item_id: itemid
                },
                dataType: "JSON",
                success: function(data) {
                    if (data == '') {
                        swal('No Purchase Record Found', {
                            icon: "warning",
                            timer: 5000,
                        });
                        return false;
                    } else {

                        $('#m_sale_lot' + incount).empty();
                        $.each(data, function(i, item) {
                            $('#m_sale_lot' + incount).append(`
                                                <option value="` + item.m_purcs_id + `" data-avail_qty="` + item.m_purcs_available + `" >` + item.m_purcs_lot + ` | ` + item.m_purcs_available + ` | ` + item.m_user_trademark + ` | ` + item.m_purcs_date + `</option>`);
                        });
                        // alert(data[0].m_machine_title)
                        // console.log(data[0].m_purcs_id)
                    }


                },
                error: function(jqXHR, status, err) {
                    swal("Some Proble Occurred!! please try again", {
                        icon: "error",
                        timer: 2000,
                    });
                    return false;
                }
            });

            addrow(incount)
            $('#m_sale_item' + incount).val(itemid).trigger('change');
            $('#item_fright_rate' + incount).val(frightrate);
            $('#m_sale_price' + incount).val(price);
            $('#item_crate' + incount).html(crate);
            $('#item_unit' + incount).html(unit);
            $('#item_name' + incount).html(iname);
            calculate_function(incount)
            $(this).val('');
        });

        $(document).on("keyup", '.calcuclass', function() {
            var count = $(this).data('count');
            calculate_function(count)

        });

        $(document).on("keyup", '.cal_exp', function() {
            calculate_expenses()
        });

        // $(document).on("keyup", '.checkqty', function() {
        //     var count = $(this).data('count');
        //     var entqty = $(this).val();

        //     var qty_avail = $('#m_sale_lot' + count).find(":selected").data('avail_qty');

        //     if (qty_avail < entqty) {
        //         swal('Quantity should be equal or less then ' + qty_avail, {
        //             icon: "error",
        //             timer: 5000,
        //         });
        //         $(this).val(0);
        //     } else {
        //         return true;
        //     }

        // });

        $(document).on("keyup", '.checkqty', function() {
            var count = $(this).data('count');
            var entqty = parseInt($(this).val()) || 0;
            var selectedLot = $('#m_sale_lot' + count).val(); // Get selected lot
            var totalUsedQty = 0; // Track total entered quantity for the same lot

            // Calculate total entered quantity for the same lot across all rows
            $(".checkqty").each(function() {
                var otherCount = $(this).data('count');
                var otherLot = $('#m_sale_lot' + otherCount).val();
                if (otherLot == selectedLot) {
                    totalUsedQty += parseInt($(this).val()) || 0;
                }
            });

            // Get the original available quantity for the selected lot
            var qty_avail = $('#m_sale_lot' + count).find(":selected").data('avail_qty');

            // Validate total entered quantity against available quantity
            if (totalUsedQty > qty_avail) {
                swal('Total quantity for this lot should be equal or less than ' + qty_avail, {
                    icon: "error",
                    timer: 5000,
                });
                $(this).val(0); // Reset the last entered value
            }
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
        <td id="rowcount` + x + `">` + x + `</td>
                                            <td id="item_name` + x + `"></td>
                                            <td>
                                        
                                                <select name="m_sale_lot[]" id="m_sale_lot` + x + `">

                                                </select>
                                            </td>
                                            <td><input type="hidden" name="m_sale_id[]" id="m_sale_id` + x + `" value="">
                                            <input type="hidden" name="m_sale_item[]" id="m_sale_item` + x + `" value="">
                                            <input type="number" id="m_sale_qty` + x + `" name="m_sale_qty[]" class="prodqty calcuclass checkqty" data-count="` + x + `" style="width:80px" value="0">
                                            <input type="hidden" name="pre_item_qty[]" value="">
                                            <input type="hidden" name="item_fright[]" id="item_fright_rate` + x + `" >
                                            <input type="hidden" name="item_fright_total[]" id="item_fright_total` + x + `" class="pfrighttotal" ></td>
                                            <td id="item_unit` + x + `"></td>
                                            <td><input type="text" id="m_sale_weight` + x + `" name="m_sale_weight[]" class="prodweight calcuclass" data-count="` + x + `" style="width:150px" value="0"></td>
                                            <td><input type="text" id="m_sale_price` + x + `" name="m_sale_price[]" class="prodprice calcuclass" data-count="` + x + `" style="width:80px" value=""> 
                                            <input type="hidden" id="m_sale_crate` + x + `" name="m_sale_crate[]" class="prodcrate" data-count="` + x + `" style="width:150px"></td>
                                            <td id="item_crate` + x + `"></td>
                                            <td><input type="number" id="s_item_nettotal` + x + `" name="m_sale_total[]" class="pnettotal" data-count="` + x + `" style="width:150px" value="" readonly></td>
                                                            <td>  <button type="button" class="btn btn-danger px-1 py-0 removerow" data-count="` + x + `" title="Delete"><i class="bi bi-trash"></i></button></td>
                                                            </tr>`);
        selectRefresh();
    }


    function calculate_function(count) {

        var rate = parseFloat($('#m_sale_price' + count).val());
        var qty = $('#m_sale_qty' + count).val();
        var fright_rate = $('#item_fright_rate' + count).val();
        var weight = parseFloat($('#m_sale_weight' + count).val());

        if ($('#item_crate' + count).html() == '') {
            $('#m_sale_crate' + count).val(0);
        } else {
            $('#m_sale_crate' + count).val(qty);
        };

        if (weight != '') {
            var netamount = (weight * rate);
        } else {
            var netamount = (qty * rate);
        }

        var fright_total = (fright_rate * qty);
        // var gstamount = amount * gst / 100;
        // var netamount = (amount + gstamount - disc);


        // $('#s_item_total' + count).val(amount);
        $('#s_item_nettotal' + count).val(netamount);
        $('#item_fright_total' + count).val(fright_total);
        // $('#s_item_gstamt' + count).val(gstamount);
        total_calculate_fun()
    }


    function total_calculate_fun() {
        var totalqty = 0;
        $('.prodqty').each(function(index) {
            totalqty += parseInt($(this).val());

        });

        var Tweight = 0;
        $('.prodweight').each(function(index) {
            Tweight += parseFloat($(this).val());

        });

        var Ntotal = 0;
        $('.pnettotal').each(function(index) {
            Ntotal += parseFloat($(this).val());

        });

        var Tcrate = 0;
        $('.prodcrate').each(function(index) {
            Tcrate += parseInt($(this).val());

        });

        var Titemfright = 0;
        $('.pfrighttotal').each(function(index) {
            Titemfright += parseInt($(this).val());

        });

        $('#m_sale_fright').val(Titemfright);
        $('#qty_total').html(totalqty);
        $('#wgt_total').html(Tweight);
        $('#crate_total').html(Tcrate);
        $('#grand_total').html(Ntotal);
        $('#total_amount').html(Ntotal);
        calculate_expenses()
    }

    function calculate_expenses() {

        var comrate = parseInt($('#m_sale_comrate').val());
        var total_amount = parseFloat($('#grand_total').html());
        sale_commamo = comrate == 0 ? 0 : (total_amount * comrate / 100);
        var sale_fright = parseFloat($('#m_sale_fright').val());
        var sale_hamali = parseFloat($('#m_sale_hamali').val());
        var sale_others = parseFloat($('#m_sale_others').val());

        var totalexp = (sale_commamo + sale_fright + sale_hamali + sale_others);

        $('#m_sale_comm').val(sale_commamo);
        $('#exp_comm').html(sale_commamo);
        $('#exp_fright').html(sale_fright);
        $('#exp_hamali').html(sale_hamali);
        $('#exp_other').html(sale_others);
        $('#total_expenses').html(totalexp);
        $('#net_amount').html(totalexp + total_amount);

    }
</script>