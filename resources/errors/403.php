<?php
    declare(strict_types=1);

    // Define Base Directory
    define(
        'ROOT_DIR_PATH', 
        dirname(__DIR__)
    );
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= $appName ?> | <?= $status ?></title>
    <!-- Include Head Section -->
    <?php include_once ROOT_DIR_PATH . '/includes/public/head.php'; ?>
</head>

<body>

    <!-- Include Header Section -->
    <?php include_once ROOT_DIR_PATH. '/includes/public/header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s"><?= $status ?> Page</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white"><?= $status ?></li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- 404 Start -->
    <div class="container-fluid py-5">
        <div class="container py-5 text-center">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <i class="bi bi-exclamation-triangle display-1 text-secondary"></i>
                    <h1 class="display-1"><?= $status ?></h1>
                    <h1 class="mb-4"><?= $message ?></h1>
                    <p class="mb-4">
                        <?= $exception ?>
                    </p>
                    <a class="btn btn-primary rounded-pill py-3 px-5" href="/">Go Back To Home</a>
                </div>
            </div>
        </div>
    </div>
    <!-- 404 End -->

    <!-- Include Footer Section -->
    <?php include_once ROOT_DIR_PATH . '/includes/public/footer.php'; ?>

</body>
</html>