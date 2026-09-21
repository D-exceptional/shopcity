<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Category extends Model
{
    protected string $table = 'product_categories';

    public function all(): ?array
    {
        $fetchQuery = "
            SELECT 
                DISTINCT category_name
            FROM {$this->table} 
            WHERE 
                category_name IS NOT NULL 
                AND category_name != '' 
            ORDER BY category_name ASC
        ";

        return $this->queryAll($fetchQuery);
    }

    public function group(
        ?int $storeId = null
    ): ?array {

        if ($storeId === null) {
            // Global View: Show All Categories (Even Empty Ones)
            $allQuery = "
                SELECT
                    pc.category_id,
                    pc.category_name,
                    COUNT(p.product_id) AS product_count
                FROM {$this->table} pc
                LEFT JOIN products p 
                    ON p.category = pc.category_name
                GROUP BY pc.category_id, pc.category_name
                ORDER BY pc.category_name ASC
            ";

            return $this->queryAll($allQuery);

        } else {
            // Store View: Only Categories That Have Products For That Store
            $storeQuery = "
                SELECT
                    pc.category_id,
                    pc.category_name,
                    COUNT(p.product_id) AS product_count
                FROM {$this->table} pc
                INNER JOIN products p 
                    ON p.category = pc.category_name
                    AND p.store_id = ?
                GROUP BY pc.category_id, pc.category_name
                HAVING product_count > 0
                ORDER BY pc.category_name ASC
            ";

            return $this->queryAll($storeQuery, [$storeId]);
        }
    }

    public function create(
        string $category
    ): bool {

        return $this->query()
            ->insert([
                'category_name' => $category,
            ]);
    }

    public function update(
        string $name, 
        int $categoryId
    ): bool {

        return $this->query()
            ->update([
                'category_name' => $name,
                'category_id'   => $categoryId,
            ]);
    }

    public function delete(
        int $categoryId
    ): bool {

        return $this->query()
            ->where('category_id', '=', $categoryId)
            ->delete();
    }

    public function count(): int
    {
        $countQuery = "
            SELECT 
                COUNT(DISTINCT category_name) AS total_categories
            FROM {$this->table}
        ";

        $result = $this->queryOne($countQuery);

        return (int) $result['total_categories'] ?? 0;
    }
    
    public function id(
        string $category
    ): int {

        $result = $this->query()
            ->where('category_name', '=', $category)
            ->first();

        return (int) $result['category_id'];
    }
    
    public function sub(
        int $categoryId
    ): ?array {

        $fetchQuery = "
            SELECT 
                subcategory_name 
            FROM product_subcategories 
            WHERE 
                category_id = ?
        ";
        
        return $this->queryAll($fetchQuery, [$categoryId]);
    }
    
    public function fetch(
        string $category
    ): array {

        $categoryId    = $this->id($category);
        $subcategories = $this->sub($categoryId);
        
        return $subcategories ?? [];
    }
}
