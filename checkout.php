<?php
    // Import Initializer File
    require_once __DIR__ . '/includes/init.php';

    // Import Default Images File (For Development Phase ONLY)
    require_once __DIR__ . '/includes/helper.php';

    // Initialize Necessary Models
    $userModel   = $container->get(\App\Models\User::class);
    $walletModel = $container->get(\App\Models\Wallet::class); 

    // Get Products Data
    $cartItems = $isLoggedIn ? $cartModel->view($userId) : [];

    // Get total cart amount
    $totalAmount = 0;
    if (!empty($cartItems)) {
        foreach ($cartItems as $item) {
            $totalAmount += (float)$item['total_price'];
        }
    }

    // Get User Details
    $userDetails = $isLoggedIn ? $userModel->findById($userId) : null;

    // Get wallet Amount
    $walletBalance = $isLoggedIn ? $walletModel->getBalance(PAYMENT_TABLE, $userId) : 0;

    // Get User Details
    $billingDetails = $isLoggedIn ? $userModel->getBillingDetails($userId) : [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= SITE_NAME ?> | Checkout</title>
    <!-- Include the head section code file -->
    <?php include_once 'includes/head.php'; ?>
</head>

<body>

    <!-- Include the header section code file -->
    <?php include_once 'includes/header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Checkout Page</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Checkout</li>
        </ol>
    </div>
    <!-- Single Page Header End -->


    <!-- Checkout Page Start -->
    <div class="container-fluid bg-light overflow-hidden py-5">
        <?php if ($isLoggedIn): ?>
            <?php if (!empty($cartItems)): ?>
                <!-- 🛒 LOGGED IN + CART HAS ITEMS -->
                <div class="container py-5">
                    <h1 class="mb-4 wow fadeInUp" data-wow-delay="0.1s">Billing Details</h1>
                    <form action="#">
                        <div class="row g-5">
                            <div class="col-md-12 col-lg-6 col-xl-6 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <div class="form-item w-100">
                                            <label class="form-label my-3">First Name<sup>*</sup></label>
                                            <input type="text" class="form-control" value="<?= $isLoggedIn ? $userDetails['firstname'] : 'Enter Firstname' ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <div class="form-item w-100">
                                            <label class="form-label my-3">Last Name<sup>*</sup></label>
                                            <input type="text" class="form-control" value="<?= $isLoggedIn ? $userDetails['lastname'] : 'Enter Lastname' ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-item">
                                    <label class="form-label my-3">Email Address<sup>*</sup></label>
                                    <input type="email" class="form-control" value="<?= $isLoggedIn ? $userDetails['email'] : 'Enter Email' ?>" disabled>
                                </div>
                                <div class="form-item">
                                    <label class="form-label my-3">Mobile<sup>*</sup></label>
                                    <input type="tel" class="form-control" value="<?= $isLoggedIn ? $userDetails['contact'] : 'Enter Contact' ?>" disabled>
                                </div>
                                <div class="form-item">
                                    <label class="form-label my-3">Country<sup>*</sup></label>
                                    <input type="text" class="form-control" value="<?= $isLoggedIn ? $userDetails['country'] : 'Enter Country' ?>" disabled>
                                </div>
                                <!------------ Shipping Details -------------->
                                <div class="form-item form-location">
                                    <label class="form-label my-3">Delivery Address <sup>*</sup></label>
                                    <input type="text" class="form-control delivery-address" data-type="location" data-name="Address" value="<?= ($isLoggedIn && !empty($billingDetails)) ? $billingDetails['delivery_address'] : 'Enter Delivery' ?>">
                                </div>
                                <div class="form-item form-location">
                                    <label class="form-label my-3">Town/City<sup>*</sup></label>
                                    <input type="text" class="form-control delivery-city" data-type="location" data-name="City" value="<?= ($isLoggedIn && !empty($billingDetails)) ? $billingDetails['city'] : 'Enter Town or City' ?>">
                                </div>
                                <div class="form-item form-location">
                                    <label class="form-label my-3">Postcode/Zip<sup>*</sup></label>
                                    <input type="text" class="form-control delivery-postcode" data-type="numeric" data-name="Postcode" value="<?= ($isLoggedIn && !empty($billingDetails)) ? $billingDetails['postcode'] : 'Enter Postcode' ?>">
                                </div>
                                <!--
                                <div class="form-item form-note">
                                    <label class="form-label my-3">Order Notes<sup>*</sup></label>
                                    <textarea name="text" class="form-control delivery-note" data-type="alpha-numeric" spellcheck="false" cols="30" rows="11" placeholder="Order Notes (Optional)"></textarea>
                                </div>
                                <div class="form-check my-3">
                                    <input class="form-check-input" type="checkbox" id="Address-1" name="Address" value="">
                                    <label class="form-check-label" for="Address-1">Ship to a different address?</label>
                                </div>
                                -->
                                <!--------------- Update Details ----------------->
                                <div class="row g-4 text-center align-items-center justify-content-center pt-4">
                                    <button type="button" class="btn btn-primary border-primary py-3 px-4 text-uppercase w-100 text-primary btn-location">
                                        Update Details
                                    </button>
                                </div>
                                <!--------------- Create Account ----------------->
                                <div class="form-check my-3">
                                    <p>Don't have an account? <a href="register">Create an account</a></p>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6 col-xl-6 wow fadeInUp" data-wow-delay="0.3s">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr class="text-center">
                                                <th scope="col">S/N</th>
                                                <th scope="col" class="text-start">Name</th>
                                                <th scope="col">Image</th>
                                                <th scope="col">Price</th>
                                                <th scope="col">Quantity</th>
                                                <th scope="col">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($cartItems as $item): ?>
                                                <?php
                                                    // Prefer actual product image, fallback to random image
                                                    $imageUrl = $item['product_image'] ?? null;
                                                ?>

                                                <tr class="text-center checkout-item" data-pid="<?= $item['product_id'] ?>" data-sid="<?= $item['store_id'] ?>">
                                                    <td class="py-4">
                                                        #
                                                    </td>
                                                    <th scope="row" class="text-start py-4">
                                                        <?= htmlspecialchars($item['product_name']) ?>
                                                    </th>
                                                    <td class="py-4">
                                                        <img src="<?= htmlspecialchars($imageUrl) ?>" class="img-fluid rounded" alt="Product Image"/>
                                                    </td>
                                                    <td class="py-4 item-price">
                                                        <?= $currencyManager->format((float)$item['product_price'] ?? 0); ?>
                                                    </td>
                                                    <td class="py-4 text-center item-quantity">
                                                        <?= $item['quantity'] ?? 1 ?>
                                                    </td>
                                                    <td class="py-4 item-total">
                                                        <?= $currencyManager->format((float)$item['total_price'] ?? 0); ?>
                                                    </td>
                                                </tr>

                                            <?php endforeach; ?>

                                            <!--------------------- Subtotal & Shipping ----------------------->
                                            <tr>
                                                <th scope="row">
                                                </th>
                                                <td class="py-4"></td>
                                                <td class="py-4"></td>
                                                <td class="py-4">
                                                    <p class="mb-0 text-dark py-2">Subtotal</p>
                                                </td>
                                                <td class="py-4">
                                                    <div class="py-2 text-center border-bottom border-top">
                                                        <p class="mb-0 text-dark subtotal"><?= $currencyManager->format((float)$totalAmount ?? 0); ?></p>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">
                                                </th>
                                                <td class="py-4">
                                                    <p class="mb-0 text-dark py-4">Logistics</p>
                                                </td>
                                                <td colspan="3" class="py-4">
                                                    <div class="form-check text-start">
                                                        <input type="radio" class="form-check-input bg-primary border-0 flat-rate" id="Shipping-2" name="Shipping" value="<?= FLAT_RATE ?>">
                                                        <label class="form-check-label" for="Shipping-2">Flat rate: <?= $currencyManager->format((float)FLAT_RATE ?? 0); ?></label>
                                                    </div>
                                                    <div class="form-check text-start">
                                                        <input type="radio" class="form-check-input bg-primary border-0 shipping" id="Shipping-1" name="Shipping" value="0">
                                                        <label class="form-check-label" for="Shipping-1">Free Shipping: <?= $currencyManager->format(0); ?></label>
                                                    </div>
                                                    <div class="form-check text-start">
                                                        <input type="radio" class="form-check-input bg-primary border-0 pickup" id="Shipping-3" name="Shipping" value="1000">
                                                        <label class="form-check-label" for="Shipping-3">Local Pickup: <?= $currencyManager->format(1000); ?></label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">
                                                </th>
                                                <td class="py-4">
                                                    <p class="mb-0 text-dark text-uppercase py-2">TOTAL</p>
                                                </td>
                                                <td class="py-4"></td>
                                                <td class="py-4"></td>
                                                <td class="py-4">
                                                    <div class="py-2 text-center border-bottom border-top">
                                                        <p class="mb-0 text-dark checkout-total"><?= $currencyManager->format((float)$totalAmount + (float)FLAT_RATE); ?></p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row gap-6 coupon-section">
                                    <div class="col-5 d-flex align-items-center justify-content-center">
                                        <input type="text" class="form-control border-0 border-bottom rounded py-3 coupon-code" data-type="alpha-numeric" placeholder="Have Coupon?">
                                    </div>
                                    <div class="col-7 d-flex align-items-center justify-content-center">
                                        <button class="btn btn-primary rounded-pill px-4 py-3 btn-coupon" type="button">Apply Coupon</button>
                                    </div>
                                </div>
                                 <div class="row g-0 text-center align-items-center justify-content-center border-bottom py-2">
                                    <div class="col-12">
                                        <div class="form-check text-start my-2">
                                            <input type="checkbox" class="form-check-input bg-primary border-0" id="Transfer-1" name="Transfer" value="Transfer" checked disabled>
                                            <label class="form-check-label" for="Transfer-1">Wallet Balance</label>
                                        </div>
                                        <p class="text-start text-dark wallet-balance" data-balance="<?= $walletBalance; ?>">
                                            Your current balance is: <b><?= $currencyManager->format((float)$walletBalance ?? 0); ?></b>,
                                            which is <?= $walletBalance >= ((float)$totalAmount + (float)FLAT_RATE) ? 'sufficient' : 'insufficient' ?>
                                            for your total order payment of: <b><?= $currencyManager->format((float)$totalAmount + (float)FLAT_RATE); ?></b>
                                        </p>
                                    </div>
                                </div>
                                <div class="row g-4 text-center align-items-center justify-content-center pt-4">
                                    <button type="button" class="btn btn-primary border-primary py-3 px-4 text-uppercase w-100 text-primary btn-checkout">
                                        <?= $walletBalance >= ((float)$totalAmount + (float)FLAT_RATE) ? 'Place Order' : 'Topup Wallet' ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
            <?php else: ?>
                <!-- 🧺 LOGGED IN + CART EMPTY -->
                <div class="mx-auto text-center pb-5" style="max-width: 700px;">
                    <p class="wow fadeInUp" data-wow-delay="0.2s">
                        <i class="fa fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="lead mb-2">Your cart is empty.</p>
                        <a href="/" class="btn btn-primary btn-sm">
                            <i class="fa fa-shopping-bag me-1"></i> Start Shopping
                        </a>
                    </p>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- 🚫 NOT LOGGED IN -->
            <div class="mx-auto text-center pb-5" style="max-width: 700px;">
                <p class="wow fadeInUp" data-wow-delay="0.2s">
                   <i class="fa fa-user-lock fa-3x text-muted mb-3"></i>
                    <p class="lead mb-2">Login to checkout.</p>
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
    <script src="<?php echo $cacheManager->parse('assets/js/checkout.js'); ?>" type="module"></script>

</body>
</html>