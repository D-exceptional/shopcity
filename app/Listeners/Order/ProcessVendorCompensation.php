<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\OrderCanceled;
use App\Queue\Queue;
use App\Models\Store;
use App\Models\Wallet;
use App\Listeners\Listener;

class ProcessVendorCompensation extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected Store $storeModel,
        protected Wallet $walletModel,
    ) {}

    public function handle(
        OrderCanceled $event
    ): void {

        if (!empty($event->stores)) {

            foreach ($event->stores as $store) {

                // Get store ID
                $storeId = $store['store_id'];

                // Get Vendor ID From Store ID
                $vendorId = $this->storeModel->findUserByStoreId($storeId);
                
                // Compensate Vendor Wallet
                $this->walletModel->creditWallet('wallet_payout', $event->vendorCompensation, $vendorId);
                $this->walletModel->creditWallet('wallet_payout_backup', $event->vendorCompensation, $vendorId);
            }
        }
    }
}