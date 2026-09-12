<?php

declare(strict_types=1);

namespace App\Listeners\Product;

use App\Events\Product\ReviewCreated;
use App\Queue\Queue;
use App\Models\User;
use APP\Models\Notification;
use App\Listeners\Listener;

class CreateVendorReviewCreationNotification extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected User $userModel,
        protected Notification $notificationModel
    ) {}

    public function handle(
        ReviewCreated $event
    ): void {

        // Get Vendor Details
        $vendorData  = $this->getBiodata($event->vendorId);
        $vendorName  = $vendorData['name'];
        $vendorEmail = $vendorData['email'];

        // Build Vendors Message
        $vendorNotificationMessage = "
            Hi <b>{$vendorName}</b>, 

            <br> Your product, <b>{$event->name}</b>, just got a new review. 
            <br> It's a positive sign customers love it.
            <br> Keep updating your store with awesome products like this one.
            <br> Have a great day ahead.
        ";

        $created = $this->notificationModel->create(
            $vendorNotificationMessage,
            'Product Review',
            $event->vendorId
        );

        if ($created === false) {

            $this->logError("Failed to create product review notification for vendor: {$vendorName}");
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