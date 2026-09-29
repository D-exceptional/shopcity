<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    public function __construct( 
        string $message = 'Validation failed', 
        int $code = 422,
        public array $errors = []
    ) {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }
}
