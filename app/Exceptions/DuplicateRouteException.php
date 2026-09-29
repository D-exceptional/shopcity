<?php

namespace App\Exceptions;

use LogicException;

class DuplicateRouteException extends LogicException
{
    public function __construct(
        string $message = 'Duplicate route detected',
        int $code = 0
    ) {
        parent::__construct($message, $code);
    }
}