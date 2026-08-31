<?php

declare(strict_types=1);

namespace App\Validations\Rules\Category;

class CreateCategoryRequest
{
    public static function rules(): array
    {
        return [
           'category' => ['required', 'string'],
        ];
    }
}