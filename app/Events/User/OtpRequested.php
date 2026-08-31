<?php

declare(strict_types=1);

namespace App\Events\User;

use App\Contracts\EventInterface;

class OtpRequested implements EventInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly int $otp,
    ) {}
}