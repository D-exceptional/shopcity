<?php
    // Import Initializer File
    require_once __DIR__ . '/includes/init.php';

    // Import Default Images File (For Development Phase ONLY)
    require_once __DIR__ . '/includes/helper.php';

    // Initialize Necessary Models
    $orderModel = $container->get(\App\Models\Order::class);

    // Get Page
    $orderId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

    // Get Orders
    $orderDetails = $isLoggedIn ? $orderModel->getOrder($orderId) : [];

    // Status maps
    $statusMap = [
        'Pending' => [
            'status' => ['prop' => 'danger'],
            'action' => ['prop' => 'info', 'text' => 'View'],
        ],
        'Processing' => [
            'status' => ['prop' => 'info'],
            'action' => ['prop' => 'info', 'text' => 'View'],
        ],
        'Shipped' => [
            'status' => ['prop' => 'warning'],
            'action' => ['prop' => 'success', 'text' => 'I have received this item'],
        ],
        'Delivered' => [
            'status' => ['prop' => 'success'],
            'action' => ['prop' => 'success', 'text' => 'Completed'],
        ],
    ];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= SITE_NAME ?> | Order</title>
    <!-- Include the head section code file -->
    <?php include_once 'includes/head.php'; ?>
</head>

<body>

    <!-- Include the header section code file -->
    <?php include_once 'includes/header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Order Page</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Order</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Cart Page Start -->
    <div class="container-fluid py-5">
        <div class="container pt-2">
            <h1>Order Details</h1>
            <hr>
        </div>
        <div class="container py-5">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <?php if ($isLoggedIn && !empty($orderDetails['items'])): ?>
                            <tr>
                                <th scope="col">S/N</th>
                                <th scope="col">Image</th>
                                <th scope="col">Product</th>
                                <th scope="col">Price</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Tracking ID</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>

                        <?php if ($isLoggedIn): ?>
                            <?php if ($orderDetails['order_status'] != 'Cancelled' && !empty($orderDetails['items'])): ?>
                                <!-- 🛒 LOGGED IN + ORDER LIST HAS ITEMS -->
                                <?php foreach ($orderDetails['items'] as $item): ?>

                                    <?php
                                        // Pick the product's first media if it exists
                                        $imageUrl = $item['product_image'] ?? null; 
                                    ?>

                                    <tr class="item-card" data-id="<?= $item['item_id'] ?>">
                                        <th scope="row">
                                            <p class="mb-0 py-4">#</p>
                                        </th>
                                        <th scope="row">
                                            <img src="<?= htmlspecialchars($imageUrl) ?>" class="img-fluid rounded" alt="Product Image"/>
                                        </th>
                                        <th scope="row">
                                            <p class="mb-0 py-4"><?= htmlspecialchars($item['product_name']) ?></p>
                                        </th>
                                        <td>
                                            <p class="mb-0 py-4"><?= $currencyManager->format((float)$item['price'] ?? 0); ?></p>
                                        </td>
                                        <th scope="row">
                                            <p class="mb-0 py-4"><?= htmlspecialchars($item['quantity']) ?></p>
                                        </th>
                                        <th scope="row">
                                            <p class="mb-0 py-4"><?= htmlspecialchars($item['tracking_code']) ?></p>
                                        </th>
                                        <td class="py-4">
                                            <button class="btn btn-<?= $statusMap[$item['item_status']]['status']['prop'] ?> btn-sm btn-status">
                                               <?= htmlspecialchars($item['item_status']) ?>
                                            </button>
                                        </td>
                                        <td class="py-4">
                                            <button class="btn btn-<?= $statusMap[$item['item_status']]['action']['prop'] ?> btn-sm btn-order-item text-white">
                                                <?= $statusMap[$item['item_status']]['action']['text'] ?>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                            <?php else: ?>
                                <!-- 🧺 LOGGED IN + CART EMPTY -->
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fa fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <p class="lead mb-2">You have not made any orders yet.</p>
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
                                    <p class="lead mb-2">Login to view your order.</p>
                                    <a href="login" class="btn btn-primary btn-sm">
                                        <i class="fa fa-sign-in-alt me-1"></i> Log In
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
            <!-- Show Cancel Button Here -->
            <?php if ($isLoggedIn): ?>
                <?php if ($orderDetails['order_status'] != 'Cancelled' && !empty($orderDetails['items'])): ?>
                    <div class="col-12 wow fadeInUp pagination-row" data-wow-delay="0.1s">
                        <div class="pagination d-flex justify-content-center mt-5">
                             <?php
                                // Get total cart amount
                                $shippedItems = 0;
                                foreach ($orderDetails['items'] as $item) {
                                    if (in_array($item['item_status'], ['Shipped', 'Delivered'])) {
                                        $shippedItems++;
                                    }
                                }
                            ?>

                            <?php if ($shippedItems === 0): ?>
                                <button class="btn btn-danger btn-sm btn-order-cancel" data-id="<?= $orderDetails['order_id'] ?>">
                                    Cancel Order
                                </button>
                            <?php endif; ?>
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
    <script src="<?php echo $cacheManager->parse('assets/js/orders.js'); ?>" type="module"></script>

</body>
</html>