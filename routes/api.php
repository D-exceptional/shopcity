<?php

// Import security middlewares and controllers
use App\Middlewares\{
    AuthMiddleware,
    CsrfMiddleware,
    RateLimitMiddleware,
};

// Import controllers
use App\Controllers\{
    UserController,
    ProductController,
    ProductMediaController,
    LinkController,
    NotificationController,
    CategoryController,
    CartController,
    OrderController,
    CheckoutController,
    StoreController,
    WalletController,
    MailController,
    RedisController,
    TestController,
    WishlistController
};

/** @var Router $router */

// -------------------------------------------------
// TEST ROUTES
// ------------------------------------------------
$router->group('/api/test', function ($router) {
    $router->get('/ping', [TestController::class, 'ping']);
},
// Add all group middlewares here
[
    [RateLimitMiddleware::class, 'handle', ['scope' => 'api', 'userLimit' => 100, 'anonLimit' => 20]],
]
);

// -------------------------------------------------
// USER ROUTES
// -------------------------------------------------
$router->group('/api/user', function ($router) {
    $router->get('/count', [UserController::class, 'count'], [[AuthMiddleware::class, 'admin']]);
    $router->get('/fetch', [UserController::class, 'fetch'], [[AuthMiddleware::class, 'admin']]);
    $router->get('/id', [UserController::class, 'id'], [[AuthMiddleware::class, 'admin']]);
    $router->post('/register', [UserController::class, 'register']);
    $router->post('/login', [UserController::class, 'login']);
    $router->post('/otp', [UserController::class, 'otp']);
    $router->post('/logout', [UserController::class, 'logout']);
    $router->post('/contact', [UserController::class, 'contact']);
    $router->post('/subscribe', [UserController::class, 'subscribe']);
    $router->put('/password/reset', [UserController::class, 'reset']);
    $router->put('/update', [UserController::class, 'update'], [[CsrfMiddleware::class, 'handle']]);
    $router->put('/profile', [UserController::class, 'profile'], [[CsrfMiddleware::class, 'handle']]);
    $router->put('/social', [UserController::class, 'social'], [[CsrfMiddleware::class, 'handle']]);
    $router->put('/password/change', [UserController::class, 'password'], [[CsrfMiddleware::class, 'handle']]);
    $router->put('/status', [UserController::class, 'status'], [[AuthMiddleware::class, 'admin'], [CsrfMiddleware::class, 'handle']]);
    $router->put('/billing', [UserController::class, 'billing'], [[AuthMiddleware::class, 'user'], [CsrfMiddleware::class, 'handle']]);
    $router->delete('/unsubscribe', [UserController::class, 'unsubscribe'], [[CsrfMiddleware::class, 'handle']]);
    $router->delete('/delete', [UserController::class, 'delete'], [[AuthMiddleware::class, 'admin'], [CsrfMiddleware::class, 'handle']]);
},
// Add all group middlewares here
[
    [RateLimitMiddleware::class, 'handle', ['scope' => 'api', 'userLimit' => 100, 'anonLimit' => 20]],
]
);

// -------------------------------------------------
// PRODUCT ROUTES
// -------------------------------------------------
$router->group('/api/products', function ($router) {
    $router->get('', [ProductController::class, 'findByAll']);
    $router->get('/category', [ProductController::class, 'findByCategory']);
    $router->get('/store', [ProductController::class, 'findByStore']);
    $router->get('/store/category', [ProductController::class, 'findByStoreCategory']);
    $router->get('/recent', [ProductController::class, 'findNewArrivals']);
    $router->get('/featured', [ProductController::class, 'findFeatured']);
    $router->get('/top-selling', [ProductController::class, 'findTopSelling']);
    $router->get('/price/range', [ProductController::class, 'findByPriceRange']);
    $router->get('/price/min', [ProductController::class, 'findByMinPrice']);
    $router->get('/price/max', [ProductController::class, 'findByMaxPrice']);
    $router->get('/category/group', [ProductController::class, 'findByGroupedCategory']);
    $router->get('/find', [ProductController::class, 'findOne']);
    $router->get('/search', [ProductController::class, 'findBySearch']);
    $router->get('/color', [ProductController::class, 'findByColor']);
    $router->post('', [ProductController::class, 'create'], [[AuthMiddleware::class, 'vendor']]);
    $router->post('/review', [ProductController::class, 'addReview'], [[AuthMiddleware::class, 'user']]);
    $router->put('', [ProductController::class, 'update'], [[AuthMiddleware::class, 'adminOrVendor'], [CsrfMiddleware::class, 'handle']]);
    $router->delete('', [ProductController::class, 'delete'], [[AuthMiddleware::class, 'adminOrVendor'], [CsrfMiddleware::class, 'handle']]);
},
// Add all group middlewares here
[
    [RateLimitMiddleware::class, 'handle', ['scope' => 'api', 'userLimit' => 100, 'anonLimit' => 20]],
]
);

