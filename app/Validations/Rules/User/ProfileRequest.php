<?php

declare(strict_types=1);

namespace App\Validations\Rules\User;

class ProfileRequest
{
    public static function rules(): array
    {
        return [
            'avatar' => ['required', 'string'],
        ];
    }
}