<script type="text/javascript">
  $(document).ready(function(e) {
    //============================category============================//

    $(".click-btn").click(function(e) {
      var link = $(this).data('linkk');
      $('#frm-balance_report').attr('action', link);
      $('#frm-balance_report').submit();

    });

    $(".ledger_btn_submit").click(function(e) {
      var link = $(this).data('link');
      $('#frm-ledger-list').attr('action', link);

      // Most of these ledgers are about one account, so the submit waits for
      // the picker. The transfer ledger is not - it lists every Head Office
      // -> branch movement in the range - so it opts out with data-noaccount
      // rather than being blocked by a picker it never uses.
      if ($(this).data('noaccount') == 1 || $('#account_name_select').val() != '') {
        $('#frm-ledger-list').submit();
      }

    });


    $('.trclass').on('click', function() {
      $('.trclass').removeClass('active');
      $(this).addClass('active');
      var link = $(this).data('value');
      $('#frm-report-list').attr('action', link);

      var fild = $(this).data('fild');
      if (fild == 1) {
        $('#sprtype').removeClass('d-none');
      } else {
        $('#sprtype').addClass('d-none');
      }

    });

    $('.suplier_btn').on('click', function() {
      if ($(this).data('req') == 1) {
        $('#agent').prop('required', false);
        $('#customers').prop('required', false);
        $('#suppiler').prop('required', true);
      } else {
        $('#agent').prop('required', false);
        $('#customers').prop('required', false);
        $('#suppiler').prop('required', false);
      };
      $('#sup_div').removeClass('d-none');
      $('#cust_div').addClass('d-none');
      $('#agent_div').addClass('d-none');

    });

    $('.agent_btn').on('click', function() {

      $('#customers').prop('required', false);
      $('#suppiler').prop('required', false);
      $('#agent').prop('required', true);

      $('#agent_div').removeClass('d-none');
      $('#sup_div').addClass('d-none');
      $('#cust_div').addClass('d-none');

    });

    $('.cust_btn').on('click', function() {
      if ($(this).data('req') == 1) {
        $('#customers').prop('required', true);
        $('#suppiler').prop('required', false);
        $('#agent').prop('required', false);
      } else {
        $('#customers').prop('required', false);
        $('#suppiler').prop('required', false);
        $('#agent').prop('required', false);
      };
      $('#agent_div').addClass('d-none');
      $('#sup_div').addClass('d-none');
      $('#cust_div').removeClass('d-none');

    });

    $('.submit_btn').on('click', function() {
      if ($(".trclass").hasClass("active")) {
        return true;
      } else {
        $(".trclass").css('border', '2px solid #002bff');
        swal("Please Select Report Type", {
          icon: "error",
          timer: 2000,
        });
        return false;
      };



    });




    $('#account_type').on('change', function() {
      // Back to the default shape first - a visible, required picker - then
      // let each type below change what it needs. Without this the "not
      // required" set by the voucher and transfer types leaked into whichever
      // type was picked afterwards.
      $('#cust_div').removeClass('d-none');
      $('#account_name_select').prop('required', true);

      if ($(this).val() == 1) {
        $('.ledger_btn_submit').addClass('d-none');
        $('#custcashbtn').removeClass('d-none');
        $('#custcratebtn').removeClass('d-none');
        $('#selet_label').html('Customer Account <span class="text-danger">*</span>');
        $('#account_name_select').empty().html(`
        <option value="">--Select--</option>
        <?php
        if (!empty($cust_dtl)) {
          foreach ($cust_dtl as $calue) {
        ?>
                                            <option value="<?php echo $calue->m_cust_id; ?>">
                                                <?php echo $calue->m_cust_name . ' | ' . $calue->m_cust_mobile ?>
                                            </option>
                                        <?php
                                      }
                                    }

                                        ?>
        `);
      } else if ($(this).val() == 2) {
        $('.ledger_btn_submit').addClass('d-none');
        $('#suppcashbtn').removeClass('d-none');
        $('#suppcratebtn').removeClass('d-none');

        $('#selet_label').html('Supplier Account <span class="text-danger">*</span>');
        $('#account_name_select').empty().html(`
        <option value="">--Select--</option>
        <?php
        if (!empty($supl_dtl)) {
          foreach ($supl_dtl as $calue) {
        ?>
                                            <option value="<?php echo $calue->m_user_id; ?>">
                                                <?php echo $calue->m_user_name . ' | ' . $calue->m_user_mobile ?>
                                            </option>
                                        <?php
                                      }
                                    }

                                        ?>
        `);
      } else if ($(this).val() == 10) {
        $('.ledger_btn_submit').addClass('d-none');
        $('#voucherdebitbtn').removeClass('d-none');
        $('#vouchercreditbtn').removeClass('d-none');
        $('#account_name_select').prop('required',false)
        $('#selet_label').html('Supplier Account');
        $('#account_name_select').empty().html(`
        <option value="">--Select--</option>
        <?php
        if (!empty($supl_dtl)) {
          foreach ($supl_dtl as $calue) {
        ?>
                                            <option value="<?php echo $calue->m_user_id; ?>">
                                                <?php echo $calue->m_user_name . ' | ' . $calue->m_user_mobile ?>
                                            </option>
                                        <?php
                                      }
                                    }

                                        ?>
        `);
      } else if ($(this).val() == 6) {
        $('.ledger_btn_submit').addClass('d-none');
        $('#staffcommledgerbtn').removeClass('d-none');

        $('#selet_label').html('Staff Account <span class="text-danger">*</span>');
        $('#account_name_select').empty().html(`
        <option value="">--Select--</option>
        <?php
        if (!empty($agent_dtl)) {
          foreach ($agent_dtl as $calue) {
        ?>
                                            <option value="<?php echo $calue->m_user_id; ?>">
                                                <?php echo $calue->m_user_name . ' | ' . $calue->m_user_mobile ?>
                                            </option>
                                        <?php
                                      }
                                    }

                                        ?>
        `);
      } else if ($(this).val() == 7) {
        $('.ledger_btn_submit').addClass('d-none');
        $('#generalledgerbtn').removeClass('d-none');

        $('#selet_label').html('General Account <span class="text-danger">*</span>');
        $('#account_name_select').empty().html(`
        <option value="">--Select--</option>
        <?php
        if (!empty($general_lst)) {
          foreach ($general_lst as $calue) {
        ?>
                                            <option value="<?php echo $calue->m_user_id; ?>">
                                                <?php echo $calue->m_user_name . ' | ' . $calue->m_user_mobile ?>
                                            </option>
                                        <?php
                                      }
                                    }

                                        ?>
        `);
      } else if ($(this).val() == 8) {
        $('.ledger_btn_submit').addClass('d-none');
        $('#investmentledgerbtn').removeClass('d-none');

        $('#selet_label').html('Inventment Account <span class="text-danger">*</span>');
        $('#account_name_select').empty().html(`
        <option value="">--Select--</option>
        <?php
        if (!empty($investement_lst)) {
          foreach ($investement_lst as $calue) {
        ?>
                                            <option value="<?php echo $calue->m_user_id; ?>">
                                                <?php echo $calue->m_user_name . ' | ' . $calue->m_user_mobile ?>
                                            </option>
                                        <?php
                                      }
                                    }

                                        ?>
        `);

      } else if ($(this).val() == 3) {
        $('.ledger_btn_submit').addClass('d-none');
        $('#expenseledgerbtn').removeClass('d-none');


        $('#selet_label').html('Expense Account <span class="text-danger">*</span>');
        $('#account_name_select').empty().html(`
        <option value="">--Select--</option>
                                    <?php
                                    if (!empty($expense_lst)) {
                                      foreach ($expense_lst as $vat) {
                                        if ($vat->m_group_group == 0 || $vat->m_group_group == '') {
                                    ?>
                                            <option value="<?php echo $vat->m_group_id; ?>"><?= $vat->m_group_name; ?>
                                        <?php
                                        }
                                      }
                                    }

                                        ?>
        `);
      } else if ($(this).val() == 9) {
        $('.ledger_btn_submit').addClass('d-none');
        $('#frightledgerbtn').removeClass('d-none');


        $('#selet_label').html('Fright Account <span class="text-danger">*</span>');
        $('#account_name_select').empty().html(`
        <option value="">--Select--</option>
                                    <?php
                                    if (!empty($expense_lst)) {
                                      foreach ($expense_lst as $vat) {
                                        if ($vat->m_group_group != 0 && $vat->m_group_group != '') {
                                    ?>
                                            <option value="<?php echo $vat->m_group_id; ?>"><?= $vat->m_group_name; ?>
                                        <?php
                                        }
                                      }
                                    }

                                        ?>
        `);
      } else if ($(this).val() == 4) {
        $('.ledger_btn_submit').addClass('d-none');
        $('#cashledgerbtn').removeClass('d-none');

        $('#selet_label').html('Cash Account <span class="text-danger">*</span>');
        $('#account_name_select').empty().html(`
        <option value="">--Select--</option>
                                    <?php
                                    if (!empty($cash_lst)) {
                                      foreach ($cash_lst as $vat) {

                                    ?>
                                            <option value="<?php echo $vat->m_group_id; ?>"><?= $vat->m_group_name; ?>
                                        <?php
                                      }
                                    }

                                        ?>
        `);

      } else if ($(this).val() == 5) {
        $('.ledger_btn_submit').addClass('d-none');
        $('#bankledgerbtn').removeClass('d-none');

        $('#selet_label').html('Bank Account <span class="text-danger">*</span>');
        $('#account_name_select').empty().html(`
        <option value="">--Select--</option>
                                    <?php
                                    if (!empty($bank_lst)) {
                                      foreach ($bank_lst as $vat) {

                                    ?>
                                            <option value="<?php echo $vat->m_group_id; ?>"><?= $vat->m_group_name; ?>
                                        <?php
                                      }
                                    }

                                        ?>
        `);

      } else if ($(this).val() == 12) {
        $('.ledger_btn_submit').addClass('d-none');
        $('#branchledgerbtn').removeClass('d-none');
        $('#branchoutstandingbtn').removeClass('d-none');

        // Branch Ledger needs one branch; Branch Outstanding lists them all
        // and opts out of the picker with data-noaccount.
        $('#selet_label').html('Branch <span class="text-danger">*</span>');
        $('#account_name_select').empty().html(`
        <option value="">--Select--</option>
        <?php
        if (!empty($branch_list)) {
          foreach ($branch_list as $calue) {
        ?>
                                            <option value="<?php echo $calue->m_user_id; ?>">
                                                <?php echo $calue->m_user_name . ' | ' . $calue->m_user_mobile ?>
                                            </option>
                                        <?php
                                      }
                                    }

                                        ?>
        `);
      } else if ($(this).val() == 11) {
        $('.ledger_btn_submit').addClass('d-none');
        $('#transferledgerbtn').removeClass('d-none');

        // Scoped by date range and branch, not by one account, so the picker
        // has nothing to offer here - hide it rather than show an empty
        // required field the user cannot satisfy.
        $('#cust_div').addClass('d-none');
        $('#account_name_select').prop('required', false).empty();
      }

    });




  });
</script>