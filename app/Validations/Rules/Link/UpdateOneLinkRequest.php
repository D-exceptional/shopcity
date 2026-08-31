<?php

declare(strict_types=1);

namespace App\Validations\Rules\Link;

class UpdateOneLinkRequest
{
    public static function rules(): array
    {
        return [
            'status' => ['required', 'string'],
        ];
    }
}