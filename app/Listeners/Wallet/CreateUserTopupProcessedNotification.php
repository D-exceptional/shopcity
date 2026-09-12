<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\TopupProcessed;
use App\Support\CurrencyManager;
use App\Models\User;
use App\Models\Notification;
use App\Listeners\Listener;

class CreateUserTopupProcessedNotification extends Listener
{
    public function __construct(
        protected CurrencyManager $currencyManager,
        protected User $userModel,
        protected Notification $notificationModel
    ) {}

    public function handle(
        TopupProcessed $event
    ): void {

        // Get User Details
        $userData = $this->getBiodata($event->userId);
        $userName = $userData['name'];

        // Format Amount
        $fundedAmount = $this->currencyManager->format((float) $event->amount);
        $newBalance   = $this->currencyManager->format((float) $event->balance);

        $userNotificationMessage = "
            Hi <b>{$userName}</b>, 

            <br> You have successfully funded your shopping wallet with <b>{$fundedAmount}</b>.
            <br> Your new wallet balance is <b>{$newBalance}</b>.
            <br> Your transaction reference is: <b>{$event->reference}</b>.
            <br> We hope to see you shop again soon enough.
        ";

        $created = $this->notificationModel->create(
            $userNotificationMessage,
            'Wallet Topup',
            $event->userId
        );

        if ($created === false) {

            $this->logError("Failed to create topup processed notification for user: {$userName}");
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