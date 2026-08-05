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
                <a href="<?php echo site_url('Sales/purchase_list') ?>" class="btn btn-sm btn-primary">Purchase List </a>
                <button onclick="history.back()" class="btn btn-danger btn-sm">
                    <i class="bi bi-box-arrow-left me-2"></i>Exit
                </button>
            </div>
        </div>
    </div>
</section>
<section class="py-3 d-flex" style="background:#f3f3ff;min-height:75vh;">
    <div class="container-fluid">

        <form method="post" action="#" id="frm-add-purchase">

            <?php if (!empty($edit_value)) {
                $purcs_spo = $edit_value[0]->m_purcs_spo;
                $purcs_date = $edit_value[0]->m_purcs_date;
                $purcs_truckno = $edit_value[0]->m_purcs_truckno;
                $purcs_suplier = $edit_value[0]->m_purcs_suplier;
                $purcs_billno = $edit_value[0]->m_purcs_billno;
                $purcs_user = $edit_value[0]->m_purcs_user;
                $purcs_comm = $edit_value[0]->m_purcs_comm;
                $purcs_comrate = $edit_value[0]->m_purcs_comrate;
                $purcs_fright = $edit_value[0]->m_purcs_fright;
                $purcs_hamali = $edit_value[0]->m_purcs_hamali;
                $purcs_charity = $edit_value[0]->m_purcs_charity;
                $purcs_packaging = $edit_value[0]->m_purcs_packaging;
                $purcs_loading = $edit_value[0]->m_purcs_loading;
                $purcs_advance = $edit_value[0]->m_purcs_advance;
                $purcs_others = $edit_value[0]->m_purcs_others;
                $purcs_note = $edit_value[0]->m_purcs_note;
                $purcs_branch = $edit_value[0]->m_purcs_branch;
            } else {
                $purcs_spo = '';
                $purcs_date = date('Y-m-d');
                $purcs_truckno = '';
                $purcs_suplier = '';
                $purcs_billno = '';
                $purcs_user = '';
                $purcs_comm = 0;
                $purcs_comrate = 0;
                $purcs_fright = 0;
                $purcs_hamali = 0;
                $purcs_charity = 0;
                $purcs_packaging = 0;
                $purcs_loading = 0;
                $purcs_advance = 0;
                $purcs_others = 0;
                $purcs_note = '';
                $purcs_branch = !empty($branch_id) ? $branch_id : '';
            } ?>


            <div class="row mb-1 g-3">
                <div class="col-md-2">
                    <div class="row">
                        <div class="col-3">
                            <label>Date<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-9">
                            <div class="form-group">
                                <input type="hidden" name="m_purcs_spo" id="m_purcs_spo" value="<?= $purcs_spo ?>">
                                <input type="hidden" name="m_purcs_suplier" id="m_purcs_suplier" value="<?= $purcs_suplier ?>">
                                <input type="hidden" name="precust" id="precust" value="<?= $purcs_suplier ?>">
                                <input type="date" name="m_purcs_date" id="m_purcs_date" class="form-control" required="" value="<?= $purcs_date ?>" max="<?= date('Y-m-d')?>">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-md-3">

                    <div class="row">
                        <div class="col-3">
                            <label>Truck No<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-9">
                            <div class="form-group">
                                <input type="text" name="m_purcs_truckno" id="m_purcs_truckno" class="form-control" placeholder="Enter Truck No" value="<?= $purcs_truckno ?>">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-3">

                    <div class="row">
                        <div class="col-3">
                            <label>Bill No<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-9">
                            <div class="form-group">
                                <input type="text" name="m_purcs_billno" id="m_purcs_billno" class="form-control" placeholder="Enter Bill No" value="<?= $purcs_billno ?>">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-md-2">
                    <div class="row">
                        <div class="col-4">
                            <label>Branch</label>
                        </div>
                        <div class="col-8">
                            <div class="form-group">
                                <select name="m_purcs_branch" id="m_purcs_branch" class="form-select select2">
                                    <option value="0">Head Office</option>
                                    <?php if (!empty($branch_list)) {
                                        foreach ($branch_list as $branch) {
                                    ?>
                                        <option value="<?= $branch->m_user_id ?>" <?= $purcs_branch == $branch->m_user_id ? 'selected' : '' ?>><?= $branch->m_user_name ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">

                    <div class="row">
                        <div class="col-4">
                            <label>Supplier Name <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-8">
                            <div class="form-group">
                                <select name="m_purcs_suplier" id="m_purcs_suplier" class="form-select select2" required autofocus>
                                    <option value="">--Select--</option>
                                    <?php
                                    if (!empty($suplier_list)) {
                                        foreach ($suplier_list as $vat) {

                                            if ($purcs_suplier == $vat->m_user_id) {
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
                                        $purTotalAmt = ($edit_value[0]->m_purcs_comm + $edit_value[0]->m_purcs_fright + $edit_value[0]->m_purcs_hamali + $edit_value[0]->m_purcs_charity + $edit_value[0]->m_purcs_packaging + $edit_value[0]->m_purcs_loading + $edit_value[0]->m_purcs_advance + $edit_value[0]->m_purcs_others);
                                        foreach ($edit_value as $kry) {
                                            $purTotalAmt += $kry->m_purcs_total;
                                            $cou++;
                                ?>
                                        <tr>
                                            <td id="rowcount<?= $cou ?>"><?= $cou ?></td>
                                            <td id="item_name<?= $cou ?>"><?= $kry->m_item_name ?> <input type="hidden" name="m_purcs_id[]" id="m_purcs_id<?= $cou ?>" value="<?php echo $kry->m_purcs_id; ?>">
                                                <input type="hidden" name="m_purcs_item[]" id="m_purcs_item<?= $cou ?>" value="<?php echo $kry->m_purcs_item; ?>">
                                            </td>

                                            <td><input type="text" id="m_purcs_lot<?= $cou ?>" name="m_purcs_lot[]" data-count="<?= $cou ?>" value="<?php echo $kry->m_purcs_lot; ?>"></td>
                                            <td><input type="number" id="m_purcs_qty<?= $cou ?>" name="m_purcs_qty[]" class="prodqty calcuclass" data-count="<?= $cou ?>" style="width:80px" value="<?php echo $kry->m_purcs_qty; ?>">
                                                <input type="hidden" name="pre_item_qty[]" value="<?php echo $kry->m_purcs_qty; ?>">
                                            </td>
                                            <td id="item_unit<?= $cou ?>"><?= $kry->unitname ?></td>
                                            <td><input type="text" id="m_purcs_weight<?= $cou ?>" name="m_purcs_weight[]" class="prodweight calcuclass" data-count="<?= $cou ?>" style="width:150px" value="<?php echo $kry->m_purcs_weight; ?>"></td>
                                            <td><input type="text" id="m_purcs_price<?= $cou ?>" name="m_purcs_price[]" class="prodprice calcuclass" data-count="<?= $cou ?>" style="width:80px" value="<?php echo $kry->m_purcs_price; ?>">
                                                <input type="hidden" id="m_purcs_crate<?= $cou ?>" name="m_purcs_crate[]" class="prodcrate" data-count="<?= $cou ?>" style="width:150px" value="<?php echo $kry->m_purcs_crate; ?>">
                                            </td>
                                            <td id="item_crate<?= $cou ?>"><?= $kry->cratetype ?></td>
                                            <td><input type="number" id="s_item_nettotal<?= $cou ?>" name="m_purcs_total[]" class="pnettotal" data-count="<?= $cou ?>" style="width:150px" value="<?php echo $kry->m_purcs_total; ?>" readonly></td>
                                            <td> <button type="button" class="btn btn-danger px-1 py-0 del-purchase-id" data-value="<?php echo $kry->m_purcs_id; ?>" title="Delete"><i class="bi bi-trash"></i></button></td>
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
                        <input type="hidden" name="pre_grand_total" value="<?= isset($purTotalAmt) ? $purTotalAmt : 0; ?>">
                        <input list="items_datalist" id="item_serch_inp" placeholder="Add Items" class="form-control" style="width: 50%;">

                        <datalist id="items_datalist">
                            <?php
                            if (!empty($item_list)) {
                                foreach ($item_list as $Vitem) {

                            ?>
                                    <option value="<?php echo $Vitem->m_item_name; ?>" data-itemid="<?= $Vitem->m_item_id ?>" data-price="<?= $Vitem->m_item_price ?>" data-crate="<?= $Vitem->cratetype ?>" data-unit="<?= $Vitem->unitname ?>"><?php echo $Vitem->m_item_name; ?></option>
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
                                <td>Bill</td>
                                <td>
                                    <input type="hidden" name="m_purcs_comrate" id="m_purcs_comrate" placeholder="Enter Commission rate" value="<?= $purcs_comrate ?>">
                                    <input type="text" name="m_purcs_comm" id="m_purcs_comm" class="cal_exp" placeholder="Enter Commission rate" value="<?= $purcs_comm ?>">
                                </td>
                                <td id="exp_comm">0</td>
                            </tr>
                            <tr>
                                <td>Fright</td>
                                <td>Bill</td>
                                <td><input type="text" name="m_purcs_fright" id="m_purcs_fright" class="cal_exp" placeholder="Enter Fright" value="<?= $purcs_fright ?>"></td>
                                <td id="exp_fright">0</td>
                            </tr>
                            <tr>
                                <td>Hamali</td>
                                <td>Bill</td>
                                <td><input type="text" name="m_purcs_hamali" id="m_purcs_hamali" class="cal_exp" placeholder="Enter Hamali" value="<?= $purcs_hamali ?>"></td>
                                <td id="exp_hamali">0</td>
                            </tr>
                            <tr>
                                <td>Charity</td>
                                <td>Bill</td>
                                <td><input type="text" name="m_purcs_charity" id="m_purcs_charity" class="cal_exp" placeholder="Enter Charity" value="<?= $purcs_charity ?>"></td>
                                <td id="exp_charity">0</td>
                            </tr>
                            <tr>
                                <td>Packaging</td>
                                <td>Bill</td>
                                <td><input type="text" name="m_purcs_packaging" id="m_purcs_packaging" class="cal_exp" placeholder="Enter Packaging" value="<?= $purcs_packaging ?>"></td>
                                <td id="exp_packaging">0</td>
                            </tr>
                            <tr>
                                <td>Loading</td>
                                <td>Bill</td>
                                <td><input type="text" name="m_purcs_loading" id="m_purcs_loading" class="cal_exp" placeholder="Enter Loading" value="<?= $purcs_loading ?>"></td>
                                <td id="exp_loading">0</td>
                            </tr>
                            <tr>
                                <td>Advance</td>
                                <td>Bill</td>
                                <td><input type="text" name="m_purcs_advance" id="m_purcs_advance" class="cal_exp" placeholder="Enter Advance" value="<?= $purcs_advance ?>"></td>
                                <td id="exp_advance">0</td>
                            </tr>
                            <tr>
                                <td>Other</td>
                                <td>Bill</td>
                                <td><input type="text" name="m_purcs_others" id="m_purcs_others" class="cal_exp" placeholder="Enter Others" value="<?= $purcs_others ?>"></td>
                                <td id="exp_other">0</td>
                            </tr>

                        </tbody>

                    </table>

                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Note </label>
                        <input type="text" name="m_purcs_note" id="m_purcs_note" class="form-control" placeholder="Enter Note" value="<?= $purcs_note ?>">
                    </div>

                    <table class="table table-striped table-bordered dt-responsive nowra mt-3">
                        <thead>
                            <th>Expenses</th>
                            <th>Per</th>
                            <th>Amount</th>
                            <th></th>

                        </thead>
                        <tbody id="exprowdiv">
                            <?php if (!empty($id) &&  !empty($inter_expense)) {
                                $rte = 0;
                                foreach ($inter_expense as $kty) {
                                    $rte++; ?>

                                    <tr id="exprowtr<?= $rte ?>">
                                        <td><select name="m_exp_name[]" id="m_exp_name<?= $rte ?>" class="select select2">
                                                <?php if (!empty($expense_lst)) {
                                                    foreach ($expense_lst as $key) {
                                                        if ($kty->m_exp_name == $key->m_group_id) {
                                                            $op = 'selected';
                                                        } else {
                                                            $op = '';
                                                        }
                                                        echo '<option value="' . $key->m_group_id . '" ' . $op . '>' . $key->m_group_name . '</option>';
                                                    }
                                                } ?>
                                            </select></td>
                                        <td>Bill</td>
                                        <td>
                                            <input type="hidden" name="m_exp_id[]" id="m_exp_id<?= $rte ?>" value="<?= $kty->m_exp_id ?>">
                                            <input type="text" name="m_exp_amount[]" id="m_exp_amount<?= $rte ?>" class="cal_exp" placeholder="Enter Amount" value="<?= $kty->m_exp_amount ?>">
                                        </td>
                                        <td id="exptdact<?= $rte ?>">
                                            <button class="btn btn-sm btn-danger" type="button" onclick=""><i class="bi bi-trash3"></i></button>
                                        </td>

                                    </tr>


                                <?php } ?>
                                <tr id="exprowtr<?= ($rte + 1) ?>">
                                    <td><select name="m_exp_name[]" id="m_exp_name<?= ($rte + 1) ?>" class="select select2">
                                            <?php if (!empty($expense_lst)) {
                                                foreach ($expense_lst as $key) {
                                                    echo '<option value="' . $key->m_group_id . '">' . $key->m_group_name . '</option>';
                                                }
                                            } ?>
                                        </select></td>
                                    <td>Bill</td>
                                    <td>
                                        <input type="hidden" name="m_exp_id[]" id="m_exp_id<?= ($rte + 1) ?>" value="">
                                        <input type="text" name="m_exp_amount[]" id="m_exp_amount<?= ($rte + 1) ?>" class="cal_exp" placeholder="Enter Amount">
                                    </td>
                                    <td id="exptdact<?= ($rte + 1) ?>">
                                        <button class="btn btn-sm btn-primary" type="button" onclick="add_exprow(<?= ($rte + 1) ?>)"><i class="bi bi-plus"></i></button>
                                    </td>

                                </tr>

                            <?php } else { ?>
                                <tr id="exprowtr1">
                                    <td><select name="m_exp_name[]" id="m_exp_name1" class="select select2">
                                            <?php if (!empty($expense_lst)) {
                                                foreach ($expense_lst as $key) {
                                                    echo '<option value="' . $key->m_group_id . '">' . $key->m_group_name . '</option>';
                                                }
                                            } ?>
                                        </select></td>
                                    <td>Bill</td>
                                    <td>
                                        <input type="hidden" name="m_exp_id[]" id="m_exp_id1" value="">
                                        <input type="text" name="m_exp_amount[]" id="m_exp_amount1" class="cal_exp" placeholder="Enter Amount">
                                    </td>
                                    <td id="exptdact1">
                                        <button class="btn btn-sm btn-primary" type="button" onclick="add_exprow(1)"><i class="bi bi-plus"></i></button>
                                    </td>

                                </tr>
                            <?php } ?>
                        </tbody>

                    </table>
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

                    <div class="form-layout-submit text-end mt-2">
                        <button type="submit" id="btn-add-item_issue" class="btn btn-block btn-info">Save</button>
                        <a href="<?php echo site_url('Sales/purchase_list') ?>" class="btn btn-block btn-danger">Cancel </a>

                    </div>
                </div>
            </div>

        </form>

    </div>
</section>

<!-- ========== Page Content ========== -->
<?php include("footer.php"); ?>
<?php $this->view('js/sale_js') ?>
<?php $this->view('js/custom_js') ?>

<script>
    // let x = $('#rowunt').val();

    $(document).ready(function(e) {
        total_calculate_fun()
        calculate_expenses()
        let incount = $('#rowunt').val();

        // Live branch cascading: re-scope Supplier/Item pickers when the
        // Branch select changes, so they only show that branch's data.
        // NOTE: a hidden input also shares id="m_purcs_suplier" elsewhere in
        // this form (pre-existing markup) - target the <select> only.
        BranchCascade.bind('#m_purcs_branch', [{
                listType: 'supplier',
                target: 'select#m_purcs_suplier',
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

            addrow(incount)
            $('#m_purcs_item' + incount).val(itemid).trigger('change');
            $('#m_purcs_price' + incount).val(price);
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
                                            <td><input type="text" id="m_purcs_lot` + x + `" name="m_purcs_lot[]"  data-count="` + x + `" style="width:100px" value=""></td>
                                            <td><input type="hidden" name="m_purcs_id[]" id="m_purcs_id` + x + `" value="">
                                            <input type="hidden" name="m_purcs_item[]" id="m_purcs_item` + x + `" value="">
                                            <input type="number" id="m_purcs_qty` + x + `" name="m_purcs_qty[]" class="prodqty calcuclass" data-count="` + x + `" style="width:80px" value="0">
                                            <input type="hidden" name="pre_item_qty[]" value=""></td>
                                            <td id="item_unit` + x + `"></td>
                                            <td><input type="text" id="m_purcs_weight` + x + `" name="m_purcs_weight[]" class="prodweight calcuclass" data-count="` + x + `" style="width:150px" value="0"></td>
                                            <td><input type="text" id="m_purcs_price` + x + `" name="m_purcs_price[]" class="prodprice calcuclass" data-count="` + x + `" style="width:80px" value=""> 
                                            <input type="hidden" id="m_purcs_crate` + x + `" name="m_purcs_crate[]" class="prodcrate" data-count="` + x + `" style="width:150px"></td>
                                            <td id="item_crate` + x + `"></td>
                                            <td><input type="number" id="s_item_nettotal` + x + `" name="m_purcs_total[]" class="pnettotal" data-count="` + x + `" style="width:150px" value="" readonly></td>
                                                            <td>  <button type="button" class="btn btn-danger px-1 py-0 removerow" data-count="` + x + `" title="Delete"><i class="bi bi-trash"></i></button></td>
                                                            </tr>`);
        selectRefresh();
    }



    function calculate_function(count) {

        var rate = parseFloat($('#m_purcs_price' + count).val());
        var qty = $('#m_purcs_qty' + count).val();
        var weight = parseFloat($('#m_purcs_weight' + count).val());

        if ($('#item_crate' + count).html() == '') {
            $('#m_purcs_crate' + count).val(0);
        } else {
            $('#m_purcs_crate' + count).val(qty);
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

    function calculate_expenses() {

        var comrate = parseInt($('#m_purcs_comm').val());
        var total_amount = parseFloat($('#grand_total').html());

        // sale_commamo = (total_amount * comrate / 100);


        var sale_fright = parseFloat($('#m_purcs_fright').val());
        var sale_hamali = parseFloat($('#m_purcs_hamali').val());
        var purcs_charity = parseFloat($('#m_purcs_charity').val());
        var purcs_packaging = parseFloat($('#m_purcs_packaging').val());
        var purcs_loading = parseFloat($('#m_purcs_loading').val());
        var purcs_advance = parseFloat($('#m_purcs_advance').val());
        var sale_others = parseFloat($('#m_purcs_others').val());

        var totalexp = (comrate + sale_fright + sale_hamali + sale_others);

        // $('#m_purcs_comm').val(sale_commamo);
        $('#exp_comm').html(comrate);
        $('#exp_fright').html(sale_fright);
        $('#exp_hamali').html(sale_hamali);
        $('#exp_charity').html(purcs_charity);
        $('#exp_packaging').html(purcs_packaging);
        $('#exp_loading').html(purcs_loading);
        $('#exp_advance').html(purcs_advance);
        $('#exp_other').html(sale_others);
        $('#total_expenses').html(totalexp);
        $('#net_amount').html(totalexp + total_amount);

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
        $('#total_amount').html(Ntotal);

    }


    function add_exprow(xy) {

        $('#exptdact' + xy).empty().html(`<button class="btn btn-sm btn-danger" type="button" onclick="remove_exprow(` + xy + `)"><i class="bi bi-trash3"></i></button>`);

        xy++;
        $('#exprowdiv').append(`<tr id="exprowtr` + xy + `">
                                <td><select name="m_exp_name[]" id="m_exp_name` + xy + `" class="select select2">
                                        <?php if (!empty($expense_lst)) {
                                            foreach ($expense_lst as $key) {
                                                echo '<option value="' . $key->m_group_id . '">' . $key->m_group_name . '</option>';
                                            }
                                        } ?>
                                    </select></td>
                                <td>Bill</td>
                                <td>
                                    <input type="hidden" name="m_exp_id[]" id="m_exp_id` + xy + `" value="">
                                    <input type="number" name="m_exp_amount[]" id="m_exp_amount` + xy + `" class="cal_exp" placeholder="Enter Amount" value="">
                                </td>
                                <td id="exptdact` + xy + `">
                                   <button class="btn btn-sm btn-primary" type="button" onclick="add_exprow(` + xy + `)"><i class="bi bi-plus"></i></button>
                                </td>
                              
                            </tr>`);
        selectRefresh();
    }

    function remove_exprow(xy) {
        $('#exprowtr' + xy).remove();
    }
</script>