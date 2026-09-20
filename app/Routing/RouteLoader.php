<?php

declare(strict_types=1);

namespace App\Routing;

use App\Core\Application;

class RouteLoader
{
    public function __construct(
        protected Application $app
    ) {}

    public function load(): void
    {
        $router = $this->app
            ->container()
            ->get(Router::class);

        $basePath = dirname( __DIR__, 2);

        $routeCache =
            $basePath .
            '/storage/framework/cache/route/routes.php';

        /*
        |--------------------------------------------------------------------------
        | Load Cached Routes
        |--------------------------------------------------------------------------
        */

        if (file_exists($routeCache)) {

            $router->setRoutes(
                require $routeCache
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Load Route Definition Files
        |--------------------------------------------------------------------------
        */

        $routeCollections = $this->app
            ->config()
            ->get('router.collections', ['api', 'web']);

        foreach ($routeCollections as $collection) {

            $file =
                $basePath .
                "/routes/{$collection}.php";

            if (!file_exists($file)) {

                echo "Skipping missing route file: {$file}\n";

                continue;
            }

            $router->setCollection(
                $collection
            );

            require $file;
        }

        /*
        |--------------------------------------------------------------------------
        | Reset Active Collection
        |--------------------------------------------------------------------------
        */

        $router->resetCollection();
    }
}