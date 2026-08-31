<?php

declare(strict_types=1);

namespace App\Validations\Rules\User;

class BillingRequest
{
    public static function rules(): array
    {
        return [
            'address' => ['required', 'string'],
            'city'    => ['required', 'string'],
            'code'    => ['required', 'number'],
        ];
    }
}