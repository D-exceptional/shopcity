<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\TopupProcessed;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Support\CurrencyManager;
use App\Models\User;
use App\Listeners\Listener;

class SendUserTopupProcessedEmail extends Listener
{
    public function __construct(
        protected Queue $queue,
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

        // Format Amount
        $fundedAmount = $this->currencyManager->format((float) $event->amount);
        $newBalance   = $this->currencyManager->format((float) $event->balance);

        $userEmailMessage = "
            Hi <b>{$userName}</b>, 

            <br> You have successfully funded your shopping wallet with <b>{$fundedAmount}</b>.
            <br> Your new wallet balance is <b>{$newBalance}</b>.
            <br> Your transaction reference is: <b>{$event->reference}</b>.
            <br> We hope to see you shop again soon enough.
        ";

        $this->queue->dispatch(
            SimpleMailJob::class,
            [
                'Wallet Topup Successful',
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