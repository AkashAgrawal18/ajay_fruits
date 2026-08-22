<!-- ========================Header==============Fix========= -->
<?php $this->view('head'); ?>
<?php $this->view('header'); ?>

<?php
// if($type == 1){
//    $paglink = 'Item_group';
//    $pgname = 'Group';
// }else  if($type == 2){
//    $paglink = 'Item_unit';
//    $pgname = 'Unit';
// }else if($type == 3){
//    $paglink = 'Item_crate';
//    $pgname = 'Crate';
// }
?>
<!-- ========== Page Content ========== -->
<style>
    @media print {
        .no-print {
            display: none !important;
        }

        .printTableDiv {
            overflow: visible !important;
            height: 100vh !important;
        }

        .actionth {
            display: none;
        }
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
                <form action="<?= base_url('Accounts/cust_list') ?>" method="POST" class="row align-items-center">

                    <div class="col-2">
                        <div class="form-group">
                            <label for="">From date</label>
                            <input type="date" max="<?= date('Y-m-d')?>" name="from_date" id="from_date" class="form-control" value="<?= $from_date ?>">
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="">To date</label>
                            <input type="date" max="<?= date('Y-m-d')?>" name="to_date" id="to_date" class="form-control" value="<?= $to_date ?>">
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
                    <div class="col-2">
                        <div class="form-group">
                            <label for="">Search</label>
                            <input type="text" name="search_in" id="search_in" class="form-control" placeholder="Enter Name,mobile,group..." title="Enter Name/mobile/group to search" value="<?= $search_in ?>">
                        </div>
                    </div>
                    <div class="col-4 mt-4">
                        <button class="btn btn-info" type="submit"><i class="bi bi-search mx-1"></i> Search</button>
                        <a class="btn btn-danger" href="<?= base_url('Accounts/cust_list') ?>"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
                        <a class="btn btn-primary" href="<?= base_url('Accounts/add_cust' . (!empty($branch_id) ? '?branch_id=' . $branch_id : '')) ?>"><i class="bi bi-person-plus-fill"></i> Add New</a>
                        <button class="btn btn-success" type="button" onclick="printcustomtable()"><i class="bi bi-printer me-2"></i>Print</button>

                    </div>
                </form>
            </div>
        </div>

        <div class="row justify-content-evenly g-0">
            <div class="col-12">


                <div class="table-responsive bg-light printTableDiv" style="height: 64vh;">
                    <table id="cust_tbl" class="table table-striped table-bordered dt-responsive nowra w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Mobile No.</th>
                                <th>City/State</th>
                                <th>Address</th>
                                <th>Group Name</th>
                                <th>Current Balance</th>
                                <th>Crate Balance <br>(10kg | 20kg | 25kg)</th>
                                <th width="7%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            if (!empty($mech_value)) {
                                foreach ($mech_value as $value) {
                                    $original_date = $value->m_cust_added_on;
                                    $new_date = date("d-m-Y", strtotime($original_date));
                            ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $value->m_cust_name . '<br>' . $value->m_cust_hndiname; ?></td>
                                        <td><?php echo $value->m_cust_mobile; ?></td>
                                        <td><?php echo $value->m_city_name . '-' . $value->m_state_name; ?></td>
                                        <td><?php echo $value->m_cust_address; ?></td>
                                        <td><?php echo $value->m_group_name; ?></td>
                                        <th>₹<?php echo money2($value->m_cust_balance); ?></th>
                                        <th><?php echo $value->m_cust_10bal .' | '. $value->m_cust_20bal .' | '. $value->m_cust_25bal; ?></th>
                                       

                                        <td class="wd-30">
                                            <div class="d-flex">
                                                <a href="<?php echo base_url('Accounts/add_cust?id=') . $value->m_cust_id . '&type=' . $type; ?>" class="btn btn-info btn-sm p-1 me-1" title="Edit" data-toggle="tooltip"><i class="bi bi-pencil-square"></i></a>
                                                <button class="btn btn-danger btn-sm delete-cust p-1" data-value="<?php echo $value->m_cust_id; ?>" title="Delete" data-toggle="tooltip"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
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
<?php $this->view('footer'); ?>
<?php $this->view('js/user_js') ?>
<?php $this->view('js/custom_js'); ?>

<!-- ========================Footer================Fix======= -->