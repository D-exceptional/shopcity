<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\PaymentProcessed;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\TextManager;
use App\Listeners\Listener;

class SendUserPaymentProcessedPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected TextManager $textProcessor
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

        $message = $this->textProcessor
            ->formatPushMessage($message);

        $baseUrl = config(
            'app.base_path',
            '/'
        );

        $this->queue->dispatch(
            PushNotificationJob::class,
            [
                "Single {$event->role}",
                $event->userId,
                'Payment Request Processed',
                $message,
                [
                    'url' => "/login",
                    'type' => 'payout'
                ]
            ],
            'push'
        );
    }
}