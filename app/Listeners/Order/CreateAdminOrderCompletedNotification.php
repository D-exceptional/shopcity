<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\OrderCompleted;
use App\Models\User;
use App\Models\Notification;
use App\Listeners\Listener;

class CreateAdminOrderCompletedNotification extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Notification $notificationModel
    ) {}

    public function handle(
        OrderCompleted $event
    ): void {

        $adminNotificationMessage =  "
            Hello Admin,

            <br> The order, <b>{$event->orderCode}</b>, has been completed!
            <br> Kindly review and take necessary actions. 
        ";

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $created = $this->notificationModel->create(
                $adminNotificationMessage,
                'Order Completion',
                $admin['user_id']
            );

            if ($created === false) {

                $this->logError("Failed to create order completion notification for admin: {$admin['email']}");
            }
        }
    }
}