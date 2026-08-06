<!-- ========================Header==============Fix========= -->
<?php $this->view('head'); ?>
<?php $this->view('header'); ?>

<?php
if ($pgtype == 1) {
    $paglink = 'user_list';
    $pgname = 'Staff';
} else  if ($pgtype == 2) {
    $paglink = 'supplier_list';
    $pgname = 'Supplier';
} else if ($pgtype == 3) {
    $paglink = 'loader_list';
    $pgname = 'Loader';
} else if ($pgtype == 4) {
    $paglink = 'general_list';
    $pgname = 'General';
} else if ($pgtype == 5) {
    $paglink = 'investment_list';
    $pgname = 'Investment';
} else if ($pgtype == 9) {
    $paglink = 'branch_list';
    $pgname = 'Branch';
}
?>
<!-- ========== Page Content ========== -->
<section class="py-1" style="background: #bbf;">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-6">
                <h6 class="m-0 text-white">
                    Home >> <span class="text-primary"><?= $pgname ?> List</span>
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
                <form action="<?= base_url('Accounts/') . $paglink ?>" method="POST" class="row align-items-center">

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
                    <div class="col-2">
                        <div class="form-group">
                            <label for="">Search</label>
                            <input type="text" name="search_in" id="search_in" class="form-control" placeholder="Enter Name,mobile,city..." title="Enter Name/mobile/city to search" value="<?= $search_in ?>">
                        </div>
                    </div>
                    <?php if ($this->session->userdata('user_type') == 8) { ?>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="">Branch</label>
                            <select name="branch_id" id="branch_id" class="form-select select2">
                                <option value="">All Branches</option>
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
                        <button class="btn btn-info" type="submit"><i class="bi bi-search mx-1"></i> Search</button>
                        <a class="btn btn-danger" href="<?= base_url('Accounts/') . $paglink ?>"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
                        <a class="btn btn-primary" href="<?= base_url('Accounts/add_user?pgtype=' . $pgtype . (!empty($branch_id) ? '&branch_id=' . $branch_id : '')) ?>"><i class="bi bi-person-plus-fill"></i> Add New</a>

                    </div>
                </form>
            </div>
        </div>

        <div class="row justify-content-evenly g-0">
            <div class="col-12">
                <div class="table-responsive bg-light" style="height: 64vh;">
                    <table id="user_tbl" class="my_custom_datatable table table-striped table-bordered dt-responsive nowra w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Mobile No.</th>
                                <th>City/State</th>
                                <th>Address</th>
                                <th>Trade Mark</th>
                                <th>Contact Person</th>
                                <th>Balance</th>
                                <?php if ($pgtype == 1) { ?>
                                <th>Group name</th>
                                <?php } ?>
                                <th>Joining Date</th>
                                <th>Status</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            if (!empty($mech_value)) {
                                foreach ($mech_value as $value) {
                                    $original_date = $value->m_user_added_on;
                                    $new_date = date("d-m-Y", strtotime($original_date));
                                    $query = $this->db->select('GROUP_CONCAT(m_group_name) as group_name')->where_in('m_group_id', explode(',', $value->m_user_group))->get('master_group_tbl')->result();
                            ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $value->m_user_name; ?></td>
                                        <td><?php echo $value->m_user_mobile; ?></td>
                                        <td><?php echo $value->m_city_name . '-' . $value->m_state_name; ?></td>
                                        <td><?php echo $value->m_user_address; ?></td>
                                        <td><?php echo $value->m_user_trademark; ?></td>
                                        <td><?php echo $value->m_user_contractPerd; ?></td>
                                        <td><?php echo money2($value->m_user_balance); ?></td>
                                        <?php if ($pgtype == 1) { ?>
                                            <td><?php echo $query[0]->group_name; ?></td>
                                        <?php } ?>

                                        <td><?php echo $new_date; ?></td>
                                        <td><?php if ($value->m_user_status == 1) echo "Active";
                                            else {
                                                echo "In-Active";
                                            } ?></td>

                                        <td class="wd-30">
                                            <div class="d-flex">
                                                <a href="<?php echo base_url('Accounts/add_user?type=' . $pgtype . '&id=') . $value->m_user_id; ?>" class="btn btn-info btn-sm p-1 me-1" title="Edit" data-toggle="tooltip"><i class="bi bi-pencil-square"></i></a>
                                                <?php if (($pgtype == 1 || $pgtype == 9) && $this->session->userdata('user_type') == 8) { ?>
                                                <button class="btn btn-secondary btn-sm view-password p-1 me-1" data-value="<?php echo $value->m_user_id; ?>" title="View Password" data-toggle="tooltip"><i class="bi bi-eye"></i></button>
                                                <?php } ?>
                                                 <button class="btn btn-danger btn-sm delete-user p-1" data-value="<?php echo $value->m_user_id; ?>" title="Delete" data-toggle="tooltip"><i class="bi bi-trash"></i></button>
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