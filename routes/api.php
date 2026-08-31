<?php

declare(strict_types=1);

// Import Middlewares
use App\Http\Middlewares\{
    AuthMiddleware,
    CsrfMiddleware,
    RateLimitMiddleware,
    ValidationMiddleware,
};

// Import Cart Validations
use App\Validations\Rules\Cart\{
    MergeCartRequest,
};

// Import Category Validations
use App\Validations\Rules\Category\{
    CreateCategoryRequest,
    UpdateCategoryRequest,
};

// Import Checkout Validations
use App\Validations\Rules\Checkout\{
    ProcessCheckoutRequest,
};

// Import Link Validations
use App\Validations\Rules\Link\{
    CreateLinkRequest,
    UpdateAllLinkRequest,
    UpdateOneLinkRequest,
};

// Import Mail Validations
use App\Validations\Rules\Mail\{
    SubscribeMailRequest,
};

// Import Product Media Validations
use App\Validations\Rules\Media\{
    DeleteBulkMediaRequest,
    UpdateMediaRequest,
};

// Import Notification Validations
use App\Validations\Rules\Notification\{
    CreateNotificationRequest,
    FetchByIdRequest,
    GetUnreadRequest,
};

// Import Order Validations
use App\Validations\Rules\Order\{
    GetSalesSummaryRequest,
};

// Import Product Validations
use App\Validations\Rules\Product\{
    AddProductReviewRequest,
    CreateProductRequest,
    FindAllProductRequest,
    FindCategoryProductRequest,
    FindColorProductRequest,
    FindFeaturedProductRequest,
    FindGroupedCategoryProductRequest,
    FindMaxPriceProductRequest,
    FindMinPriceProductRequest,
    FindNewArrivalProductRequest,
    FindPriceRangeProductRequest,
    FindSearchProductRequest,
    FindStoreCategoryProductRequest,
    FindStoreProductRequest,
    FindTopSellingProductRequest,
    UpdateProductRequest,
    UpdateProductMetadataRequest,
};


// Import Store Validations
use App\Validations\Rules\Store\{
    CreateCouponRequest,
    CreateStoreRequest,
    FindStoreCustomerRequest,
    UpdateCouponRequest,
    UpdateStoreAvatarRequest,
    UpdateStoreDetailRequest,
    UpdateStoreSocialRequest,
};

// Import User Validations
use App\Validations\Rules\User\{
    BillingRequest,
    ContactRequest,
    LoginRequest,
    OtpRequest,
    PasswordRequest,
    ProfileRequest,
    RegisterRequest,
    ResetRequest,
    SocialRequest,
    SubscribeRequest,
    SwitchRequest,
    UnsubscribeRequest,
    UpdateRequest,
};

// Import Wallet Validations
use App\Validations\Rules\Wallet\{
    BulkTransferRequest,
    CreatePaymentRequest,
    GetChannelPaymentRequest,
    GetReferencePaymentRequest,
    GetStatusPaymentRequest,
    GetStatusPayoutRequest,
    GetSummaryPaymentRequest,
    GetTypePaymentRequest,
    GetUserPaymentRequest,
    RedeemFundRequest,
    RequestFundRequest,
    SingleTransferRequest,
    UpdatePaymentDetailRequest,
    VerifyPaymentAutoRequest,
    VerifyPaymentManualRequest,
};

// Import Wishlist Validations
use App\Validations\Rules\Wishlist\{
    MergeWishlistRequest,
};

// Import controllers
use App\Http\Controllers\{
    TestController,
    CartController,
    CategoryController,
    CheckoutController,
    LinkController,
    MailController,
    NotificationController,
    OrderController,
    ProductController,
    ProductMediaController,
    StoreController,
    UserController,
    WalletController,    
    WishlistController
};

/** @var Router $router */

