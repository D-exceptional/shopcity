<?php

declare(strict_types=1);

namespace App\Listeners\Checkout;

use App\Events\Checkout\Checkout;
use App\Models\Notification;
use App\Listeners\Listener;

class CreateCustomerCheckoutNotification extends Listener
{
    public function __construct(
        protected Notification $notificationModel
    ) {}

    public function handle(
        Checkout $event
    ): void {

        $customerNotificationMessage = "
            Hi <b>{$event->user['name']}</b>,

            <br> Your order has been received and is currently being processed. 
            <br> You can track this order using the code: <b>{$event->orderCode}</b> on your order track page.
            <br> We hope to see you shop again soon.
        ";

        $created = $this->notificationModel->create(
            $customerNotificationMessage,
            'New Order Received',
            $event->user['id']
        );

        if ($created === false) {

            $this->logError("Failed to create order received notification for user: {$event->user['email']}");
        }
    }
}