<?php

declare(strict_types=1);

namespace App\Events\User;

use App\Contracts\EventInterface;

class UserRegistered implements EventInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $role,
        public readonly string $creator,
        public readonly string $subject,
    ) {}
}