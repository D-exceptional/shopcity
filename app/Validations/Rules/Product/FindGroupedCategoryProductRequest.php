<?php

declare(strict_types=1);

namespace App\Validations\Rules\Product;

class FindGroupedCategoryProductRequest
{
    public static function rules(): array
    {
        return [
            'page'  => ['required', 'number'],
            'total' => ['required', 'number'],
        ];
    }
}