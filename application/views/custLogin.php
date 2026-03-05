<?php include("head.php"); ?>
<!-- ========== Page Content ========== -->
<section class="py-5 d-flex align-items-center" style="background:#b6b6b6;min-height:100vh;">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-4">
                <div class="card rounded-4 p-4 text-center border-0 shadow">
                    <!-- <img src="<? // base_url('uploads/'). get_settings('m_app_logo') ?>" alt="" class="w-25 mx-auto"> -->
                    <p class="text-secondary my-2">Welcome <?= get_settings('m_app_name') ?></p>
                    <hr class="my-3">
                    <form action="<?= base_url('CustLogin') ?>" class="text-start" method="post">
                        <label for="" class="small mb-0">Login id</label>
                        <input type="text" class="form-control" name="login_id" id="login_id" placeholder="Enter Login Id" required>
                        <label for="" class="small mb-0 mt-3">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="login_pass" id="login_pass" placeholder="********">
                            <button class="btn btn-light border text-secondary" type="button"><i class="bi bi-eye-fill"></i></button>
                        </div>
                        <input type="submit" class="btn btn-info btn-lg w-100 mt-4" value="Log IN">
                    </form>
                </div>
                <?php if ($this->session->flashdata('status')) echo $this->session->flashdata('status'); ?>
            </div>
        </div>
    </div>
</section>
<!-- ========== Page Content ========== -->
<?php include("footer.php"); ?>