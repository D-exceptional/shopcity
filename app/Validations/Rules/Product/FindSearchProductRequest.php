<?php

declare(strict_types=1);

namespace App\Validations\Rules\Product;

class FindSearchProductRequest
{
    public static function rules(): array
    {
        return [
            'page'  => ['required', 'number'],
            'total' => ['required', 'number'],
            'view'  => ['required', 'string'],
        ];
    }
}