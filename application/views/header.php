<!-- ========== header ========== -->
<header>
    <!-- top navigation -->
    <section class="py-2" style="background: #dfdfdf;">
        <div class="container-fluid">
            <div class="d-flex gap-1 align-items-center justify-content-between">
                <div>
                    <!-- <img src="<? // base_url('uploads/') . get_settings('m_app_logo') 
                                    ?>" alt="" style="height: 30px" /> -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Item
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('Master/Item_group') ?>">Item Group</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Master/Item_unit') ?>">Item Unit</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Master/Item_crate') ?>">Item Crate</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Master/item_list') ?>">Item Master</a></li>

                        </ul>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Account
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('Accounts/add_cust') ?>">Customer Account</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Accounts/add_user?pgtype=2') ?>">Supplier Account</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Accounts/add_user?pgtype=3') ?>">Loader Account</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Accounts/add_user?pgtype=1') ?>">Staff Account</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Accounts/add_user?pgtype=4') ?>">General Account</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Accounts/add_user?pgtype=5') ?>">Investment Account</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Master/expense_account_list') ?>">Expense Account</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Master/bank_account_list') ?>">Bank Account</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Master/cash_account_list') ?>">Cash Account</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Master/group_list') ?>">Groups</a></li>
                            <?php if ($this->session->userdata('user_type') == 8) : ?>
                                <li><a class="dropdown-item" href="<?= base_url('Accounts/branch_list') ?>">Branch List</a></li>
                            <?php endif; ?>

                        </ul>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Groups Trans
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('Sales/add_issue_item') ?>">Staff Item Issue</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Sales/add_sales') ?>">Create Sale</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Sales/recieved_list/1') ?>">Payment Recieved</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Sales/recieved_list/2') ?>">Create Recieved</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Sales/voucher_list') ?>">Voucher</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Accounts/customer_group_list') ?>">Customer Group</a></li>
                        </ul>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Administration
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('Sales/send_bill_indiviouly') ?>">Send Summary</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Sales/Reminder_list') ?>">Reminder List</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Sales/purchase_item_list') ?>">Stock in & out Report</a></li>
                            <?php if ($this->session->userdata('user_type') != 8) : ?>
                                <?php // Superadmin reaches this through Ledger > Account Type > Branch;
                                      // Create Transfer has its own tile in the icon row below. ?>
                                <li><a class="dropdown-item" href="<?= base_url('Reports/branch_ledger') ?>">Branch Ledger</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Windows
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('Master/city_list') ?>">City List</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Master/state_list') ?>">State List</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Welcome/application_setting') ?>">System Settings </a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Welcome/db_backup') ?>">Download Database Backup </a></li>

                        </ul>
                    </div>
                </div>
                <div class="d-flex align-items-center flex-wrap">
                    <div class="global-account-search d-flex align-items-center gap-2 me-3" style="min-width: 360px;">
                        <?php
                        $customers = $this->Main_model->get_cust_active_list();
                        $users = $this->Main_model->get_active_users(0);
                        $expenses = $this->Master_model->get_all_expenses();
                        function userTypeLabel($type)
                        {
                            switch ($type) {
                                case 1:
                                    return 'Staff';
                                case 2:
                                    return 'Supplier';
                                case 3:
                                    return 'Loader';
                                case 4:
                                    return 'General';
                                case 5:
                                    return 'Investment';
                                default:
                                    return 'User';
                            }
                        }
                        function expenseTypeLabel($type, $group)
                        {
                            if ($type == 2 && $group != 0) {
                                return 'Fright';
                            } else if ($type == 2) {
                                return 'Expense';
                            } else if ($type == 3) {
                                return 'Bank';
                            } else if ($type == 4) {
                                return 'Cash';
                            } else {
                                return 'Group';
                            }
                        }
                        ?>

                        <form id="global-ledger-search" class="d-flex align-items-center gap-1" onsubmit="return false;">
                            <input type="text" id="global_account_input" list="global_accounts" class="form-control form-control-sm" placeholder="Search customer/user" autocomplete="off" style="min-width:180px;" />
                            <datalist id="global_accounts">
                                <?php if (!empty($customers)) {
                                    foreach ($customers as $cust) {
                                        $label = htmlspecialchars($cust->m_cust_name . ' | ' . $cust->m_cust_mobile . ' (' . $cust->m_cust_id . ')');
                                        echo '<option value="' . $label . '" data-id="' . $cust->m_cust_id . '" data-type="customer">' . $label . '</option>';
                                    }
                                }
                                if (!empty($users)) {
                                    foreach ($users as $user) {
                                        $type = userTypeLabel($user->m_user_type);
                                        $label = htmlspecialchars($user->m_user_name . ' | ' . $user->m_user_mobile . ' (' . $user->m_user_id . ')');
                                        echo '<option value="' . $label . '" data-id="' . $user->m_user_id . '" data-type="' . strtolower($type) . '">' . $label . '</option>';
                                    }
                                }
                                if (!empty($expenses)) {
                                    foreach ($expenses as $expense) {
                                        $type = expenseTypeLabel($expense->m_group_type, $expense->m_group_group);
                                        $label = htmlspecialchars($expense->m_group_name . ' (' . $expense->m_group_id . ')');
                                        echo '<option value="' . $label . '" data-id="' . $expense->m_group_id . '" data-type="' . strtolower($type) . '">' . $label . '</option>';
                                    }
                                }

                                ?>
                            </datalist>
                            <input type="date" id="global_from_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" title="From Date" />
                            <input type="date" id="global_to_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" title="To Date" />
                            <button id="global_search_btn" class="btn btn-sm btn-primary" type="button">Search</button>
                            <input type="hidden" id="global_account_id" value="" />
                            <input type="hidden" id="global_account_type" value="" />
                        </form>
                    </div>

                    <div title="Total SMS Left" class="d-inline-flex justify-content-start p-2 gap-1 align-items-center rounded-pill me-3" style="align-self: center;background:#f4f4f4;">
                        <i class="bi bi-envelope-plus-fill"></i>
                        <div>

                            <h6 class="m-0 text-dark pe-3"> <?php $sms_bal = get_sms_balance();
                                                            if ($sms_bal === false) {
                                                                echo '';
                                                            } else {
                                                                $ids = explode(',', $sms_bal);
                                                                $bal =  $ids[0];
                                                                $total_balance = str_replace('[[', '', $bal);
                                                                echo $total_balance;
                                                            }
                                                            ?></h6>
                        </div>
                    </div>

                    <a href="<?= base_url() ?>">
                        <div class="d-inline-flex justify-content-start p-2 gap-1 align-items-center rounded-pill me-3" style="align-self: center;background:#f4f4f4;">
                            <!-- <img src="<? // base_url('uploads/') . get_settings('m_app_logo') 
                                            ?>" alt="" class="rounded-circle" style="aspect-ratio: 1/1;height:30px;"> -->
                            <div>
                                <h6 class="m-0 text-dark pe-3"><?= $login_detail->m_user_name ?></h6>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const accountInput = document.getElementById('global_account_input');
            const datalist = document.getElementById('global_accounts');
            const accountIdInput = document.getElementById('global_account_id');
            const accountTypeInput = document.getElementById('global_account_type');
            const fromDateInput = document.getElementById('global_from_date');
            const toDateInput = document.getElementById('global_to_date');
            const searchBtn = document.getElementById('global_search_btn');

            function clearHiddenFields() {
                accountIdInput.value = '';
                accountTypeInput.value = '';
            }

            function setHiddenFromSelection(value) {
                const option = Array.from(datalist.options).find(opt => opt.value === value);
                if (option) {
                    accountIdInput.value = option.getAttribute('data-id');
                    accountTypeInput.value = option.getAttribute('data-type');
                } else {
                    clearHiddenFields();
                }
            }

            accountInput.addEventListener('input', function() {
                setHiddenFromSelection(this.value);
            });

            searchBtn.addEventListener('click', function() {
                const accountName = accountInput.value.trim();
                const accountId = accountIdInput.value.trim();
                const accountType = accountTypeInput.value.trim();
                const fromDate = fromDateInput.value;
                const toDate = toDateInput.value;

                if (!accountName || !accountId || !accountType) {
                    alert('Please select an account from the list to search.');
                    accountInput.focus();
                    return;
                }

                if (!fromDate || !toDate) {
                    alert('Please fill both From Date and To Date.');
                    return;
                }

                if (fromDate > toDate) {
                    alert('From Date cannot be later than To Date.');
                    return;
                }

                let targetUrl = '';
                switch (accountType.toLowerCase()) {
                    case 'customer':
                        targetUrl = '<?= base_url('Reports/customer_cash_ledger') ?>';
                        break;
                    case 'supplier':
                        targetUrl = '<?= base_url('Reports/supplier_cash_ledger') ?>';
                        break;
                    case 'staff':
                    case 'agent':
                    case 'loader':
                        targetUrl = '<?= base_url('Reports/staffcomm_ledger') ?>';
                        break;
                    case 'general':
                        targetUrl = '<?= base_url('Reports/general_leger/1') ?>';
                        break;
                    case 'investment':
                        targetUrl = '<?= base_url('Reports/general_leger/2') ?>';
                        break;
                    case 'expense':
                        targetUrl = '<?= base_url('Reports/expense_ledger') ?>';
                        break;
                    case 'bank':
                        targetUrl = '<?= base_url('Reports/cash_ledger/2') ?>';
                        break;
                    case 'cash':
                        targetUrl = '<?= base_url('Reports/cash_ledger/1') ?>';
                        break;
                    case 'fright':
                        targetUrl = '<?= base_url('Reports/fright_ledger') ?>';
                        break;
                    default:
                        targetUrl = '<?= base_url('Reports/account_ledger') ?>';
                        break;
                }

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = targetUrl;

                const fields = {
                    account_name: accountId,
                    from_date: fromDate,
                    to_date: toDate,
                };

                Object.keys(fields).forEach(function(key) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = fields[key];
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            });
        });
    </script>

    <!--navigation Panel-->
    <nav class="py-2">
        <div class="container-fluid">
            <div class="d-flex align-items-stretch justify-content-start">
                <!-- profile -->


                <!-- <div class="d-inline-flex justify-content-start px-2 gap-1">
                    <a href="<?= base_url('Accounts/add_user/1') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'add_user') echo 'active' ?>">
                        <img src="<?= base_url('assets/icons/user.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Add User
                        </p>
                    </a>
                </div> -->

                <!--line -->
                <!-- <div style="border: 1px dashed gray; width: 1px"></div> -->
                <!--line -->

                <div class="d-inline-flex justify-content-start px-2 gap-1">
                    <a href="<?= base_url('Reports/lotwise_item') ?>" class="d-block text-dark py-1 main-link">
                        <img src="<?= base_url('assets/icons/lotwise.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Lotwise
                        </p>
                    </a>
                    <a href="<?= base_url('Sales/voucher_list') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'voucher_list') echo 'active' ?>">
                        <img src="<?= base_url('assets/icons/bill.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Voucher
                        </p>
                    </a>
                    <a href="<?= base_url('Sales/recieved_list/1') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'recieved_list' && $this->uri->segment(3) == 1) echo 'active' ?>">
                        <img src="<?= base_url('assets/icons/bill.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Receipt
                        </p>
                    </a>
                    <a href="<?= base_url('Sales/payment_list/1') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'payment_list' && $this->uri->segment(3) == 1) echo 'active' ?>">
                        <img src="<?= base_url('assets/icons/wallet.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Payment
                        </p>
                    </a>
                    <a href="<?= base_url('Sales/recieved_list/2') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'recieved_list' && $this->uri->segment(3) == 2) echo 'active' ?>">
                        <img src="<?= base_url('assets/icons/google-wallet.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Crate
                        </p>
                    </a>
                </div>

                <!--line -->
                <div style="border: 1px dashed gray; width: 1px"></div>
                <!--line -->

                <div class="d-inline-flex justify-content-start px-2 gap-1">
                    <a href="<?= base_url('Sales/add_sales') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'add_sales') echo 'active' ?>">
                        <img src="<?= base_url('assets/icons/statistics.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Add Sale
                        </p>
                    </a>
                    <a href="<?= base_url('Sales/sales_list') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'sales_list') echo 'active' ?>">
                        <img src="<?= base_url('assets/icons/proceed.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Sale List
                        </p>
                    </a>
                    <a href="<?= base_url('Sales/add_issue_item') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'add_issue_item') echo 'active' ?>">
                        <img src="<?= base_url('assets/icons/loading.png') ?>" alt="" class="w-50 mx-auto" />

                        <p class="m-0 mt-1">
                            Add Issue
                        </p>
                    </a>
                    <a href="<?= base_url('Sales/issue_item_list') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'issue_item_list') echo 'active' ?>">
                        <img src="<?= base_url('assets/icons/purchase.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Issue List
                        </p>
                    </a>
                    <?php if ($this->session->userdata('user_type') == 8) { ?>
                        <a href="<?= base_url('Sales/add_purchase') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'add_purchase') echo 'active' ?>">
                            <img src="<?= base_url('assets/icons/ledger1.png') ?>" alt="" class="w-50 mx-auto " />
                            <p class="m-0 mt-1">
                                Add Purchase
                            </p>
                        </a>
                    <?php } ?>
                    <a href="<?= base_url('Sales/purchase_list') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'purchase_list') echo 'active' ?>">
                        <img src="<?= base_url('assets/icons/plist.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Purchase List
                        </p>
                    </a>
                    <?php if ($this->session->userdata('user_type') == 8) { ?>
                        <a href="<?= base_url('Transfer/add_transfer') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'add_transfer') echo 'active' ?>">
                            <img src="<?= base_url('assets/icons/merge.png') ?>" alt="" class="w-50 mx-auto" />
                            <p class="m-0 mt-1">
                                Transfer
                            </p>
                        </a>
                    <?php } ?>
                </div>

                <!--line -->
                <div style="border: 1px dashed gray; width: 1px"></div>
                <!--line -->

                <div class="d-inline-flex justify-content-start px-2 gap-1">

                    <a href="<?= base_url('Sales/Reminder_list') ?>" class="d-block text-dark py-1 main-link">
                        <img src="<?= base_url('assets/icons/notification.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Reminder
                        </p>
                    </a>
                    <a href="<?= base_url('Reports/account_ledger') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'account_ledger') echo 'active' ?>">
                        <img src="<?= base_url('assets/icons/laser.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Ledger
                        </p>
                    </a>
                    <a href="<?= base_url('Reports/reports_list') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'reports_list') echo 'active' ?>">
                        <img src="<?= base_url('assets/icons/report.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Report
                        </p>
                    </a>
                    <a href="<?= base_url('Reports/balance_report') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'balance_report') echo 'active' ?>">
                        <img src="<?= base_url('assets/icons/accounting.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Balance
                        </p>
                    </a>
                </div>

                <!--line -->
                <div style="border: 1px dashed gray; width: 1px"></div>
                <!--line -->

                <div class="d-inline-flex justify-content-start px-2 gap-1">
                    <!-- <a href="#" class="d-block text-dark py-1 main-link">
                        <img src="<?= base_url('assets/icons/translation.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Language
                        </p>
                    </a> -->
                    <a href="<?= base_url('Welcome/db_backup') ?>" class="d-block text-dark py-1 main-link">
                        <img src="<?= base_url('assets/icons/download.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Data Backup
                        </p>
                    </a>
                    <a href="<?= base_url('Login/Logout') ?>" class="d-block text-dark py-1 main-link">
                        <img src="<?= base_url('assets/icons/logout.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Logout
                        </p>
                    </a>
                    <a href="<?= base_url() ?>" class="d-block text-dark py-1 main-link">
                        <img src="<?= base_url('assets/icons/cross.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Exit
                        </p>
                    </a>





                </div>
            </div>
        </div>
    </nav>
</header>

<?php
/**
 * Outcome of a form post that redirected back here.
 *
 * The receipt / payment / voucher forms are ordinary posts, not AJAX: they
 * throw the model's return value away and redirect, so a refused save used to
 * come back as a silent page reload that looked exactly like success. The
 * controllers now leave the reason in flashdata and it is shown here, on every
 * page that includes the shared header.
 */
$flash_error   = $this->session->flashdata('form_error');
$flash_success = $this->session->flashdata('form_success');
if (!empty($flash_error) || !empty($flash_success)) : ?>
    <script>
        // JSON_HEX_TAG so a message can never close this <script> block - these
        // strings are built from posted values (dates, ids).
        // swal lives in footer.php, which has already run by DOMContentLoaded.
        document.addEventListener('DOMContentLoaded', function() {
            swal(<?= json_encode($flash_error ?: $flash_success, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, {
                icon: <?= $flash_error ? '"error"' : '"success"' ?>,
                timer: <?= $flash_error ? 8000 : 2000 ?>,
            });
        });
    </script>
<?php endif; ?>