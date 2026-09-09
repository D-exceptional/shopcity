<?php
    declare(strict_types=1);

    // -------------------------------------------------
    // Base Directory 
    // -------------------------------------------------
    define('ROOT_DIR_PATH', dirname(__DIR__, 3));
    
    // Import Initializer File
    require_once ROOT_DIR_PATH . '/includes/public/init.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= config('app.name') ?> | Register</title>
    <!-- Include Head Section -->
    <?php include_once  ROOT_DIR_PATH . '/includes/public/head.php'; ?>
</head>

<body>

    <!-- Include Header Section -->
    <?php include_once ROOT_DIR_PATH . '/includes/public/header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5" data-role="<?= $role ?>">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Sign Up</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Sign Up</li>
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
                        <h1 class="display-5 mb-4 wow fadeInUp" data-wow-delay="0.3s">Sign Up</h1>
                        <p class="mb-4 wow fadeInUp" data-wow-delay="0.5s">
                            Get an account to experience all benefits the platform has to offer. It has never been so much better!
                        </p>
                        <form class="form-signup">
                            <div class="row g-4 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating form-text">
                                        <input type="text" class="form-control form-firstname" data-type="name" placeholder="First Name">
                                        <label for="name">Your First Name</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating form-text">
                                        <input type="text" class="form-control form-lastname" data-type="name" placeholder="Last Name">
                                        <label for="name">Your Last Name</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating form-text">
                                        <input type="email" class="form-control form-email" data-type="email" placeholder="Email">
                                        <label for="email">Your Email</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating form-text">
                                        <input type="phone" class="form-control form-phone" data-type="phone" placeholder="Phone">
                                        <label for="phone">Your Contact</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6 password-section">
                                    <div class="form-floating form-text">
                                        <input type="password" class="form-control form-password" data-type="all" placeholder="Password">
                                        <label for="password">Your Password</label>
                                        <i class="fas fa-eye" style="position: absolute;top: 40%;right: 2%;z-index: 10;"></i>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <select name="country" class="form-control bg-white py-3 form-country">
                                        <option value="">Select Your Country</option>
                                    </select>
                                </div>

                                <?php if ($role ==='Vendor'): ?>

                                     <div class="col-lg-12 col-xl-12">
                                        <div class="form-floating form-text">
                                            <input type="text" class="form-control form-state" data-type="alpha" placeholder="State">
                                            <label for="state">Your State Of Origin</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-xl-12">
                                        <div class="form-floating">
                                            <input type="file" class="form-control bg-white form-file" placeholder="ID">
                                            <label for="file">Government Issued ID</label>
                                        </div>
                                    </div>

                                <?php endif; ?>

                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3 btn-signup" type="submit">Sign Up</button>
                                </div>
                                <br>
                                <br>
                                <div class="col-12">
                                    <p>Already have an account? Login <a href="/login">here</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="h-100 rounded">
                            <img class="rounded w-100 h-100" style="height: 100%;" src="<?= asset('img/policy-1.jpg') ?>" 
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contuct End -->

    <!-- Include the footer section code file -->
    <?php include_once ROOT_DIR_PATH . '/includes/public/footer.php'; ?>

    <!-- Add user defined scripts here -->
    <script src="<?= asset_versioned('js/public_auth/register.js') ?>" type="module"></script>

</body>
</html>