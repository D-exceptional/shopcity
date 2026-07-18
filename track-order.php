<?php
    // Import Initializer File
    require_once __DIR__ . '/includes/init.php';

    // Import Default Images File (For Development Phase ONLY)
    require_once __DIR__ . '/includes/helper.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= SITE_NAME ?> | Track Order</title>
    <!-- Include the head section code file -->
    <?php include_once 'includes/head.php'; ?>
</head>

<body>

    <!-- Include the header section code file -->
    <?php include_once 'includes/header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Track Order Page</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Track Order</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Checkout Page Start -->
    <div class="container-fluid bg-light overflow-hidden py-5">
        <div class="container pt-2">
            <h1>Track Order</h1>
            <hr>
        </div>
        <?php if ($isLoggedIn): ?>
            <!-- LOGGED IN -->
            <div class="container py-5">
                <h1 class="mb-4 wow fadeInUp" data-wow-delay="0.1s">Track An Order</h1>
                <form class="form-track">
                    <div class="row g-5">
                        <div class="col-md-12 col-lg-6 col-xl-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="form-item">
                                <label class="form-label my-3">Enter Order ID</label>
                                <input type="text" class="form-control form-code" data-tag="alpha-numeric" placeholder="Enter Order Tracking Code">
                            </div>
                            <!--------------- Update Details ----------------->
                            <div class="row g-4 text-center align-items-center justify-content-flex-start pt-4">
                                <button type="submit" class="btn btn-primary border-primary py-3 px-4 text-uppercase w-50 text-primary btn-track">
                                   Track Order
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        <?php else: ?>
            <!-- 🚫 NOT LOGGED IN -->
            <div class="mx-auto text-center pb-5" style="max-width: 700px;">
                <p class="wow fadeInUp" data-wow-delay="0.2s">
                   <i class="fa fa-user-lock fa-3x text-muted mb-3"></i>
                    <p class="lead mb-2">Login to track order.</p>
                    <a href="login" class="btn btn-primary btn-sm">
                        <i class="fa fa-sign-in-alt me-1"></i> Log In
                    </a>
                </p>
            </div>
        <?php endif; ?>

    </div>
    <!-- Checkout Page End -->

    <!-- Include the footer section code file -->
    <?php include_once 'includes/footer.php'; ?>

    <!-- Add user defined scripts here -->
    <script src="<?php echo $cacheManager->parse('assets/js/track.js'); ?>" type="module"></script>
    
</body>
</html>