<?php

use Cloudinary\Cloudinary;

/**
 * Returns a configured Cloudinary instance for v3 SDK.
 *
 * - Frontend uploads remain unchanged.
 * - Backend delete / admin calls work through adminApi().
 */
return new Cloudinary([
    'cloud' => [
        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'] ?? '',
        'api_key'    => $_ENV['CLOUDINARY_API_KEY'] ?? '',
        'api_secret' => $_ENV['CLOUDINARY_API_SECRET'] ?? '',
    ],
    'url' => [
        'secure' => true,   // Use HTTPS URLs by default
    ],
]);
