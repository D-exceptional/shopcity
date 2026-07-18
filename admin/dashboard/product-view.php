<?php 
    // Import Config File (Remove the demo images at launch)
    require_once __DIR__ . '/includes/config.php';

    // Import Initializer File
    require_once __DIR__ . '/includes/init.php';

    // Initialize Necessary Models
    $productModel = $container->get(\App\Models\Product::class);

    // Get product and store ID from URL
    $productId = isset($_GET['productId']) && is_numeric($_GET['productId']) ? (int)$_GET['productId'] : null;

    // Get product details
    $productDetails = $productModel->findOne($productId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Product Details</title>
    <?php include 'includes/head.php'; ?>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include 'includes/header.php'; ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include 'includes/sidebar.php'; ?>
        <!-- / Main Sidebar Container -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1><b>Product Details</b></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href=".">Home</a></li>
                                <li class="breadcrumb-item active">Product Details</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content content-view">
                <div class="container-fluid">
                    <div class="row h-550">
                        <!-- /.col -->
                        <div class="col-md-9">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Product Details</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input data-type="alpha-numeric" type="text" name="name" class="form-control form-name form-data" value="<?= $productDetails['product_name'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea data-type="text" name="description" class="form-control form-description form-data">
                                            <?= $productDetails['product_description'] ?>
                                        </textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="category">Category</label>
                                        <select name="category" class="form-control form-category">
                                            <option value="<?= $productDetails['category'] ?>"><?= $productDetails['category'] ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="sub-category">Sub Category</label>
                                        <select name="sub-category" class="form-control form-sub-category">
                                            <option value="<?= $productDetails['sub_category'] ?>"><?= $productDetails['sub_category'] ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Price</label>
                                        <input data-type="number" type="text" name="price" class="form-control form-price form-data" value="<?= $currencyManager->format((float)$productDetails['product_price'] ?? 0); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="slash-price">Slash Price</label>
                                        <input data-type="number" type="text" name="slash-price" class="form-control form-slash-price form-data" value="<?= $currencyManager->format((float)$productDetails['slash_price'] ?? 0); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="stock">Stock Available</label>
                                        <input data-type="number" type="text" name="stock" class="form-control form-stock form-data" value=" <?= $ratingManager->format((int)$productDetails['stock'] ?? 0); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="color">Color</label>
                                        <input data-type="alpha" type="text" name="color" class="form-control form-color form-data" value="<?= $productDetails['color'] ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="visibility">Visibility</label>
                                        <select name="visibility" class="form-control form-visibility">
                                            <option value="<?= $productDetails['visibility'] ?>" selected><?= $productDetails['visibility'] ?></option>
                                            <?php if (!empty($productDetails['visibility'])): ?>
                                                <?php
                                                    $reverseVisibility = $productDetails['visibility'] === 'Visible' ? 'Hidden' : 'Visible';
                                                ?>
                                                <option value="<?= $reverseVisibility ?>"><?= $reverseVisibility ?></option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="mediaInput">Media</label>
                                        <div class="row media-row">
                                            <?php if (!empty($productDetails['media'])): ?>
                                                <!-- Display All Media -->
                                                <?php foreach ($productDetails['media'] as $media): ?>
                                                    <?php
                                                        $file = in_array($media['media_type'], ["image", "image/jpeg", "image/jpg", "image/png"]) 
                                                        ? "<img src='{$media['media_url']}' alt='Product Image'>"
                                                        : "<video src='{$media['media_url']}' controls></video>";
                                                    ?>
                                                    <div class="media-card" data-id="<?= $media['media_id'] ?>" data-type="<?= $media['media_type'] ?>">
                                                        <div class="media-content">
                                                            <?= $file ?>
                                                        </div>
                                                        <div class="media-icons">
                                                            <i class="fa fa-expand"></i>
                                                            <i class="fa fa-retweet"></i>
                                                            <i class="fa fa-trash"></i>
                                                            <input type="file" class="form-control" accept="image/*,video/*"  hidden>
                                                        </div>
                                                        <div class="card-overlay"><p>Processing...</p></div>
                                                    </div>

                                                <?php endforeach; ?>

                                                <?php else: ?>
                                                <!-- No Media Available -->
                                                <input type="text"class="form-control" value="No media found for this product" disabled>

                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-12">
                                            <input type="button" value="Update Details" class="btn btn-success float-left btn-update">
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-footer -->
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
    </div>
    <!-- ./wrapper -->

    <!-- Footer section -->
    <?php include 'includes/footer.php'; ?>

    <div class='overlay'>
        <div class="zoom-container">
            <!--<img src='../../assets/img/logo.jpg' alt='Product Image'>-->
        </div>
        <div class='close-overlay'>
            <i class='fa fa-times' aria-hidden='true'></i>
        </div>
    </div>

    <!-- Page specific script -->
    <script>
        $(function () {
            //Add text editor
            $('.form-description').summernote()
        })
    </script>

   <!-- Custom Scripts -->
   <script src="<?php echo $cacheManager->parse('scripts/product-details.js'); ?>" type="module"></script>
</body>
</html>