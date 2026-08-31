<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\UserRegistered;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Models\User;
use App\Listeners\Listener;

class SendRegistrationPaymentFinalizedEmail extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Queue $queue
    ) {}

    public function handle(
        RegistrationPaymentFinalized $event
    ): void {

        $message = "
            Hello Admin, 

            <br> A new {$event->regType}, <b>{$event->userName}</b>, just registered on the platform!
            <br> Kindly review and take necessary actions. 
        ";

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $this->queue->dispatch(
                SimpleMailJob::class,
                [
                    'New Registration',
                    $admin['email'],
                    $message
                ],
                'emails'
            );
        }
    }
}