<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>  
 
    <title><?= $pagename ?></title>

</head>

<style>
     h4 {
      font-size: 18px;
      font-weight: 600;
    }
    .bill-area { 
font-size: 13px;
    }
    @media print {
        .no-print {
            display: none !important;
        }

        .bill-area .container {
            max-width: 100%;
            margin: 0px;
            width: 100%;
            
        }
    }
    hr {
    margin: 0.2rem 0;
    color: inherit;
    border: 0;
    border-top: var(--bs-border-width) solid;
    opacity: .25;
}
</style>

<body>
    <div class="rr">
        <section class="py-1" style="background: #bbf;">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h6 class="m-0 text-white">
                            Home >> <span class="text-primary"><?= $pagename ?></span>
                        </h6>
                    </div>
                    <div class="col-6 text-end">
                    <button type="button" id="button-excel" class="btn btn-success btn-sm"> Excel Export</button>
                        <a onclick="printcustomdiv()" class="btn btn-success btn-sm">
                            <i class="bi bi-printer me-2"></i>Print
                        </a>
                        <a onclick="window.history.go(-1)" class="btn btn-danger btn-sm">
                            <i class="bi bi-box-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <section class="bill-area">
        <div class="container printDiv">
            <div class="row">
            <h3 class="text-center fw-bold"><?= get_settings('m_app_title')?></h3>
                <?= $subhead ?>
               
            </div>

            <div class="table-responsive bg-light" >
                <table id="items_tbl" class="my_custom_datatable table table-light table-bordered table-hover">
                    <thead>
                        <tr>
                            <?php if (!empty($tableheader)) {
                                foreach ($tableheader as $head) {
                                    echo '<th>' . $head . '</th>';
                                }
                            } ?>


                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        echo $Mainarray;
                        ?>
                    </tbody>
                    <tfoot>
                    <?php
                        echo $tablefoot;
                        ?>
                    </tfoot>
                </table>
            </div>



        </div>
    </section>
</body>
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

<script>

function preventBack(){window.history.forward();} 
    setTimeout("preventBack()", 0); 
    window.onunload=function(){null}; 

    function printcustomdiv() {
        printDiv = ".printDiv"; // id of the div you want to print
        $("*").addClass("no-print");
        $(printDiv + " *").removeClass("no-print");
        $(printDiv).removeClass("no-print");

        parent = $(printDiv).parent();
        while ($(parent).length) {
            $(parent).removeClass("no-print");
            parent = $(parent).parent();
        }
        window.print();

    }
</script>
<script>

  let button = document.querySelector("#button-excel");

  button.addEventListener("click", e => {

    let table = document.querySelector("#items_tbl");

    TableToExcel.convert(table);

  });

</script>
</html>