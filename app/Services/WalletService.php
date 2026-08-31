<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Result;
use App\Support\TextManager;
use App\Mail\MailManager;
use App\Notification\PushManager;
use App\Support\CurrencyManager;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Store;

class WalletService 
{
    private string $secretKey;
    private string $currency;
    private string $withdrawalTable;
    private string $baseUrl;

    public function __construct(
        protected Result $result, 
        protected TextManager $textManager, 
        protected MailManager $mailManager,
        protected PushManager $pushManager,
        protected CurrencyManager $currencyManager,
        protected Wallet $walletModel, 
        protected User $userModel, 
        protected Notification $notificationModel, 
        protected Order $orderModel, 
        protected Store $storeModel
    ) {
        $this->secretKey       = env('FLW_SECRET_KEY'); 
        $this->currency        = env('BASE_CURRENCY');  
        $this->withdrawalTable = 'wallet_payout';  
        $this->baseUrl         = config('app.base_path', '/projects/showcase/shopcity'); 
    }

    public function updateDetails(
        int $account, 
        string $bank, 
        string $code, 
        int $userId
    ): Result { 

        $updated = $this->walletModel->updateDetails($account, $bank, $code, $userId);
        if ($updated === false) {
            return $this->result->error('Failed to update details', 400);
        }

        return $this->result->success('Details updated successfully');
    }

    public function createPayment(
        float $amount, 
        int $userId
    ): Result { 

        // Generate Reference
        $reference = $this->walletModel->createPayment($amount, $this->currency, $userId);
        if ($reference === null) {
            return $this->result->error('Failed to generate reference', 500);
        }

        // Get User Data
        $userData = $this->getBiodata($userId);
        $userName    = $userData['name'];
        $userEmail   = $userData['email'];
        $userContact = $userData['phone'];

        // Payment Data
        $paymentData = [
            'reference' => $reference, 
            'user' => [
                'name'  => $userName, 
                'email' => $userEmail, 
                'phone' => $userContact
            ]
        ];

        return $this->result->success('Payment reference generated', $paymentData);
    }

    public function redeemFunds(
        int $itemId, 
        int $storeId, 
        string $status
    ): Result { 

        // Get Vendor Data
        $vendorId    = $this->storeModel->findUserByStoreId($storeId);
        $vendorData  = $this->getBiodata($vendorId);
        $vendorName  = $vendorData['name'];
        $vendorEmail = $vendorData['email'];

        // Redeem Funds
        $pendingFunds = $this->walletModel->redeemFunds($vendorId);
        if (!$pendingFunds || $pendingFunds === 0) {
           return $this->result->error('Failed to redeem funds', 400);
        }

        $remittedFunds = $this->currencyManager->format((float) $pendingFunds);

        // Credit Wallets
        $this->walletModel->creditWallet('wallet_payout', $pendingFunds, $vendorId);
        $this->walletModel->creditWallet('wallet_payout_backup', $pendingFunds, $vendorId);

        // Update Item Status
        $this->orderModel->updateItemFinalizedStatus($itemId, $status);

        // Build Vendor Message
        $vendorMessage = "
            Hi <b>{$vendorName}</b>, 

            <br> A total of <b>{$remittedFunds}</b> has been credited to your withdrawable wallet. 
            <br> You can proceed to withdraw the funds if you deem necessary.
            <br> We hope to see more sales from your store.
            <br> Have a great day ahead.
        ";

        // Create In-App Vendor Notification
        $notification = $this->notificationModel->create($vendorMessage, 'Fund Redeem', $vendorId);
        if ($notification === false) {
            return $this->result->error('Failed to create notification for vendor', 500);
        }

        // Send Vendor Email
        $this->mailManager->sendSimpleMail('Funds Redeemed', $vendorEmail, $vendorMessage);

        // Send Vendor Push Notification
        $vendorPushMessage = $this->textManager->formatPushMessage($vendorMessage);

        $this->pushManager->send('Single Vendor', $vendorId, 'Funds Redeemed', $vendorPushMessage, [
            'url' => "{$this->baseUrl}/seller/",
            'type' => 'funds'
        ]);

        return $this->result->success('Item completed and funds redeemed successfully');
    }

