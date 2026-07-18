<?php 
    // Import Config File (Remove the demo images at launch)
    require_once __DIR__ . '/includes/config.php';

    // Import Initializer File
    require_once dirname(__DIR__) . '/includes/init.php';

    // Initialize Necessary Models
    $storeModel = $container->get(\App\Models\Store::class); 

    // Get store and page ID from URL
    $storeId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

    // Get store details
    require_once 'includes/setup.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Store | Create Coupon</title>
    <?php include_once 'includes/head.php'; ?>
</head>
<body class="hold-transition sidebar-mini">
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
                        <h1><b>Create Coupon</b></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href=".?id=<?= $storeId ?>">Home</a></li>
                        <li class="breadcrumb-item active">Create Coupon</li>
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
                            <h3 class="card-title">Supply Coupon Data</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Code</label>
                                <input data-type="all" type="text" name="name" class="form-control form-code form-data">
                            </div>
                            <div class="form-group">
                                <label for="discount">Discount (%)</label>
                                <select name="discount" class="form-control form-discount"></select>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-12">
                                    <input type="button" value="Create Coupon" class="btn btn-success float-left btn-create">
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
    <?php include_once 'includes/footer.php'; ?>

   <!-- Custom Scripts -->
   <script src="<?php echo $cacheManager->parse('scripts/coupon-create.js'); ?>" type="module"></script>
   
</body>
</html>