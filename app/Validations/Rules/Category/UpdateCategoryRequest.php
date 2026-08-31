<?php

declare(strict_types=1);

namespace App\Validations\Rules\Category;

class UpdateCategoryRequest
{
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string'],
        ];
    }
}