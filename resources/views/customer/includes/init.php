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
// Import Necessary Classes
// -------------------------------------------------
use App\Contracts\SessionInterface;
use App\Support\CurrencyManager;
use App\Support\NumberManager;
use App\Support\CacheManager;
use App\Models\Category;
use App\Models\Cart;    

// -------------------------------------------------
// Initialize Managers And Models
// -------------------------------------------------
$currencyManager = $app->container()->get(CurrencyManager::class);
$ratingManager   = $app->container()->get(NumberManager::class);
$cacheManager    = $app->container()->get(CacheManager::class);
$categoryModel   = $app->container()->get(Category::class);
$cartModel       = $app->container()->get(Cart::class);

// -------------------------------------------------
// Store The Session Instance Globally
// -------------------------------------------------
$session = $app->container()->get(SessionInterface::class);

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

// -----------------------------------------
// Page counter url helper
// -----------------------------------------
function pageUrl($page, $baseUrl) {
    return $baseUrl . '&page=' . $page;
}

// -------------------------------------------------
// Import Anything Else
// -------------------------------------------------