<?php

declare(strict_types=1);

namespace App\Validations\Rules\Product;

class FindMinPriceProductRequest
{
    public static function rules(): array
    {
        return [
            'min'   => ['required', 'number'],
            'page'  => ['required', 'number'],
            'total' => ['required', 'number'],
            'view'  => ['required', 'string'],
        ];
    }
}