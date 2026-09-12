<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Core\Result;
use App\Events\Order\ItemStatusUpdated;
use App\Events\Order\OrderCompleted;
use App\Events\Order\OrderCanceled;
use App\Events\EventDispatcher;
use App\Support\TextManager;
use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use App\Models\Product;

class OrderService
{
    private string $baseUrl;

    public function __construct(
        protected Result $result, 
        protected EventDispatcher $eventDispatcher,
        protected TextManager $textManager, 
        protected Order $orderModel,
        protected Store $storeModel,
        protected User $userModel,
        protected Product $productModel,
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

        // Get Product Details
        $productData = $this->productModel->find($productId);
        $storeId     = $productData['store_id'];

        // Get Order Details
        $orderData = $this->orderModel->getOrderDetails($orderId);

        // Get Customer ID
        $customerId = $this->orderModel->getUserByOrderId($orderId);

        // Get Vendor ID
        $vendorId = $this->storeModel->findUserByStoreId($storeId);

        // Initialize Keys
        $subject = ($status === 'Shipped') 
            ? 'New Shipment Notification' 
            : 'Delivery Confirmation';


        $this->eventDispatcher->dispatch(
            new ItemStatusUpdated(
                itemName: $productData['product_name'],
                itemCode: $itemData['tracking_code'],
                itemQuantity: $itemData['quantity'],
                orderCode: $orderData['tracking_code'],
                orderDate: $orderData['created_at'],
                deliveryDate: date('Y-m-d H:i:s'),
                status: $status,
                subject: $subject,
                customerId: $customerId,
                vendorId: $vendorId
            )
        );

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

        // Get Customer ID
        $customerId = $this->orderModel->getUserByOrderId($orderId);

        // Get Associated Stores
        $stores = $this->orderModel->getOrderStores($orderId);

        $this->eventDispatcher->dispatch(
            new OrderCompleted(
                orderCode: $orderData['tracking_code'],
                customerId: $customerId,
                stores: $stores
            )
        );

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

        // Get Associated Stores
        $stores     = $this->orderModel->getOrderStores($orderId);
        $storeCount = count($stores);

        // Calculate Sharings Of Total Amount Paid
        $customerRefund       = round($orderTotal * 0.80, 2); // 80% of payment
        $sharedCompensation   = round($orderTotal * 0.20, 2); // 20% of payment
        $platformCompensation = $sharedCompensation * 0.10;   // 10% to platform
        $storeCompensation    = $sharedCompensation * 0.90;   // 90% to vendors
        $vendorCompensation   = $storeCompensation / $storeCount;

        $this->eventDispatcher->dispatch(
            new OrderCanceled(
                orderCode: $orderData['tracking_code'],
                customerId: $user['id'],
                customerRefund: $customerRefund,
                stores: $stores,
                vendorCompensation: $vendorCompensation,
            )
        );

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
}
