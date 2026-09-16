<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Wishlist extends Model
{
    protected string $table = 'wishlist';

    private function fetchWishlist(
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

    private function countWishlist(
        ?string $sql = null, 
        array $params = []
    ): int {

        return $this->fetchColumn($sql, $params);
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

    public function view(
        ?int $userId = null, 
        int $page = 1, 
        int $limit = 20
    ): ?array {

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
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM {$this->table}
            WHERE 
                user_id = ?
        ";

        $wishlist = $this->fetchWishlist($fetchQuery, [$userId], $page, $limit);
        $total    = $this->countWishlist($countQuery, [$userId]);

        return $this->format($wishlist, $total, $page, $limit);
    }

    public function add(
        int $userId, 
        int $productId
    ): ?bool {

        // check If Already Exists
        $isExisting = $this->query()
            ->where('user_id', '=', $userId)
            ->where('product_id', '=', $productId)
            ->first();

        if ($isExisting) {
            return null;
        } else {

            // insert new
            return $this->query()
                ->insert([
                    'product_id' => $productId,
                    'user_id'    => $userId
                ]);
        }
    }

    public function remove(
        int $userId, 
        int $productId
    ): bool {

        return $this->query()
            ->where('user_id', '=', $userId)
            ->where('product_id', '=', $productId)
            ->delete();
    }

    public function clear(
        int $userId
    ): bool {

        return $this->query()
            ->where('user_id', '=', $userId)
            ->delete();
    }
}
