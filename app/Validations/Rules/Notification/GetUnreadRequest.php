<?php

declare(strict_types=1);

namespace App\Validations\Rules\Notification;

class GetUnreadRequest
{
    public static function rules(): array
    {
        return [
            'page'  => ['number'],
            'limit' => ['number'],
        ];
    }
}