    public function requestFunds(
        float $amount, 
        string $narration, 
        int $userId
    ): Result {

        // Get User Data
        $userData  = $this->getBiodata($userId);
        $userName  = $userData['name'];
        $userEmail = $userData['email'];

        // Get Bank Details
        $bankDetails   = $this->walletModel->getBankDetails($userId);
        $bankName      = $bankDetails['bank_name'];
        $accountNumber = $bankDetails['account_number'];

        // Prevent Invalid Details
        if (
            !$bankName 
            || in_array($bankName, ['None', 'N/A'])
            || !$accountNumber  
            || $accountNumber === 0
        ) {
            return $this->result->error('Invalid bank details. Review your bank details and try again.', 400);
        }

        // Get Current Balance
        $currentBalance = $this->walletModel->getBalance($this->withdrawalTable, $userId);
        if ($currentBalance === false) {
           return $this->result->error('Failed to get balance', 500);
        }

        // Prevent Wrong Withdrawals
        if ($currentBalance === 0 || $amount > $currentBalance) {
            return $this->result->error('Insufficient balance', 400);
        }

        // Withdraw Funds
        $withdraw = $this->walletModel->requestFunds($amount, $bankName, $accountNumber, $narration, $userId);
        if ($withdraw === false) {
           return $this->result->error('Failed to place withdrawal', 500);
        }

        // Debit Wallet
        $this->walletModel->debitWallet($this->withdrawalTable, $amount, $userId);

        // Format Payout Amount
        $payoutFunds = $this->currencyManager->format((float) $amount); 

        // Build User Message
        $userMessage = "
            Hi <b>{$userName}</b>, 

            <br> You have successfully placed a withdrawal of <b>{$payoutFunds}</b>. 
            <br> A total of <b>{$payoutFunds}</b> will be paid into your bank account shortly. 
            <br> We hope to see more sales from your store.
            <br> Have a great day ahead.
        ";

        // Create In-App User Notification
        $notification = $this->notificationModel->create($userMessage, 'Fund Request', $userId);
        if ($notification === false) {
            return $this->result->error('Failed to create notification for vendor', 500);
        }

        // Send User Email
        $this->mailManager->sendSimpleMail('Withdrawal Initiated', $userEmail, $userMessage);

        // Send User Push Notification
        $userPushMessage = $this->textManager->formatPushMessage($userMessage);

        $this->pushManager->send('Single Vendor', $userId, 'Withdrawal Initiated', $userPushMessage, [
            'url' => "{$this->baseUrl}/seller/",
            'type' => 'withdrawal'
        ]);

        return $this->result->success('Withdrawal successful');
    }

    public function singleTransfer(
        string $name,
        int $bank,
        int $account,
        float $amount,
        string $narration,
        string $currency,
        string $reference
    ): Result {

        $url = "https://api.flutterwave.com/v3/transfers";

        $body = [
            "account_bank"     => $bank, // Bank code is used (e.g 044)
            "account_number"   => $account,
            "amount"           => $amount,
            "narration"        => $narration,
            "currency"         => $currency,
            "reference"        => $reference,
            "debit_currency"   => $this->currency, // NGN by default
            "beneficiary_name" => $name,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->secretKey}",
            "Content-Type: application/json"
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result, true);

