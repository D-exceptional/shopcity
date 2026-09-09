<?php 
  declare(strict_types=1);
  
  // -------------------------------------------------
  // Base Directory 
  // -------------------------------------------------
  define('ROOT_DIR_PATH', dirname(__DIR__, 2));

  // Pagination Parameters
  $totalPages  = $orderList['total_pages'];
  $currentPage = $orderList['page'];

  // Base Pagination URL
  $baseUrl = "/admin/order/status/{$status}";
?> 

<!DOCTYPE html>
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <title>Admin | <?= $status ?> Orders</title>
  <?php include_once ROOT_DIR_PATH . '/includes/shared/head.php'; ?>
</head>
<body class="hold-transition sidebar-mini">
  <!-- Site wrapper -->
  <div class="wrapper">
    <!-- Navbar -->
    <?php include_once ROOT_DIR_PATH . '/includes/admin/header.php'; ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include_once ROOT_DIR_PATH . '/includes/admin/sidebar.php'; ?>
    <!-- / Main Sidebar Container -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" data-status="<?= $status ?>">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6 header-count">
              <h1><b><?= $status ?> Orders (<?= $numberManager->format($orderList['total']) ?>)</b></h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Home</a></li>
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
            <h3 class="card-title">All <?= $status ?> Orders</h3>
            <div class="card-tools">
              <!-- Previous button -->
              <button type="button" class="btn btn-info btn-tool btn-previous wb-50 hide">
                <a href="<?= ($currentPage > 1) ? page_url($currentPage - 1, $baseUrl) : '#' ?>" 
                  class="rounded <?= ($currentPage <= 1) ? 'disabled' : '' ?>"
                >
                  &laquo;
                </a>
              </button>
              <!-- Next button -->
              <button type="button" class="btn btn-info btn-tool btn-next wb-50 hide">
                <a href="<?= ($currentPage < $totalPages) ? page_url($currentPage + 1, $baseUrl) : '#' ?>" 
                  class="rounded <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>"
                >
                  &raquo;
                </a>
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

                  <!-- Display Orders -->
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
                    <!-- No Orders Available -->
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
  </div>

  <!-- Footer section -->
  <?php include_once ROOT_DIR_PATH . '/includes/shared/footer.php'; ?>

  <!-- Main Script -->
  <script src="<?= asset_versioned('js/admin/main.js'); ?>" type="module"></script>

  <!-- Custom Scripts -->
  <script src="<?= asset_versioned('js/admin/order-list.js'); ?>" type="module"></script>

</body>
</html>
