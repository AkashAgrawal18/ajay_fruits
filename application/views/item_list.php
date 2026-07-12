<!-- ========================Header==============Fix========= -->
<?php $this->view('head'); ?>
<?php $this->view('header'); ?>

<style>
    .item-match {
        background: #fff176;
        border-radius: 2px;
        padding: 0 2px;
        font-weight: 600;
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
                                            <a href="<?php echo $edit_link; ?>" class="btn btn-success btn-action btn-sm" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                            <button class="btn btn-danger btn-action btn-sm delete-items" data-value="<?php echo $value->m_item_id; ?>" title="Delete"><i class="bi bi-trash"></i></button>
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

<!-- ========================Footer================Fix======= -->
<?php $this->view('footer'); ?>
<?php $this->view('js/js_master') ?>
<?php $this->view('js/custom_js'); ?>
<!-- ========================Footer================Fix======= -->
<script>
    $(function() {

        $('#items_tbl').tableSearch({
            inputSelector: '#item_search_input',
            minChars: 1,
            noResultMsg: 'Koi item nahi mila',
            debounce: 150,
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