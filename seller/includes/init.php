<?php
// -------------------------------------------------
// Base Path
// -------------------------------------------------
define('BASE_PATH', dirname(__DIR__));

// -------------------------------------------------
// Autoload Classes
// -------------------------------------------------
require_once BASE_PATH . '/../bootstrap.php';

// -------------------------------------------------
// Initialize Managers And Models
// -------------------------------------------------
$bankManager     = $container->get(\App\Helpers\BankManager::class);
$currencyManager = $container->get(\App\Helpers\CurrencyManager::class);
$ratingManager   = $container->get(\App\Helpers\RatingManager::class);
$cacheManager    = $container->get(\App\Helpers\CacheManager::class);
$timeManager     = $container->get(\App\Helpers\TimeManager::class);
$userModel       = $container->get(\App\Models\User::class);
$notificationModel = $container->get(\App\Models\Notification::class);

// -------------------------------------------------
// Store The Session Instance Globally
// -------------------------------------------------
$GLOBALS['session'] = $session;

// -------------------------------------------------------------------
// Determine Auth Url From Current View
// -------------------------------------------------------------------
$authUrl = $_ENV['USER_LOGIN_PATH'] ?? rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/login';

// -------------------------------------------------
// Validate Session
// -------------------------------------------------
$isLoggedIn = $session->validate(7200, 1800);
if ($isLoggedIn === true) {
    $user      = $session->user();
    $userId    = $user['id'];
    $email     = $user['email'];
    // Get name
    $fullName = $user['name'];
    $nameParts = explode(' ', $fullName);
    $firstName = $nameParts[0];
    $lastName  = $nameParts[1];
    // Get profile
    $avatar = $userModel->getProfile($userId);
    $profile = ($avatar === 'None') ? rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/assets/img/avatar.jpg' : $avatar;
} 
else{
    $session->redirect($authUrl);
}

// ---------------------------------------------------------
// Automatically Detect Environment (Development/Production)
// ---------------------------------------------------------
$isDevelopment = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);

// -------------------------------------------------
// Get Cart Data
// -------------------------------------------------
//$cartCount = $isLoggedIn ? $cartModel->countCart($userId) : 0;

// -------------------------------------------------
// Set Timezone
// -------------------------------------------------
date_default_timezone_set('Africa/Lagos');

// -------------------------------------------------
// Base Offset For Products
// -------------------------------------------------
define('PER_PAGE', 20);

// -------------------------------------------------
// Base Payout Table
// -------------------------------------------------
define('PAYOUT_WALLET', 'wallet_payout');

// -------------------------------------------------
// Base Payout Backup Table
// -------------------------------------------------
define('PAYOUT_BACKUP_WALLET', 'wallet_payout_backup');

// -------------------------------------------------
// Base Savings Table
// -------------------------------------------------
define('SAVINGS_WALLET', 'wallet_savings');

// -------------------------------------------------
// Base Withdrawals Table
// -------------------------------------------------
define('WITHDRAWAL_TABLE', 'withdrawals');

// -------------------------------------------------
// Base Conversion Rate
// -------------------------------------------------
define('BASE_CONVERSION_RATE', 100);

// -------------------------------------------------
// Import Anything Else
// -------------------------------------------------