<?php

declare(strict_types=1);

namespace App\Validations\Rules\User;

class RegisterRequest
{
    public static function rules(): array
    {
        return [
            'firstname' => ['required', 'string'],
            'lastname'  => ['required', 'string'],
            'email'     => ['required', 'email'], 
            'contact'   => ['required', 'string', 'min:7'],
            'country'   => ['required', 'string'],
            'password'  => ['required', 'string', 'min:6'], 
            'role'      => ['required', 'string'],
            'code'      => ['required', 'string'],
            'abbr'      => ['required', 'string'],
            'currency'  => ['required', 'string'],
            'state'     => ['required', 'string'],
            'creator'   => ['required', 'string'],
        ];
    }
}