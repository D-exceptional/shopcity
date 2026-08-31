<?php

declare(strict_types=1);

namespace App\Validations\Rules\Store;

class FindStoreCustomerRequest
{
    public static function rules(): array
    {
        return [
            'type' => ['required', 'string'],
            'page' => ['required', 'number'],
        ];
    }
}