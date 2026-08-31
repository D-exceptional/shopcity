<?php
// -------------------------------------------------
// Base Path
// -------------------------------------------------
define('BASE_PATH', dirname(__DIR__, 3));

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
use App\Support\TimeManager;
use App\Models\User;
use App\Models\Notification; 

// -------------------------------------------------
// Initialize Managers And Models
// -------------------------------------------------
$currencyManager   = $app->container()->get(CurrencyManager::class);
$ratingManager     = $app->container()->get(NumberManager::class);
$cacheManager      = $app->container()->get(CacheManager::class);
$timeManager       = $app->container()->get(TimeManager::class);
$userModel         = $app->container()->get(User::class);
$notificationModel = $app->container()->get(Notification::class);

// -------------------------------------------------
// Store The Session Instance Globally
// -------------------------------------------------
$session = $app->container()->get(SessionInterface::class);

// -------------------------------------------------------------------
// Determine Auth Url
// -------------------------------------------------------------------
$authUrl = BASE_PATH . '/admin';

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
    $profile = ($avatar === 'None') ? BASE_PATH . '/public/assets/img/avatar.jpg' : $avatar;
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

// -------------------------------------------------
// Base Conversion Rate
// -------------------------------------------------
define('BASE_CONVERSION_RATE', 100);

// -------------------------------------------------
// Import Anything Else
// -------------------------------------------------