<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\RegistrationPaymentFinalized;
use App\Models\Notification;
use App\Listeners\Listener;

class CreateAffiliateRegistrationPaymentFinalizedNotification extends Listener
{
    public function __construct(
        protected Notification $notificationModel
    ) {}

    public function handle(
        RegistrationPaymentFinalized $event
    ): void {

        if (
            !in_array(strtolower($event->regType), ['admin']) 
            && !is_null($event->affiliateId) 
            && !is_null($event->affiliateName)
            && !is_null($event->affiliateEmail)
        ) {

            $message = "
                Congratulations {$event->affiliateName}!

                <br> You've earned a commission for a successful referral.
                <br> You can access your account to verify this transaction.
                <br> Login to your dashboard here: <b><a href='/login'>Login to dashboard</a></b>
                <br> We look forward to seeing more referrals from you.
            ";

            $created = $this->notificationModel->create(
                $message,
                'New Commission',
                $event->affiliateId
            );

            if ($created === false) {

                $this->logError("Failed to create registration payment finalized notification for affiliate: {$event->affiliateEmail}");
            }
            
        }
    }
}