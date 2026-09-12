<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\RequestPlaced;
use App\Support\CurrencyManager;
use App\Models\User;
use App\Models\Notification;
use App\Listeners\Listener;

class CreateUserRequestPlacedNotification extends Listener
{
    public function __construct(
        protected CurrencyManager $currencyManager,
        protected User $userModel,
        protected Notification $notificationModel
    ) {}

    public function handle(
        RequestPlaced $event
    ): void {

        // Get User Details
        $userData  = $this->getBiodata($event->userId);
        $userName  = $userData['name'];

        // Format Payout Amount
        $processedAmount = $this->currencyManager->format((float) $event->amount); 

        // Build User Message
        $userNotificationMessage = "
            Hi <b>{$userName}</b>, 

            <br> You have successfully placed a withdrawal of <b>{$processedAmount}</b>. 
            <br> A total of <b>{$processedAmount}</b> will be paid into your bank account shortly. 
            <br> We hope to see more sales from your store.
            <br> Have a great day ahead.
        ";

        $created = $this->notificationModel->create(
            $userNotificationMessage,
            'Fund Request',
            $event->userId
        );

        if ($created === false) {

            $this->logError("Failed to create request withdrawal notification for user: {$userName}");
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