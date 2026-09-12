<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\RequestPlaced;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Support\CurrencyManager;
use App\Models\User;
use App\Listeners\Listener;

class SendUserRequestPlacedEmail extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected CurrencyManager $currencyManager,
        protected User $userModel,
    ) {}

    public function handle(
        RequestPlaced $event
    ): void {

        // Get User Details
        $userData  = $this->getBiodata($event->userId);
        $userName  = $userData['name'];
        $userEmail = $userData['email'];

        // Format Payout Amount
        $processedAmount = $this->currencyManager->format((float) $event->amount); 

        $userEmailMessage = "
            Hi <b>{$userName}</b>, 

            <br> You have successfully placed a withdrawal of <b>{$processedAmount}</b>. 
            <br> A total of <b>{$processedAmount}</b> will be paid into your bank account shortly. 
            <br> We hope to see more sales from your store.
            <br> Have a great day ahead.
        ";

        $this->queue->dispatch(
            SimpleMailJob::class,
            [
                'Withdrawal Placed Successfully',
                $userEmail,
                $userEmailMessage
            ],
            'emails'
        );
    }

    private function getBiodata(
        int $userId
    ): array {

        $userData = $this->userModel->findById($userId);

        return [
            'name'  => $userData['firstname'] . ' ' . $userData['lastname'],
            'email' => $userData['email'],
        ];
    }
}