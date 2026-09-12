<?php

declare(strict_types=1);

namespace App\Listeners\Checkout;

use App\Events\Checkout\Checkout;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Models\User;
use App\Support\TextManager;
use App\Listeners\Listener;

class SendAdminCheckoutPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected User $userModel,
        protected TextManager $textProcessor
    ) {}

    public function handle(
       Checkout $event
    ): void {

        $message = "
            Hello Admin, 

            <br> A new order, <b>{$event->orderCode}</b>, has been created!
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
                    'New Order Notification',
                    $adminPushMessage,
                    [
                        'url' => "/auth/admin/login",
                        'type' => 'order'
                    ]
                ],
                'push'
            );
        }
    }
}