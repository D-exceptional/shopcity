<?php

declare(strict_types=1);

namespace App\Listeners\Media;

use App\Events\Media\BulkMediaDeleted;
use App\Queue\Queue;
use App\Jobs\CloudinaryJob;
use App\Listeners\Listener;

class DeleteBulkMedia extends Listener
{
    public function __construct(
        protected Queue $queue
    ) {}

    public function handle(
        BulkMediaDeleted $event
    ): void {

        if (
            !isset($event->media) 
            || empty($event->media)
            || is_null($event->media)
        ) {
            return;
        }

        $this->queue->dispatch(
            CloudinaryJob::class,
            [
               $event->media
            ],
            'cloudinary'
        );
    }
}