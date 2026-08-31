<?php

declare(strict_types=1);

namespace App\Validations\Rules\Cart;

class MergeCartRequest
{
    public static function rules(): array
    {
        return [
           'cart' => ['required', 'array'],
        ];
    }
}