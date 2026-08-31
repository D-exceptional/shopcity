<?php

declare(strict_types=1);

namespace App\Listeners\Task;

use App\Events\Task\TaskStatusUpdated;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\TextManager;
use App\Listeners\Listener;

class SendUserTaskStatusUpdatedPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected TextManager $textProcessor
    ) {}

    public function handle(
        TaskStatusUpdated $event
    ): void {

        $statusMessages = [
            'Approved' => "
                Hi <b>{$event->name}</b>, 
                <br> Great news! 🎉 Your latest attempt has been <b>approved</b>. 
                <br> A total of {$event->amount} has been credited to your task wallet.
                <br> Login to your dashboard via this link: <b><a href='/login'>Visit Dashboard</a></b> to confirm.
                <br> We hope to see more attempts from you!
            ",
        ];

        // Fallback in case of unknown status
        $message = $statusMessages[$event->status] ?? "
            Hi <b>{$event->name}</b>, 
            <br> There has been an update to your latest task attempt. 
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
                'Task Status Updated',
                $message,
                [
                    'url' => "/login",
                    'type' => 'task'
                ]
            ],
            'push'
        );
    }
}