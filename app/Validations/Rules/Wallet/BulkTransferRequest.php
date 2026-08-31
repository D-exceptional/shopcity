<?php

declare(strict_types=1);

namespace App\Validations\Rules\Wallet;

class BulkTransferRequest
{
    public static function rules(): array
    {
        return [
            'title'     => ['required', 'string'],
            'bulk_data' => ['required', 'array'],
        ];
    }
}