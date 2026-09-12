<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\OrderCanceled;
use App\Queue\Queue;
use App\Support\CurrencyManager;
use App\Models\User;
use App\Models\Store;
use APP\Models\Notification;
use App\Listeners\Listener;

class CreateVendorOrderCanceledNotification extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected CurrencyManager $currencyManager,
        protected User $userModel,
        protected Store $storeModel,
        protected Notification $notificationModel
    ) {}

    public function handle(
        OrderCanceled $event
    ): void {

        if (!empty($event->stores)) {

            foreach ($event->stores as $store) {

                // Get store ID
                $storeId = $store['store_id'];

                // Get Vendor Details
                $vendorId    = $this->storeModel->findUserByStoreId($storeId);
                $vendorData  = $this->getBiodata($vendorId);
                $vendorName  = $vendorData['name'];

                // Format Compensation
                $processedCompensation = $this->currencyManager->format((float) $event->vendorCompensation);

                // Build Vendors Message
                $vendorNotificationMessage = "
                    Hi <b>{$vendorName}</b>, 

                    <br> You have been compensated with the the sum of: <b>{$processedCompensation}</b> due to cancellation of the order: <b>{$event->orderCode}</b>. 
                    <br> This is in line with our policy to compensate vendors for any inconveniencies incurred during the order processing phase.
                    <br> We hope to see more sales from your shop.
                    <br> Have a great day ahead.
                ";

                $created = $this->notificationModel->create(
                    $vendorNotificationMessage,
                    'Order Cancellation',
                    $vendorId
                );

                if ($created === false) {

                    $this->logError("Failed to create order cancellation notification for vendor: {$vendorName}");
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