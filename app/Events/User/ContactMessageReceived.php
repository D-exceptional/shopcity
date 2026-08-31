<?php

declare(strict_types=1);

namespace App\Events\User;

use App\Contracts\EventInterface;

class ContactMessageReceived implements EventInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $contact,
        public readonly string $country,
        public readonly string $subject,
        public readonly string $message,
    ) {}
}