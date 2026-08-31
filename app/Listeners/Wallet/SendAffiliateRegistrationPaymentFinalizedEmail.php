<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\RegistrationPaymentFinalized;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Listeners\Listener;

class SendAffiliateRegistrationPaymentFinalizedEmail extends Listener
{
    public function __construct(
        protected Queue $queue
    ) {}

    public function handle(
        RegistrationPaymentFinalized $event
    ): void {

        if (
            !in_array(strtolower($event->regType), ['admin']) 
            && !is_null($event->affiliateEmail) 
            && !is_null($event->affiliateName)
        ) {

            $message = "
                Congratulations {$event->affiliateName}!

                <br> You've earned a commission for a successful referral.
                <br> You can access your account to verify this transaction.
                <br> Login to your dashboard here: <b><a href='/login'>Login to dashboard</a></b>
                <br> We look forward to seeing more referrals from you.
            ";
            
            $this->queue->dispatch(
                SimpleMailJob::class,
                [
                    'New Commission',
                    $event->affiliateEmail,
                    $message
                ],
                'emails'
            );
        }
    }
}