<?php

declare(strict_types=1);

namespace App\Validations\Rules\Media;

class UpdateMediaRequest
{
    public static function rules(): array
    {
        return [
            'url' => ['required', 'string'],
        ];
    }
}