<?php

namespace App\Exceptions;

use LogicException;

class InvalidRoutePatternException extends LogicException
{
    public function __construct(
        string $message = 'Invalid route pattern',
        int $code = 0
    ) {
        parent::__construct($message, $code);
    }
}