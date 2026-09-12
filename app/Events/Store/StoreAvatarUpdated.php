<?php

declare(strict_types=1);

namespace App\Events\Store;

use App\Contracts\EventInterface;

class StoreAvatarUpdated implements EventInterface
{
    public function __construct(
        public readonly ?string $oldAvatar,
        public readonly ?string $newAvatar,
    ) {}
}