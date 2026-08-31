<?php

declare(strict_types=1);

namespace App\Listeners\Blog;

use App\Events\Blog\BlogStatusUpdated;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\TextManager;
use App\Listeners\Listener;

class SendAuthorBlogStatusUpdatedPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected TextManager $textProcessor
    ) {}

    public function handle(
        BlogStatusUpdated $event
    ): void {

        // Build message based on status
        $statusMessages = [
            'Active' => "
                Hi <b>{$event->name}</b>, 
                <br> Great news! 🎉 Your latest blog is now <b>active</b>. 
                <br> People can now read, react, share and bookmark your blog.
                <br> Check it out via this link: <b><a href='/read?id={$event->id}'>Read Blog</a></b>.
                <br> We hope to see more of your blogs on our platform!
            ",

            'Pending' => "
                Hi <b>{$event->name}</b>, 
                <br> Your store has been <b>deactivated</b>. 
                <br> This may be due to policy violations, inactivity, or other issues. 
                <br> Please contact support at <b>support@mrsamase.com</b> or visit <b><a href='/contact'>Appeal Page</a></b> to resolve this and restore your blog. 
                <br> We value your partnership and hope to have you back soon.
            ",
        ];

        // Fallback in case of unknown status
        $message = $statusMessages[$event->status] ?? "
            Hi <b>{$event->name}</b>, 
            <br> There has been an update to your blog status. 
            <br> Please check your dashboard for more details.
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
                'Blog Status Updated',
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