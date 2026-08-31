<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\RegistrationPaymentFinalized;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\TextManager;
use App\Listeners\Listener;

class SendUserRegistrationPaymentFinalizedPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected TextManager $textProcessor
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

        $message = $this->textProcessor
            ->formatPushMessage($message);

        $baseUrl = config(
            'app.base_path',
            '/'
        );

        $this->queue->dispatch(
            PushNotificationJob::class,
            [
                "Single {$event->userRole}",
                $event->userId,
                'Successful Registration',
                $message,
                [
                    'url' => "/login",
                    'type' => 'registration'
                ]
            ],
            'push'
        );
    }
}