<?php
    // Import Initializer File
    require_once __DIR__ . '/includes/init.php';

    // Import Default Images File (For Development Phase ONLY)
    require_once __DIR__ . '/includes/helper.php';

    // Initialize Necessary Models
    $wishlistModel = $container->get(\App\Models\Wishlist::class);

    // Get Page
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

    // Get  Wishlist Data
    $wishlistItems = $isLoggedIn ? $wishlistModel->view($userId, $page) : [];

    // Pagination parameters
    $totalPages  = $wishlistItems['total_pages'] ?? 1;
    $currentPage = $wishlistItems['page'] ?? 1;
    $baseUrl     = "wishlist?view=user";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= SITE_NAME ?> | Wishlist</title>
    <!-- Include the head section code file -->
    <?php include_once 'includes/head.php'; ?>
</head>

<body>

    <!-- Include the header section code file -->
    <?php include_once 'includes/header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Wishlist Page</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Wishlist</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Cart Page Start -->
    <div class="container-fluid py-5">
        <div class="container pt-2">
            <h1>My Wishlist</h1>
            <hr>
        </div>
        <div class="container py-5">
            <div class="table-responsive <?= $isLoggedIn && !empty($wishlistItems['wishlist']) ? '' : 'nmt-25' ?>">
                <table class="table">
                    <thead>
                        <?php if ($isLoggedIn && !empty($wishlistItems['wishlist'])): ?>
                            <tr>
                                <th scope="col">S/N</th>
                                <th scope="col">Image</th>
                                <th scope="col">Name</th>
                                <th scope="col">Category</th>
                                <th scope="col">Price</th>
                                <th scope="col">Action</th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>

                        <?php if ($isLoggedIn): ?>
                            <?php if (!empty($wishlistItems['wishlist'])): ?>
                                <!-- 🛒 LOGGED IN + CART HAS ITEMS -->
                                <?php foreach ($wishlistItems['wishlist'] as $item): ?>
                                    <?php
                                        // Prefer actual product image, fallback to random image
                                        $imageUrl = $item['product_image'] ?? $defaultProductImage;

                                        // For now, just use random images but switch to the code above at launch
                                        // $imageUrl = $fallbackImages[array_rand($fallbackImages)];
                                    ?>

                                    <tr class="table-item item-wishlist" data-id="<?= $item['wishlist_id'] ?>" data-pid="<?= $item['product_id'] ?>">
                                        <td>
                                            <p class="mb-0 py-4">#</p>
                                        </td>
                                        <th scope="row">
                                            <img src="<?= htmlspecialchars($imageUrl) ?>" class="img-fluid rounded product-image" alt="Product Image"/>
                                        </th>
                                        <th scope="row">
                                            <p class="mb-0 py-4 product-name"><?= htmlspecialchars($item['product_name']) ?></p>
                                        </th>
                                        <td>
                                            <p class="mb-0 py-4"><?= htmlspecialchars($item['category']) ?></p>
                                        </td>
                                        <td>
                                            <p class="mb-0 py-4"><?= $currencyManager->format((float)$item['product_price'] ?? 0); ?></p>
                                        </td>
                                        <td class="py-4">
                                            <button class="btn btn-md rounded-circle bg-light border item-remove">
                                                <i class="fa fa-times text-danger"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                            <?php else: ?>
                                <!-- 🧺 LOGGED IN + CART EMPTY -->
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fa fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <p class="lead mb-2">Your wishlist is empty.</p>
                                        <a href="/" class="btn btn-primary btn-sm">
                                            <i class="fa fa-shopping-bag me-1"></i> Start Shopping
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>

                        <?php else: ?>
                            <!-- 🚫 NOT LOGGED IN -->
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fa fa-user-lock fa-3x text-muted mb-3"></i>
                                    <p class="lead mb-2">Login to view your wishlist.</p>
                                    <a href="login" class="btn btn-primary btn-sm">
                                        <i class="fa fa-sign-in-alt me-1"></i> Log In
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
            <!-- Show Pagination Links --->
            <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                <?php if ($isLoggedIn): ?>
                    <?php if (!empty($orderList['orders'])): ?>
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
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Cart Page End -->

    <!-- Include the footer section code file -->
    <?php include_once 'includes/footer.php'; ?>

    <!-- Add user defined scripts here -->
    <script src="<?php echo $cacheManager->parse('assets/js/wishlist.js'); ?>" type="module"></script>
    
</body>
</html>