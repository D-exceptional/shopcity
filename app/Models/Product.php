<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use DateTime;

class Product extends Model
{
    protected string $table = 'products';
    
    public function create(
        string $name, 
        string $description, 
        string $category, 
        string $subcategory, 
        float $price, 
        float $slash, 
        int $stock, 
        string $color, 
        int $storeId
    ): int {

        return $this->query()
            ->insertGetId([
                'product_name'        => $name,
                'product_description' => $description,
                'category'            => $category,
                'sub_category'        => $subcategory,
                'product_price'       => $price,
                'slash_price'         => $slash,
                'stock'               => $stock,
                'color'               => $color,
                'store_id'            => $storeId
            ]);
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
    ): bool {

        return $this->query()
            ->where('product_id', '=', $productId)
            ->update([
                'product_name'        => $name,
                'product_description' => $description,
                'category'            => $category,
                'sub_category'        => $subcategory,
                'product_price'       => $price,
                'slash_price'         => $slash,
                'stock'               => $stock,
                'color'               => $color,
                'reselling'           => $reselling,
                'commission'          => $commission,
                'visibility'          => $visibility,
            ]);
    }

    public function metadata(
        string $visibility,   
        bool $isFeatured,
        int $productId
    ): bool {

        return $this->query()
            ->where('product_id', '=', $productId)
            ->update([
                'visibility'  => $visibility,
                'is_featured' => $isFeatured,
            ]);
    }

    public function delete(
        int $productId
    ): bool {

        return $this->query()
            ->where('product_id', '=', $productId)
            ->delete();
    }

    public function find(
        int $productId
    ): ?array {

        return $this->query()
            ->where('product_id', '=', $productId)
            ->first();
    }
    
