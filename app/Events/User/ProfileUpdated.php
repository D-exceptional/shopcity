<?php

declare(strict_types=1);

namespace App\Events\User;

use App\Contracts\EventInterface;

class ProfileUpdated implements EventInterface
{
    public function __construct(
        public readonly ?string $oldAvatar,
        public readonly ?string $newAvatar,
    ) {}
}