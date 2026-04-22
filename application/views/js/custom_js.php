<script type="text/javascript">
 $(document).ready(function() {
  // $('input[type="date"]').on('blur', function() {
  //   let selectedfiled = $(this);
  //   let selectedDate = $(this).val();
    
  //   // Function to perform AJAX request
  //   function performAjax(date, password = '') {
  //     return $.ajax({
  //       url: "<?= base_url('Login/change_date') ?>",
  //       type: "POST",
  //       data: {
  //         date: date,
  //         password: password
  //       },
  //       dataType: "json"
  //     });
  //   }

  //   // First AJAX call to check if password is required
  //   performAjax(selectedDate).done(function(response) {
  //     if (response.status === 'password_required') {
  //       let userPassword = prompt("Enter password to proceed:");
        
  //       if (userPassword) {
  //         // If password is entered, make second AJAX call
  //         performAjax(selectedDate, userPassword).done(function(res) {
  //           if (res.status == "error") {
  //             $(selectedfiled).val('');
  //           }
  //         }).fail(function() {
  //           $(selectedfiled).val('');
  //         });
  //       } else {
  //         $(selectedfiled).val('');
  //       }
  //     }
  //   }).fail(function() {
  //     $(selectedfiled).val('');
  //   });
  // });
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