<?php

declare(strict_types=1);

namespace App\Models;

class Wallet extends Model
{
    protected string $table = 'withdrawals';

    public function createDetails(
        int $account, 
        string $bank, 
        string $code, 
        string $currency, 
        int $userId
    ): bool {

        return $this->table('bank_details')
            ->insert([
                'account_number' => $account,
                'bank_name'      => $bank,
                'bank_code'      => $code,
                'currency_code'  => $currency,
                'user_id'        => $userId
            ]);
    }

    public function updateDetails(
        int $account, 
        string $bank, 
        string $code, 
        int $userId
    ): bool {

        return $this->table('bank_details')
            ->where('user_id', '=', $userId)
            ->update([
                'account_number' => $account,
                'bank_name'      => $bank,
                'bank_code'      => $code,
            ]);
    }

    public function checktWallet(
        string $table, 
        int $userId
    ): bool {

        $result = $this->table($table)
            ->where('user_id', '=', $userId)
            ->first();

        return !is_null($result);
    }

    public function createWallet(
        string $type,
        float $amount,
        int $userId
    ): bool {

        $tables = ($type === 'Customer')
            ? ['wallet_shopping']
            : ['wallet_payout', 'wallet_payout_backup'];

        foreach ($tables as $table) {

            $walletCreated = $this->table($table)
                ->insert([
                    'wallet_amount' => $amount,
                    'user_id'       => $userId
                ]);

            if (!$walletCreated) {
                return false;
            }
        }

        return true;
    }

    public function createPayment(
        float $amount, 
        string $currency, 
        int $userId
    ): string {

        // Generate Reference
        $reference = $this->generateReference('Topup');

        $paymentCreated = $this->table('topups')
            ->insert([
                'user_id'   => $userId,
                'amount'    => $amount,
                'reference' => $reference,
                'currency'  => $currency
            ]);

        if (!$paymentCreated) {
            throw new \Exception('Failed to create payment record');
        }

        return $reference;
    }

    public function getWalletForUpdate(
        string $table,
        int $userId
    ): ?array {

        return $this->table($table)
            ->where('user_id', '=', $userId)
            ->lockForUpdate()
            ->first();
    }

    public function creditWallet(
        string $table, 
        float $amount, 
        int $userId
    ): bool {

        $wallet = $this->getWalletForUpdate($table, $userId);
        $column = $wallet['wallet_amount'];

        if (!$wallet) {
            throw new \Exception('Wallet not found.');
        }

        return $this->table($table)
            ->where('user_id', '=', $userId)
            ->increment($column, $amount);
    }

    public function debitWallet(
        string $table, 
        float $amount, 
        int $userId
    ): bool {

        $wallet = $this->getWalletForUpdate($table, $userId);
        $column = $wallet['wallet_amount'];

        if (!$wallet) {
            throw new \Exception('Wallet not found.');
        }

        if ($wallet[$column] < $amount) {
            throw new \Exception('Insufficient wallet balance.');
        }

        return $this->table($table)
            ->where('user_id', '=', $userId)
            ->decrement($column, $amount);
    }

    public function redeemFunds(
        int $userId
    ): ?float {

        // Fetch Current Redeemable Balance
        $table  = 'wallet_savings';
        $column = 'wallet_amount';

        $wallet = $this->getWalletForUpdate($table, $userId);

        if (!$wallet) {
            throw new \Exception('Wallet not found.');
        }

        $amount = $wallet[$column];

        // Reset Wallet
        $this->table($table)
            ->where('user_id', '=', $userId)
            ->update([
                $column => 0,
            ]);

        // Return Redeemed Amount
        return $amount;
    }

    public function requestFunds(
        float $amount, 
        string $bank, 
        int $account, 
        string $narration, 
        int $userId
    ): bool {

        // Generate Reference
        $reference = $this->generateReference('Withdraw');

        return $this->table('withdrawals')
            ->insert([
                'amount'    => $amount,
                'bank'      => $bank,
                'account'   => $account,
                'reference' => $reference,
                'narration' => $narration,
                'user_id'   => $userId,
            ]);
    }

    public function updateStatus(
        string $table, 
        string $column, 
        string $reference, 
        string $status
    ): bool {

        return $this->table($table)
            ->where('reference', '=', $reference)
            ->update([
                $column => $status,
            ]);
    }

    public function getBankDetails(
        int $userId
    ): ?array {

        return $this->table('bank_details')
            ->where('user_id', '=', $userId)
            ->first();
    }

    public function getByReference(
        string $table, 
        string $reference
    ): ?array {

        return $this->table($table)
            ->where('reference', '=', $reference)
            ->first();
    }

