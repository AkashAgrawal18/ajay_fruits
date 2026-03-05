<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $pagename ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>
  <style>
    .afc_account {
      padding: 10px 20px;
    }

    body,
    p,
    li,
    td {
      font-size: 14px;
      font-weight: 600 !important;
      font-family: sans-serif;
    }

    h4 {
      font-size: 14px;
      font-weight: 600;
    }

    h1 {
      font-size: 20px;
    }

    h3 {
      font-size: 22px;
    }

    h2 {
      font-size: 16px;
    }

    th,
    td {
      padding: 3px 10px !important;
      font-weight: 600 !important;
    }

    @media print {
      .no-print {
        display: none !important;
      }

    }
  </style>
</head>

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
            <form action="<?= base_url('Reports/' . $pagelink) ?>" method="post">
              <?php if ($exporttype == 2) { ?>
                <input type="hidden" name="account_name" value="<?= $account_name ?>">
                <input type="hidden" name="from_date" value="<?= $from_date ?>">
                <input type="hidden" name="to_date" value="<?= $todate ?>">
                <input type="hidden" name="orderby" value="<?= $orderby ?>">
                <button type="submit" class="btn btn-success btn-sm"> Excel Export</button>

              <?php  } else {
                echo '<button type="button" id="button-excel" class="btn btn-success btn-sm"> Excel Export</button>';
              } ?>

              <a onclick="printcustomdiv()" class="btn btn-success btn-sm">
                <i class="bi bi-printer me-2"></i>Print
              </a>
              <a onclick="window.history.go(-1)" class="btn btn-danger btn-sm">
                <i class="bi bi-box-arrow-left me-2"></i>Back
              </a>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>

  <div class="printDiv">
    <section class="afc_account">
      <div class="text-center mb-1">
        <h1 class="m-0"><strong>AK</strong></h1>
        <h2><strong>|| Shri Ganeshay Namah ||</strong></h2>
        <h3><strong> AJAY KUSHWAHA & COMPANY</strong></h3>
        <h4 class="fst-italic m-0">
          <strong>Commission Agent & Order Supplier</strong>
        </h4>
        <p class="m-0">
          Depo1: Anjora-Thanod, Durg(C.G), PNT Colony, Ranal Kamptee Nagpur (MH)
          <br />
          Mob: 9225233301, 8329044323 (Ajay), 9273444495 (Ashish), 8999039535
          (Santosh)
        </p>
      </div>
      <div class="container-fluid">
        <div class="row">
          <?= $subhead ?>
        </div>
        <div class="row">
          <div class="table-responsive">
            <table id="table_data" class="table table-bordered fw-bold">
              <thead>
                <?= $tableheader ?>
              </thead>
              <tbody>
                <?= $Mainarray ?>

              </tbody>
              <tfoot>
                <?= isset($tablefoot) ? $tablefoot : "" ?>
              </tfoot>

            </table>
          </div>
        </div>

        <?= $mainfooter ?>


      </div>

    </section>
  </div>
</body>
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

<script>
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

  let button = document.querySelector("#button-excel");

  button.addEventListener("click", e => {

    let table = document.querySelector("#table_data");

    TableToExcel.convert(table);

  });

  // Function to calculate column totals
  // function calculateColumnTotal(colIndex) {
  //   var table = document.getElementById("table_data");
  //   var rows = table.rows;
  //   var total = 0;

  //   for (var i = 1; i < rows.length; i++) {
  //     var cellValue = parseInt(rows[i].cells[colIndex].innerHTML, 10);
  //     total += isNaN(cellValue) ? 0 : cellValue;
  //   }

  //   return total;
  // }

  // var column1Total = calculateColumnTotal(0);
  // console.log("Total for Column 1: " + column1Total);
</script>

</html>