    private function buildProducts(
        array $products
    ): ?array {

        if (empty($products)) return [];

        $productIds   = array_column($products, 'product_id');
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));

        // ------------ FETCH ONE MEDIA PER PRODUCT ----------- //
        $mediaQuery = "
            SELECT pm.*
            FROM product_media pm
            INNER JOIN (
                SELECT product_id, MIN(media_id) AS first_media_id
                FROM product_media
                WHERE 
                    product_id IN ($placeholders)
                GROUP BY product_id
            ) first_media
            ON pm.media_id = first_media.first_media_id
        ";

        $allMedia = $this->queryAll($mediaQuery, $productIds);

        $mediaByProduct = [];
        foreach ($allMedia as $media) {
            $mediaByProduct[$media['product_id']][] = $media; 
        }

        // --- Fetch Ratings --- //
        $ratingsQuery = "
            SELECT product_id, AVG(rating) as average_rating, COUNT(*) as rating_count
            FROM reviews
            WHERE 
                product_id IN ($placeholders)
            GROUP BY product_id
        ";

        $allRatings = $this->queryAll($ratingsQuery, $productIds);

        $ratingsByProduct = [];
        foreach ($allRatings as $rating) {
            $ratingsByProduct[$rating['product_id']] = [
                'average' => round((float)$rating['average_rating'], 2),
                'count'   => (int)$rating['rating_count']
            ];
        }

        // --- Attach Media & Ratings --- //
        foreach ($products as &$product) {
            $productId         = $product['product_id'];
            $product['media']  = $mediaByProduct[$productId] ?? [];
            $product['rating'] = $ratingsByProduct[$productId] ?? ['average' => 0.0, 'count' => 0];
        }

        unset($product);

        return $products;
    }

    private function fetchProducts(
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

        $products = $stmt->fetchAll();

        return $this->buildProducts($products);
    }

    private function countProducts(
        string $sql, 
        array $params = []
    ): int {

        return $this->fetchColumn($sql, $params);
    }

    private function format(
        array $data, 
        int $total, 
        int $page, 
        int $limit
    ): ?array {

        return [
            'products'    => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    =>$limit,
            'total_pages' => ceil($total /$limit),
        ];
    }

    public function findByAll(
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): ?array {

        // Add Filtering Based On View
        $condition = in_array($view, ['customer']) ? "WHERE visibility = 'Visible'" : "";  

        $productsQuery = "
            SELECT 
                * 
            FROM {$this->table} 
            {$condition} 
            ORDER BY created_at DESC
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM {$this->table} 
            {$condition}
        ";

        $products = $this->fetchProducts($productsQuery, [], $page, $limit);
        $total    = $this->countProducts($countQuery);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByCategory(
        ?string $category = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): ?array {

        // Add Filtering Based On View
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $productsQuery = "
            SELECT 
                * 
            FROM {$this->table} 
            WHERE 
                category = ? 
                {$condition} 
            ORDER BY created_at DESC
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM {$this->table} 
            WHERE 
                category = ? 
                {$condition}
        ";

        $products = $this->fetchProducts($productsQuery, [$category], $page, $limit);
        $total    = $this->countProducts($countQuery, [$category]);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByStore(
        ?int $storeId = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): ?array {

        // Add Filtering Based On View
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : "";  

        $productsQuery = "
            SELECT 
                * 
            FROM {$this->table}
            WHERE 
                store_id = ? 
                {$condition} 
            ORDER BY created_at DESC
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM {$this->table} 
            WHERE 
                store_id = ? 
                {$condition}
        ";

        $products = $this->fetchProducts($productsQuery, [$storeId], $page, $limit);
        $total    = $this->countProducts($countQuery, [$storeId]);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByStoreCategory(
        ?int $storeId = null, 
        ?string $category = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): ?array {

        // Add Filtering Based On View
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $productsQuery = "
            SELECT 
                * 
            FROM {$this->table} 
            WHERE 
                store_id = ? 
                AND category = ? 
                {$condition} 
            ORDER BY created_at DESC
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM {$this->table} 
            WHERE 
                store_id = ? 
                AND category = ? 
                {$condition}
        ";

        $products = $this->fetchProducts($productsQuery, [$storeId, $category], $page, $limit);
        $total    = $this->countProducts($countQuery, [$storeId, $category]);

        return $this->format($products, $total, $page, $limit);
    }

    public function findNewArrivals(
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): ?array {

        // Add Filtering Based On View
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $productsQuery = "
            SELECT 
                * 
            FROM {$this->table} 
            WHERE 
                created_at >= NOW() - INTERVAL 7 DAY 
                {$condition} 
            ORDER BY created_at DESC
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM {$this->table} 
            WHERE 
                created_at >= NOW() - INTERVAL 7 DAY 
                {$condition}
        ";

        $products = $this->fetchProducts($productsQuery, [], $page, $limit);
        $total    = $this->countProducts($countQuery);

        return $this->format($products, $total, $page, $limit);
    }

    public function findFeatured( 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): ?array {

        // Add Filtering Based On View
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        // Logic for featured products is now based on `is_featured` column of the products table and not by `storedId`.
        $productsQuery = "
            SELECT 
                * 
            FROM {$this->table} 
            WHERE 
                is_featured = TRUE 
                {$condition} 
            ORDER BY created_at DESC
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM {$this->table} 
            WHERE 
                is_featured = TRUE 
                {$condition}
        ";

        $products = $this->fetchProducts($productsQuery, [], $page, $limit);
        $total    = $this->countProducts($countQuery);

        return $this->format($products, $total, $page, $limit);
    }

    public function findTopSelling(
        int $page = 1, 
        int $limit = 20
    ): ?array {

        $productsQuery = "
            SELECT 
                p.*, 
                SUM(oi.quantity) AS total_quantity_sold
            FROM order_items oi
            JOIN products p ON p.product_id = oi.product_id
            GROUP BY oi.product_id
            HAVING SUM(oi.quantity) > 100
            ORDER BY total_quantity_sold DESC
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM (
                SELECT product_id
                FROM order_items
                GROUP BY product_id
                HAVING SUM(quantity) > 100
            ) AS top_sellers
        ";

        $products = $this->fetchProducts($productsQuery, [], $page, $limit);
        $total    = $this->countProducts($countQuery);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByPriceRange(
        ?float $min = null, 
        ?float $max = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): ?array {

        // Add Filtering Based On View
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $productsQuery = "
            SELECT 
                * 
            FROM {$this->table} 
            WHERE 
                product_price BETWEEN ? AND ? 
                {$condition} 
            ORDER BY product_price ASC
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM {$this->table} 
            WHERE 
                product_price BETWEEN ? AND ? 
                {$condition}
        ";

        $products = $this->fetchProducts($productsQuery, [$min, $max], $page, $limit);
        $total    = $this->countProducts($countQuery, [$min, $max]);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByMinPrice(
        ?float $min = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): ?array {

        // Add Filtering Based On View
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $productsQuery = "
            SELECT 
                * 
            FROM {$this->table} 
            WHERE 
                product_price >= ? 
                {$condition} 
            ORDER BY product_price ASC
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM {$this->table} 
            WHERE 
                product_price >= ? 
                {$condition}
        ";

        $products = $this->fetchProducts($productsQuery, [$min], $page, $limit);
        $total    = $this->countProducts($countQuery, [$min]);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByMaxPrice(
        ?float $max = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): ?array {

        // Add Filtering Based On View
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $productsQuery = "
            SELECT 
                * 
            FROM {$this->table} 
            WHERE 
                product_price <= ? 
                {$condition} 
            ORDER BY product_price ASC
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM {$this->table} 
            WHERE 
                product_price <= ? 
                {$condition}
        ";

        $products = $this->fetchProducts($productsQuery, [$max], $page, $limit);
        $total    = $this->countProducts($countQuery, [$max]);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByGroupedCategory(
        int $page = 1, 
        int $perCategory = 10, 
        string $view = 'customer'
    ): ?array {

        // Add Filtering Based On View
        $condition = in_array($view, ['customer']) ? "WHERE visibility = 'Visible'" : ""; 

        $productsQuery = "
            SELECT 
                * 
            FROM {$this->table} 
            {$condition} 
            ORDER BY category ASC, created_at DESC 
            LIMIT 1000
        ";

        $products = $this->queryAll($productsQuery);

        $productIds       = array_column($products, 'product_id');
        $mediaByProduct   = [];
        $ratingsByProduct = [];

        if (!empty($productIds)) {
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));

            // ------------ FETCH ONE MEDIA PER PRODUCT ----------- //
            $mediaQuery = "
                SELECT 
                    pm.*
                FROM product_media pm
                INNER JOIN (
                    SELECT 
                        product_id, MIN(media_id) AS first_media_id
                    FROM product_media
                    WHERE 
                        product_id IN ($placeholders)
                    GROUP BY product_id
                ) first_media
                ON pm.media_id = first_media.first_media_id
            ";

            $allMedia = $this->queryAll($mediaQuery, $productIds);

            foreach ($allMedia as $media) {
                $mediaByProduct[$media['product_id']][] = $media;
            }

            $ratingsQuery = "
                SELECT 
                    product_id, AVG(rating) AS average_rating, 
                    COUNT(*) AS rating_count
                FROM reviews
                WHERE 
                    product_id IN ($placeholders)
                GROUP BY product_id
            ";

            $allRatings = $this->queryAll($ratingsQuery, $productIds);

            foreach ($allRatings as $rating) {

                $ratingsByProduct[$rating['product_id']] = [
                    'average' => round((float)$rating['average_rating'], 2),
                    'count'   => (int)$rating['rating_count']
                ];
            }
        }

        $grouped          = [];
        $categoryCounters = [];

        foreach ($products as &$product) {
            $productId = $product['product_id'];

            $product['media']  = $mediaByProduct[$productId] ?? [];
            $product['rating'] = $ratingsByProduct[$productId] ?? ['average' => 0.0, 'count' => 0];

            $category = $product['category'] ?? 'uncategorized';

            if (!isset($grouped[$category])) {
                $grouped[$category]          = [];
                $categoryCounters[$category] = 0;
            }

            if ($categoryCounters[$category] < $perCategory) {
                $grouped[$category][] = $product;
                $categoryCounters[$category]++;
            }
        }

        unset($product);

        return [
            'categories'              => $grouped,
            'per_category_limit'      => $perCategory,
            'category_count'          => count($grouped),
            'total_products_returned' => array_sum($categoryCounters)
        ];
    }

    public function findOne(
        int $productId
    ): ?array {

        $productQuery = "
            SELECT * FROM {$this->table} 
            WHERE 
                product_id = ?
        ";

        // ----------- Fetch Product ----------- //
        $product = $this->queryOne($productQuery, [$productId]);

        if ($product === false) {
            return false;
        }

        // ----------- Attach Media ----------- //
        $mediaQuery = "
            SELECT * FROM product_media 
            WHERE 
                product_id = ?
        ";

        $product['media'] = $this->queryAll($mediaQuery, [$productId]);

        // ----------- Attach Reviews ----------- //
        $reviewsQuery = "
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
            WHERE 
                r.product_id = ?
            ORDER BY r.created_at DESC
        ";

        $reviews = $this->queryAll($reviewsQuery, [$productId]);

        $totalRating = 0;
        $ratingCount = 0;

        foreach ($reviews as &$review) {

            if (!empty($review['created_at'])) {
                $date                 = new DateTime($review['created_at']);
                $review['created_at'] = $date->format('l, F j, Y \a\t g:iA');

            }

            if (isset($review['rating'])) {
                $totalRating += (float)$review['rating'];
                $ratingCount++;
            }
        }

        unset($review);

        // ----------- Build Full Product ----------- //
        $product['reviews'] = $reviews;
        $averageRating      = $ratingCount > 0 ? round($totalRating / $ratingCount, 2) : 0.0;
        $product['rating']  = [
            'average' => $averageRating,
            'count'   => $ratingCount
        ];

        return $product;
    }

    public function findBySearch(
        ?string $query = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): ?array {

        // Add Filtering Based On View
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $likeQuery = '%' . $query . '%';

        $productsQuery = "
            SELECT 
                * 
            FROM {$this->table} 
            WHERE 
                product_name LIKE ? 
                OR product_description LIKE ? 
                {$condition}
            ORDER BY created_at DESC
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM {$this->table} 
            WHERE 
                product_name LIKE ? 
                OR product_description LIKE ? 
                {$condition}
        ";

        $products = $this->fetchProducts($productsQuery, [$likeQuery, $likeQuery], $page, $limit);
        $total    = $this->countProducts($countQuery, [$likeQuery, $likeQuery]);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByColor(
        ?string $color = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): ?array {

        // Add Filtering Based On View
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $productsQuery = "
            SELECT 
                * 
            FROM {$this->table} 
            WHERE 
                color = ? 
                {$condition} 
            ORDER BY created_at DESC
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM {$this->table}
            WHERE 
                color = ? 
                {$condition}
        ";

        $products = $this->fetchProducts($productsQuery, [$color], $page, $limit);
        $total    = $this->countProducts($countQuery, [$color]);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByStoreColor(
        ?int $storeId = null, 
        ?string $color = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): ?array {

        // Add Filtering Based On View
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $productsQuery = "
            SELECT 
                * 
            FROM {$this->table} 
            WHERE 
                store_id = ? 
                AND color = ? 
                {$condition} 
            ORDER BY created_at DESC
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM {$this->table} 
            WHERE 
                store_id = ? 
                AND color = ? 
                {$condition}
        ";

        $products = $this->fetchProducts($productsQuery, [$storeId, $color], $page, $limit);
        $total    = $this->countProducts($countQuery, [$storeId, $color]);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByRelated(
        ?string $category = null, 
        ?int $productId = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): ?array {

        // Add Filtering Based On View
        $condition = in_array($view, ['customer']) ? "AND visibility = 'Visible'" : ""; 

        $productsQuery = "
            SELECT 
                * 
            FROM {$this->table} 
            WHERE 
                category = ? 
                AND product_id != ? 
                {$condition} 
            ORDER BY created_at DESC
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM {$this->table} 
            WHERE 
                category = ? 
                AND product_id != ? 
                {$condition}
        ";

        $products = $this->fetchProducts($productsQuery, [$category, $productId], $page, $limit);
        $total    = $this->countProducts($countQuery, [$category, $productId]);

        return $this->format($products, $total, $page, $limit);
    }

    public function allColors(): ?array
    {
        $sql = "
            SELECT DISTINCT 
                color 
            FROM {$this->table} 
            WHERE 
                color IS NOT NULL 
                AND color != '' 
            ORDER BY color ASC
        ";

        return $this->queryAll($sql);
    }

    public function groupByColor(
        ?int $storeId = null
    ): ?array {

        if ($storeId === null) {

            // Global View: Show All Distinct Product Colors
            $query = "
                SELECT 
                    p.color,
                    COUNT(p.product_id) AS product_count
                FROM {$this->table} p
                WHERE 
                    p.color IS NOT NULL 
                    AND p.color != ''
                GROUP BY p.color
                ORDER BY p.color ASC
            ";

            return $this->queryAll($query);

        } else {

            // Store View: Only Colors That Exist In This Store
            $query = "
                SELECT 
                    p.color,
                    COUNT(p.product_id) AS product_count
                FROM {$this->table} p
                WHERE 
                    p.color IS NOT NULL 
                    AND p.color != ''
                    AND p.store_id = ?
                GROUP BY p.color
                HAVING product_count > 0
                ORDER BY p.color ASC
            ";

            return $this->queryAll($query, [$storeId]);
        }
    }

    public function decrementStock(
        int $productId, 
        int $quantity
    ): bool {

        $product = $this->query()
            ->select(['stock'])
            ->where('product_id', '=', $productId)
            ->first();

        if (!$product) {
            throw new \RuntimeException("Product not found.");
        }

        $currentQty = (int)$product['stock'];
        $newQty     = max(0, $currentQty - $quantity);

        return $this->query()
            ->where('product_id', '=', $productId)
            ->update(['stock' => $newQty]);
    }

    public function addReview(
        int $userId, 
        int $productId, 
        string $review, 
        int $rating
    ): bool {

        $reviewQuery = "
            INSERT INTO reviews (user_id, product_id, comment, rating) 
            VALUES (?, ?, ?, ?)
        ";

        return $this->executeQuery(
            $reviewQuery,
            [$userId, $productId, $review, $rating]
        );
    }

    public function countProductsByType(
        ?int $userId = null, 
        ?int $storeId = null, 
        ?string $status = null, 
        string $role = 'vendor'
    ): int {

        $sql = "
            SELECT 
                COUNT(*)
            FROM {$this->table} 
            WHERE 
                1
        ";

        $params = [];

        // Vendor Mode: Restrict By Vendor's User ID
        if ($role === 'vendor' && !is_null($userId)) {
            $sql .= " AND store_id IN (SELECT store_id FROM stores WHERE user_id = ?)";
            $params[] = $userId;
        }

        // Optional Store Filter
        if (!is_null($storeId)) {
            $sql .= " AND store_id = ?";
            $params[] = $storeId;
        }

        // Optional Status/Visibility Filter
        if (!is_null($status)) {
            $sql .= " AND visibility = ?";
            $params[] = $status;
        }

        $total = $this->fetchColumn($sql, $params);

        return (int) ($total !== false ? $total : 0);
    }

    public function getVendorProductStats(
        int $userId
    ): ?array {

        return [
            'active'  => $this->countProductsByType($userId, null, 'Visible', 'vendor'),
            'pending' => $this->countProductsByType($userId, null, 'Hidden', 'vendor'),
        ];
    }

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
    public function countReviewsByVendor(
        ?int $userId = null, 
        ?int $storeId = null
    ): int {

        if (is_null($userId)) {
            return 0;
        }

        $sql = "
            SELECT 
                COALESCE(COUNT(r.review_id), 0) AS total_reviews
            FROM reviews AS r
            INNER JOIN products AS p ON p.product_id = r.product_id
            INNER JOIN stores AS s ON s.store_id = p.store_id
            WHERE 
                s.user_id = ?
        ";

        $params = [$userId];

        // ✅ Filter By Specific Store If Provided
        if (!is_null($storeId)) {
            $sql .= " AND s.store_id = ?";
            $params[] = $storeId;
        }

        $result = $this->fetchColumn($sql, $params);

        return (int) ($result !== false ? $result : 0);
    }
}
