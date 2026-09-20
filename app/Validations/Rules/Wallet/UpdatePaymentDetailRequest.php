<?php

declare(strict_types=1);

namespace App\Validations\Rules\Wallet;

class UpdatePaymentDetailRequest
{
    public static function rules(): array
    {
        return [
            'account' => ['required', 'number'],
            'bank'   => ['required', 'string'],
            'code'   => ['required', 'string'],
        ];
    }
}