<?php

declare(strict_types=1);

namespace App\Validations\Rules\Checkout;

class ProcessCheckoutRequest
{
    public static function rules(): array
    {
        return [
            'subtotal' => ['required', 'number'],
            'tax'      => ['required', 'number'],
            'discount' => ['required', 'number'],
            'shipping' => ['required', 'number'],
            'total'    => ['required', 'number'],
            'address'  => ['required', 'string'],
            'items'    => ['required', 'array'],
        ];
    }
}