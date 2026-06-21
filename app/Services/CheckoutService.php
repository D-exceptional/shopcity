<?php
namespace App\Services;

use App\Helpers\ResponseManager;
use App\Helpers\MailManager;
use App\Helpers\PushManager;
use App\Helpers\CurrencyManager;
use App\Helpers\RatingManager;
use App\Models\Cart;
use App\Models\Checkout;    
use App\Models\Order;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Store;
use App\Models\Notification;

require_once dirname(__DIR__, 2) . '/bootstrap.php'; // Auto load files

class CheckoutService
{
    protected ResponseManager $response;
    protected MailManager $mailer;
    protected PushManager $push;
    protected CurrencyManager $transaction;
    protected RatingManager $coin;
    protected Cart $cartModel;
    protected Checkout $checkoutModel;
    protected Order $orderModel;
    protected Wallet $walletModel;
    protected User $userModel;
    protected Store $storeModel;
    protected Notification $notificationModel;
    protected string $currency;
    private   float $baseConversionRate;
    private   string $baseUrl;

    public function __construct(
        ResponseManager $response,
        MailManager $mailer,
        PushManager $push,
        CurrencyManager $transaction,
        RatingManager $coin,
        Cart $cartModel, 
        Checkout $checkoutModel, 
        Order $orderModel, 
        Wallet $walletModel, 
        User $userModel, 
        Store $storeModel, 
        Notification $notificationModel
    )
    {
        // Required services for this controller class
        $this->response    = $response;
        $this->mailer      = $mailer;
        $this->push        = $push;
        $this->transaction = $transaction;
        $this->coin        = $coin;
        // Required model for this controller class
        $this->cartModel         = $cartModel;
        $this->checkoutModel     = $checkoutModel;
        $this->orderModel        = $orderModel;
        $this->walletModel       = $walletModel;
        $this->userModel         = $userModel;
        $this->storeModel        = $storeModel;
        $this->notificationModel = $notificationModel;
        // Required currency for the controller class
        $this->currency = $_ENV['BASE_CURRENCY'];  
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

    /** Process checkout */
    public function processCheckout(array $user, array $payload)
    { 
        // Create order, save items and return details
        $order = $this->orderModel->createOrder(
            $user['id'], 
            $payload['subtotal'], 
            $payload['tax'], 
            $payload['discount'], 
            $payload['shipping'], 
            $payload['total'], 
            $payload['address'], 
            $payload['items']
        );
        
        // Get order data
        $orderId = $order['id'];
        $orderCode = $order['code'];
        $stores = $order['stores'];
        $date = date('Y-m-d H:i:s');

        // Check if order was successful
        if (!$orderId || !$orderCode || !$stores) {
            return $this->response->fail('Failed to create your order', 500);
        }

        // Create payment
        $payment = $this->checkoutModel->createPayment($orderId, $user['id'], $payload['total'], $this->currency);
        if ($payment === false) {
           return $this->response->fail('Failed to create payment record', 500);
        }

        // Clear cart
        $this->cartModel->clear($user['id']);

        // Deduct wallet
        $this->walletModel->debitWallet('wallet_coin', $payload['total'], $user['id']);

        // Build customer message
        $userMessage = "
            Hi <b>{$user['name']}</b>, 
            <br> Your order has been received and is currently being processed. 
            <br> You can track this order using the code: <b>{$orderCode}</b> on your order track page.
            <br> We hope to see you shop again soon.
        ";

        // Create in-app notification for customer
        $notification = $this->notificationModel->create($userMessage, 'New Order', $user['id'], $date, 'Unread');
        if ($notification === false) {
            return $this->response->fail('Failed to create notification for customer', 500);
        }
        // Send mail to user
        $mail = [
            'subject' => 'Order Placed',
            'message' => $userMessage,
        ];
        $this->mailer->send($mail['subject'], $user['email'], $mail['message']);
        // Send push notification to user
        $processedMessage = $this->processMessage($userMessage);
        $this->push->send('Single Customer', $user['id'], 'Order Placed', $processedMessage, [
            'url' => "{$this->baseUrl}/track-order",
            'type' => 'order'
        ]);

        // Loop through the stores, notify the vendors and credit their temporary savings wallet
        foreach ($stores as $store) {
            $storeId = $store['store_id'];
            $storeTotal = $store['total'];

            // Commission (90%)
            $vendorCommission = round($storeTotal * 0.90, 2);
            $processedCommission = $this->transaction->format((float)$vendorCommission); // In Naira
            $processedCoinCommission = $this->coin->format((float)$vendorCommission / $this->baseConversionRate) . ' Coins'; // In Coins

            // Get vendor details
            $vendorId = $this->storeModel->findUserByStoreId($storeId);
            $vendorDetails = $this->userModel->findById($vendorId);
            $vendorName = $vendorDetails['firstname'] . ' ' . $vendorDetails['lastname'];
            $vendorEmail = $vendorDetails['email'];

            // Credit vendor savings wallet
            $this->walletModel->creditWallet('wallet_savings', $vendorCommission, $vendorId);

            // Build vendors message
            $vendorMessage = "
                Hi <b>{$vendorName}</b>, 
                <br> You have a new order on your store with an ID: <b>{$orderCode}</b>!
                <br> Your savings wallet has been credited with <b>{$processedCoinCommission}</b> for this order and will be redeemed to your withdrawal wallet at order completion</b>. 
                <br> Thank you for selling on our platform. Keep up the great work!
                <br> We hope to see more sales from your shop.
                <br> Have a great day ahead.
            ";

            // Create in-app notification for vendors
            $notification = $this->notificationModel->create($vendorMessage, 'New Order', $vendorId, $date, 'Unread');
            if ($notification === false) {
                return $this->response->fail('Failed to create notification for vendor', 500);
            }
            // Send vendor notification mail
            $mail = [
                'subject' => 'New Order Notification',
                'message' => $vendorMessage,
            ];
            $this->mailer->send($mail['subject'], $vendorEmail, $mail['message']);
            // Send push notification to vendors
            $processedMessage = $this->processMessage($vendorMessage);
            $this->push->send('Single Vendor', $vendorId, 'New Order', $processedMessage, [
                'url' => "{$this->baseUrl}/seller/",
                'type' => 'order'
            ]);
        }

        // Initialize admin mail message
        $adminMessage =  "
            Hello Admin, 
            <br> A new order, <b>{$orderCode}</b>, has been created!
            <br> Kindly review and take necessary actions. 
        ";

        // Send admin mails
        $admins = $this->userModel->allByRole('Admin');
        foreach ($admins as $admin) {
            $this->mailer->send('New Order', $admin['email'], $adminMessage);
            // Send push notification to admins
            $processedMessage = $this->processMessage($adminMessage);
            $this->push->send('Single Admin', $admin['user_id'], 'New Order', $processedMessage, [
                'url' => "{$this->baseUrl}/admin/",
                'type' => 'order'
            ]);
            
            // Create in-app notification for admins
            $notification = $this->notificationModel->create($adminMessage, 'New Order', $admin['user_id'], $date, 'Unread');
            if ($notification === false) {
                return $this->response->fail('Failed to create notification for admin', 500);
            }
        }
       
        // Give final response
        return $this->response->success('Your order has been created successfully');
    }
}
