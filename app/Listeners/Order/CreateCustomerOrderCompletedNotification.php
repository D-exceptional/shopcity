<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\OrderCompleted;
use App\Models\Notification;
use App\Models\User;
use App\Listeners\Listener;

class CreateCustomerOrderCompletedNotification extends Listener
{
    public function __construct(
        protected Notification $notificationModel,
        protected User $userModel
    ) {}

    public function handle(
        OrderCompleted $event
    ): void {

        // Get Customer Details
        $customerData  = $this->getBiodata($event->customerId);
        $customerName  = $customerData['name'];

        $customerNotificationMessage = "
            Hi <b>{$customerName}</b>, 

            <br> Your order, <b>{$event->orderCode}</b> has been completed. 
            <br> You can track this order using the code: <b>{$event->orderCode}</b> on your order track page.
            <br> We hope to see you shop again soon.
        ";

        $created = $this->notificationModel->create(
            $customerNotificationMessage,
            'Order Completion',
            $event->customerId
        );

        if ($created === false) {

            $this->logError("Failed to create order completion notification for customer: {$customerName}");
        }
    }

    private function getBiodata(
        int $userId
    ): array {

        $userData = $this->userModel->findById($userId);

        return [
            'name'  => $userData['firstname'] . ' ' . $userData['lastname'],
        ];
    }
}