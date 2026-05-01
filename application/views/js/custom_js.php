<script type="text/javascript">
$(document).ready(function () {

    function performAjax(date, password = '') {
        return $.ajax({
            url: "<?= base_url('Login/change_date') ?>",
            type: "POST",
            data: { date, password },
            dataType: "json"
        });
    }

    function clearField($field) {
        $field.val('');
    }

    $(document).on('blur', 'input[type="date"]', function () {
        const $field       = $(this);
        const selectedDate = $field.val();

        if (!selectedDate) return; // skip if empty

        performAjax(selectedDate)
            .done(function (response) {
                if (response.status === 'password_required') {
                    const userPassword = prompt(response.message || "Enter password to proceed:");

                    if (!userPassword) {
                        clearField($field);
                        return;
                    }

                    performAjax(selectedDate, userPassword)
                        .done(function (res) {
                            if (res.status === 'error') clearField($field);
                        })
                        .fail(function () { clearField($field); });

                } else if (response.status === 'error') {
                    clearField($field);
                }
            })
            .fail(function () { clearField($field); });
    });

});


  /* var cls_table = $(".dash_datatable").DataTable({

    'order': [
      [0, "asc"]
    ],

    'paging': true,

    'pageLength': 5,

    'pagingType': "numbers",

    'language': {

      searchPlaceholder: 'Search...',

      sSearch: ''

    }

  });


  var cls_table = $(".my_custom_datatable").DataTable({

    'order': [
      [0, "asc"]
    ],

    'paging': true,

    'pageLength': 50,

    'pagingType': "numbers",

    'language': {

      searchPlaceholder: 'Search...',

      sSearch: ''

    }

  });



  $(".my_custom_datatable").each(function(i, element) {
    //    new $.fn.dataTable.Buttons(cls_table.eq(i), {
    //      buttons: [{
    //          extend: "excel",
    //          className: "datatable-btn btn-sm"
    //        },
    //        {
    //          extend: "pdf",
    //          className: "datatable-btn btn-sm"
    //        },
    //        {
    //          extend: "print",
    //          className: "datatable-btn btn-sm"
    //        }
    //      ]
    //    });

    cls_table.eq(i).buttons().container().appendTo(

      $('.col-sm-6:eq(0)',

        cls_table.eq(i).table().container())

    );

  }); */


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

  function printcustomtable() {
    printDiv = ".printTableDiv"; // id of the div you want to print
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