<?php

declare(strict_types=1);

namespace App\Events\Wallet;

use App\Contracts\EventInterface;

class RegistrationPaymentFinalized implements EventInterface
{
    public function __construct(
        public readonly int $userId,
        public readonly string $userEmail,
        public readonly string $userRole,
        public readonly string $regType,
        public readonly ?int $affiliateId,
        public readonly ?string $affiliateName,
        public readonly ?string $affiliateEmail,
        public readonly ?string $affiliateRole,
    ) {}
}