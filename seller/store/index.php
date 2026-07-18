<?php 
  // Import Config File (Remove the demo images at launch)
  require_once __DIR__ . '/includes/config.php';

  // Import Initializer File
  require_once dirname(__DIR__) . '/includes/init.php';

  // Initialize Necessary Models
  $orderModel   = $container->get(\App\Models\Order::class); 
  $productModel = $container->get(\App\Models\Product::class); 
  $storeModel   = $container->get(\App\Models\Store::class); 

  // Get mail ID from URL
  $storeId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

  // Get combined sales stats
  $storeOrderStats   = $orderModel->getVendorStoreStats($userId, $storeId);
  $activeProducts    = $productModel->countProductsByType(null, $storeId, 'Visible', 'vendor');
  $pendingProducts   = $productModel->countProductsByType(null, $storeId, 'Hidden', 'vendor');
  $storeReviewsStats = $productModel->countReviewsByVendor($userId, $storeId);
  $storeCouponStats  = $storeModel->getStoreCouponStats($storeId);

  // Get store details
  require_once 'includes/setup.php';
?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Store | Home</title>
  <?php include_once 'includes/head.php'; ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
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
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-12">
              <h1 class="m-0"><b>Dashboard</b></h1>
            </div><!-- /.col -->
          </div><!-- /.row -->
          <div class="row mb-2 flex-wrap-nowrap">
            <div class="col-sm-6 d-flex align-items-center">
              <select class="border-none w-100 commission-filter">
                <option value="">Check Sales By:</option>
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="last_week">Last Week</option>
                <option value="last_month">Last Month</option>
                <option value="last_year">Last Year</option>
                <option value="custom">Custom Date</option>
              </select>
            </div><!-- /.col -->
            <div class="col-sm-6 d-flex align-items-center" class='pr-2'>
              <select class="border-none w-50 currency-filter">
                <option value="">Select Currency</option>
                <option value="dollar">&#x24;</option>
                <option value="naira">&#x20A6;</option>
                <option value="cedis">&#x20B5;</option>
                <option value="shillings">&#83;</option>
                <option value="cefa">&#x20A3;</option>
                <option value="rand">&#82;</option>
              </select>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content content-view">
        <div class="container-fluid">
          <!-- Small boxes (Stat box) -->
          <div class="row h-500">

            <!-- Stats section -->
            <?php include_once 'includes/stats.php'; ?>

          </div>
        </div>
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
  </div>

  <!--- Commission Overlay ---->
  <div class="modal">
    <div class="totals sales-section">Total sales: <span></span></div>
    <div class="totals amount-section">Total amount: <span></span></div>
    <p class="info">Search sales between date intervals</p>
    <div class="range-section">
      <p>From</p> 
      <input type="date" class="form-control date-from">
      <p>To</p> 
      <input type="date" class="form-control date-to">
      <div class="row align-items-center justify-content-center mt-15">
        <button class="form-control range-check">Search</button>
      </div>
    </div>
    <div class="close-overlay">
      <i class='fa fa-times' aria-hidden='true'></i>
    </div>
  </div>

  <!-- Footer section -->
  <?php include_once 'includes/footer.php'; ?>

  <!-- Custom Scripts -->
  <script src="<?php echo $cacheManager->parse('scripts/dashboard.js'); ?>" type="module"></script>

</body>
</html>
