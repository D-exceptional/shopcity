<?php

declare(strict_types=1);

namespace App\Validations\Rules\Mail;

class SubscribeMailRequest
{
    public static function rules(): array
    {
        return [
           'email' => ['required', 'string'],
        ];
    }
}