<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\OrderCompleted;
use App\Queue\Queue;
use App\Models\User;
use App\Models\Store;
use APP\Models\Notification;
use App\Listeners\Listener;

class CreateVendorOrderCompletedNotification extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected User $userModel,
        protected Store $storeModel,
        protected Notification $notificationModel
    ) {}

    public function handle(
        OrderCompleted $event
    ): void {

        if (!empty($event->stores)) {

            foreach ($event->stores as $store) {

                // Get Store ID
                $storeId = $store['store_id'];

                // Get Vendor Details
                $vendorId    = $this->storeModel->findUserByStoreId($storeId);
                $vendorData  = $this->getBiodata($vendorId);
                $vendorName  = $vendorData['name'];

                // Build Vendors Message
                $vendorNotificationMessage = "
                    Hi <b>{$vendorName}</b>, 

                    <br> The order, <b>{$event->orderCode}</b> has been completed. 
                    <br> You can reach out to our support service for any issues as regards this order.
                    <br> We hope to see more sales from your shop.
                    <br> Have a great day ahead.
                ";

                $created = $this->notificationModel->create(
                    $vendorNotificationMessage,
                    'Order Completion',
                    $vendorId
                );

                if ($created === false) {

                    $this->logError("Failed to create order completion notification for vendor: {$vendorName}");
                }
            }
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