<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\OrderCanceled;
use App\Support\CurrencyManager;
use App\Models\Notification;
use App\Models\User;
use App\Listeners\Listener;

class CreateCustomerOrderCanceledNotification extends Listener
{
    public function __construct(
        protected CurrencyManager $currencyManager,
        protected Notification $notificationModel,
        protected User $userModel
    ) {}

    public function handle(
        OrderCanceled $event
    ): void {

        // Get Customer Details
        $customerData  = $this->getBiodata($event->customerId);
        $customerName  = $customerData['name'];

        // Format Refund
        $processedRefund = $this->currencyManager->format((float) $event->customerRefund);

        $customerNotificationMessage = "
            Hi <b>{$customerName}</b>, 

            <br> You have cancelled your order: <b>{$event->orderCode}</b>. 
            <br> You have been refunded the the sum of: <b>{$processedRefund}</b> to enable you continue with seamless shopping across our marketplace. 
            <br> This is in line with our policy to ensure grievances are settled wholly.
            <br> We hope to see more shopping from you.
            <br> Have a great day ahead.
        ";

        $created = $this->notificationModel->create(
            $customerNotificationMessage,
            'Order Cancellation',
            $event->customerId
        );

        if ($created === false) {

            $this->logError("Failed to create order cancelation notification for customer: {$customerName}");
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