<?php

declare(strict_types=1);

namespace App\Validations\Rules\Wallet;

class RequestFundRequest
{
    public static function rules(): array
    {
        return [
            'amount'    => ['required', 'number'],
            'narration' => ['required', 'string'],
        ];
    }
}