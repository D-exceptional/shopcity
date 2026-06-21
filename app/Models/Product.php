<?php

namespace App\Models;

use PDO;
use DateTime;

class Product extends Database
{
    
    // ---------------- CRUD METHODS ---------------- //
    public function create(string $name, string $description, string $category, string $sub, float $price, float $slash, int $stock, string $color, int $storeId): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO products (product_name, product_description, category, sub_category, product_price, slash_price, stock, color, store_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $description, $category, $sub, $price, $slash, $stock, $color, $storeId]);
        return $this->db->lastInsertId();
    }

    public function update(
        string $name, 
        string $description, 
        string $category, 
        string $subcategory, 
        float $price, 
        float $slash, 
        int $stock, 
        string $color, 
        string $visibility, 
        string $reselling, 
        float $commission, 
        int $productId
    ): bool
    {
        $stmt = $this->db->prepare("
            UPDATE products 
            SET product_name = ?, product_description = ?, category = ?, sub_category = ?, 
                product_price = ?, slash_price = ?, stock = ?, color = ?, visibility = ?, reselling = ?, commission = ? 
            WHERE product_id = ?
        ");
        return $stmt->execute([$name, $description, $category, $subcategory, $price, $slash, $stock, $color, $visibility, $reselling, $commission, $productId]);
    }

    public function delete(int $productId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM products WHERE product_id = ?");
        return $stmt->execute([$productId]);
    }

    public function find(int $productId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->execute([$productId]);
        return $stmt->fetch();
    }
    
    /**
     * Build products with media + ratings
    */
    private function buildProducts(array $products): ?array
    {
        if (empty($products)) return [];

        $productIds = array_column($products, 'product_id');
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));

        // --- Fetch media --- //
        // ------------ FETCH ALL MEDIA OF A PRODUCT --------------------------------------------------------- //
        // $stmtMedia = $this->db->prepare("SELECT * FROM product_media WHERE product_id IN ($placeholders)");
        // --------------------------------------------------------------------------------------------------- //

        // ------------ FETCH ONE MEDIA PER PRODUCT ----------- //
        $stmtMedia = $this->db->prepare("
            SELECT pm.*
            FROM product_media pm
            INNER JOIN (
                SELECT product_id, MIN(media_id) AS first_media_id
                FROM product_media
                WHERE product_id IN ($placeholders)
                GROUP BY product_id
            ) first_media
            ON pm.media_id = first_media.first_media_id
        ");
        $stmtMedia->execute($productIds);
        $allMedia = $stmtMedia->fetchAll();

        $mediaByProduct = [];
        foreach ($allMedia as $media) {
            // $mediaByProduct[$media['product_id']] = $media['media_url']; For only the media url
            $mediaByProduct[$media['product_id']][] = $media; // For full media details
        }

        // --- Fetch ratings --- //
        $stmtRatings = $this->db->prepare("
            SELECT product_id, AVG(rating) as average_rating, COUNT(*) as rating_count
            FROM reviews
            WHERE product_id IN ($placeholders)
            GROUP BY product_id
        ");
        $stmtRatings->execute($productIds);
        $allRatings = $stmtRatings->fetchAll();

        $ratingsByProduct = [];
        foreach ($allRatings as $rating) {
            $ratingsByProduct[$rating['product_id']] = [
                'average' => round((float)$rating['average_rating'], 2),
                'count'   => (int)$rating['rating_count']
            ];
        }

        // --- Attach media & ratings --- //
        foreach ($products as &$product) {
            $id = $product['product_id'];
            $product['media'] = $mediaByProduct[$id] ?? [];
            $product['rating'] = $ratingsByProduct[$id] ?? ['average' => 0.0, 'count' => 0];
        }
        unset($product);

        return $products;
    }

    /**
     * Generic fetch with enrichment & pagination
    */
    private function fetchProducts(?string $sql = null, array $params = [], int $page = 1, int $perPage = 20): ?array
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
        $products = $stmt->fetchAll();

        return $this->buildProducts($products);
    }

    private function countProducts(string $sql, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    private function paginate(array $data, int $total, int $page, int $perPage): ?array
    {
        return [
            'products'    => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => ceil($total / $perPage),
        ];
    }

    // ---------------- FETCH METHODS ---------------- //
    public function findByAll(int $page = 1, int $perPage = 20, string $view = 'customer'): ?array
    {
        // Add filtering based on view
        $condition = in_array($view, ['customer']) ? "WHERE visibility = 'Visible'" : "";  

        $sql = "SELECT * FROM products {$condition} ORDER BY created_at DESC";
        $products = $this->fetchProducts($sql, [], $page, $perPage);
        $total = $this->countProducts("SELECT COUNT(*) FROM products {$condition}");
        return $this->paginate($products, $total, $page, $perPage);
    }

    public function findByCategory(?string $category = null, int $page = 1, int $perPage = 20, string $view = 'customer'): ?array
    {
        // Add filtering based on view
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $sql = "SELECT * FROM products WHERE category = ? {$condition} ORDER BY created_at DESC";
        $products = $this->fetchProducts($sql, [$category], $page, $perPage);
        $total = $this->countProducts("SELECT COUNT(*) FROM products WHERE category = ? {$condition}", [$category]);
        return $this->paginate($products, $total, $page, $perPage);
    }

    public function findByStore(?int $storeId = null, int $page = 1, int $perPage = 20, string $view = 'customer'): ?array
    {
        // Add filtering based on view
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : "";  

        $sql = "SELECT * FROM products WHERE store_id = ? {$condition} ORDER BY created_at DESC";
        $products = $this->fetchProducts($sql, [$storeId], $page, $perPage);
        $total = $this->countProducts("SELECT COUNT(*) FROM products WHERE store_id = ? {$condition}", [$storeId]);
        return $this->paginate($products, $total, $page, $perPage);
    }

    public function findByStoreCategory(?int $storeId = null, ?string $category = null, int $page = 1, int $perPage = 20, string $view = 'customer'): ?array
    {
        // Add filtering based on view
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $sql = "SELECT * FROM products WHERE store_id = ? AND category = ? {$condition} ORDER BY created_at DESC";
        $products = $this->fetchProducts($sql, [$storeId, $category], $page, $perPage);
        $total = $this->countProducts("SELECT COUNT(*) FROM products WHERE store_id = ? AND category = ? {$condition}", [$storeId, $category]);
        return $this->paginate($products, $total, $page, $perPage);
    }

    public function findNewArrivals(int $page = 1, int $perPage = 20, string $view = 'customer'): ?array
    {
        // Add filtering based on view
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $sql = "SELECT * FROM products WHERE created_at >= NOW() - INTERVAL 7 DAY {$condition} ORDER BY created_at DESC";
        $products = $this->fetchProducts($sql, [], $page, $perPage);
        $total = $this->countProducts("SELECT COUNT(*) FROM products WHERE created_at >= NOW() - INTERVAL 7 DAY {$condition}");
        return $this->paginate($products, $total, $page, $perPage);
    }

    public function findFeatured(int $storeId = 1, int $page = 1, int $perPage = 20, string $view = 'customer'): ?array
    {
        // Add filtering based on view
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $sql = "SELECT * FROM products WHERE store_id = ? {$condition} ORDER BY created_at DESC";
        $products = $this->fetchProducts($sql, [$storeId], $page, $perPage);
        $total = $this->countProducts("SELECT COUNT(*) FROM products WHERE store_id = ? {$condition}", [$storeId]);
        return $this->paginate($products, $total, $page, $perPage);
    }

    public function findTopSelling(int $page = 1, int $perPage = 20): ?array
    {
        $sql = "SELECT p.*, SUM(oi.quantity) AS total_quantity_sold
                FROM order_items oi
                JOIN products p ON p.product_id = oi.product_id
                GROUP BY oi.product_id
                HAVING SUM(oi.quantity) > 100
                ORDER BY total_quantity_sold DESC";

        $products = $this->fetchProducts($sql, [], $page, $perPage);

        $total = $this->countProducts("
            SELECT COUNT(*) FROM (
                SELECT product_id
                FROM order_items
                GROUP BY product_id
                HAVING SUM(quantity) > 100
            ) AS top_sellers
        ");

        return $this->paginate($products, $total, $page, $perPage);
    }

    public function findByPriceRange(?float $min = null, ?float $max = null, int $page = 1, int $perPage = 20, string $view = 'customer'): ?array
    {
        // Add filtering based on view
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $sql = "SELECT * FROM products WHERE product_price BETWEEN ? AND ? {$condition} ORDER BY product_price ASC";
        $products = $this->fetchProducts($sql, [$min, $max], $page, $perPage);
        $total = $this->countProducts("SELECT COUNT(*) FROM products WHERE product_price BETWEEN ? AND ? {$condition}", [$min, $max]);
        return $this->paginate($products, $total, $page, $perPage);
    }

    public function findByMinPrice(?float $min = null, int $page = 1, int $perPage = 20, string $view = 'customer'): ?array
    {
        // Add filtering based on view
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $sql = "SELECT * FROM products WHERE product_price >= ? {$condition} ORDER BY product_price ASC";
        $products = $this->fetchProducts($sql, [$min], $page, $perPage);
        $total = $this->countProducts("SELECT COUNT(*) FROM products WHERE product_price >= ? {$condition}", [$min]);
        return $this->paginate($products, $total, $page, $perPage);
    }

    public function findByMaxPrice(?float $max = null, int $page = 1, int $perPage = 20, string $view = 'customer'): ?array
    {
        // Add filtering based on view
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $sql = "SELECT * FROM products WHERE product_price <= ? {$condition} ORDER BY product_price ASC";
        $products = $this->fetchProducts($sql, [$max], $page, $perPage);
        $total = $this->countProducts("SELECT COUNT(*) FROM products WHERE product_price <= ? {$condition}", [$max]);
        return $this->paginate($products, $total, $page, $perPage);
    }

    public function findByGroupedCategory(int $page = 1, int $perCategory = 10, string $view = 'customer'): ?array
    {
        // Add filtering based on view
        $condition = in_array($view, ['customer']) ? "WHERE visibility = 'Visible'" : ""; 

        $stmt = $this->db->query("SELECT * FROM products {$condition} ORDER BY category ASC, created_at DESC LIMIT 1000");
        $products = $stmt->fetchAll();

        $productIds = array_column($products, 'product_id');
        $mediaByProduct = [];
        $ratingsByProduct = [];

        if (!empty($productIds)) {
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));

            // ------------ FETCH ALL MEDIA OF A PRODUCT --------------------------------------------------------- //
            // $stmtMedia = $this->db->prepare("SELECT * FROM product_media WHERE product_id IN ($placeholders)");
            // --------------------------------------------------------------------------------------------------- //

            // ------------ FETCH ONE MEDIA PER PRODUCT ----------- //
            $stmtMedia = $this->db->prepare("
                SELECT pm.*
                FROM product_media pm
                INNER JOIN (
                    SELECT product_id, MIN(media_id) AS first_media_id
                    FROM product_media
                    WHERE product_id IN ($placeholders)
                    GROUP BY product_id
                ) first_media
                ON pm.media_id = first_media.first_media_id
            ");
            $stmtMedia->execute($productIds);
            $allMedia = $stmtMedia->fetchAll();

            foreach ($allMedia as $media) {
                $mediaByProduct[$media['product_id']][] = $media;
            }

            $stmtRatings = $this->db->prepare("
                SELECT product_id, AVG(rating) AS average_rating, COUNT(*) AS rating_count
                FROM reviews
                WHERE product_id IN ($placeholders)
                GROUP BY product_id
            ");
            $stmtRatings->execute($productIds);
            $allRatings = $stmtRatings->fetchAll();

            foreach ($allRatings as $rating) {
                $ratingsByProduct[$rating['product_id']] = [
                    'average' => round((float)$rating['average_rating'], 2),
                    'count' => (int)$rating['rating_count']
                ];
            }
        }

        $grouped = [];
        $categoryCounters = [];

        foreach ($products as &$product) {
            $id = $product['product_id'];

            $product['media'] = $mediaByProduct[$id] ?? [];
            $product['rating'] = $ratingsByProduct[$id] ?? ['average' => 0.0, 'count' => 0];

            $category = $product['category'] ?? 'uncategorized';

            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
                $categoryCounters[$category] = 0;
            }

            if ($categoryCounters[$category] < $perCategory) {
                $grouped[$category][] = $product;
                $categoryCounters[$category]++;
            }
        }
        unset($product);

        return [
            'categories' => $grouped,
            'per_category_limit' => $perCategory,
            'category_count' => count($grouped),
            'total_products_returned' => array_sum($categoryCounters)
        ];
    }
    

    public function findOne(int $productId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if ($product === false) {
            return false;
        }

        $stmtMedia = $this->db->prepare("SELECT * FROM product_media WHERE product_id = ?");
        $stmtMedia->execute([$productId]);
        $product['media'] = $stmtMedia->fetchAll();

        $stmtReviews = $this->db->prepare("
            SELECT 
                r.review_id,
                r.comment,
                r.rating,
                r.created_at,
                u.user_id,
                u.firstname AS first_name,
                u.lastname AS last_name,
                u.avatar
            FROM reviews r
            JOIN users u ON r.user_id = u.user_id
            WHERE r.product_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmtReviews->execute([$productId]);
        $reviews = $stmtReviews->fetchAll();

        $totalRating = 0;
        $ratingCount = 0;

        foreach ($reviews as &$review) {
            if (!empty($review['created_at'])) {
                $date = new DateTime($review['created_at']);
                //$review['created_at'] = $date->format('F j, Y');
                $review['created_at'] = $date->format('l, F j, Y \a\t g:iA');

            }
            if (isset($review['rating'])) {
                $totalRating += (float)$review['rating'];
                $ratingCount++;
            }
        }
        unset($review);

        $product['reviews'] = $reviews;
        $averageRating = $ratingCount > 0 ? round($totalRating / $ratingCount, 2) : 0.0;
        $product['rating'] = [
            'average' => $averageRating,
            'count' => $ratingCount
        ];

        return $product;
    }

    // ---------------- SEARCH METHOD ---------------- //
    public function findBySearch(?string $query = null, int $page = 1, int $perPage = 20, string $view = 'customer'): ?array
    {
        // Add filtering based on view
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $likeQuery = '%' . $query . '%';

        $sql = "SELECT * FROM products 
                WHERE product_name LIKE ? OR product_description LIKE ? 
                {$condition}
                ORDER BY created_at DESC";

        $products = $this->fetchProducts($sql, [$likeQuery, $likeQuery], $page, $perPage);

        $total = $this->countProducts(
            "SELECT COUNT(*) FROM products WHERE product_name LIKE ? OR product_description LIKE ? {$condition}",
            [$likeQuery, $likeQuery]
        );

        return $this->paginate($products, $total, $page, $perPage);
    }

    public function findByColor(?string $color = null, int $page = 1, int $perPage = 20, string $view = 'customer'): ?array
    {
        // Add filtering based on view
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $sql = "SELECT * FROM products WHERE color = ? {$condition} ORDER BY created_at DESC";
        $products = $this->fetchProducts($sql, [$color], $page, $perPage);
        $total = $this->countProducts("SELECT COUNT(*) FROM products WHERE color = ? {$condition}", [$color]);
        return $this->paginate($products, $total, $page, $perPage);
    }

    public function findByStoreColor(?int $storeId = null, ?string $color = null, int $page = 1, int $perPage = 20, string $view = 'customer'): ?array
    {
        // Add filtering based on view
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $sql = "SELECT * FROM products WHERE store_id = ? AND color = ? {$condition} ORDER BY created_at DESC";
        $products = $this->fetchProducts($sql, [$storeId, $color], $page, $perPage);
        $total = $this->countProducts("SELECT COUNT(*) FROM products WHERE store_id = ? AND color = ? {$condition}", [$storeId, $color]);
        return $this->paginate($products, $total, $page, $perPage);
    }

    public function findByRelated(?string $category = null, ?int $productId = null, int $page = 1, int $perPage = 20, string $view = 'customer'): ?array
    {
        // Add filtering based on view
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $sql = "SELECT * FROM products WHERE category = ? AND product_id != ? {$condition} ORDER BY created_at DESC";
        $products = $this->fetchProducts($sql, [$category, $productId], $page, $perPage);
        $total = $this->countProducts("SELECT COUNT(*) FROM products WHERE category = ? AND product_id != ? {$condition}", [$category, $productId]);
        return $this->paginate($products, $total, $page, $perPage);
    }

    public function allColors(): ?array
    {
        $stmt = $this->db->prepare("
            SELECT DISTINCT color 
            FROM products 
            WHERE color IS NOT NULL AND color != '' 
            ORDER BY color ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function groupByColor(?int $storeId = null): ?array
    {
        if ($storeId === null) {
            // Global view: show all distinct product colors
            $query = "
                SELECT 
                    p.color,
                    COUNT(p.product_id) AS product_count
                FROM products p
                WHERE p.color IS NOT NULL AND p.color != ''
                GROUP BY p.color
                ORDER BY p.color ASC
            ";

            $stmt = $this->db->prepare($query);
        } else {
            // Store view: only colors that exist in this store
            $query = "
                SELECT 
                    p.color,
                    COUNT(p.product_id) AS product_count
                FROM products p
                WHERE 
                    p.color IS NOT NULL 
                    AND p.color != ''
                    AND p.store_id = :storeId
                GROUP BY p.color
                HAVING product_count > 0
                ORDER BY p.color ASC
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':storeId', $storeId, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function decrementStock(int $productId, int $quantity): bool
    {
        $stmt = $this->db->prepare("SELECT stock FROM products WHERE product_id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            throw new \RuntimeException("Product not found.");
        }

        $currentQty = (int)$product['stock'];
        $newQty = max(0, $currentQty - $quantity);

        $update = $this->db->prepare("UPDATE products SET stock = ? WHERE product_id = ?");
        return $update->execute([$newQty, $productId]);
    }

    public function addReview(int $userId, int $productId, string $review, int $rating): bool
    {
        $stmt = $this->db->prepare("INSERT INTO reviews (user_id, product_id, comment, rating) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$userId, $productId, $review, $rating]);
    }

    public function countProductsByType(?int $userId = null, ?int $storeId = null, ?string $status = null, string $role = 'vendor'): int
    {
        $sql = "SELECT COUNT(*) FROM products WHERE 1";
        $params = [];

        // Vendor mode: restrict by vendor's user ID
        if ($role === 'vendor' && !is_null($userId)) {
            $sql .= " AND store_id IN (SELECT store_id FROM stores WHERE user_id = ?)";
            $params[] = $userId;
        }

        // Optional store filter
        if (!is_null($storeId)) {
            $sql .= " AND store_id = ?";
            $params[] = $storeId;
        }

        // Optional status/visibility filter
        if (!is_null($status)) {
            $sql .= " AND visibility = ?";
            $params[] = $status;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $total = $stmt->fetchColumn();
        return (int) ($total !== false ? $total : 0);
    }

    /**
     * Fetch all key dashboard stats in one call.
    */
    public function getVendorProductStats(int $userId): ?array
    {
        return [
            'active'  => $this->countProductsByType($userId, null, 'Visible', 'vendor'),
            'pending' => $this->countProductsByType($userId, null, 'Hidden', 'vendor'),
        ];
    }

     /**
     * Fetch all key dashboard stats in one call.
    */
    public function getAdminProductStats(): ?array
    {
        return [
            'total'     => $this->countProductsByType(null, null, null, 'admin'),
            'active'  => $this->countProductsByType(null, null, 'Visible', 'admin'),
            'pending' => $this->countProductsByType(null, null, 'Hidden', 'admin'),
        ];
    }

    /**
     * ✅ Count All Reviews (for all stores or a specific store)
     *
     * @param int $userId   - Vendor (store owner) ID
     * @param int|null $storeId - Optional specific store ID
     * @return int
     */
    public function countReviewsByVendor(?int $userId = null, ?int $storeId = null): int
    {
        if (is_null($userId)) {
            return 0;
        }

        $sql = "
            SELECT 
                COALESCE(COUNT(r.review_id), 0) AS total_reviews
            FROM reviews AS r
            INNER JOIN products AS p ON p.product_id = r.product_id
            INNER JOIN stores AS s ON s.store_id = p.store_id
            WHERE s.user_id = ?
        ";

        $params = [$userId];

        // ✅ Filter by specific store if provided
        if (!is_null($storeId)) {
            $sql .= " AND s.store_id = ?";
            $params[] = $storeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchColumn();

        return (int) ($result !== false ? $result : 0);
    }
}
