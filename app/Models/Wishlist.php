<?php
namespace App\Models;
use PDO;

class Wishlist extends Database
{
    /** Fetch orders */
    private function fetchWishlist(?string $sql = null, array $params = [], int $page = 1, int $perPage = 20): ?array
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

    /** Count orders */
    private function countWishlist(?string $sql = null, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

     /** Paginate orders */
    private function paginate(array $data, int $total, int $page, int $perPage): ?array
    {
        return [
            'wishlist'    => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => ceil($total / $perPage),
        ];
    }

    // Get all items in a user's wishlist
    public function view(?int $userId = null, int $page = 1, int $perPage = 20)
    {
        $sql = "
            SELECT 
                w.wishlist_id, 
                w.user_id, 
                w.product_id, 
                p.product_name, 
                p.category,
                p.product_price, 
                pm.media_url AS product_image
            FROM wishlist w
            JOIN products p ON p.product_id = w.product_id
            LEFT JOIN product_media pm 
                ON pm.product_id = p.product_id
                AND pm.media_id = (
                    SELECT MIN(media_id) 
                    FROM product_media 
                    WHERE product_id = p.product_id
                )
            WHERE w.user_id = ?
        ";
        $wishlist = $this->fetchWishlist($sql, [$userId], $page, $perPage);
        $total = $this->countWishlist("SELECT COUNT(*) FROM wishlist WHERE user_id = ?", [$userId]);
        return $this->paginate($wishlist, $total, $page, $perPage);
    }

    // Add item to wishlist
    public function add(int $userId, int $productId)
    {
        // check if already exists
        $stmt = $this->db->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        $existing = $stmt->fetch();

        if ($existing) {
            return null;
        } else {
            // insert new
            $stmt = $this->db->prepare("INSERT INTO wishlist (product_id, user_id) VALUES (?, ?)");
            return $stmt->execute([$productId, $userId]);
        }
    }

    // Remove item
    public function remove(int $userId, int $productId)
    {
        $stmt = $this->db->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        return $stmt->execute([$userId, $productId]);
    }

    // Clear cart
    public function clear(int $userId)
    {
        $stmt = $this->db->prepare("DELETE FROM wishlist WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}
