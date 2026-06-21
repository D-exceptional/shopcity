<?php

namespace App\Models;

class Cart extends Database
{
    // Get all items in a user's cart
    public function view(int $userId): ?array
    {
        $stmt = $this->db->prepare("
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
            WHERE c.user_id = ?
        ");
        
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Add item to cart
    public function add(int $userId, int $productId, int $quantity)
    {
        // check if already exists
        $stmt = $this->db->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        $existing = $stmt->fetch();

        if ($existing) {
            // update quantity
            $stmt = $this->db->prepare("UPDATE cart SET quantity = quantity + ? WHERE cart_id = ?");
            return $stmt->execute([$quantity, $existing['cart_id']]);
        } else {
            // insert new
            $stmt = $this->db->prepare("INSERT INTO cart (product_id, quantity, user_id) VALUES (?, ?, ?)");
            return $stmt->execute([$productId, $quantity, $userId]);
        }
    }

    // Update quantity
    public function update(int $userId, int $productId, int $quantity)
    {
        $stmt = $this->db->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        return $stmt->execute([$quantity, $userId, $productId]);
    }

    // Remove item
    public function remove(int $userId, int $productId)
    {
        $stmt = $this->db->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        return $stmt->execute([$userId, $productId]);
    }

    // Clear cart
    public function clear(int $userId)
    {
        $stmt = $this->db->prepare("DELETE FROM cart WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }

    // Count cart total items for a user
    public function countTotal(int $userId)
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(quantity), 0) AS total_items
            FROM cart
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return (int) $result['total_items'];
    }

    public function countCart(int $userId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM cart
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    // Count all carts (abandoned carts)
    public function countAll(): int
    {
        $stmt = $this->db->query("
            SELECT COUNT(DISTINCT user_id) AS pending_carts
            FROM cart
        ");
        $result = $stmt->fetch();
        return (int) $result['pending_carts'];
    }

    // Get pending carts count + users involved + items they have at once
    public function getCartUsers(): ?array
    {
        $sql = "
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

        $stmt = $this->db->query($sql);
        $users = $stmt->fetchAll();

        return [
            'count' => count($users),   // total distinct users with carts
            'users' => $users           // user details + cart totals
        ];
    }
}
