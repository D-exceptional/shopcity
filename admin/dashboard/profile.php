<?php 
  // Import Config File (Remove the demo images at launch)
  require_once __DIR__ . '/includes/config.php';

  // Import Initializer File
  require_once __DIR__ . '/includes/init.php';

  // Get combined sales stats
  $userDetails = $userModel->findById($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin | Profile</title>
  <?php include_once 'includes/head.php'; ?>
</head>
<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <!-- Navbar -->
    <?php include_once 'includes/header.php'; ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include_once 'includes/sidebar.php'; ?>
    <!-- / Main Sidebar Container -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1><b>Profile</b></h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href=".">Home</a></li>
                <li class="breadcrumb-item active">Profile</li>
              </ol>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content content-view">
        <div class="container-fluid">
          <div class="row h-500">
            <div class="col-md-3">

              <!-- Profile Image -->
              <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <div class="text-center">
                    <img src='<?= $profile ?>' class='profile-user-img img-fluid img-circle wmg-100 hmg-100' alt='Admin Image'>
                  </div>

                  <h3 class="profile-username text-center">
                    <a href='#' class='d-block overflow-none nowrap ellipsis color-primary'><?= $fullName ?? 'Admin' ?></a>
                  </h3>
                  <p class="text-muted text-center">Admin</p>
                  <a href="#" class="btn btn-primary btn-block bg-transparent color-primary border-primary file-click"><b>Update Profile</b></a>
                  <input type="file" class="form-control form-file" hidden>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
            <div class="col-md-9">
              <div class="card">
                <div class="card-header p-2">
                  <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active bg-transparent color-primary border-primary mgr-5" href="#details" data-toggle="tab"><b>Details</b></a></li>
                    <li class="nav-item"><a class="nav-link bg-transparent color-primary border-primary" href="#security" data-toggle="tab"><b>Security</b></a></li>
                  </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                  <div class="tab-content">
                    <div class="tab-pane" id="details" style="display: block;">
                      <form class="form-horizontal">
                        <div class="form-group row">
                          <label for="name" class="col-sm-2 col-form-label">Name</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" value='<?= $fullName ?>' disabled>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="email" class="col-sm-2 col-form-label">Email</label>
                          <div class="col-sm-10">
                            <input type="email" class="form-control" name="email" value='<?= $email ?>' disabled>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="contact" class="col-sm-2 col-form-label">Contact</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" name="contact" value='<?= $userDetails['contact'] ?>' disabled>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="country" class="col-sm-2 col-form-label">Country</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" name="country" value='<?= $userDetails['country'] ?>' disabled>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="role" class="col-sm-2 col-form-label">Role</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" name="role" value='<?= $userDetails['user_role'] ?>' disabled>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="status" class="col-sm-2 col-form-label">Status</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" name="status" value='<?= $userDetails['user_status'] ?>' disabled>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="date" class="col-sm-2 col-form-label">Registered</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" name="date" value='<?= $userDetails['created_at'] ?>' disabled>
                          </div>
                        </div>
                        <!--
                        <div class="form-group row">
                          <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-success btn-details" disabled>Update</button>
                          </div>
                        </div>
                        -->
                      </form>
                    </div>
                    <!-- /.tab-pane -->
                    <!-- Security section -->
                    <div class="tab-pane" id="security">
                      <form class="form-horizontal">
                        <div class="form-group row">
                          <label for="facebook" class="col-sm-2 col-form-label">Current Password</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control form-password form-security" data-type="all" name="current_password" placeholder="Current password">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="facebook" class="col-sm-2 col-form-label">New Password</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control form-repassword form-security" data-type="all" name="new_password" placeholder="New password">
                          </div>
                        </div>
                        <div class="form-group row">
                          <div class="offset-sm-2 col-sm-10">
                            <button type="submit" class="btn btn-success btn-password">Update</button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                  <!-- /.tab-content -->
                </div><!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div><!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
  </div>

  <!-- Notification Bell Icon -->
  <div class="btn btn-lg-square notification-bell"></div>

  <!-- Footer section -->
  <?php include_once 'includes/footer.php'; ?>

  <!-- Custom Scripts -->
  <script src="<?php echo $cacheManager->parse('./scripts/profile.js'); ?>" type='module'></script>
  <script src="<?php echo $cacheManager->parse('./scripts/security.js'); ?>" type='module'></script>
</body>
</html>
