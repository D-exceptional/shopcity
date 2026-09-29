<?php

declare(strict_types=1);

namespace App\Models;

class Cart extends Model
{
    protected string $table = 'cart';

    // =========================================
    // VIEW CART
    // =========================================

    public function view(
        int $userId
    ): array {
        /*
         * The product_media subquery is still raw SQL for now.
         *
         * Your current QueryBuilder does not yet support
         * subqueries in joins, so there is no reason to force
         * this query into the builder prematurely.
         */

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

            FROM {$this->table} c

            JOIN products p
                ON p.product_id = c.product_id

            LEFT JOIN product_media pm
                ON pm.product_id = p.product_id
                AND pm.media_id = (
                    SELECT MIN(media_id)
                    FROM product_media
                    WHERE product_id = p.product_id
                )

            WHERE c.user_id = ?
        ";

        return $this->queryAll($sql, [$userId]);
    }


    // =========================================
    // ADD TO CART
    // =========================================

    public function add(
        int $productId,
        int $quantity,
        int $userId
    ): bool {

        return $this->transaction(function () use (
            $productId,
            $quantity,
            $userId
        ) {

            $existing = $this->table()
                ->where('user_id', '=', $userId)
                ->where('product_id', '=', $productId)
                ->lockForUpdate()
                ->first();

            if ($existing) {

                return $this->table()
                    ->where('cart_id', '=', $existing['cart_id'])
                    ->increment('quantity', $quantity);
            }

            return $this->table()
                ->insert([
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                    'user_id'    => $userId,
                ]);
        });
    }


    // =========================================
    // UPDATE CART ITEM
    // =========================================

    public function update(
        int $productId,
        int $quantity,
        int $userId
    ): bool {

        return $this->table()
            ->where('product_id', '=', $productId)
            ->where('user_id', '=', $userId)
            ->update([
                'quantity' => $quantity,
            ]);
    }


    // =========================================
    // REMOVE CART ITEM
    // =========================================

    public function remove(
        int $productId,
        int $userId
    ): bool {

        return $this->table()
            ->where('product_id', '=', $productId)
            ->where('user_id', '=', $userId)
            ->delete();
    }


    // =========================================
    // CLEAR CART
    // =========================================

    public function clear(
        int $userId
    ): bool {

        return $this->table()
            ->where('user_id', '=', $userId)
            ->delete();
    }


    // =========================================
    // TOTAL ITEMS
    // =========================================

    public function countTotal(
        int $userId
    ): int {

        return (int) (
            $this->table()
                ->where('user_id', '=', $userId)
                ->sum('quantity')
        );
    }


    // =========================================
    // NUMBER OF CART ITEMS
    // =========================================

    public function countCart(
        int $userId
    ): int {

        return $this->table()
            ->where('user_id', '=', $userId)
            ->count();
    }


    // =========================================
    // NUMBER OF USERS WITH CARTS
    // =========================================

    public function countAll(): int
    {
        return $this->table()
            ->count('user_id');
    }


    // =========================================
    // CART USERS
    // =========================================

    public function getCartUsers(): array
    {
        $users = $this->table()
            ->select([
                'u.user_id',
                'u.firstname',
                'u.lastname',
                'u.email',
                'COALESCE(SUM(cart.quantity), 0) AS total_items',
            ])
            ->join(
                'users u',
                'cart.user_id',
                '=',
                'u.user_id',
                'INNER'
            )
            ->groupBy([
                'u.user_id',
                'u.firstname',
                'u.lastname',
                'u.email',
            ])
            ->get();

        return [
            'count' => count($users),
            'users' => $users,
        ];
    }
}