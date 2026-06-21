<?php

namespace App\Models;

class Link extends Database
{
    /**
     * Create a new product link
    */
    public function create(int $product, int $user, string $short, string $long, string $code, string $status): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO product_links (product_id, user_id, short_link, long_link, short_code, link_status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$product, $user, $short, $long, $code, $status]);
    }

    /**
     * Find all links by product_id
     */
    public function findAll(int $productId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM product_links WHERE product_id = ?");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    /**
     * Find one link by link_id
     */
    public function findOne(int $linkId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM product_links WHERE link_id = ?");
        $stmt->execute([$linkId]);
        return $stmt->fetch();
    }

    /**
     * Find link by product + user
     */
    public function findByUser(int $productId, int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM product_links WHERE product_id = ? AND user_id = ?");
        $stmt->execute([$productId, $userId]);
        return  $stmt->fetch();
    }

    /**
     * Enable/Disable all links for a product
     */
    public function updateAll(int $productId, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE product_links SET link_status = ? WHERE product_id = ?");
        return $stmt->execute([$status, $productId]);
    }

    /**
     * Enable/Disable one link
     */
    public function updateOne(int $linkId, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE product_links SET link_status = ? WHERE link_id = ?");
        return $stmt->execute([$status, $linkId]);
    }

    /**
     * Delete all links for a product
     */
    public function deleteAll(int $productId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM product_links WHERE product_id = ?");
        return $stmt->execute([$productId]);
    }

    /**
     * Delete one link
     */
    public function deleteOne(int $linkId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM product_links WHERE link_id = ?");
        return $stmt->execute([$linkId]);
    }

    /**
     * Count links (optionally filter by product)
     */
    public function count(?int $productId = null): int
    {
        if ($productId !== null) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM product_links WHERE product_id = ?");
            $stmt->execute([$productId]);
        } else {
            $stmt = $this->db->query("SELECT COUNT(*) FROM product_links");
        }
        return (int) $stmt->fetchColumn();
    }

    /**
     * Generate unique affiliate code
    */
    public function generateCode(): string
    {
        return bin2hex(random_bytes(6));
    }
}
