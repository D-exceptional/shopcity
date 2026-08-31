<?php

declare(strict_types=1);

namespace App\Validations\Rules\Product;

class FindMaxPriceProductRequest
{
    public static function rules(): array
    {
        return [
            'max'   => ['required', 'number'],
            'page'  => ['required', 'number'],
            'total' => ['required', 'number'],
            'view'  => ['required', 'string'],
        ];
    }
}