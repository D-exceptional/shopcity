<?php

declare(strict_types=1);

namespace App\Models;

class Wishlist extends Model
{
    protected string $table = 'wishlist';

    public function view(
        ?int $userId = null, 
        int $page = 1, 
        int $limit = 20
    ): array {

        $offset = ($page - 1) * $limit;

        $fetchQuery = "
            SELECT 
                w.wishlist_id, 
                w.user_id, 
                w.product_id, 
                p.product_name, 
                p.category,
                p.product_price, 
                pm.media_url AS product_image

            FROM {$this->table} w

            JOIN products p ON p.product_id = w.product_id

            LEFT JOIN product_media pm 

                ON pm.product_id = p.product_id
                AND pm.media_id = (
                    SELECT MIN(media_id) 
                    FROM product_media 
                    WHERE product_id = p.product_id
                )

            WHERE 
                w.user_id = ?

            LIMIT {$limit} OFFSET {$offset}
        ";

        $wishlist = $this->queryAll($fetchQuery, [$userId]);
        $total    = $this->table()
            ->where('user_id', '=', $userId)
            ->count();

        return $this->format($wishlist, $total, $page, $limit);
    }

    public function add(
        int $userId, 
        int $productId
    ): bool {

        return $this->transaction(function () use (
            $userId,
            $productId
        ) {

            $existing = $this->table()
                ->where('user_id', '=', $userId)
                ->where('product_id', '=', $productId)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return false;
            }

            return $this->table()
                ->insert([
                    'product_id' => $productId,
                    'user_id'    => $userId,
                ]);
        });
    }

    public function remove(
        int $userId, 
        int $productId
    ): bool {

        return $this->table()
            ->where('user_id', '=', $userId)
            ->where('product_id', '=', $productId)
            ->delete();
    }

    public function clear(
        int $userId
    ): bool {

        return $this->table()
            ->where('user_id', '=', $userId)
            ->delete();
    }

    private function format(
        array $data, 
        int $total, 
        int $page, 
        int $limit
    ): array {

        return [
            'wishlist'    => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $limit,
            'total_pages' => ceil($total / $limit),
        ];
    }
}