<!-- ========================Header==============Fix========= -->
<?php $this->view('head'); ?>
<?php $this->view('header'); ?>
<style>
     @media print {

.no-print {
    display: none !important;
}

.printTableDiv {
    overflow: visible !important;
    height: 100vh !important;
}


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
            <div class="col-12">

                <form action="<?= base_url('Reports/stock_list') ?>" method="POST" class="row align-items-center mb-3">

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
                        <a class="btn btn-danger" href="<?= base_url('Reports/stock_list')?>"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
                        <button class="btn btn-success btn-sm" type="button" onclick="printcustomtable()"><i class="bi bi-printer me-2"></i>Print</button>
                    </div>
                </form>


                <div class="table-responsive bg-light printTableDiv" style="height: 64vh;">
                    <table id="items_tbl" class=" table table-light table-bordered table-hover">
                        <thead style="position: sticky; top: 0; z-index: 999; background: #f3f3ff !important;">
                            <tr>
                                <th width="5%">Sno</th>
                                <th>Item Name</th>
                                <th>Group</th>
                                <th>Unit</th>
                                <th>Crate Type</th>
                                <th>Price</th>
                                <th>Available Stock Qty</th>
                                <th>Available Stock Weight</th>
                                <th>Last Update</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            if (!empty($all_value)) {
                                foreach ($all_value as $value) {

                            ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $value['m_item_name']; ?></td>
                                        <td><?php echo $value['groupname']; ?></td>
                                        <td><?php echo $value['unitname']; ?></td>
                                        <td><?php echo $value['cratetype']; ?></td>
                                        <td><?php echo $value['m_issue_price']; ?></td>
                                        <td><?php echo $value['balance_qty']; ?></td>
                                        <td><?php echo $value['balance_weight']; ?></td>
                                        <td><?php echo $value['last_updated']; ?></td>


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
<?php $this->view('js/js_master') ?>
<?php $this->view('js/custom_js'); ?>
<!-- ========================Footer================Fix======= -->