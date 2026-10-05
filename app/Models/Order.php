<?php

declare(strict_types=1);

namespace App\Models;

class Order extends Model
{
    protected string $table = 'orders';

    public function createOrder(
        float $subtotal, 
        float $tax, 
        float $discount, 
        float $shipping, 
        float $total, 
        string $address, 
        array $items,
        int $userId
    ): array {

        // Generate Order Tracking Code
        $orderCode = $this->generateOrderCode();

        // Create Order
        $orderId = $this->table()
            ->insertGetId([
                'user_id'          => $userId,
                'subtotal_amount'  => $subtotal,
                'tax_amount'       => $tax,
                'discount_amount'  => $discount,
                'shipping_amount'  => $shipping,
                'total_amount'     => $total,
                'shipping_address' => $address,
                'tracking_code'    => $orderCode,
                'facilitator_id'   => $userId  // This could be the referrer's ID 
            ]);

        if (!$orderId) {
            throw new \Exception(
                'Failed to create order ID'
            );
        }

        // Track Stores
        $stores = [];

        foreach ($items as $item) {

            $productId = $item['product_id'];
            $quantity  = $item['quantity'];
            $price     = $item['price'];
            $storeId   = $item['store_id'];

            // Lock the product row to prevent concurrency
            $product = $this->table('products')
                ->where('product_id', '=', $productId)
                ->lockForUpdate()
                ->first();

            if (!$product) {
                throw new \Exception("Product #{$productId} not found");
            }

            // Check stock while the row is locked
            if ($product['stock'] < $quantity) {
                throw new \Exception("Insufficient stock for product #{$productId}");
            }

            // Update stock (reduce)
            $stockUpdated = $this->table('products')
                ->where('product_id', '=', $productId)
                ->decrement('stock', $quantity);

            if (!$stockUpdated) {
                throw new \Exception("Failed to update stock for product #{$productId}");
            }

            // Generate item tracking code
            $itemCode = $this->generateItemCode();

            // Create Order
            $itemSaved = $this->table('order_items')
                ->insert([
                    'order_id'      => $orderId,
                    'product_id'    => $productId,
                    'quantity'      => $quantity,
                    'price'         => $price,
                    'store_id'      => $storeId,
                    'tracking_code' => $itemCode,
                ]);

            // Check item saved status
            if (!$itemSaved) {
                throw new \Exception("Failed to save order item: #{$productId}");
            }

            // Calculate Item Total = Price * Quantity
            $itemTotal = $price * $quantity;

            // Aggregate Per Store
            if (!isset($stores[$storeId])) {

                $stores[$storeId] = [
                    'store_id' => $storeId,
                    'total' => 0
                ];
            }

            $stores[$storeId]['total'] += $itemTotal;
        }

        // Return OrderId And Unique Store Details
        $orderData = [
            'id'     => $orderId,
            'code'   => $orderCode,
            'stores' => array_values($stores) // reset keys
        ];

        return $orderData;
    }

    public function trackOrder(
        int $userId, 
        string $code
    ): ?array {

        return $this->table()
            ->where('user_id', '=', $userId)
            ->where('tracking_code', '=', $code)
            ->first();
    }

    public function getOrder(
        int $orderId
    ): ?array {

        $order = $this->table()
            ->where('order_id', '=', $orderId)
            ->first();

        if (
            $order 
            && is_array($order)
        ) {
            $order['items'] = $this->getOrderItems($orderId);
        }

        return $order;
    }

   public function getOrderItems(
        int $orderId
    ): ?array {

        // ----------------------------------------
        // Find the first image for each product
        // ----------------------------------------
        $firstMedia = $this->table('product_media')
            ->select([
                'product_id',
                'MIN(media_id) AS first_media_id',
            ])
            ->groupBy('product_id');

        // ----------------------------------------
        // Fetch order items
        // ----------------------------------------
        return $this->table('order_items AS oi')
            ->select([
                'oi.*',
                'p.product_name',
                'pm.media_url AS product_image',
            ])
            ->join(
                'products AS p',
                'p.product_id',
                '=',
                'oi.product_id'
            )
            ->leftJoinSub(
                $firstMedia,
                'pm_first',
                'pm_first.product_id',
                '=',
                'oi.product_id'
            )
            ->leftJoin(
                'product_media AS pm',
                'pm.media_id',
                '=',
                'pm_first.first_media_id'
            )
            ->where(
                'oi.order_id',
                '=',
                $orderId
            )
            ->get();
    }

