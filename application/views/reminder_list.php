<!-- ========================Header==============Fix========= -->
<?php $this->view('head'); ?>
<?php $this->view('header'); ?>

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
<style>
    input[type="checkbox"] {
        width: 22px;
        /* Adjust size */
        height: 22px;
    }

    .select2-results__option--disabled {
        display: none;
    }

    span.select2.select2-container.select2-container--default.select2-container--above {
        overflow-y: scroll;
        max-height: 300px !important;
    }
</style>
<section class="py-4" style="background:#f3f3ff;min-height:70vh;">
    <div class="container-fluid">
        <?php if ($pgtype == 1) { ?>
            <div class="row justify-content-evenly g-2 mb-2">
                <div class="col-12">
                    <form action="<?= base_url('Sales/Reminder_list') ?>" method="POST" class="row align-items-center">

                        <div class="col-2">
                            <div class="form-group">
                                <label for="">Last Sale Days</label>
                                <select name="days" id="days" class="form-control">
                                    <option value="10" <?= $days == 10 ? 'selected' : '' ?>>10</option>
                                    <option value="15" <?= $days == 15 ? 'selected' : '' ?>>15</option>
                                    <option value="20" <?= $days == 20 ? 'selected' : '' ?>>20</option>
                                    <option value="30" <?= $days == 30 ? 'selected' : '' ?>>30</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label for="">Group</label>
                                <select name="group_id" id="group_id" class="form-select select2">
                                    <option value="o">Admin</option>
                                    <?php
                                    if (!empty($group_dtl)) {
                                        foreach ($group_dtl as $vat) {

                                            if ($group_id == $vat->m_group_id) {
                                                $option1 = "selected";
                                            } else {
                                                $option1 = "";
                                            }

                                    ?>
                                            <option value="<?php echo $vat->m_group_id; ?>" <?= $option1 ?>><?= $vat->m_group_name; ?>
                                        <?php
                                        }
                                    }

                                        ?>

                                </select>
                            </div>
                        </div>
                        <div class="col-4 mt-4">
                            <button class="btn btn-info" type="submit"><i class="bi bi-search mx-1"></i> Search</button>
                            <a class="btn btn-danger" href="<?= base_url('Sales/Reminder_list') ?>"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row justify-content-evenly g-0">
                <div class="col-12">
                    <form action="#" id="reminder_form" method="POST" class="row align-items-center">

                        <div class="table-responsive bg-light printTableDiv" style="height: 64vh;">
                            <table id="cust_tbl" class="table table-striped table-bordered dt-responsive nowra w-100">
                                <thead>
                                    <tr>
                                        <th class="text-center"><input type="checkbox" id="all_checked" class=""></th>
                                        <th>Name</th>
                                        <th>Mobile No.</th>
                                        <th>Line</th>
                                        <th>Last Sale</th>
                                        <th>Last Recived</th>
                                        <th>Balance Amount</th>
                                        <th>Crate Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    if (!empty($mech_value)) {
                                        foreach ($mech_value as $value) {
                                    ?>
                                            <tr>
                                                <td class="text-center"><input type="checkbox" name="cust_ids[]" class="cust_idscls" value="<?php echo $value['m_cust_id'] ?>"></td>
                                                <td><?php echo $value['m_cust_name'] . '<br>' . $value['m_cust_hndiname']; ?></td>
                                                <td><?php echo $value['m_cust_mobile']; ?></td>
                                                <td><?php echo $value['m_group_name']; ?></td>
                                                <td><?php echo !empty($value['last_sale_date']) ? date('d-m-Y', strtotime($value['last_sale_date'])) : '-'; ?></td>
                                                <td><?php echo !empty($value['last_recvd_date']) ? date('d-m-Y', strtotime($value['last_recvd_date'])) : '-'; ?></td>
                                                <td><?php echo $value['total_balance']; ?></td>
                                                <td><?php echo $value['total_crate_balance']; ?></td>


                                            </tr>
                                    <?php
                                            $i++;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <button id="reminder_btn_submit" type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php } else { ?>
            <div class="card p-3">
                <form action="" id="frm-send-summary" method="post">
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label>Date <span class="text-danger">*</span></label>
                                <input type="date" name="to_date" id="to_date" class="form-control" required autofocus value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="form-group">
                                <label for="">Group (optional)</label>
                                <select name="group_id" id="group_id" class="form-select select2">
                                    <option value="">All Group</option>
                                    <option value="0">Admin</option>
                                    <?php
                                    if (!empty($group_dtl)) {
                                        foreach ($group_dtl as $vat) {
                                            $option1 = ($group_id == $vat->m_group_id) ? "selected" : "";
                                    ?>
                                            <option value="<?php echo $vat->m_group_id; ?>" <?= $option1 ?>><?= $vat->m_group_name; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="form-group">
                                <label for="">Customer</label>
                                <select name="cust_id[]" id="cust_id" class="form-select select2" multiple required>
                                    <option value="all">Select all</option>
                                    <?php
                                    if (!empty($cust_list)) {
                                        foreach ($cust_list as $vat) {
                                    ?>
                                            <option value="<?php echo $vat->m_cust_id; ?>" data-group="<?= $vat->m_cust_group ?>"> <?= $vat->m_cust_name . ' | ' . $vat->m_cust_mobile; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-3">
                            <button class="btn btn-success mt-4" type="submit" id="btn-send-summary">Submit</button>
                        </div>
                        <div class="col-12 mt-3" id="selected-custo">
                        </div>
                    </div>
                </form>
            </div>
        <?php } ?>
    </div>
</section>
<!-- ========== Page Content ========== -->



<!-- ========================Footer================Fix======= -->
<?php $this->view('footer'); ?>
<?php $this->view('js/user_js') ?>
<?php $this->view('js/custom_js'); ?>
<script>
    $(document).ready(function() {
        $("#group_id").change(function() {
            var selectedGroup = $(this).val();

            $("#cust_id option").each(function() {
                var customerGroup = $(this).data("group");

                if (selectedGroup === "" || selectedGroup == customerGroup) {
                    $(this).prop("disabled", false).show();
                } else {
                    $(this).prop("disabled", true).hide();
                }
            });

            // Reset the selection
            $("#cust_id").val('').trigger("change");
        });

        $('#cust_id').on('change', function() {
            var selectedValues = $(this).val();

            if (selectedValues && selectedValues.includes('all')) {
                // Select all options except the "all" option itself
                var allValues = [];
                $('#cust_id option').each(function() {
                    if ($(this).val() !== 'all') {
                        allValues.push($(this).val());
                    }
                });

                $(this).val(allValues).trigger('change'); // Update the select2 UI
            }
        });
    });
</script>
<!-- ========================Footer================Fix======= -->