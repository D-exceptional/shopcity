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

        return $this->query()
            ->insert([
                'order_id'  => $orderId,
                'user_id'   => $userId,
                'amount'    => $amount,
                'reference' => $reference,
                'currency'  => $currency
            ]);
    }
}
