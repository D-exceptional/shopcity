<?php

declare(strict_types=1);

namespace App\Listeners\Blog;

use App\Events\Blog\BlogCreated;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Listeners\Listener;

class SendAuthorBlogCreationEmail extends Listener
{
    public function __construct(
        protected Queue $queue
    ) {}

    public function handle(
        BlogCreated $event
    ): void {

        $message = "
            Hi <b>{$event->name}</b>, 
            
            <br> Your blog is currently <b>pending approval</b>. 
            <br> Our team is reviewing your blog details. Once approved, it'll be displyed live on the site. 
            <br> We'll notify you as soon as the status changes.
            <br> Thank you for your patience.
        ";

        $this->queue->dispatch(
            SimpleMailJob::class,
            [
                'Blog Creation Successful',
                $event->email,
                $message
            ],
            'emails'
        );
    }
}