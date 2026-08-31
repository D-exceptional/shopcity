<?php

declare(strict_types=1);

namespace App\Validations\Rules\User;

class UpdateRequest
{
    public static function rules(): array
    {
        return [
            'firstname' => ['required', 'string'],
            'lastname'  => ['required', 'string'],
            'contact'   => ['required', 'string'],
        ];
    }
}