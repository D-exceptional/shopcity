<?php

declare(strict_types=1);

namespace App\Events\User;

use App\Contracts\EventInterface;

class UserRegistered implements EventInterface
{
    public function __construct(
        public readonly string $fullName,
        public readonly string $email,
        public readonly string $contact,
        public readonly string $membership,
        public readonly ?string $reference,
    ) {}
}