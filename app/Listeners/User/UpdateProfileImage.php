<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\User\ProfileUpdated;
use App\Queue\Queue;
use App\Jobs\CloudinaryJob;
use App\Listeners\Listener;

class UpdateProfileImage extends Listener
{
    public function __construct(
        protected Queue $queue
    ) {}

    public function handle(
        ProfileUpdated $event
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