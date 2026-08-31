<?php

declare(strict_types=1);

namespace App\Validations\Rules\Wallet;

class GetStatusPayoutRequest
{
    public static function rules(): array
    {
        return [
            'status' => ['required', 'string'], 
            'page'   => ['required', 'number'],
        ];
    } 
}