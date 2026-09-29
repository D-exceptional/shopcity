<?php

namespace App\Exceptions;

use LogicException;

class DuplicateRouteNameException extends LogicException
{
    public function __construct(
        string $message = 'Duplicate route name detected',
        int $code = 0
    ) {
        parent::__construct($message, $code);
    }
}