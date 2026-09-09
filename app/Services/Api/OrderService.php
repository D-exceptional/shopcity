<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Core\Result;
use App\Support\TextManager;
use App\Support\CurrencyManager;
use App\Mail\MailManager;
use App\Notification\PushManager;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\Store;
use App\Models\User;
use App\Models\Product;
use App\Models\Notification;

class OrderService
{
    private string $baseUrl;

    public function __construct(
        protected Result $result, 
        protected TextManager $textManager, 
        protected CurrencyManager $currencyManager,
        protected MailManager $mailManager,
        protected PushManager $pushManager,
        protected Order $orderModel,
        protected Wallet $walletModel,
        protected Store $storeModel,
        protected User $userModel,
        protected Product $productModel,
        protected Notification $notificationModel,
    ) {
        $this->baseUrl = $appUrl; 
    }

    public function trackOrder(
        int $userId, 
        string $code
    ): Result {  

        $order = $this->orderModel->trackOrder($userId, $code);
        if ($order === false) {
            return $this->result->error('Order not found', 500);
        }

        return $this->result->success('Order fetched', $order);
    }

    public function getOrder(
        int $orderId
    ): Result {

        $order = $this->orderModel->getOrder($orderId);
        if ($order === false) {
            return $this->result->error('Failed to fetch order', 500);
        }

        return $this->result->success('Order fetched', $order);
    }

    public function getAllOrders(
        int $page
    ): Result { 

        $orders = $this->orderModel->getAllOrders($page);
        if (count($orders['orders']) === 0) {
            return $this->result->error('No orders to fetch', 200);
        }

        return $this->result->success('Orders fetched', $orders);
    }

    public function getOrdersByStatus(
        string $status, 
        int $page
    ): Result {

        $orders = $this->orderModel->getOrdersByStatus($status, $page);
        if (count($orders['orders']) === 0) {
            return $this->result->error('No orders to fetch', 200);
        }

        return $this->result->success('Orders fetched', $orders);
    }

    public function getUserOrders(
        int $userId, 
        int $page
    ): Result {

        $orders = $this->orderModel->getUserOrders($userId, $page);
        if (count($orders['orders']) === 0) {
            return $this->result->error('No orders to fetch', 200);
        }

        return $this->result->success('Orders fetched', $orders);
    }

    public function getStoreOrders(
        int $storeId, 
        int $page
    ): Result {

        $orders = $this->orderModel->getStoreOrders($storeId, $page);
        if (count($orders['orders']) === 0) {
            return $this->result->error('No orders to fetch', 200);
        }

        return $this->result->success('Orders fetched', $orders);
    }

    public function getStoreOrdersByStatus(
        int $storeId, 
        string $status, 
        int $page
    ): Result {

        $orders = $this->orderModel->getStoreOrdersByStatus($storeId, $status, $page);
        if (count($orders['orders']) === 0) {
            return $this->result->error('No orders to fetch', 200);
        }

        return $this->result->success('Orders fetched', $orders);
    }

