<?php

declare(strict_types=1);

// Import Middlewares
use App\Http\Middlewares\{
    AuthMiddleware,
    CsrfMiddleware,
    RoleMiddleware,
    RateLimitMiddleware,
};

// Import Controllers
use App\Http\Controllers\Web\{
    AuthController,
    AdminController,
    HomeController,
    StoreController,
    VendorController,
};

// -------------------------------------------------
// AUTH ROUTES
// ------------------------------------------------
$router->group('/auth', function ($router) {
    $router->get('/user/login', [AuthController::class, 'userLogin'], [], 'auth.user.login');
    $router->get('/user/register', [AuthController::class, 'userRegister'], [], 'auth.user.register');
    $router->get('/admin/login', [AuthController::class, 'adminLogin'], [], 'auth.admin.login');
    $router->get('/admin/verify', [AuthController::class, 'adminVerify'], [], 'auth.admin.verify');
    $router->get('/admin/update', [AuthController::class, 'adminUpdate'], [], 'auth.admin.update');
}, [
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope'     => 'web', 
            'userLimit' => 10, 
            'anonLimit' => 5
        ]
    ],
]);

// -------------------------------------------------
// ADMIN ROUTES
// -------------------------------------------------
$router->group('/admin', function ($router) {
    $router->get('/dashboard', [AdminController::class, 'dashboard'], [], 'web.admin.dashboard');
    $router->get('/mail/compose', [AdminController::class, 'mailCompose'], [], 'web.admin.mail.compose');
    $router->get('/mail/{id}', [AdminController::class, 'mailRead'], [], 'web.admin.mail.read');
    $router->get('/mail/outbox/page/{page}', [AdminController::class, 'mailSent'], [], 'web.admin.mail.sent');
    $router->get('/mail/inbox/page/{page}', [AdminController::class, 'mailBox'], [], 'web.admin.mailbox');
    $router->get('/notification', [AdminController::class, 'notification'], [], 'web.admin.notification');
    $router->get('/order/status/{status}/page/{page}', [AdminController::class, 'orderList'], [], 'web.admin.order.list');
    $router->get('/order/{id}', [AdminController::class, 'orderView'], [], 'web.admin.order.view');
    $router->get('/payouts/status/{status}/page/{page}', [AdminController::class, 'payouts'], [], 'web.admin.payouts');
    $router->get('/products/page/{page}', [AdminController::class, 'productList'], [], 'web.admin.product.list');
    $router->get('/product/{id}', [AdminController::class, 'productView'], [], 'web.admin.product.view');
    $router->get('/profile', [AdminController::class, 'profile'], [], 'web.admin.profile');
    $router->get('/stores/page/{page}', [AdminController::class, 'storeList'], [], 'web.admin.store.list');
    $router->get('/users/role/{role}/page/{page}', [AdminController::class, 'users'], [], 'web.admin.users');
}, [
    [RoleMiddleware::class, 'handle', ['role' => ['admin']]],
    [AuthMiddleware::class, 'handle'],
    [CsrfMiddleware::class, 'handle'],
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'web', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);

// -------------------------------------------------
// VENDOR ROUTES
// -------------------------------------------------
$router->group('/vendor', function ($router) {
    $router->get('/dashboard', [VendorController::class, 'dashboard'], [], 'web.vendor.dashboard');
    $router->get('/mail/compose', [VendorController::class, 'mailCompose'], [], 'web.vendor.mail.compose');
    $router->get('/mail/{id}', [VendorController::class, 'mailRead'], [], 'web.vendor.mail.read');
    $router->get('/mail/outbox/page/{page}', [VendorController::class, 'mailSent'], [], 'web.vendor.mail.sent');
    $router->get('/mail/inbox/page/{page}', [VendorController::class, 'mailBox'], [], 'web.vendor.mailbox');
    $router->get('/notification', [VendorController::class, 'notification'], [], 'web.vendor.notification');
    $router->get('/profile', [VendorController::class, 'profile'], [], 'web.vendor.profile');
    $router->get('/store/create', [VendorController::class, 'storeCreate'], [], 'web.vendor.store.create');
    $router->get('/stores', [VendorController::class, 'storeList'], [], 'web.vendor.store.list');
    $router->get('/wallet', [VendorController::class, 'wallet'], [], 'web.vendor.wallet');
    $router->get('/payouts', [VendorController::class, 'withdrawal'], [], 'web.vendor.withdrawal');
}, [
    [RoleMiddleware::class, 'handle', ['role' => ['vendor']]],
    [AuthMiddleware::class, 'handle'],
    [CsrfMiddleware::class, 'handle'],
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'web', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);

// -------------------------------------------------
// STORE ROUTES
// -------------------------------------------------
$router->group('/store', function ($router) {
    $router->get('/{id}/dashboard', [StoreController::class, 'dashboard'], [], 'web.store.dashboard');
    $router->get('/{id}/settings', [StoreController::class, 'settings'], [], 'web.store.settings');
    $router->get('/{id}/product/create', [StoreController::class, 'productCreate'], [], 'web.store.product.create');
    $router->get('/{id}/products/page/{page}', [StoreController::class, 'productList'], [], 'web.store.product.list');
    $router->get('/{id}/product/{pid}/view', [StoreController::class, 'productView'], [], 'web.store.product.view');
    $router->get('/{id}/orders/status/{status}/page/{page}', [StoreController::class, 'orderList'], [], 'web.store.orders');
    $router->get('/{id}/customers/type/{type}/page/{page}', [StoreController::class, 'customers'], [], 'web.store.customers');
    $router->get('/{id}/coupon/create', [StoreController::class, 'couponCreate'], [], 'web.store.coupon.create');
    $router->get('/{id}/coupons/page/{page}', [StoreController::class, 'couponList'], [], 'web.store.coupon.list');
}, [
    [RoleMiddleware::class, 'handle', ['role' => ['vendor']]],
    [AuthMiddleware::class, 'handle'],
    [CsrfMiddleware::class, 'handle'],
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'web', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);

// -------------------------------------------------
// HOME ROUTES
// -------------------------------------------------
$router->group('', function ($router) {
    $router->get('/', [HomeController::class, 'home'], [], 'web.public.home');
    $router->get('/page/{page}', [HomeController::class, 'homePaginated'], [], 'web.public.home');
    $router->get('/about', [HomeController::class, 'about'], [], 'web.public.about');
    $router->get('/brands', [HomeController::class, 'brands'], [], 'web.public.brands');
    $router->get('/brands/page/{page}', [HomeController::class, 'brandsPaginated'], [], 'web.public.brands');
    $router->get('/cart', [HomeController::class, 'cart'], [], 'web.public.cart');
    $router->get('/delivery', [HomeController::class, 'delivery'], [], 'web.public.delivery');
    $router->get('/faq', [HomeController::class, 'faq'], [], 'web.public.faq');
    $router->get('/order/{id}', [HomeController::class, 'orderView'], [], 'web.public.order.view');
    $router->get('/orders', [HomeController::class, 'orders'], [], 'web.public.orders');
    $router->get('/orders/page/{page}', [HomeController::class, 'ordersPaginated'], [], 'web.public.orders');
    $router->get('/privacy', [HomeController::class, 'privacy'], [], 'web.public.privacy');
    $router->get('/product/{id}', [HomeController::class, 'productView'], [], 'web.public.product.view');
    $router->get('/product/{filter}/{value}/page/{page}', [HomeController::class, 'productList'], [], 'web.public.product.list');
    $router->get('/store/{id}', [HomeController::class, 'store'], [], 'web.public.store.view');
    $router->get('/store/{id}/page/{page}', [HomeController::class, 'storePaginated'], [], 'web.public.store.view');
    $router->get('/store/{id}/{filter}/{value}/page/{page}', [HomeController::class, 'productStore'], [], 'web.public.product.store');
    $router->get('/product/{search}/search', [HomeController::class, 'productSearch'], [], 'web.public.product.search');
    $router->get('/product/{search}/search/page/{page}', [HomeController::class, 'productSearchPaginated'], [], 'web.public.product.search');
    $router->get('/profile', [HomeController::class, 'profile'], [], 'web.public.profile');
    $router->get('/returns', [HomeController::class, 'returns'], [], 'web.public.returns');
    $router->get('/support', [HomeController::class, 'support'], [], 'web.public.support');
    $router->get('/terms', [HomeController::class, 'terms'], [], 'web.public.terms');
    $router->get('/testimonials', [HomeController::class, 'testimonials'], [], 'web.public.testimonials');
    $router->get('/track', [HomeController::class, 'track'], [], 'web.public.track');
    $router->get('/wallet', [HomeController::class, 'wallet'], [], 'web.public.wallet');
    $router->get('/warranty', [HomeController::class, 'warranty'], [], 'web.public.warranty');
    $router->get('/wishlist', [HomeController::class, 'wishlist'], [], 'web.public.wishlist');
    $router->get('/wishlist/page/{page}', [HomeController::class, 'wishlistPaginated'], [], 'web.public.wishlist');
}, [
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'web', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);




