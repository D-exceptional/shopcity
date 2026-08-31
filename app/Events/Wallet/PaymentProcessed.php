<?php

declare(strict_types=1);

namespace App\Events\Wallet;

use App\Contracts\EventInterface;

class PaymentProcessed implements EventInterface
{
    public function __construct(
        public readonly int $userId,
        public readonly string $name,
        public readonly string $email,
        public readonly string $role,
        public readonly string $amount,
        public readonly string $reference,
    ) {}
}