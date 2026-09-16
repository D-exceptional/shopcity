<?php

declare(strict_types=1);

namespace App\Listeners\Media;

use App\Events\Media\SingleMediaDeleted;
use App\Queue\Queue;
use App\Jobs\CloudinaryJob;
use App\Listeners\Listener;

class DeleteSingleMedia extends Listener
{
    public function __construct(
        protected Queue $queue
    ) {}

    public function handle(
        SingleMediaDeleted $event
    ): void {

        if (
            !isset($event->url) 
            || empty($event->url)
            || is_null($event->url)
        ) {
            return;
        }

        $this->queue->dispatch(
            CloudinaryJob::class,
            [
               $event->url
            ],
            'cloudinary'
        );
    }
}