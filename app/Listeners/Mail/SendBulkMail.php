<?php

declare(strict_types=1);

namespace App\Listeners\Mail;

use App\Events\Mail\MailSent;
use App\Queue\Queue;
use App\Jobs\BulkMailJob;
use App\Listeners\Listener;

class SendBulkMail extends Listener
{
    public function __construct(
        protected Queue $queue
    ) {}

    public function handle(
        MailSent $event
    ): void {

        $this->queue->dispatch(
            BulkMailJob::class,
            [
                $event->recipients,
                $event->hasAttachment,
                $event->type
            ],
            'broadcast',
            0,
            3,
            120 // 2 minutes in seconds. You can increase or reduce the time
        );
    }
}