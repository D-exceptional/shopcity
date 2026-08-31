<?php

declare(strict_types=1);

namespace App\Validations\Rules\Media;

class DeleteBulkMediaRequest
{
    public static function rules(): array
    {
        return [
            'urls' => ['required', 'array'],
        ];
    }
}