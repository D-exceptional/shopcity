<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Application Service Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [

        App\Providers\CoreServiceProvider::class,

        App\Providers\ExceptionServiceProvider::class,

        App\Providers\RoutingServiceProvider::class,

        App\Providers\CacheServiceProvider::class,

        App\Providers\SessionServiceProvider::class,

        App\Providers\EventServiceProvider::class,

        App\Providers\ViewServiceProvider::class,

    ],

];