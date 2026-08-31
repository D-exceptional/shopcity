<?php

declare(strict_types=1);

namespace App\Validations\Rules\User;

class PasswordRequest
{
    public static function rules(): array
    {
        return [
            'password' => ['required', 'string'],
        ];
    }
}