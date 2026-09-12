<?php

declare(strict_types=1);

namespace App\Events\Checkout;

use App\Contracts\EventInterface;

class Checkout implements EventInterface
{
    public function __construct(
        public readonly string $orderCode,
        public readonly array $user,
        public readonly array $stores,
    ) {}
}