<?php
    // Import Initializer File
    require_once __DIR__ . '/includes/init.php';

    // Import Default Images File (For Development Phase ONLY)
    require_once __DIR__ . '/includes/helper.php';

    // Get Products Data
    $cartItems = $isLoggedIn ? $cartModel->view($userId) : [];

    // Get total cart amount
    $totalAmount = 0;
    if (!empty($cartItems)) {
        foreach ($cartItems as $item) {
            $totalAmount += (float)$item['total_price'];
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= SITE_NAME ?> | Cart</title>
    <!-- Include the head section code file -->
    <?php include_once 'includes/head.php'; ?>
</head>

<body>

    <!-- Include the header section code file -->
    <?php include_once 'includes/header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Cart Page</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Cart</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Cart Page Start -->
    <div class="container-fluid py-5">
        <div class="container pt-2">
            <h1>My Cart</h1>
            <hr>
        </div>
        <div class="container py-5">
            <div class="table-responsive <?= $isLoggedIn && !empty($cartItems) ? '' : 'nmt-25' ?>">
                <table class="table">
                    <thead>
                        <?php if ($isLoggedIn && !empty($cartItems)): ?>
                            <tr>
                                <th scope="col">S/N</th>
                                <th scope="col">Image</th>
                                <th scope="col">Name</th>
                                <th scope="col">Category</th>
                                <th scope="col">Price</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Total</th>
                                <th scope="col">Action</th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>

                        <?php if ($isLoggedIn): ?>
                            <?php if (!empty($cartItems)): ?>
                                <!-- 🛒 LOGGED IN + CART HAS ITEMS -->
                                <?php foreach ($cartItems as $item): ?>
                                    <?php
                                        // Prefer actual product image, fallback to random image
                                        $imageUrl = $item['product_image'] ?? null;
                                    ?>

                                    <tr class="cart-item table-item item-shopping" data-id="<?= $item['cart_id'] ?>" data-pid="<?= $item['product_id'] ?>">
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
                                            <p class="mb-0 py-4 item-price"><?= $currencyManager->format((float)$item['product_price'] ?? 0); ?></p>
                                        </td>
                                        <td>
                                            <div class="input-group quantity py-4" style="width: 100px;">
                                                <div class="input-group-btn">
                                                    <button class="btn btn-sm btn-minus rounded-circle bg-light border">
                                                        <i class="fa fa-minus"></i>
                                                    </button>
                                                </div>
                                                <input type="text" class="form-control form-control-sm text-center border-0 item-quantity" value="<?= $item['quantity'] ?? 1 ?>">
                                                <div class="input-group-btn">
                                                    <button class="btn btn-sm btn-plus rounded-circle bg-light border">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="mb-0 py-4 item-total"><?= $currencyManager->format((float)$item['total_price'] ?? 0); ?></p>
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
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fa fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <p class="lead mb-2">Your cart is empty.</p>
                                        <a href="./" class="btn btn-primary btn-sm">
                                            <i class="fa fa-shopping-bag me-1"></i> Start Shopping
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>

                        <?php else: ?>
                            <!-- 🚫 NOT LOGGED IN -->
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fa fa-user-lock fa-3x text-muted mb-3"></i>
                                    <p class="lead mb-2">Login to view your cart.</p>
                                    <a href="login" class="btn btn-primary btn-sm">
                                        <i class="fa fa-sign-in-alt me-1"></i> Log In
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

            <?php if ($isLoggedIn): ?>
                <?php if (!empty($cartItems)): ?>

                    <div class="row g-4 justify-content-end">
                        <div class="col-8"></div>
                        <div class="col-sm-8 col-md-7 col-lg-6 col-xl-4">
                            <div class="bg-light rounded">
                                <div class="p-4">
                                    <h1 class="display-6 mb-4">Cart <span class="fw-normal">Total</span></h1>
                                    <div class="d-flex justify-content-between mb-4">
                                        <h5 class="mb-0 me-4">Subtotal:</h5>
                                        <p class="mb-0 sub-total"><?= $currencyManager->format((float)$totalAmount ?? 0); ?></p>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <h5 class="mb-0 me-4">Shipping</h5>
                                        <div>
                                            <p class="mb-0">Flat rate: <?= $currencyManager->format((float)FLAT_RATE); ?></p>
                                        </div>
                                    </div>
                                    <p class="mb-0 text-end">Shipping within Nigeria.</p>
                                </div>
                                <div class="py-4 mb-4 border-top border-bottom d-flex justify-content-between">
                                    <h5 class="mb-0 ps-4 me-4">Total</h5>
                                    <p class="mb-0 pe-4 cart-sum"><?= $currencyManager->format((float)$totalAmount + (float)FLAT_RATE); ?></p>
                                </div>
                               <button class="btn btn-primary rounded-pill px-4 py-3 text-uppercase mb-4 ms-4 text-white" type="button">
                                    <a href="checkout" class="text-decoration-none text-white">Proceed To Checkout</a>
                                </button>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <!-- Cart Page End -->

    <!-- Include the footer section code file -->
    <?php include_once 'includes/footer.php'; ?>

    <!-- Add user defined scripts here -->
    <script src="<?php echo $cacheManager->parse('assets/js/cart.js'); ?>" type="module"></script>

</body>
</html>