    public function updateItemStatus(
        int $itemId, 
        string $status
    ): Result {

        $updated = $this->orderModel->updateItemStatus($itemId, $status);
        if ($updated === false) {
            return $this->result->error('Failed to update status', 500);
        }

        // Get Item Details
        $itemData  = $this->orderModel->getItemDetails($itemId);
        $orderId   = $itemData['order_id'];
        $productId = $itemData['product_id'];

        // Get Order Details
        $orderData = $this->orderModel->getOrderDetails($orderId);
        $orderCode = $orderData['tracking_code'];

        // Get Product Details
        $productData = $this->productModel->find($productId);
        $productName = $productData['product_name'];
        $storeId     = $productData['store_id'];

        // Get Vendor Details
        $vendorId    = $this->storeModel->findUserByStoreId($storeId);
        $vendorData  = $this->getBiodata($vendorId);
        $vendorName  = $vendorData['name'];
        $vendorEmail = $vendorData['email'];

        // Get Customer Details
        $customerId    = $this->orderModel->getUserByOrderId($orderId);
        $customerData  = $this->getBiodata($customerId);
        $customerName  = $customerData['name'];
        $customerEmail = $customerData['email'];

        // Initialize Keys
        $currentDate     = date('Y-m-d H:i:s');
        $statusProcessed = strtolower($status);
        $statusAction    = $statusProcessed === 'shipped' ? 'shipment' : 'delivery';
        $subject         = ($status === 'Shipped') ? 'New Shipment Notification' : 'Delivery Confirmation';

        // Build Customer Message
        $customerMessage = ($status === 'Shipped')
        ? "
            Hi <b>{$customerName}</b>, 

            <br> Your product, <b>{$productName}</b>, from the order, <b>{$orderCode}</b>, has been shipped!
            <br> When you receive the shipment, login to your dashboard and click on the <b>I have received shipment</b> button beside this product on your order page. 
            <br> Thank you for buying on our platform.
            <br> We hope to see more shopping from you soon.
            <br> Have a great day ahead.
        " :
        "
            Hi <b>{$customerName}</b>, 

            <br> Thank you for confirming the shipment.
            <br> Here are the details of the product: 
            <br> 
            <hr>
            <br>
            
            <center>
                Product Name:    <b>{$productName}</b><br>
                Product Code:    <b>{$itemData['tracking_code']}</b><br>
                Total Quantity:  <b>{$itemData['quantity']}</b><br>
                Order Date:      <b>{$orderData['created_at']}</b><br>
                Delivery Date:   <b>{$currentDate}</b><br>
                Status:          <b>{$status}</b><br>
            </center>

            <br> If you have any complaints or reviews, feel free to reach out to us via support@shopcity.com
            <br> We hope to see more shopping from you soon.
            <br> Have a great day ahead.
        ";

        // Create In-App Customer Notification
        $notification = $this->notificationModel->create($customerMessage, "Item Update", $customerId);
        if ($notification === false) {
            return $this->result->error('Failed to create notification', 500);
        }

        // Send Customer Email
        $this->mailManager->sendSimpleMail($subject, $customerEmail, $customerMessage);

        // Send Customer Push Notification
        $customerPushMessage = $this->textManager->formatPushMessage($customerMessage);

        $this->pushManager->send('Single Customer', $customerId, 'Order Status Update', $customerPushMessage, [
            'url' => "{$this->baseUrl}/track-order",
            'type' => 'order'
        ]);

        /*
            Get the vendor involved, 
            Notify the vendor via email
            Notify the vendor via push if available
        */

        // Build Vendor Message
        $vendorMessage = "
            Hi <b>{$vendorName}</b>, 

            <br> The product, <b>{$productName}</b>, from your order, <b>{$orderCode}</b>, has been {$statusProcessed}. 
            <br> You can reach out to our support service for any issues as regards this {$statusAction}.
            <br> We hope to see more sales from your shop.
            <br> Have a great day ahead.
        ";

        // Create In-App Vendor Notification
        $notification = $this->notificationModel->create($vendorMessage, "Item Update", $vendorId);
        if ($notification === false) {
            return $this->result->error('Failed to create notification', 500);
        }

        // Send Vendor Email
        $this->mailManager->sendSimpleMail("Item {$status}", $vendorEmail, $vendorMessage);

        // Send Vendor Push Notification
        $vendorPushMessage = $this->textManager->formatPushMessage($vendorMessage);

        $this->pushManager->send('Single Vendor', $vendorId, 'Order Status Update', $vendorPushMessage, [
            'url' => "{$this->baseUrl}/seller/",
            'type' => 'order'
        ]);

        /*
            Loop through the admins, 
            Notify them via email
            Notify them via push if available
        */

        // Build Admin Message
        $adminMessage = ($status === 'Shipped')
        ? "
            Hello Admin, 

            <br> A product, <b>{$productName}</b>, from the order, <b>{$orderCode}</b>, has been shipped!
            <br> Kindly review and take necessary actions. 
        " :
        "   
            Hello Admin, 

            <br> A product shipment has been confirmed by a customer.
            <br> Here are the details of the product: 
            <br> 
            <hr>
            <br>

            <center>
                Product Name:    <b>{$productName}</b><br>
                Product Code:    <b>{$itemData['tracking_code']}</b><br>
                Total Quantity:  <b>{$itemData['quantity']}</b><br>
                Order Date:      <b>{$orderData['created_at']}</b><br>
                Delivery Date:   <b>{$currentDate}</b><br>
                Status:          <b>{$status}</b><br>
            </center>

            <br> Kindly review and credit the vendor's wallet accordingly
        ";

        // Process Admin Notifications
        $admins = $this->userModel->allByRole('Admin');
        foreach ($admins as $admin) {

            // Create In-App Admin Notification
            $notification = $this->notificationModel->create($adminMessage, "Item Update", $admin['user_id']);
            if ($notification === false) {
                return $this->result->error('Failed to create notification for admin', 500);
            }

            // Send Admin Email
            $this->mailManager->sendSimpleMail($subject, $admin['email'], $adminMessage);

            // Send Admin Push Notification
            $adminPushMessage = $this->textManager->formatPushMessage($adminMessage);

            $this->pushManager->send('Single Admin', $admin['user_id'], 'Order Status Update', $adminPushMessage, [
                'url' => "{$this->baseUrl}/admin/",
                'type' => 'order'
            ]);
        }

        return $this->result->success('Status updated successfully');
    }

