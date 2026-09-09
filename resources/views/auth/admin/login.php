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
  <title>Admin | Login</title>
  <?php include_once ROOT_DIR_PATH . '/includes/shared/head.php'; ?>
</head>
<body class="hold-transition login-page d-flex flex-direction-column align-items-center justify-content-center overflow-hidden">
    <div class="login-box">
        <div class="login-logo">
            <a href="#!"><b>Admin | Login</b></a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <form class="login-form">
                <div class="input-group mb-3">
                    <input type="email" class="form-control form-email" placeholder="Email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control form-password" data-type="all" placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock psw-span"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <button type="button" class="btn btn-primary btn-block btn-login">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
            <br>
            <p class="mb-1">
                <a href="/auth/admin/verify">I forgot my password</a>
            </p>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <!-- Footer Section -->
    <?php include_once ROOT_DIR_PATH . '/includes/shared/footer.php'; ?>
 
    <!-- Core File -->
    <script src="<?= asset_versioned('js/admin_auth/login.js'); ?>" type="module"></script>
</body>
</html>
