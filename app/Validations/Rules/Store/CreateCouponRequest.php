<?php

declare(strict_types=1);

namespace App\Validations\Rules\Store;

class CreateCouponRequest
{
    public static function rules(): array
    {
        return [
            'code'     => ['required', 'string'],
            'discount' => ['required', 'number'],
        ];
    }
}