<?php
namespace App\Models;

use PDO;

class Wallet extends Database
{
    /** Generate checkout reference */
    public function generatePaymentReference(string $type = 'Topup', string $identifier = 'SYS') 
    {
        $prefix = 'PAY';
        $type = strtoupper(substr($type, 0, 3));
        $date = date('ymd');
        $random = strtoupper(bin2hex(random_bytes(4)));
        return "{$prefix}-{$type}-{$date}-{$random}-{$identifier}";
    }

    /** Generate checkout reference */
    public function generateWithdrawalReference(string $type = 'Withdrawal', string $identifier = 'SYS') 
    {
        $prefix = 'PAY';
        $type = strtoupper(substr($type, 0, 3));
        $date = date('ymd');
        $random = strtoupper(bin2hex(random_bytes(4)));
        return "{$prefix}-{$type}-{$date}-{$random}-{$identifier}";
    }

    /** Create bank details */
    public function createDetails(int $account, string $bank, string $code, string $currency, int $userId)
    {
        $sql = "INSERT INTO bank_details (account_number, bank_name, bank_code, currency_code, user_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$account, $bank, $code, $currency, $userId]);

        // Check if request actually happened
        return $stmt->rowCount() > 0;
    }

    /** Update bank details */
    public function updateDetails(int $account, string $bank, string $code, int $userId)
    {
        $sql = "UPDATE bank_details SET account_number = ?, bank_name = ?, bank_code = ? WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$account, $bank, $code, $userId]);
    }

    /** Check wallet */
    public function checktWallet(string $table, int $userId)
    {
        $sql = "SELECT * FROM {$table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        // Check if credit actually happened
        return $stmt->rowCount() > 0;
    }

    public function createWallet(string $type, int $amount, int $userId)
    {
        $tables = ($type === 'Customer') ? ['wallet_coin'] : ['wallet_savings', 'wallet_payout', 'wallet_payout_backup'];

        foreach ($tables as $table) {
            $sql = "INSERT INTO {$table} (wallet_amount, user_id) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$amount, $userId]);
        }
        // Check if request actually happened
        return $stmt->rowCount() > 0;
    }

    /** Create a pending payment reference */
    public function createPayment(int $userId, float $amount, string $currency)
    {
        // Generate reference
        $reference = $this->generatePaymentReference();

        $sql = "INSERT INTO topups (user_id, amount, reference, currency) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $amount, $reference, $currency]);

        return $reference;
    }

    /** Credit wallet */
    public function creditWallet(string $table, float $amount, int $userId)
    {
        $sql = "UPDATE $table SET wallet_amount = wallet_amount + ? WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$amount, $userId]);

        // Check if credit actually happened
        return $stmt->rowCount() > 0;
    }

