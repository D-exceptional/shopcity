<?php

namespace App\Models;

class ProductMedia extends Database
{
    /**
     * Create a new media record
     */
    public function create(string $url, string $type, int $productId): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO product_media (media_url, media_type, product_id)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$url, $type, $productId]);
    }

    /**
     * Find all media for a product
     */
    public function findAll(int $productId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM product_media WHERE product_id = ?");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    /**
     * Find one media record by ID
     */
    public function findOne(int $mediaId)
    {
        $stmt = $this->db->prepare("SELECT * FROM product_media WHERE media_id = ?");
        $stmt->execute([$mediaId]);
        return $stmt->fetch();
    }

    /**
     * Update a media record
     */
    public function update(string $url, int $productId): bool
    {
        $stmt = $this->db->prepare("UPDATE product_media SET media_url = ? WHERE media_id = ?");
        return $stmt->execute([$url, $productId]);
    }

    /**
     * Delete all media records for a product
     */
    public function deleteAll(int $productId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM product_media WHERE product_id = ?");
        return $stmt->execute([$productId]);
    }

    /**
     * Delete a single media record
     */
    public function deleteOne(int $mediaId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM product_media WHERE media_id = ?");
        return $stmt->execute([$mediaId]);
    }
}
