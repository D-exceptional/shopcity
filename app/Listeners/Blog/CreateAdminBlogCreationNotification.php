<?php

declare(strict_types=1);

namespace App\Listeners\Blog;

use App\Events\Blog\BlogCreated;
use App\Models\User;
use App\Models\Notification;
use App\Listeners\Listener;

class CreateAdminBlogCreationNotification extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Notification $notificationModel
    ) {}

    public function handle(
        BlogCreated $event
    ): void {

        $message = "
            Hello Admin,

            <br> A new blog was created on the platform!
            <br> Kindly review and take necessary actions.
        ";

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $created = $this->notificationModel->create(
                $message,
                'New Blog',
                $admin['user_id']
            );

            if ($created === false) {

                $this->logError("Failed to create blog creation notification for admin: {$admin['email']}");
            }
        }
    }
}