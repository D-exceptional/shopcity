<?php

declare(strict_types=1);

return [

    'default' => env('DB_CONNECTION', 'mysql'),

    'mysql' => [

        'host' => env('DB_HOST') ?? 'mysql',

        'name' => env('DB_NAME') ?? '',

        'user' => env('DB_USER') ?? '',

        'pass' => env('DB_PASS') ?? '',

    ]
];