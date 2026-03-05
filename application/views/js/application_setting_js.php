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
   });
</script>