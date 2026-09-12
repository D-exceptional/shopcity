<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Core\Result;
use App\Events\Checkout\Checkout;
use App\Events\EventDispatcher;
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
        protected Cart $cartModel, 
        protected CheckoutPayment $checkoutModel, 
        protected Order $orderModel, 
        protected Wallet $walletModel, 
    ) {
        $this->currency = env('BASE_CURRENCY');  
        $this->baseUrl  = $appUrl;
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
        if (!$orderId || !$orderCode || !$stores) {
            return $this->result->error('Failed to create your order', 500);
        }

        // Create Payment Record
        $payment = $this->checkoutModel->createPayment($orderId, $total, $this->currency, $userId);
        if ($payment === false) {
           return $this->result->error('Failed to create payment record', 500);
        }

        // Clear Cart
        $this->cartModel->clear($userId);

        // Deduct Wallet
        $this->walletModel->debitWallet('wallet_coin', $total, $userId);

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