        if (
            isset($result['status']) 
            && $result['status'] === 'success'
        ) {

            return $this->result->success('Transfer queued successfully', $result);
        } else {
           
            return $this->result->error('Transfer queue failed', 400, $result);
        }
    }

    public function bulkTransfer(
        string $title, 
        array $data
    ): Result {

        $url = "https://api.flutterwave.com/v3/bulk-transfers";

        $body = [
            "title"     => $title,
            "bulk_data" => $data
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->secretKey}",
            "Content-Type: application/json"
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result, true);

        if (isset($result['status']) && $result['status'] === 'success') {

            // (Optional) Debit vendor payout wallet (sum of all transfers)
            $totalAmount = array_sum(array_column($data, 'amount'));

            return $this->result->success('Bulk transfer queued successfully', $result);
        } else {
            
            return $this->result->error('Bulk transfer queue failed', 400, $result);
        }
    }

    public function getByReference(
        string $type, 
        string $reference
    ): Result {

        $payment = $this->walletModel->getByReference($type, $reference);
        if ($payment === false) {
            return $this->result->error('Failed to fetch payment', 400);
        }
        
        return $this->result->success('Payment fetched', ['payment' => $payment]);
    }

    public function getPaymentsByUser(
        string $type, 
        int $page, 
        int $userId
    ): Result {

        $payments = $this->walletModel->getPaymentsByUser($type, $userId, $page);
        if ($payments === false) {
            return $this->result->error('Failed to fetch payments', 400);
        }
        
        return $this->result->success('Payments fetched', $payments);
    }

    public function getPaymentsByType(
        string $table, 
        int $page
    ): Result
    {
        $payments = $this->walletModel->getPaymentsByType($table, $page);
        if ($payments === false) {
            return $this->result->error('Failed to fetch payments', 400);
        }
        
        return $this->result->success('Payments fetched', ['payments' => $payments]);
    }

    public function getPaymentsByStatus(
        string $table, 
        string $column, 
        string $status, 
        int $page
    ): Result {

        $payments = $this->walletModel->getPaymentsByStatus($table, $column, $status, $page);
        if ($payments === false) {
            return $this->result->error('Failed to fetch payments', 400);
        }
        
        return $this->result->success('Payments fetched', ['payments' => $payments]);
    }

    public function getPayoutsByStatus(
        string $status, 
        int $page
    ): Result {

        $payments = $this->walletModel->getWithdrawalsByStatus($status, $page);
        if (count($payments['payments']) === 0) {
            return $this->result->error('Failed to fetch payments', 400);
        }
        
        return $this->result->success('Payments fetched', $payments);
    }

    private function decodeReference(string $reference): array
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

    private function finalizeTransaction(
        ?string $reference = null, 
        ?string $status = null, 
        ?float $amount = null, 
        ?string $currency = null
    ): bool {

        $decoded = $this->decodeReference($reference);
        $type    = $decoded['type'];

        if ($type === 'WAL') {
            return $this->finalizeWalletTopup($reference, $status, $amount);
        }

        if ($type === 'WIT') {
            return $this->finalizeWithdrawal($reference, $status);
        }

        return false;
    }

    private function finalizeWalletTopup(
        ?string $reference = null, 
        ?string $status = null, 
        ?float $amount = null
    ): bool {

        $record = $this->walletModel->getByReference('topups', $reference);
        if (!$record) return false;

        // Already resolved? → ignore duplicate webhook/API calls
        if (in_array($record['status'], ['Completed', 'Failed'])) return true;

        $customerId    = $record['user_id'];
        $customerData  = $this->getBiodata($customerId);
        $customerName  = $customerData['name'];
        $customerEmail = $customerData['email'];

        if ($status === 'successful') {
            $amount = $amount ?: $record['amount'];

            // Credit Wallet
            $this->walletModel->creditWallet('wallet_coin', $amount, $customerId);

            // Update DB status
            $this->walletModel->updateStatus('topups', 'reference', $reference, 'Completed');

            // Fetch New Balance
            $newBalance = $this->walletModel->getBalance('wallet_coin', $customerId);

            // Format Amounts
            $topupAmount   = $this->currencyManager->format((float) $amount); 
            $balanceAmount = $this->currencyManager->format((float) $newBalance); 

            // Build Customer Message
            $customerMessage = "
                Hi <b>{$customerName}</b>, 

                <br> You have successfully funded your shopping wallet with <b>{$topupAmount}</b>.
                <br> Your new wallet balance is <b>{$balanceAmount}</b>.
                <br> Your transaction reference is: <b>{$reference}</b>.
                <br> We hope to see you shop again soon enough.
            ";

            // Send Customer Email
            $this->mailManager->sendSimpleMail('Wallet Topup', $customerEmail, $customerMessage);

            // Send Customer Push Notification
            $customerPushMessage = $this->textManager->formatPushMessage($customerMessage);

            $this->pushManger->send('Single Customer', $customerId, 'Wallet Topup', $customerPushMessage, [
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

    private function finalizeWithdrawal(
        string $reference, 
        string $status
    ): bool {

        $record = $this->walletModel->getByReference('withdrawals', $reference);
        if (!$record) return false;

        if (in_array($record['status'], ['Completed', 'Failed'])) return true;

        $vendorId    = $record['user_id'];
        $vendorData  = $this->getBiodata($vendorId);
        $vendorName  = $vendorData['name'];
        $vendorEmail = $vendorData['email'];

        if ($status === 'successful') {
            $amount = $amount ?: $record['amount'];

            // Update DB Status
            $this->walletModel->updateStatus('withdrawals', 'reference', $reference, 'Completed');

            // Format Amount
            $payoutAmount = $this->currencyManager->format((float) $amount);

            // Build User Message
            $vendorMessage = "
                Hi <b>{$vendorName}</b>, 

                <br> You have received a payout of <b>{$payoutAmount}</b> from ShopCity.
                <br> Your transaction reference is: <b>{$reference}</b>.
                <br> We hope to see more sales from your stores</b>.
                <br> Have a great day ahead.
            ";

            // Send Vendor Email
            $this->mailManager->sendimpleMail('ShopCity Payout', $vendorEmail, $vendorMessage);

            // Send Vendor Push Notification
            $vendorPushMessage = $this->textManager->formatPushMessage($vendorMessage);

            $this->pushManager->send('Single Vendor', $vendorId, 'ShopCity Payout', $vendorPushMessage, [
                'url' => "{$this->baseUrl}/seller/",
                'type' => 'payout'
            ]);

            return true;
        }

        if ($status === 'failed') {
            $this->walletModel->updateStatus('withdrawals', 'reference', $reference, 'Failed');
            return true;
        }

        $this->walletModel->updateStatus('withdrawals', 'reference', $reference, ucfirst($status));
        return false;
    }

    public function verifyPayment(
        int $paymentId, 
        string $reference
    ): Result {

        // Call Flutterwave
        $url = "https://api.flutterwave.com/v3/transactions/{$paymentId}/verify";
        $result = curl_exec(curl_init($url));
        $result = json_decode($result, true);

        // If No Conclusive Outcome → Pending → Wait For Webhook
        if (!isset($result['data']['status'])) {
            return $this->result->success("We are waiting for payment confirmation", ['pending' => true]);
        }

        $status   = strtolower($result['data']['status']);
        $amount   = $result['data']['amount'] ?? null;
        $currency = $result['data']['currency'] ?? null;

        // Finalize Transaction
        $done = $this->finalizeTransaction($reference, $status, $amount, $currency);

        if ($done) {
            return $this->result->success("Transaction processed", ['status' => $status]);
        }

        return $this->result->success("Transaction pending, awaiting webhook...");
    }

    private function getBiodata(
        int $userId
    ): array {

        $userData = $this->userModel->findById($userId);

        return [
            'name'  => $userData['firstname'] . ' ' . $userData['lastname'],
            'email' => $userData['email'],
            'role'  => $userData['user_role'],
            'phone' => $userData['contact'],

        ];
    }
}
