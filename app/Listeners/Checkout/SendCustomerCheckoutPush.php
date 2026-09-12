<?php

declare(strict_types=1);

namespace App\Listeners\Checkout;

use App\Events\Checkout\Checkout;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\TextManager;
use App\Listeners\Listener;

class SendCustomerCheckoutPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected TextManager $textProcessor
    ) {}

    public function handle(
        Checkout $event
    ): void {

        $message = "
            Hi <b>{$event->user['name']}</b>,

            <br> Your order has been received and is currently being processed. 
            <br> You can track this order using the code: <b>{$event->orderCode}</b> on your order track page.
            <br> We hope to see you shop again soon.
        ";

        $customerPushMessage = $this->textProcessor
            ->formatPushMessage($message);

        $this->queue->dispatch(
            PushNotificationJob::class,
            [
                "Single Customer",
                $event->user['id'],
                'Order Created Successfully',
                $customerPushMessage,
                [
                    'url' => "/auth/user/login",
                    'type' => 'order'
                ]
            ],
            'push'
        );
    }
}