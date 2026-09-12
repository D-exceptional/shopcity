<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\PayoutProcessed;
use App\Support\CurrencyManager;
use App\Models\User;
use App\Models\Notification;
use App\Listeners\Listener;

class CreateUserPayoutProcessedNotification extends Listener
{
    public function __construct(
        protected CurrencyManager $currencyManager,
        protected User $userModel,
        protected Notification $notificationModel
    ) {}

    public function handle(
        PayoutProcessed $event
    ): void {

        // Get User Details
        $userData  = $this->getBiodata($event->userId);
        $userName  = $userData['name'];

        // Format Amount
        $processedPayout = $this->currencyManager->format((float) $event->amount);

        $userNotificationMessage = "
            Hi <b>{$userName}</b>, 

            <br> You have received a payout of <b>{$processedPayout}</b> from ShopCity.
            <br> Your transaction reference is: <b>{$event->reference}</b>.
            <br> We hope to see more sales from your stores</b>.
            <br> Have a great day ahead.
        ";

        $created = $this->notificationModel->create(
            $userNotificationMessage,
            'Fund Payout',
            $event->userId
        );

        if ($created === false) {

            $this->logError("Failed to create payout processed notification for user: {$userName}");
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