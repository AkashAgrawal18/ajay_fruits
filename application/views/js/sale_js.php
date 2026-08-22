<?php $logged_user_type = $this->session->userdata('user_type'); ?>
<script type="text/javascript">
  $(document).ready(function(e) {

    //===========================/item_issue============================//
    $("form#frm-add-item_issue").submit(function(e) {
      e.preventDefault();
      var clkbtn = $("#btn-add-item_issue");
      clkbtn.prop('disabled', true);
      var formData = new FormData(this);

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Sales/insert_issue_item'); ?>",
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
              window.location = "<?= site_url('Sales/issue_item_list'); ?>";
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

    $("#item_issue_tbl").on("click", ".delete-item_issue", function() {
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
            url: "<?php echo site_url('Sales/delete_issue_item'); ?>",
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

    $(".del-item_issue-id").on("click", function() {
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
            url: "<?php echo site_url('Sales/delete_issue_item_id'); ?>",
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

    //===========================/item_issue============================//

    //===========================/sales============================//
    $("form#frm-add-sales").submit(function(e) {
      e.preventDefault();
      var clkbtn = $("#btn-add-sales");
      clkbtn.prop('disabled', true);
      var formData = new FormData(this);

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Sales/insert_sales'); ?>",
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
              window.location = "<?= site_url('Sales/sales_list'); ?>";
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

    $("#sales_tbl").on("click", ".delete-sales", function() {
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
            url: "<?php echo site_url('Sales/delete_sales'); ?>",
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


    $(".del-sales-id").on("click", function() {
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
            url: "<?php echo site_url('Sales/delete_sales_id'); ?>",
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

    //===========================/sales============================//
    //===========================/recied/payment============================//


    $("#recieved_tbl").on("click", ".delete-revied", function() {
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
            url: "<?php echo site_url('Sales/delete_recieved_data'); ?>",
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

    $("#payment_tbl").on("click", ".delete-payment", function() {
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
            url: "<?php echo site_url('Sales/delete_payment_data'); ?>",
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

    $("#voucher_tbl").on("click", ".delete-voucher", function() {
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
            url: "<?php echo site_url('Sales/delete_voucher_data'); ?>",
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

    //===========================/recied/payment============================//

    //===========================/purchase============================//
    $("form#frm-add-purchase").submit(function(e) {
      e.preventDefault();
      var clkbtn = $("#btn-add-purchase");
      clkbtn.prop('disabled', true);
      var formData = new FormData(this);

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Sales/insert_purchase'); ?>",
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
              window.location = "<?= site_url('Sales/purchase_list'); ?>";
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

    $("#purchase_tbl").on("click", ".delete-purchase", function() {
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
            url: "<?php echo site_url('Sales/delete_purchase'); ?>",
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

    $(".del-purchase-id").on("click", function() {
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
            url: "<?php echo site_url('Sales/delete_purchase_id'); ?>",
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

    //===========================/purchase============================//


    $('#m_payment_account').on('change', function() {
      get_payaccout_list($(this).val(),'')
    });
    
    $(document).on('click', '.myeditpayModal', function(e) {
      var vall = $(this).data('value');
      var act_type = $('#m_payment_account' + vall).val();
      // alert(vall)
      get_payaccout_list(act_type, vall);
    });

    $('#m_recvd_account').on('change', function() {
      get_accout_list($(this).val(), '');
    });

    $(document).on('click', '.myeditModal', function(e) {
      var valu = $(this).data('value');
      var act_type = $('#m_recvd_account' + valu).val();
      get_accout_list(act_type, valu);
    });


  });

  function get_payaccout_list(acct_type, filed_id) {
    if (acct_type == 1) {
      $('#selet_label').html('Supplier Name <span class="text-danger">*</span>');
      $('#m_supplier_list' + filed_id).empty().html(`
        <option value="">--Select--</option>
                                    <?php
                                    if (!empty($supplier_list)) {
                                      foreach ($supplier_list as $vat) {

                                    ?>
                                            <option value="<?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?>" data-name="<?= $vat->m_user_name ?>" data-balance="<?= money2($vat->m_user_balance) ?>" data-id="<?= $vat->m_user_id ?>" data-mobile="<?= $vat->m_user_mobile ?>"><?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?></option>
                                    <?php
                                      }
                                    }

                                        ?>
        `);

    } else if (acct_type == 2) {
      $('#selet_label').html('Expense Name <span class="text-danger">*</span>');
      $('#m_supplier_list' + filed_id).empty().html(`
        <option value="">--Select--</option>
                                    <?php
                                    if (!empty($expense_lst)) {
                                      foreach ($expense_lst as $vat) {

                                    ?>
                                             <option value="<?php echo $vat->m_group_name; ?>" data-name="<?= $vat->m_group_name ?>" data-id="<?= $vat->m_group_id ?>" data-balance="0" data-mobile=""><?= $vat->m_group_name; ?></option>
                                        <?php
                                      }
                                    }

                                        ?>
        `);
    } else if (acct_type == 7) {
      $('#selet_label').html('Bank Name <span class="text-danger">*</span>');
      $('#m_supplier_list' + filed_id).empty().html(`
        <option value="">--Select--</option>
                                    <?php
                                    if (!empty($paymode_lst)) {
                                      foreach ($paymode_lst as $vat) {
                                        if ($vat->m_group_type == 3) {
                                    ?>
                                            <option value="<?php echo $vat->m_group_name; ?>" data-name="<?= $vat->m_group_name ?>" data-id="<?= $vat->m_group_id ?>" data-balance="0" data-mobile=""><?= $vat->m_group_name; ?></option>
                                       <?php
                                        }
                                      }
                                    }

                                        ?>
        `);

    } else if (acct_type == 3) {
      $('#selet_label').html('Loader Name <span class="text-danger">*</span>');
      $('#m_supplier_list' + filed_id).empty().html(`
        <option value="">--Select--</option>
                                    <?php
                                    if (!empty($loader_list)) {
                                      foreach ($loader_list as $vat) {

                                    ?>
                                              <option value="<?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?>" data-name="<?= $vat->m_user_name ?>" data-balance="<?= money2($vat->m_user_balance) ?>" data-id="<?= $vat->m_user_id ?>" data-mobile="<?= $vat->m_user_mobile ?>"><?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?></option>
                                      <?php
                                      }
                                    }

                                        ?>

        `);

    } else if (acct_type == 4) {
      $('#selet_label').html('Staff Name <span class="text-danger">*</span>');
      $('#m_supplier_list' + filed_id).empty().html(`
        <option value="">--Select--</option>
                                    <?php
                                    if (!empty($staff_list)) {
                                      foreach ($staff_list as $vat) {

                                    ?>
                                            <option value="<?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?>" data-name="<?= $vat->m_user_name ?>" data-balance="<?= money2($vat->m_user_balance) ?>" data-id="<?= $vat->m_user_id ?>" data-mobile="<?= $vat->m_user_mobile ?>" ><?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?></option>
                                        <?php
                                      }
                                    }

                                        ?>

        `);

    } else if (acct_type == 5) {
      $('#selet_label').html('General Name <span class="text-danger">*</span>');
      $('#m_supplier_list' + filed_id).empty().html(`
        <option value="">--Select--</option>
                                    <?php
                                    if (!empty($general_list)) {
                                      foreach ($general_list as $vat) {

                                    ?>
                                             <option value="<?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?>" data-name="<?= $vat->m_user_name ?>" data-balance="<?= money2($vat->m_user_balance) ?>" data-id="<?= $vat->m_user_id ?>" data-mobile="<?= $vat->m_user_mobile ?>" ><?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?></option>
                                       <?php
                                      }
                                    }

                                        ?>

        `);


    } else if (acct_type == 8) {
      // Head Office paying a branch. Branches are ordinary master_users_tbl
      // rows (type 9), so this is the same datalist shape as any other party.
      $('#selet_label').html('Branch <span class="text-danger">*</span>');
      $('#m_supplier_list' + filed_id).empty().html(`
        <option value="">--Select--</option>
                                    <?php
                                    if (!empty($branch_list)) {
                                      foreach ($branch_list as $vat) {
                                    ?>
                                             <option value="<?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?>" data-name="<?= $vat->m_user_name ?>" data-balance="<?= money2($vat->m_user_balance) ?>" data-id="<?= $vat->m_user_id ?>" data-mobile="<?= $vat->m_user_mobile ?>" ><?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?></option>
                                       <?php
                                      }
                                    }
                                        ?>
        `);
    } else if (acct_type == 6) {
      $('#selet_label').html('Investment Name <span class="text-danger">*</span>');
      $('#m_supplier_list' + filed_id).empty().html(`
        <option value="">--Select--</option>
                                    <?php
                                    if (!empty($investment_list)) {
                                      foreach ($investment_list as $vat) {

                                    ?>
                                             <option value="<?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?>" data-name="<?= $vat->m_user_name ?>" data-balance="<?= money2($vat->m_user_balance) ?>" data-id="<?= $vat->m_user_id ?>" data-mobile="<?= $vat->m_user_mobile ?>" ><?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?></option>
                                       <?php
                                      }
                                    }

                                        ?>

        `);

    };
  }

  function get_accout_list(account_type, filed_id) {

    if (account_type == 1) {
      $('#selet_label').html('Customer Name <span class="text-danger">*</span>');
      $('#m_customer_list' + filed_id).empty().html(`
        <option value="">--Select--</option>
                                    <?php
                                    if (!empty($custo_list)) {
                                      foreach ($custo_list as $vat) {

                                    ?> 
                                               <option value="<?= $vat->m_cust_name . ' | ' . $vat->m_cust_mobile; ?>"  data-name="<?= $vat->m_cust_name ?>" data-balance="<?= money2($vat->m_cust_balance) ?>" data-id="<?= $vat->m_cust_id ?>" data-mobile="<?= $vat->m_cust_mobile ?>"><?= $vat->m_cust_name . ' | ' . $vat->m_cust_mobile; ?></option>
                                     <?php
                                      }
                                    }

                                        ?>

        `);

    } else if (account_type == 2) {
      $('#selet_label').html('General Name <span class="text-danger">*</span>');
      $('#m_customer_list' + filed_id).empty().html(`
        <option value="">--Select--</option>
                                    <?php
                                    if (!empty($general_list)) {
                                      foreach ($general_list as $vat) {

                                    ?>
                                             <option value="<?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?>" data-name="<?= $vat->m_user_name ?>" data-balance="<?= money2($vat->m_user_balance) ?>" data-id="<?= $vat->m_user_id ?>" data-mobile="<?= $vat->m_user_mobile ?>"><?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?></option>
                                       <?php
                                      }
                                    }

                                        ?>

        `);

    } else if (account_type == 3) {
      $('#selet_label').html('Investment Name <span class="text-danger">*</span>');
      $('#m_customer_list' + filed_id).empty().html(`
        <option value="">--Select--</option>
                                    <?php
                                    if (!empty($investment_list)) {
                                      foreach ($investment_list as $vat) {

                                    ?>
                                              <option value="<?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?>" data-name="<?= $vat->m_user_name ?>" data-balance="<?= money2($vat->m_user_balance) ?>" data-id="<?= $vat->m_user_id ?>" data-mobile="<?= $vat->m_user_mobile ?>" ><?= $vat->m_user_name . ' | ' . $vat->m_user_mobile; ?></option>
                                      <?php
                                      }
                                    }

                                        ?>

        `);

    };

  }
</script>