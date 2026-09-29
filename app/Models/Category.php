<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Category extends Model
{
    protected string $table = 'product_categories';

    public function all(): ?array
    {
        return $this->table()
            ->select([
                'category_name',
            ])
            ->distinct()
            ->whereNotNull('category_name')
            ->orderBy('category_name', 'ASC')
            ->get();
    }

    public function group(
        ?int $storeId = null
    ): array {

        $query = $this->table('product_categories pc')
            ->select([
                'pc.category_id',
                'pc.category_name',
                'COUNT(p.product_id) AS product_count',
            ])
            ->join(
                'products p',
                'p.category',
                '=',
                'pc.category_name',
                $storeId === null ? 'LEFT' : 'INNER'
            )
            ->when(
                $storeId !== null,
                function ($query) use ($storeId) {
                    $query->where('p.store_id', '=', $storeId);
                }
            )
            ->groupBy([
                'pc.category_id',
                'pc.category_name',
            ])
            ->orderBy('pc.category_name', 'ASC');

        return $query->get();
    }

    public function create(
        string $category
    ): bool {

        return $this->table()
            ->insert([
                'category_name' => $category,
            ]);
    }

    public function update(
        string $name, 
        int $categoryId
    ): bool {

        return $this->table()
            ->update([
                'category_name' => $name,
                'category_id'   => $categoryId,
            ]);
    }

    public function delete(
        int $categoryId
    ): bool {

        return $this->table()
            ->where('category_id', '=', $categoryId)
            ->delete();
    }

    public function count(): int
    {
        return $this->table()
            ->count('category_name');
    }
    
    public function id(
        string $category
    ): int {

        $result = $this->table()
            ->select([
                'category_id',
            ])
            ->where('category_name', '=', $category)
            ->first();

        return (int) $result['category_id'];
    }
    
    public function sub(
        int $categoryId
    ): ?array {

        return $this->table('product_subcategories')
            ->select([
                'subcategory_name',
            ])
            ->where('category_id', '=', $categoryId)
            ->get();

    }
    
    public function fetch(
        string $category
    ): array {

        $categoryId    = $this->id($category);
        $subcategories = $this->sub($categoryId);
        
        return $subcategories ?? [];
    }
}
