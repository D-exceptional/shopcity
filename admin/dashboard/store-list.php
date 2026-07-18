<?php 
  // Import Config File (Remove the demo images at launch)
  require_once __DIR__ . '/includes/config.php';

  // Import Initializer File
  require_once __DIR__ . '/includes/init.php';

  // Initialize Necessary Models
  $storeModel = $container->get(\App\Models\Store::class);

  // Get store and page ID from URL
  $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

  // Get combined sales stats
  $storeList = $storeModel->findStoresByStatus(null, $page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin | Stores</title>
  <?php include_once 'includes/head.php'; ?>
</head>
<body class="hold-transition sidebar-mini">
  <!-- Site wrapper -->
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
              <h1><b>Stores</b></h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href=".">Home</a></li>
                <li class="breadcrumb-item active">Stores</li>
              </ol>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content content-view">
        <!-- Default box -->
        <div class="card h-550">
          <div class="card-header">
            <h3 class="card-title">Stores</h3>
            <div class="card-tools">
              <!-- Default button -->
              <button type="button" class="btn bg-primary color-white btn-sm btn-all">All</button>
              <button type="button" class="btn bg-primary color-white btn-sm btn-pending">Pending</button>
              <button type="button" class="btn bg-primary color-white btn-sm btn-active">Active</button>
              <button type="button" class="btn bg-primary color-white btn-sm btn-load">Load More</button>
              <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body p-0 overlow-x-auto white-space-normal">
            <table class="table table-striped projects">
              <thead>
                <tr>
                  <th>S/N</th>
                  <th>Name</th>
                  <th>Avatar</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Facebook</th>
                  <th>Instagram</th>
                  <th>Tiktok</th>
                  <th>Twitter</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($storeList)): ?>
                  <!------ Dispplay Stores ------>
                  <?php foreach ($storeList as $store): ?>

                    <tr class='content-row' data-id='<?=$store['store_id'] ?>'>
                      <td>#</td>
                      <td><?= $store['store_name'] ?></td>
                      <td>
                        <img src='<?= $store['store_avatar'] ?? '../assets/img/avatar.jpg' ?>' class='profile-user-img img-fluid hmg-100 border-radius-10' alt='Store Image'>
                      </td>
                      <td class='status'>
                        <button class='btn <?= ($store['store_status'] === 'Active') ? 'btn-success' : 'btn-danger' ?> btn-sm'>
                          <?= $store['store_status'] ?>
                        </button> 
                      </td>
                      <td><?= $store['created_at'] ?></td>
                      <td><?= mb_substr($store['facebook'] ?? 'Not set', 0, 17) . '...' ?></td>
                      <td><?= mb_substr($store['instagram'] ?? 'Not set', 0, 17) . '...' ?></td>
                      <td><?= mb_substr($store['tiktok'] ?? 'Not set', 0, 17) . '...' ?></td>
                      <td><?= mb_substr($store['twitter'] ?? 'Not set', 0, 17) . '...' ?></td>
                      <td class='action' style="display: flex; gap: 10px;">
                        <?= ($store['store_status'] === 'Active') 
                        ? "<button class='btn bg-primary color-white btn-sm btn-action w-150'>Deactivate</button>" 
                        : "<button class='btn bg-primary color-white btn-sm btn-action w-150'>Activate</button>"
                        ?>
                      </td>
                    </tr>

                  <?php endforeach; ?>

                  <?php else: ?>
                  <tr>
                    <td colspan="11" class='text-center'>No store available</td>
                  </tr>

                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
  </div>

  <!------- Image Preview ------>
  <div id='overlay'>
    <iframe src="" frameborder="0"></iframe>
    <div class='close-view'>
      <i class='fa fa-times' aria-hidden='true'></i>
    </div>
  </div>

  <!-- Footer section -->
  <?php include_once 'includes/footer.php'; ?>

  <!-- Custom Scripts -->
  <script src="<?php echo $cacheManager->parse('./scripts/store-list.js'); ?>" type="module"></script>

</body>
</html>
