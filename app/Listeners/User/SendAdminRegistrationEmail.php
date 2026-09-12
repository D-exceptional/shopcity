<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\User\UserRegistered;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Models\User;
use App\Listeners\Listener;

class SendAdminRegistrationEmail extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Queue $queue
    ) {}

    public function handle(
        UserRegistered $event
    ): void {

        $adminEmailMessage = "
            Hello Admin,

            <br> A new {$event->role}, <b>{$event->name}</b>, just registered on the platform!
            <br> Kindly review and take necessary actions.
        ";

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $this->queue->dispatch(
                SimpleMailJob::class,
                [
                    'New Registration',
                    $admin['email'],
                    $adminEmailMessage
                ],
                'emails'
            );
        }
    }
}