// -------------------------------------------------
// TEST ROUTES
// ------------------------------------------------
$router->group('/api/test', function ($router) {
    $router->get('/ping', [TestController::class, 'ping'], [], 'api.test.ping');
    $router->get('/redis', [TestController::class, 'redis'], [], 'api.test.redis');
    $router->get('/event', [TestController::class, 'event'], [], 'api.test.event');
}, [
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'api', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);


// -------------------------------------------------
// CART ROUTES
// -------------------------------------------------
$router->group('/api/cart', function ($router) {
    $router->get('', [CartController::class, 'view'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]]
    ], 'api.cart.get');
    $router->get('/count-user', [CartController::class, 'countUser'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]]
    ], 'api.cart.countuser');
    $router->get('/count-all', [CartController::class, 'countAll'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]]
    ], 'api.cart.countall');
    $router->get('/users', [CartController::class, 'getCartUsers'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]]
    ], 'api.cart.getusers');
    $router->post('/product/{id}/quantity/{quantity}', [CartController::class, 'add'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]],
        [ValidationMiddleware::class, 'handle', ['rules' => AddToCartRequest::rules()]]
    ], 'api.cart.add');
    $router->put('/product/{id}/quantity/{quantity}', [CartController::class, 'update'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]],
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => UpdateCartRequest::rules()]]
    ], 'api.cart.update');
    $router->put('/merge', [CartController::class, 'merge'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]],
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => MergeCartRequest::rules()]]
    ], 'api.cart.merge');
    $router->delete('/{id}', [CartController::class, 'remove'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]],
        [CsrfMiddleware::class, 'handle'],
    ], 'api.cart.delete');
    $router->delete('/clear', [CartController::class, 'clear'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]],
        [CsrfMiddleware::class, 'handle']
    ], 'api.cart.clear');
}, [
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'api', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);


// -------------------------------------------------
// CATEGORY ROUTES
// -------------------------------------------------
$router->group('/api/category', function ($router) {
    $router->get('', [CategoryController::class, 'all'], [], 'api.category.all');
    $router->get('/group', [CategoryController::class, 'group'], [], 'api.category.group');
    $router->get('/subcategories/{category}', [CategoryController::class, 'fetch'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]],
    ], 'api.category.fetch');
    $router->get('/count', [CategoryController::class, 'count'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]]
    ], 'api.category.count');
    $router->post('', [CategoryController::class, 'create'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]],
        [ValidationMiddleware::class, 'handle', ['rules' => CreateCategoryRequest::rules()]]
    ], 'api.category.create');
    $router->put('/{id}', [CategoryController::class, 'update'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]],
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => UpdateCategoryRequest::rules()]]
    ], 'api.category.update');
    $router->delete('/{id}', [CategoryController::class, 'delete'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]],
        [CsrfMiddleware::class, 'handle'],
    ], 'api.category.delete');
}, [
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'api', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);


// -------------------------------------------------
// CHECKOUT ROUTES
// -------------------------------------------------
$router->group('/api/checkout', function ($router) {
    $router->post('', [CheckoutController::class, 'processCheckout'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]],
        [ValidationMiddleware::class, 'handle', ['rules' => ProcessCheckoutRequest::rules()]]
    ], 'api.checkout.process');
}, [
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'api', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);


// -------------------------------------------------
// LINK ROUTES
// -------------------------------------------------
$router->group('/api/link', function ($router) {
    $router->get('/product/{id}', [LinkController::class, 'findAll'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]], 
    ], 'api.link.all');
    $router->get('/{id}', [LinkController::class, 'findOne'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]], 
    ], 'api.link.one'); 
    $router->post('', [LinkController::class, 'create'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]],
        [ValidationMiddleware::class, 'handle', ['rules' => CreateLinkRequest::rules()]]
    ], 'api.link.create');
    $router->put('/product/{id}', [LinkController::class, 'updateAll'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => UpdateAllLinkRequest::rules()]]
    ], 'api.link.update.all');
    $router->put('/{id}', [LinkController::class, 'updateOne'], [                 
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => UpdateOneLinkRequest::rules()]]
    ], 'api.link.update.one');
    $router->delete('/product/{id}', [LinkController::class, 'deleteAll'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => DeleteAllLinkRequest::rules()]]
    ], 'api.link.delete.all');
    $router->delete('/{id}', [LinkController::class, 'deleteOne'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => DeleteOneLinkRequest::rules()]]
    ], 'api.link.delete.one');
}, [
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'api', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);


