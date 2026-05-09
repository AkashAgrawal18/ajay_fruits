<!-- ========================Header==============Fix========= -->
<?php $this->view('head'); ?>
<?php $this->view('header'); ?>

<style>
    .item-match {
    background : #fff176;
    border-radius : 2px;
    padding : 0 2px;
    font-weight : 600;
}
</style>

<!-- ========== Page Content ========== -->
<section class="py-1" style="background: #bbf;">
    <div class="container-fluid">
        <div class="row align-item-center">
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
<section class="py-4 d-flex align-item-center" style="background:#f3f3ff;min-height:70vh;">
    <div class="container-fluid">
        <div class="row justify-content-evenly g-0">
            <div class="col-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="input-group input-group-sm" style="max-width:320px;">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" id="item_search_input" class="form-control border-start-0"
                            placeholder="Search item, code, unit…">
                    </div>
                    <small class="text-muted" id="item_search_count"></small>
                </div>
                <div class="table-responsive bg-light" style="height: 64vh;">
                    <table id="items_tbl" class="my_customize_datatable table table-light table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="5%">Code</th>
                                <th>Item Name</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Crate Type</th>
                                <th>Fright Rate</th>
                                <th>Commission Rate</th>
                                <!-- <th>Status</th> -->
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            if (!empty($all_value)) {
                                foreach ($all_value as $value) {
                                    $edit_link = site_url('Master/item_list/') . $value->m_item_id;
                            ?>
                                    <tr>
                                        <td><?php echo $value->m_item_id; ?></td>
                                        <td><?php echo $value->m_item_name; ?></td>
                                        <td><?php echo $value->unitname; ?></td>
                                        <td><?php echo $value->m_item_price; ?></td>
                                        <td><?php echo $value->cratetype; ?></td>
                                        <td><?php echo $value->m_item_fright; ?></td>
                                        <td><?php echo $value->m_item_comm; ?></td>

                                        <td title="Action" style="white-space: nowrap;">
                                            <?php // if ($logged_user_type == 1 || has_perm($logged_user_id, 'Mtr','city', 'Edit')) { 
                                            ?>
                                            <a href="<?php echo $edit_link; ?>" class="btn btn-success btn-action btn-sm" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                            <?php // }if ($logged_user_type == 1 || has_perm($logged_user_id, 'Mtr','city', 'Delete')) { 
                                            ?>
                                            <button class="btn btn-danger btn-action btn-sm delete-items" data-value="<?php echo $value->m_item_id; ?>" title="Delete"><i class="bi bi-trash"></i></button>
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
                            <form method="post" action="#" id="frm-add-items">

                                <?php if (!empty($edit_value)) {
                                    $id = $edit_value->m_item_id;
                                    $title = $edit_value->m_item_name;
                                    $igroup = $edit_value->m_item_group;
                                    $icrate = $edit_value->m_item_crate;
                                    $iunit = $edit_value->m_item_unit;
                                    $iprice = $edit_value->m_item_price;
                                    $ifright = $edit_value->m_item_fright;
                                    $comm = $edit_value->m_item_comm;
                                } else {
                                    $id = '';
                                    $title = '';
                                    $igroup = '';
                                    $icrate = '';
                                    $iunit = '';
                                    $iprice = '';
                                    $ifright = '';
                                    $comm = '';
                                } ?>


                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label> Item Name<span class="text-danger">*</span></label>
                                            <input type="hidden" name="m_item_id" id="m_item_id" value="<?= $id ?>">
                                            <input type="text" name="m_item_name" id="m_item_name" class="form-control" placeholder="Enter Item Title" required="" value="<?= $title ?>">
                                        </div>
                                    </div>
                                </div>


                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label> Item Group </label>
                                            <select name="m_item_group" id="m_item_group" class="form-control select2">
                                                <option value="">-- Select Group --</option>
                                                <?php if (!empty($group_lst)) {
                                                    foreach ($group_lst as $grp) {
                                                        if ($igroup == $grp->m_itgrp_id) {
                                                            $op = "selected";
                                                        } else {
                                                            $op = '';
                                                        }
                                                        echo '<option value="' . $grp->m_itgrp_id . '" ' . $op . '>' . $grp->m_itgrp_title . '</option>';
                                                    }
                                                } ?>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label> Item Unit </label>
                                            <select name="m_item_unit" id="m_item_unit" class="form-control select2">
                                                <option value="">-- Select Unit --</option>
                                                <?php if (!empty($unit_lst)) {
                                                    foreach ($unit_lst as $grp) {
                                                        if ($iunit == $grp->m_itgrp_id) {
                                                            $op = "selected";
                                                        } else {
                                                            $op = '';
                                                        }
                                                        echo '<option value="' . $grp->m_itgrp_id . '" ' . $op . '>' . $grp->m_itgrp_title . '</option>';
                                                    }
                                                } ?>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label> Item Crate </label>
                                            <select name="m_item_crate" id="m_item_crate" class="form-control select2">
                                                <option value="">-- Select Crate --</option>
                                                <?php if (!empty($crate_lst)) {
                                                    foreach ($crate_lst as $grp) {
                                                        if ($icrate == $grp->m_itgrp_id) {
                                                            $op = "selected";
                                                        } else {
                                                            $op = '';
                                                        }
                                                        echo '<option value="' . $grp->m_itgrp_id . '" ' . $op . '>' . $grp->m_itgrp_title . '</option>';
                                                    }
                                                } ?>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Fright Rate</label>
                                            <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="m_item_fright" id="m_item_fright" class="form-control" placeholder="Enter Fright Rate" value="<?= $ifright ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Commission Rate</label>
                                            <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="m_item_comm" id="m_item_comm" class="form-control" placeholder="Enter Commission" value="<?= $comm ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label> Item Price</label>
                                            <input type="tel" onkeypress="return (event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46)" name="m_item_price" id="m_item_price" class="form-control" placeholder="Enter Item Price" value="<?= $iprice ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="row mb-1">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="m_item_status" id="m_item_status" class="form-control" title="Select Status">
                                                <option value="1" <?php if ($status == 1) echo 'selected' ?>>Active</option>
                                                <option value="0" <?php if ($status == 0) echo 'selected' ?>>In-Active</option>
                                            </select>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="row mb-1 mt-3">
                                    <div class="col-3 me-3">
                                        <div class="form-layout-submit">
                                            <button type="submit" id="btn-add-items" class="btn btn-block btn-info">Submit</button>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-layout-submit">
                                            <a href="<?php echo site_url('Master/item_list') ?>" class="btn btn-block btn-danger">Cancel </a>

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
            <form method="POST" action="<?php echo site_url('Master/import_state_items') ?>" enctype="multipart/form-data">
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
<script>
    $(function() {

        $('#items_tbl').tableSearch({
            inputSelector: '#item_search_input',

            // Sirf in columns me search hoga: 0=Code, 1=Name, 2=Unit, 3=Price
            // Sab columns me search chahiye to yeh line hata do
            columns: [0, 1, 2, 3],

            minChars: 1, // 1 char type karte hi search shuru
            noResultMsg: 'Koi item nahi mila',
            debounce: 150, // ms

            highlight: true, // matched text highlight hoga
            highlightClass: 'item-match',

            onResult: function(visible, total) {
                var $el = $('#item_search_count');
                if (visible === total) {
                    $el.text('');
                } else {
                    $el.text(visible + ' of ' + total + ' items');
                }
            }
        });

    });
</script>