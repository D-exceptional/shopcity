<?php
// -------------------------------------------------
// Base Path
// -------------------------------------------------
define('BASE_PATH', dirname(__DIR__));

// -------------------------------------------------
// Autoload Classes
// -------------------------------------------------
require_once BASE_PATH . '/bootstrap.php';

// -------------------------------------------------
// Initialize Managers And Models
// -------------------------------------------------
$currencyManager = $container->get(\App\Helpers\CurrencyManager::class);
$ratingManager   = $container->get(\App\Helpers\RatingManager::class);
$cacheManager    = $container->get(\App\Helpers\CacheManager::class);
$categoryModel   = $container->get(\App\Models\Category::class);
$cartModel       = $container->get(\App\Models\Cart::class);

// -------------------------------------------------
// Store The Session Instance Globally
// -------------------------------------------------
$GLOBALS['session'] = $session;

// ---------------------------------------------------------
// Automatically Detect Environment (Development/Production)
// ---------------------------------------------------------
$isDevelopment = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);

// -------------------------------------------------
// Check Session 
// -------------------------------------------------
$isLoggedIn = $session->validate(7200, 1800);
if ($isLoggedIn) {
    $user   = $session->user();
    $userId = $user['id'];
} 

// -------------------------------------------------
// Get Category Data
// -------------------------------------------------
$allCategories     = $categoryModel->all();
$groupedCategories = $categoryModel->group();

// -------------------------------------------------
// Get Cart Data
// -------------------------------------------------
$cartCount = $isLoggedIn ? $cartModel->countCart($userId) : 0;

// -------------------------------------------------
// Set Timezone
// -------------------------------------------------
date_default_timezone_set('Africa/Lagos');

// -------------------------------------------------
// Base Store For Featured Products
// -------------------------------------------------
define('STORE_ID', 4);

// -------------------------------------------------
// Base Offset For Products
// -------------------------------------------------
define('PER_PAGE', 20);

// -------------------------------------------------
// Base Flat Rate
// -------------------------------------------------
define('FLAT_RATE', 100);

// -------------------------------------------------
// Base Conversion Rate
// -------------------------------------------------
define('BASE_CONVERSION_RATE', 100);

// -------------------------------------------------
// Base Payment Table
// -------------------------------------------------
define('PAYMENT_TABLE', 'wallet_coin');

// -------------------------------------------------
// App Name
// -------------------------------------------------
define('SITE_NAME', 'ShopCity');

// -------------------------------------------------
// Import Anything Else
// -------------------------------------------------