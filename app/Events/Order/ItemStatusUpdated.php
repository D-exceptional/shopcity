<?php

declare(strict_types=1);

namespace App\Events\Order;

use App\Contracts\EventInterface;

class ItemStatusUpdated implements EventInterface
{
    public function __construct(
        public readonly string $itemName,
        public readonly string $itemCode,
        public readonly int $itemQuantity,
        public readonly string $orderCode,
        public readonly string $orderDate,
        public readonly string $deliveryDate,
        public readonly string $status,
        public readonly string $subject,
        public readonly int $customerId,
        public readonly int $vendorId,
    ) {}
}