// -------------------------------------------------
// MAIL ROUTES
// -------------------------------------------------
$router->group('/api/mail', function ($router) {
    $router->get('/count', [MailController::class, 'countMessages'], [
        [AuthMiddleware::class, 'handle']
    ], 'api.mail.count');
    $router->get('/inbox/{page}', [MailController::class, 'getInbox'], [
        [AuthMiddleware::class, 'handle'],
    ], 'api.mail.inbox');
    $router->get('/outbox/{page}', [MailController::class, 'getOutbox'], [
        [AuthMiddleware::class, 'handle'],
    ], 'api.mail.outbox');
    $router->get('/{id}', [MailController::class, 'getMail'], [], 'api.mail.get');
    $router->post('/send', [MailController::class, 'sendBulk'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]]
    ], 'api.mail.send');
    $router->delete('/{id}', [MailController::class, 'deleteMail'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]], 
        [CsrfMiddleware::class, 'handle'],
    ], 'api.mail.delete');
}, [
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'api', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);


// -------------------------------------------------
// PRODUCT MEDIA ROUTES
// -------------------------------------------------
$router->group('/api/media', function ($router) {
    $router->get('/product/{id}', [ProductMediaController::class, 'findAll'], [
    ], 'api.media.getall');
    $router->get('/{id}', [ProductMediaController::class, 'findOne'], [
    ], 'api.media.getone');
    $router->put('/{id}', [ProductMediaController::class, 'update'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => UpdateMediaRequest::rules()]]
    ], 'api.media.update');
    $router->delete('/product/{id}', [ProductMediaController::class, 'deleteAll'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]], 
        [CsrfMiddleware::class, 'handle'],
    ], 'api.media.deleteall');
    $router->delete('/{id}', [ProductMediaController::class, 'deleteOne'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]], 
        [CsrfMiddleware::class, 'handle'],
    ], 'api.media.deleteone');
    $router->delete('/bulk', [ProductMediaController::class, 'deleteBulk'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => DeleteBulkMediaRequest::rules()]]
    ], 'api.media.deletebulk');
}, [
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'api', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);


// -------------------------------------------------
// NOTIFICATION ROUTES
// -------------------------------------------------
$router->group('/api/notification', function ($router) {
    $router->get('', [NotificationController::class, 'fetchById'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]],
        [ValidationMiddleware::class, 'handle', ['rules' => FetchByIdRequest::rules()]]
    ], 'api.notification.fetchid');
    $router->get('/count-all', [NotificationController::class, 'countAll'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]]
    ], 'api.notification.countall');
    $router->get('/count-user', [NotificationController::class, 'countAllById'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]]
    ], 'api.notification.countuser');
    $router->get('/count-unread', [NotificationController::class, 'countUnreadById'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]]
    ], 'api.notification.countunread');
    $router->get('/unread', [NotificationController::class, 'getUnread'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]],
        [ValidationMiddleware::class, 'handle', ['rules' => GetUnreadRequest::rules()]]
    ], 'api.notification.getunread');
    $router->post('', [NotificationController::class, 'create'], [
        [ValidationMiddleware::class, 'handle', ['rules' => CreateNotificationRequest::rules()]]
    ], 'api.notification.create');
    $router->put('/mark-read', [NotificationController::class, 'markAsRead'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]], 
        [CsrfMiddleware::class, 'handle']
    ], 'api.notification.markread');
}, [
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'api', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);


