<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\OrderCanceled;
use App\Queue\Queue;
use App\Models\Wallet;
use App\Listeners\Listener;

class ProcessCustomerRefund extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected Wallet $walletModel,
    ) {}

    public function handle(
        OrderCanceled $event
    ): void {

        // Refund User
        $this->walletModel->creditWallet('wallet_coin', $event->customerRefund, $event->customerId);
    }
}