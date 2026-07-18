<?php
    // Import Initializer File
    require_once __DIR__ . '/includes/init.php';
    // Import Default Images File (For Development Phase ONLY)
    require_once __DIR__ . '/includes/helper.php'; 

    // Initialize Necessary Models
    $productModel = $container->get(\App\Models\Product::class);

    // Get Products Data
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

    // Get Products Data
    $allProducts      = $productModel->findByAll($page);
    $newArrivals      = $productModel->findNewArrivals();
    $featuredProducts = $productModel->findFeatured(STORE_ID); // From init.php
    $topProducts      = $productModel->findTopSelling();

    // Product Colors
    $productColors = $productModel->groupByColor();

    // Pagination parameters
    $totalPages  = $allProducts['total_pages'];
    $currentPage = $allProducts['page'];
    $baseUrl     =  __DIR__ . "?view=home";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= SITE_NAME ?> | Home</title>
    <!-- Include the head section code file -->
    <?php include_once 'includes/head.php'; ?>
</head>

<body>

    <!-- Include the header section code file -->
    <?php include_once 'includes/header.php'; ?>

    <!-- Carousel Start -->
    <div class="container-fluid carousel bg-light px-0">
        <div class="row g-0 justify-content-end">
            <div class="col-12 col-lg-7 col-xl-9">
                <div class="header-carousel owl-carousel bg-light py-5">
                    <div class="row g-0 header-carousel-item align-items-center">
                        <div class="col-xl-6 carousel-img wow fadeInLeft" data-wow-delay="0.1s">
                            <img src="assets/img/carousel-1.png" class="img-fluid w-100" alt="Image">
                        </div>
                        <div class="col-xl-6 carousel-content p-4">
                            <h4 class="text-uppercase fw-bold mb-4 wow fadeInRight" data-wow-delay="0.1s"
                                style="letter-spacing: 3px;">Save Up To 20%</h4>
                            <h1 class="display-3 text-capitalize mb-4 wow fadeInRight" data-wow-delay="0.3s">Fast Laptops & Desktop Computers</h1>
                            <p class="text-dark wow fadeInRight" data-wow-delay="0.5s">Limited Time Offer</p>
                            <a class="btn btn-primary rounded-pill py-3 px-5 wow fadeInRight" data-wow-delay="0.7s" href="#">Shop Now</a>
                        </div>
                    </div>
                    <div class="row g-0 header-carousel-item align-items-center">
                        <div class="col-xl-6 carousel-img wow fadeInLeft" data-wow-delay="0.1s">
                            <img src="assets/img/carousel-2.png" class="img-fluid w-100" alt="Image">
                        </div>
                        <div class="col-xl-6 carousel-content p-4">
                            <h4 class="text-uppercase fw-bold mb-4 wow fadeInRight" data-wow-delay="0.1s"
                                style="letter-spacing: 3px;">Save Up To 15%</h4>
                            <h1 class="display-3 text-capitalize mb-4 wow fadeInRight" data-wow-delay="0.3s">Heavy Work Station Laptops</h1>
                            <p class="text-dark wow fadeInRight" data-wow-delay="0.5s">Limited Time Offer</p>
                            <a class="btn btn-primary rounded-pill py-3 px-5 wow fadeInRight" data-wow-delay="0.7s" href="#">Shop Now</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-5 col-xl-3 wow fadeInRight" data-wow-delay="0.1s">
                <div class="carousel-header-banner h-100">
                    <img src="assets/img/ads-2.jpg" class="img-fluid w-100 h-100" style="object-fit: cover;object-position: center;" alt="Image">
                    <div class="carousel-banner-offer">
                        <p class="bg-primary text-white rounded fs-5 py-2 px-4 mb-0 me-3" style="background-color: #f28b00 !important;">Save big</p>
                        <p class="text-primary fs-5 fw-bold mb-0">Special Offer</p>
                    </div>
                    <div class="carousel-banner">
                        <div class="carousel-banner-content text-center p-4">
                            <a href="#" class="d-block mb-2">Electronics</a>
                            <a href="#" class="d-block text-white fs-3">Apple iPad Mini <br> G2356</a>
                            <del class="me-2 text-white fs-5">₦655,000.00</del>
                            <span class="text-primary fs-5">₦530,000.00</span>
                        </div>
                        <a href="#" class="btn btn-primary rounded-pill py-2 px-4"><i class="fas fa-shopping-cart me-2"></i> Shop Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->

    <!-- Searvices Start -->
    <div class="container-fluid px-0">
        <div class="row g-0">
            <div class="col-6 col-md-4 col-lg-2 border-start border-end wow fadeInUp" data-wow-delay="0.1s">
                <div class="p-4">
                    <div class="d-inline-flex align-items-center">
                        <i class="fa fa-sync-alt fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Fair Return</h6>
                            <p class="mb-0">30 days money back guarantee</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.2s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fab fa-telegram-plane fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Secure Shipping</h6>
                            <p class="mb-0">Secured shipping on all order</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.3s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-life-ring fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Support 24/7</h6>
                            <p class="mb-0">We provide online support around the clock</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.4s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-credit-card fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Secure Payments</h6>
                            <p class="mb-0">All payments are handled securely</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.5s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-lock fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Secure Connection</h6>
                            <p class="mb-0">All our services are fully secured</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.6s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-blog fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Global Reach</h6>
                            <p class="mb-0">Shop with us from around the world</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Searvices End -->

    <!-- Products Offer Start -->
    <div class="container-fluid bg-light py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.2s">
                    <a href="#" class="d-flex align-items-center justify-content-between border bg-white rounded p-4">
                        <div>
                            <p class="text-muted mb-3">Find The Best Camera for You!</p>
                            <h3 class="text-primary">Smart Camera</h3>
                            <h1 class="display-3 text-secondary mb-0">40% <span class="text-primary fw-normal">Off</span></h1>
                        </div>
                        <img src="assets/img/product-1.png" class="img-fluid" alt="">
                    </a>
                </div>
                <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.3s">
                    <a href="#" class="d-flex align-items-center justify-content-between border bg-white rounded p-4">
                        <div>
                            <p class="text-muted mb-3">Find The Best Whatches for You!</p>
                            <h3 class="text-primary">Smart Whatch</h3>
                            <h1 class="display-3 text-secondary mb-0">20% <span class="text-primary fw-normal">Off</span></h1>
                        </div>
                        <img src="assets/img/product-2.png" class="img-fluid" alt="">
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Products Offer End -->


    <!-- Our Products Start -->
    <div class="container-fluid product py-5">
        <div class="container py-5">
            <div class="tab-class">
                <div class="row g-4">
                    <div class="col-lg-4 text-start wow fadeInLeft" data-wow-delay="0.1s">
                        <h1>Product Catalog</h1>
                    </div>
                    <div class="col-lg-8 text-end wow fadeInRight" data-wow-delay="0.1s">
                        <ul class="nav nav-pills d-inline-flex text-center mb-5">
                            <li class="nav-item mb-4">
                                <a class="d-flex mx-2 py-2 bg-light rounded-pill active" data-bs-toggle="pill" href="#tab-1">
                                    <span class="text-dark" style="width: 130px;">For You</span>
                                </a>
                            </li>
                            <li class="nav-item mb-4">
                                <a class="d-flex py-2 mx-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-2">
                                    <span class="text-dark" style="width: 130px;">New Arrivals</span>
                                </a>
                            </li>
                            <li class="nav-item mb-4">
                                <a class="d-flex mx-2 py-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-3">
                                    <span class="text-dark" style="width: 130px;">Featured</span>
                                </a>
                            </li>
                            <li class="nav-item mb-4">
                                <a class="d-flex mx-2 py-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-4">
                                    <span class="text-dark" style="width: 130px;">Top Selling</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                            <?php if (!empty($allProducts['products'])): ?>
                                <!-- Display All Products -->
                                <?php foreach ($allProducts['products'] as $product): ?>

                                    <?php
                                        // Pick the product's first media if it exists
                                        $imageUrl = $product['media'][0]['media_url'] ?? null; 

                                        // For now, just use random images but switch to the code above at launch
                                        // $imageUrl = $fallbackImages[array_rand($fallbackImages)];
                                    ?>

                                    <div class="col-md-6 col-lg-4 col-xl-3 product-card" data-id="<?= $product['product_id'] ?>">
                                        <div class="product-item rounded wow fadeInUp" data-wow-delay="0.1s">
                                            <div class="product-item-inner border rounded">
                                                <div class="product-item-inner-item">
                                                    <img src="<?= htmlspecialchars($imageUrl) ?>" class="img-fluid w-100 rounded-top product-image" alt="Product Image">

                                                    <!--<div class="product-new">New</div>-->
                                                    <div class="product-details">
                                                        <a href="product-details?id=<?= $product['product_id'] ?>"><i class="fa fa-eye fa-1x"></i></a>
                                                    </div>
                                                </div>
                                                <div class="text-center rounded-bottom p-4">
                                                    <a href="product-list?view=category&data=<?= htmlspecialchars($product['category']) ?>" class="d-block mb-2 product-category"><?= htmlspecialchars($product['category']) ?></a>
                                                    <a href="product-details?id=<?= $product['product_id'] ?>" class="d-block h4 product-name"><?= htmlspecialchars($product['product_name']) ?></a>

                                                    <del class="me-2 fs-5 price-slash"><?= $currencyManager->format((float)$product['slash_price'] ?? 0); ?></del>
                                                    <span class="text-primary fs-5 price-tag"><?= $currencyManager->format((float)$product['product_price'] ?? 0); ?></span>
                                                </div>
                                            </div>

                                            <div class="product-item-add border border-top-0 rounded-bottom text-center p-4 pt-0">
                                                <a href="#" class="btn btn-primary border-primary rounded-pill py-2 px-4 mb-4 cart-add <?= $isLoggedIn ? 'cart-user' : 'cart-guest' ?>">
                                                    <i class="fas fa-shopping-cart me-2"></i> Add To Cart
                                                </a>

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <?= $ratingManager->render($product['rating']['average']); ?>
                                                    <div class="d-flex">
                                                        <a href="#" class="text-primary d-flex align-items-center justify-content-center me-3 product-share">
                                                            <span class="rounded-circle btn-sm-square border">
                                                                <i class="fas fa-random"></i>
                                                            </span>
                                                        </a>
                                                        <a href="#" class="text-primary d-flex align-items-center justify-content-center me-0 wishlist-add <?= $isLoggedIn ? 'wishlist-user' : 'wishlist-guest' ?>">
                                                            <span class="rounded-circle btn-sm-square border">
                                                                <i class="fas fa-heart"></i>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>

                                <?php else: ?>
                                <!-- No Product Available -->
                                <div class="d-flex justify-content-center align-items-center">
                                    <p>No product available</p>
                                </div>

                            <?php endif; ?>

                        </div>
                    </div>
                    <div id="tab-2" class="tab-pane fade show p-0">
                        <div class="row g-4">
                            <?php if (!empty($newArrivals['products'])): ?>
                                <!-- Display New Arrivals Products -->
                                <?php foreach ($newArrivals['products'] as $product): ?>

                                    <?php
                                        // Pick the product's first media if it exists
                                        $imageUrl = $product['media'][0]['media_url'] ?? null;

                                        // For now, just use random images but switch to the code above at launch
                                        // $imageUrl = $fallbackImages[array_rand($fallbackImages)];
                                    ?>

                                    <div class="col-md-6 col-lg-4 col-xl-3 product-card" data-id="<?= $product['product_id'] ?>">
                                        <div class="product-item rounded wow fadeInUp" data-wow-delay="0.1s">
                                            <div class="product-item-inner border rounded">
                                                <div class="product-item-inner-item">
                                                    <img src="<?= htmlspecialchars($imageUrl) ?>" class="img-fluid w-100 rounded-top product-image" alt="Product Image">

                                                    <!--<div class="product-new">New</div>-->
                                                    <div class="product-details">
                                                        <a href="product-details?id=<?= $product['product_id'] ?>"><i class="fa fa-eye fa-1x"></i></a>
                                                    </div>
                                                </div>
                                                <div class="text-center rounded-bottom p-4">
                                                    <a href="product-list?view=category&data=<?= htmlspecialchars($product['category']) ?>" class="d-block mb-2 product-category"><?= htmlspecialchars($product['category']) ?></a>
                                                    <a href="product-details?id=<?= $product['product_id'] ?>" class="d-block h4 product-name"><?= htmlspecialchars($product['product_name']) ?></a>

                                                    <del class="me-2 fs-5 price-slash"><?= $currencyManager->format((float)$product['slash_price'] ?? 0); ?></del>
                                                    <span class="text-primary fs-5 price-tag"><?= $currencyManager->format((float)$product['product_price'] ?? 0); ?></span>
                                                </div>
                                            </div>

                                            <div class="product-item-add border border-top-0 rounded-bottom text-center p-4 pt-0">
                                                <a href="#" class="btn btn-primary border-primary rounded-pill py-2 px-4 mb-4 cart-add <?= $isLoggedIn ? 'cart-user' : 'cart-guest' ?>">
                                                    <i class="fas fa-shopping-cart me-2"></i> Add To Cart
                                                </a>

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <?= $ratingManager->render($product['rating']['average']); ?>
                                                    <div class="d-flex">
                                                        <a href="#" class="text-primary d-flex align-items-center justify-content-center me-3 product-share">
                                                            <span class="rounded-circle btn-sm-square border">
                                                                <i class="fas fa-random"></i>
                                                            </span>
                                                        </a>
                                                        <a href="#" class="text-primary d-flex align-items-center justify-content-center me-0 wishlist-add <?= $isLoggedIn ? 'wishlist-user' : 'wishlist-guest' ?>">
                                                            <span class="rounded-circle btn-sm-square border">
                                                                <i class="fas fa-heart"></i>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                                <?php else: ?>
                                <!-- No Product Available -->
                                <div class="d-flex justify-content-center align-items-center">
                                    <p>Recent products unavailable</p>
                                </div>

                            <?php endif; ?>

                        </div>
                    </div>
                    <div id="tab-3" class="tab-pane fade show p-0">
                        <div class="row g-4">
                            <?php if (!empty($featuredProducts['products'])): ?>
                                <!-- Display Featured Products -->
                                <?php foreach ($featuredProducts['products'] as $product): ?>

                                    <?php
                                        // Pick the product's first media if it exists
                                        $imageUrl = $product['media'][0]['media_url'] ?? null;

                                        // For now, just use random images but switch to the code above at launch
                                        // $imageUrl = $fallbackImages[array_rand($fallbackImages)];
                                    ?>

                                    <div class="col-md-6 col-lg-4 col-xl-3 product-card" data-id="<?= $product['product_id'] ?>">
                                        <div class="product-item rounded wow fadeInUp" data-wow-delay="0.1s">
                                            <div class="product-item-inner border rounded">
                                                <div class="product-item-inner-item">
                                                    <img src="<?= htmlspecialchars($imageUrl) ?>" class="img-fluid w-100 rounded-top product-image" alt="Product Image">

                                                    <!--<div class="product-new">New</div>-->
                                                    <div class="product-details">
                                                        <a href="product-details?id=<?= $product['product_id'] ?>"><i class="fa fa-eye fa-1x"></i></a>
                                                    </div>
                                                </div>
                                                <div class="text-center rounded-bottom p-4">
                                                    <a href="product-list?view=category&data=<?= htmlspecialchars($product['category']) ?>" class="d-block mb-2 product-category"><?= htmlspecialchars($product['category']) ?></a>
                                                    <a href="product-details?id=<?= $product['product_id'] ?>" class="d-block h4 product-name"><?= htmlspecialchars($product['product_name']) ?></a>

                                                    <del class="me-2 fs-5 price-slash"><?= $currencyManager->format((float)$product['slash_price'] ?? 0); ?></del>
                                                    <span class="text-primary fs-5 price-tag"><?= $currencyManager->format((float)$product['product_price'] ?? 0); ?></span>
                                                </div>
                                            </div>

                                            <div class="product-item-add border border-top-0 rounded-bottom text-center p-4 pt-0">
                                                <a href="#" class="btn btn-primary border-primary rounded-pill py-2 px-4 mb-4 cart-add <?= $isLoggedIn ? 'cart-user' : 'cart-guest' ?>">
                                                    <i class="fas fa-shopping-cart me-2"></i> Add To Cart
                                                </a>

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <?= $ratingManager->render($product['rating']['average']); ?>
                                                    <div class="d-flex">
                                                        <a href="#" class="text-primary d-flex align-items-center justify-content-center me-3 product-share">
                                                            <span class="rounded-circle btn-sm-square border">
                                                                <i class="fas fa-random"></i>
                                                            </span>
                                                        </a>
                                                        <a href="#" class="text-primary d-flex align-items-center justify-content-center me-0 wishlist-add <?= $isLoggedIn ? 'wishlist-user' : 'wishlist-guest' ?>">
                                                            <span class="rounded-circle btn-sm-square border">
                                                                <i class="fas fa-heart"></i>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                                <?php else: ?>
                                <!-- No Product Available -->
                                <div class="d-flex justify-content-center align-items-center">
                                    <p>Featured products unavailable</p>
                                </div>

                            <?php endif; ?>

                        </div>
                    </div>
                    <div id="tab-4" class="tab-pane fade show p-0">
                        <div class="row g-4">
                            <?php if (!empty($topProducts['products'])): ?>
                                <!-- Display Top Selling Products -->
                                <?php foreach ($topProducts['products'] as $product): ?>

                                    <?php
                                        // Pick the product's first media if it exists
                                        $imageUrl = $product['media'][0]['media_url'] ?? null; 

                                        // For now, just use random images but switch to the code above at launch
                                        // $imageUrl = $fallbackImages[array_rand($fallbackImages)];
                                    ?>

                                    <div class="col-md-6 col-lg-4 col-xl-3 product-card" data-id="<?= $product['product_id'] ?>">
                                        <div class="product-item rounded wow fadeInUp" data-wow-delay="0.1s">
                                            <div class="product-item-inner border rounded">
                                                <div class="product-item-inner-item">
                                                    <img src="<?= htmlspecialchars($imageUrl) ?>" class="img-fluid w-100 rounded-top product-image" alt="Product Image">

                                                    <!--<div class="product-new">New</div>-->
                                                    <div class="product-details">
                                                        <a href="product-details?id=<?= $product['product_id'] ?>"><i class="fa fa-eye fa-1x"></i></a>
                                                    </div>
                                                </div>
                                                <div class="text-center rounded-bottom p-4">
                                                    <a href="product-list?view=category&data=<?= htmlspecialchars($product['category']) ?>" class="d-block mb-2 product-category"><?= htmlspecialchars($product['category']) ?></a>
                                                    <a href="product-details?id=<?= $product['product_id'] ?>" class="d-block h4 product-name"><?= htmlspecialchars($product['product_name']) ?></a>

                                                    <del class="me-2 fs-5 price-slash"><?= $currencyManager->format((float)$product['slash_price'] ?? 0); ?></del>
                                                    <span class="text-primary fs-5 price-tag"><?= $currencyManager->format((float)$product['product_price'] ?? 0); ?></span>
                                                </div>
                                            </div>

                                            <div class="product-item-add border border-top-0 rounded-bottom text-center p-4 pt-0">
                                                <a href="#" class="btn btn-primary border-primary rounded-pill py-2 px-4 mb-4 cart-add <?= $isLoggedIn ? 'cart-user' : 'cart-guest' ?>">
                                                    <i class="fas fa-shopping-cart me-2"></i> Add To Cart
                                                </a>

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <?= $ratingManager->render($product['rating']['average']); ?>
                                                    <div class="d-flex">
                                                        <a href="#" class="text-primary d-flex align-items-center justify-content-center me-3 product-share">
                                                            <span class="rounded-circle btn-sm-square border">
                                                                <i class="fas fa-random"></i>
                                                            </span>
                                                        </a>
                                                        <a href="#" class="text-primary d-flex align-items-center justify-content-center me-0 wishlist-add <?= $isLoggedIn ? 'wishlist-user' : 'wishlist-guest' ?>">
                                                            <span class="rounded-circle btn-sm-square border">
                                                                <i class="fas fa-heart"></i>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                                <?php else: ?>
                                <!-- No Product Available -->
                                <div class="d-flex justify-content-center align-items-center">
                                    <p>Top selling products unavailable</p>
                                </div>

                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Our Products End -->

    <!-- Product Banner Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
                    <a href="#">
                        <div class="bg-primary rounded position-relative">
                            <img src="assets/img/product-banner.jpg" class="img-fluid w-100 rounded" alt="">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center rounded p-4" style="background: rgba(255, 255, 255, 0.5);">
                                <h3 class="display-5 text-primary">EOS Rebel <br> <span>T7i Kit</span></h3>
                                <!--<p class="fs-4 text-muted">$899.99</p>-->
                                <a href="#" class="btn btn-primary rounded-pill align-self-start py-2 px-4">Shop Now</a>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.2s">
                    <a href="#">
                        <div class="text-center bg-primary rounded position-relative">
                            <img src="assets/img/product-banner-2.jpg" class="img-fluid w-100" alt="">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center rounded p-4" style="background: rgba(242, 139, 0, 0.5);">
                                <h2 class="display-2 text-secondary">SALE</h2>
                                <h4 class="display-5 text-white mb-4">Get UP To 50% Off</h4>
                                <a href="#" class="btn btn-primary rounded-pill align-self-center py-2 px-4">Shop Now</a>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Product Banner End -->

    <!-- Shop Page Start -->
    <div class="container-fluid shop py-5">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-3 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="product-categories mb-4">
                        <h4>Filter By  Category</h4>
                        <ul class="list-unstyled">
                            <!-- Display Grouped Categories -->
                            <?php foreach ($groupedCategories as $category): ?>
                                <li>
                                    <div class="categories-item">
                                        <a href="product-list?view=category&data=<?= $category['category_name'] ?>">
                                            <i class="fas fa-tag text-primary me-2"></i>
                                            <?= $category['category_name'] ?>
                                        </a>
                                        <span>(<?= $category['product_count'] ?>)</span>
                                    </div>
                                </li>
                            <?php endforeach; ?>

                        </ul>
                    </div>
                    <div class="price mb-4">
                        <h4 class="mb-2">Filter By Price</h4>
                        <input type="range" class="form-range w-100 price-range" name="rangeInput" min="1000" max="10000000" value="1000" step="1000">
                        <output class="output-range" name="amount" for="rangeInput">1000</output>
                        <div class=""></div>
                    </div>
                    <div class="product-color mb-3">
                        <h4>Filter By Color</h4>
                        <ul class="list-unstyled">
                            <!-- Display Color Categories -->
                            <?php foreach ($productColors as $color): ?>
                                <li>
                                    <div class="product-color-item">
                                        <a href="product-list?view=color&data=<?= $color['color'] ?>">
                                            <i class="fas fa-palette text-primary me-2"></i>
                                            <?= $color['color'] ?>
                                        </a>
                                        <span>(<?= $color['product_count'] ?>)</span>
                                    </div>
                                </li>
                            <?php endforeach; ?>

                        </ul>
                    </div>
                    <div class="featured-product mb-4">
                        <h4 class="mb-3">Featured products</h4>

                        <!-- Display Featured Products -->
                        <?php foreach ($featuredProducts['products'] as $product): ?>

                            <?php
                                // Pick the product's first media if it exists
                                $imageUrl = $product['media'][0]['media_url'] ?? null; 

                                // For now, just use random images but switch to the code above at launch
                                // $imageUrl = $fallbackImages[array_rand($fallbackImages)];
                            ?>

                            <div class="featured-product-item" data-id="<?= $product['product_id'] ?>">
                                <div class="rounded me-4" style="width: 100px; height: 100px;">
                                    <img src="<?= htmlspecialchars($imageUrl) ?>" class="img-fluid rounded product-image" alt="Product Image">
                                </div>
                                <div>
                                    <h6 class="mb-2"><a href="product-details?id=<?= $product['product_id'] ?>" class="text-dark product-category"><?= htmlspecialchars($product['product_name']) ?></a></h6>
                                    <?= $ratingManager->render($product['rating']['average']); ?>
                                    <div class="d-flex mb-2 mt-2">
                                        <h5 class="fw-bold me-2 price-tag"><?= $currencyManager->format((float)$product['product_price'] ?? 0); ?></h5>
                                        <h5 class="text-danger text-decoration-line-through price-slash"><?= $currencyManager->format((float)$product['slash_price'] ?? 0); ?></h5>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>

                        <div class="d-flex justify-content-center my-4">
                            <a href="#" class="btn btn-primary px-4 py-3 rounded-pill w-100">Vew More</a>
                        </div>
                    </div>
                    <a href="#">
                        <div class="position-relative">
                            <img src="assets/img/product-banner-2.jpg" class="img-fluid w-100 rounded" alt="Image">
                            <div class="text-center position-absolute d-flex flex-column align-items-center justify-content-center rounded p-4"
                                style="width: 100%; height: 100%; top: 0; right: 0; background: rgba(242, 139, 0, 0.3);">
                                <h5 class="display-6 text-primary">SALE</h5>
                                <h4 class="text-secondary">Get UP To 50% Off</h4>
                                <a href="#" class="btn btn-primary rounded-pill px-4">Shop Now</a>
                            </div>
                        </div>
                    </a>
                    <div class="product-tags py-4">
                        <h4 class="mb-3">PRODUCT TAGS</h4>
                        <div class="product-tags-items bg-light rounded p-3">
                            <a href="#" class="border rounded py-1 px-2 mb-2">New</a>
                            <a href="#" class="border rounded py-1 px-2 mb-2">brand</a>
                            <a href="#" class="border rounded py-1 px-2 mb-2">black</a>
                            <a href="#" class="border rounded py-1 px-2 mb-2">white</a>
                            <a href="#" class="border rounded py-1 px-2 mb-2">tablets</a>
                            <a href="#" class="border rounded py-1 px-2 mb-2">phone</a>
                            <a href="#" class="border rounded py-1 px-2 mb-2">camera</a>
                            <a href="#" class="border rounded py-1 px-2 mb-2">drone</a>
                            <a href="#" class="border rounded py-1 px-2 mb-2">television</a>
                            <a href="#" class="border rounded py-1 px-2 mb-2">sales</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="rounded mb-4 position-relative">
                        <img src="assets/img/product-banner-3.jpg" class="img-fluid rounded w-100" style="height: 250px;"
                            alt="Image">
                        <div class="position-absolute rounded d-flex flex-column align-items-center justify-content-center text-center"
                            style="width: 100%; height: 250px; top: 0; left: 0; background: rgba(242, 139, 0, 0.3);">
                            <h4 class="display-5 text-primary">SALE</h4>
                            <h3 class="display-4 text-white mb-4">Get UP To 50% Off</h3>
                            <a href="#" class="btn btn-primary rounded-pill">Shop Now</a>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-xl-7">
                            <div class="input-group w-100 mx-auto d-flex">
                                <input type="search" class="form-control p-3 sub-search" placeholder="Search products..." aria-describedby="search-icon-1">
                                <span id="search-icon-1" class="input-group-text p-3 search-click"><i class="fa fa-search"></i></span>
                            </div>
                        </div>
                        <div class="col-xl-3 text-end">
                            <div class="bg-light ps-3 py-3 rounded d-flex justify-content-between">
                                <label for="electronics">Sort By:</label>
                                <?php include_once 'includes/filter.php'; ?>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-2">
                            <ul class="nav nav-pills d-inline-flex text-center py-2 px-2 rounded bg-light mb-4">
                                <li class="nav-item me-4">
                                    <a class="bg-light" data-bs-toggle="pill" href="#tab-5">
                                        <i class="fas fa-th fa-3x text-primary"></i>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="bg-light" data-bs-toggle="pill" href="#tab-6">
                                        <i class="fas fa-bars fa-3x text-primary"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div id="tab-5" class="tab-pane fade show p-0 active">
                            <div class="row g-4 product product-main">
                                <?php if (!empty($allProducts['products'])): ?>
                                    <!-- Display All Products -->
                                    <?php foreach ($allProducts['products'] as $product): ?>

                                        <?php
                                            // Pick the product's first media if it exists
                                            $imageUrl = $product['media'][0]['media_url'] ?? null; 

                                            // For now, just use random images but switch to the code above at launch
                                            // $imageUrl = $fallbackImages[array_rand($fallbackImages)];
                                        ?>

                                        <div class="col-lg-4 product-card card-main" data-id="<?= $product['product_id'] ?>" data-rating="<?= $product['rating']['average'] ?>">
                                            <div class="product-item rounded wow fadeInUp" data-wow-delay="0.1s">
                                                <div class="product-item-inner border rounded">
                                                    <div class="product-item-inner-item">
                                                        <img src="<?= htmlspecialchars($imageUrl) ?>" class="img-fluid w-100 rounded-top product-image" alt="Product Image">
                                                        <!--<div class="product-new">New</div>-->
                                                        <div class="product-details">
                                                            <a href="product-details?id=<?= $product['product_id'] ?>"><i class="fa fa-eye fa-1x"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="text-center rounded-bottom p-4">
                                                        <a href="product-list?view=category&data=<?= htmlspecialchars($product['category']) ?>" class="d-block mb-2 product-category"><?= htmlspecialchars($product['category']) ?></a>
                                                        <a href="product-details?id=<?= $product['product_id'] ?>" class="d-block h4 product-name"><?= htmlspecialchars($product['product_name']) ?></a>
                                                        <del class="me-2 fs-5 price-slash"><?= $currencyManager->format((float)$product['slash_price'] ?? 0); ?></del>
                                                        <span class="text-primary fs-5 price-tag"><?= $currencyManager->format((float)$product['product_price'] ?? 0); ?></span>
                                                    </div>
                                                </div>
                                                <div class="product-item-add border border-top-0 rounded-bottom text-center p-4 pt-0">
                                                    <a href="#" class="btn btn-primary border-primary rounded-pill py-2 px-4 mb-4 cart-add <?= $isLoggedIn ? 'cart-user' : 'cart-guest' ?>">
                                                        <i class="fas fa-shopping-cart me-2"></i> Add To Cart
                                                    </a>

                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <?= $ratingManager->render($product['rating']['average']); ?>
                                                        <div class="d-flex">
                                                            <a href="#" class="text-primary d-flex align-items-center justify-content-center me-3 product-share">
                                                                <span class="rounded-circle btn-sm-square border">
                                                                    <i class="fas fa-random"></i>
                                                                </span>
                                                            </a>
                                                            <a href="#" class="text-primary d-flex align-items-center justify-content-center me-0 wishlist-add <?= $isLoggedIn ? 'wishlist-user' : 'wishlist-guest' ?>">
                                                                <span class="rounded-circle btn-sm-square border">
                                                                    <i class="fas fa-heart"></i>
                                                                </span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endforeach; ?>

                                    <div class="col-12 wow fadeInUp pagination-row" data-wow-delay="0.1s">
                                        <div class="pagination d-flex justify-content-center mt-5">
                                            <!-- Previous button -->
                                            <a href="<?= ($currentPage > 1) ? pageUrl($currentPage - 1, $baseUrl) : '#' ?>" class="rounded <?= ($currentPage <= 1) ? 'disabled' : '' ?>">&laquo;</a>

                                            <!-- Page number links -->
                                            <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                                                <a href="<?= pageUrl($page, $baseUrl) ?>" class="<?= ($page == $currentPage) ? 'active' : '' ?> rounded"><?= $page ?></a>
                                            <?php endfor; ?>

                                            <!-- Next button -->
                                            <a href="<?= ($currentPage < $totalPages) ? pageUrl($currentPage + 1, $baseUrl) : '#' ?>" class="rounded <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">&raquo;</a>
                                        </div>
                                    </div>

                                    <?php else: ?>
                                        <!-- No Product Available -->
                                        <div class="d-flex justify-content-center align-items-center">
                                            <p>No products available</p>
                                        </div>

                                <?php endif; ?>
                            </div>
                        </div>
                        <div id="tab-6" class="products tab-pane fade show p-0">
                            <div class="row g-4 products-mini product-mini-main">
                                <?php if (!empty($allProducts['products'])): ?>
                                    <!-- Display All Products -->
                                    <?php foreach ($allProducts['products'] as $product): ?>

                                        <?php
                                            // Pick the product's first media if it exists
                                            $imageUrl = $product['media'][0]['media_url'] ?? null;

                                            // For now, just use random images but switch to the code above at launch
                                            // $imageUrl = $fallbackImages[array_rand($fallbackImages)];
                                        ?>

                                        <div class="col-lg-6 product-card card-main" data-id="<?= $product['product_id'] ?>" data-rating="<?= $product['rating']['average'] ?>">
                                            <div class="products-mini-item border">
                                                <div class="row g-0">
                                                    <div class="col-5">
                                                        <div class="products-mini-img border-end h-100">
                                                            <img src="<?= htmlspecialchars($imageUrl) ?>" class="img-fluid w-100 h-100 product-image" alt="Product Image">
                                                            <div class="products-mini-icon rounded-circle bg-primary">
                                                                <a href="product-details?id=<?= $product['product_id'] ?>"><i class="fa fa-eye fa-1x text-white"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-7">
                                                        <div class="products-mini-content p-3">
                                                            <a href="product-list?view=category&data=<?= htmlspecialchars($product['category']) ?>" class="d-block mb-2 product-category"><?= htmlspecialchars($product['category']) ?></a>
                                                            <a href="product-details?id=<?= $product['product_id'] ?>" class="d-block h4 product-name"><?= htmlspecialchars($product['product_name']) ?></a>
                                                            <del class="me-2 fs-5 price-slash"><?= $currencyManager->format((float)$product['slash_price'] ?? 0); ?></del>
                                                            <span class="text-primary fs-5 price-tag"><?= $currencyManager->format((float)$product['product_price'] ?? 0); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="products-mini-add border p-3">
                                                    <a href="#" class="btn btn-primary border-primary rounded-pill py-2 px-4 cart-add <?= $isLoggedIn ? 'cart-user' : 'cart-guest' ?>"><i class="fas fa-shopping-cart me-2"></i> Add To Cart</a>
                                                    <div class="d-flex">
                                                        <a href="#" class="text-primary d-flex align-items-center justify-content-center me-3 product-share">
                                                            <span class="rounded-circle btn-sm-square border">
                                                                <i class="fas fa-random"></i>
                                                            </span>
                                                        </a>
                                                        <a href="#" class="text-primary d-flex align-items-center justify-content-center me-0 wishlist-add <?= $isLoggedIn ? 'wishlist-user' : 'wishlist-guest' ?>">
                                                            <span class="rounded-circle btn-sm-square border">
                                                                <i class="fas fa-heart"></i>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endforeach; ?>
                                
                                    <div class="col-12 wow fadeInUp pagination-row" data-wow-delay="0.1s">
                                        <div class="pagination d-flex justify-content-center mt-5">
                                            <!-- Previous button -->
                                            <a href="<?= ($currentPage > 1) ? pageUrl($currentPage - 1, $baseUrl) : '#' ?>" class="rounded <?= ($currentPage <= 1) ? 'disabled' : '' ?>">&laquo;</a>

                                            <!-- Page number links -->
                                            <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                                                <a href="<?= pageUrl($page, $baseUrl) ?>" class="<?= ($page == $currentPage) ? 'active' : '' ?> rounded"><?= $page ?></a>
                                            <?php endfor; ?>

                                            <!-- Next button -->
                                            <a href="<?= ($currentPage < $totalPages) ? pageUrl($currentPage + 1, $baseUrl) : '#' ?>" class="rounded <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">&raquo;</a>
                                        </div>
                                    </div>

                                    <?php else: ?>
                                        <!-- No Product Available -->
                                        <div class="d-flex justify-content-center align-items-center">
                                            <p>No products available</p>
                                        </div>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Shop Page End -->

    <!-- Bestseller Products Start -->
    <div class="container-fluid products pb-5">
        <div class="container products-mini py-5">
            <div class="mx-auto text-center mb-5" style="max-width: 700px;">
                <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp"
                    data-wow-delay="0.1s">Bestseller Products</h4>
                <p class="mb-0 wow fadeInUp" data-wow-delay="0.2s">
                    Discover the most trusted and popular products that our customers love. 
                </p>
            </div>
            <div class="row g-4">
                <!-- Display Top Selling Products -->
                <?php foreach ($topProducts['products'] as $product): ?>

                    <?php
                        // Pick the product's first media if it exists
                        $imageUrl = $product['media'][0]['media_url'] ?? null; 

                        // For now, just use random images but switch to the code above at launch
                        // $imageUrl = $fallbackImages[array_rand($fallbackImages)];
                    ?>

                    <div class="col-md-6 col-lg-6 col-xl-4 wow fadeInUp product-card" data-wow-delay="0.1s" data-id="<?= $product['product_id'] ?>">
                        <div class="products-mini-item border">
                            <div class="row g-0">
                                <div class="col-5">
                                    <div class="products-mini-img border-end h-100">
                                        <img src="<?= htmlspecialchars($imageUrl) ?>" class="img-fluid w-100 h-100 product-image" alt="Product Image">
                                        <div class="products-mini-icon rounded-circle bg-view">
                                            <a href="product-details?id=<?= $product['product_id'] ?>"><i class="fa fa-eye fa-1x text-white"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-7">
                                    <div class="products-mini-content p-3">
                                        <a href="product-list?view=category&data=<?= htmlspecialchars($product['category']) ?>" class="d-block mb-2 product-category"><?= htmlspecialchars($product['category']) ?></a>
                                        <a href="product-details?id=<?= $product['product_id'] ?>" class="d-block h4 product-name"><?= htmlspecialchars($product['product_name']) ?></a>
                                        <del class="me-2 fs-5 price-slash"><?= $currencyManager->format((float)$product['slash_price'] ?? 0); ?></del>
                                        <span class="text-primary fs-5 price-tag"><?= $currencyManager->format((float)$product['product_price'] ?? 0); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="products-mini-add border p-3">
                                <a href="#" class="btn btn-primary border-primary rounded-pill py-2 px-4 cart-add <?= $isLoggedIn ? 'cart-user' : 'cart-guest' ?>"><i class="fas fa-shopping-cart me-2"></i> Add To Cart</a>
                                <div class="d-flex">
                                    <a href="#" class="text-primary d-flex align-items-center justify-content-center me-3 product-share">
                                        <span class="rounded-circle btn-sm-square border">
                                            <i class="fas fa-random"></i>
                                        </span>
                                    </a>
                                    <a href="#" class="text-primary d-flex align-items-center justify-content-center me-0 wishlist-add <?= $isLoggedIn ? 'wishlist-user' : 'wishlist-guest' ?>">
                                        <span class="rounded-circle btn-sm-square border">
                                            <i class="fas fa-heart"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

            </div>
        </div>
    </div>
    <!-- Bestseller Products End -->

    <!-- Include the footer section code file -->
    <?php include_once 'includes/footer.php'; ?>

    <!-- Add user defined scripts here -->

</body>
</html>