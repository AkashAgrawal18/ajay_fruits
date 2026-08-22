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
            height: auto !important;
            max-height: none !important;
            overflow: visible !important;
        }

        .actionth {
            display: none;
        }



    }
</style>

<!-- ========== Page Content ========== -->
<section class="py-1" style="background: #bbf;">
    <div class="container-fluid">
        <div class="row align-item-center">
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
<section class="py-4 d-flex align-item-center" style="background:#f3f3ff;min-height:70vh;">
    <div class="container-fluid">
        <div class="row justify-content-evenly g-0">
            <div class="col-12">

                <form action="<?= base_url('Reports/lotwise_item') ?>" method="POST" class="row align-items-center mb-3">

                    <div class="col-2">
                        <div class="form-group">
                            <label for="">To date</label>
                            <input type="date" max="<?= date('Y-m-d') ?>" name="to_date" id="to_date" class="form-control" value="<?= $to_date ?>">
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="">Item</label>
                            <select name="item_id" id="item_id" class="form-select select2">
                                <option value="">All Items</option>
                                <?php
                                if (!empty($all_items)) {
                                    foreach ($all_items as $Vitem) {
                                        if ($item_id == $Vitem->m_item_id) {
                                            $op = 'selected';
                                        } else {
                                            $op = '';
                                        }
                                ?>
                                        <option value="<?= $Vitem->m_item_id ?>" <?= $op ?>><?php echo $Vitem->m_item_name; ?></option>
                                <?php
                                    }
                                }
                                ?>

                            </select>
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
                        <a class="btn btn-danger btn-sm" href="<?= base_url('Reports/lotwise_item') ?>"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
                        <button class="btn btn-success btn-sm" type="button" onclick="printcustomtable()"><i class="bi bi-printer me-2"></i>Print</button>
                        <!-- <a class="btn btn-primary btn-sm" href="<? //= base_url('Reports/lotwise_item_check') ?>"><i class="bi bi-arrow-clockwise"></i> Refresh Stock</a> -->

                    </div>
                </form>


                <div class="table-responsive bg-light printTableDiv" style="height: 64vh;">
                    <table id="items_tbl" class=" table table-light table-bordered table-hover">
                        <thead style="position: sticky; top: 0; z-index: 999; background: #f3f3ff !important;">
                            <tr>
                                <th width="5%">SNO</th>
                                <th>ITEM NAME</th>
                                <th>LOT</th>
                                <th>CHALLAN</th>
                                <th>OPENING</th>
                                <th>CLOSING</th>
                                <th>UNIT</th>
                                <th>WEIGHT</th>
                                <th>CRATE</th>
                                <th class="actionth"></th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            if (!empty($all_value)) {
                                foreach ($all_value as $value) {

                            ?>
                                    <tr onclick="lotwise_sale_fun('<?= $value['m_item_id']; ?>','<?= $value['m_purcs_id']; ?>','<?= $value['m_purcs_lot']; ?>','<?= $value['m_item_name']; ?>','<?= $value['cratetype']; ?>','<?= $value['m_item_fright']; ?>','<?= $value['closing_qty']; ?>')">
                                        <td><?= $i; ?></td>
                                        <td><?= $value['m_purcs_id']; ?> | <?= $value['m_item_name']; ?></td>
                                        <th><?= $value['m_purcs_qty']; ?> <?= $value['m_purcs_lot']; ?></th>
                                        <td><?= $value['m_purcs_spo']; ?></td>
                                        <td><?= $value['opening_qty']; ?></td>
                                        <td><?= $value['closing_qty']; ?></td>
                                        <td><?= $value['unitname']; ?></td>
                                        <td><?= $value['balance_weight']; ?></td>
                                        <td><?= $value['cratetype']; ?></td>
                                        <td class="actionth"><a href="<?= base_url('Sales/add_purchase?id=') . $value['m_purcs_spo'] ?>" class="btn btn-info btn-sm p-1 me-1" title="View Incoming"><i class="bi bi-pencil-square"></i></a></td>


                                    </tr>
                            <?php
                                    $i++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</section>
<!-- ========== Page Content ========== -->

<!-- view Modal start -->
<div class="modal fade" id="salelistModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row" style="display: contents;">
                    <div class="col-md-5">
                        <h4 class="modal-title">Lotwise add Sales</h4>
                    </div>
                    <div class="col-md-7" style="text-align: end;">
                        <button class="btn btn-primary btn-sm navbtn" data-type="1">Add Sale</button>
                        <button class="btn btn-secondary btn-sm navbtn" data-type="2">Add Issue</button>
                        <button class="btn btn-secondary btn-sm navbtn" data-type="3">Sales list</button>
                        <a class="btn btn-success btn-sm d-none" id="printsbtn"><i class="bi bi-printer me-2"></i> Print </a>
                        <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-close"></i></button>
                    </div>
                </div>
            </div>
            <form action="" id="frm-add-sales">

                <div class="modal-body printDiv" style="word-break: break-all">

                    <h5 id="headitem"></h5>
                    <div class="row bodydiv" id="viewsalesdiv">
                        <div class="col-md-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <th>Sn</th>
                                        <th>Date</th>
                                        <th>Customer Name</th>
                                        <th>Quantity</th>
                                        <th>Weight (KG)</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th></th>
                                    </thead>
                                    <tbody id="modal_body_contant">

                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row bodydiv" id="addsalediv">
                        <div class="col-md-12 mt-3">
                            <div class="table-responsive">
                                <input type="hidden" id="item_cratetype">
                                <input type="hidden" id="item_fright">
                                <input type="hidden" id="item_id">
                                <input type="hidden" id="item_lot">
                                <input type="hidden" id="qty_availble">

                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <th width="3%">Sn</th>
                                        <th width="8%">Date</th>
                                        <th width="14%" id="texttitle">Customer Name</th>
                                        <th width="10%">Quantity</th>
                                        <th width="10%">Weight (KG)</th>
                                        <th width="10%">Rate</th>
                                        <th width="7%">Total</th>
                                        <th id="noteth">Note</th>
                                        <th width="20%" id="agentth">Agent</th>
                                        <th width="3%"></th>
                                    </thead>
                                    <tbody id="tableblock">

                                    </tbody>

                                </table>
                                <input list="m_sale_customer" id="citem_serch_inp" placeholder="Add customer" data-value="1" class="form-control" style="width: 50%; margin-bottom:5px">

                                <datalist id="m_sale_customer" class="">
                                    <?php
                                    if (!empty($cust_dtl)) {
                                        foreach ($cust_dtl as $vat) {

                                    ?>
                                            <option value="<?= $vat->m_cust_name . ' | ' . $vat->m_cust_mobile; ?>" data-name="<?= $vat->m_cust_name ?>" data-id="<?= $vat->m_cust_id ?>" data-mobile="<?= $vat->m_cust_mobile ?>"><?= $vat->m_cust_name . ' | ' . $vat->m_cust_mobile; ?>
                                        <?php
                                        }
                                    }

                                        ?>
                                </datalist>
                                <datalist id="si_issue_user" class="d-none">
                                    <?php
                                    if (!empty($agent_dtl)) {
                                        foreach ($agent_dtl as $vat) {

                                    ?>
                                            <option value="<?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?>" data-name="<?= $vat->m_user_name ?>" data-id="<?= $vat->m_user_id ?>" data-mobile="<?= $vat->m_user_mobile ?>"><?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?>
                                        <?php
                                        }
                                    }

                                        ?>
                                </datalist>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" id="btn-add-sales">Submit Sale</button>
                    <button class="btn btn-primary d-none" type="submit" id="btn-add-issue">Submit Issue</button>
                    <button class="btn btn-danger" type="button" onclick="location.reload()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- view modal end -->


<!-- ========================Footer================Fix======= -->
<?php $this->view('footer'); ?>
<?php $this->view('js/custom_js'); ?>
<!-- ========================Footer================Fix======= -->

<script>
    $(document).on('click', '.navbtn', function() {
        var divtype = $(this).data('type');
        $('.navbtn').removeClass('btn-primary').addClass('btn-secondary');
        $(this).addClass('btn-primary').removeClass('btn-secondary');
        $('.bodydiv').hide();
        if (divtype == 1) {
            $('#citem_serch_inp').attr('list', 'm_sale_customer');
            $('#citem_serch_inp').prop('placeholder', 'Add Customer').attr('data-value', '1');
            $('#m_sale_customer').removeClass('d-none');
            $('#si_issue_user').addClass('d-none');
            $('#printsbtn').addClass('d-none');
            $('#btn-add-issue').addClass('d-none');
            $('#btn-add-sales').removeClass('d-none');
            $('#addsalediv').show();
            $('.modal-title').html('Lotwise Add Sales');
            $('#texttitle').html('Customer Name');
            $('#noteth').removeClass('d-none');
            $('#agentth').removeClass('d-none');
        } else if (divtype == 2) {
            $('#citem_serch_inp').attr('list', 'si_issue_user');
            $('#citem_serch_inp').prop('placeholder', 'Add Agent').attr('data-value', '2');
            $('#si_issue_user').removeClass('d-none');
            $('#m_sale_customer').addClass('d-none');
            $('#btn-add-issue').removeClass('d-none');
            $('#btn-add-sales').addClass('d-none');
            $('#printsbtn').addClass('d-none');
            $('#addsalediv').show();
            $('.modal-title').html('Lotwise Add Issue');
            $('#texttitle').html('Agent Name');
            $('#noteth').addClass('d-none');
            $('#agentth').addClass('d-none');
        } else {
            $('#btn-add-issue').addClass('d-none');
            $('#btn-add-sales').addClass('d-none');
            $('#printsbtn').removeClass('d-none');
            $('#viewsalesdiv').show();
            $('.modal-title').html('Lotwise Sale Details');
        }


    });


    $(document).ready(function(e) {

        let incount = 0;

        $(document).on('change', '#citem_serch_inp', function() {
            var ctype = $(this).data('value');

            if (ctype == 1) {
                var custid = $("#m_sale_customer option[value='" + $(this).val() + "']").attr('data-id')
                var custname = $("#m_sale_customer option[value='" + $(this).val() + "']").attr('data-name')
                var custmobile = $("#m_sale_customer option[value='" + $(this).val() + "']").attr('data-mobile')

                incount++

                addrow(incount)
                $('#m_sale_customer' + incount).val(custid);
                $('#cust_name' + incount).html(custname + ' | ' + custmobile);
            } else {
                var custid = $("#si_issue_user option[value='" + $(this).val() + "']").attr('data-id')
                var custname = $("#si_issue_user option[value='" + $(this).val() + "']").attr('data-name')
                var custmobile = $("#si_issue_user option[value='" + $(this).val() + "']").attr('data-mobile')

                incount++

                addissuerow(incount)
                $('#m_sale_customer' + incount).val(custid);
                $('#cust_name' + incount).html(custname + ' | ' + custmobile);
            }

            calculate_function(incount)
            $(this).val('');
        });

        $(document).on("keyup", '.calcuclass', function() {
            var count = $(this).data('count');
            calculate_function(count)

        });

        $(document).on("keyup", '.check_qty', function() {
            var sum = 0;
            $('.check_qty').each(function() {
                var tqty = parseInt($(this).val());
                sum += tqty;
            });
            var qty_avail = parseInt($('#qty_availble').val());

            if (qty_avail < sum) {
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

        });


        $("#btn-add-sales").click(function(e) {
            var clkbtn = $(this);
            clkbtn.prop('disabled', true);
            var formData = $("#frm-add-sales").serialize();

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('Sales/lotwise_insert_sales'); ?>",
                data: formData,
                // processData: false,
                // contentType: false,
                dataType: "JSON",
                success: function(data) {
                    if (data.status == 'success') {
                        swal(data.message, {
                            icon: "success",
                            timer: 1000,
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        clkbtn.prop('disabled', false);
                        swal(data.message, {
                            icon: "error",
                            timer: 5000,
                        });
                    }
                },
                error: function(jqXHR, status, err) {
                    clkbtn.prop('disabled', false);
                    swal("Some Problem Occurred!! please try again", {
                        icon: "error",
                        timer: 2000,
                    });
                }
            });

        });

        $("#btn-add-issue").click(function(e) {
            var clkbtn = $(this);
            clkbtn.prop('disabled', true);
            var formData = $("#frm-add-sales").serialize();

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('Sales/lotwise_insert_issue'); ?>",
                data: formData,
                // processData: false,
                // contentType: false,
                dataType: "JSON",
                success: function(data) {
                    if (data.status == 'success') {
                        swal(data.message, {
                            icon: "success",
                            timer: 1000,
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        clkbtn.prop('disabled', false);
                        swal(data.message, {
                            icon: "error",
                            timer: 5000,
                        });
                    }
                },
                error: function(jqXHR, status, err) {
                    clkbtn.prop('disabled', false);
                    swal("Some Problem Occurred!! please try again", {
                        icon: "error",
                        timer: 2000,
                    });
                }
            });

        });


    });


    function addrow(x) {
        // x++;
        var ditem_id = $('#item_id').val()
        var ditem_lot = $('#item_lot').val()

        $('#tableblock').append(`<tr id="rowcot` + x + `">
            <td id="rowcount` + x + `">` + x + `</td>
            <td><input type="hidden" name="m_sale_date[]" id="m_sale_date` + x + `" value="<?= $to_date ?>"><?= date("d-m-Y", strtotime($to_date)); ?></td>
                                    <td id="cust_name` + x + `"></td>
                                    <td> <input type="hidden" name="m_sale_crate[]" id="m_sale_crate` + x + `" value="">
                                    <input type="hidden" name="m_sale_item[]" id="m_sale_item` + x + `" value="` + ditem_id + `">
                                    <input type="hidden" name="m_sale_customer[]" id="m_sale_customer` + x + `" value="">
                                    <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="m_sale_qty[]" id="m_sale_qty` + x + `" class="form-control calcuclass check_qty" data-count="` + x + `" value="0"></td>
                                    <td><input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="m_sale_weight[]" id="m_sale_weight` + x + `" class="form-control calcuclass" data-count="` + x + `" value="0"></td>
                                    <td><input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="m_sale_price[]" id="m_sale_price` + x + `" class="form-control calcuclass" data-count="` + x + `" value="0"></td>
                                    <td id="sale_total` + x + `"></td>
                                    <td><input type="hidden" name="m_sale_lot[]" id="m_sale_lot` + x + `" class="form-control" value="` + ditem_lot + `"><input type="hidden" name="m_sale_total[]" id="m_sale_total` + x + `" class="form-control">
                                    <input type="hidden" name="m_sale_fright[]" id="m_sale_fright` + x + `" class="form-control"> <input type="text" name="m_sale_note[]" id="m_sale_note` + x + `" class="form-control"></td>
                                    <td><select name="m_sale_user[]" id="m_sale_user` + x + `" class="form-control">
                                            <option value="">Select agent</option>
                                            <?php
                                            if (!empty($agent_dtl)) {
                                                foreach ($agent_dtl as $vat) {

                                            ?>
                                                <option value="<?= $vat->m_user_id ?>" ><?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?></option>
                                            <?php
                                                }
                                            }

                                            ?>
                                            </select></td>
                                                    <td><button type="button" class="btn btn-danger px-1 py-0 removerow" data-count="` + x + `" title="Delete"><i class="bi bi-trash"></i></button></td>
                                                    </tr>`);

    }

    function addissuerow(x) {
        // x++;
        var ditem_id = $('#item_id').val()
        var ditem_lot = $('#item_lot').val()

        $('#tableblock').append(`<tr id="rowcot` + x + `">
            <td id="rowcount` + x + `">` + x + `</td>
            <td><input type="hidden" name="si_issue_date[]" id="m_sale_date` + x + `" value="<?= $to_date ?>"><?= date("d-m-Y", strtotime($to_date)); ?></td>
                                    <td id="cust_name` + x + `"></td>
                                    <td> <input type="hidden" name="si_issue_crate[]" id="m_sale_crate` + x + `" value="">
                                    <input type="hidden" name="si_issue_item[]" id="m_sale_item` + x + `" value="` + ditem_id + `">
                                    <input type="hidden" name="si_issue_user[]" id="m_sale_customer` + x + `" value="">
                                    <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="si_issue_qty[]" id="m_sale_qty` + x + `" class="form-control calcuclass check_qty" data-count="` + x + `" value="0"></td>
                                    <td><input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="si_issue_weight[]" id="m_sale_weight` + x + `" class="form-control calcuclass" data-count="` + x + `" value="0"></td>
                                    <td><input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="si_issue_price[]" id="m_sale_price` + x + `" class="form-control calcuclass" data-count="` + x + `" value="0"> <input type="hidden" name="si_issue_lotno[]" id="m_sale_lot` + x + `" class="form-control" value="` + ditem_lot + `"><input type="hidden" name="si_issue_total[]" id="m_sale_total` + x + `" class="form-control">
                                    <input type="hidden" name="m_sale_fright[]" id="m_sale_fright` + x + `" class="form-control"></td>
                                    <td id="sale_total` + x + `"></td>
                                  
                                                    <td><button type="button" class="btn btn-danger px-1 py-0 removerow" data-count="` + x + `" title="Delete"><i class="bi bi-trash"></i></button></td>
                                                    </tr>`);

    }


    function lotwise_sale_fun(item_id, purid, lotno, itemname, itemcrate, itemfright, available_qty) {
        $.ajax({
            url: "<?php echo site_url('Reports/lotwise_sales_list'); ?>",
            type: "POST",
            data: {
                purid,
                item_id
            },
            dataType: "JSON",
            success: function(data) {
                var qtytotal = 0;
                var wgttotal = 0;
                var pricetotal = 0;
                var nettotal = 0;
                $('#salelistModal').modal('show');
                $('#modal_body_contant').empty();
                $.each(data, function(i, item) {
                    qtytotal += parseFloat(item.m_sale_qty);
                    wgttotal += parseFloat(item.m_sale_weight);
                    pricetotal += parseFloat(item.m_sale_price);
                    nettotal += parseFloat(item.m_sale_total);
                    $('#modal_body_contant').append(`
                      <tr>
                                                              <td>` + (i + 1) + `</td>
                                                              <td>` + item.m_sale_date + `</td>
                                                              <td>` + item.m_cust_name + `</td>
                                                              <td>` + item.m_sale_qty + `</td>
                                                         
                                                              <td>` + item.m_sale_weight + `</td>
                                                              <td>` + item.m_sale_price + `</td>
                                                              <td>` + item.m_sale_total + `</td>
                                                              <td><a href="<?php echo base_url('Sales/add_sales?id=') ?>` + item.m_sale_spo + `" class="btn btn-info btn-sm p-1" title="Edit" data-toggle="tooltip"><i class="bi bi-pencil-square"></i></a></td>
                                                          </tr>
                      `);
                });

                $('#modal_body_contant').append(`
                      <tr>
                                                              <td colspan="3">Total</td>
                                                           
                                                              <td>` + qtytotal + `</td>
                                                         
                                                              <td>` + wgttotal + `</td>
                                                              <td></td>
                                                              <td>` + nettotal + `</td>
                                                          </tr>
                      `);
            }
        });
        perms = `purid=${purid}&item_id=${item_id}`;
        $('#printsbtn').attr('href','<?= base_url('Sales/lotwise_sale_print?')?>'+perms);
        $('#item_cratetype').val(itemcrate)
        $('#item_fright').val(itemfright)
        $('#item_id').val(item_id)
        $('#item_lot').val(purid)
        $('#qty_availble').val(available_qty)
        $('#headitem').html(itemname + `(` + itemcrate + `) Lot ` + lotno + ` Available ` + available_qty)
        $('.bodydiv').hide();
        $('#addsalediv').show();

    }

    function calculate_function(count) {

        var rate = parseFloat($('#m_sale_price' + count).val());
        var qty = parseInt($('#m_sale_qty' + count).val());
        var weight = parseFloat($('#m_sale_weight' + count).val());

        var itemcrate = $('#item_cratetype').val()
        var fright_rate = parseFloat($('#item_fright').val());

        if (itemcrate == '') {
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
        $('#sale_total' + count).html(netamount);
        $('#m_sale_total' + count).val(netamount);
        $('#m_sale_fright' + count).val(fright_total);
        // $('#s_item_gstamt' + count).val(gstamount);
        // total_calculate_fun()
    }
</script>