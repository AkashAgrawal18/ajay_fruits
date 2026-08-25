<?php $this->view('head'); ?>
<?php $this->view('header'); ?>
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
                <a href="<?php echo site_url('Reports/transfer_ledger') ?>" class="btn btn-sm btn-primary">Transfer Ledger</a>
                <button onclick="history.back()" class="btn btn-danger btn-sm">
                    <i class="bi bi-box-arrow-left me-2"></i>Exit
                </button>
            </div>
        </div>
    </div>
</section>
<section class="py-3 d-flex" style="background:#f3f3ff;min-height:75vh;">
    <div class="container-fluid">

        <form method="post" action="#" id="frm-return-transfer">

            <div class="row mb-1 g-3">
                <div class="col-md-2">
                    <div class="row">
                        <div class="col-3">
                            <label>Date<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-9">
                            <div class="form-group">
                                <input type="date" name="return_date" id="return_date" class="form-control" required value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="row">
                        <div class="col-4">
                            <label>Returning Branch <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-8">
                            <div class="form-group">
                                <select name="from_branch" id="from_branch" class="form-select select2" required autofocus>
                                    <option value="">--Select--</option>
                                    <?php if (!empty($branch_list)) {
                                        foreach ($branch_list as $branch) { ?>
                                            <option value="<?= $branch->m_user_id ?>"><?= $branch->m_user_name ?></option>
                                    <?php }
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
                                <th>Sn</th>
                                <th>Item Name</th>
                                <th>Lot No</th>
                                <th>Branch Holds</th>
                                <th>Rate</th>
                                <th>Return Qty</th>
                                <th>Total</th>
                                <th></th>
                            </thead>
                            <tbody id="tableblock">
                                <input type="hidden" id="rowunt" value="0">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">Total</td>
                                    <td id="qty_total"></td>
                                    <td></td>
                                    <td></td>
                                    <td id="grand_total"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>

                        <!-- Empty until a branch is chosen above - that branch's held stock
                        loads via Transfer/get_branch_stock, unlike add_transfer.php's own
                        stock list, which is fixed (HO's own) and can render server-side. -->
                        <input list="branch_stock_datalist" id="item_serch_inp" placeholder="Select a branch above, then search its held stock by item/lot" class="form-control" style="width: 30%;" disabled>

                        <datalist id="branch_stock_datalist"></datalist>

                    </div>
                </div>

                <div class="col-md-2 text-end fw-bold">
                    <div class="form-group">
                        <label>Total Qty : </label>
                        <label id="total_qty_lbl">0</label>
                    </div>
                    <div class="form-group">
                        <label>Total Value : </label>
                        <label id="total_value_lbl">0</label>
                    </div>
                    <hr style="margin: 0.1rem 0;">

                    <div class="form-layout-submit text-end mt-2">
                        <button type="submit" id="btn-return-transfer" class="btn btn-block btn-warning">Record Return</button>
                        <a href="<?php echo site_url('Sales/purchase_list') ?>" class="btn btn-block btn-danger">Cancel</a>
                    </div>
                </div>
            </div>

        </form>

    </div>
</section>
<!-- ========== Page Content ========== -->
<?php $this->view('footer'); ?>
<?php $this->view('js/custom_js') ?>

<script>
    $(document).ready(function(e) {
        total_calculate_fun();
        let incount = $('#rowunt').val();

        // Picking a different branch drops whatever was already added - the
        // rows on the table belong to the PREVIOUS branch's stock and would
        // otherwise silently post against the wrong one.
        $('#from_branch').on('change', function() {
            $('#tableblock').empty().append('<input type="hidden" id="rowunt" value="0">');
            incount = 0;
            total_calculate_fun();

            var $search = $('#item_serch_inp');
            var $list = $('#branch_stock_datalist');
            $list.empty();

            var branchId = $(this).val();
            if (!branchId) {
                $search.prop('disabled', true).attr('placeholder', 'Select a branch above, then search its held stock by item/lot');
                return;
            }

            $search.prop('disabled', true).attr('placeholder', 'Loading branch stock...');

            $.ajax({
                url: "<?= base_url('Transfer/get_branch_stock') ?>",
                type: "POST",
                data: {
                    branch_id: branchId
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status !== 'success') {
                        swal(res.message || 'Could not load that branch\'s stock.', {
                            icon: 'error'
                        });
                        $search.prop('disabled', true).attr('placeholder', 'Select a branch above, then search its held stock by item/lot');
                        return;
                    }

                    if (!res.stock.length) {
                        $search.prop('disabled', true).attr('placeholder', 'This branch has no transferred stock to return');
                        return;
                    }

                    $.each(res.stock, function(i, li) {
                        var label = li.m_item_name + ' - Lot ' + li.m_purcs_lot + ' (Holds: ' + li.m_purcs_available + ')';
                        $list.append($('<option>', {
                            value: label,
                            'data-lotid': li.m_purcs_id,
                            'data-itemname': li.m_item_name,
                            'data-lot': li.m_purcs_lot,
                            'data-available': li.m_purcs_available,
                            'data-rate': li.m_purcs_price
                        }).text(label));
                    });

                    $search.prop('disabled', false).attr('placeholder', 'Search this branch\'s held stock by item/lot');
                },
                error: function() {
                    swal('Could not load that branch\'s stock. Please try again.', {
                        icon: 'error'
                    });
                    $search.prop('disabled', true).attr('placeholder', 'Select a branch above, then search its held stock by item/lot');
                }
            });
        });

        $(document).on('change', '#item_serch_inp', function() {
            var $opt = $("#branch_stock_datalist option[value='" + $(this).val() + "']");
            if (!$opt.length) {
                $(this).val('');
                return;
            }

            var lotid = $opt.attr('data-lotid');

            // don't allow the same lot to be added twice
            if ($('#lot_id' + lotid + '_marker').length) {
                swal('This lot has already been added.', {
                    icon: 'warning'
                });
                $(this).val('');
                return;
            }

            var itemname = $opt.attr('data-itemname');
            var lot = $opt.attr('data-lot');
            var available = $opt.attr('data-available');
            var rate = $opt.attr('data-rate');

            incount++;
            addrow(incount, lotid, itemname, lot, available, rate);
            calculate_function(incount);
            $(this).val('');
            $('#rowunt').val(incount);
        });

        $(document).on('keyup', '.calcuclass', function() {
            var count = $(this).data('count');
            calculate_function(count);
        });

        $(document).on('click', '.removerow', function() {
            var count = $(this).data('count');
            $('#rowcot' + count).remove();
            total_calculate_fun();
        });

        $('#frm-return-transfer').on('submit', function(ev) {
            ev.preventDefault();

            if (!$('#from_branch').val()) {
                swal('Please select which branch is returning stock.', {
                    icon: 'warning'
                });
                return;
            }

            if ($('.prodqty').length === 0) {
                swal('Please add at least one lot to return.', {
                    icon: 'warning'
                });
                return;
            }

            var invalid = false;
            $('.prodqty').each(function() {
                var max = parseFloat($(this).data('max'));
                var qty = parseFloat($(this).val());
                if (!qty || qty <= 0 || qty > max) {
                    invalid = true;
                }
            });

            if (invalid) {
                swal('Please enter a valid Return Qty (within what the branch holds) for every line.', {
                    icon: 'error'
                });
                return;
            }

            var $btn = $('#btn-return-transfer');
            $btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "<?= base_url('Transfer/insert_return') ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).text('Record Return');
                    if (res.status === 'success') {
                        swal(res.message, {
                            icon: 'success',
                            timer: 1200
                        });
                        setTimeout(function() {
                            window.location = "<?= base_url('Sales/purchase_list') ?>";
                        }, 1200);
                    } else {
                        swal(res.message, {
                            icon: 'error'
                        });
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Record Return');
                    swal('Something went wrong. Please try again.', {
                        icon: 'error'
                    });
                }
            });
        });
    });

    function addrow(x, lotid, itemname, lot, available, rate) {
        $('#tableblock').append(`<tr id="rowcot` + x + `">
            <td id="rowcount` + x + `">` + x + `</td>
            <td>` + itemname + `<input type="hidden" name="lot_id[]" id="lot_id` + x + `_marker" value="` + lotid + `"></td>
            <td>` + lot + `</td>
            <td>` + available + `</td>
            <td>` + rate + `</td>
            <td><input type="number" min="0.01" step="0.01" max="` + available + `" id="qty` + x + `" name="qty[]" class="prodqty calcuclass" data-count="` + x + `" data-max="` + available + `" data-rate="` + rate + `" style="width:90px" value="` + available + `"></td>
            <td><input type="number" id="nettotal` + x + `" class="pnettotal" data-count="` + x + `" style="width:120px" value="0" readonly></td>
            <td><button type="button" class="btn btn-danger px-1 py-0 removerow" data-count="` + x + `" title="Delete"><i class="bi bi-trash"></i></button></td>
        </tr>`);
    }

    function calculate_function(count) {
        var qty = parseFloat($('#qty' + count).val()) || 0;
        var rate = parseFloat($('#qty' + count).data('rate')) || 0;
        $('#nettotal' + count).val((qty * rate).toFixed(2));
        total_calculate_fun();
    }

    function total_calculate_fun() {
        var totalqty = 0;
        $('.prodqty').each(function() {
            totalqty += parseFloat($(this).val()) || 0;
        });

        var Ntotal = 0;
        $('.pnettotal').each(function() {
            Ntotal += parseFloat($(this).val()) || 0;
        });

        $('#qty_total').html(totalqty);
        $('#grand_total').html(Ntotal.toFixed(2));
        $('#total_qty_lbl').html(totalqty);
        $('#total_value_lbl').html(Ntotal.toFixed(2));
    }
</script>
