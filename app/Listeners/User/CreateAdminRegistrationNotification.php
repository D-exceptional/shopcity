<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\User\UserRegistered;
use App\Models\User;
use App\Models\Notification;
use App\Listeners\Listener;

class CreateAdminRegistrationNotification extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Notification $notificationModel
    ) {}

    public function handle(
        UserRegistered $event
    ): void {

        $adminNotificationMessage = "
            A new {$event->role},
            <b>{$event->name}</b>,
            just registered on the platform.
        ";

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $created = $this->notificationModel->create(
                $adminNotificationMessage,
                'New Registration',
                $admin['user_id']
            );

            if ($created === false) {

                $this->logError("Failed to create new user registration notification for admin: {$admin['email']}");
            }
        }
    }
}