// -------------------------------------------------
// ORDER ROUTES
// -------------------------------------------------
$router->group('/api/order', function ($router) {
    $router->get('/{id}', [OrderController::class, 'getOrder'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]],
    ], 'api.order.getone');
    $router->get('/{page}', [OrderController::class, 'getAllOrders'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]],
    ], 'api.order.getall');
    $router->get('/status/{status}/page/{page}', [OrderController::class, 'getOrdersByStatus'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]],
    ], 'api.order.status');
    $router->get('/user/page/{page}', [OrderController::class, 'getUserOrders'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]],
    ], 'api.order.user');
    $router->get('/store/{id}/page/{page}', [OrderController::class, 'getStoreOrders'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]],
    ], 'api.order.store');
    $router->get('/store/{id}/status/{status}/page/{page}', [OrderController::class, 'getStoreOrdersByStatus'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]],
    ], 'api.order.storestatus');
    $router->get('/track/{code}', [OrderController::class, 'trackOrder'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]],
    ], 'api.order.track');
    $router->get('/sales-summary', [OrderController::class, 'getSalesSummary'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]],
        [ValidationMiddleware::class, 'handle', ['rules' => GetSalesSummaryRequest::rules()]]
    ], 'api.order.summary');
    $router->put('/{id}/complete', [OrderController::class, 'completeOrder'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]], 
        [CsrfMiddleware::class, 'handle'],
    ], 'api.order.complete');
    $router->put('/item/{id}/status/{status}', [OrderController::class, 'updateItemStatus'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]], 
        [CsrfMiddleware::class, 'handle'],
    ], 'api.order.itemupdate');
    $router->delete('/{id}/cancel', [OrderController::class, 'cancelOrder'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]], 
        [CsrfMiddleware::class, 'handle'],
    ], 'api.order.cancel');
}, [
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'api', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);


// -------------------------------------------------
// PRODUCT ROUTES
// -------------------------------------------------
$router->group('/api/product', function ($router) {
    $router->get('', [ProductController::class, 'findByAll'], [
        [ValidationMiddleware::class, 'handle', ['rules' => FindAllProductRequest::rules()]]
    ], 'api.product.all');
    $router->get('/{category}', [ProductController::class, 'findByCategory'], [
        [ValidationMiddleware::class, 'handle', ['rules' => FindCategoryProductRequest::rules()]]
    ], 'api.product.category');
    $router->get('/store/{id}', [ProductController::class, 'findByStore'], [
        [ValidationMiddleware::class, 'handle', ['rules' => FindStoreProductRequest::rules()]]
    ], 'api.product.store');
    $router->get('/store/{id}/category/{category}', [ProductController::class, 'findByStoreCategory'], [
        [ValidationMiddleware::class, 'handle', ['rules' => FindStoreCategoryProductRequest::rules()]]
    ], 'api.product.storecategory');
    $router->get('/recent', [ProductController::class, 'findNewArrivals'], [
        [ValidationMiddleware::class, 'handle', ['rules' => FindNewArrivalProductRequest::rules()]]
    ], 'api.product.recent');
    $router->get('/featured', [ProductController::class, 'findFeatured'], [
        [ValidationMiddleware::class, 'handle', ['rules' => FindFeaturedProductRequest::rules()]]
    ], 'api.product.featured');
    $router->get('/top-selling', [ProductController::class, 'findTopSelling'], [
        [ValidationMiddleware::class, 'handle', ['rules' => FindTopSellingProductRequest::rules()]]
    ], 'api.product.topselling');
    $router->get('/price-range', [ProductController::class, 'findByPriceRange'], [
        [ValidationMiddleware::class, 'handle', ['rules' => FindPriceRangeProductRequest::rules()]]
    ], 'api.product.pricerange');
    $router->get('/price-min', [ProductController::class, 'findByMinPrice'], [
        [ValidationMiddleware::class, 'handle', ['rules' => FindMinPriceProductRequest::rules()]]
    ], 'api.product.pricemin');
    $router->get('/price-max', [ProductController::class, 'findByMaxPrice'], [
        [ValidationMiddleware::class, 'handle', ['rules' => FindMaxPriceProductRequest::rules()]]
    ], 'api.product.pricemax');
    $router->get('/category/group', [ProductController::class, 'findByGroupedCategory'], [
        [ValidationMiddleware::class, 'handle', ['rules' => FindGroupedCategoryProductRequest::rules()]]
    ], 'api.product.categorygroup');
    $router->get('/{id}', [ProductController::class, 'findOne'], [
    ], 'api.product.one');
    $router->get('/search/{query}', [ProductController::class, 'findBySearch'], [
        [ValidationMiddleware::class, 'handle', ['rules' => FindSearchProductRequest::rules()]]
    ], 'api.product.search');
    $router->get('/color/{color}', [ProductController::class, 'findByColor'], [
        [ValidationMiddleware::class, 'handle', ['rules' => FindColorProductRequest::rules()]]
    ], 'api.product.color');
    $router->post('/store/{id}', [ProductController::class, 'create'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]],
        [ValidationMiddleware::class, 'handle', ['rules' => CreateProductRequest::rules()]]
    ], 'api.product.create');
    $router->post('/{id}/review', [ProductController::class, 'addReview'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]],
        [ValidationMiddleware::class, 'handle', ['rules' => AddProductReviewRequest::rules()]]
    ], 'api.product.review');
    $router->put('/{id}', [ProductController::class, 'update'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => UpdateProductRequest::rules()]]
    ], 'api.product.update');
    $router->put('/{id}/metadata', [ProductController::class, 'metadata'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => UpdateProductMetadataRequest::rules()]]
    ], 'api.product.updatemetadata');
    $router->delete('/{id}', [ProductController::class, 'delete'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin', 'vendor']]], 
        [CsrfMiddleware::class, 'handle'],
    ], 'api.product.delete');
}, [
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'api', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);


