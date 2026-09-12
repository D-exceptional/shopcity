<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\ItemStatusUpdated;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Models\User;
use App\Listeners\Listener;

class SendVendorItemStatusUpdatedEmail extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected User $userModel,
    ) {}

    public function handle(
        ItemStatusUpdated $event
    ): void {

        // Get Vendor Details
        $vendorData  = $this->getBiodata($event->vendorId);
        $vendorName  = $vendorData['name'];
        $vendorEmail = $vendorData['email'];

        // Format Action
        $statusProcessed = strtolower($event->status);
        $statusAction    = $statusProcessed === 'shipped' ? 'shipment' : 'delivery';

        // Build Vendor Message
        $vendorEmailMessage = "
            Hi <b>{$vendorName}</b>, 

            <br> The product, <b>{$event->itemName}</b>, from your order, <b>{$event->orderCode}</b>, has been {$statusProcessed}. 
            <br> You can reach out to our support service for any issues as regards this {$statusAction}.
            <br> We hope to see more sales from your shop.
            <br> Have a great day ahead.
        ";

        $this->queue->dispatch(
            SimpleMailJob::class,
            [
                "Item {$event->status}",
                $vendorEmail,
                $vendorEmailMessage
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