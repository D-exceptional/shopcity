<?php

declare(strict_types=1);

namespace App\Events\Product;

use App\Contracts\EventInterface;

class ReviewCreated implements EventInterface
{
    public function __construct(
        public readonly string $name,
        public readonly int $vendorId,
    ) {}
}