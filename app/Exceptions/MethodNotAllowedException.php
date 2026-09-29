<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class MethodNotAllowedException extends Exception 
{
    public function __construct(
        string $message = 'Method not allowed',
        int $code = 405
    ) {
        parent::__construct($message, $code);
    }
}