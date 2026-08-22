<!-- ========================Header==============Fix========= -->
<?php $this->view('head'); ?>
<?php $this->view('header'); ?>

<?php if ($type == 1) {
   $paglink = 'Item_group';
   $pgname = 'Group';
} else  if ($type == 2) {
   $paglink = 'Item_unit';
   $pgname = 'Unit';
} else if ($type == 3) {
   $paglink = 'Item_crate';
   $pgname = 'Crate';
} ?>
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
            <?php if ($this->session->userdata('user_type') == 8) { ?>
            <form action="<?= site_url('Master/') . $paglink ?>" method="POST" class="row align-items-center mb-2">
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
                  <a class="btn btn-danger btn-sm" href="<?= site_url('Master/') . $paglink ?>"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
               </div>
            </form>
            <?php } ?>
            <div class="table-responsive bg-light" style="height: 64vh;">
               <table id="itemgroup_tbl" class="my_custom_datatable table table-light table-bordered table-hover">
                  <thead>
                     <tr>
                        <th width="5%">#</th>
                        <th>Name</th>
                        <!-- <th>Status</th> -->
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                     $i = 1;
                     if (!empty($all_value)) {
                        foreach ($all_value as $value) {
                           $edit_link = site_url('Master/' . $paglink . '/') . $value->m_itgrp_id;
                     ?>
                           <tr>
                              <td><?php echo $i; ?></td>
                              <td><?php echo $value->m_itgrp_title; ?></td>
                              <!-- <td>
                                 <?php
                                 if (!empty($value->m_itgrp_status == 1)) {
                                 ?>
                                    <a class="btn btn-success btn-sm btn-box btn-action" title="Active" data-toggle="Active">Active</a>
                                 <?php
                                 } else {
                                 ?>
                                    <a class="btn btn-danger btn-sm btn-box btn-action" title="In-Active" data-toggle="In-Active">In-Active</a>
                                 <?php
                                 }
                                 ?>
                              </td> -->
                              <td title="Action" style="white-space: nowrap;">
                                 <?php // if ($logged_user_type == 1 || has_perm($logged_user_id, 'Mtr','city', 'Edit')) { 
                                 ?>
                                 <a href="<?php echo $edit_link; ?>" class="btn btn-success btn-action btn-sm" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                 <?php // }if ($logged_user_type == 1 || has_perm($logged_user_id, 'Mtr','city', 'Delete')) { 
                                 ?>
                                 <button class="btn btn-danger btn-action btn-sm delete-itemgroup" data-value="<?php echo $value->m_itgrp_id; ?>" title="Delete"><i class="bi bi-trash"></i></button>
                                 <?php // } 
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
         <div class="col-4">
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
                        <?php // if ($logged_user_type == 1 || has_perm($logged_user_id, 'Mtr','city', 'Add')) { 
                        ?>
                           <button class="btn btn-warning btn-sm custom_btn1" type="button" data-bs-toggle="modal" data-bs-target="#myImportModal" title="Excel Import"><i class="bx bx-import"></i>Import</button>
                        <?php // } 
                        ?>
                     </div> -->
               </div>
               <div class="form-example">
                  <div class="form-wrap top-label-exapmple form-layout-page">
                     <form method="post" action="#" id="frm-add-itemgroup">

                        <?php if (!empty($edit_value)) {
                           $id = $edit_value->m_itgrp_id;
                           $title = $edit_value->m_itgrp_title;
                           $type = $edit_value->m_itgrp_type;
                           $itgrp_branch = $edit_value->m_itgrp_branch;
                           // $status = $edit_value->m_itgrp_status;
                        } else {
                           $id = '';
                           $title = '';
                           $itgrp_branch = '';
                           // $status = 1;
                        } ?>


                        <div class="row mb-1">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <label><?= $pgname ?> Name <span class="text-danger">*</span></label>
                                 <input type="hidden" name="m_itgrp_id" id="m_itgrp_id" value="<?= $id ?>">
                                 <input type="hidden" name="m_itgrp_type" id="m_itgrp_type" value="<?= $type ?>">
                                 <input type="text" name="m_itgrp_title" id="m_itgrp_title" class="form-control" placeholder="Enter <?= $pgname ?> Title" required="" value="<?= $title ?>">
                              </div>
                           </div>
                        </div>

                        <?php if ($this->session->userdata('user_type') == 8) { ?>
                        <div class="row mb-1">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <label>Branch</label>
                                 <select name="m_itgrp_branch" id="m_itgrp_branch" class="form-control select2">
                                    <option value="0">Head Office</option>
                                    <?php if (!empty($branch_list)) {
                                       foreach ($branch_list as $branch) {
                                          $selected = ($itgrp_branch == $branch->m_user_id) ? 'selected' : '';
                                    ?>
                                       <option value="<?= $branch->m_user_id ?>" <?= $selected ?>><?= $branch->m_user_name ?></option>
                                    <?php }
                                    } ?>
                                 </select>
                              </div>
                           </div>
                        </div>
                        <?php } ?>

                        <!-- <div class="row mb-1">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <label>Status</label>
                                 <select name="m_itgrp_status" id="m_itgrp_status" class="form-control" title="Select Status">
                                    <option value="1" <?php if ($status == 1) echo 'selected' ?>>Active</option>
                                    <option value="0" <?php if ($status == 0) echo 'selected' ?>>In-Active</option>
                                 </select>
                              </div>
                           </div>
                        </div> -->
                        <div class="row mb-1 mt-3">
                           <div class="col-3 me-3">
                              <div class="form-layout-submit">
                                 <button type="submit" id="btn-add-itemgroup" class="btn btn-block btn-info">Submit</button>
                              </div>
                           </div>
                           <div class="col-3">
                              <div class="form-layout-submit">
                                 <a href="<?php echo site_url('Master/') . $paglink ?>" class="btn btn-block btn-danger">Cancel </a>

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
<?php $this->view('js/js_master') ?>
<?php $this->view('js/custom_js'); ?>
<!-- ========================Footer================Fix======= -->