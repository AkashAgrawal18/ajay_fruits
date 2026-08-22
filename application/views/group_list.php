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
      <div class="row justify-content-evenly g-0">
         <div class="col-7">
            <!-- <form action="#" method="POST" class="row align-items-center">
               <div class="col-2">
                  <a href="#" class="btn btn-warning btn-sm w-100">
                     <i class="bi bi-receipt-cutoff me-2"></i>Lot Report
                  </a>
               </div>
               <div class="col-4">
                  <div class="input-group">
                     <input type="date" class="form-control">
                     <button class="btn btn-info">View Lot</button>
                  </div>
               </div>
               <div class="col-6">
                  <div class="input-group">
                     <input type="text" class="form-control" placeholder="Search for Items, Lots or Chalaan">
                     <button class="btn btn-info"><i class="bi bi-search mx-1"></i></button>
                  </div>
               </div>
            </form> -->

            <?php

            switch ($type) {
               case "1": {
                     $redlink = 'group_list';
                     $pgtitle = 'Group';
                  }
                  break;
               case "2": {
                     $redlink = 'expense_account_list';
                     $pgtitle = 'Expense';
                  }
                  break;
               case "3": {
                     $redlink = 'bank_account_list';
                     $pgtitle = 'Bank';
                  }
                  break;
               default: {
                     $redlink = 'cash_account_list';
                     $pgtitle = 'Cash';
                  }
            }

            ?>

            <?php if ($this->session->userdata('user_type') == 8) { ?>
            <form action="<?= site_url('Master/' . $redlink) ?>" method="POST" class="row align-items-center mb-2">
               <div class="col-3">
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
               <div class="col-3 mt-4">
                  <button class="btn btn-info btn-sm" type="submit"><i class="bi bi-search mx-1"></i> Filter</button>
                  <a class="btn btn-danger btn-sm" href="<?= site_url('Master/' . $redlink) ?>"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
               </div>
            </form>
            <?php } ?>

            <div class="table-responsive bg-light" style="height: 64vh;">
               <table id="group_tbl" class="my_custom_datatable table table-striped table-bordered dt-responsive nowra">
                  <thead>
                     <tr>
                        <th width="5%">#</th>
                        <th>Name</th>
                        <?php if ($type == 2) {
                           echo '<th>Group</th>';
                        }
                        if ($type == 1) {
                           echo '<th>Customer List</th>';
                        } else {
                           echo '<th>Number</th>
                           <th>Remark</th>';
                        } ?>
                        <th>Status</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                     $i = 1;
                     if (!empty($all_value)) {
                        foreach ($all_value as $value) {
                           $edit_link = site_url('Master/' . $redlink . '?id=') . $value->m_group_id;
                     ?>
                           <tr>
                              <td><?php echo $i; ?></td>
                              <td><?php echo $value->m_group_name; ?></td>
                              <?php
                              if ($type == 2) {
                                 echo '  <td>' . $value->expense_group . '</td>';
                              }
                              if ($type == 1) {
                                 echo '<td>
                           <a href="' . base_url('Accounts/customer_group_list/') . $value->m_group_id . '" class="btn btn-primary btn-sm btn-box btn-action" title="Customer List" data-toggle="Active">Customer List</a>
                        </td>';
                              } else {
                                 echo '  <td>' . $value->m_group_number . '</td>
                           <td>' . $value->m_group_remark . '</td>';
                              } ?>

                              <td>
                                 <?php
                                 if (!empty($value->m_group_status == 1)) {
                                 ?>
                                    <a class="btn btn-success btn-sm btn-box btn-action" title="Active" data-toggle="Active">Active</a>
                                 <?php
                                 } else {
                                 ?>
                                    <a class="btn btn-danger btn-sm btn-box btn-action" title="In-Active" data-toggle="In-Active">In-Active</a>
                                 <?php
                                 }
                                 ?>
                              </td>
                              <td title="Action" style="white-space: nowrap;">
                                 <?php // if ($logged_user_type == 1 || has_perm($logged_user_id, 'Mtr', 'group', 'Edit')) { 
                                 ?>
                                 <a href="<?php echo $edit_link; ?>" class="btn btn-success btn-sm btn-box btn-action" title="Edit" data-toggle="tooltip"><i class="bi bi-pencil-square"></i></a>
                                 <?php // }
                                 // if ($logged_user_type == 1 || has_perm($logged_user_id, 'Mtr', 'group', 'Delete')) { 
                                 ?>
                                 <button class="btn btn-danger btn-sm btn-box btn-action delete-group" data-value="<?php echo $value->m_group_id; ?>" title="Delete" data-toggle="tooltip"><i class="bi bi-trash"></i></button>
                                 <?php //} 
                                 ?>
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
         <div class="col-md-4">
            <div class="card p-3">
               <div class="row">
                  <div class="col-md-6">
                     <h5 style="margin-bottom: 10px;"><?php if (!empty($id)) {
                                                         echo 'Edit Value';
                                                      } else {
                                                         echo 'Add New';
                                                      } ?></h5>
                  </div>
                  <!-- <div class="col-md-6 text-end">
                     <?php // if ($logged_user_type == 1) {   ?>
                        <button class="btn btn-warning btn-sm custom_btn1" type="button" data-bs-toggle="modal" data-bs-target="#myImportModal" title="Excel Import"><i class="bx bx-import"></i>Import</button>
                     <?php // } ?>
                  </div> -->
               </div>


               <div class="form-example">
                  <div class="form-wrap top-label-exapmple form-layout-page">
                     <form method="post" action="#" id="frm-add-group">

                        <?php if (!empty($edit_value)) {
                           $id = $edit_value->m_group_id;
                           $title = $edit_value->m_group_name;
                           $status = $edit_value->m_group_status;
                           $grp_type = $edit_value->m_group_type;
                           $grp_group = $edit_value->m_group_group;
                           $grp_number = $edit_value->m_group_number;
                           $grp_opening = $edit_value->m_group_opening;
                           $grp_remark = $edit_value->m_group_remark;
                           $grp_branch = $edit_value->m_group_branch;
                        } else {
                           $id = '';
                           $title = '';
                           $status = 1;
                           $grp_group = '';
                           $grp_type = $type;
                           $grp_number = '';
                           $grp_opening = 0;
                           $grp_remark = '';
                           $grp_branch = '';
                        } ?>



                        <?php if ($this->session->userdata('user_type') == 8) { ?>
                        <div class="row mb-2">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <label>Branch</label>
                                 <select name="m_group_branch" id="m_group_branch" class="form-control select2">
                                    <option value="0">Head Office</option>
                                    <?php if (!empty($branch_list)) {
                                       foreach ($branch_list as $branch) {
                                          $selected = ($grp_branch == $branch->m_user_id) ? 'selected' : '';
                                    ?>
                                       <option value="<?= $branch->m_user_id ?>" <?= $selected ?>><?= $branch->m_user_name ?></option>
                                    <?php }
                                    } ?>
                                 </select>
                              </div>
                           </div>
                        </div>
                        <?php } ?>
                        <div class="row mb-2">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <label><?= $pgtitle ?> Title<span class="text-danger">*</span></label>
                                 <input type="hidden" name="m_group_type" id="m_group_type" value="<?= $grp_type ?>">
                                 <input type="hidden" name="m_group_id" id="m_group_id" value="<?= $id ?>">
                                 <input type="text" name="m_group_name" id="m_group_name" class="form-control" placeholder="Enter <?= $pgtitle ?> Title" required="" value="<?= $title ?>">
                              </div>
                           </div>
                        </div>

                        <?php if ($type == 2) { ?>
                           <div class="row mb-2">
                              <div class="col-md-12">
                                 <label for="" class="mb-0 form-label small">Group</label>
                                 <select name="m_group_group" id="m_group_group" class="form-control select2">
                                    <option value="">Select Group</option>
                                    <?php
                                    foreach ($group_dtl as $Svalue) {

                                       if (isset($grp_group) && $grp_group == $Svalue->m_group_id) {
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
                           </div>
                        <?php  }
                        if ($type != 1) { ?>
                           <div class="row mb-2">
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label><?= $pgtitle ?> Number<span class="text-danger">*</span></label>

                                    <input type="text" name="m_group_number" id="m_group_number" class="form-control" placeholder="Enter <?= $pgtitle ?> Number" value="<?= $grp_number ?>">
                                 </div>
                              </div>
                           </div>
                           <div class="row mb-2">
                              <div class="col-md-12">
                                 <label for="" class="mb-0 form-label small">Opening Balance</label>
                                 <div class="input-group mb-3">
                                    <div class="input-group-prepend">

                                       <select class="form-control dropdown-toggle-split" aria-haspopup="true" aria-expanded="false" name="m_group_optp" style="border-radius: 5px 0px 0px 5px;">
                                          <option value="2" <?php if (!empty($grp_opening) && $grp_opening > 0) echo 'selected' ?>>DR.</option>
                                          <option value="1" <?php if (!empty($grp_opening) && $grp_opening < 0) echo 'selected' ?>>CR.</option>
                                       </select>
                                    </div>
                                    <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="m_group_opening" id="m_group_opening" value="<?= abs($grp_opening) ?>" placeholder="Enter opening Balance" class="form-control" aria-label="Text input with segmented dropdown button">
                                 </div>
                              </div>
                           </div>
                           <div class="row mb-2">
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label><?= $pgtitle ?> Remark<span class="text-danger">*</span></label>

                                    <input type="text" name="m_group_remark" id="m_group_remark" class="form-control" placeholder="Enter <?= $pgtitle ?> Remark" value="<?= $grp_remark ?>">
                                 </div>
                              </div>
                           </div>
                        <?php } ?>
                        <div class="row mb-2">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <label><?= $pgtitle ?> Status</label>
                                 <select name="m_group_status" id="m_group_status" class="form-control" title="Select Status">
                                    <option value="1" <?php if ($status == 1) echo 'selected' ?>>Active</option>
                                    <option value="0" <?php if ($status == 0) echo 'selected' ?>>In-Active</option>
                                 </select>
                              </div>
                           </div>
                        </div>
                        <div class="row mb-2 mt-3">
                           <div class="col-3 me-3">
                              <div class="form-layout-submit">
                                 <button type="submit" id="btn-add-group" class="btn btn-block btn-info">Submit</button>
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-layout-submit">
                                 <a href="<?php echo site_url('Master/' . $redlink) ?>" class="btn btn-block btn-danger">Cancel </a>

                              </div>
                           </div>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>


<!-- ========================Footer================Fix======= -->
<?php $this->view('footer'); ?>
<?php $this->view('js/js_master') ?>
<?php $this->view('js/custom_js'); ?>
<!-- ========================Footer================Fix======= -->