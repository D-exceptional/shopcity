<?php

declare(strict_types=1);

namespace App\Events\Blog;

use App\Contracts\EventInterface;

class BlogCreated implements EventInterface
{
    public function __construct(
        public readonly int $userId,
        public readonly string $name,
        public readonly string $email,
        public readonly string $role,
    ) {}
}