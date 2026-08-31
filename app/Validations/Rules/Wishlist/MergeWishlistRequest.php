<?php

declare(strict_types=1);

namespace App\Validations\Rules\Wishlist;

class MergeWishlistRequest
{
    public static function rules(): array
    {
        return [
           'wishlist' => ['required', 'array'],
        ];
    }
}