<?php

declare(strict_types=1);

namespace App\Listeners\Store;

use App\Events\Store\StoreCreated;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\TextManager;
use App\Models\User;
use App\Listeners\Listener;

class SendAdminStoreCreatedPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected User $userModel,
        protected TextManager $textProcessor
    ) {}

    public function handle(
       StoreCreated $event
    ): void {

        // Build Admin Message
        $message = "
           Hello Admin,

            <br> A new store, <b>{$event->name}</b>, was created on the platform!
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
                    'New Store Created',
                    $adminPushMessage,
                    [
                        'url' => "/auth/admin/login",
                        'type' => 'store'
                    ]
                ],
                'push'
            );
        }
    }
}