// -------------------------------------------------
// PRODUCT MEDIA ROUTES
// -------------------------------------------------
$router->group('/api/products/media', function ($router) {
    $router->get('', [ProductMediaController::class, 'findAll']);
    $router->get('/find', [ProductMediaController::class, 'findOne']);
    $router->put('/update', [ProductMediaController::class, 'update'], [[AuthMiddleware::class, 'vendor'], [CsrfMiddleware::class, 'handle']]);
    $router->delete('/delete', [ProductMediaController::class, 'deleteAll'], [[AuthMiddleware::class, 'adminOrVendor'], [CsrfMiddleware::class, 'handle']]);
    $router->delete('/delete/single', [ProductMediaController::class, 'deleteOne'], [[AuthMiddleware::class, 'adminOrVendor'], [CsrfMiddleware::class, 'handle']]);
    $router->delete('/delete/bulk', [ProductMediaController::class, 'deleteBulk'], [[AuthMiddleware::class, 'adminOrVendor'], [CsrfMiddleware::class, 'handle']]);
},
// Add all group middlewares here
[
    [RateLimitMiddleware::class, 'handle', ['scope' => 'api', 'userLimit' => 100, 'anonLimit' => 20]],
]
);

// -------------------------------------------------
// LINK ROUTES
// -------------------------------------------------
$router->group('/api/links', function ($router) {
    $router->get('', [LinkController::class, 'findAll']);
    $router->get('/find', [LinkController::class, 'findOne']);
    $router->post('', [LinkController::class, 'create'], [[AuthMiddleware::class, 'adminOrVendor']]);
    $router->put('/update/all', [LinkController::class, 'updateAll'], [[AuthMiddleware::class, 'adminOrVendor'], [CsrfMiddleware::class, 'handle']]);
    $router->put('/update/single', [LinkController::class, 'updateOne'], [[AuthMiddleware::class, 'adminOrVendor'], [CsrfMiddleware::class, 'handle']]);
    $router->delete('', [LinkController::class, 'deleteAll'], [[AuthMiddleware::class, 'adminOrVendor'], [CsrfMiddleware::class, 'handle']]);
    $router->delete('/delete/single', [LinkController::class, 'deleteOne'], [[AuthMiddleware::class, 'adminOrVendor'], [CsrfMiddleware::class, 'handle']]);
},
// Add all group middlewares here
[
    [RateLimitMiddleware::class, 'handle', ['scope' => 'api', 'userLimit' => 100, 'anonLimit' => 20]],
]
);

// -------------------------------------------------
// NOTIFICATION ROUTES
// -------------------------------------------------
$router->group('/api/notification', function ($router) {
    $router->get('', [NotificationController::class, 'fetchById'], [[AuthMiddleware::class, 'adminOrVendor']]);
    $router->get('/all/count', [NotificationController::class, 'countAll'], [[AuthMiddleware::class, 'admin']]);
    $router->get('/user/count', [NotificationController::class, 'countAllById'], [[AuthMiddleware::class, 'adminOrVendor']]);
    $router->get('/unread/count', [NotificationController::class, 'countUnreadById'], [[AuthMiddleware::class, 'adminOrVendor']]);
    $router->get('/unread', [NotificationController::class, 'getUnread'], [[AuthMiddleware::class, 'adminOrVendor']]);
    $router->post('', [NotificationController::class, 'create']);
    $router->put('/mark/read', [NotificationController::class, 'markAsRead'], [[AuthMiddleware::class, 'adminOrVendor'], [CsrfMiddleware::class, 'handle']]);
},
// Add all group middlewares here
[
    [RateLimitMiddleware::class, 'handle', ['scope' => 'api', 'userLimit' => 100, 'anonLimit' => 20]],
]
);

