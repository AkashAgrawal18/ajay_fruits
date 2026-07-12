<?php include("head.php"); ?>
<?php include("header.php"); ?>
<!-- ========== Page Content ========== -->
<?php
$paglink = '';
$pgname = '';
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

<section class="py-1" style="background: #bbf;">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-6">
                <h6 class="m-0 text-white">
                    Home >> <span class="text-primary"><?= $pagename . ' ' . $pgname ?></span>
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
            $id = $edit_value->m_user_id;
            $name = $edit_value->m_user_name;
            $mobile = $edit_value->m_user_mobile;
            $phoneno = $edit_value->m_user_phoneno;
            $remark = $edit_value->m_user_remark;
            $contractPerd = $edit_value->m_user_contractPerd;
            $pan_no = $edit_value->m_user_pan_no;
            $accountno = $edit_value->m_user_accountno;
            $adharno = $edit_value->m_user_adharno;
            $design = $edit_value->m_user_design;
            $state = $edit_value->m_user_state;
            $city = $edit_value->m_user_city;
            $address = $edit_value->m_user_address;
            $trademark = $edit_value->m_user_trademark;
            $pgtype = $edit_value->m_user_type;
            $group = explode(',', $edit_value->m_user_group);
            $login_allow = $edit_value->m_user_login_allow;
            $loginid = $edit_value->m_user_loginid;
            $password = $edit_value->m_user_password;
            $opening = $edit_value->m_user_opening;
            $crateOP = explode(',', $edit_value->m_user_crateOP);
        } else {
            $id = '';
            $name = '';
            $mobile = '';
            $phoneno = '';
            $remark = '';
            $contractPerd = '';
            $pan_no = '';
            $accountno = '';
            $adharno = '';
            $design = '';
            $state = '';
            $city = '';
            $address = '';
            $trademark = '';
            $group = '';
            $password = '';
            $loginid = '';
            $login_allow = 1;
            $opening = 0;
            $crateOP = array(0, 0, 0);
        }

        $cv10 = isset($crateOP[0]) ? $crateOP[0] : 0;
        $cv20 = isset($crateOP[1]) ? $crateOP[1] : 0;
        $cv25 = isset($crateOP[2]) ? $crateOP[2] : 0;
        ?>

        <form class="row justify-content-evenly g-4" id="frm-add-user" action="#" method="POST">
            <div class="col-4">
                <div class="row g-4">
                    <div class="col-12">
                        <label for="" class="mb-0 form-label small">Account Name <span class="text-danger">*</span></label>
                        <input type="hidden" name="m_user_id" id="m_user_id" value="<?= $id ?>">
                        <input type="hidden" name="m_user_type" id="m_user_type" value="<?= $pgtype ?>">
                        <input type="text" name="m_user_name" id="m_user_name" value="<?= $name ?>" class="form-control" placeholder="Enter Account Name" required>
                    </div>
                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">Contact Person</label>
                        <input type="text" name="m_user_contractPerd" id="m_user_contractPerd" value="<?= $contractPerd ?>" class="form-control" placeholder="Harsh Agrawal">
                    </div>
                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">Mobile Number <span class="text-danger">*</span></label>
                        <input type="tel" maxlength="10" minlength="10" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" class="form-control" name="m_user_mobile" id="m_user_mobile" value="<?= $mobile ?>" placeholder="1234512345" required>
                    </div>
                    <?php if ($pgtype == 1) { ?>
                        <div class="col-6">
                            <label for="" class="mb-0 form-label small">Group</label>
                            <select name="m_user_group[]" id="group" class="form-control select2" multiple>
                                <option value="">Select Group</option>
                                <?php
                                $j = 0;
                                foreach ($group_dtl as $Svalue) {
                                    $option1 = "";

                                    if ($group[$j] == $Svalue->m_group_id) {
                                        $option1 = "selected";
                                        $j++;
                                    }

                                ?>
                                    <option value="<?php echo $Svalue->m_group_id; ?>" <?= $option1 ?>><?= $Svalue->m_group_name; ?>
                                    </option>
                                <?php
                                }

                                ?>

                            </select>
                        </div>
                        <div class="col-6">
                            <label for="" class="mb-0 form-label small">Designation</label>
                            <select name="m_user_design" id="design" class="form-control select2">
                                <option value="1" <?= $design == 1 ? 'selected' : '' ?>>Sale Agent</option>
                                <option value="2" <?= $design == 2 ? 'selected' : '' ?>>Manager</option>

                            </select>
                        </div>
                    <?php } else if ($pgtype != 9) { ?>
                        <div class="col-4">
                            <label for="" class="mb-0 form-label small">10kg Crate Balance</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <select class="form-control dropdown-toggle-split" aria-haspopup="true" aria-expanded="false" name="ct10" style="border-radius: 5px 0px 0px 5px;">
                                        <option value="2" <?php if ($cv10 > 0) echo 'selected' ?>>DR.</option>
                                        <option value="1" <?php if ($cv10 < 0) echo 'selected' ?>>CR.</option>
                                    </select>
                                </div>
                                <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="cbv10" id="cbv10" value="<?= abs((int)$cv10) ?>" placeholder="10kg Balance" class="form-control" aria-label="Text input with segmented dropdown button">
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
                                <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="cbv20" id="cbv20" value="<?= abs((int)$cv20) ?>" placeholder="20kg Balance" class="form-control" aria-label="Text input with segmented dropdown button">
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
                                <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="cbv25" id="cbv25" value="<?= abs((int)$cv25) ?>" placeholder="25kg Balance" class="form-control" aria-label="Text input with segmented dropdown button">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">Area/City</label>
                        <select name="m_user_city" id="city" class="form-control select2">
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
                        <select name="m_user_state" id="state" class="form-control select2">
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
                        <textarea rows="3" class="form-control" name="m_user_address" id="m_user_address" value="<?= $address ?>" placeholder="enter full address"><?= $address ?></textarea>
                    </div>


                </div>
            </div>
            <div class="col-4">
                <div class="row g-4">
                    <?php if ($pgtype == 1 || $pgtype == 9) { ?>
                        <div class="col-6">
                            <label for="" class="mb-0 form-label small">Login Allow</label>
                            <select name="m_user_login_allow" id="m_user_login_allow" class="form-control">
                                <option value="1" <?php if ($login_allow == 1) {
                                                        echo 'selected';
                                                    } ?>>YES</option>
                                <option value="0" <?php if ($login_allow == 0) {
                                                        echo 'selected';
                                                    } ?>>NO</option>
                            </select>
                        </div>

                        <div class="col-6 logindtl <?php if ($login_allow == 0) {
                                                        echo 'd-none';
                                                    } ?>">
                            <label for="" class="mb-0 form-label small">Login Id</label>
                            <?php if ($pgtype == 9) { ?>
                                <input type="text" class="form-control" name="m_user_loginid" id="m_user_loginid" value="<?= $loginid ?>">
                            <?php } else { ?>
                                <input type="tel" maxlength="10" minlength="10" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" class="form-control" name="m_user_loginid" id="m_user_loginid" value="<?= $loginid ?>">
                            <?php } ?>
                        </div>

                        <div class="col-6 logindtl <?php if ($login_allow == 0) {
                                                        echo 'd-none';
                                                    } ?>">
                            <label for="" class="mb-0 form-label small">Password</label>
                            <input type="text" minlength="6" class="form-control" name="m_user_password" id="m_user_password" value="<?= $password ?>">
                        </div>

                    <?php } ?>
                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">Opening Cash Balance</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">

                                <select class="form-control dropdown-toggle-split" aria-haspopup="true" aria-expanded="false" name="m_user_typeopening" style="border-radius: 5px 0px 0px 5px;">
                                    <option value="2" <?php if (!empty($opening) && $opening > 0) echo 'selected' ?>>DR.</option>
                                    <option value="1" <?php if (!empty($opening) && $opening < 0) echo 'selected' ?>>CR.</option>
                                </select>

                            </div>
                            <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="m_user_opening" id="m_user_opening" value="<?= abs($opening) ?>" placeholder="Enter opening Balance" class="form-control" aria-label="Text input with segmented dropdown button">
                        </div>
                    </div>

                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">Trade Mark</label>
                        <input type="text" class="form-control" name="m_user_trademark" id="m_user_trademark" value="<?= $trademark ?>" placeholder="if any">
                    </div>
                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">Account number</label>
                        <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" class="form-control" name="m_user_accountno" id="m_user_accountno" value="<?= $accountno ?>" placeholder="123412341234">
                    </div>
                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">Pan Number</label>
                        <input type="tel" maxlength="10" minlength="10" class="form-control" name="m_user_pan_no" id="m_user_pan_no" value="<?= $pan_no ?>" placeholder="123412341234">
                    </div>
                    <div class="col-6">
                        <label for="" class="mb-0 form-label small">Aadhar number</label>
                        <input type="tel" maxlength="12" minlength="12" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" class="form-control" name="m_user_adharno" id="m_user_adharno" value="<?= $adharno ?>" placeholder="123412341234">
                    </div>
                    <div class="col-12">
                        <label for="" class="mb-0 form-label small">Remarks</label>
                        <textarea rows="3" class="form-control" name="m_user_remark" id="m_user_remark" placeholder="if any"><?= $remark ?></textarea>
                    </div>

                    <!-- <div class="col-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked">
                            <label class="form-check-label small" for="flexSwitchCheckChecked">Print in English
                                Only</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked">
                            <label class="form-check-label small" for="flexSwitchCheckChecked">Special Expense</label>
                        </div>
                    </div> -->

                </div>
            </div>
            <div class="col-3 text-center">
                <img src="<?= base_url('assets/imgs/addUser.svg') ?>" alt="" class="w-100">
                <div class="row pt-3">
                    <div class="col-6">
                        <a href="<?= base_url('Accounts/') . $paglink ?>" class="btn btn-primary btn-md w-100 mb-3"><?= $pgname ?> List</a>
                    </div>
                    <div class="col-6">
                        <button type="reset" class="btn btn-danger btn-md w-100 mb-3">Clear</button>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success btn-lg w-100" id="btn-add-user">Save Details</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- ========== Page Content ========== -->
<?php include("footer.php"); ?>
<?php $this->view('js/user_js') ?>