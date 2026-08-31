<?php

declare(strict_types=1);

namespace App\Events\Mail;

use App\Contracts\EventInterface;

class MailSent implements EventInterface
{
    public function __construct(
        public readonly array $recipients,
        public readonly bool $hasAttachment,
        public readonly string $type,
    ) {}
}