    public function getPaymentsByUser(
        ?string $table = null, 
        ?int $userId = null, 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        return $this->table($table)
            ->when(
                $userId !== null,
                function ($query) use ($userId) {
                    $query->where('user_id', '=', $userId);
                }
            )
            ->latest()
            ->paginate($page, $limit)
            ->get();
    }

    public function getPaymentsByType(
        ?string $table = null, 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        return $this->table($table)
            ->latest()
            ->paginate($page, $limit)
            ->get();
    }

    public function getPaymentsByStatus(
        ?string $table = null, 
        ?string $column = null, 
        ?string $status = null, 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        return $this->table($table)
            ->when(
                $column !== null
                && $status !== null,
                function ($query) use ($column, $status) {
                    $query->where($column, '=', $status);
                }
            )
            ->latest()
            ->paginate($page, $limit)
            ->get();
    }

    public function getBalance(
        string $table, 
        int $userId
    ): ?float {

        $result = $this->table($table)
            ->select([
                'wallet_amount',
            ])
            ->where('user_id', '=', $userId)
            ->first();

        return $result ? (float)$result['wallet_amount'] : null;
    }

    public function getWithdrawalByType(
        ?int $userId = null, 
        ?string $status = null, 
        string $role = 'vendor'
    ): float {

        $result = $this->table('withdrawals')
            ->select([
                'COALESCE(SUM(amount), 0)',
            ])
            ->when(
                $role === 'vendor'
                && $userId !== null,
                function ($query) use ($userId) {
                    $query->where('user_id', '=', $userId);
                }
            )
            ->when(
                $status !== null,
                function ($query) use ($status) {
                    $query->where('withdrawal_status', '=', $status);
                }
            )
            ->first();

        return (float) ($result !== false ? $result : 0);
    }

    public function getVendorWalletStats(int $userId): ?array
    {
        return [
            'current_balance' => $this->getBalance('wallet_payout', $userId),
            'total_balance'   => $this->getBalance('wallet_payout_backup', $userId),
            // 'savings_balance' => $this->getBalance('wallet_savings', $userId),
            'total_payout'    => $this->getWithdrawalByType($userId, 'Completed', 'vendor'),
            'pending_payout'  => $this->getWithdrawalByType($userId, 'Pending', 'vendor'),
        ];
    }

    public function getAdminWalletStats(int $userId): ?array
    {
        return [
            'total_payout'    => $this->getWithdrawalByType(null, 'Completed', 'admin'),
            'pending_payout'  => $this->getWithdrawalByType(null, 'Pending', 'admin'),
        ];
    }

    public function getWithdrawalsByStatus(
        ?string $status, 
        int $page = 1, 
        int $limit = 20
    ): array {

        // Fetch Withdrawals With User & Bank Details
        $payments = $this->table('withdrawals w')
            ->select([
                'w.withdrawal_id',
                'w.amount',
                'w.bank',
                'w.account',
                'w.reference',
                'w.narration',
                'w.withdrawal_status',
                'w.created_at',
                // User details
                'u.user_id',
                'u.firstname',
                'u.lastname',
                'u.email',
                'u.contact',
                'u.country',
                'u.user_state',
                // Bank details
                'bd.account_number AS account_number',
                'bd.bank_name AS bank_name',
                'bd.bank_code AS bank_code',
                'bd.currency_code AS currency_code',
            ])
            ->join(
                'users u',
                'w.user_id',
                '=',
                'u.user_id',
            )
            ->leftJoin(
                'bank_details bd',
                'w.user_id',
                '=',
                'bd.user_id',
            )
            ->when(
                $status !== null,
                function ($query) use ($status) {
                    $query->where('w.withdrawal_status', '=', $status);
                }
            )
            ->latest('w.created_at')
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->where('withdrawal_status', '=', $status)
            ->count();

        $sum = (float) ($total !== false ? $total : 0);

        return $this->formatPayments($payments, $total, $sum, $page, $limit);
    }

    // =========================================
    // HELPER METHODS
    // =========================================

    private function generateReference(
        string $type = 'Topup', 
        string $identifier = 'SYS'
    ): string {

        $prefix = 'PAY';
        $type   = strtoupper(substr($type, 0, 3));
        $date   = date('ymd');
        $random = strtoupper(bin2hex(random_bytes(4)));

        return "{$prefix}-{$type}-{$date}-{$random}-{$identifier}";
    }

    private function formatPayments(
        array $data, 
        int $total, 
        float $sum, 
        int $page, 
        int $limit
    ): array {

        return [
            'payments'    => $data,
            'total'       => $total,
            'sum'         => $sum,
            'page'        => $page,
            'per_page'    => $limit,
            'total_pages' => ceil($total / $limit),
        ];
    }
}
