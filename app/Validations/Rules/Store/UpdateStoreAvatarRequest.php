<?php

declare(strict_types=1);

namespace App\Validations\Rules\Store;

class UpdateStoreAvatarRequest
{
    public static function rules(): array
    {
        return [
            'url' => ['required', 'string'],
        ];
    }
}