    public function completeOrder(
        int $orderId
    ): Result {

        $completed = $this->orderModel->completeOrder($orderId);
        if ($completed === false) {
            return $this->result->error('Failed to complete order', 500);
        }

        // Get Order Details
        $orderData = $this->orderModel->getOrderDetails($orderId);
        $orderCode = $orderData['tracking_code'];            

        // Get Customer Details
        $customerId    = $this->orderModel->getUserByOrderId($orderId);
        $customerData  = $this->getBiodata($customerId);
        $customerName  = $customerData['name'];
        $customerEmail = $customerData['email'];

        // Build Customer Message
        $customerMessage = "
            Hi <b>{$customerName}</b>, 

            <br> Your order, <b>{$orderCode}</b> has been completed. 
            <br> You can track this order using the code: <b>{$orderCode}</b> on your order track page.
            <br> We hope to see you shop again soon.
        ";

        // Create In-App Customer Notification
        $notification = $this->notificationModel->create($customerMessage, 'Order Completion', $customerId);
        if ($notification === false) {
            return $this->result->error('Failed to create notification for customer', 500);
        }

        // Send Customer Email
        $this->mailer->send('Order Completed', $customerEmail, $customerMessage);

        // Send Customer Push Notification
        $customerPushMessage = $this->textManager->formatPushMessage($customerMessage);

        $this->pushManager->send('Single Customer', $customerId, 'Order Completed', $customerPushMessage, [
            'url' => "{$this->baseUrl}/track-order",
            'type' => 'order'
        ]);

        /*
            Loop through the stores, 
            Notify the vendors and 
            Credit their withdrawal wallet
        */

        // Get Order Stores
        $stores = $this->orderModel->getOrderStores($orderId);
        foreach ($stores as $store) {

            $vendorId    = $this->storeModel->findUserByStoreId($store['store_id']);
            $vendorData  = $this->getBiodata($vendorId);
            $vendorName  = $vendorData['name'];
            $vendorEmail = $vendorData['email'];

            // Build Vendor Message
            $vendorMessage = "
                Hi <b>{$vendorName}</b>, 

                <br> The order, <b>{$orderCode}</b> has been completed. 
                <br> You can reach out to our support service for any issues as regards this order.
                <br> We hope to see more sales from your shop.
                <br> Have a great day ahead.
            ";

            // Create In-App Vendor Notification
            $notification = $this->notificationModel->create($vendorMessage, 'Order Completion', $vendorId);
            if ($notification === false) {
                return $this->result->error('Failed to create notification', 500);
            }

            // Send Vendor Email
            $this->mailManager->send('Order Completed', $vendorEmail, $vendorMessage);

            // Send Vendor Push Notification
            $vendorPushMessage = $this->textManager->formatPushMessage($vendorMessage);

            $this->pushManager->send('Single Vendor', $vendorId, 'Order Completed', $vendorPushMessage, [
                'url' => "{$this->baseUrl}/seller/",
                'type' => 'order'
            ]);
        }

        /*
            Loop through the admins, 
            Notify them via email
            Notify them via push if available
        */

        // Build Admin Message
        $adminMessage =  "
            Hello Admin,

            <br> The order, <b>{$orderCode}</b>, has been completed!
            <br> Kindly review and take necessary actions. 
        ";

        // Process Admin Notifications
        $admins = $this->userModel->allByRole('Admin');
        foreach ($admins as $admin) {

            // Create In-App Admin Notification
            $notification = $this->notificationModel->create($adminMessage, 'Order Completion', $admin['user_id']);
            if ($notification === false) {
                return $this->result->error('Failed to create notification for admin', 500);
            }

            // Send Admin Email
            $this->mailManager->send('Order Completed', $admin['email'], $adminMessage);

            // Send Admin Push Notification
            $adminPushMessage = $this->textManager->formatPushMessage($adminMessage);

            $this->pushManager->send('Single Admin', $admin['user_id'], 'Order Completed', $adminPushMessage, [
                'url' => "{$this->baseUrl}/admin/",
                'type' => 'order'
            ]);
        }

        return $this->result->success('Order completed successfully');
    }

