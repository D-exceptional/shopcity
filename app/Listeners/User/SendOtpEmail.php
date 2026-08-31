<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\User\OtpRequested;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Listeners\Listener;

class SendOtpEmail extends Listener
{
    public function __construct(
        protected Queue $queue
    ) {}

    public function handle(
        OtpRequested $event
    ): void {

        $message = "
            Hi {$event->name}, 
            
            <br>
            Your password reset OTP is <b>{$event->otp}</b> and it expires in 5 minutes
        ";

        $this->queue->dispatch(
            SimpleMailJob::class,
            [
                'Password Reset OTP',
                $event->email,
                $message
            ],
            'emails'
        );
    }
}