<?php
namespace App\Services;

use App\Helpers\ResponseManager;
use App\Helpers\MailManager;
use App\Helpers\PushManager;
use App\Helpers\CurrencyManager;
use App\Helpers\RatingManager;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\Store;
use App\Models\User;
use App\Models\Product;
use App\Models\Notification;

require_once dirname(__DIR__, 2) . '/bootstrap.php'; // Auto load files

class OrderService
{
    protected ResponseManager $response;
    protected MailManager $mailer;
    protected PushManager $push;
    protected CurrencyManager $transaction;
    protected RatingManager $coin;
    protected Order $orderModel;
    protected Wallet $walletModel;
    protected Store $storeModel;
    protected User $userModel;
    protected Product $productModel;
    protected Notification $notificationModel;
    private   int $baseConversionRate;
    private   string $baseUrl;

    public function __construct(
        ResponseManager $response,
        MailManager $mailer,
        PushManager $push,
        CurrencyManager $transaction,
        RatingManager $coin,
        Order $orderModel, 
        Wallet $walletModel, 
        Store $storeModel, 
        User $userModel, 
        Product $productModel, 
        Notification $notificationModel
    )
    {
        // Required helpers for this controller class
        $this->response    = $response;
        $this->mailer      = $mailer;
        $this->push        = $push;
        $this->transaction = $transaction;
        $this->coin        = $coin;
        // Required models for this controller class
        $this->orderModel        = $orderModel;
        $this->walletModel       = $walletModel;
        $this->storeModel        = $storeModel;
        $this->userModel         = $userModel;
        $this->productModel      = $productModel;
        $this->notificationModel = $notificationModel;
        // Set base conversion rate
        $this->baseConversionRate = $_ENV['BASE_CONVERSION_RATE']; 
        // Set base URL
        $this->baseUrl = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) ? 'http://localhost/projects/demos/shopcity' : '__remote__site__link__';
    }

    /**
     * Converts HTML message to push-friendly plain text
     *
     * @param string $htmlMessage The HTML content
     * @return string Plain text suitable for push notifications
     */
    private function processMessage(string $htmlMessage): string
    {
        // Replace <br> (and variants) with \n
        $textWithLineBreaks = preg_replace('/<br\s*\/?>/i', "\n", $htmlMessage);

        // Remove all other HTML tags
        $plainText = strip_tags($textWithLineBreaks);

        // Trim extra whitespace at start/end and return
        return trim($plainText);
    }

    /** Get a single order */
    public function trackOrder(int $userId, string $code)
    {  
        $order = $this->orderModel->trackOrder($userId, $code);
        if ($order === false) {
            return $this->response->fail('Order not found', 500);
        }

        // Prepare data
        return $this->response->success('Order fetched', $order);
    }

    /** Get a single order */
    public function getOrder(int $id)
    {
        $order = $this->orderModel->getOrder($id);
        if ($order === false) {
            return $this->response->fail('Failed to fetch order', 500);
        }

        // Prepare data
        return $this->response->success('Order fetched', $order);
    }

    /** Get all orders (admin) */
    public function getAllOrders(int $page)
    { 
        $orders = $this->orderModel->getAllOrders($page);
        if (count($orders['orders']) === 0) {
            return $this->response->fail('No orders to fetch', 200);
        }

        // Prepare data
        return $this->response->success('Orders fetched', $orders);
    }

    /** Get all orders (admin) */
    public function getOrdersByStatus(string $status, int $page)
    {
        $orders = $this->orderModel->getOrdersByStatus($status, $page);
        if (count($orders['orders']) === 0) {
            return $this->response->fail('No orders to fetch', 200);
        }

        // Prepare data
        return $this->response->success('Orders fetched', $orders);
    }

    /** Get all orders for a user */
    public function getUserOrders(int $userId, int $page)
    {
        $orders = $this->orderModel->getUserOrders($userId, $page);
        if (count($orders['orders']) === 0) {
            return $this->response->fail('No orders to fetch', 200);
        }

        // Prepare data
        return $this->response->success('Orders fetched', $orders);
    }

    /** Get all orders for a store */
    public function getStoreOrders(int $storeId, int $page)
    {
        $orders = $this->orderModel->getStoreOrders($storeId, $page);
        if (count($orders['orders']) === 0) {
            return $this->response->fail('No orders to fetch', 200);
        }

        // Prepare data
        return $this->response->success('Orders fetched', $orders);
    }

    /** Get all orders for a store */
    public function getStoreOrdersByStatus(int $storeId, string $status, int $page)
    {
        $orders = $this->orderModel->getStoreOrdersByStatus($storeId, $status, $page);
        if (count($orders['orders']) === 0) {
            return $this->response->fail('No orders to fetch', 200);
        }

        // Prepare data
        return $this->response->success('Orders fetched', $orders);
    }

    /** Update item status */
    public function updateItemStatus(array $payload)
    {
        $updated = $this->orderModel->updateItemStatus($payload['id'], $payload['status']);
        if ($updated === false) {
            return $this->response->fail('Failed to update status', 500);
        }

        // Get item details
        $itemDetails = $this->orderModel->getItemDetails($payload['id']);
        $orderId = $itemDetails['order_id'];
        $productId = $itemDetails['product_id'];

        // Get order details
        $orderDetails = $this->orderModel->getOrderDetails($orderId);
        $orderCode = $orderDetails['tracking_code'];

        // Get product details
        $productDetails = $this->productModel->find($productId);
        $productName = $productDetails['product_name'];
        $storeId = $productDetails['store_id'];

        // Get vendor details
        $vendorId = $this->storeModel->findUserByStoreId($storeId);
        $vendorDetails = $this->userModel->findById($vendorId);
        $vendorName = $vendorDetails['firstname'] . ' ' .  $vendorDetails['lastname'];
        $vendorEmail = $vendorDetails['email'];

        // Get user details
        $userId = $this->orderModel->getUserByOrderId($orderId);
        $userDetails = $this->userModel->findById($userId);
        $userName = $userDetails['firstname'] . ' ' . $userDetails['lastname'];
        $userEmail = $userDetails['email'];

        // Initialize date
        $date = date('Y-m-d H:i:s');

        // Initialize mail subject
        $subject = ($payload['status'] === 'Shipped') ? 'New Shipment Notification' : 'Delivery Confirmation';

        // Initialize user mail message
        $userMessage = ($payload['status'] === 'Shipped')
        ? "Hi <b>{$userName}</b>, 
            <br> Your product, <b>{$productName}</b>, from the order, <b>{$orderCode}</b>, has been shipped!
            <br> When you receive the shipment, login to your dashboard and click on the <b>I have received shipment</b> button beside this product on your order page. 
            <br> Thank you for buying on our platform.
            <br> We hope to see more shopping from you soon.
            <br> Have a great day ahead.
        ":
        "Hi <b>{$userName}</b>, 
            <br> Thank you for confirming the shipment.
            <br> Here are the details of the product: 
            <br> <hr>
            <br>
            Product Name: <b>{$productName}</b><br>
            Product Code:  <b>{$itemDetails['tracking_code']}</b><br>
            Total Quantity:  <b>{$itemDetails['quantity']}</b><br>
            Order Date:  <b>{$orderDetails['created_at']}</b><br>
            Delivery Date:  <b>{$date}</b><br>
            Status:  <b>{$payload['status']}</b><br>
            <br> If you have any complaints or reviews, feel free to reach out to us via support@mrsamase.com
            <br> We hope to see more shopping from you soon.
            <br> Have a great day ahead.
        ";

        // Create in-app notification for user
        $notification = $this->notificationModel->create($userMessage, "Item Update", $userId, $date, 'Unread');
        if ($notification === false) {
            return $this->response->fail('Failed to create notification', 500);
        }
        // Send user mail
        $this->mailer->send($subject, $userEmail, $userMessage);
        // Send push notification to user
        $processedMessage = $this->processMessage($userMessage);
        $this->push->send('Single Customer', $userId, 'Order Status Update', $processedMessage, [
            'url' => "{$this->baseUrl}/track-order",
            'type' => 'order'
        ]);

        // Build message
        $statusAction  = strtolower($payload['status']);
        $messageAction = $statusAction === 'shipped' ? 'shipment' : 'delivery';

        $vendorMessage = "
            Hi <b>{$vendorName}</b>, 
            <br> The product, <b>{$productName}</b>, from your order, <b>{$orderCode}</b>, has been {$statusAction}. 
            <br> You can reach out to our support service for any issues as regards this {$messageAction}.
            <br> We hope to see more sales from your shop.
            <br> Have a great day ahead.
        ";

        // Create in-app notification
        $notification = $this->notificationModel->create($vendorMessage, "Item Update", $vendorId, $date, 'Unread');
        if ($notification === false) {
            return $this->response->fail('Failed to create notification', 500);
        }
        // send vendor mail
        $this->mailer->send("Item {$payload['status']}", $vendorEmail, $vendorMessage);
        // Send push notification to vendors
        $processedMessage = $this->processMessage($vendorMessage);
        $this->push->send('Single Vendor', $vendorId, 'Order Status Update', $processedMessage, [
            'url' => "{$this->baseUrl}/seller/",
            'type' => 'order'
        ]);

        // Initialize admin mail message
        $adminMessage = ($payload['status'] === 'Shipped')
        ? "Hello Admin, 
            <br> A product, <b>{$productName}</b>, from the order, <b>{$orderCode}</b>, has been shipped!
            <br> Kindly review and take necessary actions. 
        ":
        "Hello Admin, 
            <br> A product shipment has been confirmed by a customer.
            <br> Here are the details of the product: 
            <br> <hr>
            <br>
            Product Name: <b>{$productName}</b><br>
            Product Code:  <b>{$itemDetails['tracking_code']}</b><br>
            Total Quantity:  <b>{$itemDetails['quantity']}</b><br>
            Order Date:  <b>{$orderDetails['created_at']}</b><br>
            Delivery Date:  <b>{$date}</b><br>
            Status:  <b>{$payload['status']}</b><br>
            <br> Kindly review and credit the vendor's wallet accordingly
        ";

        // Send admin mails
        $admins = $this->userModel->allByRole('Admin');
        foreach ($admins as $admin) {
            $this->mailer->send($subject, $admin['email'], $adminMessage);
            // Send push notification to admins
            $processedMessage = $this->processMessage($adminMessage);
            $this->push->send('Single Admin', $admin['user_id'], 'Order Status Update', $processedMessage, [
                'url' => "{$this->baseUrl}/admin/",
                'type' => 'order'
            ]);

            $notification = $this->notificationModel->create($adminMessage, "Item Update", $admin['user_id'], $date, 'Unread');
            if ($notification === false) {
                return $this->response->fail('Failed to create notification for admin', 500);
            }
        }

        return $this->response->success('Status updated successfully');
    }

    /** Complete order */
    public function completeOrder(array $payload)
    {
        $completed = $this->orderModel->completeOrder($payload['id']);
        if ($completed === false) {
            return $this->response->fail('Failed to complete order', 500);
        }

        // Get date
        $date = date('Y-m-d H:i:s');

        // Set orderId
        $orderId = $payload['id'];

        // Get order details
        $orderDetails = $this->orderModel->getOrderDetails($orderId);
        $orderCode = $orderDetails['tracking_code'];            

        // Get user details
        $userId = $this->orderModel->getUserByOrderId($orderId);
        $userDetails = $this->userModel->findById($userId);
        $userName = $userDetails['firstname'] . ' ' . $userDetails['lastname'];
        $userEmail = $userDetails['email'];

        // Build customer message
        $userMessage = "
            Hi <b>{$userName}</b>, 
            <br> Your order, <b>{$orderCode}</b> has been completed. 
            <br> You can track this order using the code: <b>{$orderCode}</b> on your order track page.
            <br> We hope to see you shop again soon.
        ";

        // Create in-app notification for customer
        $notification = $this->notificationModel->create($userMessage, 'Order Completion', $userId, $date, 'Unread');
        if ($notification === false) {
            return $this->response->fail('Failed to create notification for customer', 500);
        }
        // Send mail to user
        $customerMail = [
            'subject' => 'Order Completed',
            'message' => $userMessage,
        ];
        $this->mailer->send($customerMail['subject'], $userEmail, $customerMail['message']);
        // Send push notification to user
        $processedMessage = $this->processMessage($userMessage);
        $this->push->send('Single Customer', $userId, 'Order Completed', $processedMessage, [
            'url' => "{$this->baseUrl}/track-order",
            'type' => 'order'
        ]);

        // Get order stores
        $stores = $this->orderModel->getOrderStores($orderId);
        foreach ($stores as $store) {
            $vendorId = $this->storeModel->findUserByStoreId($store['store_id']);
            $vendorDetails = $this->userModel->findById($vendorId);
            // Get details
            $vendorName = $vendorDetails['firstname'] . ' ' .  $vendorDetails['lastname'];
            $vendorEmail = $vendorDetails['email'];

            // Build message
            $vendorMessage = "
                Hi <b>{$vendorName}</b>, 
                <br> The order, <b>{$orderCode}</b> has been completed. 
                <br> You can reach out to our support service for any issues as regards this order.
                <br> We hope to see more sales from your shop.
                <br> Have a great day ahead.
            ";

            // Create in-app notification
            $notification = $this->notificationModel->create($vendorMessage, 'Order Completion', $vendorId, $date, 'Unread');
            if ($notification === false) {
                return $this->response->fail('Failed to create notification', 500);
            }
            // Send mail
            $vendorMail = [
                'subject' => 'Order Completed',
                'message' => $vendorMessage,
            ];
            $this->mailer->send($vendorMail['subject'], $vendorEmail, $vendorMail['message']);
            // Send push notification to vendors
            $processedMessage = $this->processMessage($vendorMessage);
            $this->push->send('Single Vendor', $vendorId, 'Order Completed', $processedMessage, [
                'url' => "{$this->baseUrl}/seller/",
                'type' => 'order'
            ]);
        }

         // Initialize admin mail message
        $adminMessage =  "
            Hello Admin, 
            <br> The order, <b>{$orderCode}</b>, has been completed!
            <br> Kindly review and take necessary actions. 
        ";

        // Send admin mails
        $admins = $this->userModel->allByRole('Admin');
        foreach ($admins as $admin) {
            $this->mailer->send('Order Completed', $admin['email'], $adminMessage);
            // Send push notification to admins
            $processedMessage = $this->processMessage($vendorMessage);
            $this->push->send('Single Admin', $admin['user_id'], 'Order Completed', $processedMessage, [
                'url' => "{$this->baseUrl}/admin/",
                'type' => 'order'
            ]);
            
            $notification = $this->notificationModel->create($adminMessage, 'Order Completion', $admin['user_id'], $date, 'Unread');
            if ($notification === false) {
                return $this->response->fail('Failed to create notification for admin', 500);
            }
        }

        return $this->response->success('Order completed successfully');
    }

    /** Cancel order */
    public function cancelOrder(array $user, array $payload)
    {
        $userId    = $user['id'];
        $userName  = $user['name'];
        $userEmail = $user['email'];

        $cancelled = $this->orderModel->cancelOrder($payload['id']);
        if ($cancelled === false) {
            return $this->response->fail('Failed to cancel order', 500);
        }

        // Get details
        $orderDetails = $this->orderModel->getOrderDetails($payload['id']);
        $orderTotal = $orderDetails['total_amount'];
        $orderCode = $orderDetails['tracking_code'];

        // Calculate percentage sharings of the total
        $compensation = round($orderTotal * 0.20, 2);
        $refund = round($orderTotal * 0.80, 2);
        $processedRefund = $this->transaction->format((float)$refund); // In Naira
        $processedCoinRefund = $this->coin->format((float)$refund / $this->baseConversionRate) . ' Coins'; // In Coins

        // Calculate compensations
        $platformCompensation = $compensation * 0.10;
        $storeCompensation = $compensation * 0.90;

        // Get the stores involved
        $stores = $this->orderModel->getOrderStores($payload['id']);

        // Get number of vendors involved
        $store_count = count($stores);

        // Get total compensation for each vendor
        $vendorCompensation = $storeCompensation / $store_count;
        $processedVendorCompensation = $this->transaction->format((float)$vendorCompensation);
        $processedCoinCompensation = $this->coin->format((float)$vendorCompensation / $this->baseConversionRate) . ' Coins'; // In Coins

        // Refund user
        $this->walletModel->creditWallet('wallet_coin', $refund, $userId);

        // Build customer message
        $userMessage = "
            Hi <b>{$userName}</b>, 
            <br> You have cancelled your order: <b>{$orderCode}</b>. 
            <br> You have been refunded the the sum of: <b>{$processedCoinRefund}</b> to enable you continue with seamless shopping across our marketplace. 
            <br> This is in line with our policy to ensure grievances are settled wholly.
            <br> We hope to see more shopping from you.
            <br> Have a great day ahead.
        ";

        // Create in-app notification for customer
        $notification = $this->notificationModel->create($userMessage, 'Order Cancellation', $userId, $date, 'Unread');
        if ($notification === false) {
            return $this->response->fail('Failed to create notification for customer', 500);
        }
        // Send mail to user
        $customerMail = [
            'subject' => 'Order Cancelled',
            'message' => $userMessage,
        ];
        $this->mailer->send($customerMail['subject'], $userEmail, $customerMail['message']);
        // Send push notification to user
        $processedMessage = $this->processMessage($userMessage);
        $this->push->send('Single Customer', $userId, 'Order Cancelled', $processedMessage, [
            'url' => "{$this->baseUrl}/track-order",
            'type' => 'order'
        ]);

        // Compensate each vendor from the total amount and send them mails
        foreach ($stores as $store) {
            $vendorId = $this->storeModel->findUserByStoreId($store['store_id']);
            $vendorDetails = $this->userModel->findById($vendorId);
            // Get details
            $vendorName = $vendorDetails['firstname'] . ' ' .  $vendorDetails['lastname'];
            $vendorEmail = $vendorDetails['email'];

            // Compensate vendor
            $this->walletModel->creditWallet('wallet_payout', $vendorCompensation, $vendorId);
            $this->walletModel->creditWallet('wallet_payout_backup', $vendorCompensation, $vendorId);

            // Get date
            $date = date('Y-m-d H:i:s');

            // Build message
            $vendorMessage = "
                Hi <b>{$vendorName}</b>, 
                <br> You have been compensated with the the sum of: <b>{$processedCoinCompensation}</b> due to cancellation of the order: <b>{$orderCode}</b>. 
                <br> This is in line with our policy to compensate vendors for any inconveniencies incurred during the order processing phase.
                <br> We hope to see more sales from your shop.
                <br> Have a great day ahead.
            ";

            // Create in-app notification
            $notification = $this->notificationModel->create($vendorMessage, 'Order Cancellation', $vendorId, $date, 'Unread');
            if ($notification === false) {
                return $this->response->fail('Failed to create notification', 500);
            }
            // Send mail
            $vendorMail = [
                'subject' => 'Order Cancelled',
                'message' => $vendorMessage,
            ];
            $this->mailer->send($vendorMail['subject'], $vendorEmail, $vendorMail['message']);
            // Send push notification to vendors
            $processedMessage = $this->processMessage($vendorMessage);
            $this->push->send('Single Vendor', $vendorId, 'Order Cancelled', $processedMessage, [
                'url' => "{$this->baseUrl}/seller/",
                'type' => 'order'
            ]);
        }

        // Initialize admin mail message
        $adminMessage =  "
            Hello Admin, 
            <br> The order, <b>{$orderCode}</b>, has been cancelled!
            <br> Kindly review and take necessary actions. 
        ";

        // Send admin mails
        $admins = $this->userModel->allByRole('Admin');
        foreach ($admins as $admin) {
            $this->mailer->send('Order Cancelled', $admin['email'], $adminMessage);
            // Send push notification to admins
            $processedMessage = $this->processMessage($vendorMessage);
            $this->push->send('Single Admin', $admin['user_id'], 'Order Cancelled', $processedMessage, [
                'url' => "{$this->baseUrl}/admin/",
                'type' => 'order'
            ]);
            
            $notification = $this->notificationModel->create($adminMessage, 'Order Cancellation', $admin['user_id'], $date, 'Unread');
            if ($notification === false) {
                return $this->response->fail('Failed to create notification for admin', 500);
            }
        }

        // Delete order
        // $this->orderModel->deleteOrderItems($payload['id']);
        // $this->orderModel->deleteOrderPayment($payload['id']);
        // $this->orderModel->deleteOrder($payload['id']);
        return $this->response->success('Order cancelled successfully');
    }

    /** Get a single order */
    public function getSalesSummary(string $view, int $userId, $storeId, string $period, string $startDate, string $endDate)
    {  
        // Get stats
        $stats = $this->orderModel->getSalesAndRevenueByPeriod($view, $userId, $storeId, $period, $startDate, $endDate);

        // Send data
        return $this->response->success('Stats fetched', $stats);
    }
}
