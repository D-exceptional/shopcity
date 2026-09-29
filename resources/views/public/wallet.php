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
    <title><?= $appName ?> | Wallet</title>
    <!-- Include Head Section -->
    <?php include_once ROOT_DIR_PATH . '/includes/public/head.php'; ?>
</head>

<body>

    <!-- Include Header Section -->
    <?php include_once ROOT_DIR_PATH . '/includes/public/header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Wallet Page</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
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
                                <input type="text" class="form-control wallet-balance" 
                                    value="<?= $isLoggedIn ? $currencyManager->format((float)$walletBalance ?? 0) : 0 ?>" 
                                    disabled
                                >
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Country</label>
                                <input type="text" class="form-control country" 
                                    value="<?= $isLoggedIn ? $userDetails['country'] : null ?>" 
                                    disabled
                                >
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
                                <h4 class="mb-4 wow fadeInUp" data-wow-delay="0.1s">Topup History</h4>
                            </div>
                          
                            <div class="table-responsive <?= $isLoggedIn ? '' : 'nmt-25' ?>">
                                <table class="table">
                                    <thead>

                                        <?php if ($isLoggedIn): ?>

                                            <tr>
                                                <th scope="col">S/N</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Reference</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Action</th>
                                            </tr>

                                        <?php endif; ?>

                                    </thead>
                                    <tbody>

                                        <?php if ($isLoggedIn): ?>

                                            <?php if (!empty($paymentList)): ?>

                                                <!-- 🛒 LOGGED IN + PAYMENTS HAS ITEMS -->
                                                <?php foreach ($paymentList as $payment): ?>

                                                    <tr class="payment-item table-item" data-id="<?= $payment['topup_id'] ?>">
                                                        <td>
                                                            <p class="mb-0 py-4">#</p>
                                                        </td>
                                                        <td scope="row">
                                                            <p class="mb-0 py-4 item-price"><?= $currencyManager->format((float)$payment['amount'] ?? 0); ?></p>
                                                        </td>
                                                        <td>
                                                            <p class="mb-0 py-4"><?= htmlspecialchars($payment['reference']) ?></p>
                                                        </td>
                                                        <td>
                                                            <p class="mb-0 py-4 item-price"><?= htmlspecialchars($payment['status']); ?></p>
                                                        </td>
                                                        <td>
                                                            <p class="mb-0 py-4 item-price"><?= htmlspecialchars($payment['created_at']); ?></p>
                                                        </td>
                                                        <td class="py-4">
                                                            <button class="btn btn-md rounded-circle bg-light border">
                                                                <i class="fa fa-expand text-blue"></i>
                                                            </button>
                                                        </td>
                                                    </tr>

                                                <?php endforeach; ?>

                                            <?php else: ?>

                                                <!-- 🧺 LOGGED IN + PAYMENT EMPTY -->
                                                <tr>
                                                    <td colspan="8" class="text-center py-5">
                                                        <i class="fa fa-wallet fa-3x text-muted mb-3"></i>
                                                        <p class="lead mb-2">You haven't made any topups yet.</p>
                                                    </td>
                                                </tr>

                                            <?php endif; ?>
                                            
                                        <?php endif; ?>

                                    </tbody>
                                </table>

                                <?php if ($isLoggedIn && !empty($paymentList)): ?>

                                    <div class="col-12 text-center wow fadeInUp mt-5 py-2" data-wow-delay="0.2s">
                                        <button type="button" class="btn btn-primary rounded-pill py-2 px-3 btn-load">Load More</button>
                                    </div>

                                <?php endif; ?>
                                
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
                    <p class="lead mb-2">Login to view wallet.</p>
                    <a href="/auth/user/login" class="btn btn-primary btn-sm">
                        <i class="fa fa-sign-in-alt me-1"></i> Log In
                    </a>
                </p>
            </div>

        <?php endif; ?>

    </div>
    <!-- Wallet Page End -->

    <!-- Include Footer Section -->
    <?php include_once ROOT_DIR_PATH . '/includes/public/footer.php'; ?>

    <!-- Flutterwave Payments -->
    <script src="https://checkout.flutterwave.com/v3.js"></script>

    <!-- Main Script -->
    <script src="<?= asset_versioned('js/public/main.js'); ?>" type="module"></script>

    <!-- Add user defined scripts here -->
    <script src="<?= asset_versioned('js/public/wallet.js'); ?>" type="module"></script>
    
</body>
</html>