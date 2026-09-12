<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\OrderCompleted;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Models\User;
use App\Models\Store;
use App\Listeners\Listener;

class SendVendorOrderCompletedPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected User $userModel,
        protected Store $storeModel,
    ) {}

    public function handle(
        OrderCompleted $event
    ): void {

        if (!empty($event->stores)) {

            foreach ($event->stores as $store) {

                // Get store ID
                $storeId = $store['store_id'];

                // Get Vendor Details
                $vendorId    = $this->storeModel->findUserByStoreId($storeId);
                $vendorData  = $this->getBiodata($vendorId);
                $vendorName  = $vendorData['name'];

                // Build Vendors Message
                $message = "
                    Hi <b>{$vendorName}</b>, 

                    <br> The order, <b>{$event->orderCode}</b> has been completed. 
                    <br> You can reach out to our support service for any issues as regards this order.
                    <br> We hope to see more sales from your shop.
                    <br> Have a great day ahead.
                ";

                $vendorPushMessage = $this->textProcessor
                    ->formatPushMessage($message);

                $this->queue->dispatch(
                    PushNotificationJob::class,
                    [
                        "Single Vendor",
                        $vendorId,
                        'Order Completed',
                        $vendorPushMessage,
                        [
                            'url' => "/auth/user/login",
                            'type' => 'order'
                        ]
                    ],
                    'push'
                );
            }
        }
    }

    private function getBiodata(
        int $userId
    ): array {

        $userData = $this->userModel->findById($userId);

        return [
            'name' => $userData['firstname'] . ' ' . $userData['lastname'],
        ];
    }
}