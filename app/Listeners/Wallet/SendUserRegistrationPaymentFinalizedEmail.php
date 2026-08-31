<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\RegistrationPaymentFinalized;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Listeners\Listener;

class SendUserRegistrationPaymentFinalizedEmail extends Listener
{
    public function __construct(
        protected Queue $queue
    ) {}

    public function handle(
        RegistrationPaymentFinalized $event
    ): void {

        $message = "
            Hi <b>{$event->userName}</b>, 

            <br> You have successfully registered on JobSpot. 
            <br> Login to your dashboard here: <b><a href='/login'>Login to dashboard</a></b>
            <br> We are happy to have you onboard.
        ";

        $this->queue->dispatch(
            SimpleMailJob::class,
            [
                'Successful Registration',
                $event->userEmail,
                $message
            ],
            'emails'
        );
    }
}