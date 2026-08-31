<?php

declare(strict_types=1);

namespace App\Validations\Rules\Store;

class UpdateStoreSocialRequest
{
    public static function rules(): array
    {
        return [
            'facebook'  => ['required', 'string'],
            'instagram' => ['required', 'string'],
            'tiktok'    => ['required', 'string'],
            'twitter'   => ['required', 'string'],
        ];
    }
}