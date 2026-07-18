<?php
    // Import Initializer File
    require_once __DIR__ . '/includes/init.php';

    // Import Default Images File (For Development Phase ONLY)
    require_once __DIR__ . '/includes/helper.php';

    // Initialize Necessary Models
    $orderModel = $container->get(\App\Models\Order::class); 

    // Get Page
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

    // Get Orders
    $orderList = $isLoggedIn ? $orderModel->getUserOrders($userId, $page) : [];

    // Pagination parameters
    $totalPages  = $orderList['total_pages'] ?? 1;
    $currentPage = $orderList['page'] ?? 1;
    $baseUrl     = "orders?view=user";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= SITE_NAME ?> | Orders</title>
    <!-- Include the head section code file -->
    <?php include_once 'includes/head.php'; ?>
</head>

<body>

    <!-- Include the header section code file -->
    <?php include_once 'includes/header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Orders Page</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Orders</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Cart Page Start -->
    <div class="container-fluid py-5">
         <div class="container pt-2">
            <h1>My Orders</h1>
            <hr>
        </div>
        <div class="container py-5">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <?php if ($isLoggedIn && !empty($orderList['orders'])): ?>
                            <tr>
                                <th scope="col">S/N</th>
                                <th scope="col">Tracking ID</th>
                                <th scope="col">Shipping Address</th>
                                <th scope="col">Total Payment</th>
                                <th scope="col">Status</th>
                                <th scope="col">Date</th>
                                <th scope="col">Action</th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>

                        <?php if ($isLoggedIn): ?>
                            <?php if (!empty($orderList['orders'])): ?>
                                <!-- 🛒 LOGGED IN + ORDER LIST HAS ITEMS -->
                                <?php foreach ($orderList['orders'] as $order): ?>

                                    <tr data-id="<?= $order['order_id'] ?>">
                                        <td>
                                            <p class="mb-0 py-4">#</p>
                                        </td>
                                        <th scope="row">
                                            <p class="mb-0 py-4"><?= htmlspecialchars($order['tracking_code']) ?></p>
                                        </th>
                                        <td>
                                            <p class="mb-0 py-4"><?= htmlspecialchars($order['shipping_address'] ?? 'Nigeria') ?></p>
                                        </td>
                                        <td>
                                            <p class="mb-0 py-4"><?= $currencyManager->format((float)$order['total_amount'] ?? 0); ?></p>
                                        </td>
                                        <td class="py-4">
                                            <button class="btn btn-<?= ($order['order_status'] === 'Pending') ? 'danger' : 'success' ?> btn-sm">
                                               <?= htmlspecialchars($order['order_status']) ?>
                                            </button>
                                        </td>
                                        <td>
                                            <p class="mb-0 py-4"><?= htmlspecialchars($order['created_at']) ?></p>
                                        </td>
                                        <td class="py-4">
                                            <button class="btn btn-info btn-sm">
                                                <a href="order-details?id=<?= $order['order_id'] ?>" class="text-decoration-none text-white">
                                                    <i class="fa fa-arrow-right me-1"></i> View
                                                </a>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                            <?php else: ?>
                                <!-- 🧺 LOGGED IN + CART EMPTY -->
                                <tr>
                                    <td colspan="7" class="text-center py-5">
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
                                <td colspan="7" class="text-center py-5">
                                    <i class="fa fa-user-lock fa-3x text-muted mb-3"></i>
                                    <p class="lead mb-2">Login to view your orders.</p>
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
</body>
</html>