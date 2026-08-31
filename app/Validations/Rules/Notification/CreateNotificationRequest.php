<?php

declare(strict_types=1);

namespace App\Validations\Rules\Notification;

class CreateNotificationRequest
{
    public static function rules(): array
    {
        return [
            'details'  => ['required', 'string'],
            'type'     => ['required', 'string'],
            'receiver' => ['required', 'int'],
        ];
    }
}