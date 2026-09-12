<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\ItemStatusUpdated;
use App\Queue\Queue;
use App\Models\User;
use App\Models\Notification;
use App\Listeners\Listener;

class CreateVendorItemStatusUpdatedNotification extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected User $userModel,
        protected Notification $notificationModel
    ) {}

    public function handle(
        ItemStatusUpdated $event
    ): void {

        // Get Vendor Details
        $vendorData  = $this->getBiodata($event->vendorId);
        $vendorName  = $vendorData['name'];

        // Format Action
        $statusProcessed = strtolower($event->status);
        $statusAction    = $statusProcessed === 'shipped' ? 'shipment' : 'delivery';

        // Build Vendor Message
        $vendorNotificationMessage = "
            Hi <b>{$vendorName}</b>, 

            <br> The product, <b>{$event->itemName}</b>, from your order, <b>{$event->orderCode}</b>, has been {$statusProcessed}. 
            <br> You can reach out to our support service for any issues as regards this {$statusAction}.
            <br> We hope to see more sales from your shop.
            <br> Have a great day ahead.
        ";

        $created = $this->notificationModel->create(
            $vendorNotificationMessage,
            'Item Update',
            $event->vendorId
        );

        if ($created === false) {

            $this->logError("Failed to create item status updated notification for vendor: {$vendorName}");
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