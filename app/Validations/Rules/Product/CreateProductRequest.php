<?php

declare(strict_types=1);

namespace App\Validations\Rules\Product;

class CreateProductRequest
{
    public static function rules(): array
    {
        return [
            'name'        => ['required', 'string'],
            'description' => ['required', 'string'],
            'category'    => ['required', 'string'],
            'subcategory' => ['required', 'string'],
            'price'       => ['required', 'number'],
            'slash'       => ['required', 'number'],
            'stock'       => ['required', 'number'],
            'color'       => ['required', 'string'],
            'media'       => ['required', 'array'],
        ];
    }
}