// -------------------------------------------------
// CATEGORY ROUTES
// -------------------------------------------------
$router->group('/api/categories', function ($router) {
    $router->get('', [CategoryController::class, 'all']);
    $router->get('/with-products', [CategoryController::class, 'group']);
    $router->get('/fetch', [CategoryController::class, 'fetch'], [[AuthMiddleware::class, 'vendor']]);
    $router->get('/count', [CategoryController::class, 'count'], [[AuthMiddleware::class, 'admin']]);
    $router->post('', [CategoryController::class, 'create'], [[AuthMiddleware::class, 'admin']]);
    $router->put('', [CategoryController::class, 'update'], [[AuthMiddleware::class, 'admin'], [CsrfMiddleware::class, 'handle']]);
    $router->delete('', [CategoryController::class, 'delete'], [[AuthMiddleware::class, 'admin'], [CsrfMiddleware::class, 'handle']]);
},
// Add all group middlewares here
[
    [RateLimitMiddleware::class, 'handle', ['scope' => 'api', 'userLimit' => 100, 'anonLimit' => 20]],
]
);

// -------------------------------------------------
// CART ROUTES
// -------------------------------------------------
$router->group('/api/cart', function ($router) {
    $router->get('', [CartController::class, 'view'], [[AuthMiddleware::class, 'user']]);
    $router->get('/count/user', [CartController::class, 'countUser'], [[AuthMiddleware::class, 'user']]);
    $router->get('/count/all', [CartController::class, 'countAll'], [[AuthMiddleware::class, 'admin']]);
    $router->get('/users', [CartController::class, 'getCartUsers'], [[AuthMiddleware::class, 'admin']]);
    $router->post('', [CartController::class, 'add'], [[AuthMiddleware::class, 'user']]);
    $router->put('', [CartController::class, 'update'], [[AuthMiddleware::class, 'user'], [CsrfMiddleware::class, 'handle']]);
    $router->put('/merge', [CartController::class, 'merge'], [[AuthMiddleware::class, 'user'], [CsrfMiddleware::class, 'handle']]);
    $router->delete('', [CartController::class, 'remove'], [[AuthMiddleware::class, 'user'], [CsrfMiddleware::class, 'handle']]);
    $router->delete('/clear', [CartController::class, 'clear'], [[AuthMiddleware::class, 'user'], [CsrfMiddleware::class, 'handle']]);
},
// Add all group middlewares here
[
    [RateLimitMiddleware::class, 'handle', ['scope' => 'api', 'userLimit' => 100, 'anonLimit' => 20]],
]
);

// -------------------------------------------------
// CHECKOUT ROUTES
// -------------------------------------------------
$router->group('/api/checkout', function ($router) {
    $router->post('', [CheckoutController::class, 'processCheckout'], [[AuthMiddleware::class, 'user']]);
},
// Add all group middlewares here
[
    [RateLimitMiddleware::class, 'handle', ['scope' => 'api', 'userLimit' => 100, 'anonLimit' => 20]],
]
);

