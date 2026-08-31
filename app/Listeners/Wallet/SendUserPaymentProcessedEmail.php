<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\PaymentProcessed;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Listeners\Listener;

class SendUserPaymentProcessedEmail extends Listener
{
    public function __construct(
        protected Queue $queue
    ) {}

    public function handle(
        PaymentProcessed $event
    ): void {

        $message = "
            Hi <b>{$event->name}</b>, 

            <br> You have received a payout of <b>{$event->amount}</b> from JobSpot.
            <br> Your transaction reference is: <b>{$event->reference}</b>.
            <br> Have a great day ahead.
        ";

        $this->queue->dispatch(
            SimpleMailJob::class,
            [
                'Payment Request Processed',
                $event->email,
                $message
            ],
            'emails'
        );
    }
}