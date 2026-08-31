<?php

declare(strict_types=1);

namespace App\Validations\Rules\Product;

class FindPriceRangeProductRequest
{
    public static function rules(): array
    {
        return [
            'min'   => ['required', 'number'],
            'max'   => ['required', 'number'],
            'page'  => ['required', 'number'],
            'total' => ['required', 'number'],
            'view'  => ['required', 'string'],
        ];
    }
}