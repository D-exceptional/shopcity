<?php

declare(strict_types=1);

namespace App\Validations\Rules\Wallet;

class GetReferencePaymentRequest
{
    public static function rules(): array
    {
        return [
           'type'       => ['required', 'string'],
            'reference' => ['required', 'string'],
        ];
    }
}