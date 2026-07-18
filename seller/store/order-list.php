<?php 
  // Import Config File (Remove the demo images at launch)
  require_once __DIR__ . '/includes/config.php';

  // Import Initializer File
  require_once dirname(__DIR__) . '/includes/init.php';

  // Initialize Necessary Models
  $orderModel = $container->get(\App\Models\Order::class); 
  $storeModel = $container->get(\App\Models\Store::class); 

  // Get store and page ID from URL
  $orderStatus = isset($_GET['status']) && is_string($_GET['status']) ? (string)$_GET['status'] : null;
  $storeId     = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
  $page        = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

  // Store Products
  $orderList = $orderModel->getStoreOrdersByStatus($storeId, $orderStatus, $page);

  // Pagination parameters
  $totalPages  = $orderList['total_pages'];
  $currentPage = $orderList['page'];
  $baseUrl     = "order-list?status={$orderStatus}&id={$storeId}";

  // Get store details
  require_once 'includes/setup.php';
?> 

<!DOCTYPE html>
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Store | <?= $orderStatus ?>Orders</title>
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
              <h1><b><?= $orderStatus ?> Orders (<?= $ratingManager->format($orderList['total']) ?>)</b></h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href=".?id=<?= $storeId ?>">Home</a></li>
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
            <h3 class="card-title">All <?= $orderStatus ?> Orders</h3>
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
                  <th>Image</th>
                  <th>Name</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Stock</th>
                  <th>Tracking ID</th>
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
                      // Pick the product's first media if it exists
                      $imageUrl = $order['product_image'] ?? $defaultProductImage; 
                      
                      // For now, just use random images but switch to the code above at launch
                      // $imageUrl = $fallbackImages[array_rand($fallbackImages)];

                      // Set status maps
                      $statusMaps = [
                        'Pending'   => ['style' => 'btn-danger', 'text' => 'Mark as shipped'],
                        'Shipped'   => ['style' => 'btn-info', 'text' => 'Shipped'],
                        'Delivered' => ['style' => 'btn-success', 'text' => 'Completed'],
                      ];
                    ?>

                    <tr class='content-row' data-id='<?= $order['item_id'] ?>'>
                      <td>#</td>
                      <td class='item-image'>
                        <img class='img-fluid wmg-100 h-100' src='<?= $order['product_image'] ?>' alt='Order Image'>
                      </td>
                      <td class='item-name'><?= $order['product_name'] ?></td>
                      <td class='item-price'><?= $currencyManager->format((float)$order['price'] ?? 0); ?></td>
                      <td class='item-quantity'>
                      <?= $ratingManager->format((int)$order['quantity'] ?? 0); ?>
                      </td>
                      <td class='item-stock'>
                        <button class='btn <?= ($order['stock'] > 100) ? 'btn-info' : 'btn-danger' ?> btn-sm wmg-70'>
                          <?= $ratingManager->format((int)$order['stock'] ?? 0); ?>
                        </button> 
                      </td>
                      <td class='item-code'><?= $order['tracking_code'] ?></td>
                      <td class='item-status'>
                        <button class='btn <?= $statusMaps[$order['item_status']]['style'] ?> btn-sm'>
                          <?= $order['item_status'] ?>
                        </button> 
                      </td>
                      <td><?= $order['created_at'] ?></td>
                      <td class='item-action'>
                        <div style="display: flex; gap: 10px;">
                          <button class='btn btn-info btn-view btn-sm w-150'><?= $statusMaps[$order['item_status']]['text'] ?></button> 
                        </div>
                      </td>
                    </tr>

                  <?php endforeach; ?>

                  <?php else: ?>
                    <!-- No Product Available -->
                    <td colspan="10" class='text-center'>No orders available</td>

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

  <!-- Footer section -->
  <?php include_once 'includes/footer.php'; ?>

  <!-- Custom Scripts -->
  <script src="<?php echo $cacheManager->parse('scripts/order-list.js'); ?>" type="module"></script>

</body>
</html>
