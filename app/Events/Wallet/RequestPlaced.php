<?php

declare(strict_types=1);

namespace App\Events\Wallet;

use App\Contracts\EventInterface;

class RequestPlaced implements EventInterface
{
    public function __construct(
        public readonly int $userId,
        public readonly float $amount,
    ) {}
}