// -------------------------------------------------
// STORE ROUTES
// -------------------------------------------------
$router->group('/api/store', function ($router) {
    $router->get('/{id}', [StoreController::class, 'findOne'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]],
    ], 'api.store.findone');
    $router->get('/status/{status}/page/{page}', [StoreController::class, 'findByStatus'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]],
    ], 'api.store.findstatus');
    $router->get('/user/{id}/page/{page}', [StoreController::class, 'findByUser'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]],
    ], 'api.store.finduser');
    $router->get('/{id}/coupon/page/{page}', [StoreController::class, 'findCouponsByStore'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]],
    ], 'api.store.findcoupon');
    $router->get('{id}/coupon/status/{status}/page/{page}', [StoreController::class, 'findCouponsByStoreAndStatus'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]],
    ], 'api.store.findcouponstatus');
    $router->get('/count', [StoreController::class, 'countStoresByStatus'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]]
    ], 'api.store.count');
    $router->get('/{id}/customer', [StoreController::class, 'findStoreCustomers'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]],
        [ValidationMiddleware::class, 'handle', ['rules' => FindStoreCustomerRequest::rules()]]
    ], 'api.store.findcustomer');
    $router->get('/{id}/coupon/{code}', [StoreController::class, 'findCoupon'], [], 'api.store.findonecoupon');
    $router->post('', [StoreController::class, 'createStore'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]],
        [ValidationMiddleware::class, 'handle', ['rules' => CreateStoreRequest::rules()]]
    ], 'api.store.create');
    $router->post('/{id}/coupon', [StoreController::class, 'createCoupon'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]],
        [ValidationMiddleware::class, 'handle', ['rules' => CreateCouponRequest::rules()]]
    ], 'api.store.createcoupon');
    $router->put('/coupon/{id}', [StoreController::class, 'updateCoupon'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => UpdateCouponRequest::rules()]]
    ], 'api.store.updatecoupon');
    $router->put('/{id}', [StoreController::class, 'updateStoreDetails'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => UpdateStoreDetailRequest::rules()]]
    ], 'api.store.updatedetails');
    $router->put('/{id}/socials', [StoreController::class, 'updateStoreSocials'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => UpdateStoreSocialRequest::rules()]]
    ], 'api.store.updatesocials');
    $router->put('/{id}/avatar', [StoreController::class, 'updateStoreAvatar'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => UpdateStoreAvatarRequest::rules()]]
    ], 'api.store.updateavatar');
    $router->put('/{id}/status/{status}', [StoreController::class, 'updateStoreStatus'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]], 
        [CsrfMiddleware::class, 'handle'],
    ], 'api.store.updatestatus');
    $router->delete('/{id}', [StoreController::class, 'deleteStore'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]], 
        [CsrfMiddleware::class, 'handle'],
    ], 'api.store.delete');
    $router->delete('/coupon/{id}', [StoreController::class, 'deleteSingleCoupon'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]], 
        [CsrfMiddleware::class, 'handle'],
    ], 'api.store.deletecoupon');
    $router->delete('/{id}/coupon', [StoreController::class, 'deleteCouponByStore'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]], 
        [CsrfMiddleware::class, 'handle'],
    ], 'api.store.deleteallcoupon');
}, [
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'api', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);


