<?php

declare(strict_types=1);

namespace App\Validations\Rules\Store;

class CreateStoreRequest
{
    public static function rules(): array
    {
        return [
            'name'        => ['required', 'string'],
            'avatar'      => ['required', 'string'],
            'description' => ['required', 'string'],
            'type'        => ['required', 'string'],
            'delivery'    => ['required', 'string'],
        ];
    }
}