    /**
     * Fetch order items with product info and first image
     * Supports pagination and optional custom SQL
     *
     * @param string|null $sql - Optional custom SQL for order_items
     * @param array $params - Bindings for the custom SQL
     * @param int $page - Page number for pagination
     * @param int $limit - Number of items per page
     * @return array|null
    */

    private function processOrders(
        int $page = 1,
        int $limit = 20
    ): ?array {

        // ----------------------------------------
        // First product image
        // ----------------------------------------
        $firstMedia = $this->table('product_media')
            ->select([
                'product_id',
                'MIN(media_id) AS first_media_id',
            ])
            ->groupBy('product_id');

        // ----------------------------------------
        // Fetch orders with product information
        // ----------------------------------------
        return $this->table('order_items AS oi')
            ->select([
                'oi.*',
                'p.product_name',
                'p.stock',
                'pm.media_url AS product_image',
            ])
            ->join(
                'products AS p',
                'p.product_id',
                '=',
                'oi.product_id',
                'LEFT'
            )
            ->leftJoinSub(
                $firstMedia,
                'first_media',
                'first_media.product_id',
                '=',
                'p.product_id'
            )
            ->leftJoin(
                'product_media AS pm',
                'pm.media_id',
                '=',
                'first_media.first_media_id'
            )
            ->latest('oi.item_id')
            ->paginate($page, $limit)
            ->get();
    }

    public function getAllOrders(
        int $page = 1, 
        int $limit = 20
    ): array {

        $orders = $this->table()
            ->latest()
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->count();

        return $this->format($orders, $total, $page, $limit);
    }

    public function getOrdersByStatus(
        ?string $status = null, 
        int $page = 1, 
        int $limit = 20
    ): array {

        $orders = $this->table()
            ->where('order_status', '=', $status)
            ->latest('order_id')
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->where('order_status', '=', $status)
            ->count();

        return $this->format($orders, $total, $page, $limit);
    }

    public function getUserOrders(
        ?int $userId = null, 
        int $page = 1, 
        int $limit = 20
    ): array {

        $orders = $this->table()
            ->where('user_id', '=', $userId)
            ->where('order_status', '!=', 'Cancelled')
            ->latest()
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->where('user_id', '=', $userId)
            ->count();

        return $this->format($orders, $total, $page, $limit);
    }

    public function getStoreOrders(
        ?int $storeId = null, 
        int $page = 1, 
        int $limit = 20
    ): array {

        $orders = $this->table('order_items')
            ->where('store_id', '=', $storeId)
            ->latest('item_id')
            ->paginate($page, $limit)
            ->get();

        $total = $this->table('order_items')
            ->where('store_id', '=', $storeId)
            ->count();

        return $this->format($orders, $total, $page, $limit);
    }

    public function getStoreOrdersByStatus(
        ?int $storeId = null, 
        ?string $status = null, 
        int $page = 1, 
        int $limit = 20
    ): array {

        $orders = $this->table('order_items')
            ->where('store_id', '=', $storeId)
            ->where('item_status', '=', $status)
            ->latest('item_id')
            ->paginate($page, $limit)
            ->get();

        $total = $this->table('order_items')
            ->where('store_id', '=', $storeId)
            ->where('item_status', '=', $status)
            ->count();

        return $this->format($orders, $total, $page, $limit);
    }

    public function updateItemStatus(
        int $itemId, 
        string $status
    ): bool {

        $existing = $this->table('order_items')
            ->where('item_id', '=', $itemId)
            ->lockForUpdate()
            ->first();

        if (!$existing) {
            throw new \Exception("Item #{$itemId} not found");
        }

        return $this->table('order_items')
            ->where('item_id', '=', $itemId)
            ->update([
                'item_status' => $status,
            ]);
    }

