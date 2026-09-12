<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\OrderCanceled;
use App\Models\User;
use App\Models\Notification;
use App\Listeners\Listener;

class CreateAdminOrderCanceledNotification extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Notification $notificationModel
    ) {}

    public function handle(
        OrderCanceled $event
    ): void {

        $adminNotificationMessage =  "
            Hello Admin,

            <br> The order, <b>{$event->orderCode}</b>, has been canceled!
            <br> Kindly review and take necessary actions. 
        ";

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $created = $this->notificationModel->create(
                $adminNotificationMessage,
                'Order Cancellation',
                $admin['user_id']
            );

            if ($created === false) {

                $this->logError("Failed to create order cancellation notification for admin: {$admin['email']}");
            }
        }
    }
}