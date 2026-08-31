<?php

declare(strict_types=1);

namespace App\Listeners\Blog;

use App\Events\Blog\BannerUpdated;
use App\Queue\Queue;
use App\Jobs\CloudinaryJob;
use App\Listeners\Listener;

class UpdateBanner extends Listener
{
    public function __construct(
        protected Queue $queue
    ) {}

    public function handle(
        BannerUpdated $event
    ): void {

        if (
            empty($event->oldBanner) ||
            $event->oldBanner === $event->newBanner
        ) {
            return;
        }

        $this->queue->dispatch(
            CloudinaryJob::class,
            [
               $event->oldBanner
            ],
            'cloudinary'
        );
    }
}