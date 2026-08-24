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
                <a href="<?php echo site_url('Sales/issue_item_list') ?>" class="btn btn-sm btn-primary">Issue List </a>
                <button onclick="history.back()" class="btn btn-danger btn-sm">
                    <i class="bi bi-box-arrow-left me-2"></i>Exit
                </button>
            </div>
        </div>
    </div>
</section>
<section class="py-3 d-flex" style="background:#f3f3ff;min-height:75vh;">
    <div class="container-fluid">

        <form method="post" action="#" id="frm-add-item_issue">

            <?php if (!empty($edit_value)) {
                $issue_spo = $edit_value[0]->si_issue_spo;
                $issue_date = $edit_value[0]->si_issue_date;
                $issue_trackno = $edit_value[0]->si_issue_trackno;
                $issue_type = $edit_value[0]->si_issue_type;
                $issue_user = $edit_value[0]->si_issue_user;
                $issue_branch = $edit_value[0]->si_issue_branch;
                // $issue_item = $edit_value->si_issue_item;
                // $issue_qty = $edit_value->si_issue_qty;
                // $issue_weight = $edit_value->si_issue_weight;
                // $issue_crate = $edit_value->si_issue_crate;
                // $issue_price = $edit_value->si_issue_price;
            } else {
                $issue_spo = '';
                $issue_date = date('Y-m-d');
                $issue_trackno = '';
                $issue_type = 1;
                $issue_user = '';
                $issue_branch = !empty($branch_id) ? $branch_id : '';
                // $issue_item = '';
                // $issue_qty = '';
                // $issue_weight = '';
                // $issue_crate = '';
                // $issue_price = '';
            } ?>


            <div class="row mb-1 g-3">

                <?php if ($this->session->userdata('user_type') == 8) { ?>
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-3">
                            <label>Branch</label>
                        </div>
                        <div class="col-9">
                            <div class="form-group">
                                <select name="si_issue_branch" id="si_issue_branch" class="form-select select2">
                                    <option value="0">Head Office</option>
                                    <?php if (!empty($branch_list)) {
                                        foreach ($branch_list as $branch) {
                                    ?>
                                        <option value="<?= $branch->m_user_id ?>" <?= $issue_branch == $branch->m_user_id ? 'selected' : '' ?>><?= $branch->m_user_name ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-3">
                            <label>Date<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-9">
                            <div class="form-group">
                                <input type="hidden" name="si_issue_spo" id="si_issue_spo" value="<?= $issue_spo ?>">
                                <input type="hidden" name="si_issue_type" id="si_issue_type" value="<?= $issue_type ?>">
                                <input type="date" min="<?= $issue_date ?>" max="<?= date('Y-m-d')?>" name="si_issue_date" id="si_issue_date" class="form-control" required="" value="<?= $issue_date ?>">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-md-3">

                    <div class="row">
                        <div class="col-4">
                            <label>Truck No<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-8">
                            <div class="form-group">
                                <input type="text" name="si_issue_trackno" id="si_issue_trackno" class="form-control" placeholder="Enter Truck No" value="<?= $issue_trackno ?>">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-md-3">

                    <div class="row">
                        <div class="col-4">
                            <label>Staff Name <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-8">
                            <div class="form-group">
                                <select name="si_issue_user" id="si_issue_user" class="form-select select2" required autofocus>
                                    <option value="">--Select--</option>
                                    <?php
                                    if (!empty($staff_list)) {
                                        foreach ($staff_list as $vat) {

                                            if ($issue_user == $vat->m_user_id) {
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
                    </div>


                </div>


                <div class="col-md-12 mb-2">

                    <div class="table-responsive">

                        <table class="table table-striped table-bordered dt-responsive nowra">
                            <thead>
                                <th>Sn</th>
                                <th>Item Name</th>
                                <th>LOT Number</th>
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
                                    foreach ($edit_value as $kry) {
                                        $cou++;
                                ?>
                                        <tr>
                                            <td id="rowcount<?= $cou ?>"><?= $cou ?></td>
                                            <td id="item_name<?= $cou ?>"><?= $kry->m_item_name ?> <input type="hidden" name="si_issue_id[]" id="si_issue_id<?= $cou ?>" value="<?php echo $kry->si_issue_id; ?>">
                                                <input type="hidden" name="si_issue_item[]" id="si_issue_item<?= $cou ?>" value="<?php echo $kry->si_issue_item; ?>">
                                            </td>
                                            <td>
                                                <input type="hidden" name="si_issue_lotno[]" id="si_issue_lotno<?= $cou ?>" value="<?php echo $kry->si_issue_lotno; ?>" readonly>
                                                <?= $kry->si_issue_lotno; ?> | <?= $kry->pur_lotno; ?>
                                            </td>
                                            <td><input type="number" id="si_issue_qty<?= $cou ?>" name="si_issue_qty[]" class="prodqty calcuclass" data-count="<?= $cou ?>" style="width:80px" max="<?= ($kry->available_stock + $kry->si_issue_qty)?>" value="<?php echo $kry->si_issue_qty; ?>">
                                            <input type="hidden" name="pre_item_qty[]" value="<?php echo $kry->si_issue_qty; ?>">
                                            </td>
                                            <td id="item_unit<?= $cou ?>"><?= $kry->unitname ?></td>
                                            <td><input type="number" id="si_issue_weight<?= $cou ?>" name="si_issue_weight[]" class="prodweight calcuclass" data-count="<?= $cou ?>" style="width:150px" value="<?php echo $kry->si_issue_weight; ?>"></td>
                                            <td><input type="number" id="si_issue_price<?= $cou ?>" name="si_issue_price[]" class="prodprice calcuclass" data-count="<?= $cou ?>" style="width:80px" value="<?php echo $kry->si_issue_price; ?>">
                                                <input type="hidden" id="si_issue_crate<?= $cou ?>" name="si_issue_crate[]" class="prodcrate" data-count="<?= $cou ?>" style="width:150px" value="<?php echo $kry->si_issue_crate; ?>">
                                            </td>
                                            <td id="item_crate<?= $cou ?>"><?= $kry->cratetype ?></td>
                                            <td><input type="number" id="s_item_nettotal<?= $cou ?>" name="si_issue_total[]" class="pnettotal" data-count="<?= $cou ?>" style="width:150px" value="<?php echo $kry->si_issue_total; ?>" readonly></td>
                                            <td> <button type="button" class="btn btn-danger px-1 py-0 del-item_issue-id" data-value="<?php echo $kry->si_issue_id; ?>" title="Delete"><i class="bi bi-trash"></i></button></td>
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

                <div class="col-md-12">
                    <div class="form-layout-submit text-end">
                        <button type="submit" id="btn-add-item_issue" class="btn btn-block btn-info">Save</button>
                        <a href="<?php echo site_url('Sales/issue_item_list') ?>" class="btn btn-block btn-danger">Cancel </a>
                    </div>
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
        let incount = $('#rowunt').val();

        // Live branch cascading: re-scope Staff/Item pickers when the
        // Branch select changes, so they only show that branch's data.
        BranchCascade.bind('#si_issue_branch', [{
                listType: 'staff',
                target: '#si_issue_user',
                mode: 'select',
                idField: 'm_user_id',
                labelFn: function(s) { return s.m_user_name + ' | ' + s.m_user_mobile; },
                placeholder: '--Select--'
            },
            {
                listType: 'item',
                target: '#items_datalist',
                mode: 'datalist',
                valueFn: function(it) { return it.m_item_name; },
                attrsFn: function(it) {
                    return {
                        itemid: it.m_item_id,
                        price: it.m_item_price,
                        ifright: it.m_item_fright,
                        crate: it.m_crate_name,
                        unit: it.m_unit_name
                    };
                }
            }
        ], "<?php echo site_url('Master/branch_scoped_options'); ?>");

        $(document).on('change', '#item_serch_inp', function() {
            var price = $("#items_datalist option[value='" + $(this).val() + "']").attr('data-price')
            var unit = $("#items_datalist option[value='" + $(this).val() + "']").attr('data-unit')
            var crate = $("#items_datalist option[value='" + $(this).val() + "']").attr('data-crate')
            var itemid = $("#items_datalist option[value='" + $(this).val() + "']").attr('data-itemid')
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
                        $('#si_issue_lotno' + incount).empty();
                        $.each(data, function(i, item) {
                            $('#si_issue_lotno' + incount).append(`
                                                <option value="${item.m_purcs_id}" data-avail_qty="${item.m_purcs_available}" >${item.m_purcs_lot} | ${item.m_purcs_available} | ${item.m_user_trademark} | ${item.m_purcs_date}</option>`);
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

            addrow(incount);
            $('#si_issue_item' + incount).val(itemid).trigger('change');
            $('#si_issue_price' + incount).val(price);
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

        $(document).on("keyup", '.checkqty', function() {
            var count = $(this).data('count');
            var entqty = $(this).val();

            var qty_avail = $('#si_issue_lotno' + count).find(":selected").data('avail_qty');

            if (qty_avail < entqty) {
                swal('Quantity should be equal or less then ' + qty_avail, {
                    icon: "error",
                    timer: 5000,
                });
                $(this).val(0);
            } else {
                return true;
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
                                                <select name="si_issue_lotno[]" id="si_issue_lotno` + x + `">

                                                </select>
                                            </td>
                                            <td><input type="hidden" name="si_issue_id[]" id="si_issue_id` + x + `" value="">
                                            <input type="hidden" name="si_issue_item[]" id="si_issue_item` + x + `" value="">
                                            <input type="number" id="si_issue_qty` + x + `" name="si_issue_qty[]" class="prodqty calcuclass checkqty" data-count="` + x + `" style="width:80px" value="0">
                                            <input type="hidden" name="pre_item_qty[]" value=""></td>
                                            <td id="item_unit` + x + `"></td>
                                            <td><input type="number" id="si_issue_weight` + x + `" name="si_issue_weight[]" class="prodweight calcuclass" data-count="` + x + `" style="width:150px" value="0"></td>
                                            <td><input type="number" id="si_issue_price` + x + `" name="si_issue_price[]" class="prodprice calcuclass" data-count="` + x + `" style="width:80px" value=""> 
                                            <input type="hidden" id="si_issue_crate` + x + `" name="si_issue_crate[]" class="prodcrate" data-count="` + x + `" style="width:150px"></td>
                                            <td id="item_crate` + x + `"></td>
                                            <td><input type="number" id="s_item_nettotal` + x + `" name="si_issue_total[]" class="pnettotal" data-count="` + x + `" style="width:150px" value="" readonly></td>
                                                            <td>  <button type="button" class="btn btn-danger px-1 py-0 removerow" data-count="` + x + `" title="Delete"><i class="bi bi-trash"></i></button></td>
                                                            </tr>`);
        selectRefresh();
    }


    function calculate_function(count) {

        var rate = parseFloat($('#si_issue_price' + count).val());
        var qty = $('#si_issue_qty' + count).val();
        var weight = parseFloat($('#si_issue_weight' + count).val());

        if ($('#item_crate' + count).html() == '') {
            $('#si_issue_crate' + count).val(0);
        } else {
            $('#si_issue_crate' + count).val(qty);
        };

        if (weight != '') {
            var netamount = (weight * rate);
        } else {
            var netamount = (qty * rate);
        }
        // var gstamount = amount * gst / 100;
        // var netamount = (amount + gstamount - disc);


        // $('#s_item_total' + count).val(amount);
        $('#s_item_nettotal' + count).val(netamount);
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


        $('#qty_total').html(totalqty);
        $('#wgt_total').html(Tweight);
        $('#crate_total').html(Tcrate);
        $('#grand_total').html(Ntotal);

    }
</script>