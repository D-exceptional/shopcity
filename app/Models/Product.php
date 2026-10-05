<?php

declare(strict_types=1);

namespace App\Models;

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

        return $this->table()
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

        return $this->table()
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

        return $this->table()
            ->where('product_id', '=', $productId)
            ->update([
                'visibility'  => $visibility,
                'is_featured' => $isFeatured,
            ]);
    }

    public function delete(
        int $productId
    ): bool {

        return $this->table()
            ->where('product_id', '=', $productId)
            ->delete();
    }

    public function find(
        int $productId,
        bool $transaction = false
    ): ?array {

        if (!$transaction) {

            return $this->table()
                ->where('product_id', '=', $productId)
                ->lockForUpdate()
                ->first();
        } 

        return $this->table()
            ->where('product_id', '=', $productId)
            ->first();
    }
    
    private function buildProducts(
        array $products
    ): ?array {

        if (empty($products)) return [];

        $productIds       = array_column($products, 'product_id');
        $mediaByProduct   = [];
        $ratingsByProduct = [];

        // ------------ FETCH ONE MEDIA PER PRODUCT ----------- //
        $firstMedia = $this->table('product_media')
            ->select([
                'product_id',
                'MIN(media_id) AS first_media_id'
            ])
            ->whereIn('product_id', $productIds)
            ->groupBy(['product_id']);

        $allMedia = $this->table('product_media AS pm')
            ->joinSub(
                $firstMedia,
                'first_media',
                'pm.media_id',
                '=',
                'first_media.first_media_id'
            )
            ->get();

        foreach ($allMedia as $media) {
            $mediaByProduct[$media['product_id']][] = $media; 
        }

        // --- Fetch Ratings --- //
        $allRatings = $this->table('reviews')
            ->select([
                'product_id',
                'AVG(rating) AS average_rating',
                'COUNT(*) AS rating_count',
            ])
            ->whereIn('product_id', $productIds)
            ->groupBy(['product_id'])
            ->get();

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

    public function findByAll(
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): array {

        $data = $this->table()
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->latest()
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->count();

        $products = $this->buildProducts($data);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByCategory(
        ?string $category = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): array {

        $data = $this->table()
            ->where('category', '=', $category)
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->latest()
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->where('category', '=', $category)
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->count();

        $products = $this->buildProducts($data);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByStore(
        int $storeId = 0, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): array {

        $data = $this->table()
            ->where('store_id', '=', $storeId)
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->latest()
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->where('store_id', '=', $storeId)
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->count();

        $products = $this->buildProducts($data);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByStoreCategory(
        ?int $storeId = null, 
        ?string $category = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): array {

        $data = $this->table()
            ->where('store_id', '=', $storeId)
            ->where('category', '=', $category)
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->latest()
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->where('store_id', '=', $storeId)
            ->where('category', '=', $category)
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->count();

        $products = $this->buildProducts($data);

        return $this->format($products, $total, $page, $limit);
    }

    public function findNewArrivals(
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): array {

        $data = $this->table()
            ->whereRaw('created_at >= NOW() - INTERVAL 7 DAY')
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->latest()
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->whereRaw('created_at >= NOW() - INTERVAL 7 DAY')
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->count();

        $products = $this->buildProducts($data);

        return $this->format($products, $total, $page, $limit);
    }

    public function findFeatured( 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): array {

        $data = $this->table()
            ->where('is_featured', '=', 'TRUE')
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->latest()
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->where('is_featured', '=', 'TRUE')
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->count();

        $products = $this->buildProducts($data);

        return $this->format($products, $total, $page, $limit);
    }

    public function findTopSelling(
        int $page = 1,
        int $limit = 20
    ): ?array {

        // =========================================
        // TOP SELLING PRODUCTS QUERY
        // =========================================
        $data = $this->table('products AS p')
            ->select([
                'p.*',
                'SUM(oi.quantity) AS total_quantity_sold',
            ])
            ->join(
                'order_items AS oi',
                'oi.product_id',
                '=',
                'p.product_id'
            )
            ->groupBy('p.product_id')
            ->having(
                'SUM(oi.quantity)',
                '>',
                100
            )
            ->orderBy(
                'total_quantity_sold',
                'DESC'
            )
            ->paginate($page, $limit)
            ->get();

        $products = $this->buildProducts($data);

        // =========================================
        // TOTAL TOP SELLING PRODUCTS
        // =========================================
        $topSellingProducts = $this->table('order_items')
            ->select('product_id')
            ->groupBy('product_id')
            ->having(
                'SUM(quantity)',
                '>',
                100
            )
            ->get();

        $total = count($topSellingProducts);

        // =========================================
        // FORMAT RESPONSE
        // =========================================

        return $this->format(
            $products,
            $total,
            $page,
            $limit
        );
    }

    public function findByPriceRange(
        ?float $min = null, 
        ?float $max = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): array {

        $data = $this->table()
            ->whereBetween('product_price', [$min, $max])
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->latest()
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->whereBetween('product_price', [$min, $max])
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->count();

        $products = $this->buildProducts($data);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByMinPrice(
        ?float $min = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): array {

        $data = $this->table()
            ->where('product_price', '>=', $min)
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->oldest('product_price')
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->where('product_price', '>=', $min)
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->count();

        $products = $this->buildProducts($data);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByMaxPrice(
        ?float $max = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): array {

        $data = $this->table()
            ->where('product_price', '<=', $max)
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->oldest('product_price')
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->where('product_price', '<=', $max)
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->count();

        $products = $this->buildProducts($data);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByGroupedCategory(
        int $page = 1, 
        int $perCategory = 10, 
        string $view = 'customer'
    ): array {

        $products = $this->table()
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->oldest('category')
            ->limit(1000)
            ->get();

        $productIds       = array_column($products, 'product_id');
        $mediaByProduct   = [];
        $ratingsByProduct = [];
        $grouped          = [];
        $categoryCounters = [];

        if (!empty($productIds)) {

            // ------------ FETCH ONE MEDIA PER PRODUCT ----------- //
            $firstMedia = $this->table('product_media')
                ->select([
                    'product_id',
                    'MIN(media_id) AS first_media_id'
                ])
                ->whereIn('product_id', $productIds)
                ->groupBy(['product_id']);

            $allMedia = $this->table('product_media AS pm')
                ->joinSub(
                    $firstMedia,
                    'first_media',
                    'pm.media_id',
                    '=',
                    'first_media.first_media_id'
                )
                ->get();

            foreach ($allMedia as $media) {
                $mediaByProduct[$media['product_id']][] = $media;
            }

            $allRatings = $this->table('reviews')
                ->select([
                    'product_id',
                    'AVG(rating) AS average_rating',
                    'COUNT(*) AS rating_count',
                ])
                ->whereIn('product_id', $productIds)
                ->groupBy(['product_id'])
                ->get();

            foreach ($allRatings as $rating) {

                $ratingsByProduct[$rating['product_id']] = [
                    'average' => round((float)$rating['average_rating'], 2),
                    'count'   => (int)$rating['rating_count']
                ];
            }
        }

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

        // ----------- Fetch Product ----------- //
        $product = $this->table()
            ->where('product_id', '=', $productId)
            ->first();

        if ($product === false) {
            return false;
        }

        // ----------- Attach Media ----------- //
        $product['media'] = $this->table('product_media')
            ->where('product_id', '=', $productId)
            ->get();

        // ----------- Attach Reviews ----------- //
        $reviews = $this->table('reviews r')
            ->select([
                'r.review_id',
                'r.comment',
                'r.rating',
                'r.created_at',
                'u.user_id',
                'u.firstname AS first_name',
                'u.lastname AS last_name',
                'u.avatar',
            ])
            ->join(
                'users u',
                'r.user_id',
                '=',
                'u.user_id',
            )
            ->when(
                $productId !== null,

                function ($query) use ($productId) {
                    $query->where('r.product_id', '=', $productId);
                }
            )
            ->orderBy('r.created_at', 'DESC')
            ->get();

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
    ): array {

        // Add Filtering Based On View
        $likeQuery = '%' . $query . '%';

        $data = $this->table()
            ->whereLike('product_name', $likeQuery)
            ->orWhere('product_description', 'LIKE', $likeQuery)
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->latest()
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->whereLike('product_name', $likeQuery)
            ->orWhere('product_description', 'LIKE', $likeQuery)
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->count();

        $products = $this->buildProducts($data);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByColor(
        ?string $color = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): array {

        $data = $this->table()
            ->where('color', '=', $color)
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->latest()
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->where('color', '=', $color)
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->count();

        $products = $this->buildProducts($data);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByStoreColor(
        ?int $storeId = null, 
        ?string $color = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): array {

        $data = $this->table()
            ->where('store_id', '=', $storeId)
            ->where('color', '=', $color)
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->latest()
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->where('store_id', '=', $storeId)
            ->where('color', '=', $color)
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->count();

        $products = $this->buildProducts($data);

        return $this->format($products, $total, $page, $limit);
    }

    public function findByRelated(
        ?string $category = null, 
        ?int $productId = null, 
        int $page = 1, 
        int $limit = 20, 
        string $view = 'customer'
    ): array {

        $data = $this->table()
            ->where('category', '=', $category)
            ->whereNotIn('product_id', [ $productId ])
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->latest()
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->where('category', '=', $category)
            ->whereNotIn('product_id', [ $productId ])
            ->when(
                $view === 'customer',

                fn($query) =>
                    $query->where('visibility', '=', 'Visible')
            )
            ->count();

        $products = $this->buildProducts($data);

        return $this->format($products, $total, $page, $limit);
    }

    public function allColors(): ?array
    {
        return $this->table()
            ->select([
                'color',
            ])
            ->distinct()
            ->whereNotNull('color')
            ->oldest('color')
            ->get();
    }

   public function groupByColor(
        ?int $storeId = null
    ): ?array {

        return $this->table('products p')
            ->select([
                'p.color',
                'COUNT(p.product_id) AS product_count',
            ])
            ->whereNotNull('p.color')
            ->when(
                $storeId !== null,
                function ($query) use ($storeId) {
                    $query->where(
                        'p.store_id',
                        '=',
                        $storeId
                    );
                }
            )
            ->groupBy([
                'p.color',
            ])
            ->oldest(
                'p.color',
            )
            ->get();
    }

    public function decrementStock(
        int $productId, 
        int $quantity
    ): bool {

        $product = $this->table()
            ->select(['stock'])
            ->where('product_id', '=', $productId)
            ->first();

        if (!$product) {
            throw new \RuntimeException("Product not found.");
        }

        $currentQty = (int)$product['stock'];
        $newQty     = max(0, $currentQty - $quantity);

        return $this->table()
            ->where('product_id', '=', $productId)
            ->update(['stock' => $newQty]);
    }

    public function addReview(
        int $userId, 
        int $productId, 
        string $review, 
        int $rating
    ): bool {

        return $this->table('reviews')
            ->insert([
                'user_id'    => $userId,
                'product_id' => $productId,
                'comment'    => $comment,
                'rating'     => $rating
            ]);
    }

    public function countProductsByType(
        ?int $userId = null,
        ?int $storeId = null,
        ?string $status = null,
        string $role = 'vendor'
    ): int {

        return $this->table('products AS p')
            ->when(
                $role === 'vendor' && $userId !== null,
                function ($query) use ($userId) {
                    $query
                        ->join(
                            'stores AS s',
                            's.store_id',
                            '=',
                            'p.store_id'
                        )
                        ->where(
                            's.user_id',
                            '=',
                            $userId
                        );
                }
            )
            ->when(
                $storeId !== null,
                function ($query) use ($storeId) {
                    $query->where(
                        'p.store_id',
                        '=',
                        $storeId
                    );
                }
            )
            ->when(
                $status !== null,
                function ($query) use ($status) {
                    $query->where(
                        'p.visibility',
                        '=',
                        $status
                    );
                }
            )
            ->count();
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
     * Count All Reviews (for all stores or a specific store)
     *
     * @param int $userId   - Vendor (store owner) ID
     * @param int|null $storeId - Optional specific store ID
     * @return int
     */
    public function countReviewsByVendor(
        ?int $userId = null,
        ?int $storeId = null
    ): int {

        if ($userId === null) {
            return 0;
        }

        return $this->table('reviews AS r')
            ->join(
                'products AS p',
                'p.product_id',
                '=',
                'r.product_id'
            )
            ->join(
                'stores AS s',
                's.store_id',
                '=',
                'p.store_id'
            )
            ->where(
                's.user_id',
                '=',
                $userId
            )
            ->when(
                $storeId !== null,
                function ($query) use ($storeId) {
                    $query->where(
                        's.store_id',
                        '=',
                        $storeId
                    );
                }
            )
            ->count('r.review_id');
    }

    //=========================================
    // PRIVATE HELPER METHODS   
    //=========================================
    private function format(
        array $data, 
        int $total, 
        int $page, 
        int $limit
    ): array {

        return [
            'products'    => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    =>$limit,
            'total_pages' => ceil($total /$limit),
        ];
    }
}