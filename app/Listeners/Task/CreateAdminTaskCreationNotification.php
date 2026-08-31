<?php

declare(strict_types=1);

namespace App\Listeners\Task;

use App\Events\Task\TaskCreated;
use App\Models\User;
use App\Models\Notification;
use App\Listeners\Listener;

class CreateAdminTaskCreationNotification extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Notification $notificationModel
    ) {}

    public function handle(
        TaskCreated $event
    ): void {

        $message = "
            A new task was created on the platform.
        ";

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $created = $this->notificationModel->create(
                $message,
                'New Task',
                $admin['user_id']
            );

            if ($created === false) {

                $this->logError("Failed to create task creation notification for admin: {$admin['email']}");
            }
        }
    }
}