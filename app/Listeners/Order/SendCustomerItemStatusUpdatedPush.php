<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\ItemStatusUpdated;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\TextManager;
use App\Models\User;
use App\Listeners\Listener;

class SendCustomerItemStatusUpdatedPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected TextManager $textProcessor,
        protected User $userModel,
    ) {}

    public function handle(
        ItemStatusUpdated $event
    ): void {

        // Get Vendor Details
        $customerData  = $this->getBiodata($event->customerId);
        $customerName  = $customerData['name'];

        // Build Customer Message
        $message = ($event->status === 'Shipped')
        ? "
            Hi <b>{$customerName}</b>, 

            <br> Your item, <b>{$event->itemName}</b>, from the order, <b>{$event->orderCode}</b>, has been shipped!
            <br> When you receive the shipment, login to your dashboard and click on the <b>I have received shipment</b> button beside this product on your order page. 
            <br> Thank you for buying on our platform.
            <br> We hope to see more shopping from you soon.
            <br> Have a great day ahead.
        " :
        "
            Hi <b>{$customerName}</b>, 

            <br> Thank you for confirming the shipment.
            <br> Here are the details of the product: 
            <br> 
            <hr>
            <br>
            
            <center>
                Item Name:       <b>{$event->itemName}</b><br>
                Item Code:       <b>{$event->itemCode}</b><br>
                Total Quantity:  <b>{$event->itemQuantity}</b><br>
                Order Date:      <b>{$event->orderDate}</b><br>
                Delivery Date:   <b>{$event->deliveryDate}</b><br>
                Status:          <b>{$event->status}</b><br>
            </center>

            <br> If you have any complaints or reviews, feel free to reach out to us via support@{strtolower(appUrl)}.com
            <br> We hope to see more shopping from you soon.
            <br> Have a great day ahead.
        ";

        $customerPushMessage = $this->textProcessor
            ->formatPushMessage($message);

        $this->queue->dispatch(
            PushNotificationJob::class,
            [
                "Single Customer",
                $event->customerId,
                'Order Item Status Update',
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