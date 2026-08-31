<?php

declare(strict_types=1);

namespace App\Listeners\Blog;

use App\Events\Blog\BlogCreated;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Models\User;
use App\Support\TextManager;
use App\Listeners\Listener;

class SendAdminBlogCreationPush extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Queue $queue,
        protected TextManager $textProcessor
    ) {}

    public function handle(
       BlogCreated $event
    ): void {

        $message = "
            Hello Admin,

            <br> A new blog was created on the platform!
            <br> Kindly review and take necessary actions.
        ";

        $message = $this->textProcessor
            ->formatPushMessage($message);

        $baseUrl = config(
            'app.base_path',
            '/'
        );

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $this->queue->dispatch(
                PushNotificationJob::class,
                [
                    'Single Admin',
                    $admin['user_id'],
                    'New Blog',
                    $message,
                    [
                        'url' => "{$baseUrl}/admin",
                        'type' => 'blog'
                    ]
                ],
                'push'
            );
        }
    }
}