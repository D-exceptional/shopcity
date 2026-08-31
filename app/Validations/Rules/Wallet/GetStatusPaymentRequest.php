<?php

declare(strict_types=1);

namespace App\Validations\Rules\Wallet;

class GetStatusPaymentRequest
{
    public static function rules(): array
    {
        return [
            'table'  => ['required', 'string'], 
            'column' => ['required', 'string'],
            'status' => ['required', 'string'], 
            'page'   => ['required', 'number'],
        ];
    }
}