    public function cancelOrder(
        array $user, 
        int $orderId
    ): Result {

        $cancelled = $this->orderModel->cancelOrder($orderId);
        if ($cancelled === false) {
            return $this->result->error('Failed to cancel order', 500);
        }

        // Get Order Details
        $orderData  = $this->orderModel->getOrderDetails($orderId);
        $orderTotal = $orderData['total_amount'];
        $orderCode  = $orderData['tracking_code'];

        // Calculate Sharings Of Total Amount Paid
        $sharedCompensation   = round($orderTotal * 0.20, 2); // 20% of payment
        $customerCompensation = round($orderTotal * 0.80, 2); // 80% of payment
        $platformCompensation = $sharedCompensation * 0.10;   // 10% to platform
        $storeCompensation    = $sharedCompensation * 0.90;   // 90% to vendors
        $customerRefund       = $this->currencyManager->format((float) $customerCompensation); 

        /*
            Process customer refund
            Notify them via email
            Notify them via push if available
        */

        $customerId    = $user['id'];
        $customerName  = $user['name'];
        $customerEmail = $user['email'];

        // Refund User
        $this->walletModel->creditWallet('wallet_coin', $customerCompensation, $customerId);

        // Build Customer Message
        $customerMessage = "
            Hi <b>{$customerName}</b>, 

            <br> You have cancelled your order: <b>{$orderCode}</b>. 
            <br> You have been refunded the the sum of: <b>{$customerRefund}</b> to enable you continue with seamless shopping across our marketplace. 
            <br> This is in line with our policy to ensure grievances are settled wholly.
            <br> We hope to see more shopping from you.
            <br> Have a great day ahead.
        ";

        // Create In-App Customer Notification
        $notification = $this->notificationModel->create($customerMessage, 'Order Cancellation', $customerId);
        if ($notification === false) {
            return $this->result->error('Failed to create notification for customer', 500);
        }

        // Send Customer Email
        $this->mailManager->sendSimpleMail('Order Cancelled', $userEmail, $customerMessage);

        // Send Customer Push Notification
        $customerPushMessage = $this->textManager->formatPushMessage($customerMessage);

        $this->pushManager->send('Single Customer', $customerId, 'Order Cancelled', $customerPushMessage, [
            'url' => "{$this->baseUrl}/track-order",
            'type' => 'order'
        ]);

        /*
            Get the vendors involved, 
            Split the total vendor commission amongst them,
            Notify each vendor via email
            Notify each vendor via push if available
        */

        // Get Associated Stores
        $stores = $this->orderModel->getOrderStores($orderId);

        $storeCount = count($stores);

        // Split Vendor Commission
        $vendorCompensation          = $storeCompensation / $storeCount;
        $processedVendorCompensation = $this->currencyManager->format((float) $vendorCompensation);

        foreach ($stores as $store) {
            $storeId = $store['store_id'];

            $vendorId    = $this->storeModel->findUserByStoreId($storeId);
            $vendorData  = $this->getBiodata($vendorId);
            $vendorName  = $vendorData['name'];
            $vendorEmail = $vendorData['email'];

            // Compensate Vendor Wallet
            $this->walletModel->creditWallet('wallet_payout', $vendorCompensation, $vendorId);
            $this->walletModel->creditWallet('wallet_payout_backup', $vendorCompensation, $vendorId);

            // Build Vendor Message
            $vendorMessage = "
                Hi <b>{$vendorName}</b>, 

                <br> You have been compensated with the the sum of: <b>{$processedVendorCompensation}</b> due to cancellation of the order: <b>{$orderCode}</b>. 
                <br> This is in line with our policy to compensate vendors for any inconveniencies incurred during the order processing phase.
                <br> We hope to see more sales from your shop.
                <br> Have a great day ahead.
            ";

            // Create In-App Vendor Notification
            $notification = $this->notificationModel->create($vendorMessage, 'Order Cancellation', $vendorId);
            if ($notification === false) {
                return $this->result->error('Failed to create notification', 500);
            }

            // Send Vendor Email
            $this->mailManager->send('Order Cancelled', $vendorEmail, $vendorMessage);

            // Send Vendor Push Notification
            $vendorPushMessage = $this->textManager->formatPushMessage($vendorMessage);

            $this->pushManager->send('Single Vendor', $vendorId, 'Order Cancelled', $vendorPushMessage, [
                'url' => "{$this->baseUrl}/seller/",
                'type' => 'order'
            ]);
        }

        /*
            Loop through the admins, 
            Notify each admin via email
            Notify each admin via push if available
        */

        // Build Admin Message
        $adminMessage =  "
            Hello Admin, 

            <br> The order, <b>{$orderCode}</b>, has been cancelled!
            <br> Kindly review and take necessary actions. 
        ";

        $admins = $this->userModel->allByRole('Admin');
        foreach ($admins as $admin) {

            // Create In-App Admin Notification
            $notification = $this->notificationModel->create($adminMessage, 'Order Cancellation', $admin['user_id']);
            if ($notification === false) {
                return $this->result->error('Failed to create notification for admin', 500);
            }

            // Send Admin Email
            $this->mailManager->send('Order Cancelled', $admin['email'], $adminMessage);

            // Send Admin Push Notification
            $adminPushMessage = $this->textManager->formatPushMessage($adminMessage);

            $this->push->send('Single Admin', $admin['user_id'], 'Order Cancelled', $adminPushMessage, [
                'url' => "{$this->baseUrl}/admin/",
                'type' => 'order'
            ]);
        }

        // Delete Order Information
        // $this->orderModel->deleteOrderItems($orderId);
        // $this->orderModel->deleteOrderPayment($orderId);
        // $this->orderModel->deleteOrder($orderId);

        return $this->result->success('Order cancelled successfully');
    }

    public function getSalesSummary(
        string $view, 
        int $userId, 
        $storeId, 
        string $period, 
        string $startDate, 
        string $endDate
    ): Result {  

        $stats = $this->orderModel->getSalesAndRevenueByPeriod($view, $userId, $storeId, $period, $startDate, $endDate);

        return $this->result->success('Stats fetched', $stats);
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