    /** Debit wallet safely (no negative balance) */
    public function debitWallet(string $table, float $amount, int $userId)
    {
        $sql = "
            UPDATE {$table} 
            SET wallet_amount = CASE 
                WHEN wallet_amount >= ? THEN wallet_amount - ? 
                ELSE wallet_amount 
            END
            WHERE user_id = ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$amount, $amount, $userId]);

        // Check if deduction actually happened
        return $stmt->rowCount() > 0;
    }

    /** Redeem funds to main wallet */
    public function redeemFunds(int $userId)
    {
        $sql = "SELECT wallet_amount FROM wallet_savings WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $amount = $stmt->fetchColumn();

        // Reset wallet
        $sql = "UPDATE wallet_savings SET wallet_amount = 0 WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        // Check if deduction actually happened
        return $amount;
    }

    /** Request funds from withdrawal wallet */
    public function requestFunds(float $amount, string $bank, int $account, string $narration, int $userId)
    {
        $reference = $this->generateWithdrawalReference();
        $sql = "INSERT INTO withdrawals (amount, bank, account, reference, narration, user_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$amount, $bank, $account, $reference, $narration, $userId]);

        // Check if request actually happened
        // return $stmt->rowCount() > 0;
    }

    /** Update payment status */
    public function updateStatus(string $table, string $column, string $reference, string $status)
    {
        $sql = "UPDATE $table SET $column = ? WHERE reference = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $reference]);

        // Check if deduction actually happened
        // return $stmt->rowCount() > 0;
    }

    /** Get bank details */
    public function getBankDetails(int $userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM bank_details WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    /** Get payment by reference */
    public function getByReference(string $table, string $reference)
    {
        $sql = "SELECT * FROM {$table} WHERE reference = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$reference]);
        return $stmt->fetch();
    }

    /** Fetch payments of all kinds for a user -> (User and vendor) */
    public function getPaymentsByUser(?string $table = null, ?int $userId = null, int $page = 1, int $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM {$table} WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, (int)$userId, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** Fetch payments by type (withdrawals, payments, topups) -> Admin */
    public function getPaymentsByType(?string $table = null, int $page = 1, int $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

     /** Fetch payments by status (Pending, Completed, Failed) -> Admin */
    public function getPaymentsByStatus(?string $table = null, ?string $column = null, ?string $status = null, int $page = 1, int $perPage = 20)
    {
       $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM {$table} WHERE $column = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, (int)$status, PDO::PARAM_STR);
        $stmt->bindValue(2, (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** Fetch wallet balance */
    public function getBalance(string $table, int $userId)
    {
        $sql = "SELECT wallet_amount FROM {$table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetchColumn();

        return (int) ($result !== false ? $result : 0);
    }

     /** Fetch wallet balance */
    public function getWithdrawalByType(?int $userId = null, ?string $status = null, string $role = 'vendor'): float
    {
        $sql = "SELECT COALESCE(SUM(amount), 0) FROM withdrawals WHERE 1";
        $params = [];

        // Vendor mode: restrict to vendor's user ID
        if ($role === 'vendor' && !is_null($userId)) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }

        // Optional status filter
        if (!is_null($status)) {
            $sql .= " AND withdrawal_status = ?";
            $params[] = $status;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchColumn();

        return (float) ($result !== false ? $result : 0);
    }

    /**
     * Fetch all key dashboard stats in one call.
    */
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

    /**
     * Fetch all key dashboard stats in one call.
    */
    public function getAdminWalletStats(int $userId): ?array
    {
        return [
            'total_payout'    => $this->getWithdrawalByType(null, 'Completed', 'admin'),
            'pending_payout'  => $this->getWithdrawalByType(null, 'Pending', 'admin'),
        ];
    }

    private function fetchPayments(?string $sql = null, array $params = [], int $page = 1, int $perPage = 20): ?array
    {
        $offset = ($page - 1) * $perPage;

        $sql .= " LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);

        $i = 1;
        foreach ($params as $param) {
            $type = is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($i++, $param, $type);
        }

        $stmt->bindValue($i++, (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue($i, (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function countPayments(string $sql, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    private function sumPayments(string $sql, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    private function paginate(array $data, int $total, int $sum, int $page, int $perPage): ?array
    {
        return [
            'payments'    => $data,
            'total'       => $total,
            'sum'         => $sum,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => ceil($total / $perPage),
        ];
    }

    public function getWithdrawalsByStatus(string $status, int $page = 1, int $perPage = 20): ?array
    {
        // Fetch withdrawals with user & bank details
        $sql = "
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
            WHERE w.withdrawal_status = ?
            ORDER BY w.created_at DESC
        ";

        $payments = $this->fetchPayments($sql, [$status], $page, $perPage);
        $total = $this->countPayments("SELECT COUNT(*) FROM withdrawals WHERE withdrawal_status = ?", [$status]);
        $sum = $this->sumPayments("SELECT SUM(amount) FROM withdrawals WHERE withdrawal_status = ?", [$status]);
        return $this->paginate($payments, $total, $sum, $page, $perPage);
    }
}
