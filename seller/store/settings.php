<?php 
  // Import Config File (Remove the demo images at launch)
  require_once __DIR__ . '/includes/config.php';

  // Import Initializer File
  require_once dirname(__DIR__) . '/includes/init.php';

  // Initialize Necessary Models
  $storeModel = $container->get(\App\Models\Store::class);

  // Get mail ID from URL
  $storeId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

  // Get combined sales stats
  $storeDetails   = $storeModel->findOne($storeId);

  // Get store details
  require_once 'includes/setup.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Store | Settings</title>
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
              <h1><b>Settings</b></h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href=".?id=<?= $storeId ?>">Home</a></li>
                <li class="breadcrumb-item active">Settings</li>
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
                    <img src='<?= $storeDetails['store_avatar'] ?? null ?>' class='profile-user-img img-fluid img-circle wmg-100 hmg-100' alt='Store Image'>
                  </div>

                  <h3 class="profile-username text-center">
                    <a href='#' class='d-block overflow-none nowrap ellipsis color-primary'><?= $storeDetails['store_name'] ?? 'Store' ?></a>
                  </h3>
                  <p class="text-muted text-center">Store</p>
                  <a href="#" class="btn btn-primary btn-block bg-transparent color-primary border-primary file-click"><b>Update Avatar</b></a>
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
                    <li class="nav-item"><a class="nav-link bg-transparent color-primary border-primary mgr-5" href="#socials" data-toggle="tab"><b>Socials</b></a></li>
                  </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                  <div class="tab-content">
                    <div class="tab-pane" id="details" style="display: block;">
                      <form class="form-horizontal">
                        <div class="form-group row">
                          <label for="name" class="col-sm-2 col-form-label">Name</label>
                          <div class="col-sm-10">
                            <input data-type="name" type="text" class="form-control form-name form-text" name="name" value='<?= $storeDetails['store_name'] ?>'>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="description" class="col-sm-2 col-form-label">Description</label>
                          <div class="col-sm-10">
                           <textarea data-type="aplha-numeric" class="form-control form-description form-text">
                              <?= $storeDetails['store_description'] ?>
                           </textarea>
                          </div>
                        </div>
                         <div class="form-group row">
                          <label for="delivery" class="col-sm-2 col-form-label">Delivery</label>
                          <div class="col-sm-10">
                            <input data-type="aplha-numeric" type="text" class="form-control form-delivery form-text" name="delivery" value='<?= $storeDetails['store_delivery'] ?>'>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="status" class="col-sm-2 col-form-label">Status</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" name="status" value='<?= $storeDetails['store_status'] ?>' disabled>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="date" class="col-sm-2 col-form-label">Created</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" name="date" value='<?= $storeDetails['created_at'] ?>' disabled>
                          </div>
                        </div>
                        <div class="form-group row">
                          <div class="offset-sm-2 col-sm-10">
                            <button type="button" class="btn btn-success btn-details">Update</button>
                          </div>
                        </div>
                      </form>
                    </div>
                    <!-- /.tab-pane -->
                    <!-- Socials section -->
                    <div class="tab-pane" id="socials">
                      <form class="form-horizontal">
                        <div class="form-group row">
                          <label for="facebook" class="col-sm-2 col-form-label">Facebook</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control form-facebook form-social" data-type="all" name="facebook" value='<?= $storeDetails['facebook'] ?? 'Not set' ?>' placeholder="Facebook">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="instagram" class="col-sm-2 col-form-label">Instagram</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control form-instagram form-social" data-type="all" name="instagram" value='<?= $storeDetails['instagram'] ?? 'Not set' ?>' placeholder="Instagram">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="tiktok" class="col-sm-2 col-form-label">Tiktok</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control form-tiktok form-social" data-type="all" name="tiktok" value='<?= $storeDetails['tiktok'] ?? 'Not set' ?>' placeholder="Tiktok">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="twitter" class="col-sm-2 col-form-label">Twitter</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control form-twitter form-social" data-type="all" name="twitter" value='<?= $storeDetails['twitter'] ?? 'Not set' ?>' placeholder="Twitter">
                          </div>
                        </div>
                        <div class="form-group row">
                          <div class="offset-sm-2 col-sm-10">
                            <button type="button" class="btn btn-success btn-socials">Update</button>
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

  <!-- Footer section -->
  <?php include_once 'includes/footer.php'; ?>

  <!-- Page specific script -->
  <script>
    $(function () {
      //Add text editor
      $('.form-description').summernote()
    })
  </script>

  <!-- Custom Scripts -->
  <script src="<?php echo $cacheManager->parse('scripts/details.js'); ?>" type='module'></script>
  <script src="<?php echo $cacheManager->parse('scripts/profile.js'); ?>" type='module'></script>
  <script src="<?php echo $cacheManager->parse('scripts/socials.js'); ?>" type='module'></script>
</body>
</html>
