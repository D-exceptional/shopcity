<?php
namespace App\Models;

class Checkout extends Database
{
    /** Generate checkout reference */
    public function generateReference(string $type = 'Purchase', string $identifier = 'SYS') 
    {
        $prefix = 'TXN';
        $type = strtoupper(substr($type, 0, 3));
        $date = date('ymd');
        $random = strtoupper(bin2hex(random_bytes(4)));
        return "{$prefix}-{$type}-{$date}-{$random}-{$identifier}";
    }

    /** Create a pending payment reference */
    public function createPayment(int $orderId, int $userId, int $amount, string $currency) //Pass currency as NGN always
    {
        // Generate reference
        $reference = $this->generateReference();

        $sql = "INSERT INTO payments (order_id, user_id, amount, reference, currency) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$orderId, $userId, $amount, $reference, $currency]);
    }

    /* Get payment by reference 
    public function getByReference(string $reference)
    {
        $sql = "SELECT * FROM payments WHERE reference = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$reference]);
        return $stmt->fetch();
    }

    ** Update payment status *
    public function updateStatus(string $reference, string $status)
    {
        $sql = "UPDATE payments SET status = ? WHERE reference = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $reference]);
    }

    ** Fetch payments for a user *
    public function getUserPayments(int $userId)
    {
        $sql = "SELECT * FROM payments WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    */
}
