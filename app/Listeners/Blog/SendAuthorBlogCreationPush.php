<?php

declare(strict_types=1);

namespace App\Listeners\Blog;

use App\Events\Blog\BlogCreated;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\TextManager;
use App\Listeners\Listener;

class SendAuthorBlogCreationPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected TextManager $textProcessor
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

        $message = $this->textProcessor
            ->formatPushMessage($message);

        $baseUrl = config(
            'app.base_path',
            '/'
        );

        $this->queue->dispatch(
            PushNotificationJob::class,
            [
                "Single {$event->role}",
                $event->userId,
                'Blog Creation Successful',
                $message,
                [
                    'url' => "{$baseUrl}/blog",
                    'type' => 'blog'
                ]
            ],
            'push'
        );
    }
}