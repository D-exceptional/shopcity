<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\RequestPlaced;
use App\Queue\Queue;
use App\Models\Wallet;
use App\Listeners\Listener;

class ProcessRequestPlacedDebit extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected Wallet $walletModel,
    ) {}

    public function handle(
        RequestPlaced $event
    ): void {

        // Debit Wallet
        $this->walletModel->debitWallet('wallet_payout', $event->amount, $event->userId);
    }
}