<!-- ========================Header==============Fix========= -->
<?php $this->view('head') ?>
<?php $this->view('header') ?>

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->
  <div class="container-fluid flex-grow-1 container-p-y">

    <!-- Basic Bootstrap Table -->
    <div class="card py-3">
      <div class="row">
        <div class="col-6">
          <div class="text-start m-0 m-3">
            <h5><?php echo $pagename;  ?></h5>
          </div>
        </div>
      </div>
      <div class="card-body">
        <form method="POST" action="#" id="frm-update">
          <div class="row g-4">
            <input type="hidden" name="appid" value="<?php echo $app_details[0]->m_app_id ?>">
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Application Name <span class="text-danger">*</span></label>
                <input value="<?php echo $app_details[0]->m_app_name; ?>" type="text" required placeholder="App Name" class="form-control" name="m_app_name">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Application Title <span class="text-danger">*</span></label>
                <input value="<?php echo $app_details[0]->m_app_title; ?>" type="text" required placeholder="App Title" class="form-control" name="m_app_title">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Application Contact</label>
                <input value="<?php echo $app_details[0]->m_app_mobile  ?>" type="text" name="m_app_contact" placeholder="App Contact" class="form-control">
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Application Alternative Contact</label>
                <input value="<?php echo $app_details[0]->m_app_alt_mobile  ?>" type="text" name="m_app_alt_contact" placeholder="App Contact" class="form-control">
              </div>
            </div>


            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Application Mail</label>
                <input value="<?php echo $app_details[0]->m_app_email  ?>" type="text" name="m_app_mail" placeholder="App Mail" class="form-control">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="control-label">Application Login Id</label>
                <input value="<?php echo $login_detail->m_user_login_id  ?>" type="text" name="m_admin_login_id" placeholder="App Login Id" class="form-control">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label class="control-label">Login Password</label>
                <input value="<?php echo $login_detail->m_user_pass  ?>" type="text" name="m_admin_pass" placeholder="App Password" class="form-control">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label class="control-label">Date Password</label>
                <input value="<?php echo $app_details[0]->date_lock_password  ?>" type="text" name="date_lock_password" placeholder="App Date Password" class="form-control">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-check form-switch mt-4">
                <input class="form-check-input" value="1" type="checkbox" name="date_lock_enabled" role="switch" id="flexSwitchCheckChecked" <?php if($app_details[0]->date_lock_enabled == 1) echo 'checked'; ?> >
                <label class="form-check-label small" for="flexSwitchCheckChecked">Is Date Lock</label>
              </div>

            </div>


            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label">Application Address</label>
                <textarea name="m_app_address" class="form-control"><?php echo $app_details[0]->m_app_address ?></textarea>
              </div>
            </div>
          </div>
          <br>

          <div class="row g-3">

            <h5>Social Setting</h5>
            <div class="col-md-6">
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Instagram</label>
                <div class="col-sm-10">
                  <div class="input-group select-group-merge">
                    <input type="text" id="m_app_instagram" name="m_app_instagram" class="form-control phone-mask" placeholder="#" value="<?php echo $app_details[0]->m_app_insta; ?>" aria-label="" aria-describedby="" required />
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="">Facebook</label>
                <div class="col-sm-10">
                  <div class="input-group input-group-merge">
                    <input type="text" id="m_app_fesbook" name="m_app_fesbook" class="form-control phone-mask" aria-label="" value="<?php echo $app_details[0]->m_app_fb; ?>" aria-describedby="" placeholder="#" required />
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-message">Whatsapp</label>
                <div class="col-sm-10">
                  <input type="text" id="m_app_whatsapp" name="m_app_whatsapp" class="form-control phone-mask" aria-label="" value="<?php echo $app_details[0]->m_app_whatsapp; ?>" placeholder="#" aria-describedby="" required />
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-message">Twitter</label>
                <div class="col-sm-10">
                  <input type="text" id="m_app_twitter" name="m_app_twitter" class="form-control phone-mask" aria-label="" value="<?php echo $app_details[0]->m_app_twitter; ?>" placeholder="#" aria-describedby="" required />
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-message">Linkedin</label>
                <div class="col-sm-10">
                  <input type="text" id="m_app_linkedin" name="m_app_linkedin" class="form-control phone-mask" aria-label="" value="<?php echo $app_details[0]->m_app_linkedin; ?>" placeholder="#" aria-describedby="" required />
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-message">Youtube</label>
                <div class="col-sm-10">
                  <input type="text" id="m_app_youtude" name="m_app_youtude" class="form-control phone-mask" aria-label="" aria-describedby="" value="<?php echo $app_details[0]->m_app_youtube; ?>" placeholder="#" required />

                </div>
              </div>
            </div>


          </div><br>

          <div class="row g-3" style="margin-top:10px;">
            <div class="col-md-3" style="border: 2px solid #f1f2f4;height: 210px;">
              <div class="form-group">
                <?php
                if (!empty($app_details[0]->m_app_logo) && file_exists('uploads/' . $app_details[0]->m_app_logo)) {
                  $applogo = base_url('uploads/' . $app_details[0]->m_app_logo);
                } else {
                  $applogo = base_url('assets/img/default.jpg');
                }
                ?>
                <img style="max-height:120px" src="<?php echo $applogo ?>" class="img-responsive img-thumbnail" /><br>
                <label class="control-label">Color Logo</label>
                <input type="hidden" name="applogo" value="<?php echo $app_details[0]->m_app_logo ?>">
                <input type="file" name="m_app_logo" class="form-control">
              </div>
            </div>
            <div class="col-md-3" style="border: 2px solid #f1f2f4;height: 210px;">
              <div class="form-group">
                <?php
                if (!empty($app_details[0]->m_app_icon) && file_exists('uploads/' . $app_details[0]->m_app_icon)) {
                  $appfavi = base_url('uploads/' . $app_details[0]->m_app_icon);
                } else {
                  $appfavi = base_url('assets/img/default.jpg');
                }
                ?>
                <img style="max-height:50px" src="<?php echo $appfavi ?>" class="img-responsive img-thumbnail" /><br>
                <label class="control-label">Favicon logo</label>
                <input type="hidden" name="appfavicon" value="<?php echo $app_details[0]->m_app_icon ?>">
                <input type="file" name="m_app_icon" class="form-control">
              </div>
            </div>
            <div class="col-md-3" style="border: 2px solid #f1f2f4;height: 210px;">
              <div class="form-group">
                <?php
                if (!empty($app_details[0]->m_app_black_logo) && file_exists('uploads/' . $app_details[0]->m_app_black_logo)) {
                  $appblack_logo = base_url('uploads/' . $app_details[0]->m_app_black_logo);
                } else {
                  $appblack_logo = base_url('assets/img/default.jpg');
                }
                ?>
                <img style="max-height:120px;" src="<?php echo $appblack_logo ?>" class="img-responsive img-thumbnail" /><br>
                <label class="control-label">Black Logo</label>
                <input type="hidden" name="app_black_logo" value="<?php echo $app_details[0]->m_app_black_logo ?>">
                <input type="file" name="m_app_black_logo" class="form-control">
              </div>
            </div>

            <div class="col-md-3" style="border: 2px solid #f1f2f4;height: 210px;">
              <div class="form-group">
                <?php
                if (!empty($app_details[0]->m_app_white_logo) && file_exists('uploads/' . $app_details[0]->m_app_white_logo)) {
                  $appwhite_logo = base_url('uploads/' . $app_details[0]->m_app_white_logo);
                } else {
                  $appwhite_logo = base_url('assets/img/default.jpg');
                }
                ?>
                <img style="max-height:120px;" src="<?php echo $appwhite_logo ?>" class="img-responsive img-thumbnail" /><br>
                <label class="control-label">White Logo</label>
                <input type="hidden" name="app_white_logo" value="<?php echo $app_details[0]->m_app_white_logo ?>">
                <input type="file" name="m_app_white_logo" class="form-control">
              </div>
            </div>
          </div><br>
          <div class="row p-3">
            <div class="form-layout-submit text-center">
              <button type="submit" id="btn-update" class="btn btn-block btn-info">Update Settings</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <!--/ Basic Bootstrap Table -->
  </div>
</div>

<!-- / Content -->
<!-- ========================Footer================Fix======= -->
<?php $this->view('footer');
$this->view('js/application_setting_js');
?>

<!-- ========================Footer================Fix======= -->