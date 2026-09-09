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
    <title><?= $appName ?> | Profile</title>
    <!-- Include Head Section -->
    <?php include_once ROOT_DIR_PATH . '/includes/public/head.php'; ?>
</head>

<body>

    <!-- Include Header Section -->
    <?php include_once ROOT_DIR_PATH . '/includes/public/header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Profile Page</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Profile</li>
        </ol>
    </div>
    <!-- Single Page Header End -->


    <!-- Checkout Page Start -->
    <div class="container-fluid bg-light overflow-hidden py-5">
        <div class="container pt-2">
            <h1>My Profile</h1>
            <hr>
        </div>

        <?php if ($isLoggedIn): ?>

            <!-- LOGGED IN -->
            <div class="container py-5">
                <h4 class="mb-4 wow fadeInUp" data-wow-delay="0.1s">Account Details</h4>
                <form class="form-details">
                    <div class="row g-5">
                        <div class="col-md-12 col-lg-6 col-xl-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="row">
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-item w-100">
                                        <label class="form-label my-3">First Name</label>
                                        <input type="text" class="form-control" 
                                            value="<?= $isLoggedIn ? $userDetails['firstname'] : 'Enter Firstname' ?>" 
                                            disabled
                                        >
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-item w-100">
                                        <label class="form-label my-3">Last Name</label>
                                        <input type="text" class="form-control" 
                                            value="<?= $isLoggedIn ? $userDetails['lastname'] : 'Enter Lastname' ?>" 
                                            disabled
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Email Address</label>
                                <input type="email" class="form-control" 
                                    value="<?= $isLoggedIn ? $userDetails['email'] : 'Enter Email' ?>" 
                                    disabled
                                >
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Mobile</label>
                                <input type="tel" class="form-control" 
                                    value="<?= $isLoggedIn ? $userDetails['contact'] : 'Enter Contact' ?>" 
                                    disabled
                                >
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Country</label>
                                <input type="text" class="form-control" 
                                    value="<?= $isLoggedIn ? $userDetails['country'] : 'Enter Country' ?>" 
                                    disabled
                                >
                            </div>
                            <br>
                            <!------------ Shipping Details -------------->
                            <h4 class="mb-4 wow fadeInUp" data-wow-delay="0.1s">Billing Details</h4>
                            <div class="form-item">
                                <label class="form-label my-3">Delivery Address </label>
                                <input type="text" class="form-control form-location form-address" 
                                    data-type="location" 
                                    data-name="Address" 
                                    placeholder="House Number Street Name" 
                                    value="<?= ($isLoggedIn && !empty($billingDetails)) ? $billingDetails['delivery_address'] : 'Enter Delivery Address' ?>"
                                >
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Town/City</label>
                                <input type="text" class="form-control form-location form-city" 
                                    data-type="location" 
                                    data-name="City" 
                                    value="<?= ($isLoggedIn && !empty($billingDetails)) ? $billingDetails['city'] : 'Enter City' ?>"
                                >
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Postcode/Zip</label>
                                <input type="text" class="form-control form-location form-code" 
                                    data-type="numeric" 
                                    data-name="Postcode" 
                                    value="<?= ($isLoggedIn && !empty($billingDetails)) ? $billingDetails['postcode'] : 'Enter Postcode' ?>"
                                >
                            </div>
                            <br>
                            <!--------------- Update Details ----------------->
                            <div class="row g-4 text-center align-items-center justify-content-flex-start pt-4">
                                <button type="submit" class="btn btn-primary border-primary py-3 px-4 text-uppercase w-100 text-primary btn-update">
                                    Update Details
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
                    <p class="lead mb-2">Login to view profile.</p>
                    <a href="/auth/user/login" class="btn btn-primary btn-sm">
                        <i class="fa fa-sign-in-alt me-1"></i> Log In
                    </a>
                </p>
            </div>

        <?php endif; ?>

    </div>
    <!-- Profile Page End -->

    <!-- Include Footer Section -->
    <?php include_once ROOT_DIR_PATH . '/includes/public/footer.php'; ?>

    <!-- Main Script -->
    <script src="<?= asset_versioned('js/public/main.js'); ?>" type="module"></script>

    <!-- Add user defined scripts here -->
    <script src="<?= asset_versioned('js/public/profile.js'); ?>" type="module"></script>

</body>
</html>