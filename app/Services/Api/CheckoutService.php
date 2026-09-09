<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Core\Result;
use App\Support\TextManager;
use App\Mail\MailManager;
use App\Notification\PushManager;
use App\Support\CurrencyManager;
use App\Models\Cart;
use App\Models\Checkout;    
use App\Models\Order;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Store;
use App\Models\Notification;

class CheckoutService
{
    private string $currency;
    private string $baseUrl;

    public function __construct(
        protected Result $result, 
        protected TextManager $textManager, 
        protected MailManager $mailManager,
        protected PushManager $pushManager,
        protected CurrencyManager $currencyManager,
        protected Cart $cartModel, 
        protected Checkout $checkoutModel, 
        protected Order $orderModel, 
        protected Wallet $walletModel, 
        protected User $userModel, 
        protected Store $storeModel, 
        protected Notification $notificationModel
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
        $userId    = $user['id'];
        $userName  = $user['name'];
        $userEmail = $user['email'];

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
        $date      = date('Y-m-d H:i:s');

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

        // Build Customer Message
        $customerEmailMessage = "
            Hi <b>{$userName}</b>,

            <br> Your order has been received and is currently being processed. 
            <br> You can track this order using the code: <b>{$orderCode}</b> on your order track page.
            <br> We hope to see you shop again soon.
        ";

        // Create In-App Customer Notification
        $notification = $this->notificationModel->create($customerEmailMessage, 'New Order', $userId);
        if ($notification === false) {
            return $this->result->error('Failed to create notification for customer', 500);
        }

        // Send Customer Email
        $this->mailManager->sendSimpleMail('Order Placed', $userEmail, $customerEmailMessage);

        // Send Customer Push Notification
        $customerPushMessage = $this->textManager->formatPushMessage($customerEmailMessage);

        $this->pushManager->send('Single Customer', $userId, 'Order Placed', $customerPushMessage, [
            'url' => "{$this->baseUrl}/track-order",
            'type' => 'order'
        ]);


        /*
            Loop through the stores, 
            Notify the vendors and 
            Credit their temporary savings wallet
        */
        foreach ($stores as $store) {

            $storeId    = $store['store_id'];
            $storeTotal = $store['total'];

            // Commission (90%)
            $vendorCommission    = round($storeTotal * 0.90, 2);
            $processedCommission = $this->currencyManager->format((float) $vendorCommission); 

            // Get Vendor Details
            $vendorId    = $this->storeModel->findUserByStoreId($storeId);
            $vendorData  = $this->getBiodata($vendorId);
            $vendorName  = $vendorData['name'];
            $vendorEmail = $vendorData['email'];

            // Credit Vendor Savings Wallet
            $this->walletModel->creditWallet('wallet_savings', $vendorCommission, $vendorId);

            // Build Vendors Message
            $vendorEmailMessage = "
                Hi <b>{$vendorName}</b>,

                <br> You have a new order on your store with an ID: <b>{$orderCode}</b>!
                <br> Your savings wallet has been credited with <b>{$processedCommission}</b> for this order and will be redeemed to your withdrawal wallet at order completion</b>. 
                <br> Thank you for selling on our platform. Keep up the great work!
                <br> We hope to see more sales from your shop.
                <br> Have a great day ahead.
            ";

            // Create In-App Vendor Notification
            $notification = $this->notificationModel->create($vendorEmailMessage, 'New Order', $vendorId);
            if ($notification === false) {
                return $this->result->error('Failed to create notification for vendor', 500);
            }

            // Send Vendor Email
            $this->mailManager->sendSimpleMail('New Order Notification', $vendorEmail, $vendorEmailMessage);

            // Send Vendor Push Notification
            $vendorPushMessage = $this->textManager->formatPushMessage($vendorEmailMessage);

            $this->pushManager->send('Single Vendor', $vendorId, 'New Order', $vendorPushMessage, [
                'url' => "{$this->baseUrl}/seller/",
                'type' => 'order'
            ]);
        }
        

        /*
            Loop through the admins, 
            Send emails,
            Send push notifications
            Create in-app notifications
        */

        // Build Admin Message
        $adminEmailMessage =  "
            Hello Admin, 

            <br> A new order, <b>{$orderCode}</b>, has been created!
            <br> Kindly review and take necessary actions. 
        ";

        $admins = $this->userModel->allByRole('Admin');
        foreach ($admins as $admin) {

            // Create In-App Admin Notification
            $notification = $this->notificationModel->create($adminEmailMessage, 'New Order', $admin['user_id']);
            if ($notification === false) {
                return $this->result->error('Failed to create notification for admin', 500);
            }

            // Send Admin Mails
            $this->mailManager->sendSimpleMail('New Order', $admin['email'], $adminEmailMessage);

            // Send Admin Push Notification
            $adminPushMessage = $this->textManager->formatPushMessage($adminEmailMessage);

            $this->pushManager->send('Single Admin', $admin['user_id'], 'New Order', $adminPushMessage, [
                'url' => "{$this->baseUrl}/admin/",
                'type' => 'order'
            ]);
        }
       
        // Give Final Response
        return $this->result->success('Your order has been created successfully');
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
