<?php
namespace App\Services;

use App\Helpers\ResponseManager;
use App\Helpers\MailManager;
use App\Helpers\PushManager;
use App\Helpers\CurrencyManager;
use App\Helpers\RatingManager;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Store;

require_once dirname(__DIR__, 2) . '/bootstrap.php'; // Auto load files

class WalletService 
{
    protected ResponseManager $response;
    protected MailManager $mailer;
    protected PushManager $push;
    protected CurrencyManager $transaction;
    protected RatingManager $coin;
    protected Wallet $walletModel;
    protected User $userModel;
    protected Notification $notificationModel;
    protected Order $orderModel;
    protected Store $storeModel;
    protected string $secretKey;
    protected string $currency;
    private   string $withdrawalTable;
    private   float $baseConversionRate;
    private   string $baseUrl;

    public function __construct(
        ResponseManager $response,
        MailManager $mailer,
        PushManager $push,
        CurrencyManager $transaction,
        RatingManager $coin,
        Wallet $walletModel, 
        User $userModel, 
        Notification $notificationModel, 
        Order $orderModel, 
        Store $storeModel
    )
    {
        // Required services for this controller class
        $this->response    = $response;
        $this->mailer      = $mailer;
        $this->push        = $push;
        $this->transaction = $transaction;
        $this->coin        = $coin;
        // Required models for this controller class
        $this->walletModel       = $walletModel;
        $this->userModel         = $userModel;
        $this->notificationModel = $notificationModel;
        $this->orderModel        = $orderModel;
        $this->storeModel        = $storeModel;
        // Required secret key for this controller class
        $this->secretKey = $_ENV['FLW_SECRET_KEY']; 
        // Required currency for this controller class
        $this->currency  = $_ENV['BASE_CURRENCY'];  
        // Set base conversion rate
        $this->baseConversionRate = $_ENV['BASE_CONVERSION_RATE']; 
        // Set base withdrawal table
        $this->withdrawalTable = 'wallet_payout';  
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

    /** Initialize payment */
    public function updateDetails(int $account, string $bank, string $code, int $userId)
    { 
        $updated = $this->walletModel->updateDetails($account, $bank, $code, $userId);
        if ($updated === false) {
            return $this->response->fail('Failed to update details', 400);
        }
        return $this->response->success('Details updated successfully');
    }

    /** Initialize payment */
    public function createPayment(float $amount, int $userId)
    { 
        // Generate reference
        $reference = $this->walletModel->createPayment($userId, $amount, $this->currency);
        if ($reference === null) {
            return $this->response->fail('Failed to generate reference', 500);
        }

        // Get user details
        $userDetails = $this->userModel->findById($userId);
        $userName    = $userDetails['firstname'] . ' ' . $userDetails['lastname'];
        $userEmail   = $userDetails['email'];
        $userContact = $userDetails['contact'];

        // Prepare data
        $data = [
            'reference' => $reference, 
            'user' => ['name' => $userName, 'email' => $userEmail, 'phone' => $userContact]
        ];
        return $this->response->success('Payment reference generated', $data);
    }

    /** Redeem funds to main wallet */
    public function redeemFunds(array $payload)
    { 
        // Get vendor details
        $vendorId = $this->storeModel->findUserByStoreId($payload['storeId']);
        $vendorDetails = $this->userModel->findById($vendorId);
        $vendorName = $vendorDetails['firstname'] . ' ' .  $vendorDetails['lastname'];
        $vendorEmail = $vendorDetails['email'];

        // Redeem funds
        $funds = $this->walletModel->redeemFunds($vendorId);
        if ($funds === 0) {
           return $this->response->fail('Failed to redeem funds', 400);
        }
        $this->walletModel->creditWallet('wallet_payout', $funds, $vendorId);
        $this->walletModel->creditWallet('wallet_payout_backup', $funds, $vendorId);

        // Update item finalized status
        $this->orderModel->updateItemFinalizedStatus($payload['itemId'], $payload['status']);

        $processedFunds = $this->transaction->format((float)$funds); // In Naira
        $processedCoinFunds = $this->coin->format((float)$funds / $this->baseConversionRate) . ' Coins'; // In Coins

        // Initialize date
        $date = date('Y-m-d H:i:s');

        // Build message
        $vendorMessage = "
            Hi <b>{$vendorName}</b>, 
            <!--<br> You have successfully redeemed <b>{$processedCoinFunds}</b> from your temporary savings wallet.-->
            <br> A total of <b>{$processedCoinFunds}</b> has been credited to your withdrawable wallet. 
            <br> You can proceed to withdraw the funds if you deem necessary.
            <br> We hope to see more sales from store.
            <br> Have a great day ahead.
        ";

        // Create in-app notification for customer
        $notification = $this->notificationModel->create($vendorMessage, 'Fund Redeem', $vendorId, $date, 'Unread');
        if ($notification === false) {
            return $this->response->fail('Failed to create notification for vendor', 500);
        }
        // Send email to user
        $mail = [
            'subject' => 'Funds Redeemed',
            'message' => $vendorMessage,
        ];
        $this->mailer->send($mail['subject'], $vendorEmail, $mail['message']);
        // Send push notification to vendor
        $processedMessage = $this->processMessage($vendorMessage);
        $this->push->send('Single Vendor', $vendorId, 'Funds Redeemed', $processedMessage, [
            'url' => "{$this->baseUrl}/seller/",
            'type' => 'funds'
        ]);

        return $this->response->success('Item completed and funds redeemed successfully');
    }

    /** Request withdrawal */
    public function requestFunds(array $payload, array $user)
    {
        // Get account details
        $userId   = $user['id'];
        $userName = $user['name'];

        $bankDetails = $this->walletModel->getBankDetails($userId);
        $bank = $bankDetails['bank_name'];
        $account = $bankDetails['account_number'];

        // Get current balance
        $balance = $this->walletModel->getBalance($this->withdrawalTable, $userId);
        if ($balance === false) {
           return $this->response->fail('Failed to get balance', 500);
        }

        // Get raw amount
        $rawAmount = $payload['amount'] * $this->baseConversionRate;

        // Prevent wrong withdrawals
        if ($balance === 0 || $rawAmount > $balance) {
            return $this->response->fail('Insufficient balance', 400);
        }

        // Prevent malicious withdrawals
        if ($payload['bank'] !== $bank || $payload['account'] !== (int)$account) {
            return $this->response->fail('Bank details do not match', 400);
        }
        
        // Initialize date
        $date = date('Y-m-d H:i:s');

        // Format response amount
        $processedWithdrawal = $this->transaction->format((float)$rawAmount); // In Naira
        $processedCoinWithdrawal = $this->coin->format((float)$rawAmount / $this->baseConversionRate) . ' Coins'; // In Coins

        // Request funds
        $request = $this->walletModel->requestFunds($rawAmount, $bank, $account, $payload['narration'], $userId);
        if ($request === false) {
           return $this->response->fail('Failed to place withdrawal', 500);
        }

        // Debit wallet
        $this->walletModel->debitWallet($this->withdrawalTable, $rawAmount, $userId);

        // Build message
        $vendorMessage = "
            Hi <b>{$userName}</b>, 
            <br> You have successfully placed a withdrawal of <b>{$processedCoinWithdrawal}</b>. 
            <br> A total of <b>{$processedCoinWithdrawal}</b> will be paid into your bank account shortly. 
            <br> We hope to see more sales from store.
            <br> Have a great day ahead.
        ";

        // Create in-app notification for customer
        $notification = $this->notificationModel->create($vendorMessage, 'Fund Request', $userId, $date, 'Unread');
        if ($notification === false) {
            return $this->response->fail('Failed to create notification for vendor', 500);
        }
        // Send email to user
        $mail = [
            'subject' => 'Withdrawal Initiated',
            'message' => $vendorMessage,
        ];
        $this->mailer->send($mail['subject'], $user['email'], $mail['message']);
        // Send push notification to vendor
        $processedMessage = $this->processMessage($vendorMessage);
        $this->push->send('Single Vendor', $userId, 'Withdrawal Initiated', $processedMessage, [
            'url' => "{$this->baseUrl}/seller/",
            'type' => 'withdrawal'
        ]);

        return $this->response->success('Withdrawal successful');
    }

    /** 🔹 Flutterwave - Single Transfer */
    public function singleTransfer(array $payload)
    {
        $url = "https://api.flutterwave.com/v3/transfers";

        $body = [
            "account_bank"     => $payload['bank'], // Bank code is used (e.g 044)
            "account_number"   => $payload['account'],
            "amount"           => $payload['amount'],
            "narration"        => $payload['narration'],
            "currency"         => $payload['currency'],
            "reference"        => $payload['reference'],
            "debit_currency"   => $this->currency, // NGN by default
            "beneficiary_name" => $payload['name'],
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->secretKey}",
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['status']) && $result['status'] === 'success') {
            // Debit payout wallet after success

            return $this->response->success('Transfer queued successfully', $result);
        } else {
            // Prepare data
            return $this->response->fail('Transfer queue failed', 400, $result);
        }
    }

    /** 🔹 Flutterwave - Bulk Transfer */
    public function bulkTransfer(array $payload)
    {
        $url = "https://api.flutterwave.com/v3/bulk-transfers";

        // Decode bulk_data into PHP format
        $body = [
            "title"     => $payload['title'],
            "bulk_data" => $payload['bulk_data']
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->secretKey}",
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['status']) && $result['status'] === 'success') {
            // Debit vendor payout wallet (sum of all transfers)
            $totalAmount = array_sum(array_column($payload['bulk_data'], 'amount'));

            return $this->response->success('Bulk transfer queued successfully', $result);
        } else {
            // Prepare data
            return $this->response->fail('Bulk transfer queue failed', 400, $result);
        }
    }

    /** Get payment by reference */
    public function getByReference(array $payload)
    {
        // Get details
        $payment = $this->walletModel->getByReference($payload['type'], $payload['reference']);
        if ($payment === false) {
            return $this->response->fail('Failed to fetch payment', 400);
        }
        // Prepare data
        return $this->response->success('Payment fetched', ['payment' => $payment]);
    }

    /** Get payment by user */
    public function getPaymentsByUser(array $payload, int $userId)
    {
        // Fetch payments
        $payments = $this->walletModel->getPaymentsByUser($payload['type'], $userId, $payload['page']);
        if ($payments === false) {
            return $this->response->fail('Failed to fetch payments', 400);
        }
        // Prepare data
        return $this->response->success('Payments fetched', $payments);
    }

    /** Fetch payments by type (withdrawals, payments, topups) -> Admin */
    public function getPaymentsByType(array $payload)
    {
        // Fetch payments
        $payments = $this->walletModel->getPaymentsByType($payload['table'], $payload['page']);
        if ($payments === false) {
            return $this->response->fail('Failed to fetch payments', 400);
        }
        // Prepare data
        return $this->response->success('Payments fetched', ['payments' => $payments]);
    }

    /** Fetch payments by status (Pending, Completed, Failed) -> Admin */
    public function getPaymentsByStatus(array $payload)
    {
        // Fetch payments
        $payments = $this->walletModel->getPaymentsByStatus($payload['table'], $payload['column'], $payload['status'], $payload['page']);
        if ($payments === false) {
            return $this->response->fail('Failed to fetch payments', 400);
        }
        // Prepare data
        return $this->response->success('Payments fetched', ['payments' => $payments]);
    }

    /** Fetch payments by status (Pending, Completed, Failed) -> Admin */
    public function getPayoutsByStatus(array $payload)
    {
        // Fetch payments
        $payments = $this->walletModel->getWithdrawalsByStatus($payload['status'], $payload['page']);
        if (count($payments['payments']) === 0) {
            return $this->response->fail('Failed to fetch payments', 400);
        }
        // Prepare data
        return $this->response->success('Payments fetched', $payments);
    }

    /** Finalize payments via webhook (Pending, Completed, Failed) -> Admin */
    private function decodeReference(string $reference)
    {
        $parts = explode('-', $reference);

        if (count($parts) < 3) {
            return [null, null];
        }

        return [
            'type' => strtoupper($parts[1]),  // WAL or WIT
            'date' => $parts[2]               // optional use
        ];
    }

    /** Finalize transaction (topup or transfer) */
    private function finalizeTransaction(?string $reference = null, ?string $status = null, ?float $amount = null, ?string $denomination = null): bool
    {
        $decoded = $this->decodeReference($reference);
        $type = $decoded['type'];

        if ($type === 'WAL') {
            return $this->finalizeWalletTopup($reference, $status, $amount);
        }

        if ($type === 'WIT') {
            return $this->finalizeWithdrawal($reference, $status);
        }

        return false;
    }

    /** Finalize topup */
    private function finalizeWalletTopup(?string $reference = null, ?string $status = null, ?float $amount = null): bool
    {
        $record = $this->walletModel->getByReference('topups', $reference);
        if (!$record) return false;

        // Already resolved? → ignore duplicate webhook/API calls
        if (in_array($record['status'], ['Completed', 'Failed'])) return true;

        $userId = $record['user_id'];
        $userDetails = $this->userModel->findById($userId);
        $userName = $userDetails['firstname'] . ' ' .  $userDetails['lastname'];
        $userEmail = $userDetails['email'];

        if ($status === 'successful') {
            $amount = $amount ?: $record['amount'];

            // Format values
            $processedCoinAmount = $this->coin->format(
                (float)$amount / $this->baseConversionRate
            ) . ' Coins';

            // Credit wallet
            $this->walletModel->creditWallet('wallet_coin', $amount, $userId);

            // Update DB status
            $this->walletModel->updateStatus('topups', 'reference', $reference, 'Completed');

            // Fetch new balance
            $newBalance = $this->walletModel->getBalance('wallet_coin', $userId);
            $processedCoinBalance = $this->coin->format(
                $newBalance / $this->baseConversionRate
            ) . ' Coins';

            // Send email to user
            $mail = [
                'subject' => 'Wallet Topup',
                'message' => "
                    Hi <b>{$userName}</b>, 
                    <br> You have successfully funded your shopping wallet with <b>{$processedCoinAmount}</b>.
                    <br> Your new wallet balance is <b>{$processedCoinBalance}</b>.
                    <br> Your transaction reference is: <b>{$reference}</b>.
                    <br> We hope to see you shop again soon enough.
                ",
            ];
            $this->mailer->send($mail['subject'], $userEmail, $mail['message']);
            // Send push notification to admins
            $processedMessage = $this->processMessage($mail['message']);
            $this->push->send('Single Customer', $userId, 'Wallet Topup', $processedMessage, [
                'url' => "{$this->baseUrl}/login",
                'type' => 'topup'
            ]);

            return true;
        }

        if ($status === 'failed') {
            $this->walletModel->updateStatus('topups', 'reference', $reference, 'Failed');
            return true;
        }

        // Any other state → pending
        $this->walletModel->updateStatus('topups', 'reference', $reference, ucfirst($status));
        return false;
    }

    /** Finalize transfer */
    private function finalizeWithdrawal(string $reference, string $status): bool
    {
        $record = $this->walletModel->getByReference('withdrawals', $reference);
        if (!$record) return false;

        if (in_array($record['status'], ['Completed', 'Failed'])) return true;

        $userId = $record['user_id'];
        $userDetails = $this->userModel->findById($userId);
        $userName = $userDetails['firstname'] . ' ' .  $userDetails['lastname'];
        $userEmail = $userDetails['email'];

        if ($status === 'successful') {
            $amount = $amount ?: $record['amount'];

            // Format values
            $processedAmount = $this->transaction->format((float)$amount);

            $this->walletModel->updateStatus('withdrawals', 'reference', $reference, 'Completed');

            // Send email to user
            $mail = [
                'subject' => 'Mrsamase Payout',
                'message' => "
                    Hi <b>{$userName}</b>, 
                    <br> You have received a payout of <b>{$processedAmount}</b> from Mrsamase.
                    <br> Your transaction reference is: <b>{$reference}</b>.
                    <br> We hope to see more sales from your stores</b>.
                    <br> Have a great day ahead.
                ",
            ];
            $this->mailer->send($mail['subject'], $userEmail, $mail['message']);
            // Send push notification to vendor
            $processedMessage = $this->processMessage($mail['message']);
            $this->push->send('Single Vendor', $userId, 'Mrsamase Payout', $processedMessage, [
                'url' => "{$this->baseUrl}/seller/",
                'type' => 'payout'
            ]);
            return true;
        }

        if ($status === 'failed') {
            // Refund user
            // $this->walletModel->creditWallet('wallet_coin', $record['amount'], $userId);
            $this->walletModel->updateStatus('withdrawals', 'reference', $reference, 'Failed');
            return true;
        }

        $this->walletModel->updateStatus('withdrawals', 'reference', $reference, ucfirst($status));
        return false;
    }

    /** Verify payment or transfer with Flutterwave */
    public function verifyPayment(array $payload)
    {
        // Extract data
        $id = $payload['id'];
        $reference = $payload['reference'];

        // Call Flutterwave
        $url = "https://api.flutterwave.com/v3/transactions/{$id}/verify";
        $response = curl_exec(curl_init($url));
        $result = json_decode($response, true);

        // If no conclusive outcome → pending → wait for webhook
        if (!isset($result['data']['status'])) {
            return $this->response->success("We are waiting for payment confirmation", ['pending' => true]);
        }

        $status = strtolower($result['data']['status']);
        $amount = $result['data']['amount'] ?? null;
        $denomination = $result['data']['currency'] ?? null;

        // Finalize
        $done = $this->finalizeTransaction($reference, $status, $amount, $denomination);

        if ($done) {
            return $this->response->success("Transaction processed", ['status' => $status]);
        }

        return $this->response->success("Transaction pending, awaiting webhook...");
    }
}
