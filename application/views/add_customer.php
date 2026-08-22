<?php include("head.php"); ?>
<?php include("header.php"); ?>
<!-- ========== Page Content ========== -->
<section class="py-1" style="background: #bbf;">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-6">
                <h6 class="m-0 text-white">
                    Home >> <span class="text-primary">Add Customer</span>
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
<section class="py-5 d-flex align-items-center" style="background:#f3f3ff;min-height:75vh;">
    <div class="container-fluid">

        <?php if (!empty($edit_value)) {
            $id = $edit_value->m_cust_id;
            $name = $edit_value->m_cust_name;
            $hndiname = $edit_value->m_cust_hndiname;
            $mobile = $edit_value->m_cust_mobile;
            $phoneno = $edit_value->m_cust_phoneno;
            $remark = $edit_value->m_cust_remark;
            $contractPerd = $edit_value->m_cust_contractPerd;
            // $pan_no = $edit_value->m_cust_pan_no;
            $accountno = $edit_value->m_cust_accountno;
            // $adharno = $edit_value->m_cust_adharno;
            $group = $edit_value->m_cust_group;
            $opening = $edit_value->m_cust_opening;
            $crateOP = explode(',', $edit_value->m_cust_crateOP);
            $state = $edit_value->m_cust_state;
            $city = $edit_value->m_cust_city;
            $address = $edit_value->m_cust_address;
            $trademark = $edit_value->m_cust_trademark;
            $loginid = $edit_value->m_cust_loginid;
            $password = '';
            $cust_branch = $edit_value->m_cust_branch;
        } else {
            $id = '';
            $name = '';
            $hndiname = '';
            $mobile = '';
            $phoneno = '';
            $remark = '';
            $contractPerd = '';
            // $pan_no = '';
            $accountno = '';
            // $adharno = '';
            $group = '';
            $opening = 0;
            $crateOP = array(0, 0, 0);
            $state = '';
            $city = '';
            $address = '';
            $trademark = '';
            $loginid = '';
            $password = '';
            $cust_branch = !empty($branch_id) ? $branch_id : '';
        }
        $cv10 = isset($crateOP[0]) ? $crateOP[0] : 0;
        $cv20 = isset($crateOP[1]) ? $crateOP[1] : 0;
        $cv25 = isset($crateOP[2]) ? $crateOP[2] : 0; ?>

        <form class="row justify-content-evenly g-4" id="frm-add-cust" action="#" method="POST">
            <div class="col-4">
                <div class="row g-4">
                    <div class="col-12">
                        <label for="" class="mb-0 form-label small">Account Name <span class="text-danger">*</span></label>
                        <input type="hidden" name="m_cust_id" id="m_cust_id" value="<?= $id ?>">
                        <input type="text" name="m_cust_name" id="m_cust_name" value="<?= $name ?>" class="form-control" placeholder="Enter Account Name" required>
                    </div>
                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">Contact Person</label>
                        <input type="text" name="m_cust_contractPerd" id="m_cust_contractPerd" value="<?= $contractPerd ?>" class="form-control" placeholder="Harsh Agrawal">
                    </div>
                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">Mobile Number <span class="text-danger">*</span></label>
                        <input type="tel" maxlength="10" minlength="10" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" class="form-control" name="m_cust_mobile" id="m_cust_mobile" value="<?= $mobile ?>" placeholder="1234512345" required>
                    </div>
                    <div class="col-4">
                        <label for="" class="mb-0 form-label small">10kg Crate Balance</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">

                                <select class="form-control dropdown-toggle-split" aria-haspopup="true" aria-expanded="false" name="ct10" style="border-radius: 5px 0px 0px 5px;">
                                    <option value="2" <?php if ($cv10 > 0) echo 'selected' ?>>DR.</option>
                                    <option value="1" <?php if ($cv10 < 0) echo 'selected' ?>>CR.</option>
                                </select>

                            </div>
                            <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="cbv10" id="cbv10" value="<?= abs($cv10) ?>" placeholder="10kg Balance" class="form-control" aria-label="Text input with segmented dropdown button">
                        </div>

                    </div>
                    <div class="col-4">
                        <label for="" class="mb-0 form-label small">20kg Crate Balance</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">

                                <select class="form-control dropdown-toggle-split" aria-haspopup="true" aria-expanded="false" name="ct20" style="border-radius: 5px 0px 0px 5px;">
                                    <option value="2" <?php if ($cv20 > 0) echo 'selected' ?>>DR.</option>
                                    <option value="1" <?php if ($cv20 < 0) echo 'selected' ?>>CR.</option>
                                </select>

                            </div>
                            <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="cbv20" id="cbv20" value="<?= abs($cv20) ?>" placeholder="20kg Balance" class="form-control" aria-label="Text input with segmented dropdown button">
                        </div>
                    </div>
                    <div class="col-4">
                        <label for="" class="mb-0 form-label small">25kg Crate Balance</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">

                                <select class="form-control dropdown-toggle-split" aria-haspopup="true" aria-expanded="false" name="ct25" style="border-radius: 5px 0px 0px 5px;">
                                    <option value="2" <?php if ($cv25 > 0) echo 'selected' ?>>DR.</option>
                                    <option value="1" <?php if ($cv25 < 0) echo 'selected' ?>>CR.</option>
                                </select>

                            </div>
                            <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="cbv25" id="cbv25" value="<?= abs($cv25) ?>" placeholder="25kg Balance" class="form-control" aria-label="Text input with segmented dropdown button">
                        </div>
                    </div>
                    <?php if ($this->session->userdata('user_type') == 8) { ?>
                        <div class="col-6">
                            <label for="" class="mb-0 form-label small">Branch</label>
                            <select name="m_cust_branch" id="m_cust_branch" class="form-control select2">
                                <option value="0">Head Office</option>
                                <?php foreach ($branch_list as $branch) { ?>
                                    <option value="<?= $branch->m_user_id ?>" <?= $cust_branch == $branch->m_user_id ? 'selected' : '' ?>><?= $branch->m_user_name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>
                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">Area/City</label>
                        <select name="m_cust_city" id="city" class="form-control select2">
                            <option value="">Select City</option>
                            <?php
                            foreach ($city_dtl as $city_value) {

                                if ($city == $city_value->m_city_id) {
                                    $option1 = "selected";
                                } else {
                                    $option1 = "";
                                }

                            ?>
                                <option value="<?php echo $city_value->m_city_id; ?>" <?= $option1 ?>><?php echo $city_value->m_city_name ?>
                                </option>
                            <?php
                            }

                            ?>

                        </select>
                    </div>
                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">State</label>
                        <select name="m_cust_state" id="state" class="form-control select2">
                            <option value="">Select State</option>
                            <?php
                            foreach ($state_dtl as $Svalue) {

                                if ($state == $Svalue->m_state_id) {
                                    $option1 = "selected";
                                } else {
                                    $option1 = "";
                                }

                            ?>
                                <option value="<?php echo $Svalue->m_state_id; ?>" <?= $option1 ?>><?= $Svalue->m_state_name; ?>
                                </option>
                            <?php
                            }

                            ?>

                        </select>
                    </div>

                    <div class="col-12">
                        <label for="" class="mb-0 form-label small">Address</label>
                        <textarea rows="3" class="form-control" name="m_cust_address" id="m_cust_address" value="<?= $address ?>" placeholder="enter full address"><?= $address ?></textarea>
                    </div>

                </div>
            </div>
            <div class="col-4">
                <div class="row g-4">
                    <div class="col-12">
                        <label for="" class="mb-0 form-label small">Account Name (hindi)</label>
                        <input type="text" name="m_cust_hndiname" id="m_cust_hndiname" value="<?= $hndiname ?>" class="form-control" placeholder="Enter Name in hindi">
                    </div>
                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">Opening Cash Balance</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">

                                <select class="form-control dropdown-toggle-split" aria-haspopup="true" aria-expanded="false" name="m_cust_typeopening" style="border-radius: 5px 0px 0px 5px;">
                                    <option value="2" <?php if (!empty($opening) && $opening > 0) echo 'selected' ?>>DR.</option>
                                    <option value="1" <?php if (!empty($opening) && $opening < 0) echo 'selected' ?>>CR.</option>
                                </select>

                            </div>
                            <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="m_cust_opening" id="m_cust_opening" value="<?= abs($opening) ?>" placeholder="Enter Cash Balance" class="form-control" aria-label="Text input with segmented dropdown button">
                        </div>

                    </div>
                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">Group</label>
                        <select name="m_cust_group" id="group" class="form-control select2">
                            <option value="">Select Group</option>
                            <?php
                            foreach ($group_dtl as $Svalue) {

                                if ($group == $Svalue->m_group_id) {
                                    $option1 = "selected";
                                } else {
                                    $option1 = "";
                                }

                            ?>
                                <option value="<?php echo $Svalue->m_group_id; ?>" <?= $option1 ?>><?= $Svalue->m_group_name; ?>
                                </option>
                            <?php
                            }

                            ?>

                        </select>
                    </div>
                    <div class="col-12">
                        <label for="" class="mb-0 form-label small">Remarks</label>
                        <textarea rows="3" class="form-control" name="m_cust_remark" id="m_cust_remark" placeholder="if any"><?= $remark ?></textarea>
                    </div>
                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">Trade Mark</label>
                        <input type="text" class="form-control" name="m_cust_trademark" id="m_cust_trademark" value="<?= $trademark ?>" placeholder="if any">
                    </div>
                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">Account number</label>
                        <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" class="form-control" name="m_cust_accountno" id="m_cust_accountno" value="<?= $accountno ?>" placeholder="123412341234">
                    </div>
                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">Login Id</label>
                        <input type="tel" maxlength="10" minlength="10" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" class="form-control" name="m_cust_loginid" id="m_cust_loginid" value="<?= $loginid ?>">
                    </div>

                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">Password</label>
                        <input type="text" minlength="6" class="form-control" name="m_cust_password" id="m_cust_password" value="<?= $password ?>" placeholder="<?= !empty($id) ? 'Leave blank to keep current password' : '' ?>">
                    </div>


                    <div class="col-12 d-flex justify-content-between">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked">
                            <label class="form-check-label small" for="flexSwitchCheckChecked">Print in English
                                Only</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked">
                            <label class="form-check-label small" for="flexSwitchCheckChecked">Special Expense</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3 text-center">
                <img src="<?= base_url('assets/imgs/addUser.svg') ?>" alt="" class="w-100">
                <div class="row pt-3">
                    <div class="col-6">
                        <a href="<?= base_url('Accounts/cust_list') ?>" class="btn btn-primary btn-md w-100 mb-3">Customer List</a>
                    </div>
                    <div class="col-6">
                        <button type="reset" class="btn btn-danger btn-md w-100 mb-3">Clear</button>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success btn-lg w-100" id="btn-add-cust">Save Details</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- ============================================= Modal content-================================================ -->
<div id="myImportModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header justify-content-between">
                <h4 class="modal-title">Import From Excel Customer Data</h4>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <form method="POST" action="<?php echo site_url('Master/import_customer') ?>" enctype="multipart/form-data">
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


<!-- ========== Page Content ========== -->
<?php include("footer.php"); ?>
<?php $this->view('js/user_js') ?>