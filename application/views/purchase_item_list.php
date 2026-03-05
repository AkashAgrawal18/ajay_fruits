<!-- ========================Header==============Fix========= -->
<?php $this->view('head'); ?>
<?php $this->view('header'); ?>
<style>
    .modal-custom-table th ,.main_table th {
        cursor: pointer;
    }

    .modal-custom-table th.asc::after ,.main_table th.asc::after {
        content: " ▲";
        font-size: 12px;
    }

    .modal-custom-table th.desc::after ,.main_table th.desc::after {
        content: " ▼";
        font-size: 12px;
    }

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
                <a class="btn btn-primary btn-sm" href="<?= base_url('Sales/add_purchase') ?>"><i
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
                <form action="<?= base_url('Sales/purchase_item_list') ?>" method="POST" class="row align-items-center">
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
                                        <option value="<?php echo $vat->m_user_id; ?>" <?= $option1 ?>>
                                            <?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?>
                                            <?php
                                    }
                                }

                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">Search In</label>
                            <input type="text" name="search_in" id="search_in" class="form-control"
                                value="<?= $search_in ?>">
                        </div>
                    </div>
                    
                    <div class="col-3 mt-4">
                        <button class="btn btn-info btn-sm" type="submit"><i class="bi bi-search mx-1"></i>
                            Search</button>
                        <a class="btn btn-danger btn-sm" href="<?= base_url('Sales/purchase_item_list') ?>"><i
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
                    <table id="purchase_item_tbl" class="main_table table table-striped table-bordered dt-responsive nowra w-100">
                        <thead>
                            <tr>
                                <th data-sort="number">LOT NO</th>
                                <th data-sort="date">DATE</th>
                                <th data-sort="text">BILL NUMBER</th>
                                <th data-sort="text">LOT NAME</th>
                                <th data-sort="text">ITEM NAME</th>
                                <th data-sort="number">PRICE</th>
                                <th data-sort="number">QUANTITY</th>
                                <th data-sort="number">SALE</th>
                                <th data-sort="number">ISSUE</th>
                                <th data-sort="number">RETRUN</th>
                                <th data-sort="number">AVAILABLE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            if (!empty($all_value)) {
                                foreach ($all_value as $value) {
                                    ?>
                                    <tr>
                                        <td><?php echo $value->m_purcs_id; ?></td>
                                        <td><?php echo $value->m_purcs_date; ?></td>
                                        <td><?php echo $value->m_purcs_spo; ?></td>
                                        <td><?php echo $value->m_purcs_lot; ?></td>
                                        <td><?php echo $value->m_item_name; ?></td>
                                        <td><?php echo $value->m_purcs_price; ?></td>
                                        <td><?php echo $value->m_purcs_qty; ?></td>
                                        <td>
                                            <button class="btn btn-link p-0 show-lot-data text-primary" data-type="sale"
                                                data-purid="<?= $value->m_purcs_id ?>"><?= $value->sold_qty ?: 0 ?></button>
                                        </td>
                                        <td>
                                            <button class="btn btn-link p-0 show-lot-data text-success" data-type="issue"
                                                data-purid="<?= $value->m_purcs_id ?>"><?= $value->issued_qty ?: 0 ?></button>
                                        </td>
                                        <td>
                                            <button class="btn btn-link p-0 show-lot-data text-danger" data-type="return"
                                                data-purid="<?= $value->m_purcs_id ?>"><?= $value->returned_qty ?: 0 ?></button>
                                        </td>
                                        <td><?php echo $value->m_purcs_available; ?></td>

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


<!-- ========================Footer================Fix======= -->
<!-- Modal to show lot details -->
<div class="modal fade" id="lotDetailModal" tabindex="-1" aria-labelledby="lotDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lotDetailModalLabel">Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="lotDetailBody">
                    <div class="text-center">Loading...</div>
                </div>
            </div>
        </div>
    </div>
</div>

                <!-- Modal to edit lot (sale / issue) -->
                <div class="modal fade" id="lotEditModal" tabindex="-1" aria-labelledby="lotEditModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="lotEditModalLabel">Edit Lot</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="editRecordId">
                                <input type="hidden" id="editRecordType">
                                <div class="mb-2">
                                    <label class="form-label">Current Lot</label>
                                    <input type="text" id="editCurrentLot" class="form-control" readonly>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">New Lot (enter lot id)</label>
                                    <input type="text" id="editNewLot" class="form-control" placeholder="Enter new lot id (numeric)">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" id="saveEditLotBtn" class="btn btn-primary btn-sm">Save</button>
                            </div>
                        </div>
                    </div>
                </div>

<?php $this->view('footer'); ?>
<?php $this->view('js/sale_js') ?>
<?php $this->view('js/custom_js'); ?>

<script>
    $(document).on('click', '.show-lot-data', function (e) {
        e.preventDefault();
        var type = $(this).data('type');
        var purid = $(this).data('purid');

        $('#lotDetailModalLabel').text(type.charAt(0).toUpperCase() + type.slice(1) + ' List');
        $('#lotDetailBody').html('<div class="text-center">Loading...</div>');
        $('#lotDetailModal').modal('show');

        if (type === 'sale') {
            $.ajax({
                url: "<?= site_url('Sales/purchase_sales_list') ?>",
                type: 'POST',
                data: { purid },
                dataType: 'JSON',
                success: function (res) {
                    renderSaleList(res);
                },
                error: function () {
                    $('#lotDetailBody').html('<div class="text-danger">Failed to load data.</div>');
                }
            });
        } else if (type === 'issue' || type === 'return') {
            $.ajax({
                url: "<?= site_url('Sales/purchase_issue_list') ?>",
                type: 'POST',
                data: { purid: purid, itype: (type === 'issue' ? 1 : 2) },
                dataType: 'JSON',
                success: function (res) {
                    renderIssueList(res, type);
                },
                error: function () {
                    $('#lotDetailBody').html('<div class="text-danger">Failed to load data.</div>');
                }
            });
        }

    });

    function renderSaleList(data) {
        if (!data || data.length === 0) {
            $('#lotDetailBody').html('<div class="text-muted">No sale records found.</div>');
            return;
        }
        var totalQty = 0, totalAmount = 0;
        var html = `<div class="mb-2"><input type="text" class="form-control modal-table-search" placeholder="Search..."></div><div class="table-responsive"><table class="table table-striped table-bordered modal-custom-table">`;
        html += `<thead><tr><th data-sort="#">#</th><th data-sort="text">Voucher</th><th data-sort="date">Date</th><th data-sort="text">Customer</th><th data-sort="text">Item</th><th data-sort="number">Qty</th><th data-sort="number">Price</th><th data-sort="number">Total</th><th data-sort="text">Sale Incharge</th><th>Action</th></tr></thead><tbody>`;
        $.each(data, function (i, it) {
            totalQty += parseFloat(it.m_sale_qty || 0);
            totalAmount += parseFloat(it.m_sale_total || 0);
            html += '<tr>';
            html += '<td>' + (i + 1) + '</td>';
            html += '<td>' + (it.m_sale_spo || it.m_sale_spo) + '</td>';
            html += '<td>' + (it.m_sale_date || '') + '</td>';
            html += '<td>' + (it.m_cust_name || it.m_cust_name || '') + '</td>';
            html += '<td>' + (it.m_item_name || it.m_item_name || '') + '</td>';
            html += '<td>' + (it.m_sale_qty || it.m_sale_qty || '') + '</td>';
            html += '<td>' + (it.m_sale_price || it.m_sale_price || '') + '</td>';
            html += '<td>' + (it.m_sale_total || it.m_sale_total || '') + '</td>';
            html += '<td>' + (it.m_user_name || it.m_user_name || '') + '</td>';
            html += '<td><button class="btn btn-sm btn-outline-primary edit-lot-btn" data-type="sale" data-id="' + (it.m_sale_id || '') + '" data-currentlot="' + (it.m_sale_lot || '') + '">Edit Lot</button></td>';
            html += '</tr>';
        });
        html += '</tbody> <tfoot><tr><th colspan="5" class="text-right">Total:</th><th>' + totalQty + '</th><th></th><th>' + totalAmount + '</th><th></th></tr></tfoot></table></div>';
        $('#lotDetailBody').html(html);
    }

    function renderIssueList(data, type) {
        if (!data || data.length === 0) {
            $('#lotDetailBody').html('<div class="text-muted">No records found.</div>');
            return;
        }
        var totalQty = 0;
        var totalQty = 0;
        var totalSaleQty = 0;
        var totalBalanceQty = 0;
        var totalSaleAmount = 0;
        var html = `<div class="mb-2">    <input type="text" class="form-control modal-table-search" placeholder="Search..."></div><div class="table-responsive"><table class="table table-striped table-bordered modal-custom-table">`;
        html += `<thead><tr><th data-sort="#">#</th><th data-sort="text">Voucher</th><th data-sort="text">Indicator</th><th data-sort="date">Date</th><th data-sort="text">Staff/Customer</th><th data-sort="text">Item</th><th data-sort="number">ISSUE Qty</th><th data-sort="number">SALE QTY</th><th data-sort="number">BALANCE QTY</th><th data-sort="number">SALE AMOUNT</th><th>Action</th><th>Edit Lot</th></tr></thead><tbody>`;
        $.each(data, function (i, it) {
            totalQty += parseFloat(it.si_issue_qty || 0);
            totalSaleQty += parseFloat(it.total_sale_qty || 0);
            totalBalanceQty += parseFloat(it.total_balance_qty || 0);
            totalSaleAmount += parseFloat(it.total_sale_amount || 0);
            html += '<tr>';
            html += '<td>' + (i + 1) + '</td>';
            html += '<td>' + (it.si_issue_spo || it.si_issue_spo) + '</td>';
            html += '<td>' + (it.badge || it.badge) + '</td>';
            html += '<td>' + (it.si_issue_date || '') + '</td>';
            html += '<td>' + (it.m_user_name || it.m_cust_name || '') + '</td>';
            html += '<td>' + (it.m_item_name || '') + '</td>';
            html += '<td>' + (it.si_issue_qty || 0) + '</td>';
            html += '<td>' + (it.total_sale_qty || 0) + '</td>';
            html += '<td>' + (it.total_balance_qty || 0) + '</td>';
            html += '<td>' + (it.total_sale_amount || 0) + '</td>';
            var isPending = (it.badge && it.badge.toLowerCase().includes('pending'));

            var returnBtn = isPending
                ? `<button class="btn btn-danger btn-sm return-stock-btn"
                    data-userid="${it.si_issue_user}"
                    data-date="${it.si_issue_date}">
                    Return
                </button>`
                : `<button class="btn btn-secondary btn-sm" disabled>
                    Return
                </button>`;

            html += '<td>' + returnBtn + '</td>';
            html += '<td><button class="btn btn-sm btn-outline-primary edit-lot-btn" data-type="issue" data-id="' + (it.si_issue_id || '') + '" data-currentlot="' + (it.si_issue_lotno || '') + '">Edit Lot</button></td>';
            html += '</tr>';
        });
        html += '</tbody><tfoot><tr><th colspan="6" class="text-right">Total</th><th>' + totalQty + '</th><th>' + totalSaleQty + '</th><th>' + totalBalanceQty + '</th><th>' + totalSaleAmount + '</th></tr></tfoot></table></div>';
        $('#lotDetailBody').html(html);
    }

    // ================= SEARCH FUNCTION =================
    $(document).on("keyup", ".modal-table-search", function () {
        var value = $(this).val().toLowerCase();
        var table = $(this).closest("div").next(".table-responsive").find("table");

        table.find("tbody tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });


    // ================= SORT FUNCTION =================
    $(document).on("click", ".modal-custom-table th", function () {
        var table = $(this).closest("table");
        var rows = table.find("tbody tr").toArray();
        var index = $(this).index();
        var type = $(this).data("sort");
        var asc = $(this).hasClass("asc");

        table.find("th").removeClass("asc desc");
        $(this).addClass(asc ? "desc" : "asc");

        rows.sort(function (a, b) {
            var A = $(a).children("td").eq(index).text().trim();
            var B = $(b).children("td").eq(index).text().trim();

            if (type === "number") {
                return asc ? B - A : A - B;
            }

            if (type === "date") {
                return asc ? new Date(B) - new Date(A) : new Date(A) - new Date(B);
            }

            return asc
                ? B.localeCompare(A)
                : A.localeCompare(B);
        });

        $.each(rows, function (i, row) {
            table.children("tbody").append(row);
        });
    });

    $(document).on("click", ".main_table th", function () {
        var table = $(this).closest("table");
        var rows = table.find("tbody tr").toArray();
        var index = $(this).index();
        var type = $(this).data("sort");
        var asc = $(this).hasClass("asc");

        table.find("th").removeClass("asc desc");
        $(this).addClass(asc ? "desc" : "asc");

        rows.sort(function (a, b) {
            var A = $(a).children("td").eq(index).text().trim();
            var B = $(b).children("td").eq(index).text().trim();

            if (type === "number") {
                return asc ? B - A : A - B;
            }

            if (type === "date") {
                return asc ? new Date(B) - new Date(A) : new Date(A) - new Date(B);
            }

            return asc
                ? B.localeCompare(A)
                : A.localeCompare(B);
        });

        $.each(rows, function (i, row) {
            table.children("tbody").append(row);
        });
    });

    // ================= RETURN STOCK FUNCTION =================
    $(document).on("click", ".return-stock-btn", function () {

        var user_id = $(this).data("userid");
        var from_date = $(this).data("date");
        var btn = $(this);

        if (!confirm("Are you sure you want to return this stock?")) {
            return;
        }

        btn.prop("disabled", true).text("Processing...");

        $.ajax({
            url: "<?= site_url('Api_Controller/insert_return_item') ?>",
            type: "POST",
            data: {
                user_id: user_id,
                from_date: from_date
            },
            dataType: "json",
            success: function (res) {

                if (res.response === "success") {
                    alert(res.message);
                    // location.reload();
                    btn.prop("disabled", true).text("Return");
                } else {
                    alert(res.message);
                    btn.prop("disabled", false).text("Return");
                }
            },
            error: function () {
                alert("Server error occurred.");
                btn.prop("disabled", false).text("Return");
            }
        });
    });

    // ================= EDIT LOT (SALE / ISSUE) =================
    var lastEditBtn = null;
    $(document).on('click', '.edit-lot-btn', function (e) {
        e.preventDefault();
        lastEditBtn = $(this);
        var type = lastEditBtn.data('type');
        var id = lastEditBtn.data('id');
        var current = lastEditBtn.data('currentlot');

        $('#editRecordType').val(type);
        $('#editRecordId').val(id);
        $('#editCurrentLot').val(current);
        $('#editNewLot').val('');
        $('#lotEditModal').modal('show');
    });

    $('#saveEditLotBtn').on('click', function () {
        var type = $('#editRecordType').val();
        var id = $('#editRecordId').val();
        var newlot = $('#editNewLot').val().trim();
        if (!newlot) {
            alert('Please enter new lot id');
            return;
        }

        var postUrl = '';
        var postData = {};
        if (type === 'sale') {
            postUrl = "<?= site_url('Sales/ajax_update_sale_lot') ?>";
            postData = { id: id, m_sale_lot: newlot };
        } else {
            postUrl = "<?= site_url('Sales/ajax_update_issue_lot') ?>";
            postData = { id: id, si_issue_lotno: newlot };
        }

        var btn = $(this);
        btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: postUrl,
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function (res) {
                if (res.status && res.status === 'success') {
                    alert(res.message || 'Updated');
                    if (lastEditBtn) {
                        lastEditBtn.data('currentlot', newlot);
                    }
                    $('#lotEditModal').modal('hide');
                } else {
                    alert(res.message || 'Update failed');
                }
            },
            error: function () {
                alert('Server error occurred.');
            },
            complete: function () {
                btn.prop('disabled', false).text('Save');
            }
        });
    });
</script>
<!-- ========================Footer================Fix======= -->