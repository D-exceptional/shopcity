<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Core\Result;
use App\Events\Checkout\Checkout;
use App\Events\EventDispatcher;
use App\Database\Database;
use App\Models\Cart;
use App\Models\CheckoutPayment;    
use App\Models\Order;
use App\Models\Wallet;

class CheckoutService
{
    private string $currency;
    private string $baseUrl;

    public function __construct(
        protected Result $result, 
        protected EventDispatcher $eventDispatcher,
        protected Database $dbClient,
        protected Cart $cartModel, 
        protected CheckoutPayment $checkoutModel, 
        protected Order $orderModel, 
        protected Wallet $walletModel, 
    ) {
        $this->currency = env('BASE_CURRENCY');  
        $this->baseUrl  = config('app.url');
    }

    public function processCheckout(
        float $subtotal, 
        float $tax, 
        float $discount, 
        float $shipping, 
        float $total, 
        string $address, 
        array $items, 
        array $user
    ): Result { 

        // Extract User Data
        $userId = $user['id'];

        $order = $this->dbClient->transaction(function () use (
            $subtotal,
            $tax,
            $discount,
            $shipping,
            $total,
            $address,
            $items,
            $userId,
        ) {

            // Create Order Records
            $order = $this->orderModel->createOrder(
                $subtotal, 
                $tax, 
                $discount, 
                $shipping, 
                $total, 
                $address, 
                $items, 
                $userId
            );

            // Get Order Data
            $orderId   = $order['id'];
            $orderCode = $order['code'];
            $stores    = $order['stores'];

            // Check Order Status
            if (!$orderId || !$orderCode || !$stores || empty($stores)) {
                throw new \Exception(
                    'Order creation failed'
                );
            }

            // Create Payment Record
            $paymentCreated = $this->checkoutModel->createPayment($orderId, $total, $this->currency, $userId);

            if (!$paymentCreated) {
                throw new \Exception(
                    'Failed to create payment record'
                );
            }

            // Clear Cart
            $cartCleared = $this->cartModel->clear($userId);

            if (!$cartCleared) {
                throw new \Exception(
                    'Failed to clear cart'
                );
            }

            // Deduct Wallet
            $walletDebited = $this->walletModel->debitWallet('wallet_coin', $total, $userId);

            if (!$walletDebited) {
                throw new \Exception(
                    'Failed to debit wallet'
                );
            }

            return $order;
        });
        
        // Get Order Data
        $orderId   = $order['id'];
        $orderCode = $order['code'];
        $stores    = $order['stores'];
        
        $this->eventDispatcher->dispatch(
            new Checkout(
                orderCode: $orderCode,
                user: $user,
                stores: $stores,
            )
        );
       
        // Give Final Response
        return $this->result->success('Your order has been created successfully');
    }
}
