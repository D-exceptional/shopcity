<?php 
  // Import Config File (Remove the demo images at launch)
  require_once __DIR__ . '/includes/config.php';

  // Import Initializer File
  require_once __DIR__ . '/includes/init.php';

  // Initialize Necessary Models
  $orderModel = $container->get(\App\Models\Order::class);

  // Get view and page ID from URL
  $view = isset($_GET['view']) && is_string($_GET['view']) ? (string)$_GET['view'] : null;
  $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

  // Store Products
  $orderList = in_array($view, ['Pending', 'Completed', 'Cancelled']) 
  ? $orderModel->getOrdersByStatus($view, $page)
  : $orderModel->getAllOrders($page);

  // Pagination parameters
  $totalPages  = $orderList['total_pages'];
  $currentPage = $orderList['page'];
  $baseUrl     = "order-list?view={$view}";
?> 

<!DOCTYPE html>
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <title>Admin | <?= $view ?>Orders</title>
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
            <h1><b><?= $view ?> Orders (<?= $ratingManager->format($orderList['total']) ?>)</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href=".">Home</a></li>
              <li class="breadcrumb-item active">Orders</li>
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
          <h3 class="card-title">All <?= $view ?> Orders</h3>
          <div class="card-tools">
            <!-- Previous button -->
            <button type="button" class="btn btn-info btn-tool btn-previous wb-50 hide">
              <a href="<?= ($currentPage > 1) ? pageUrl($currentPage - 1, $baseUrl) : '#' ?>" class="rounded <?= ($currentPage <= 1) ? 'disabled' : '' ?>">&laquo;</a>
            </button>
            <!-- Next button -->
             <button type="button" class="btn btn-info btn-tool btn-next wb-50 hide">
              <a href="<?= ($currentPage < $totalPages) ? pageUrl($currentPage + 1, $baseUrl) : '#' ?>" class="rounded <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">&raquo;</a>
            </button>
            <?php if (!empty($orderList['orders'])): ?>
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
                <th>Tracking ID</th>
                <th>Subtotal</th>
                <th>Tax</th>
                <th>Discount</th>
                <th>Shipping</th>
                <th>Total</th>
                <th>Address</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($orderList['orders'])): ?>
                <!-- Display All Products -->
                <?php foreach ($orderList['orders'] as $order): ?>

                  <?php
                    // Set status maps
                    $statusMaps = [
                      'Pending'   => ['style' => 'btn-danger', 'text' => 'View'],
                      'Completed' => ['style' => 'btn-success', 'text' => 'View'],
                      'Cancelled' => ['style' => 'btn-danger', 'text' => 'View'],
                    ];
                  ?>

                  <tr class='content-row' data-id='<?= $order['order_id'] ?>'>
                    <td>#</td>
                    <td class='order-code'><?= $order['tracking_code'] ?></td>
                    <td class='oder-subtotal'><?= $currencyManager->format((float)$order['subtotal_amount'] ?? 0); ?></td>
                    <td class='order-tax'><?= $currencyManager->format((float)$order['tax_amount'] ?? 0); ?></td>
                    <td class='order-discount'><?= $currencyManager->format((float)$order['discount_amount'] ?? 0); ?></td>
                    <td class='order-shipping'><?= $currencyManager->format((float)$order['shipping_amount'] ?? 0); ?></td>
                    <td class='order-total'><?= $currencyManager->format((float)$order['total_amount'] ?? 0); ?></td>
                    <td class='order-address'><?= $order['shipping_address'] ?></td>
                    <td class='item-status'>
                      <button class='btn <?= $statusMaps[$order['order_status']]['style'] ?> btn-sm'>
                        <?= $order['order_status'] ?>
                      </button> 
                    </td>
                    <td class='order-date'><?= $order['created_at'] ?></td>
                    <td class='order-action'>
                      <div style="display: flex; gap: 10px;">
                        <button class='btn btn-info btn-view btn-sm wmg-70'><?= $statusMaps[$order['order_status']]['text'] ?></button> 
                      </div>
                    </td>
                  </tr>

                <?php endforeach; ?>

                <?php else: ?>
                  <!-- No Product Available -->
                  <td colspan="11" class='text-center'>No orders available</td>

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
  <script src="<?php echo $cacheManager->parse('scripts/order-list.js'); ?>" type="module"></script>

</body>
</html>
