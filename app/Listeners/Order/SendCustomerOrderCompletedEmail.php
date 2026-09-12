<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\OrderCompleted;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Models\User;
use App\Listeners\Listener;

class SendCustomerOrderCompletedEmail extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected User $userModel
    ) {}

    public function handle(
        OrderCompleted $event
    ): void {

        // Get Customer Details
        $customerData  = $this->getBiodata($event->customerId);
        $customerName  = $customerData['name'];
        $customerEmail = $customerData['email'];

        $customerEmailMessage = "
            Hi <b>{$customerName}</b>, 

            <br> Your order, <b>{$event->orderCode}</b> has been completed. 
            <br> You can track this order using the code: <b>{$event->orderCode}</b> on your order track page.
            <br> We hope to see you shop again soon.
        ";

        $this->queue->dispatch(
            SimpleMailJob::class,
            [
                'Order Completed',
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