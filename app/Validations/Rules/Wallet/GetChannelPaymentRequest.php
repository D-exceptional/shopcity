<?php

declare(strict_types=1);

namespace App\Validations\Rules\Wallet;

class GetChannelPaymentRequest
{
    public static function rules(): array
    {
        return [
            'channel' => ['required', 'string'], 
            'status'  => ['required', 'string'], 
            'page'    => ['required', 'number'],
        ];
    }
}