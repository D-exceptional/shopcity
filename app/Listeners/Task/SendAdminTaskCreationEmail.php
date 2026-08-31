<?php

declare(strict_types=1);

namespace App\Listeners\Task;

use App\Events\Task\TaskCreated;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Models\User;
use App\Listeners\Listener;

class SendAdminTaskCreationEmail extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Queue $queue
    ) {}

    public function handle(
        TaskCreated $event
    ): void {

        $message = "
            Hello Admin,

            <br> A new task was created on the platform!
            <br> Kindly review and take necessary actions.
        ";

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $this->queue->dispatch(
                SimpleMailJob::class,
                [
                    'New Task',
                    $admin['email'],
                    $message
                ],
                'emails'
            );
        }
    }
}