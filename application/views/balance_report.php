<?php include("head.php"); ?>
<?php
$is_cust_in = $this->session->userdata('is_cust_in');
$cust_id = $this->session->userdata('cust_id');
if ($is_cust_in == false) {
    include("header.php");
} ?>
<!-- ========== Page Content ========== -->

<style>
    /* body{
        overflow-x: hidden;
    } */
    #report_table tr:hover,
    #report_table td:hover {
        background-color: blue;
        color: #fff;
    }

    #report_table .trclass.active td {
        background-color: blue !important;
        color: #fff !important;
    }

    #report_table .card:hover,
    #report_table .card.active {
        background-color: #fff0b6 !important;
    }

    p.m-0.small {
        position: relative;
        bottom: 2px;
        font-weight: 500;
        color: #757575;
    }

    #report_table .card.active p.m-0.small {
        color: black;
    }

    #report_table .card p.m-0.small i {
        position: relative;
        top: 2px;
    }

    #report_table .card.active .bi-caret-right-fill::before {
        transform: rotate(90deg);
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
                <?php if ($this->session->userdata('is_cust_in') == true) {
                    echo '<a href="' . base_url('Login/logout_cust') . '" class="btn btn-danger btn-sm">
                      <i class="bi bi-box-arrow-left me-2"></i>Exit
                  </a>';
                } else {
                    echo '<button onclick="history.back()" class="btn btn-danger btn-sm">
                <i class="bi bi-box-arrow-left me-2"></i>Exit
            </button>';
                } ?>
            </div>
        </div>
    </div>
</section>

