<?php

declare(strict_types=1);

namespace App\Models;

class CheckoutPayment extends Model
{
    protected string $table = 'payments';

    public function generateReference(
        string $type = 'Purchase', 
        string $identifier = 'SYS'
    ): string {

        $prefix = 'TXN';
        $type   = strtoupper(substr($type, 0, 3));
        $date   = date('ymd');
        $random = strtoupper(bin2hex(random_bytes(4)));

        return "{$prefix}-{$type}-{$date}-{$random}-{$identifier}";
    }

    public function createPayment(
        int $orderId, 
        float $amount, 
        string $currency, 
        int $userId
    ): bool {

        $reference = $this->generateReference();

        $createQuery = "
            INSERT INTO payments (order_id, user_id, amount, reference, currency) 
            VALUES (?, ?, ?, ?, ?)
        ";

        return $this->executeQuery(
            $createQuery,
            [$orderId, $userId, $amount, $reference, $currency]
        );
    }
}