// -------------------------------------------------
// USER ROUTES
// -------------------------------------------------
$router->group('/api/user', function ($router) {
    $router->get('/count', [UserController::class, 'count'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]],
    ], 'api.user.count');
    $router->get('/role/{role}/page/{page}', [UserController::class, 'fetch'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]],
    ], 'api.user.fetch');
    $router->get('/{id}/doc', [UserController::class, 'doc'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]],
    ], 'api.user.doc');
    $router->post('/register', [UserController::class, 'register'], [
        [ValidationMiddleware::class, 'handle', ['rules' => RegisterRequest::rules()]]
    ], 'api.user.register');
    $router->post('/login', [UserController::class, 'login'], [
        [ValidationMiddleware::class, 'handle', ['rules' => LoginRequest::rules()]]
    ], 'api.user.login');
    $router->post('/otp', [UserController::class, 'otp'], [
        [ValidationMiddleware::class, 'handle', ['rules' => OtpRequest::rules()]]
    ], 'api.user.otp');
    $router->post('/logout', [UserController::class, 'logout'], [], 'api.user.logout');
    $router->post('/contact', [UserController::class, 'contact'], [
        [ValidationMiddleware::class, 'handle', ['rules' => ContactRequest::rules()]]
    ], 'api.user.contact');
    $router->post('/subscribe', [UserController::class, 'subscribe'], [
        [AuthMiddleware::class, 'handle'], 
        [ValidationMiddleware::class, 'handle', ['rules' => SubscribeRequest::rules()]]
    ], 'api.user.subscribe');
    $router->put('/password-reset', [UserController::class, 'reset'], [
        [ValidationMiddleware::class, 'handle', ['rules' => ResetRequest::rules()]]
    ], 'api.user.passwordreset');
    $router->put('/update', [UserController::class, 'update'], [
        [AuthMiddleware::class, 'handle'], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => UpdateRequest::rules()]]
    ], 'api.user.update');
    $router->put('/profile', [UserController::class, 'profile'], [
        [AuthMiddleware::class, 'handle'], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => ProfileRequest::rules()]]
    ], 'api.user.profile');
    $router->put('/social', [UserController::class, 'social'], [
        [AuthMiddleware::class, 'handle'], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => SocialRequest::rules()]]
    ], 'api.user.social');
    $router->put('/password-change', [UserController::class, 'password'], [
        [AuthMiddleware::class, 'handle'], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => PasswordRequest::rules()]]
    ], 'api.user.passwordchange');
    $router->put('/{id}/status/{status}', [UserController::class, 'status'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]], 
        [CsrfMiddleware::class, 'handle'],
    ], 'api.user.status');
    $router->put('/billing', [UserController::class, 'billing'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => BillingRequest::rules()]]
    ], 'api.user.billing');
    $router->delete('/unsubscribe', [UserController::class, 'unsubscribe'], [
        [AuthMiddleware::class, 'handle'], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => UnsubscribeRequest::rules()]]
    ], 'api.user.unsubscribe');
    $router->delete('/{id}', [UserController::class, 'delete'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]], 
        [CsrfMiddleware::class, 'handle'],
    ], 'api.user.delete');
}, [
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'api', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);


