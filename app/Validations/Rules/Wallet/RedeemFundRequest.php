<?php

declare(strict_types=1);

namespace App\Validations\Rules\Wallet;

class RedeemFundRequest
{
    public static function rules(): array
    {
        return [
            'itemId'  => ['required', 'number'],
            'storeId' => ['required', 'number'],
            'status'  => ['required', 'string'],
        ];
    }
}