    public function updateItemFinalizedStatus(
        int $itemId, 
        string $status
    ): bool {

        $existing = $this->table('order_items')
            ->where('item_id', '=', $itemId)
            ->lockForUpdate()
            ->first();

        if (!$existing) {
            throw new \Exception("Item #{$itemId} not found");
        }

        return $this->table('order_items')
            ->where('item_id', '=', $itemId)
            ->update([
                'finalized' => $status,
            ]);
    }

    public function updateOrderStatus(
        int $orderId, 
        string $status
    ): bool {

        $existing = $this->table()
            ->where('order_id', '=', $orderId)
            ->lockForUpdate()
            ->first();

        if (!$existing) {
            throw new \Exception("Order #{$orderId} not found");
        }

        return $this->table()
            ->where('order_id', '=', $orderId)
            ->update([
                'order_status' => $status
            ]);
    }

    public function completeOrder(
        int $orderId
    ): bool {

        return $this->updateOrderStatus($orderId, 'Completed');
    }

    public function cancelOrder(
        int $orderId
    ): bool {

        return $this->updateOrderStatus($orderId, 'Cancelled');
    }

    public function deleteOrder(
        int $orderId
    ): bool {

        return $this->table()
            ->where('order_id', '=', $orderId)
            ->delete();
    }

    public function deleteOrderItems(
        int $orderId
    ): bool {

        return $this->table('order_items')
            ->where('order_id', '=', $orderId)
            ->delete();
    }

    public function deleteOrderPayment(
        int $orderId
    ): bool {

        return $this->table('payments')
            ->where('order_id', '=', $orderId)
            ->delete();
    }

    public function getOrderTotal(
        int $orderId
    ): int {

        $result = $this->table()
            ->select(['total_amount'])
            ->where('order_id', '=', $orderId)
            ->first();

        return $result ? $result['total_amount'] : 0;
    }

    public function getOrderDetails(
        int $orderId
    ): ?array {

        return $this->table()
            ->select([
                'total_amount',
                'tracking_code',
                'created_at'
            ])
            ->where('order_id', '=', $orderId)
            ->get();
    }

    public function getOrderStores(
        int $orderId
    ): ?array {

        return $this->table('order_items')
            ->select([
                'store_id'
            ])
            ->distinct()
            ->where('order_id', '=', $orderId)
            ->oldest('store_id')
            ->get();
    }

    public function getItemDetails(
        int $itemId
    ): ?array {

        return $this->table('order_items')
            ->where('item_id', '=', $itemId)
            ->first();
    }

    public function getUserByOrderId(
        int $orderId
    ): ?int {

        $result = $this->table()
            ->select(['user_id'])
            ->where('order_id', '=', $orderId)
            ->first();

        return $result ? (int) $result['user_id'] : null;
    }

    /**
     * Total Revenue (Sum of all delivered items)
     *
     * @param int $userId  - Owner of the store(s)
     * @param int|null $storeId - Optional specific store ID
     * @return float
     */

    public function getTotalRevenue(
        ?int $userId = null, 
        ?int $storeId = null
    ): float {

        $total = $this->table('order_items AS oi')
            ->select([
                'COALESCE(SUM(oi.price * oi.quantity), 0) AS total_revenue',
            ])
            ->join(
                'stores AS s',
                's.store_id',
                '=',
                'oi.store_id',
            )
            // Vendor mode: filter by vendor’s user ID
            ->when(
                $userId !== null,
                function ($query) use ($userId) {
                    $query->where('s.user_id', '=', $userId);
                }
            )
            // Store-specific mode (for both admin + vendor)
            ->when(
                $storeId !== null,
                function ($query) use ($storeId) {
                    $query->where('oi.store_id', '=', $storeId);
                }
            )
            ->count();

            return (float) $total ?? 0;
    }

    /**
     * Total Orders by Status
     *
     * @param int $userId  - Owner of the store(s)
     * @param string $status - Order item status (e.g., 'Pending', 'Delivered', etc.)
     * @param int|null $storeId - Optional specific store ID
     * @return int
     */

