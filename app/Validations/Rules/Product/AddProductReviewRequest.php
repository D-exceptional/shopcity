<?php

declare(strict_types=1);

namespace App\Validations\Rules\Product;

class AddProductReviewRequest
{
    public static function rules(): array
    {
        return [
            'review' => ['required', 'string'],
            'rating' => ['required', 'number'],
        ];
    }
}