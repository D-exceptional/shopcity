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
    ): ?array {

        // =========================================
        // FIRST PRODUCT MEDIA
        // =========================================

        $firstMedia = $this->table('product_media')
            ->select([
                'product_id',
                'MIN(media_id) AS first_media_id',
            ])
            ->groupBy('product_id');


        // =========================================
        // CART QUERY
        // =========================================

        return $this->table('cart AS c')
            ->select([
                'c.cart_id',
                'c.user_id',
                'c.product_id',
                'c.quantity',
                'p.product_name',
                'p.category',
                'p.product_price',
                'p.store_id',
                '(p.product_price * c.quantity) AS total_price',
                'pm.media_url AS product_image',
            ])
            ->join(
                'products AS p',
                'p.product_id',
                '=',
                'c.product_id'
            )
            ->leftJoinSub(
                $firstMedia,
                'first_media',
                'first_media.product_id',
                '=',
                'p.product_id'
            )
            ->leftJoin(
                'product_media AS pm',
                'pm.media_id',
                '=',
                'first_media.first_media_id'
            )
            ->where(
                'c.user_id',
                '=',
                $userId
            )
            ->get();
    }


    // =========================================
    // ADD TO CART
    // =========================================

    public function add(
        int $productId,
        int $quantity,
        int $userId
    ): bool {

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
        $users = $this->table('cart c')
            ->select([
                'u.user_id',
                'u.firstname',
                'u.lastname',
                'u.email',
                'COALESCE(SUM(cart.quantity), 0) AS total_items',
            ])
            ->join(
                'users u',
                'c.user_id',
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