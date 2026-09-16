<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Wallet extends Model
{
    protected string $table = 'withdrawals';

    public function generateReference(
        string $type = 'Topup', 
        string $identifier = 'SYS'
    ): string {

        $prefix = 'PAY';
        $type   = strtoupper(substr($type, 0, 3));
        $date   = date('ymd');
        $random = strtoupper(bin2hex(random_bytes(4)));

        return "{$prefix}-{$type}-{$date}-{$random}-{$identifier}";
    }

    public function createDetails(
        int $account, 
        string $bank, 
        string $code, 
        string $currency, 
        int $userId
    ): bool {

        $createQuery = "
           INSERT INTO bank_details (account_number, bank_name, bank_code, currency_code, user_id) 
           VALUES (?, ?, ?, ?, ?)
        ";

        return $this->executeQuery(
            $createQuery, 
            [$account, $bank, $code, $currency, $userId]
        );
    }

    public function updateDetails(
        int $account, 
        string $bank, 
        string $code, 
        int $userId
    ): bool {

        $updateQuery = "
            UPDATE bank_details 
            SET 
                account_number = ?, bank_name = ?, bank_code = ? 
            WHERE 
                user_id = ?
        ";

        return $this->executeQuery(
            $updateQuery, 
            [$account, $bank, $code, $userId]
        );
    }

    public function checktWallet(
        string $table, 
        int $userId
    ): int {

        $checkQuery = "
            SELECT 
                * 
            FROM {$table} 
            WHERE 
                user_id = ?
        ";

        $result = $this->fetchColumn($checkQuery, [$userId]);

        return $result > 0;
    }

    public function createWallet(
        string $type, 
        float $amount, 
        int $userId
    ): void {

        $tables = ($type === 'Customer') 
            ? ['wallet_shopping'] 
            : ['wallet_payout', 'wallet_payout_backup'];

        foreach ($tables as $table) {

            $createQuery = "
                INSERT INTO {$table} (wallet_amount, user_id) 
                VALUES (?, ?)
            ";

            $this->executeQuery(
                $createQuery, 
                [$amount, $userId]
            );
        }
    }

    public function createPayment(
        float $amount, 
        string $currency, 
        int $userId
    ): string {

        // Generate Reference
        $reference = $this->generateReference('Topup');

        $createQuery = "
            INSERT INTO topups (user_id, amount, reference, currency) 
            VALUES (?, ?, ?, ?)
        ";

        $this->executeQuery(
            $createQuery, 
            [$userId, $amount, $reference, $currency]
        );

        return $reference;
    }

    public function creditWallet(
        string $table, 
        float $amount, 
        int $userId
    ): bool {

        $creditQuery = "
            UPDATE {$table} 
            SET 
                wallet_amount = wallet_amount + ? 
            WHERE 
                user_id = ?
        ";

        return $this->executeQuery(
            $creditQuery, 
            [$amount, $userId]
        );
    }

    public function debitWallet(
        string $table, 
        float $amount, 
        int $userId
    ): bool {

        $debitQuery = "
            UPDATE {$table} 
            SET 
                wallet_amount = CASE 
                    WHEN wallet_amount >= ? THEN wallet_amount - ? 
                ELSE wallet_amount 
            END
            WHERE 
                user_id = ?
        ";

        return $this->executeQuery(
            $debitQuery, 
            [$amount, $amount, $userId]
        );
    }

    public function redeemFunds(
        int $userId
    ): bool {

        // Fetch Current Redeemable Balance
        $fetchQuery = "
            SELECT 
                wallet_amount 
            FROM wallet_savings 
            WHERE 
                user_id = ?
        ";

        $amount = $this->fetchColumn($fetchQuery, [$userId]);

        // Reset Wallet
        $resetQuery = "
            UPDATE wallet_savings 
            SET 
                wallet_amount = 0 
            WHERE 
                user_id = ?
        ";

        $this->executeQuery($resetQuery, [$userId]);

        // Return Amount To Caller
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

        $requestQuery = "
            INSERT INTO withdrawals (amount, bank, account, reference, narration, user_id) 
            VALUES (?, ?, ?, ?, ?, ?)
        ";

        return $this->executeQuery(
            $requestQuery, 
            [$amount, $bank, $account, $reference, $narration, $userId]
        );
    }

    public function updateStatus(
        string $table, 
        string $column, 
        string $reference, 
        string $status
    ): bool {

        $updateQuery = "
            UPDATE {$table} 
            SET 
                {$column} = ? 
            WHERE 
                reference = ?
        ";

        return $this->executeQuery(
            $updateQuery, 
            [$status, $reference]
        );
    }

    public function getBankDetails(
        int $userId
    ): ?array {

        $fetchQuery = "
            SELECT 
                * 
            FROM bank_details 
            WHERE 
                user_id = ?
        ";

        return $this->queryOne($fetchQuery, [$userId]);
    }

    public function getByReference(
        string $table, 
        string $reference
    ): ?array {

        $fetchQuery = "
            SELECT 
                * 
            FROM {$table} 
            WHERE 
                reference = ?
        ";

        return $this->queryOne($fetchQuery, [$reference]);
    }

    public function getPaymentsByUser(
        ?string $table = null, 
        ?int $userId = null, 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        $offset = ($page - 1) * $limit;

        $fetchQuery = "
            SELECT 
                * 
            FROM {$table} 
            WHERE 
                user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ";

        return $this->queryAll($fetchQuery, [$userId, $limit, $offset]);
    }

    public function getPaymentsByType(
        ?string $table = null, 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        $offset = ($page - 1) * $limit;

        $fetchQuery = "
            SELECT 
                * 
            FROM {$table} 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ";

        return $this->queryAll($fetchQuery, [$limit, $offset]);
    }

    public function getPaymentsByStatus(
        ?string $table = null, 
        ?string $column = null, 
        ?string $status = null, 
        int $page = 1, 
        int $limit = 20
    ): ?array {

       $offset = ($page - 1) * $limit;

        $fetchQuery = "
            SELECT 
                * 
            FROM {$table} 
            WHERE 
                {$column} = ? 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ";

        return $this->queryAll($fetchQuery, [$status, $limit, $offset]);
    }

    public function getBalance(
        string $table, 
        int $userId
    ): int {

        $balanceQuery = "
            SELECT 
                wallet_amount 
            FROM {$table} 
            WHERE 
                user_id = ?
        ";

        return $this->fetchColumn($balanceQuery, [$userId]);
    }

    public function getWithdrawalByType(
        ?int $userId = null, 
        ?string $status = null, 
        string $role = 'vendor'
    ): float {

        $fetchQuery = "
            SELECT 
                COALESCE(SUM(amount), 0) 
            FROM withdrawals 
            WHERE 
                1
        ";

        $params = [];

        // Vendor Mode: Restrict To Vendor's U  ser ID
        if ($role === 'vendor' && !is_null($userId)) {
            $fetchQuery .= " AND user_id = ?";
            $params[] = $userId;
        }

        // Optional Status Filter
        if (!is_null($status)) {
            $fetchQuery .= " AND withdrawal_status = ?";
            $params[] = $status;
        }

        $result = $this->fetchColumn($fetchQuery, $params);

        return (float) ($result !== false ? $result : 0);
    }

    public function getVendorWalletStats(int $userId): ?array
    {
        return [
            'current_balance' => $this->getBalance('wallet_payout', $userId),
            'total_balance'   => $this->getBalance('wallet_payout_backup', $userId),
            'savings_balance' => $this->getBalance('wallet_savings', $userId),
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

    private function fetchPayments(
        ?string $sql = null, 
        array $params = [], 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        $offset = ($page - 1) * $limit;

        $sql .= " LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);

        $i = 1;
        foreach ($params as $param) {
            $type = is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($i++, $param, $type);
        }

        $stmt->bindValue($i++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($i, (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function countPayments(
        string $sql, 
        array $params = []
    ): int {

        return $this->fetchColumn($sql, $params);
    }

    private function sumPayments(
        string $sql, 
        array $params = []
    ): float {

        $result = $this->fetchColumn($sql, $params);

        return (float) ($result !== false ? $result : 0);
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

    public function getWithdrawalsByStatus(
        ?string $status, 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        // Fetch Withdrawals With User & Bank Details
        $fetchQuery = "
            SELECT 
                w.withdrawal_id,
                w.amount,
                w.bank,
                w.account,
                w.reference,
                w.narration,
                w.withdrawal_status,
                w.created_at,

                u.user_id,
                u.firstname,
                u.lastname,
                u.email,
                u.contact,
                u.country,
                u.user_state,

                bd.account_number AS account_number,
                bd.bank_name AS bank_name,
                bd.bank_code AS bank_code,
                bd.currency_code AS currency_code

            FROM withdrawals w
            INNER JOIN users u ON w.user_id = u.user_id
            LEFT JOIN bank_details bd ON w.user_id = bd.user_id
            WHERE 
                w.withdrawal_status = ?
            ORDER BY w.created_at DESC
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM withdrawals 
            WHERE 
                withdrawal_status = ?
        ";

        $payments = $this->fetchPayments($fetchQuery, [$status], $page, $limit);
        $total    = $this->countPayments($countQuery, [$status]);
        $sum      = $this->sumPayments($countQuery, [$status]);

        return $this->formatPayments($payments, $total, $sum, $page, $limit);
    }
}
