<?php

declare(strict_types=1);

return [

    'admin' => [
        'login' => '/auth/admin/login',
        'dashboard' => '/admin/dashboard'
    ],

    'customer' => [
        'login' => '/auth/user/login',
        'dashboard' => '/'
    ],

    'vendor' => [
        'login' => '/auth/user/login',
        'dashboard' => '/vendor/dashboard'
    ],

];