<?php

declare(strict_types=1);

namespace App\Events\Media;

use App\Contracts\EventInterface;

class SingleMediaDeleted implements EventInterface
{
    public function __construct(
        public readonly string $url,
    ) {}
}