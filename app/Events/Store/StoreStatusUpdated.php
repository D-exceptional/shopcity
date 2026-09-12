<?php

declare(strict_types=1);

namespace App\Events\Store;

use App\Contracts\EventInterface;

class StoreStatusUpdated implements EventInterface
{
    public function __construct(
        public readonly string $status,
        public readonly int $vendorId,
    ) {}
}