<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\PayoutProcessed;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Support\CurrencyManager;
use App\Models\User;
use App\Listeners\Listener;

class SendUserPayoutProcessedEmail extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected CurrencyManager $currencyManager,
        protected User $userModel,
    ) {}

    public function handle(
        PayoutProcessed $event
    ): void {

        // Get User Details
        $userData  = $this->getBiodata($event->userId);
        $userName  = $userData['name'];
        $userEmail = $userData['email'];

        // Format Amount
        $processedPayout = $this->currencyManager->format((float) $event->amount);

        $userEmailMessage = "
            Hi <b>{$userName}</b>, 

            <br> You have received a payout of <b>{$processedPayout}</b> from ShopCity.
            <br> Your transaction reference is: <b>{$event->reference}</b>.
            <br> We hope to see more sales from your stores</b>.
            <br> Have a great day ahead.
        ";

        $this->queue->dispatch(
            SimpleMailJob::class,
            [
                'Payout Request Processed',
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