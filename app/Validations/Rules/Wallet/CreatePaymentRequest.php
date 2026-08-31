<?php

declare(strict_types=1);

namespace App\Validations\Rules\Wallet;

class CreatePaymentRequest
{
    public static function rules(): array
    {
        return [
            'amount' => ['required', 'number'],
        ];
    }
}