// -------------------------------------------------
// WALLET ROUTES
// -------------------------------------------------
$router->group('/api/wallet', function ($router) {
    $router->get('/payment-reference', [WalletController::class, 'getByReference'], [
        [ValidationMiddleware::class, 'handle', ['rules' => GetReferencePaymentRequest::rules()]]
    ], 'api.wallet.getreference');
    $router->get('/payment-user', [WalletController::class, 'getPaymentsByUser'], [
        [AuthMiddleware::class, 'handle', ['role' => ['customer', 'vendor']]],
        [ValidationMiddleware::class, 'handle', ['rules' => GetUserPaymentRequest::rules()]]
    ], 'api.wallet.getuser');
    $router->get('/payment-type', [WalletController::class, 'getPaymentsByType'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]],
        [ValidationMiddleware::class, 'handle', ['rules' => GetTypePaymentRequest::rules()]]
    ], 'api.wallet.gettype');
    $router->get('/payment-status', [WalletController::class, 'getPaymentsByStatus'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]],
        [ValidationMiddleware::class, 'handle', ['rules' => GetStatusPaymentRequest::rules()]]
    ], 'api.wallet.getstatus');
    $router->get('/payout-status', [WalletController::class, 'getPayoutsByStatus'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]],
        [ValidationMiddleware::class, 'handle', ['rules' => GetStatusPayoutRequest::rules()]]
    ], 'api.wallet.getpayouts');
    $router->post('/fund', [WalletController::class, 'createPayment'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]],
        [ValidationMiddleware::class, 'handle', ['rules' => CreatePaymentRequest::rules()]]
    ], 'api.wallet.createpayment');
    $router->post('/payment-verify', [WalletController::class, 'verifyPayment'], [
        [ValidationMiddleware::class, 'handle', ['rules' => VerifyPaymentAutoRequest::rules()]]
    ], 'api.wallet.updatepayment');
    $router->post('/fund-redeem', [WalletController::class, 'redeemFunds'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]],
        [ValidationMiddleware::class, 'handle', ['rules' => RedeemFundRequest::rules()]]
    ], 'api.wallet.redeemfund');
    $router->post('/fund-request', [WalletController::class, 'requestFunds'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]],
        [ValidationMiddleware::class, 'handle', ['rules' => RequestFundRequest::rules()]]
    ], 'api.wallet.requestfund');
    $router->post('/transfer-single', [WalletController::class, 'singleTransfer'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]],
        [ValidationMiddleware::class, 'handle', ['rules' => SingleTransferRequest::rules()]]
    ], 'api.wallet.singletransfer');
    $router->post('/transfer-bulk', [WalletController::class, 'bulkTransfer'], [
        [AuthMiddleware::class, 'handle', ['role' => ['admin']]],
        [ValidationMiddleware::class, 'handle', ['rules' => BulkTransferRequest::rules()]]
    ], 'api.wallet.bulktransfer');
    $router->put('/details-update', [WalletController::class, 'updateDetails'], [
        [AuthMiddleware::class, 'handle', ['role' => ['vendor']]], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => UpdatePaymentDetailRequest::rules()]]
    ], 'api.wallet.updatedetails');
}, [
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'api', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);


// -------------------------------------------------
// WISHLIST ROUTES
// -------------------------------------------------
$router->group('/api/wishlist', function ($router) {
    $router->get('', [WishlistController::class, 'view'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]],
    ], 'api.wishlist.view');
    $router->post('/product/{id}', [WishlistController::class, 'add'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]],
    ], 'api.wishlist.add');
    $router->delete('/product/{id}', [WishlistController::class, 'remove'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]], 
        [CsrfMiddleware::class, 'handle'],
    ], 'api.wishlist.remove');
    $router->put('', [WishlistController::class, 'merge'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]], 
        [CsrfMiddleware::class, 'handle'],
        [ValidationMiddleware::class, 'handle', ['rules' => MergeWishlistRequest::rules()]]
    ], 'api.wishlist.merge');
    $router->delete('/clear', [WishlistController::class, 'clear'], [
        [AuthMiddleware::class, 'handle', ['role' => ['user']]], 
        [CsrfMiddleware::class, 'handle'],
    ], 'api.wishlist.clear');
}, [
    [
        RateLimitMiddleware::class, 'handle', 
        [
            'scope' => 'api', 
            'userLimit' => 60, 
            'anonLimit' => 20
        ]
    ],
]);