    public function getTotalOrdersByStatus(
        ?int $userId = null, 
        ?string $status = null, 
        ?int $storeId = null
    ): int {

        $total = $this->table('order_items AS oi')
            ->select([
                'COALESCE(COUNT(oi.item_id), 0) AS total_orders',
            ])
            ->join(
                'stores AS s',
                's.store_id',
                '=',
                'oi.store_id',
            )
            // ------------------------------------------
            // Vendor mode: filter by vendor's user_id
            // Admin mode: skip this (admin sees all)
            // ------------------------------------------
            ->when(
                $userId !== null,
                function ($query) use ($userId) {
                    $query->where('s.user_id', '=', $userId);
                }
            )
            // ------------------------------------------
            // Status filter (Delivered, Pending, etc.)
            // Optional
            // ------------------------------------------
            ->when(
                $status !== null,
                function ($query) use ($status) {
                    $query->where('oi.item_status', '=', $status);
                }
            )
            // ------------------------------------------
            // Store filter (for admin + vendor)
            // Optional
            // ------------------------------------------
            ->when(
                $storeId !== null,
                function ($query) use ($storeId) {
                    $query->where('oi.store_id', '=', $storeId);
                }
            )
            ->count();

            return (int) $total ?? 0;
    }

    /**
     * Total Unique Customers (for all stores or a specific store)
     *
     * @param int $userId   - Vendor (store owner) ID
     * @param int|null $storeId - Optional specific store ID
     * @return int
    */
    public function countUniqueCustomers(
        ?int $userId = null, 
        ?int $storeId = null, 
        string $role = 'vendor'
    ): int {

        $total = $this->table('order_items AS oi')
            ->select([
                'COALESCE(COUNT(DISTINCT o.user_id), 0) AS unique_customers',
            ])
            ->join(
                'stores AS s',
                's.store_id',
                '=',
                'oi.store_id',
            )
            ->join(
                'orders AS o',
                'o.order_id',
                '=',
                'oi.order_id',
            )
            // Vendor mode → restrict by vendor's user_id
            ->when(
                $userId !== null
                && $role === 'vendor',
                function ($query) use ($userId) {
                    $query->where('s.user_id', '=', $userId);
                }
            )
            // Store-specific mode (for both admin + vendor)
            ->when(
                $storeId !== null,
                function ($query) use ($storeId) {
                    $query->where('oi.store_id', '=', $storeId);
                }
            )
            ->count();

            return (int) $total ?? 0;
    }

    public function generateOrderCode(): string
    {
       return '#Order-' . bin2hex(random_bytes(10));
    }

    public function generateItemCode(): string
    {
        return '#Item-' . bin2hex(random_bytes(10));
    }

    public function getVendorOrderStats(
        int $userId
    ): array {

        return [
            'total_revenue'    => $this->getTotalRevenue($userId),
            'pending_orders'   => $this->getTotalOrdersByStatus($userId, 'Pending'),
            'shipped_orders'   => $this->getTotalOrdersByStatus($userId, 'Shipped'),
            'delivered_orders' => $this->getTotalOrdersByStatus($userId, 'Delivered'),
            'unique_customers' => $this->countUniqueCustomers($userId, null, 'vendor'),
        ];
    }

    public function getVendorStoreStats(
        int $userId, 
        int $storeId
    ): array {

        return [
            'total_revenue'    => $this->getTotalRevenue($userId, $storeId),
            'pending_orders'   => $this->getTotalOrdersByStatus($userId, 'Pending', $storeId),
            'shipped_orders'   => $this->getTotalOrdersByStatus($userId, 'Shipped', $storeId),
            'delivered_orders' => $this->getTotalOrdersByStatus($userId, 'Delivered', $storeId),
            'unique_customers' => $this->countUniqueCustomers($userId, $storeId, 'vendor'),
        ];
    }

    public function getAdminOrderStats(): array
    {
        return [
            'total_revenue'    => $this->getTotalRevenue(),
            'pending_orders'   => $this->getTotalOrdersByStatus(null, 'Pending'),
            'shipped_orders'   => $this->getTotalOrdersByStatus(null, 'Shipped'),
            'delivered_orders' => $this->getTotalOrdersByStatus(null, 'Delivered'),
            'unique_customers' => $this->countUniqueCustomers(null, null, 'admin'),
        ];
    }

