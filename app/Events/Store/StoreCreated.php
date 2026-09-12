<?php

declare(strict_types=1);

namespace App\Events\Store;

use App\Contracts\EventInterface;

class StoreCreated implements EventInterface
{
    public function __construct(
        public readonly string $name,
        public readonly int $vendorId,
    ) {}
}