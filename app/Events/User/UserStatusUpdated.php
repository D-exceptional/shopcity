<?php

declare(strict_types=1);

namespace App\Events\User;

use App\Contracts\EventInterface;

class UserStatusUpdated implements EventInterface
{
    public function __construct(
        public readonly int $userId,
        public readonly string $status,
    ) {}
}