    /**
     * Fetch sales and revenue for both vendors and admins
     * For each vendor, it also genertes per-store sales and revenue summary
     * This is useful for a dashboard usage
    */
    // Get dates
    private function getDateRange(
        string $timeframe = 'today', 
        string $startDate = null, 
        string $endDate = null
    ) {
        $today = new DateTime();

        switch ($timeframe) {
            case 'today':
                $start = $today->format('Y-m-d 00:00:00');
                $end   = $today->format('Y-m-d 23:59:59');
                break;

            case 'yesterday':
                $yesterday = (clone $today)->modify('-1 day');
                $start     = $yesterday->format('Y-m-d 00:00:00');
                $end       = $yesterday->format('Y-m-d 23:59:59');
                break;

            case 'last_week':
                $start = (clone $today)->modify('monday last week')->format('Y-m-d 00:00:00');
                $end   = (clone $today)->modify('sunday last week')->format('Y-m-d 23:59:59');
                break;

            case 'last_month':
                $start = (clone $today)->modify('first day of last month')->format('Y-m-d 00:00:00');
                $end   = (clone $today)->modify('last day of last month')->format('Y-m-d 23:59:59');
                break;

            case 'last_year':
                $start = (clone $today)->modify('first day of January last year')->format('Y-m-d 00:00:00');
                $end   = (clone $today)->modify('last day of December last year')->format('Y-m-d 23:59:59');
                break;

            case 'custom':
                if (!$startDate || !$endDate) {
                    throw new Exception("Custom timeframe requires start and end dates.");
                }

                $start = $startDate . ' 00:00:00';
                $end   = $endDate . ' 23:59:59';
                break;

            default:
                // fallback to today
                $start     = $today->format('Y-m-d 00:00:00');
                $end       = $today->format('Y-m-d 23:59:59');
                $timeframe = 'today';
        }

        return [$start, $end, $timeframe];
    }

    // Generate sales summary data
    public function getSalesAndRevenue(
        string $view = 'admin', 
        ?int $userId = null, 
        string $timeframe = 'today', 
        string $startDate = null, 
        string $endDate = null
    ): array {

        list($start, $end, $resolvedTimeframe) = $this->getDateRange($timeframe, $startDate, $endDate);

        $params = [$start, $end];

        if ($view === 'admin') {

            // --- Admin: Global Totals --- //
            $result = $this->table('order_items oi')
                ->select([
                    'COUNT(*) AS total_sales',
                    'SUM(oi.price) AS total_revenue',
                ])
                ->whereBetween('oi.created_at', [$start, $end])
                ->first();

            return [
                'view'          => $view,
                'total_sales'   => (int)($result['total_sales'] ?? 0),
                'total_revenue' => (float)($result['total_revenue'] ?? 0),
                'timeframe'     => $resolvedTimeframe,
                'range'         => ['start' => $start, 'end' => $end]
            ];

        } elseif ($view === 'vendor') {

            if (!$userId) {
                throw new Exception("Vendor view requires user ID.");
            }

            // --- Vendor Totals (All Stores Combined) --- //
            $summary = $this->table('order_items AS oi')
                ->select([
                    'COUNT(*) AS total_sales',
                    'SUM(oi.price) AS total_revenue',
                ])
                ->join(
                    'stores AS s',
                    's.store_id',
                    '=',
                    'oi.store_id',
                )
                // Vendor mode → restrict by vendor's user_id
                ->when(
                    $userId !== null,
                    function ($query) use ($userId) {
                        $query->where('s.user_id', '=', $userId);
                    }
                )
                ->whereBetween('oi.created_at', [$start, $end])
                ->first();

            // --- Store Breakdown --- //
            $stores = $this->table('order_items AS oi')
                ->select([
                    's.store_id AS store_id',
                    's.store_name',
                    'COUNT(*) AS total_sales',
                    'SUM(oi.price) AS total_revenue'
                ])
                ->join(
                    'stores AS s',
                    's.store_id',
                    '=',
                    'oi.store_id',
                )
                // Vendor mode → restrict by vendor's user_id
                ->when(
                    $userId !== null,
                    function ($query) use ($userId) {
                        $query->where('s.user_id', '=', $userId);
                    }
                )
                ->whereBetween('oi.created_at', [$start, $end])
                ->groupBy([
                    's.store_id',
                    's.store_name',
                ])
                ->latest('total_revenue')
                ->get();

            return [
                'view'          => $view,
                'total_sales'   => (int)($summary['total_sales'] ?? 0),
                'total_revenue' => (float)($summary['total_revenue'] ?? 0),
                'timeframe'     => $resolvedTimeframe,
                'range'         => ['start' => $start, 'end' => $end],
                'stores'        => array_map(function($store) {
                    return [
                        'store_id'      => (int)$store['store_id'],
                        'store_name'    => $store['store_name'],
                        'total_sales'   => (int)($store['total_sales'] ?? 0),
                        'total_revenue' => (float)($store['total_revenue'] ?? 0)
                    ];
                }, $stores)
            ];

        } else {
            throw new Exception("Invalid view type: must be 'admin' or 'vendor'.");
        }
    }

