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
<section class="py-4 d-flex align-items-center" style="background:#f3f3ff;min-height:70vh;">
    <div class="container-fluid">

        <div class="row justify-content-evenly g-2 mb-2">
            <div class="col-12">
                <form action="<?= base_url('Accounts/custgrp_list') ?>" method="POST" class="row align-items-center">

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
                        <?php if ($this->session->userdata('user_type') == 8) { ?>
                        <button class="btn btn-info btn-sm" type="submit"><i class="bi bi-search mx-1"></i> Filter</button>
                        <a class="btn btn-danger btn-sm" href="<?= base_url('Accounts/custgrp_list') ?>"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
                        <?php } ?>
                        <!-- <a class="btn btn-primary" href="<?= base_url('Accounts/add_custgrp') ?>"><i class="bi bi-person-plus-fill"></i> Add New</a> -->

                    </div>
                </form>
            </div>
        </div>

        <div class="row justify-content-evenly g-0">
            <div class="col-12">
                <div class="table-responsive bg-light" style="height: 64vh;">
                    <table id="custgrp_tbl" class="my_custom_datatable table table-striped table-bordered dt-responsive nowra w-100">
                        <thead>
                            <tr>
                                <th>Sno</th>
                                <th>Group Name</th>
                                <th>Customer </th>
                                <th>Customer Contact</th>
                                <th>Address</th>
                                <th>Staff Name</th>
                                <th>Staff Contact</th>
                                <!-- <th>Assign Date</th>
                                <th width="10%">Action</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            if (!empty($all_value)) {

                                foreach ($all_value as $value) {
                                    $user_name = array();
                                    $user_mobile = array();
                                    $query = $this->db->select('m_user_id,m_user_name,m_user_mobile')->where("FIND_IN_SET('$value->m_group_id', m_user_group)")->get('master_users_tbl')->result();
                                //    echo '<pre>'; print_r($this->db->last_query());
                                    if (!empty($query)) {
                                        foreach ($query as $kky) {
                                            $user_name[] = $kky->m_user_name;
                                            $user_mobile[] = $kky->m_user_mobile;
                                        }
                                    }

                            ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $value->m_group_name; ?></td>
                                        <td><?php echo $value->m_cust_name; ?></td>
                                        <td><?php echo $value->m_cust_mobile; ?></td>
                                        <td><?php echo $value->m_cust_address; ?></td>
                                        <td><?php echo implode(',', $user_name); ?></td>
                                        <td><?php echo implode(',', $user_mobile); ?></td>


                                        <!-- <td class="wd-30">
                                            <div class="d-flex">
                                               
                                                <?php // if ($logged_user_type == 1 || has_perm($logged_user_id, $pageperm, $pageperm, 'Edit')) { 
                                                ?>
                                                <a href="<?php // echo base_url('Accounts/add_custgrp?id=') . $value->m_custgrp_id ; 
                                                            ?>" class="btn btn-info btn-sm p-1 me-1" title="Edit" data-toggle="tooltip"><i class="bi bi-pencil-square"></i></a>
                                                <?php //}
                                                // if ($logged_user_type == 1 || has_perm($logged_user_id, $pageperm, $pageperm, 'Delete')) { 
                                                ?>
                                                <button class="btn btn-danger btn-sm delete-custgrp p-1" data-value="<?php // echo $value->m_custgrp_id; 
                                                                                                                        ?>" title="Delete" data-toggle="tooltip"><i class="bi bi-trash"></i></button>
                                                <?php // } 
                                                ?>
                                            </div>
                                        </td> -->
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

<!---import model-->

<div id="myImportModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header justify-content-between">
                <h4 class="modal-title">Import From Excel State Data</h4>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <form method="POST" action="<?php echo site_url('Master/import_state_itemgroup') ?>" enctype="multipart/form-data">
                <div class="modal-body">

                    <p><b>Criteria</b>
                        <br>1. Your Excel data should be in the format below! The first row of your Excel needs a column header as in the example table! Also make sure your file is UTF-8 to avoid unnecessary encoding problems.

                        <br>2. If you're trying to rectangle the date column, make sure the format is formatted in Y-m-d (2021-01-01).

                        <br>3. Import only 1000 data at a time.
                    </p>
                    <hr>
                    <input type="file" name="import_file">
                </div>
                <div class="modal-footer justify-content-between">

                    <a href="<?php echo base_url('uploads/City_sample.xlsx') ?>" download class="btn btn-warning btn-sm"><i class="fa fa-download"></i> Download Sample</a>
                    <div>
                        <input type="submit" class="btn btn-success btn-sm" value="Import">
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>


                </div>
            </form>
        </div>
    </div>
</div>

<!---import model end above-->

<!-- ========================Footer================Fix======= -->
<?php $this->view('footer'); ?>
<?php $this->view('js/user_js') ?>
<?php $this->view('js/custom_js'); ?>
<!-- ========================Footer================Fix======= -->