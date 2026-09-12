<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\OrderCanceled;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\TextManager;
use App\Models\User;
use App\Listeners\Listener;

class SendAdminOrderCanceledPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected User $userModel,
        protected TextManager $textProcessor
    ) {}

    public function handle(
       OrderCanceled $event
    ): void {

        // Build Admin Message
        $message = "
            Hello Admin,

            <br> The order, <b>{$event->orderCode}</b>, has been canceled!
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
                    'Order Cancelled',
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