    /**
     * Fetch sales and revenue for both vendors and admins
     * For each vendor, it also genertes store-wide sales and revenue summary
     * This is useful for a dashboard usage
    */
    public function getSalesAndRevenueByPeriod(
        string $view = 'admin', 
        ?int $userId = null, 
        ?int $storeId = null, 
        ?string $period = 'today', 
        ?string $startDate = null, 
        ?string $endDate = null
    ): array {

        $baseCondition = "item_status IN ('Shipped', 'Delivered')";
        $conditions    = [];
        $params        = [];

        // --- Role-based Filtering --- //
        if ($view === 'admin') {
            $conditions[] = '1'; // no restriction
        } elseif (
            $view === 'vendor' 
            && $userId !== null
        ) {
            $conditions[] = 'store_id IN (SELECT store_id FROM stores WHERE user_id = ?)';
            $params[]     = $userId;
        }

        // --- Optional Store Filter --- //
        if ($storeId !== null) {
            $conditions[] = 'store_id = ?';
            $params[]     = $storeId;
        }

        // --- Date filtering --- //
        switch ($period) {
            case 'today':
                $conditions[] = 'DATE(created_at) = CURDATE()';
                break;
            case 'yesterday':
                $conditions[] = 'DATE(created_at) = CURDATE() - INTERVAL 1 DAY';
                break;
            case 'last_week':
                $conditions[] = 'YEARWEEK(created_at, 1) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK, 1)';
                break;
            case 'last_month':
                $conditions[] = 'YEAR(created_at) = YEAR(CURDATE() - INTERVAL 1 MONTH) 
                                AND MONTH(created_at) = MONTH(CURDATE() - INTERVAL 1 MONTH)';
                break;
            case 'last_year':
                $conditions[] = 'YEAR(created_at) = YEAR(CURDATE() - INTERVAL 1 YEAR)';
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $conditions[] = 'DATE(created_at) BETWEEN ? AND ?';
                    $params[]     = $startDate;
                    $params[]     = $endDate;
                } else {
                    throw new InvalidArgumentException('Custom range requires start_date and end_date');
                }
                break;
        }

        // --- Merge Conditions --- //
        $allConditions = array_merge([$baseCondition], $conditions);

        // --- Build Query --- //
        $sql = "
            SELECT 
                COUNT(DISTINCT order_id) AS total_orders,
                SUM(quantity) AS total_items_sold,
                SUM(price) AS total_revenue
            FROM order_items
            WHERE 
        " . implode(' AND ', $allConditions);

        // --- Execute --- //
        $result = $this->queryOne($sql, $params);

        // --- Return clean values --- //
        return [
            'total_orders'     => (int)($result['total_orders'] ?? 0),
            'total_items_sold' => (int)($result['total_items_sold'] ?? 0),
            'total_revenue'    => (float)($result['total_revenue'] ?? 0)
        ];
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
            'orders'      => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $limit,
            'total_pages' => ceil($total / $limit),
        ];
    }
}
