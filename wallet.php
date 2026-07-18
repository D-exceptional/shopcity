<?php
    // Import Initializer File
    require_once __DIR__ . '/includes/init.php';

    // Import Default Images File (For Development Phase ONLY)
    require_once __DIR__ . '/includes/helper.php';

    // Initialize Necessary Models
    $userModel   = $container->get(\App\Models\User::class);
    $walletModel = $container->get(\App\Models\Wallet::class);

    // Get wallet Amount
    $walletBalance = $isLoggedIn ? $walletModel->getBalance(PAYMENT_TABLE, $userId) : 0;

    // Get User Details
    $userDetails  = $isLoggedIn ? $userModel->findById($userId) : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= SITE_NAME ?> | Wallet</title>
    <!-- Include the head section code file -->
    <?php include_once 'includes/head.php'; ?>
</head>

<body>

    <!-- Include the header section code file -->
    <?php include_once 'includes/header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Wallet Page</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Wallet</li>
        </ol>
    </div>
    <!-- Single Page Header End -->


    <!-- Checkout Page Start -->
    <div class="container-fluid bg-light overflow-hidden py-5">
        <div class="container pt-2">
            <h1>My Wallet</h1>
            <hr>
        </div>
        <?php if ($isLoggedIn): ?>
            <!-- LOGGED IN -->
            <div class="container py-5">
                <h4 class="mb-4 wow fadeInUp" data-wow-delay="0.1s">Wallet Details</h4>
                <form action="#">
                    <div class="row g-5">
                        <div class="col-md-12 col-lg-6 col-xl-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="form-item">
                                <label class="form-label my-3">Balance</label>
                                <input type="text" class="form-control wallet-balance" value="<?= $isLoggedIn ? $ratingManager->format((float)$walletBalance / BASE_CONVERSION_RATE ?? 0) : 0 ?> Coins" disabled>
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Country</label>
                                <input type="text" class="form-control country" value="<?= $isLoggedIn ? $userDetails['country'] : null ?>" disabled>
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Topup Amount</label>
                                <input type="text" class="form-control topup-amount" data-tag="numeric" placeholder="Enter the amount to topup">
                            </div>
                            <!--------------- Update Details ----------------->
                            <div class="row g-4 text-center align-items-center justify-content-flex-start pt-4">
                                <button type="button" class="btn btn-primary border-primary py-3 px-4 text-uppercase w-50 text-primary btn-topup">
                                   Topup Wallet
                                </button>
                            </div>
                        </div>
                         <div class="col-md-12 col-lg-6 col-xl-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="form-item">
                                <h4 class="mb-4 wow fadeInUp" data-wow-delay="0.1s">Coin Calculator</h4>
                                <input type="text" class="form-control wallet-balance" value="1 Coin = ₦100.00" disabled>
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Enter Amount (in coins)</label>
                                <input type="text" class="form-control test-amount" data-tag="numeric">
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Result (in ₦aira)</label>
                                <input type="text" class="form-control result-amount" data-tag="numeric" disabled>
                            </div>
                            <!--------------- Update Details ----------------->
                        </div>
                    </div>
                </form>
            </div>

        <?php else: ?>
            <!-- 🚫 NOT LOGGED IN -->
            <div class="mx-auto text-center pb-5" style="max-width: 700px;">
                <p class="wow fadeInUp" data-wow-delay="0.2s">
                   <i class="fa fa-user-lock fa-3x text-muted mb-3"></i>
                    <p class="lead mb-2">Login to view wallet.</p>
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

    <!-- Flutterwave Payments -->
    <script src="https://checkout.flutterwave.com/v3.js"></script>

    <!-- Add user defined scripts here -->
    <script src="<?php echo $cacheManager->parse('assets/js/wallet.js'); ?>" type="module"></script>
    
</body>
</html>