// -------------------------------------------------
// ORDER ROUTES
// -------------------------------------------------
$router->group('/api/order', function ($router) {
    $router->get('/find', [OrderController::class, 'getOrder'], [[AuthMiddleware::class, 'user']]);
    $router->get('', [OrderController::class, 'getAllOrders'], [[AuthMiddleware::class, 'admin']]);
    $router->get('/find/status', [OrderController::class, 'getOrdersByStatus'], [[AuthMiddleware::class, 'admin']]);
    $router->get('/user', [OrderController::class, 'getUserOrders'], [[AuthMiddleware::class, 'user']]);
    $router->get('/store', [OrderController::class, 'getStoreOrders'], [[AuthMiddleware::class, 'vendor']]);
    $router->get('/store/status', [OrderController::class, 'getStoreOrdersByStatus'], [[AuthMiddleware::class, 'vendor']]);
    $router->get('/track', [OrderController::class, 'trackOrder'], [[AuthMiddleware::class, 'user']]);
    $router->get('/sales/summary', [OrderController::class, 'getSalesSummary'], [[AuthMiddleware::class, 'adminOrVendor']]);
    $router->put('/complete', [OrderController::class, 'completeOrder'], [[AuthMiddleware::class, 'admin'], [CsrfMiddleware::class, 'handle']]);
    $router->put('/item/status', [OrderController::class, 'updateItemStatus'], [[AuthMiddleware::class, 'vendor'], [CsrfMiddleware::class, 'handle']]);
    $router->delete('/cancel', [OrderController::class, 'cancelOrder'], [[AuthMiddleware::class, 'user'], [CsrfMiddleware::class, 'handle']]);
},
// Add all group middlewares here
[
    [RateLimitMiddleware::class, 'handle', ['scope' => 'api', 'userLimit' => 100, 'anonLimit' => 20]],
]
);

// -------------------------------------------------
// STORE ROUTES
// -------------------------------------------------
$router->group('/api/store', function ($router) {
    $router->get('', [StoreController::class, 'findOne'], [[AuthMiddleware::class, 'vendor']]);
    $router->get('/list/status', [StoreController::class, 'findByStatus'], [[AuthMiddleware::class, 'admin']]);
    $router->get('/list/user', [StoreController::class, 'findByUser'], [[AuthMiddleware::class, 'admin']]);
    $router->get('/coupon', [StoreController::class, 'findCouponsByStore'], [[AuthMiddleware::class, 'vendor']]);
    $router->get('/coupon/status', [StoreController::class, 'findCouponsByStoreAndStatus'], [[AuthMiddleware::class, 'vendor']]);
    $router->get('/count/status', [StoreController::class, 'countStoresByStatus'], [[AuthMiddleware::class, 'admin']]);
    $router->get('/customer', [StoreController::class, 'findStoreCustomers'], [[AuthMiddleware::class, 'vendor']]);
    $router->post('', [StoreController::class, 'createStore'], [[AuthMiddleware::class, 'vendor']]);
    $router->post('/coupon', [StoreController::class, 'createCoupon'], [[AuthMiddleware::class, 'vendor']]);
    $router->post('/coupon/check', [StoreController::class, 'findCoupon']);
    $router->put('/coupon', [StoreController::class, 'updateCoupon'], [[AuthMiddleware::class, 'vendor'], [CsrfMiddleware::class, 'handle']]);
    $router->put('/details', [StoreController::class, 'updateStoreDetails'], [[AuthMiddleware::class, 'vendor'], [CsrfMiddleware::class, 'handle']]);
    $router->put('/socials', [StoreController::class, 'updateStoreSocials'], [[AuthMiddleware::class, 'vendor'], [CsrfMiddleware::class, 'handle']]);
    $router->put('/avatar', [StoreController::class, 'updateStoreAvatar'], [[AuthMiddleware::class, 'vendor'], [CsrfMiddleware::class, 'handle']]);
    $router->put('/status', [StoreController::class, 'updateStoreStatus'], [[AuthMiddleware::class, 'admin'], [CsrfMiddleware::class, 'handle']]);
    $router->delete('', [StoreController::class, 'deleteStore'], [[AuthMiddleware::class, 'admin'], [CsrfMiddleware::class, 'handle']]);
    $router->delete('/coupon', [StoreController::class, 'deleteSingleCoupon'], [[AuthMiddleware::class, 'vendor'], [CsrfMiddleware::class, 'handle']]);
    $router->delete('/coupon/all', [StoreController::class, 'deleteCouponByStore'], [[AuthMiddleware::class, 'vendor'], [CsrfMiddleware::class, 'handle']]);
},
// Add all group middlewares here
[
    [RateLimitMiddleware::class, 'handle', ['scope' => 'api', 'userLimit' => 100, 'anonLimit' => 20]],
]
);

