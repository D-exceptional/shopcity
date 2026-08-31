<?php

declare(strict_types=1);

namespace App\Validations\Rules\Wallet;

class GetTypePaymentRequest
{
    public static function rules(): array
    {
        return [
            'table' => ['required', 'string'], 
            'page'  => ['required', 'number'],
        ];
    }
}