<script type="text/javascript">
 $(document).ready(function(e) {

 $("#frm-update").submit(function(e){ e.preventDefault();
      var clkbtn = $("#btn-update"); clkbtn.prop('disabled',true);
      var formData = new FormData(this); 
      
      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Welcome/update_application_settings'); ?>",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "JSON", 
        success: function(data) {
          if(data.status=='success'){
                swal(data.message, {icon: "success", timer: 1000, });
                setTimeout(function(){ location.reload(); },1000);
          }else{ clkbtn.prop('disabled',false);
            swal(data.message, {icon: "error", timer: 5000, });
          }   
        }, error: function (jqXHR, status, err){ clkbtn.prop('disabled',false);
          swal("Some Problem Occurred!! please try again", { icon: "error", timer: 2000, });
        }
      });
    });

    // Superadmin reveals their own current login id and password.
    $("#view-admin-password").on("click", function() {
      var clkbtn = $(this);
      clkbtn.prop('disabled', true);

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Accounts/view_password'); ?>",
        data: {
          id: "<?php echo $this->session->userdata('user_id'); ?>"
        },
        dataType: "JSON",
        success: function(data) {
          clkbtn.prop('disabled', false);
          if (data.status == 'success') {
            swalCredentials("Login Details", [
              ['Login ID', data.loginid],
              ['Password', data.password]
            ]);
          } else {
            swal(data.message, {icon: "error", timer: 5000, });
          }
        }, error: function (jqXHR, status, err){ clkbtn.prop('disabled', false);
          swal("Some Problem Occurred!! please try again", { icon: "error", timer: 2000, });
        }
      });
    });

    // Superadmin reveals the current date-lock password. Shown next to the
    // login id it is entered under, to match the login password popup.
    $("#view-date-password").on("click", function() {
      var clkbtn = $(this);
      clkbtn.prop('disabled', true);

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Welcome/view_date_lock_password'); ?>",
        dataType: "JSON",
        success: function(data) {
          clkbtn.prop('disabled', false);
          if (data.status == 'success') {
            swalCredentials("Date Password", [
              ['Login ID', $('input[name="m_admin_login_id"]').val()],
              ['Date Password', data.password]
            ]);
          } else {
            swal(data.message, {icon: "error", timer: 5000, });
          }
        }, error: function (jqXHR, status, err){ clkbtn.prop('disabled', false);
          swal("Some Problem Occurred!! please try again", { icon: "error", timer: 2000, });
        }
      });
    });
   });
</script>