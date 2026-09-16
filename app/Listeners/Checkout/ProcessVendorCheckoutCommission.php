<?php

declare(strict_types=1);

namespace App\Listeners\Checkout;

use App\Events\Checkout\Checkout;
use App\Queue\Queue;
use App\Models\Store;
use App\Models\Wallet;
use App\Listeners\Listener;

class ProcessVendorCheckoutCommission extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected Store $storeModel,
        protected Wallet $walletModel,
    ) {}

    public function handle(
        Checkout $event
    ): void {

        if (!empty($event->stores)) {

            foreach ($event->stores as $store) {

                $storeId    = $store['store_id'];
                $storeTotal = $store['total'];

                // Commission (90%)
                $vendorCommission = round($storeTotal * 0.90, 2);

                // Get Vendor ID From Store ID
                $vendorId = $this->storeModel->findUserByStoreId($storeId);

                // Credit Vendor Payout Wallet
                $this->walletModel->creditWallet('wallet_payout', $vendorCommission, $vendorId);
            }
        }
    }
}