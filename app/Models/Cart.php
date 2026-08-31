<?php

declare(strict_types=1);

namespace App\Models;

class Cart extends Model
{
    protected string $table = 'cart';

    public function view(
        int $userId
    ): ?array {

        $sql = "
            SELECT 
                c.cart_id, 
                c.user_id, 
                c.product_id, 
                c.quantity, 
                p.product_name, 
                p.category,
                p.product_price, 
                p.store_id,
                (p.product_price * c.quantity) AS total_price,
                pm.media_url AS product_image
            FROM cart c
            JOIN products p ON p.product_id = c.product_id
            LEFT JOIN product_media pm 
                ON pm.product_id = p.product_id
                AND pm.media_id = (
                    SELECT MIN(media_id) 
                    FROM product_media 
                    WHERE product_id = p.product_id
                )
            WHERE 
                c.user_id = ?
        ";

        return $this->queryAll($sql, [$userId]);
    }

    public function add(
        int $productId, 
        int $quantity, 
        int $userId
    ): bool {

        $isExisting = $this->query()
            ->where('user_id', '=', $userId)
            ->where('product_id', '=', $productId)
            ->first();

        if ($isExisting) {

            $updateQuery = "
                UPDATE {$table} 
                SET 
                    quantity = quantity + ? 
                WHERE 
                    cart_id = ?
            ";
           
            return $this->executeQuery(
                $updateQuery,
                [$quantity, $isExisting['cart_id']]
            );

        } else {
            
            return $this->query()
                ->insert([
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                    'user_id'    => $userId,
                ]);
        }
    }

    public function update(
        int $productId, 
        int $quantity, 
        int $userId
    ): bool {

        return $this->query()
            ->update([
                'quantity'   => $quantity,
                'product_id' => $productId,
                'user_id'    => $userId,
            ]);
    }

    public function remove(
        int $productId, 
        int $userId
    ): bool {

        return $this->query()
            ->delete([
                'product_id' => $productId,
                'user_id'    => $userId,
            ]);
    }

    public function clear(
        int $userId
    ): bool {

        return $this->query()
            ->delete([
                'user_id' => $userId,
            ]);
    }

    public function countTotal(
        int $userId
    ): int {

        $countQuery = "
            SELECT 
                COALESCE(SUM(quantity), 0) AS total_items
            FROM {$table}
            WHERE 
                user_id = ?
        ";

        $result = $this->queryOne($countQuery, [$userId]);

        return (int) $result['total_items'] ?? 0;
    }

    public function countCart(
        int $userId
    ): int {

        return $this->query()
            ->where('user_id', '=', $userId)
            ->count();
    }

    public function countAll(): int
    {
        $countQuery = "
            SELECT COUNT(DISTINCT user_id) AS pending_carts
            FROM {$table}
        ";

        $result = $this->queryOne($countQuery);

        return (int) $result['pending_carts'];
    }

    public function getCartUsers(): array
    {
        $fetchQuery = "
            SELECT 
                u.user_id, 
                u.firstname, 
                u.lastname, 
                u.email,
                COALESCE(SUM(c.quantity), 0) AS total_items
            FROM cart c
            INNER JOIN users u ON c.user_id = u.user_id
            GROUP BY u.user_id, u.firstname, u.lastname, u.email
        ";

        $users = $this->queryAll($fetchQuery);

        return [
            'count' => count($users),   // total distinct users with carts
            'users' => $users           // user details + cart totals
        ];
    }
}
