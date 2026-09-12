<?php

declare(strict_types=1);

namespace App\Events\Order;

use App\Contracts\EventInterface;

class OrderCompleted implements EventInterface
{
    public function __construct(
        public readonly string $orderCode,
        public readonly int $customerId,
        public readonly array $stores,
    ) {}
}