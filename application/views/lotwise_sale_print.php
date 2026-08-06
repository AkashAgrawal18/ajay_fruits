<!-- ========================Header==============Fix========= -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $pagename ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>
</head>
<section class="py-4 d-flex align-items-center" style="background:#f3f3ff;min-height:70vh;">
    <div class="container-fluid">

        <div class="row justify-content-evenly g-0">
            <div class="col-12">
                <div class="printTableDiv">
                    <table id="sales_tbl" class="table table-striped table-bordered dt-responsive nowra w-100">
                        <thead>
                            <tr>
                                <th>Sn</th>
                                <th>Date</th>
                                <th>Customer Name</th>
                                <th>Quantity</th>
                                <th>Weight (KG)</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $qtytotal = 0;
                            $wgttotal = 0;
                            $nettotal = 0;
                            if (!empty($all_value)) {
                                foreach ($all_value as $i => $value) {
                                    $qtytotal += $value->m_sale_qty;
                                    $wgttotal += $value->m_sale_weight;
                                    $nettotal += $value->m_sale_total;
                            ?><tr>
                                        <td><?= ($i + 1) ?></td>
                                        <td><?= $value->m_sale_date ?></td>
                                        <td><?= $value->m_cust_name ?></td>
                                        <td><?= $value->m_sale_qty ?></td>

                                        <td><?= $value->m_sale_weight ?></td>
                                        <td><?= money2($value->m_sale_price) ?></td>
                                        <td><?= money2($value->m_sale_total) ?></td>
                                    </tr>
                            <?php

                                }
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">Total</td>

                                <td><?= $qtytotal ?></td>

                                <td><?= $wgttotal ?></td>
                                <td></td>
                                <td><?= $nettotal ?></td>

                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ========================Footer================Fix======= -->
<script>
    window.onload = function() {
        window.print();
    };

    window.onafterprint = function() {
        window.history.back();
    };
</script>

</html>
<!-- ========================Footer================Fix======= -->