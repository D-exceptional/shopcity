<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class MiddlewareException extends Exception 
{
    public function __construct(
        string $message = 'Request blocked by middleware',
        int $code = 403,
        protected array $headers = []
    ) {
        parent::__construct($message, $code);
        $this->headers = $headers;
    }
}