<?php if ($pagetype == 1) { ?>
    <section class="py-3 d-flex" style="background:#f3f3ff;min-height:77vh;">
        <div class="container">

            <form class="row g-4 justify-content-between bfilterform" method="post" action="#" id="frm-balance_report">
                <div class="col-md-5">
                    <img src="<?php echo base_url("assets/imgs/balance.svg") ?>" alt="" class="w-100">
                </div>
                <div class="col-md-7">
                    <div class="row g-4">
                        <div class="col-6">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <label for="" class="mb-0 form-label small">Date From</label>
                                </div>
                                <div class="col-9">
                                    <input type="date" max="<?= date('Y-m-d') ?>" name="from_date" id="from_date" value="<?= date('Y-m-d') ?>" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="" class="mb-0 form-label small">To </label>
                                </div>
                                <div class="col-9">
                                    <input type="date" max="<?= date('Y-m-d') ?>" name="to_date" id="to_date" value="<?= date('Y-m-d') ?>" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="row align-items-center">
                                <div class="col-auto" style="min-width: 100px;">
                                    <label for="" class="mb-0 form-label small">Agent</label>
                                </div>
                                <div class="col-10">
                                    <select name="agent" id="agent" class="form-select select2">
                                        <option value="">All</option>
                                        <option value="o">Admin</option>
                                        <?php
                                        foreach ($agent_dtl as $agent) {

                                        ?>
                                            <option value="<?php echo $agent->m_user_group; ?>">
                                                <?php echo $agent->m_user_name ?></option>
                                        <?php
                                        }

                                        ?>

                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="row align-items-center">
                                <div class="col-auto" style="min-width: 100px;">
                                    <label for="" class="mb-0 form-label small">Crate Type</label>
                                </div>
                                <div class="col-10">
                                    <select name="cratetype" id="cratetype" class="form-select select2">
                                        <option value="">All</option>
                                        <?php if (!empty($all_crates)) {
                                            foreach ($all_crates as $crty) {
                                                echo ' <option value="' . $crty->m_itgrp_id . '">' . $crty->m_itgrp_title . '</option>';
                                            }
                                        } ?>

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="row align-items-center">
                                <div class="col-auto" style="min-width: 100px;">
                                    <label for="" class="mb-0 form-label small">Order By</label>
                                </div>
                                <div class="col-10">
                                    <select name="orderby" id="orderby" class="form-select select2">
                                        <option value="1">Name Wise</option>
                                        <option value="2">Amount Wise</option>
                                        <option value="3">City Wise</option>
                                        <option value="4">City Amount Wise</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center justify-content-start gap-3 mt-5">
                        <!-- <button class="btn btn-primary click-btn" id="btn-custblnc" data-linkk="<?= base_url('Reports/customer_balance_report') ?>" type="submit">Customer Balance</button> -->
                        <button class="btn btn-primary click-btn" id="btn-custblncwc" data-linkk="<?= base_url('Reports/cust_blncrate_report') ?>" type="submit">Customer Balance with Crate</button>
                        <!-- <button class="btn btn-primary click-btn" id="btn-suppblncnl" data-linkk="<?= base_url('Reports/supplier_balance_report') ?>" type="submit">Supplier Balance</button> -->
                        <button class="btn btn-primary click-btn" id="btn-suppcrate" data-linkk="<?= base_url('Reports/supplier_blncrate_report') ?>" type="submit">Supplier Balance with Crate</button>
                        <!-- <button class="btn btn-secondary click-btn" id="btn-staffblnc" data-linkk="#" type="button">Staff Balance</button>
                        <button class="btn btn-secondary click-btn" id="btn-custgrpblnc" data-linkk="#" type="button">Customer Balance Group</button>
                        <button class="btn btn-secondary click-btn" id="btn-genblnc" data-linkk="#" type="button">General Balance</button> -->
                    </div>
                </div>
            </form>
        </div>
    </section>
<?php } else if ($pagetype == 2) { ?>

    <section class="pt-3 px-4 d-flex " style="background:#f3f3ff;min-height:77vh;">
        <form class="row g-5 bfilterform" method="post" action="<?= base_url('Reports/credit_sale') ?>" id="frm-report-list">
            <div class="col-3">
                <img src="<?php echo base_url("assets/imgs/account.png"); ?>" alt="text" class="w-100">
            </div>

            <div class="col-9">
                <div class="row row-cols-5 g-2" id="report_table">
                    <div class="col">
                        <div class="card p-2 rounded-3 border-0 trclass cust_btn active" id="tr1" data-value="<?= base_url('Reports/credit_sale') ?>">
                            <p class="m-0 small"><i class="bi bi-caret-right-fill"></i><small> Sale
                                    Report</small></p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card p-2 rounded-3 border-0 trclass cust_btn" id="tr2" data-value="<?= base_url('Reports/crate_recieve/2') ?>">
                            <p class=" m-0 small"><i class="bi bi-caret-right-fill"></i><small> Crate Recieve Report</small>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card p-2 rounded-3 border-0 trclass suplier_btn" id="tr3" data-value="<?= base_url('Reports/payment_report/2') ?>">
                            <p class=" m-0 small"><i class="bi bi-caret-right-fill"></i><small> Crate Issue Report</small>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card p-2 rounded-3 border-0 trclass cust_btn" id="tr4" data-value="<?= base_url('Reports/crate_recieve/1') ?>">
                            <p class=" m-0 small"><i class="bi bi-caret-right-fill"></i><small> Receipt Report Cash/AC</small>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card p-2 rounded-3 border-0 trclass suplier_btn" id="tr5" data-value="<?= base_url('Reports/payment_report/1') ?>">
                            <p class=" m-0 small"><i class="bi bi-caret-right-fill"></i><small> Payment Report Cash/AC</small>
                            </p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card p-2 rounded-3 border-0 trclass suplier_btn" id="tr6" data-value="<?= base_url('Reports/purchase_report') ?>">
                            <p class=" m-0 small"><i class="bi bi-caret-right-fill"></i><small> Purchase Report</small></p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card p-2 rounded-3 border-0 trclass agent_btn" id="tr7" data-value="<?= base_url('Reports/staff_report') ?>">
                            <p class=" m-0 small"><i class="bi bi-caret-right-fill"></i><small> Staff Sale Report</small></p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card p-2 rounded-3 border-0 trclass agent_btn" id="tr8" data-value="<?= base_url('Reports/phone_book') ?>">
                            <p class=" m-0 small"><i class="bi bi-caret-right-fill"></i><small> Customer Phone Book</small></p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card p-2 rounded-3 border-0 trclass suplier_btn" id="tr9" data-value="<?= base_url('Reports/turck_report') ?>">
                            <p class=" m-0 small"><i class="bi bi-caret-right-fill"></i><small> Truck Profit and Loss Report</small></p>
                        </div>
                    </div>
                    <!-- <div class="col">
                            <div class="card p-2 rounded-3 border-0 trclass agent_btn" data-fild="1" data-value="<? //= base_url('Reports/staff_performance_report') 
                                                                                                                    ?>">
                                <p class=" m-0 small"><i class="bi bi-caret-right-fill"></i><small> Staff Performance Report</small></p>
                            </div>
                        </div> -->
                    <div class="col">
                        <div class="card p-2 rounded-3 border-0 trclass agent_btn" id="tr10" data-fild="1" data-value="<?= base_url('Reports/staff_performance_new_report') ?>">
                            <p class=" m-0 small"><i class="bi bi-caret-right-fill"></i><small> Line Wise Sale Report</small></p>
                        </div>
                    </div>

                </div>
                <div class="row g-4 mt-4">
                    <div class="col-12">
                        <div class="row g-4 align-items-center justify-content-between">
                            <div class="col-2">
                                <label for="" class="mb-0 form-label small">Date From</label>
                            </div>
                            <div class="col-10">
                                <div class="row">
                                    <div class="col-5">
                                        <input type="date" max="<?= date('Y-m-d') ?>" name="from_date" id="from_date" value="<?= date('Y-m-d') ?>" class="form-control">
                                    </div>
                                    <div class="col-2">
                                        <label for="" class="mb-0 form-label small">To </label>
                                    </div>
                                    <div class="col-5">
                                        <input type="date" max="<?= date('Y-m-d') ?>" name="to_date" id="to_date" value="<?= date('Y-m-d') ?>" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12" id="cust_div">
                        <div class="row g-4 align-items-center">
                            <div class="col">
                                <label for="" class="mb-0 form-label small">Customer Accounts</label>
                            </div>
                            <div class="col-10">
                                <select name="customers" id="customers" class="form-select select2">
                                    <option value="">All</option>
                                    <?php
                                    foreach ($cust_dtl as $calue) {

                                        // if ($city == $city_value->m_city_id) {
                                        //     $option1 = "selected";
                                        // } else {
                                        // $option1 = "";
                                        // }

                                    ?>
                                        <option value="<?php echo $calue->m_cust_id; ?>">
                                            <?php echo $calue->m_cust_name . ' | ' . $calue->m_cust_mobile ?>
                                        </option>
                                    <?php
                                    }

                                    ?>

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-none" id="sup_div">
                        <div class="row g-4 align-items-center">
                            <div class="col">
                                <label for="" class="mb-0 form-label small">Supplier Accounts</label>
                            </div>
                            <div class="col-10">
                                <select name="suppiler" id="suppiler" class="form-select select2">
                                    <option value="">All</option>
                                    <?php
                                    foreach ($supl_dtl as $calue) {

                                        // if ($city == $city_value->m_city_id) {
                                        //     $option1 = "selected";
                                        // } else {
                                        // $option1 = "";
                                        // }

                                    ?>
                                        <option value="<?php echo $calue->m_user_id; ?>">
                                            <?php echo $calue->m_user_name . ' | ' . $calue->m_user_mobile ?>
                                        </option>
                                    <?php
                                    }

                                    ?>

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-none" id="agent_div">
                        <div class="row g-4 align-items-center">
                            <div class="col">
                                <label for="" class="mb-0 form-label small">Agent Accounts</label>
                            </div>
                            <div class="col-10">
                                <select name="agent" id="agent" class="form-select select2">
                                    <option value="">All</option>
                                    <?php
                                    foreach ($agent_dtl as $calue) {

                                        // if ($city == $city_value->m_city_id) {
                                        //     $option1 = "selected";
                                        // } else {
                                        // $option1 = "";
                                        // }

                                    ?>
                                        <option value="<?php echo $calue->m_user_id; ?>">
                                            <?php echo $calue->m_user_name . ' | ' . $calue->m_user_mobile ?>
                                        </option>
                                    <?php
                                    }

                                    ?>

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-none" id="sprtype">
                        <div class="row g-4 align-items-center">
                            <div class="col">
                                <label for="report_type" class="mb-0 form-label small">Report Type</label>
                            </div>
                            <div class="col-10">
                                <select name="report_type" id="report_type" class="form-select select2">
                                    <option value="1">Sale Report</option>
                                    <option value="2">Amount Recieved Report</option>
                                    <option value="3">Crate Recieved Report</option>

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="row g-4 align-items-center">
                            <div class="col">
                                <label for="" class="mb-0 form-label small">Order By</label>
                            </div>
                            <div class="col-10">
                                <select name="orderby" id="orderby" class="form-select select2">
                                    <option value="3">Date Wise</option>
                                    <option value="1">Name Wise</option>
                                    <option value="2">Amount Wise</option>
                                    <option value="4">Date Amount Wise</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex gap-2 justify-content-end">
                            <button class="btn btn-warning rounded-pill submit_btn px-5" name="summary" type="submit" value="1">Summary</button>
                            <button class="btn btn-primary rounded-pill submit_btn px-5" name="summary" type="submit" value="2">Detail</button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </section>

<?php } else if ($pagetype == 3) {  ?>
    <section class="d-flex " style="background:#f3f3ff;min-height:77vh;">
        <div class="container">

            <form class="row g-5 bfilterform" method="post" action="#" id="frm-ledger-list">

                <div class="col-9">
                    <div class="row g-4 mt-4 align-items-center justify-content-between">
                        <div class="col-12">
                            <div class="row g-4 align-items-center justify-content-between">
                                <div class="col-2">
                                    <label for="" class="mb-0 form-label small">Account Type</label>
                                </div>
                                <div class="col-10">
                                    <select name="account_type" id="account_type" class="form-select select2">
                                        <option value="1">Customer</option>
                                        <?php if ($this->session->userdata('is_cust_in') == false) { ?>
                                            <option value="2">Supplier</option>
                                            <option value="6">Staff Commission</option>
                                            <option value="7">General</option>
                                            <option value="8">Investment</option>
                                            <option value="3">Expense</option>
                                            <option value="9">Fright</option>
                                            <option value="4">Cash</option>
                                            <option value="5">Bank</option>
                                            <option value="10">Discount Ledger</option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label for="" class="mb-0 form-label small">Date From</label>
                                </div>
                                <div class="col-10">
                                    <div class="row">
                                        <div class="col-5">
                                            <input type="date" max="<?= date('Y-m-d') ?>" name="from_date" id="from_date" value="<?= date('Y-m-d') ?>" class="form-control">
                                        </div>
                                        <div class="col-2">
                                            <label for="" class="mb-0 form-label small">To </label>
                                        </div>
                                        <div class="col-5">
                                            <input type="date" max="<?= date('Y-m-d') ?>" name="to_date" id="to_date" value="<?= date('Y-m-d') ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($this->session->userdata('is_cust_in') == false) { ?>
                            <div class="col-12" id="cust_div">
                                <div class="row g-4 align-items-center">
                                    <div class="col-2">
                                        <label for="" id="selet_label" class="mb-0 form-label small">Customer Accounts</label>
                                    </div>
                                    <div class="col-10">
                                        <select name="account_name" id="account_name_select" class="form-select select2" required>
                                            <option value="">--Select--</option>
                                            <?php
                                            foreach ($cust_dtl as $calue) {
                                            ?>
                                                <option value="<?php echo $calue->m_cust_id; ?>">
                                                    <?php echo $calue->m_cust_name . ' | ' . $calue->m_cust_mobile ?>
                                                </option>
                                            <?php
                                            }

                                            ?>

                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php } else {
                            echo '<input type="hidden" name="account_name" id="account_name_select" value="' . $cust_id . '">';
                        } ?>
                        <div class="col-12">
                            <div class="row g-4 align-items-center">
                                <div class="col-2">
                                    <label for="" class="mb-0 form-label small">Order By</label>
                                </div>
                                <div class="col-10">
                                    <select name="orderby" id="orderby" class="form-select select2">
                                        <option value="1">Name Wise</option>
                                        <option value="2">Amount Wise</option>
                                        <option value="3">City Wise</option>
                                        <option value="4">City Amount Wise</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex gap-2 justify-content-end">

                                <button class="btn btn-primary rounded-pill px-5 ledger_btn_submit" data-link="<?= base_url('Reports/customer_cash_ledger') ?>" id="custcashbtn" type="submit">Customer Ledger</button>
                                <button class="btn btn-success rounded-pill px-5 ledger_btn_submit" data-link="<?= base_url('Reports/customer_crate_ledger') ?>" id="custcratebtn" type="submit">Customer Crate Statement</button>
                                <?php if ($this->session->userdata('is_cust_in') == false) { ?>
                                    <button class="btn btn-primary rounded-pill px-5 ledger_btn_submit d-none" data-link="<?= base_url('Reports/supplier_cash_ledger') ?>" id="suppcashbtn" type="submit">Supplier Ledger</button>
                                    <button class="btn btn-success rounded-pill px-5 ledger_btn_submit d-none" data-link="<?= base_url('Reports/supplier_crate_ledger') ?>" id="suppcratebtn" type="submit">Supplier Crate Statement</button>
                                    <button class="btn btn-primary rounded-pill px-5 ledger_btn_submit d-none" data-link="<?= base_url('Reports/staffcomm_ledger') ?>" id="staffcommledgerbtn" type="submit">Staff Commission Ledger</button>
                                    <button class="btn btn-primary rounded-pill px-5 ledger_btn_submit d-none" data-link="<?= base_url('Reports/general_leger/1') ?>" id="generalledgerbtn" type="submit">General Ledger</button>
                                    <button class="btn btn-primary rounded-pill px-5 ledger_btn_submit d-none" data-link="<?= base_url('Reports/general_leger/2') ?>" id="investmentledgerbtn" type="submit">Investment Ledger</button>
                                    <button class="btn btn-primary rounded-pill px-5 ledger_btn_submit d-none" data-link="<?= base_url('Reports/expense_ledger') ?>" id="expenseledgerbtn" type="submit">Expense Ledger</button>
                                    <button class="btn btn-primary rounded-pill px-5 ledger_btn_submit d-none" data-link="<?= base_url('Reports/fright_ledger') ?>" id="frightledgerbtn" type="submit">Fright Ledger</button>
                                    <button class="btn btn-primary rounded-pill px-5 ledger_btn_submit d-none" data-link="<?= base_url('Reports/cash_ledger/1') ?>" id="cashledgerbtn" type="submit">Cash Ledger</button>
                                    <button class="btn btn-primary rounded-pill px-5 ledger_btn_submit d-none" id="bankledgerbtn" data-link="<?= base_url('Reports/cash_ledger/2') ?>" type="submit">Bank Ledger</button>
                                    <button class="btn btn-primary rounded-pill px-5 ledger_btn_submit d-none" id="voucherdebitbtn" data-link="<?= base_url('Reports/voucher_ledger/2') ?>" type="submit">Debit Voucher</button>
                                    <button class="btn btn-primary rounded-pill px-5 ledger_btn_submit d-none" id="vouchercreditbtn" data-link="<?= base_url('Reports/voucher_ledger/1') ?>" type="submit">Credit Voucher</button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <img src="<?php echo base_url("assets/imgs/report.png"); ?>" alt="text" class="w-100 mt-5">
                </div>


            </form>

        </div>
    </section>
<?php } ?>


<!-- code her -->

<!-- ========== Page Content ========== -->
<?php include("footer.php"); ?>
<?php $this->view('js/report_js') ?>
<?php $this->view('js/custom_js') ?>

<script>
    $(document).ready(function() {
        // Restore form data if available
        if (sessionStorage.getItem("ledgerFormData")) {
            let formData = JSON.parse(sessionStorage.getItem("ledgerFormData"));

            $("#account_type").val(formData.account_type || "").trigger("change");
            $("#from_date").val(formData.from_date || "");
            $("#orderby").val(formData.orderby || "").trigger("change");

            if (formData.account_name) {
                let $accountSelect = $("#account_name_select");
                if ($accountSelect.length) {
                    // Wait for select2 options to be fully loaded before setting value
                    setTimeout(() => {
                        $accountSelect.val(formData.account_name).trigger("change");
                    }, 500); // Adjust the delay if necessary
                }
            }
        }

        // Save form data before submission
        $("#frm-ledger-list").on("submit", function() {
            let formData = {
                account_type: $("#account_type").val(),
                from_date: $("#from_date").val(),
                orderby: $("#orderby").val(),
                account_name: $("#account_name_select").length ? $("#account_name_select").val() : ""
            };

            sessionStorage.setItem("ledgerFormData", JSON.stringify(formData));
        });

        if (sessionStorage.getItem("reportFormData")) {
            let formData = JSON.parse(sessionStorage.getItem("reportFormData"));

            $(`#${formData.id}`).trigger('click');
            $("#from_date").val(formData.from_date || "");
            $("#orderby").val(formData.orderby || "").trigger("change");
            $("#customers").val(formData.customers || "").trigger("change");
            $("#suppiler").val(formData.suppiler || "").trigger("change");
            $("#agent").val(formData.agent || "").trigger("change");
            $("#report_type").val(formData.report_type || "").trigger("change");
        }

        // Save form data before submission
        $("#frm-report-list").on("submit", function() {
            let formData = {
                id: $(".trclass.active").attr('id'),
                from_date: $("#from_date").val(),
                orderby: $("#orderby").val(),
                customers: $("#customers").val(),
                suppiler: $("#suppiler").val(),
                agent: $("#agent").val(),
                report_type: $("#report_type").val(),
            };

            sessionStorage.setItem("reportFormData", JSON.stringify(formData));
        });

        if (sessionStorage.getItem("balanceFormData")) {
            let formData = JSON.parse(sessionStorage.getItem("balanceFormData"));

            $("#from_date").val(formData.from_date || "");
            $("#agent").val(formData.agent || "").trigger("change");
            $("#cratetype").val(formData.cratetype || "").trigger("change");
            $("#orderby").val(formData.orderby || "").trigger("change");

        }

        // Save form data before submission
        $("#frm-balance_report").on("submit", function() {
            let formData = {
                from_date: $("#from_date").val(),
                agent: $("#agent").val(),
                cratetype: $("#cratetype").val(),
                orderby: $("#orderby").val(),
            };

            sessionStorage.setItem("balanceFormData", JSON.stringify(formData));
        });

        // URL parameter check
        const urlParams = new URLSearchParams(window.location.search);
        const report = urlParams.get('report');

        // Trigger tr10 automatically
        if (report == 'linewise') {
            $('#tr10').trigger('click');
        }

    });
</script>