<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\OrderCanceled;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Support\CurrencyManager;
use App\Models\User;
use App\Listeners\Listener;

class SendCustomerOrderCanceledEmail extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected CurrencyManager $currencyManager,
        protected User $userModel,
    ) {}

    public function handle(
        OrderCanceled $event
    ): void {

        // Get Vendor Details
        $customerData  = $this->getBiodata($event->customerId);
        $customerName  = $customerData['name'];
        $customerEmail = $customerData['email'];

        // Format Refund
        $processedRefund = $this->currencyManager->format((float) $event->customerRefund);

        $customerEmailMessage = "
            Hi <b>{$customerName}</b>, 

            <br> You have cancelled your order: <b>{$event->orderCode}</b>. 
            <br> You have been refunded the the sum of: <b>{$processedRefund}</b> to enable you continue with seamless shopping across our marketplace. 
            <br> This is in line with our policy to ensure grievances are settled wholly.
            <br> We hope to see more shopping from you.
            <br> Have a great day ahead.
        ";

        $this->queue->dispatch(
            SimpleMailJob::class,
            [
                'Order Cancelled',
                $customerEmail,
                $customerEmailMessage
            ],
            'emails'
        );
    }

    private function getBiodata(
        int $userId
    ): array {

        $userData = $this->userModel->findById($userId);

        return [
            'name'  => $userData['firstname'] . ' ' . $userData['lastname'],
            'email' => $userData['email'],
        ];
    }
}