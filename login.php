<?php
    // Import Initializer File
    require_once __DIR__ . '/includes/init.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= SITE_NAME ?> | Login</title>
    <!-- Include the head section code file -->
    <?php include_once 'includes/head.php'; ?>
</head>

<body>

    <!-- Include the header section code file -->
    <?php include_once 'includes/header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Login</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Login</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Contucts Start -->
    <div class="container-fluid contact py-5">
        <div class="container py-5">
            <div class="p-5 bg-light rounded">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <h5 class="text-primary wow fadeInUp" data-wow-delay="0.1s">Let’s Explore</h5>
                        <h1 class="display-5 mb-4 wow fadeInUp" data-wow-delay="0.3s">Login To Your Account</h1>
                        <p class="mb-4 wow fadeInUp" data-wow-delay="0.5s">
                            Log in for a wholesome e-commerce experience designed just for you! 
                        </p>
                        <form class="form-login">
                            <div class="row g-4 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="col-lg-12 col-xl-12 email-section">
                                    <div class="form-floating">
                                        <input type="email" class="form-control form-email" data-type="email" placeholder="Your Email">
                                        <label for="email">Your Email</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-12 password-section">
                                    <div class="form-floating">
                                        <input type="password" class="form-control form-password" data-type="all" placeholder="Your Password">
                                        <label for="password">Your Password</label>
                                        <i class="fas fa-eye" style="position: absolute;top: 40%;right: 2%;z-index: 10;"></i>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-12 new-password-section">
                                    <div class="form-floating">
                                        <input type="password" class="form-control form-password-new" data-type="all" placeholder="Retype Password">
                                        <label for="password">Retype Password</label>
                                        <i class="fas fa-eye" style="position: absolute;top: 40%;right: 2%;z-index: 10;"></i>
                                    </div>
                                </div>
                                <div class="col-12 otp-section">
                                    <div class="form-floating">
                                        <input type="text" class="form-control form-otp" data-type="numeric" placeholder="Enter OTP">
                                        <label for="otp">Password Reset OTP</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3 btn-login" type="submit">Login</button>
                                </div>
                                <br>
                                <br>
                                <div class="col-12">
                                    <p class="info-p">Forgot password?  Reset it <a href="#" class="reset">here</a></p>
                                    <p>Don't have an account? Sign up <a href="register?type=customer">here</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="h-100 rounded">
                            <img class="rounded w-100 h-100" style="height: 100%;" src="assets/img/policy-1.jpg" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contuct End -->

    <!-- Notification Bell Icon -->
    <div class="btn btn-lg-square notification-bell"></div>

    <!-- Include the footer section code file -->
    <?php include_once 'includes/footer.php'; ?>

    <!-- Add user defined scripts here -->
    <script src="<?php echo $cacheManager->parse('assets/js/login.js'); ?>" type="module"></script>

</body>

</html>