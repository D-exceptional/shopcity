<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\RegistrationPaymentFinalized;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Models\User;
use App\Support\TextManager;
use App\Listeners\Listener;

class SendRegistrationPaymentFinalizedPush extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Queue $queue,
        protected TextManager $textProcessor
    ) {}

    public function handle(
        RegistrationPaymentFinalized $event
    ): void {

        $message = "
            Hello Admin, 

            <br> A new {$event->regType}, <b>{$event->userName}</b>, just registered on the platform!
            <br> Kindly review and take necessary actions. 
        ";

        $message = $this->textProcessor
            ->formatPushMessage($message);

        $baseUrl = config(
            'app.base_path',
            '/'
        );

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $this->queue->dispatch(
                PushNotificationJob::class,
                [
                    'Single Admin',
                    $admin['user_id'],
                    'New Registration',
                    $message,
                    [
                        'url' => "{$baseUrl}/admin",
                        'type' => 'registration'
                    ]
                ],
                'push'
            );
        }
    }
}