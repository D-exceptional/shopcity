<?php 
  // Import Config File (Remove the demo images at launch)
  require_once __DIR__ . '/includes/config.php';

  // Import Initializer File
  require_once dirname(__DIR__) . '/includes/init.php';

  // Initialize Necessary Models
  $storeModel = $container->get(\App\Models\Store::class); 

  // Get store and page ID from URL
  $storeId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
  $page    = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

  // Store Coupons
  $couponList = $storeModel->findCouponsByStore($storeId, $page);

  // Pagination parameters
  $totalPages  = $couponList['total_pages'];
  $currentPage = $couponList['page'];
  $baseUrl     = "coupon-list?id={$storeId}";

  // Get store details
  require_once 'includes/setup.php';
?> 

<!DOCTYPE html>
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Store | Coupons</title>
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
          <div class="col-sm-6 header-count">
            <h1><b>Coupons (<?= $ratingManager->format($couponList['total']) ?>)</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href=".?id=<?= $storeId ?>">Home</a></li>
              <li class="breadcrumb-item active">Coupons</li>
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
          <h3 class="card-title">All Coupons</h3>
          <div class="card-tools">
            <button type="button" class="btn bg-primary color-white btn-sm btn-all">All</button>
            <button type="button" class="btn bg-primary color-white btn-sm btn-active">Active</button>
            <button type="button" class="btn bg-primary color-white btn-sm btn-inactive">Inactive</button>
            <!-- Previous button -->
            <button type="button" class="btn btn-info btn-tool btn-previous wb-50 hide">
              <a href="<?= ($currentPage > 1) ? pageUrl($currentPage - 1, $baseUrl) : '#' ?>" class="rounded <?= ($currentPage <= 1) ? 'disabled' : '' ?>">&laquo;</a>
            </button>
            <!-- Next button -->
             <button type="button" class="btn btn-info btn-tool btn-next wb-50 hide">
              <a href="<?= ($currentPage < $totalPages) ? pageUrl($currentPage + 1, $baseUrl) : '#' ?>" class="rounded <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">&raquo;</a>
            </button>
            <?php if (!empty($couponList['coupons'])): ?>
              <!-- Default button -->
              <button type="button" class="btn bg-primary color-white btn-sm btn-load">Load More</button>
            <?php endif; ?>
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
                <th>Code</th>
                <th>Discount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($couponList['coupons'])): ?>
                <!-- Display All Products -->
                <?php foreach ($couponList['coupons'] as $coupon): ?>

                  <?php
                    // Set status maps
                    $statusMaps = [
                      'Active'   => ['style' => 'btn-success', 'text' => 'Active'],
                      'Deactivated'   => ['style' => 'btn-danger', 'text' => 'Deactivated'],
                    ];
                  ?>

                  <tr class='content-row' data-id='<?= $coupon['coupon_id'] ?>' data-status='<?= $coupon['coupon_status'] ?>'>
                    <td>#</td>
                    <td class='coupon-code'><?= $coupon['coupon_code'] ?></td>
                    <td class='coupon-discount'><?= $ratingManager->format((int)$coupon['coupon_discount'] ?? 0); ?></td>
                    <td class='coupon-status'>
                      <select class='form-control' disabled>
                        <option value='<?= $coupon['coupon_status'] ?>'><?= $coupon['coupon_status'] ?></option>
                        <option value='<?= $coupon['coupon_status'] === 'Active' ? 'Deactivated' : 'Active' ?>'><?= $coupon['coupon_status'] === 'Active' ? 'Deactivated' : 'Active' ?></option>
                      </select>
                    </td>
                    <td><?= $coupon['created_at'] ?></td>
                    <td class='coupon-action'>
                      <div style="display: flex; gap: 10px;">
                        <button class='btn btn-info btn-edit btn-sm wmg-70'>Edit</button> 
                        <button class='btn btn-danger btn-delete btn-sm wmg-70'>Delete</button> 
                      </div>
                    </td>
                  </tr>

                <?php endforeach; ?>

                <?php else: ?>
                  <!-- No Product Available -->
                  <td colspan="10" class='text-center'>No coupons available</td>

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

  <!-- Footer section -->
  <?php include_once 'includes/footer.php'; ?>

  <!-- Custom Scripts -->
  <script src="<?php echo $cacheManager->parse('scripts/coupon-list.js'); ?>" type="module"></script>

</body>
</html>
