<?php

declare(strict_types=1);

namespace App\Validations\Rules\Link;

class CreateLinkRequest
{
    public static function rules(): array
    {
        return [
            'productId' => ['required', 'number'],
            'userId'    => ['required', 'number'],
            'short'     => ['required', 'string'],
            'long'      => ['required', 'string'],
            'code'      => ['required', 'string'],
            'status'    => ['required', 'string'],
        ];
    }
}