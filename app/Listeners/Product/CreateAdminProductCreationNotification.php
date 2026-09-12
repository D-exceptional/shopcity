<?php

declare(strict_types=1);

namespace App\Listeners\Product;

use App\Events\Product\ProductCreated;
use App\Models\User;
use App\Models\Notification;
use App\Listeners\Listener;

class CreateAdminProductCreationNotification extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Notification $notificationModel
    ) {}

    public function handle(
        ProductCreated $event
    ): void {

        $adminNotificationMessage = "
            Hello Admin, 

            <br> A new product, <b>{$event->name}</b>, was created on the platform!
            <br> Kindly review and take necessary actions. 
        ";

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $created = $this->notificationModel->create(
                $adminNotificationMessage,
                'New Product',
                $admin['user_id']
            );

            if ($created === false) {

                $this->logError("Failed to create product creation notification for admin: {$admin['email']}");
            }
        }
    }
}