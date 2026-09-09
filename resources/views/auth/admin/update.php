<?php 
  declare(strict_types=1);

  // -------------------------------------------------
  // Base Directory 
  // -------------------------------------------------
  define('ROOT_DIR_PATH', dirname(__DIR__, 3));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin | Update Password</title>
  <?php include_once ROOT_DIR_PATH . '/includes/shared/head.php'; ?>
</head>
<body class="hold-transition login-page d-flex flex-direction-column align-items-center justify-content-center overflow-hidden">
    <div class="login-box" data-email="<? $email ?>">
        <div class="login-logo">
            <a href="#!"><b>Admin | Update Password</b></a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Set new password for your account.</p>
                <form class="update-form">
                    <div class="input-group mb-3">
                        <input type="number" class="form-control form-otp" placeholder="OTP">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-bars"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control form-password" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control form-repassword" placeholder="Confirm Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary btn-block btn-update">Update password</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <!-- Footer Section -->
    <?php include_once ROOT_DIR_PATH . '/includes/shared/footer.php'; ?>

    <!-- Core File -->
    <script src="<?= asset_versioned('js/admin_auth/update.js'); ?>" type="module"></script>
</body>
</html>
