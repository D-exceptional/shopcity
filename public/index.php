<?php
declare(strict_types=1);

/**
 * ----------------------------------------
 *  Global Configuration & Error Handling
 * ----------------------------------------
 */
define('BASE_PATH', dirname(__DIR__));

// Enable full error reporting (for local development)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Log all errors to a file at project root
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/router-error.log');

/**
 * ----------------------------------------
 *  Autoload & Boot
 * ----------------------------------------
 */
require_once BASE_PATH . '/bootstrap.php';

/**
 * ----------------------------------------
 *  Initialize Response Handler
 * ----------------------------------------
 */
$response = $container->get(\App\Helpers\ResponseManager::class);

/**
 * -------------------------------------------------------
 *  Router Initialization
 *  Load The Router From The Container (NOT new Router())
 * --------------------------------------------------------
 */
$router = $container->get(\App\Core\Router::class);

/**
 * -------------------------------------------------------------
 *  Load API Routes (must come after router is created)
 * -------------------------------------------------------------
 */
require_once BASE_PATH . '/routes/api.php';

/**
 * ------------------------------------------------------------------------
 *  Request Handling
 * ------------------------------------------------------------------------
 */
// Remove Local Base Folder Automatically (e.g. /eCommerce or /shop)
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Extract Only The Part Starting With /api/
if (preg_match('#/api(/.*)?$#', $requestUri, $matches)) {
    $uri = rtrim($matches[0], '/');
} else {
    $uri = '/';
}

/**
 * -------------------------------------------
 *  Normalize URI And Detect HTTP Method
 * -------------------------------------------
 */
$uri = rtrim($uri, '/');
$method = strtoupper($_SERVER['REQUEST_METHOD']);

/**
 * ------------------------------------------------------------
 *  Dispatch The Request And Handle Exceptions Globally Here
 * ------------------------------------------------------------
 */
try {
    $router->dispatch($uri, $method);
} catch (\App\Exceptions\MiddlewareException $e) {
    if ($e->action === 'redirect' && $e->redirect) {
        header('Location: ' . $e->redirect);
    } else {
        $response->error($e->getMessage(), $e->status, null, 'middleware');
    }
} catch (\App\Exceptions\RouteNotFoundException $e) {
    $response->error($e->getMessage(), 404, null, 'router');
} catch (\Throwable $e) {
    $response->error('Internal server error', 500, null, 'router');
}
exit;
