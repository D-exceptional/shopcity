<?php

declare(strict_types=1);

namespace App\Validations\Rules\Wallet;

class VerifyPaymentAutoRequest
{
    public static function rules(): array
    {
        return [
           'id'         => ['required', 'number'],
            'reference' => ['required', 'string'],
        ];
    }
}