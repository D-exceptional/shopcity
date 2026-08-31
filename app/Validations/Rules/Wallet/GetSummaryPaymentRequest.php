<?php

declare(strict_types=1);

namespace App\Validations\Rules\Wallet;

class GetSummaryPaymentRequest
{
    public static function rules(): array
    {        
        return [
            'view'   => ['required', 'string'],
            'period' => ['required', 'string'],
            'start'  => ['required', 'string'],
            'end'    => ['required', 'string'],
        ];
    }
}