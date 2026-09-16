<?php

declare(strict_types=1);

namespace App\Listeners\Checkout;

use App\Events\Checkout\Checkout;
use App\Models\User;
use App\Models\Notification;
use App\Listeners\Listener;

class CreateAdminCheckoutNotification extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Notification $notificationModel
    ) {}

    public function handle(
        Checkout $event
    ): void {

        $adminNotificationMessage = "
            Hello Admin, 

            <br> A new order, <b>{$event->orderCode}</b>, has been created!
            <br> Kindly review and take necessary actions. 
        ";

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $created = $this->notificationModel->create(
                $adminNotificationMessage,
                'New Order',
                $admin['user_id']
            );

            if ($created === false) {

                $this->logError("Failed to create checkout notification for admin: {$admin['email']}");
            }
        }
    }
}