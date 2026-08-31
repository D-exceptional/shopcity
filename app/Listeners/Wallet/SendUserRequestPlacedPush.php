<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\RequestPlaced;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\TextManager;
use App\Listeners\Listener;

class SendUserRequestPlacedPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected TextManager $textProcessor
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
                'Withdrawal Placed Successfully',
                $message,
                [
                    'url' => "/login",
                    'type' => 'withdrawal'
                ]
            ],
            'push'
        );
    }
}