<?php
    // Import Initializer File
    require_once __DIR__ . '/includes/init.php';

    // Import Default Images File (For Development Phase ONLY)
    require_once __DIR__ . '/includes/helper.php';

    // Initialize Necessary Models
    $productModel = $container->get(\App\Models\Product::class);

    // Get Product Details
    $productId      = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null;
    $productDetails = $productModel->findOne($productId);

    // Get Products Data
    $featuredProducts = $productModel->findFeatured(STORE_ID); // From init.php
    $relatedProducts  = $productModel->findByRelated($productDetails['category'], $productId);
    $productColors    = $productModel->groupByColor();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= SITE_NAME ?> | Product Details</title>
    <!-- Include the head section code file -->
    <?php include_once 'includes/head.php'; ?>
</head>

<body>

    <!-- Include the header section code file -->
    <?php include_once 'includes/header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Product Details</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Product Details</li>
        </ol>
    </div>
    <!-- Single Page Header End -->


    <!-- Single Products Start -->
    <div class="container-fluid shop py-5">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-5 col-xl-3 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="input-group w-100 mx-auto d-flex mb-4">
                        <input type="search" class="form-control p-3 collection-search" data-type="text" placeholder="Search categories, collecti..." aria-describedby="search-icon-1">
                        <span id="search-icon-1" class="input-group-text p-3 collection-click"><i class="fa fa-search"></i></span>
                    </div>
                    <div class="product-categories mb-4 dropdown-menu-height">
                        <h4>Products Categories</h4>
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
                    <div class="product-categories mb-4 dropdown-menu-height">
                        <h4>Color Collections</h4>
                        <ul class="list-unstyled">
                            <!-- Display Grouped Categories -->
                            <?php foreach ($productColors as $color): ?>
                                <li>
                                    <div class="categories-item">
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
                                ?>

                                <div class="featured-product-item" data-id="<?= $product['product_id'] ?>">
                                    <div class="rounded me-4" style="width: 100px; height: 100px;">
                                        <img src="<?= htmlspecialchars($imageUrl) ?>" class="img-fluid rounded product-image" alt="Product Image">
                                    </div>
                                    <div>
                                        <h6 class="mb-2"><a href="product-details?id=<?= $product['product_id'] ?>" class="text-dark product-name"><?= htmlspecialchars($product['product_name']) ?></a></h6>
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
                    <div class="product-tags my-4">
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
                <div class="col-lg-7 col-xl-9 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="row g-4 single-product product-card product-details" data-id="<?= $productDetails['product_id'] ?>">
                        <div class="col-xl-6">
                            <div class="single-carousel owl-carousel">
                                <?php
                                    // Pick which images/media to display
                                    if ($isDevelopment) {
                                        // Random demo slides
                                        $randomImages = [];
                                        $imageCount = rand(2, 5);
                                        for ($i = 0; $i < $imageCount; $i++) {
                                            $randomImages[] = $fallbackImages[array_rand($fallbackImages)];
                                        }
                                        $mediaToShow = $randomImages;
                                    } else {
                                        // Real product media (or fallback)
                                        $mediaToShow = !empty($productDetails['media'])
                                            ? array_column($productDetails['media'], 'media_url')
                                            : ['assets/img/default-product.png'];
                                    }

                                    // Allowed extensions
                                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                    $videoExtensions = ['mp4', 'webm', 'ogg'];

                                    // Render each media
                                    foreach ($mediaToShow as $mediaUrl):
                                        // Detect extension (ignore query params)
                                        $ext = strtolower(pathinfo(parse_url($mediaUrl, PHP_URL_PATH), PATHINFO_EXTENSION));

                                        // Determine type
                                        $isImage = in_array($ext, $imageExtensions);
                                        $isVideo = in_array($ext, $videoExtensions);
                                    ?>
                                        <div class="single-item"
                                            data-dot="
                                                <?php if ($isImage): ?>
                                                    <img class='img-fluid' src='<?= htmlspecialchars($mediaUrl) ?>' alt='<?= htmlspecialchars($productDetails['product_name']) ?>'>
                                                <?php elseif ($isVideo): ?>
                                                    <video class='img-fluid' muted>
                                                        <source src='<?= htmlspecialchars($mediaUrl) ?>' type='video/<?= $ext ?>'>
                                                    </video>
                                                <?php else: ?>
                                                    <img class='img-fluid' src='assets/img/default-product.png' alt='<?= htmlspecialchars($productDetails['product_name']) ?>'>
                                                <?php endif; ?>
                                            ">
                                            <div class="single-inner bg-light rounded">
                                                <?php if ($isImage): ?>
                                                    <img src="<?= htmlspecialchars($mediaUrl) ?>" 
                                                        class="img-fluid rounded" 
                                                        alt="<?= htmlspecialchars($productDetails['product_name']) ?>">
                                                <?php elseif ($isVideo): ?>
                                                    <video class="img-fluid rounded" controls preload="metadata">
                                                        <source src="<?= htmlspecialchars($mediaUrl) ?>" type="video/<?= $ext ?>">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                <?php else: ?>
                                                    <img src="assets/img/default-product.png" 
                                                        class="img-fluid rounded" 
                                                        alt="<?= htmlspecialchars($productDetails['product_name']) ?>">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                            </div>
                        </div>
                        <div class="col-xl-6">
                            <h4 class="fw-bold mb-3 product-name"><?= htmlspecialchars($productDetails['product_name']) ?></h4>
                            <p class="mb-3 product-category"><?= htmlspecialchars($productDetails['category']) ?></p>
                            <h5 class="fw-bold mb-3 price-tag"><?= $currencyManager->format((float)$productDetails['product_price'] ?? 0); ?></h5>
                            <p class="mb-3 price-slash display-none"><?= $currencyManager->format((float)$productDetails['slash_price'] ?? 0); ?></p>
                            <?= $ratingManager->render($productDetails['rating']['average']); ?>
                            <div class="mb-3 mt-3">
                                <div class="btn btn-primary d-inline-block rounded text-white py-1 px-4 me-2"><i class="fab fa-facebook-f me-1"></i> Share</div>
                                <div class="btn btn-secondary d-inline-block rounded text-white py-1 px-4 ms-2"><i class="fab fa-twitter ms-1"></i> Share</div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <small>Product SKU: N/A</small>
                                <small>Available: <strong class="text-primary"><?= htmlspecialchars($productDetails['stock']) ?> items in stock</strong></small>
                                <small>Store Front: <strong class="text-primary"><a href="shop?id=<?= htmlspecialchars($productDetails['store_id']) ?>">Visit Store</a></strong></small>
                            </div>
                            <p class="mb-4">
                                The full product description is just some scroll down below.
                            </p>
                            <div class="input-group quantity mb-5" style="width: 100px;">
                                <div class="input-group-btn">
                                    <button class="btn btn-sm btn-minus rounded-circle bg-light border">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                                <input type="text" class="form-control form-control-sm text-center border-0" value="1">
                                <div class="input-group-btn">
                                    <button class="btn btn-sm btn-plus rounded-circle bg-light border">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <a href="#" class="btn btn-primary border border-primary rounded-pill px-4 py-2 mb-4 text-primary cart-add <?= $isLoggedIn ? 'cart-user' : 'cart-guest' ?> cart-primary">
                                <i class="fa fa-shopping-bag me-2 text-white"></i> Add to cart
                            </a>
                        </div>
                        <div class="col-lg-12">
                            <nav>
                                <div class="nav nav-tabs mb-3">
                                    <button class="nav-link active border-white border-bottom-0" type="button" role="tab" id="nav-about-tab" data-bs-toggle="tab" data-bs-target="#nav-about" aria-controls="nav-about" aria-selected="true">Description</button>
                                    <button class="nav-link border-white border-bottom-0" type="button" role="tab" id="nav-mission-tab" data-bs-toggle="tab" data-bs-target="#nav-mission" aria-controls="nav-mission" aria-selected="false">Reviews</button>
                                </div>
                            </nav>
                            <div class="tab-content mb-5">
                                <div class="tab-pane active" id="nav-about" role="tabpanel" aria-labelledby="nav-about-tab">
                                    <?= $productDetails['product_description'] ?>
                                </div>
                                <div class="tab-pane review-list" id="nav-mission" role="tabpanel" aria-labelledby="nav-mission-tab">
                                    <!-- Display reviews -->
                                    <?php if (!empty($productDetails['reviews'])): ?>
                                        <?php foreach ($productDetails['reviews'] as $review): ?>
                                            <div class="d-flex border-bottom pb-3 mb-3">
                                                <!-- Avatar -->
                                                <img src="<?= htmlspecialchars(($review['avatar'] !== 'None') ? $review['avatar'] : 'assets/img/avatar.jpg') ?>"
                                                    class="img-fluid rounded-circle p-3"
                                                    style="width: 100px; height: 100px;"
                                                    alt="Profile">

                                                <!-- Review Content -->
                                                <div class="flex-grow-1">
                                                    <!-- Date -->
                                                    <p class="mb-2" style="font-size: 14px;">
                                                        <?= htmlspecialchars($review['created_at']) ?>
                                                    </p>

                                                    <!-- Reviewer Name and Rating -->
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h5 class="mb-0">
                                                            <?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?>
                                                        </h5>
                                                        <div class="d-flex mb-3">
                                                            <?= $ratingManager->render($review['rating']); ?>
                                                        </div>
                                                    </div>

                                                    <!-- Review Comment -->
                                                    <p class="mb-0">
                                                        <?= htmlspecialchars($review['comment']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted">No reviews yet.</p>
                                    <?php endif; ?>
                                   
                                </div>
                                <!--
                                <div class="tab-pane" id="nav-vision" role="tabpanel">
                                    <p class="text-dark">
                                        Tempor erat elitr rebum at clita. Diam dolor diam ipsum et
                                        tempor sit. Aliqu diam
                                        amet diam et eos labore. Diam dolor diam ipsum et tempor sit. Aliqu diam amet diam et eos labore.
                                        Clita erat ipsum et lorem et sit
                                    </p>
                                </div>
                                -->
                            </div>
                        </div>
                        <form action="#">
                            <h4 class="mb-5 fw-bold">Leave a review</h4>
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="border-bottom rounded">
                                        <input type="text" class="form-control border-0 me-4 form-name" placeholder="Your Name *" value="<?= $isLoggedIn ? htmlspecialchars($user['name']) : '' ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="border-bottom rounded">
                                        <input type="email" class="form-control border-0 form-email" placeholder="Your Email *" value="<?= $isLoggedIn ? htmlspecialchars($user['email']) : '' ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="border-bottom rounded my-4">
                                        <textarea class="form-control border-0 form-review" cols="30" rows="8" placeholder="Your Review *" spellcheck="true"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="d-flex justify-content-between py-3 mb-5">
                                        <div class="d-flex align-items-center">
                                            <p class="mb-0 me-3">Please rate:</p>
                                            <div class="d-flex align-items-center rating-stars" style="font-size: 12px;">
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                            </div>
                                        </div>
                                        <a href="#" class="btn btn-primary border border-primary text-primary rounded-pill px-4 py-3 btn-review"><?= $isLoggedIn ? 'Post Review' : 'Login' ?></a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Single Products End -->

    <!-- Related Product Start -->
    <div class="container-fluid related-product">
        <div class="container">
            <div class="mx-auto text-center pb-5" style="max-width: 700px;">
                <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp" data-wow-delay="0.1s">Related Products</h4>
                <p class="wow fadeInUp" data-wow-delay="0.2s">
                   <?= !empty($relatedProducts['products']) ? 'Related products within the current product category' : 'Related products unavailable' ?>
                </p>
            </div>
            <div class="related-carousel owl-carousel pt-4">
               <?php if (!empty($relatedProducts['products'])): ?>
                    <!-- Display Top Selling Products -->
                    <?php foreach ($relatedProducts['products'] as $product): ?>

                        <?php
                            // Pick the product's first media if it exists
                            $imageUrl = $product['media'][0]['media_url'] ?? null; 
                        ?>

                        <div class="related-item rounded product-card" data-id="<?= $product['product_id'] ?>">
                            <div class="related-item-inner border rounded">
                                <div class="related-item-inner-item">
                                    <img src="<?= htmlspecialchars($imageUrl) ?>" class="img-fluid w-100 rounded-top" alt="Product Image">
                                    <!--<div class="related-new">New</div>-->
                                    <div class="related-details">
                                        <a href="product-details?id=<?= $product['product_id'] ?>"><i class="fa fa-eye fa-1x"></i></a>
                                    </div>
                                </div>
                                <div class="text-center rounded-bottom p-4">
                                    <a href="product-list?view=category&data=<?= htmlspecialchars($product['category']) ?>" class="d-block mb-2"><?= htmlspecialchars($product['category']) ?></a>
                                    <a href="product-details?id=<?= $product['product_id'] ?>" class="d-block h4"><?= htmlspecialchars($product['product_name']) ?></a>
                                    <del class="me-2 fs-5 price-slash"><?= $currencyManager->format((float)$product['slash_price'] ?? 0); ?></del>
                                    <span class="text-primary fs-5 price-tag"><?= $currencyManager->format((float)$product['product_price'] ?? 0); ?></span>
                                </div>
                            </div>
                            <div class="related-item-add border border-top-0 rounded-bottom  text-center p-4 pt-0">
                                <a href="#" class="btn btn-primary border-primary rounded-pill py-2 px-4 mb-4 cart-add <?= $isLoggedIn ? 'cart-user' : 'cart-guest' ?>"><i class="fas fa-shopping-cart me-2"></i> Add To Cart</a>
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

                    <?php endforeach; ?>

                    <?php else: ?>
                    <!-- No Product Available -->
                    <div class="d-flex justify-content-center align-items-center">
                        <!--<p>Related products unavailable</p>-->
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>
    <!-- Related Product End -->

    <!-- Include the footer section code file -->
    <?php include_once 'includes/footer.php'; ?>

    <!-- Add user defined scripts here -->
</body>

</html>