<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\ItemStatusUpdated;
use App\Models\User;
use App\Models\Notification;
use App\Listeners\Listener;

class CreateAdminItemStatusUpdatedNotification extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Notification $notificationModel
    ) {}

    public function handle(
        ItemStatusUpdated $event
    ): void {

        // Build Admin Message
        $adminNotificationMessage = ($event->status === 'Shipped')
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

            <br> Kindly review and credit the vendor's wallet accordingly
        ";

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $created = $this->notificationModel->create(
                $adminNotificationMessage,
                'Item Update',
                $admin['user_id']
            );

            if ($created === false) {

                $this->logError("Failed to create item status updated notification for admin: {$admin['email']}");
            }
        }
    }
}