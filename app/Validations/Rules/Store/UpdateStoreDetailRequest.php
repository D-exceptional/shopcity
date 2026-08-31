<?php

declare(strict_types=1);

namespace App\Validations\Rules\Store;

class UpdateStoreDetailRequest
{
    public static function rules(): array
    {
        return [
            'name'        => ['required', 'string'],
            'description' => ['required', 'string'],
            'delivery'    => ['required', 'string'],
        ];
    }
}