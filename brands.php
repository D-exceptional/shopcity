<?php
    // Import Initializer File
    require_once __DIR__ . '/includes/init.php';

    // Import Default Images File (For Development Phase ONLY)
    require_once __DIR__ . '/includes/helper.php'; 

    // Initialize Necessary Models
    $storeModel = $container->get(\App\Models\Store::class);

    // Get page
    $page  = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

    // Get Top Brands
    $topBrands = $storeModel->getTopBrands($page);

    // Pagination parameters
    $totalPages  = $topBrands['total_pages'];
    $currentPage = $topBrands['page'];
    $baseUrl     = "brands?view=home";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= SITE_NAME ?> | Brands</title>
    <!-- Include the head section code file -->
    <?php include_once 'includes/head.php'; ?>
</head>

<body>

    <!-- Include the header section code file -->
    <?php include_once 'includes/header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Top Brands</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Top Brands</li>
        </ol>
    </div>
    <!-- Single Page Header End -->


    <!-- Products Offer Start -->
    <div class="container-fluid bg-light py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.2s">
                    <a href="#" class="d-flex align-items-center justify-content-between border bg-white rounded p-4">
                        <div>
                            <p class="text-muted mb-3">Find The Best Camera for You!</p>
                            <h3 class="text-primary">Smart Camera</h3>
                            <h1 class="display-3 text-secondary mb-0">40% <span
                                    class="text-primary fw-normal">Off</span></h1>
                        </div>
                        <img src="assets/img/product-1.png" class="img-fluid" alt="">
                    </a>
                </div>
                <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.3s">
                    <a href="#" class="d-flex align-items-center justify-content-between border bg-white rounded p-4">
                        <div>
                            <p class="text-muted mb-3">Find The Best Whatches for You!</p>
                            <h3 class="text-primary">Smart Whatch</h3>
                            <h1 class="display-3 text-secondary mb-0">20% <span
                                    class="text-primary fw-normal">Off</span></h1>
                        </div>
                        <img src="assets/img/product-2.png" class="img-fluid" alt="">
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Products Offer End -->

    <!-- Top Brands Start -->
    <div class="container-fluid related-product">
        <div class="container">
            <div class="mx-auto text-center pb-5" style="max-width: 700px;">
                <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp" data-wow-delay="0.1s">Best-selling Brands</h4>
                <p class="wow fadeInUp" data-wow-delay="0.2s">
                    Discover the most trusted and popular brands that our customers love. 
                    From everyday essentials to premium items, these brands consistently deliver the best experiences, making them customer favorites year after year.
                </p>
            </div>
            <div class="related-carousel owl-carousel pt-4">
               
                <!-- Display Top Selling Brands -->
                <?php foreach ($topBrands['brands'] as $store): ?>

                    <?php
                        // Get or set store image
                        $imageUrl = $store['store_avatar'] ?? $defaultStoreImage; 

                        // For now, just use random images but switch to the code above at launch
                        //$imageUrl = $fallbackImages[array_rand($fallbackImages)];
                    ?>

                    <div class="related-item rounded" data-id="<?= $store['store_id'] ?>">
                        <div class="related-item-inner border rounded">
                            <div class="related-item-inner-item">
                                <img src="<?= htmlspecialchars($imageUrl) ?>" class="img-fluid w-100 rounded-top" alt="Product Image">
                                <!--<div class="related-new">New</div>-->
                                <div class="related-details">
                                    <a href="shop?id=<?= $store['store_id'] ?>"><i class="fa fa-eye fa-1x"></i></a>
                                </div>
                            </div>
                            <div class="text-center rounded-bottom p-4">
                                <a href="#" class="d-block mb-2"><?= ($store['product_count'] > 1000) ? 'Best-selling Brand' : 'Top Brand' ?></a>
                                <a href="shop?id=<?= $store['store_id'] ?>" class="d-block h4"><?= htmlspecialchars($store['store_name']) ?></a>
                                <span class="text-muted font-normal">
                                    <?php
                                        $desc = strip_tags($store['store_description'] ?? ''); // Remove HTML tags just in case
                                        $shortDesc = mb_substr($desc, 0, 100); // Get first 100 characters safely (supports UTF-8)
                                        if (mb_strlen($desc) > 100) {
                                            $shortDesc .= '...';
                                        }
                                        echo htmlspecialchars($shortDesc);
                                    ?>
                                </span>
                            </div>
                            <div class="product-item-add border border-top-0 rounded-bottom  text-center p-4 pt-0">
                                <a href="shop?id=<?= $store['store_id'] ?>" class="btn btn-primary border-primary rounded-pill py-2 px-4 mb-4"><i class="fas fa-arrow-right me-2"></i> View Brand</a>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex">
                                        <!--
                                        <a href="#" class="text-primary d-flex align-items-center justify-content-center me-3">
                                            <span class="rounded-circle btn-sm-square border">
                                                <i class="fas fa-random"></i>
                                            </span>
                                        </a>
                                        <a href="#" class="text-primary d-flex align-items-center justify-content-center me-0">
                                            <span class="rounded-circle btn-sm-square border">
                                                <i class="fas fa-heart"></i>
                                            </span>
                                        </a>
                                        -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

            </div>
            
            <div class="col-12 wow fadeInUp mag-negative" data-wow-delay="0.1s">
                <div class="pagination d-flex justify-content-center">
                    <?php if (!empty($topBrands['brands'])): ?>
                        <!-- Previous button -->
                        <a href="<?= ($currentPage > 1) ? pageUrl($currentPage - 1, $baseUrl) : '#' ?>" class="rounded <?= ($currentPage <= 1) ? 'disabled' : '' ?>">&laquo;</a>
    
                        <!-- Page number links -->
                        <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                            <a href="<?= pageUrl($page, $baseUrl) ?>" class="<?= ($page == $currentPage) ? 'active' : '' ?> rounded"><?= $page ?></a>
                        <?php endfor; ?>

                        <!-- Next button -->
                        <a href="<?= ($currentPage < $totalPages) ? pageUrl($currentPage + 1, $baseUrl) : '#' ?>" class="rounded <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">&raquo;</a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
    <!-- Top Brands End -->

    <!-- Product Banner Start -->
    <div class="container-fluid py-5">
        <div class="container pb-5">
            <div class="row g-4">
                <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
                    <a href="#">
                        <div class="bg-primary rounded position-relative">
                            <img src="assets/img/product-banner.jpg" class="img-fluid w-100 rounded" alt="">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center rounded p-4"
                                style="background: rgba(255, 255, 255, 0.5);">
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
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center rounded p-4"
                                style="background: rgba(242, 139, 0, 0.5);">
                                <h2 class="display-2 text-secondary">SALE</h2>
                                <h4 class="display-5 text-white mb-4">Get UP To 50% Off</h4>
                                <a href="#" class="btn btn-secondary rounded-pill align-self-center py-2 px-4">Shop
                                    Now</a>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Product Banner End -->

    <!-- Include the footer section code file -->
    <?php include_once 'includes/footer.php'; ?>

    <!-- Add user defined scripts here -->
</body>

</html>