<?php

declare(strict_types=1);

namespace App\Listeners\Task;

use App\Events\Task\TaskCreated;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Models\User;
use App\Support\TextManager;
use App\Listeners\Listener;

class SendAdminTaskCreationPush extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Queue $queue,
        protected TextManager $textProcessor
    ) {}

    public function handle(
        TaskCreated $event
    ): void {

        $message = "
            Hello Admin,

            <br> A new task was created on the platform.
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
                    'New Task',
                    $message,
                    [
                        'url' => "{$baseUrl}/admin",
                        'type' => 'task'
                    ]
                ],
                'push'
            );
        }
    }
}