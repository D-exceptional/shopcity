<?php

namespace App\Models;

use PDO;

class Category extends Database
{
    // 1. List all categories
    public function all(): ?array
    {
        $stmt = $this->db->prepare("
            SELECT DISTINCT category_name
            FROM product_categories 
            WHERE category_name IS NOT NULL AND category_name != '' 
            ORDER BY category_name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // 2. List all categories with product counts
    public function group(?int $storeId = null): ?array
    {
        // Base query depends on whether we're viewing all stores or a single store
        if ($storeId === null) {
            // Global view: show all categories (even empty ones)
            $query = "
                SELECT
                    pc.category_id,
                    pc.category_name,
                    COUNT(p.product_id) AS product_count
                FROM product_categories pc
                LEFT JOIN products p 
                    ON p.category = pc.category_name
                GROUP BY pc.category_name
                ORDER BY pc.category_name ASC
            ";

            $stmt = $this->db->prepare($query);
        } else {
            // Store view: only categories that have products for that store
            $query = "
                SELECT
                    pc.category_id,
                    pc.category_name,
                    COUNT(p.product_id) AS product_count
                FROM product_categories pc
                INNER JOIN products p 
                    ON p.category = pc.category_name
                    AND p.store_id = :storeId
                GROUP BY pc.category_id, pc.category_name
                HAVING product_count > 0
                ORDER BY pc.category_name ASC
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':storeId', $storeId, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    // 3. Add new category
    public function create(string $category): bool
    {
        $stmt = $this->db->prepare("INSERT INTO product_categories (category_name) VALUES (?)");
        return $stmt->execute([$category]);
    }

    // 4. Update category
    public function update(string $name, int $categoryId): bool
    {
        $stmt = $this->db->prepare("UPDATE product_categories SET category_name = ? WHERE category_id = ?");
        return $stmt->execute([$name, $categoryId]);
    }

    // 5. Delete category
    public function delete(int $categoryId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM product_categories WHERE category_id = ?");
        return $stmt->execute([$categoryId]);
    }

    // 6. Count all 
    public function count(): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT category_name) AS total_categories
            FROM product_categories
        ");
        $stmt->execute();
        $result = $stmt->fetch();
        return (int) $result['total_categories'];
    }
    
    // 7. Get id
    public function id(string $category): int
    {
        $stmt = $this->db->prepare("SELECT category_id FROM product_categories WHERE category_name = ?");
        $stmt->execute([$category]);
        return $stmt->fetchColumn();
    }
    
    // 8. Get sub categories
    public function sub(int $categoryId): ?array
    {
        $stmt = $this->db->prepare("SELECT subcategory_name FROM product_subcategories WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }
    
    // 7. Get id
    public function fetch(string $category): ?array
    {
        $categoryId = $this->id($category);
        $subcategories = $this->sub($categoryId);
        return $subcategories ?? [];
    }
}
