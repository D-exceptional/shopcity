<?php 
    // Import Config File (Remove the demo images at launch)
    require_once __DIR__ . '/includes/config.php';

    // Import Initializer File
    require_once __DIR__ . '/includes/init.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vendor | Create Store</title>
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
                        <h1><b>Create Store</b></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href=".">Home</a></li>
                        <li class="breadcrumb-item active">Create Store</li>
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
                            <h3 class="card-title">Supply Store Data</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" class="form-control form-name form-text">
                            </div>
                             <div class="form-group">
                                <label for="file">Image</label>
                                <input type="file" name="file" class="form-control form-file">
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea data-type="all" name="description" class="form-control form-description form-text"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select name="type" class="form-control form-type">
                                    <option value="">Select Type</option>
                                    <option value="Regular">Regular</option>
                                    <option value="Reselling">Reselling</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="delivery">Delivery</label>
                                <select name="delivery" class="form-control form-delivery">
                                    <option value="">Select duration</option>
                                    <option value="I Day">1 Day</option>
                                    <option value="3 Days">3 Days</option>
                                    <option value="7 Days">7 Days</option>
                                    <option value="14 Days">14 Days</option>
                                </select>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-12">
                                    <input type="button" value="Create Store" class="btn btn-success float-left btn-create">
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
    
    <!-- Page specific script -->
    <script>
        $(function () {
            //Add text editor
            $('.form-description').summernote()
        })
    </script>

   <!-- Custom Scripts -->
   <script src="<?php echo $cacheManager->parse('./scripts/store-create.js'); ?>" type="module"></script>
   
</body>
</html>