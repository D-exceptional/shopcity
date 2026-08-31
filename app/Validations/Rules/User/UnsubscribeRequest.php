<?php

declare(strict_types=1);

namespace App\Validations\Rules\User;

class UnsubscribeRequest
{
    public static function rules(): array
    {
        return [
            'token'     => ['required', 'string'],
            'device_id' => ['required', 'string'],
        ];
    }
}