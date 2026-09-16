<?php

declare(strict_types=1);

namespace App\Events\Media;

use App\Contracts\EventInterface;

class BulkMediaDeleted implements EventInterface
{
    public function __construct(
        public readonly array $media,
    ) {}
}