<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\RequestPlaced;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Listeners\Listener;

class SendUserRequestPlacedEmail extends Listener
{
    public function __construct(
        protected Queue $queue
    ) {}

    public function handle(
        RequestPlaced $event
    ): void {

        $message = "
            Hi <b>{$event->name}</b>, 
            
            <br> You have successfully placed a withdrawal of <b>{$event->amount}</b>. 
            <br> A total of <b>{$event->amount}</b> will be paid into your bank account shortly. 
            <br> Have a great day ahead.
        ";

        $this->queue->dispatch(
            SimpleMailJob::class,
            [
                'Withdrawal Placed Successfully',
                $event->email,
                $message
            ],
            'emails'
        );
    }
}