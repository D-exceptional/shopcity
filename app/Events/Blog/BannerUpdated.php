<?php

declare(strict_types=1);

namespace App\Events\Blog;

use App\Contracts\EventInterface;

class BannerUpdated implements EventInterface
{
    public function __construct(
        public readonly ?string $oldBanner,
        public readonly ?string $newBanner,
    ) {}
}