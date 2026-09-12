<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\OrderCompleted;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\TextManager;
use App\Models\User;
use App\Listeners\Listener;

class SendCustomerOrderCompletedPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected TextManager $textProcessor,
        protected User $userModel
    ) {}

    public function handle(
        OrderCompleted $event
    ): void {

        // Get Customer Details
        $customerData  = $this->getBiodata($event->customerId);
        $customerName  = $customerData['name'];

        $message = "
            Hi <b>{$customerName}</b>, 

            <br> Your order, <b>{$event->orderCode}</b> has been completed. 
            <br> You can track this order using the code: <b>{$event->orderCode}</b> on your order track page.
            <br> We hope to see you shop again soon.
        ";

        $customerPushMessage = $this->textProcessor
            ->formatPushMessage($message);

        $this->queue->dispatch(
            PushNotificationJob::class,
            [
                "Single Customer",
                $event->customerId,
                'Order Completed',
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