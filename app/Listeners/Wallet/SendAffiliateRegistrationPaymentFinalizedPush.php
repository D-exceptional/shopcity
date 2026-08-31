<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\RegistrationPaymentFinalized;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\TextManager;
use App\Listeners\Listener;

class SendAffiliateRegistrationPaymentFinalizedPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected TextManager $textProcessor
    ) {}

    public function handle(
        RegistrationPaymentFinalized $event
    ): void {

        if (
            !in_array(strtolower($event->regType), ['admin']) 
            && !is_null($event->affiliateRole)
            && !is_null($event->affiliateId)
        ) {

            $message = "
                Congratulations {$event->affiliateName}!

                <br> You've earned a commission for a successful referral.
                <br> You can access your account to verify this transaction.
                <br> Login to your dashboard here: <b><a href='/login'>Login to dashboard</a></b>
                <br> We look forward to seeing more referrals from you.
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
                    "Single {$event->affiliateRole}",
                    $event->affiliateId,
                    'New Commission',
                    $message,
                    [
                        'url' => "/login",
                        'type' => 'commission'
                    ]
                ],
                'push'
            );
        }
    }
}