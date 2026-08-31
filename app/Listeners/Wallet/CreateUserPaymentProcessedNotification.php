<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\PaymentProcessed;
use App\Models\Notification;
use App\Listeners\Listener;

class CreateUserPaymentProcessedNotification extends Listener
{
    public function __construct(
        protected Notification $notificationModel
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

        $created = $this->notificationModel->create(
            $message,
            'Fund Payout',
            $event->userId
        );

        if ($created === false) {

            $this->logError("Failed to create payment processed notification for user: {$event->email}");
        }
    }
}