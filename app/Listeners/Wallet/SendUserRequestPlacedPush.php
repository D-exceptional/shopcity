<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\RequestPlaced;
use App\Queue\Queue;
use App\Jobs\PushNotificationJob;
use App\Support\TextManager;
use App\Support\CurrencyManager;
use App\Models\User;
use App\Listeners\Listener;

class SendUserRequestPlacedPush extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected TextManager $textProcessor,
        protected CurrencyManager $currencyManager,
        protected User $userModel,
    ) {}

    public function handle(
        RequestPlaced $event
    ): void {

        // Get User Details
        $userData  = $this->getBiodata($event->userId);
        $userName  = $userData['name'];

        // Format Payout Amount
        $processedAmount = $this->currencyManager->format((float) $event->amount); 

        $message = "
            Hi <b>{$userName}</b>, 

            <br> You have successfully placed a withdrawal of <b>{$processedAmount}</b>. 
            <br> A total of <b>{$processedAmount}</b> will be paid into your bank account shortly. 
            <br> We hope to see more sales from your store.
            <br> Have a great day ahead.
        ";

        $userPushMessage = $this->textProcessor
            ->formatPushMessage($message);

        $this->queue->dispatch(
            PushNotificationJob::class,
            [
                "Single {$userData['role']}",
                $event->userId,
                'Withdrawal Placed Successfully',
                $userPushMessage,
                [
                    'url' => "/auth/user/login",
                    'type' => 'withdrawal'
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