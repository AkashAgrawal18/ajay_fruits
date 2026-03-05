<!-- ========== header ========== -->
<header>
    <!-- top navigation -->
    <section class="py-2" style="background: #dfdfdf;">
        <div class="container-fluid">
            <div class="d-flex gap-1 align-items-center justify-content-between">
                <div>
                    <!-- <img src="<? // base_url('uploads/') . get_settings('m_app_logo') ?>" alt="" style="height: 30px" /> -->
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
                            <li><a class="dropdown-item" href="<?= base_url('Accounts/add_user/2') ?>">Supplier Account</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Accounts/add_user/3') ?>">Loader Account</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Accounts/add_user/1') ?>">Staff Account</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Accounts/add_user/4') ?>">General Account</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Accounts/add_user/5') ?>">Investment Account</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Master/expense_account_list') ?>">Expense Account</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Master/bank_account_list') ?>">Bank Account</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Master/cash_account_list') ?>">Cash Account</a></li>

                            <li><a class="dropdown-item" href="<?= base_url('Master/group_list') ?>">Groups</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Reports/stock_list') ?>">Stock List</a></li>

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
                        </ul>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Administration
                        </button>
                        <ul class="dropdown-menu">
                             <li><a class="dropdown-item" href="<?= base_url('Sales/send_bill_indiviouly') ?>">Send Summary</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Sales/Reminder_list') ?>">Reminder List</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('Sales/purchase_item_list') ?>">Purchase Item list</a></li>
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
                <div class="d-flex">


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
                            <!-- <img src="<? // base_url('uploads/') . get_settings('m_app_logo') ?>" alt="" class="rounded-circle" style="aspect-ratio: 1/1;height:30px;"> -->
                            <div>
                                <h6 class="m-0 text-dark pe-3"><?= $login_detail->m_admin_name ?></h6>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
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
                    <a href="<?= base_url('Accounts/customer_group_list') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'customer_group_list') echo 'active' ?>">
                        <img src="<?= base_url('assets/icons/groups.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Group
                        </p>
                    </a>
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
                    <a href="<?= base_url('Sales/add_purchase') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'add_purchase') echo 'active' ?>">
                        <img src="<?= base_url('assets/icons/ledger1.png') ?>" alt="" class="w-50 mx-auto " />
                        <p class="m-0 mt-1">
                            Add Purchase
                        </p>
                    </a>
                    <a href="<?= base_url('Sales/purchase_list') ?>" class="d-block text-dark py-1 main-link <?php if ($this->uri->segment(2) == 'purchase_list') echo 'active' ?>">
                        <img src="<?= base_url('assets/icons/plist.png') ?>" alt="" class="w-50 mx-auto" />
                        <p class="m-0 mt-1">
                            Purchase List
                        </p>
                    </a>
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