<?php

declare(strict_types=1);

define(
    'ROOT_PATH',
    dirname(__DIR__)
);

require_once ROOT_PATH . '/vendor/autoload.php';

// LOAD BOOTSTRAP ENV HELPER
require_once ROOT_PATH . '/bootstrap/env.php';

use App\Core\Application;

// CREATE APPLICATION
$app = new Application();

// LOAD APPLICATION HELPERS
require_once ROOT_PATH . '/bootstrap/helpers.php';

// BOOT APPLICATION
$app->boot();

return $app;