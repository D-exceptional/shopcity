<?php

declare(strict_types=1);

namespace App\Validations\Rules\Product;

class UpdateProductMetadataRequest
{
    public static function rules(): array
    {
        return [
            'visibility' => ['required', 'string'],
            'featured'   => ['required', 'boolean'], // For Paid Promotion
        ];
    }
}