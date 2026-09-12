<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\ItemStatusUpdated;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\TextManager;
use App\Models\User;
use App\Listeners\Listener;

class SendVendorItemStatusUpdatedPush extends Listener
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
        $vendorData  = $this->getBiodata($event->vendorId);
        $vendorName  = $vendorData['name'];

        // Format Action
        $statusProcessed = strtolower($event->status);
        $statusAction    = $statusProcessed === 'shipped' ? 'shipment' : 'delivery';

        // Build Vendor Message
        $message = "
            Hi <b>{$vendorName}</b>, 

            <br> The product, <b>{$event->itemName}</b>, from your order, <b>{$event->orderCode}</b>, has been {$statusProcessed}. 
            <br> You can reach out to our support service for any issues as regards this {$statusAction}.
            <br> We hope to see more sales from your shop.
            <br> Have a great day ahead.
        ";

        $vendorPushMessage = $this->textProcessor
            ->formatPushMessage($message);

        $this->queue->dispatch(
            PushNotificationJob::class,
            [
                "Single Vendor",
                $event->vendorId,
                'Order Item Status Update',
                $vendorPushMessage,
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