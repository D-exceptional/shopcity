<?php

declare(strict_types=1);

namespace App\Listeners\Store;

use App\Events\Store\StoreAvatarUpdated;
use App\Queue\Queue;
use App\Jobs\CloudinaryJob;
use App\Listeners\Listener;

class UpdateStoreAvatar extends Listener
{
    public function __construct(
        protected Queue $queue
    ) {}

    public function handle(
        StoreAvatarUpdated $event
    ): void {

        if (
            empty($event->oldAvatar) ||
            $event->oldAvatar === $event->newAvatar
        ) {
            return;
        }

        $this->queue->dispatch(
            CloudinaryJob::class,
            [
               $event->oldAvatar
            ],
            'cloudinary'
        );
    }
}