<?php

declare(strict_types=1);

namespace App\Events\Order;

use App\Contracts\EventInterface;

class OrderCanceled implements EventInterface
{
    public function __construct(
        public readonly string $orderCode,
        public readonly int $customerId,
        public readonly float $customerRefund,
        public readonly array $stores,
        public readonly float $vendorCompensation,
    ) {}
}