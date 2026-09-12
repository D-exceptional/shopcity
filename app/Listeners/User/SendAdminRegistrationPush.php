<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\User\UserRegistered;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Models\User;
use App\Support\TextManager;
use App\Listeners\Listener;

class SendAdminRegistrationPush extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Queue $queue,
        protected TextManager $textProcessor
    ) {}

    public function handle(
        UserRegistered $event
    ): void {

        $message = "
            Hello Admin,

            <br> A new {$event->role}, <b>{$event->name}</b>, just registered on the platform.
            <br> Kindly review and take necessary actions. 
        ";

        $adminPushMessage = $this->textProcessor
            ->formatPushMessage($message);

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $this->queue->dispatch(
                PushNotificationJob::class,
                [
                    'Single Admin',
                    $admin['user_id'],
                    'New Registration',
                    $adminPushMessage,
                    [
                        'url' => "/auth/admin/login",
                        'type' => 'registration'
                    ]
                ],
                'push'
            );
        }
    }
}