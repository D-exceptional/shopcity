<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\OrderCanceled;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\CurrencyManager;
use App\Support\TextManager;
use App\Models\User;
use App\Listeners\Listener;

class SendCustomerOrderCanceledPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected CurrencyManager $currencyManager,
        protected TextManager $textProcessor,
        protected User $userModel,
    ) {}

    public function handle(
        OrderCanceled $event
    ): void {

        // Get Vendor Details
        $customerData  = $this->getBiodata($event->customerId);
        $customerName  = $customerData['name'];

        // Format Refund
        $processedRefund = $this->currencyManager->format((float) $event->customerRefund);

        // Build Customer Message
        $message = "
           Hi <b>{$customerName}</b>, 

            <br> You have cancelled your order: <b>{$event->orderCode}</b>. 
            <br> You have been refunded the the sum of: <b>{$processedRefund}</b> to enable you continue with seamless shopping across our marketplace. 
            <br> This is in line with our policy to ensure grievances are settled wholly.
            <br> We hope to see more shopping from you.
            <br> Have a great day ahead.
        ";

        $customerPushMessage = $this->textProcessor
            ->formatPushMessage($message);

        $this->queue->dispatch(
            PushNotificationJob::class,
            [
                "Single Customer",
                $event->customerId,
                'Order Cancelled',
                $customerPushMessage,
                [
                    'url' => "/auth/user/login",
                    'type' => 'order'
                ]
            ],
            'push'
        );
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