// -------------------------------------------------
// WALLET ROUTES
// -------------------------------------------------
$router->group('/api/wallet', function ($router) {
    $router->get('/payment/reference', [WalletController::class, 'getByReference']);
    $router->get('/payments/user', [WalletController::class, 'getPaymentsByUser'], [[AuthMiddleware::class, 'customerOrVendor']]);
    $router->get('/payments/type', [WalletController::class, 'getPaymentsByType'], [[AuthMiddleware::class, 'admin']]);
    $router->get('/payments/status', [WalletController::class, 'getPaymentsByStatus'], [[AuthMiddleware::class, 'admin']]);
    $router->get('/payouts/status', [WalletController::class, 'getPayoutsByStatus'], [[AuthMiddleware::class, 'admin']]);
    $router->post('/fund', [WalletController::class, 'createPayment'], [[AuthMiddleware::class, 'user']]);
    $router->post('/payment/verify', [WalletController::class, 'verifyPayment']);
    $router->post('/fund/redeem', [WalletController::class, 'redeemFunds'], [[AuthMiddleware::class, 'admin']]);
    $router->post('/fund/request', [WalletController::class, 'requestFunds'], [[AuthMiddleware::class, 'vendor']]);
    $router->post('/transfer/single', [WalletController::class, 'singleTransfer'], [[AuthMiddleware::class, 'admin']]);
    $router->post('/transfer/bulk', [WalletController::class, 'bulkTransfer'], [[AuthMiddleware::class, 'admin']]);
    $router->put('/details/update', [WalletController::class, 'updateDetails'], [[AuthMiddleware::class, 'vendor'], [CsrfMiddleware::class, 'handle']]);
},
// Add all group middlewares here
[
    [RateLimitMiddleware::class, 'handle', ['scope' => 'api', 'userLimit' => 100, 'anonLimit' => 20]],
]
);

// -------------------------------------------------
// MAIL ROUTES
// -------------------------------------------------
$router->group('/api/mail', function ($router) {
    $router->get('/inbox/count', [MailController::class, 'countInbox'], [[AuthMiddleware::class, 'check']]);
    $router->get('/outbox/count', [MailController::class, 'countOutbox'], [[AuthMiddleware::class, 'check']]);
    $router->get('/inbox', [MailController::class, 'getInbox'], [[AuthMiddleware::class, 'check']]);
    $router->get('/outbox', [MailController::class, 'getOutbox'], [[AuthMiddleware::class, 'check']]);
    $router->get('', [MailController::class, 'getMail']);
    $router->post('/send', [MailController::class, 'sendBulk'], [[AuthMiddleware::class, 'admin']]);
    $router->delete('', [MailController::class, 'deleteMail'], [[AuthMiddleware::class, 'admin'], [CsrfMiddleware::class, 'handle']]);
},
// Add all group middlewares here
[
    [RateLimitMiddleware::class, 'handle', ['scope' => 'api', 'userLimit' => 100, 'anonLimit' => 20]],
]
);

// -------------------------------------------------
// WISHLIST ROUTES
// -------------------------------------------------
$router->group('/api/wishlist', function ($router) {
    $router->get('', [WishlistController::class, 'view'], [[AuthMiddleware::class, 'user']]);
    $router->post('', [WishlistController::class, 'add'], [[AuthMiddleware::class, 'user']]);
    $router->delete('', [WishlistController::class, 'remove'], [[AuthMiddleware::class, 'user'], [CsrfMiddleware::class, 'handle']]);
    $router->put('/merge', [WishlistController::class, 'merge'], [[AuthMiddleware::class, 'user'], [CsrfMiddleware::class, 'handle']]);
    $router->delete('/clear', [WishlistController::class, 'clear'], [[AuthMiddleware::class, 'user'], [CsrfMiddleware::class, 'handle']]);
},
// Add all group middlewares here
[
    [RateLimitMiddleware::class, 'handle', ['scope' => 'api', 'userLimit' => 100, 'anonLimit' => 20]],
]
);
