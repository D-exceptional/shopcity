<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\User\UserRegistered;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Listeners\Listener;

class SendUserRegistrationEmail extends Listener
{
    public function __construct(
        protected Queue $queue
    ) {}

    public function handle(
        UserRegistered $event
    ): void {

        $message = "
            Hi <b>{$event->fullName}</b>, 

            <br> Your registration is currently <b>undergoing review</b>. 
            <br> Our team is reviewing your details. Once approved, you'll be able to login and use our services. 
            <br> We'll notify you as soon as the status changes.
            <br> Thank you for your patience.
        ";

        $this->queue->dispatch(
            SimpleMailJob::class,
            [
                'Registration Under Review',
                $event->email,
                $message
            ],
            'emails'
        );
    }
}