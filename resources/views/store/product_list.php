<?php 
  declare(strict_types=1);
  
 // -------------------------------------------------
  // Base Directory 
  // -------------------------------------------------
  define('ROOT_DIR_PATH', dirname(__DIR__, 2));

  // Pagination Parameters
  $totalPages  = $storeProducts['total_pages'];
  $currentPage = $storeProducts['page'];
  $baseUrl     = "/store/{$storeId}/products";
?> 

<!DOCTYPE html>
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Store | Products</title>
  <?php include_once ROOT_DIR_PATH . '/includes/shared/head.php'; ?>
</head>
<body class="hold-transition sidebar-mini">
  <!-- Site wrapper -->
  <div class="wrapper">
    <!-- Navbar -->
    <?php include_once ROOT_DIR_PATH . '/includes/store/header.php'; ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include_once ROOT_DIR_PATH . '/includes/store/sidebar.php'; ?>
    <!-- / Main Sidebar Container -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" data-storeid="<?= $storeId ?>">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6 header-count">
              <h1><b>All Products (<?= $numberManager->format($storeProducts['total']) ?>)</b></h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/store/<?= $storeId ?>/dashboard">Home</a></li>
                <li class="breadcrumb-item active">Products</li>
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
            <h3 class="card-title">All Products</h3>
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

              <?php if (!empty($storeProducts['products'])): ?>

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
                  <th>Name</th>
                  <th>Description</th>
                  <th>Category</th>
                  <th>Sub Category</th>
                  <th>Price</th>
                  <th>Slash Price</th>
                  <th>Stock</th>
                  <th>Color</th>
                  <th>Visibility</th>
                  <th>Uploaded</th>
                  <th>Updated</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>

                <?php if (!empty($storeProducts['products'])): ?>

                  <!-- Display All Products -->
                  <?php foreach ($storeProducts['products'] as $product): ?>

                    <?php
                      // Pick the product's first media if it exists
                      $imageUrl = $product['media'][0]['media_url'] ?? null; 

                      // Manage decription
                      $fullDescription = $product['product_description'] ?? '';
                      $shortDescription = strlen($fullDescription) > 50 ? substr($fullDescription, 0, 50) . '...' : $fullDescription;
                    ?>

                    <tr class='content-row' data-id='<?=$product['product_id'] ?>'>
                      <td>#</td>
                      <td class='product-name'><?= $product['product_name'] ?></td>
                      <td class='product-description'><?= $shortDescription ?></td>
                      <td class='product-category'><?= $product['category'] ?></td>
                      <td class='product-subcategory'><?= $product['sub_category'] ?></td>
                      <td class='product-price'><?= $currencyManager->format((float)$product['product_price'] ?? 0); ?></td>
                      <td class='product-slash'><?= $currencyManager->format((float)$product['slash_price'] ?? 0); ?></td>
                      <td class='product-stock'>
                        <button class='btn <?= ($product['stock'] > 100) ? 'btn-info' : 'btn-danger' ?> btn-sm wmg-70'>
                          <?= $numberManager->format((int)$product['stock'] ?? 0); ?>
                        </button> 
                      </td>
                      <td class='product-color'><?= $product['color'] ?></td>
                      <td>
                        <button class='btn <?= ($product['visibility'] === 'Visible') ? 'btn-info' : 'btn-danger' ?> btn-sm'>
                          <?= $product['visibility'] ?>
                        </button> 
                      </td>
                      <td><?= $product['created_at'] ?></td>
                      <td><?= $product['updated_at'] ?></td>
                      <td class='product-action'>
                        <div style="display: flex; gap: 10px;">
                          <button class='btn btn-info btn-view btn-sm wmg-70'>Details</button> 
                          <button class='btn btn-info btn-edit btn-sm wmg-70'>Edit</button> 
                          <button class='btn btn-danger btn-delete btn-sm wmg-70'>Delete</button> 
                        </div>
                      </td>
                    </tr>

                  <?php endforeach; ?>

                  <?php else: ?>

                    <!-- No Product Available -->
                    <td colspan="13" class='text-center'>No product available</td>

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
  <script src="<?= asset_versioned('js/store/main.js'); ?>" type="module"></script>

  <!-- Custom Scripts -->
  <script src="<?= asset_versioned('js/store/product-list.js'); ?>" type="module"></script>

</body>
</html>
