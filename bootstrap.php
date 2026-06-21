<?php

// -------------------------------------------------
// Base Path
// -------------------------------------------------
define('ROOT_PATH', __DIR__);

// -------------------------------------------------
// Composer Autoload
// -------------------------------------------------
require_once ROOT_PATH . '/vendor/autoload.php';

// -------------------------------------------------
// Load environment variables
// -------------------------------------------------
$envPath = ROOT_PATH . '/.env';
if (file_exists($envPath)) {
    $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
    $dotenv->load();
}

// -------------------------------------------------
// Load configurations
// -------------------------------------------------
//$dbConfig     = require ROOT_PATH . '/config/db.php';
$cloudinaryConf = require ROOT_PATH . '/config/cloudinary.php';

// -------------------------------------------------
// Load container and other necessary services
// -------------------------------------------------
use App\Core\Container;
use App\Helpers\SessionManager;

// -------------------------------------------------
// Initialize container
// -------------------------------------------------
$container = new Container();

// ----------------------------------------------
// Configure session cookie
// -------------------------------------------------
session_set_cookie_params([
    'lifetime' => 7200,                  // 2 hours
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],   // your domain
    'secure' => true,                     // HTTPS only
    'httponly' => true,                   // Not accessible to JS
    'samesite' => 'Lax'                   // Prevent CSRF
]);

// -------------------------------------------------
// Initialize and start the session service
// -------------------------------------------------
$session = $container->get(SessionManager::class);
$session->start();

// Return container to index.php / router
return $container;

// -------------------------------------------------
// Initialize Anything Else You Need
// -------------------------------------------------