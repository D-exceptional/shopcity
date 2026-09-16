<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\ItemStatusUpdated;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\TextManager;
use App\Models\User;
use App\Listeners\Listener;

class SendAdminItemStatusUpdatedPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected User $userModel,
        protected TextManager $textProcessor
    ) {}

    public function handle(
       ItemStatusUpdated $event
    ): void {

        // Build Admin Message
        $message = ($event->status === 'Shipped')
        ? "
            Hello Admin, 

            <br> A product, <b>{$event->itemName}</b>, from the order, <b>{$event->orderCode}</b>, has been shipped!
            <br> Kindly review and take necessary actions. 
        " :
        "   
            Hello Admin, 

            <br> A product shipment has been confirmed by a customer.
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

            <br> Kindly review and take necessary actions accordingly
        ";

        $adminPushMessage = $this->textProcessor
            ->formatPushMessage($message);

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $this->queue->dispatch(
                PushNotificationJob::class,
                [
                    'Single Admin',
                    $admin['user_id'],
                    'Order Item Status Update',
                    $adminPushMessage,
                    [
                        'url' => "/auth/admin/login",
                        'type' => 'order'
                    ]
                ],
                'push'
            );
        }
    }
}