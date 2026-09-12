<?php

declare(strict_types=1);

namespace App\Listeners\Checkout;

use App\Events\Checkout\Checkout;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Listeners\Listener;

class SendCustomerCheckoutEmail extends Listener
{
    public function __construct(
        protected Queue $queue
    ) {}

    public function handle(
        Checkout $event
    ): void {

        $customerEmailMessage = "
            Hi <b>{$event->user['name']}</b>,

            <br> Your order has been received and is currently being processed. 
            <br> You can track this order using the code: <b>{$event->orderCode}</b> on your order track page.
            <br> We hope to see you shop again soon.
        ";

        $this->queue->dispatch(
            SimpleMailJob::class,
            [
                'Order Created Successfully',
                $event->user['email'],
                $customerEmailMessage
            ],
            'emails'
        );
    }
}