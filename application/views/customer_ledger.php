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
<section class="py-4 d-flex " style="background:#f3f3ff;min-height:70vh;">
    <div class="container-fluid">

        <!-- <div class="row justify-content-evenly g-2 mb-2">
            <div class="col-12">
                <form action="<?= base_url('Accounts/cust_list') ?>" method="POST" class="row align-items-center">

                    <div class="col-2">
                        <div class="form-group">
                            <label for="">From date</label>
                            <input type="date" name="from_date" id="from_date" class="form-control" value="<?= $from_date ?>">
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="">To date</label>
                            <input type="date" name="to_date" id="to_date" class="form-control" value="<?= $to_date ?>">
                        </div>
                    </div>
                    <div class="col-4 mt-4">
                        <button class="btn btn-info" type="submit"><i class="bi bi-search mx-1"></i> Search</button>
                        <a class="btn btn-danger" href="<?= base_url('Accounts/cust_list') ?>"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
                        <a class="btn btn-primary" href="<?= base_url('Accounts/add_cust') ?>"><i class="bi bi-person-plus-fill"></i> Add New</a>

                    </div>
                </form>
            </div>
        </div> -->

        <div class="row g-2">
            <div class="col-6">
                <div class="card p-3">
                    <h5>Customer Details</h5>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <ul>
                                <li> Name : <?= $cust_detail->m_cust_name ?> </li>
                                <li> Mobile : <?= $cust_detail->m_cust_mobile ?> </li>
                                <li> State : <?= $cust_detail->m_state_name ?> </li>
                                <li> City : <?= $cust_detail->m_city_name ?> </li>
                                <li> Address : <?= $cust_detail->m_cust_address ?> </li>
                                <li> Remark : <?= $cust_detail->m_cust_remark ?> </li>

                            </ul>
                        </div>
                        <div class="col-6">
                            <ul>
                                <li> Account No : <?= $cust_detail->m_cust_accountno ?> </li>
                                <li> Adhar No : <?= $cust_detail->m_cust_adharno ?> </li>
                                <li> Pan No : <?= $cust_detail->m_cust_pan_no ?> </li>
                                <li> Group : <?= $cust_detail->m_group_name ?> </li>
                                <li> Contract Person : <?= $cust_detail->m_cust_contractPerd ?> </li>
                                <li> Trademark : <?= $cust_detail->m_cust_trademark ?> </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card p-3">
                    <h5>Customer Balance</h5>
                    <hr>
                    <ul>
                        <li> Total Given : ₹<?= $cust_balance['grand_total'] ?> </li>
                        <li>Total Recieved : ₹<?= $cust_balance['amount_rcvd'] ?> </li>
                        <li> Total Balance : ₹<?= ($cust_balance['grand_total'] - $cust_balance['amount_rcvd']) ?> </li>
                    </ul>
                </div>
            </div>
            <div class="col-3">
                <div class="card p-3">
                    <h5>Customer Crate Balance</h5>
                    <hr>
                    <ul>
                        <?php if (!empty($crate_balance)) {
                               echo '<li> Type : Given : Recived : Balance </li>';
                            foreach ($crate_balance as $key) {
                                echo '<li> ' . $key['name'] . ' : ' . $key['given'] . ' : '. $key['recived'] . ': ' . $key['balance'] . ' </li>';
                            
                            }
                        } ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row g-2 mt-4">
            <div class="col-12">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Amount Ledger</button>
                        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Crate Ledger</button>

                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="table-responsive bg-light" style="height: 64vh;">
                            <table id="sales_tbl" class="my_custom_datatable table table-striped table-bordered dt-responsive nowra w-100">
                                <thead>
                                    <tr>
                                        <th>Sno</th>
                                        <th>Date</th>
                                        <th>Staff Name</th>
                                        <th>Staff Contact</th>
                                        <th>Remark</th>
                                        <th>Amount Given</th>
                                        <th>Amount Recived</th>
                                        <th>Amount Balance</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    if (!empty($amount_ledger)) {
                                        $balace = 0;
                                        foreach ($amount_ledger as $value) {
                                            if ($value->type == 1) {
                                                $given = $value->Tamount;
                                                $recived = 0;
                                                $balace += $value->Tamount;
                                            } else {
                                                $recived = $value->Tamount;
                                                $given = 0;
                                                $balace -= $value->Tamount;
                                            }



                                    ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo date('d-m-Y', strtotime($value->m_sale_date)); ?></td>
                                                <td><?php echo $value->m_user_name ?: 'Admin'; ?></td>
                                                <td><?php echo $value->m_user_mobile ?: ''; ?></td>
                                                <td><?php echo $value->m_sale_note; ?></td>
                                                <td><?php echo $given; ?></td>
                                                <td><?php echo $recived; ?></td>
                                                <td><?php echo $balace; ?></td>

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
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <div class="table-responsive bg-light" style="height: 64vh;">
                            <table id="sales_tbl" class="my_custom_datatable table table-striped table-bordered dt-responsive nowra w-100">
                                <thead>
                                    <tr>
                                        <th>Sno</th>
                                        <th>Date</th>
                                        <th>Staff Name</th>
                                        <th>Staff Contact</th>
                                        <th>Remark</th>
                                        <th>Crate Type</th>
                                        <th>Amount Given</th>
                                        <th>Amount Recived</th>
                                        <th>Amount Balance</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    if (!empty($crate_ledger)) {
                                        $bbalace = 0;
                                        foreach ($crate_ledger as $value) {
                                            if ($value->type == 1) {
                                                $ggiven = $value->tcrateqty;
                                                $rrecived = 0;
                                                $bbalace += $value->tcrateqty;
                                            } else {
                                                $rrecived = $value->tcrateqty;
                                                $ggiven = 0;
                                                $bbalace -= $value->tcrateqty;
                                            }



                                    ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo date('d-m-Y', strtotime($value->m_sale_date)); ?></td>
                                                <td><?php echo $value->m_user_name ?: 'Admin'; ?></td>
                                                <td><?php echo $value->m_user_mobile ?: ''; ?></td>
                                                <td><?php echo $value->m_sale_note; ?></td>
                                                <td><?php echo $value->m_itgrp_title; ?></td>
                                                <td><?php echo $ggiven; ?></td>
                                                <td><?php echo $rrecived; ?></td>
                                                <td><?php echo $bbalace; ?></td>

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
        </div>


    </div>
</section>
<!-- ========== Page Content ========== -->



<!-- ========================Footer================Fix======= -->
<?php $this->view('footer'); ?>
<?php $this->view('js/user_js') ?>
<?php $this->view('js/custom_js'); ?>
<!-- ========================Footer================Fix======= -->