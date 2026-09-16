<?php

declare(strict_types=1);

namespace App\Listeners\Checkout;

use App\Events\Checkout\Checkout;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\CurrencyManager;
use App\Models\User;
use App\Models\Store;
use App\Listeners\Listener;

class SendVendorCheckoutPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected CurrencyManager $currencyManager,
        protected User $userModel,
        protected Store $storeModel,
    ) {}

    public function handle(
        Checkout $event
    ): void {

        if (!empty($event->stores)) {

            foreach ($event->stores as $store) {

                $storeId    = $store['store_id'];
                $storeTotal = $store['total'];

                // Commission (90%)
                $vendorCommission    = round($storeTotal * 0.90, 2);
                $processedCommission = $this->currencyManager->format((float) $vendorCommission); 

                // Get Vendor Details
                $vendorId    = $this->storeModel->findUserByStoreId($storeId);
                $vendorData  = $this->getBiodata($vendorId);
                $vendorName  = $vendorData['name'];

                // Build Vendors Message
                $message = "
                    Hi <b>{$vendorName}</b>,

                    <br> You have a new order on your store with an ID: <b>{$event->orderCode}</b>!
                    <br> Your payout wallet has been credited with <b>{$processedCommission}</b> for this order</b>. 
                    <br> Thank you for selling on our platform. Keep up the great work!
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
                        'New Order Received',
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