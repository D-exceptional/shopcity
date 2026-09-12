<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\User\ContactMessageReceived;
use App\Models\User;
use App\Models\Notification;
use App\Listeners\Listener;

class CreateContactNotification extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Notification $notificationModel
    ) {}

    public function handle(
        ContactMessageReceived $event
    ): void {

        $adminNotificationMessage = "
            A message was sent by <b> " . trim($event->name) . "</b> from  <b> " . trim($event->country) . "</b>

            <br> You can reach out to them via their mobile: <b>" . trim($event->contact) . "</b> or email address: <b>" . trim($event->email) . "</b>
        ";

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $this->notificationModel->create(
                $adminNotificationMessage,
                'New Message',
                $admin['user_id']
            );
        }
    }
}