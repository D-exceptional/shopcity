<?php

declare(strict_types=1);

namespace App\Listeners\Checkout;

use App\Events\Checkout\Checkout;
use App\Queue\Queue;
use App\Support\CurrencyManager;
use App\Models\User;
use App\Models\Store;
use APP\Models\Notification;
use App\Listeners\Listener;

class CreateVendorCheckoutNotification extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected CurrencyManager $currencyManager,
        protected User $userModel,
        protected Store $storeModel,
        protected Notification $notificationModel
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
                $vendorNotificationMessage = "
                    Hi <b>{$vendorName}</b>,

                    <br> You have a new order on your store with an ID: <b>{$event->orderCode}</b>!
                    <br> Your payout wallet has been credited with <b>{$processedCommission}</b> for this order</b>. 
                    <br> Thank you for selling on our platform. Keep up the great work!
                    <br> We hope to see more sales from your shop.
                    <br> Have a great day ahead.
                ";

                $created = $this->notificationModel->create(
                    $vendorNotificationMessage,
                    'New Order',
                    $vendorId
                );

                if ($created === false) {

                    $this->logError("Failed to create order received notification for vendor: {$vendorName}");
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