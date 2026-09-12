<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\TopupProcessed;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\TextManager;
use App\Support\CurrencyManager;
use App\Models\User;
use App\Listeners\Listener;

class SendUserTopupProcessedPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected TextManager $textProcessor,
        protected CurrencyManager $currencyManager,
        protected User $userModel,
    ) {}

    public function handle(
        TopupProcessed $event
    ): void {

        // Get User Details
        $userData  = $this->getBiodata($event->userId);
        $userName  = $userData['name'];
        $userEmail = $userData['email'];
        $userRole  = $userData['role'];

        // Format Amount
        $fundedAmount = $this->currencyManager->format((float) $event->amount);
        $newBalance   = $this->currencyManager->format((float) $event->balance);

        $message = "
            Hi <b>{$userName}</b>, 

            <br> You have successfully funded your shopping wallet with <b>{$fundedAmount}</b>.
            <br> Your new wallet balance is <b>{$newBalance}</b>.
            <br> Your transaction reference is: <b>{$event->reference}</b>.
            <br> We hope to see you shop again soon enough.
        ";

        $userPushMessage = $this->textProcessor
            ->formatPushMessage($message);

        $this->queue->dispatch(
            PushNotificationJob::class,
            [
                "Single {$userRole}",
                $event->userId,
                'Wallet Topup Successful',
                $userPushMessage,
                [
                    'url'  => "/auth/user/login",
                    'type' => 'topup'
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
            'email' => $userData['email'],
            'role'  => $userData['user_role']
        ];
    }
}