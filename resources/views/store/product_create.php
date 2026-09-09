<?php 
    declare(strict_types=1);
    
    // -------------------------------------------------
    // Base Directory 
    // -------------------------------------------------
    define('ROOT_DIR_PATH', dirname(__DIR__, 2));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Store | Create Product</title>
    <?php include_once ROOT_DIR_PATH . '/includes/shared/head.php'; ?>
</head>
<body class="hold-transition sidebar-mini">
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
                    <div class="col-sm-6">
                        <h1><b>Create Product</b></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/store/<?= $storeId ?>/dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Create Product</li>
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
                            <h3 class="card-title">Supply Product Data</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input data-type="alpha-numeric" type="text" name="name" class="form-control form-name form-data">
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea data-type="text" name="description" class="form-control form-description form-data"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select name="category" class="form-control form-category">
                                    <option value="">Select category</option>

                                    <?php if (!empty($allCategories)): ?>

                                        <!-- Display All Products -->
                                        <?php foreach ($allCategories as $category): ?>

                                            <option value="<?= $category['category_name'] ?>">
                                                <?= $category['category_name'] ?>
                                            </option>

                                            <?php endforeach; ?>

                                            <?php else: ?>

                                                <!-- No Product Available -->
                                                <option value="">No category available</option>

                                    <?php endif; ?>

                                </select>
                            </div>
                            <div class="form-group">
                                <label for="sub-category">Sub Category</label>
                                <select name="sub-category" class="form-control form-sub-category">
                                    <option value="">Select sub category</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input data-type="number" type="text" name="price" class="form-control form-price form-data">
                            </div>
                            <div class="form-group">
                                <label for="slash-price">Slash Price</label>
                                <input data-type="number" type="text" name="slash-price" class="form-control form-slash-price form-data">
                            </div>
                            <div class="form-group">
                                <label for="stock">Stock Available</label>
                                <input data-type="number" type="text" name="stock" class="form-control form-stock form-data">
                            </div>
                            <div class="form-group">
                                <label for="color">Color</label>
                                <input data-type="alpha" type="text" name="color" class="form-control form-color form-data">
                            </div>
                            <div class="form-group">
                                <label for="mediaInput">Image / Video</label>
                                <input 
                                    type="file" 
                                    id="mediaInput" 
                                    accept="image/*,video/*"  
                                    name="files[]" 
                                    class="form-control form-file" 
                                    multiple
                                />
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-12">
                                    <input type="button" value="Create Product" class="btn btn-success float-left btn-create">
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
    <?php include_once ROOT_DIR_PATH . '/includes/shared/footer.php'; ?>

    <!-- Main Script -->
    <script src="<?= asset_versioned('js/store/main.js'); ?>" type="module"></script>

    <!-- Summernote -->
    <script src="<?= asset('plugins/summernote/summernote-bs4.min.js') ?>"></script>

    <!-- Page specific script -->
    <script>
        $(function () {
            //Add text editor
            $('.form-description').summernote()
        })
    </script>

   <!-- Custom Scripts -->
   <script src="https://widget.cloudinary.com/v2.0/global/all.js"></script>
   <script src="<?= asset_versioned('js/store/product-create.js'); ?>" type="module"></script>
   
</body>
</html>