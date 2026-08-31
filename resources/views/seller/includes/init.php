<?php
// -------------------------------------------------
// Base Path
// -------------------------------------------------
define(
    'SELLER_ROOT_PATH', 
    dirname(__DIR__, 2)
);

// -------------------------------------------------
// Autoload Classes
// -------------------------------------------------
require_once SELLER_ROOT_PATH . '/bootstrap/bootstrap.php';

// -------------------------------------------------
// Import Necessary Classes
// -------------------------------------------------
use App\Contracts\SessionInterface;
use App\Support\BankManager;
use App\Support\CurrencyManager;
use App\Support\NumberManager;
use App\Support\CacheManager;
use App\Support\TimeManager;
use App\Models\User;
use App\Models\Notification; 

// -------------------------------------------------
// Initialize Managers And Models
// -------------------------------------------------
$bankManager       = $app->container()->get(BankManager::class);
$currencyManager   = $app->container()->get(CurrencyManager::class);
$numberManager     = $app->container()->get(NumberManager::class);
$cacheManager      = $app->container()->get(CacheManager::class);
$timeManager       = $app->container()->get(TimeManager::class);
$userModel         = $app->container()->get(User::class);
$notificationModel = $app->container()->get(Notification::class);

// -------------------------------------------------
// Store The Session Instance Globally
// -------------------------------------------------
$session = $app->container()->get(SessionInterface::class);

// -------------------------------------------------------------------
// Determine Auth Url From Current View
// -------------------------------------------------------------------
$authUrl = '/login';

// -------------------------------------------------
// Validate Session
// -------------------------------------------------
$isLoggedIn = $session->validate(7200, 1800);
if ($isLoggedIn === true) {
    $user      = $session->user();
    $userId    = $user['id'];
    $email     = $user['email'];

    // Get name
    $fullName  = $user['name'];
    $nameParts = explode(' ', $fullName);
    $firstName = $nameParts[0];
    $lastName  = $nameParts[1];

    // Get profile
    $avatar  = $userModel->getProfile($userId);
    $profile = ($avatar === 'None') ? '/public/assets/img/avatar.jpg' : $avatar;
} 
else{
    $session->redirect($authUrl);
}

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

// -----------------------------------------
// Page counter url helper
// -----------------------------------------
function pageUrl($page, $baseUrl) {
    return $baseUrl . '&page=' . $page;
}

// -------------------------------------------------
// Import Anything Else
// -------------------------------------------------