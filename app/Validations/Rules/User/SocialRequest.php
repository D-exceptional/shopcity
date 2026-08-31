<?php

declare(strict_types=1);

namespace App\Validations\Rules\User;

class SocialRequest
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