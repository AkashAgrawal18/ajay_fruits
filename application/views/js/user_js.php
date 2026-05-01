<?php $logged_user_type = $this->session->userdata('user_type'); ?>
<script type="text/javascript">
  $(document).ready(function(e) {


    //============================User============================//
    $("form#frm-add-user").submit(function(e) {
      e.preventDefault();
      var clkbtn = $("#btn-add-user");
      clkbtn.prop('disabled', true);
      var formData = new FormData(this);
      var rtype = $("#m_user_type").val();
      if (rtype == 1) {
        var link = "<?= site_url('Accounts/user_list'); ?>"
      } else if (rtype == 2) {
        var link = "<?= site_url('Accounts/supplier_list'); ?>"
      } else if (rtype == 3) {
        var link = "<?= site_url('Accounts/loader_list'); ?>"
      } else if (rtype == 4) {
        var link = "<?= site_url('Accounts/general_list'); ?>"
      } else if (rtype == 5) {
        var link = "<?= site_url('Accounts/investment_list'); ?>"
      }


      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Accounts/insert_user'); ?>",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "JSON",
        success: function(data) {
          if (data.status == 'success') {
            swal(data.message, {
              icon: "success",
              timer: 1000,
            });
            setTimeout(function() {
              window.location = link;
            }, 1000);
          } else {
            clkbtn.prop('disabled', false);
            swal(data.message, {
              icon: "error",
              timer: 5000,
            });
          }
        },
        error: function(jqXHR, status, err) {
          clkbtn.prop('disabled', false);
          swal("Some Problem Occurred!! please try again", {
            icon: "error",
            timer: 2000,
          });
        }
      });

    });




    $("#user_tbl").on("click", ".delete-user", function() {
      var clkbtn = $(this);
      clkbtn.prop('disabled', true);
      var dlt_id = $(this).data('value');

      swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {

          $.ajax({
            type: "POST",
            url: "<?php echo site_url('Accounts/delete_user'); ?>",
            data: {
              delete_id: dlt_id
            },
            dataType: "JSON",
            success: function(data) {
              if (data.status == 'success') {
                swal(data.message, {
                  icon: "success",
                  timer: 1000,
                });
                setTimeout(function() {
                  location.reload();
                }, 1000);
              } else {
                clkbtn.prop('disabled', false);
                swal(data.message, {
                  icon: "error",
                  timer: 5000,
                });
              }
            },
            error: function(jqXHR, status, err) {
              clkbtn.prop('disabled', false);
              swal("Some Problem Occurred!! please try again", {
                icon: "error",
                timer: 2000,
              });
            }
          });

        } else {
          clkbtn.prop('disabled', false);
          swal("Your Data is safe!", {
            icon: "info",
            timer: 2000,
          });
        }
      });
    });

    $(document).on("change", "#m_user_login_allow", function() {
      var loginval = $(this).val();

      if (loginval == 1) {
        $('.logindtl').removeClass('d-none');
      } else {
        $('.logindtl').addClass('d-none');
      }

    });


    $("#user_tbl").on('click', '.change-status', function() {
      change_status($(this), "<?php echo site_url('User/change_user_status'); ?>");
    });


    function change_status(clkbtn, cngs_link) {
      clkbtn.prop('disabled', true);
      var cg_id = clkbtn.data('cgid'),
        cg_status = clkbtn.children('button').data('status');

      $.ajax({
        url: cngs_link,
        type: "POST",
        data: {
          cgstatus: cg_status,
          cgid: cg_id
        },
        dataType: "JSON",
        success: function(data) {
          if (data.status == 'success') {

            if (cg_status == 1) {
              clkbtn.html('<button type="button" class="btn btn-info btn-block btn-vsm" data-status="2" title="Click here to Change Status">Active</button>');
            } else {
              clkbtn.html('<button type="button" class="btn btn-danger btn-block btn-vsm" data-status="1" title="Click here to Change Status">Blocked</button>');
            }
            clkbtn.prop('disabled', false);

          } else {
            clkbtn.prop('disabled', false);
            swal(data.message, {
              icon: "error",
              timer: 2000,
            });
          }
        },
        error: function(jqXHR, status, err) {
          clkbtn.prop('disabled', false);
          swal("Some Proble Occurred!! please try again", {
            icon: "error",
            timer: 2000,
          });
        }
      });

    }

    $("form#frm-add-cust").submit(function(e) {
      e.preventDefault();
      var clkbtn = $("#btn-add-cust");
      clkbtn.prop('disabled', true);
      var formData = new FormData(this);
      // var rtype = $("#m_cust_type").val();
      // if(rtype == 2){ var link ="<?= site_url('Accounts/cust_list'); ?>" } else if (rtype == 3) { var link = "<?= site_url('cust/consignee_list'); ?>" } else { var link = "<?= site_url('cust/serviceProvider_list'); ?>" }
      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Accounts/insert_cust'); ?>",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "JSON",
        success: function(data) {
          if (data.status == 'success') {
            swal(data.message, {
              icon: "success",
              timer: 1000,
            });
            setTimeout(function() {
              window.location = "<?= site_url('Accounts/add_cust'); ?>";
            }, 1000);
          } else {
            clkbtn.prop('disabled', false);
            swal(data.message, {
              icon: "error",
              timer: 5000,
            });
          }
        },
        error: function(jqXHR, status, err) {
          clkbtn.prop('disabled', false);
          swal("Some Problem Occurred!! please try again", {
            icon: "error",
            timer: 2000,
          });
        }
      });

    });

    $("#cust_tbl").on("click", ".delete-cust", function() {
      var clkbtn = $(this);
      clkbtn.prop('disabled', true);
      var dlt_id = $(this).data('value');

      swal({
        title: "Are you sure?",
        text: "Once deleted, All data like sales, cash/crate recived also delete and you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {

          $.ajax({
            type: "POST",
            url: "<?php echo site_url('Accounts/delete_customer'); ?>",
            data: {
              delete_id: dlt_id
            },
            dataType: "JSON",
            success: function(data) {
              if (data.status == 'success') {
                swal(data.message, {
                  icon: "success",
                  timer: 1000,
                });
                setTimeout(function() {
                  location.reload();
                }, 1000);
              } else {
                clkbtn.prop('disabled', false);
                swal(data.message, {
                  icon: "error",
                  timer: 5000,
                });
              }
            },
            error: function(jqXHR, status, err) {
              clkbtn.prop('disabled', false);
              swal("Some Problem Occurred!! please try again", {
                icon: "error",
                timer: 2000,
              });
            }
          });

        } else {
          clkbtn.prop('disabled', false);
          swal("Your Data is safe!", {
            icon: "info",
            timer: 2000,
          });
        }
      });
    });

    //===========================/User============================//

    //===========================/custgrp============================//
    $("form#frm-add-custgrp").submit(function(e) {
      e.preventDefault();
      var clkbtn = $("#btn-add-custgrp");
      clkbtn.prop('disabled', true);
      var formData = new FormData(this);

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Accounts/insert_custgrp'); ?>",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "JSON",
        success: function(data) {
          if (data.status == 'success') {
            swal(data.message, {
              icon: "success",
              timer: 1000,
            });
            setTimeout(function() {
              window.location = "<?= site_url('Accounts/custgrp_list'); ?>";
            }, 1000);
          } else {
            clkbtn.prop('disabled', false);
            swal(data.message, {
              icon: "error",
              timer: 5000,
            });
          }
        },
        error: function(jqXHR, status, err) {
          clkbtn.prop('disabled', false);
          swal("Some Problem Occurred!! please try again", {
            icon: "error",
            timer: 2000,
          });
        }
      });

    });

    $("#custgrp_tbl").on("click", ".delete-custgrp", function() {
      var clkbtn = $(this);
      clkbtn.prop('disabled', true);
      var dlt_id = $(this).data('value');

      swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {

          $.ajax({
            type: "POST",
            url: "<?php echo site_url('Accounts/delete_custgrp'); ?>",
            data: {
              delete_id: dlt_id
            },
            dataType: "JSON",
            success: function(data) {
              if (data.status == 'success') {
                swal(data.message, {
                  icon: "success",
                  timer: 1000,
                });
                setTimeout(function() {
                  location.reload();
                }, 1000);
              } else {
                clkbtn.prop('disabled', false);
                swal(data.message, {
                  icon: "error",
                  timer: 5000,
                });
              }
            },
            error: function(jqXHR, status, err) {
              clkbtn.prop('disabled', false);
              swal("Some Problem Occurred!! please try again", {
                icon: "error",
                timer: 2000,
              });
            }
          });

        } else {
          clkbtn.prop('disabled', false);
          swal("Your Data is safe!", {
            icon: "info",
            timer: 2000,
          });
        }
      });
    });

    //===========================/custgrp============================//
    //===========================/reminder list============================//

    $('#all_checked').on('click', function() {
      if ($(this).is(':checked') == true) {
        $('.cust_idscls').prop('checked', true);
      } else {
        $('.cust_idscls').prop('checked', false);
      }
    });

    $("form#reminder_form").submit(function(e) {
      e.preventDefault();
      var clkbtn = $("#reminder_btn_submit");
      clkbtn.prop('disabled', true);
      var formData = new FormData(this);

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Sales/send_reminder_msg'); ?>",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "JSON",
        success: function(data) {
          if (data.status == 'success') {
            swal(data.message, {
              icon: "success",
              timer: 1000,
            });
            setTimeout(function() {
              location.reload();
            }, 1000);
          } else {
            clkbtn.prop('disabled', false);
            swal(data.message, {
              icon: "error",
              timer: 5000,
            });
          }
        },
        error: function(jqXHR, status, err) {
          clkbtn.prop('disabled', false);
          swal("Some Problem Occurred!! please try again", {
            icon: "error",
            timer: 2000,
          });
        }
      });

    });

    $("form#frm-send-summary").submit(function(e) {
      e.preventDefault();
      var clkbtn = $("#btn-send-summary");
      clkbtn.prop('disabled', true);
      var formData = new FormData(this);

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Sales/send_bill'); ?>",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "JSON",
        success: function(data) {
          if (data.status == 'success') {
            swal(data.message, {
              icon: "success",
              timer: 1000,
            });
            setTimeout(function() {
              location.reload();
            }, 1000);
          } else {
            clkbtn.prop('disabled', false);
            swal(data.message, {
              icon: "error",
              timer: 5000,
            });
          }
        },
        error: function(jqXHR, status, err) {
          clkbtn.prop('disabled', false);
          swal("Some Problem Occurred!! please try again", {
            icon: "error",
            timer: 2000,
          });
        }
      });

    });

    $("form#frm-send-statement").submit(function(e) {
      e.preventDefault();
      var clkbtn = $("#btn-send-statement");
      clkbtn.prop('disabled', true);
      var formData = new FormData(this);

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Sales/send_statement'); ?>",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "JSON",
        success: function(data) {
          if (data.status == 'success') {
            swal(data.message, {
              icon: "success",
              timer: 1000,
            });
            setTimeout(function() {
              location.reload();
            }, 1000);
          } else {
            clkbtn.prop('disabled', false);
            swal(data.message, {
              icon: "error",
              timer: 5000,
            });
          }
        },
        error: function(jqXHR, status, err) {
          clkbtn.prop('disabled', false);
          swal("Some Problem Occurred!! please try again", {
            icon: "error",
            timer: 2000,
          });
        }
      });

    });
    //===========================/reminder list============================//
  });
</script>