<?php

declare(strict_types=1);

namespace App\Listeners\Blog;

use App\Events\Blog\BlogCreated;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Models\User;
use App\Listeners\Listener;

class SendAdminBlogCreationEmail extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Queue $queue
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

            $this->queue->dispatch(
                SimpleMailJob::class,
                [
                    'New Blog',
                    $admin['email'],
                    $message
                ],
                'emails'
            );
        }
    }
}