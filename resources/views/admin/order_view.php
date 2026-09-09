<?php 
  declare(strict_types=1);
  
  // -------------------------------------------------
  // Base Directory 
  // -------------------------------------------------
  define('ROOT_DIR_PATH', dirname(__DIR__, 2));
?> 

<!DOCTYPE html>
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <title>Admin | Order Details</title>
  <?php include ROOT_DIR_PATH . '/includes/shared/head.php'; ?>
</head>
<body class="hold-transition sidebar-mini">
  <!-- Site wrapper -->
  <div class="wrapper">
    <!-- Navbar -->
    <?php include ROOT_DIR_PATH . '/includes/admin/header.php'; ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include ROOT_DIR_PATH . '/includes/admin/sidebar.php'; ?>
    <!-- / Main Sidebar Container -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6 header-count">
              <h1><b>Total Items (<?= $numberManager->format(count($orderDetails['items'])) ?>)</b></h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Home</a></li>
                <li class="breadcrumb-item active">Order Details</li>
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
            <h3 class="card-title">Order Details</h3>
            <div class="card-tools">
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
                  <th>Tracking ID</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>

                <?php if (!empty($orderDetails['items'])): ?>

                  <!-- Display All Products -->
                  <?php foreach ($orderDetails['items'] as $order): ?>

                    <?php
                      // Set status maps
                      $statusMaps = [
                        'Pending'   => ['style' => 'btn-danger', 'text' => 'View'],
                        'Shipped'   => ['style' => 'btn-info', 'text' => 'View'],
                        'Delivered' => ['style' => 'btn-success', 'text' => 'View'],
                      ];
                    ?>

                    <tr class='content-row' data-id='<?= $order['item_id'] ?>' data-sid='<?= $order['store_id'] ?>'>
                      <td>#</td>
                      <td class='item-image'>
                        <img class='img-fluid wmg-100 h-100' src='<?= $order['product_image'] ?>' alt='Order Image'>
                      </td>
                      <td class='item-name'><?= $order['product_name'] ?></td>
                      <td class='item-price'><?= $currencyManager->format((float)$order['price'] ?? 0); ?></td>
                      <td class='item-quantity'>
                      <?= $ratingManager->format((int)$order['quantity'] ?? 0); ?>
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
                          <button class='btn <?= $statusMaps[$order['item_status']]['style'] ?> btn-view btn-sm wmg-70'>
                              <?= $statusMaps[$order['item_status']]['text'] ?>
                          </button> 
                          <?php if (in_array($order['item_status'], ['Shipped', 'Delivered']) && $order['finalized'] === 'No'): ?>
                              <button class='btn btn-success btn-sm btn-mark wmg-70'>Finalize</button> 
                          <?php endif; ?>
                        </div>
                      </td>
                    </tr>

                  <?php endforeach; ?>

                  <?php else: ?>
                    <!-- No Product Available -->
                    <td colspan="9" class='text-center'>No order items available</td>

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
  <?php include ROOT_DIR_PATH . '/includes/shared/footer.php'; ?>

  <!-- Main Script -->
  <script src="<?= asset_versioned('js/admin/main.js'); ?>" type="module"></script>

  <!-- Custom Scripts -->
  <script src="<?= asset_versioned('js/admin/order-view.js'); ?>" type="module"></script>

</body>
</html>
