<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

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

        $fetchQuery = "
            SELECT 
                oi.*,
                p.product_name,
                pm.media_url AS product_image
            FROM order_items oi
            JOIN products p ON p.product_id = oi.product_id
            LEFT JOIN (
                SELECT 
                    product_id, 
                    MIN(media_id) AS first_media_id
                FROM product_media
                GROUP BY product_id
            ) pm_first ON pm_first.product_id = oi.product_id
            LEFT JOIN product_media pm ON pm.media_id = pm_first.first_media_id
            WHERE 
                oi.order_id = ?
        ";

        return $this->queryAll($fetchQuery, [$orderId]);
    }

    private function processOrders(
        ?string $sql = null, 
        array $params = [], 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        $offset    = ($page - 1) * $limit;
        $baseQuery = $sql ?: "SELECT * FROM order_items";

        // Attach Product Info And First Image
        $fetchQuery = "
            SELECT 
                oi.*, 
                p.product_name,
                p.stock,
                (
                    SELECT pm.media_url
                    FROM product_media pm 
                    WHERE pm.product_id = p.product_id 
                    ORDER BY pm.media_id ASC 
                    LIMIT 1
                ) AS product_image
            FROM ($baseQuery) AS oi
            LEFT JOIN products p ON oi.product_id = p.product_id
            ORDER BY oi.item_id DESC
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->db->prepare($fetchQuery);

        $i = 1;
        foreach ($params as $param) {
            $type = is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($i++, $param, $type);
        }

        $stmt->bindValue($i++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($i, (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

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

    public function getAllOrders(
        int $page = 1, 
        int $limit = 20
    ): array {

        $orders = $this->table()
            ->orderBy('created_at', 'DESC')
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
            ->orderBy('order_id', 'DESC')
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
            ->orderBy('created_at', 'DESC')
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
            ->orderBy('item_id', 'DESC')
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
            ->orderBy('item_id', 'DESC')
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
            ->orderBy('store_id', 'ASC')
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
     * ✅ Total Revenue (Sum of all delivered items)
     *
     * @param int $userId  - Owner of the store(s)
     * @param int|null $storeId - Optional specific store ID
     * @return float
     */

    public function getTotalRevenue(
        ?int $userId = null, 
        ?int $storeId = null
    ): float {

        /*
        $sql = "
            SELECT 
                COALESCE(SUM(oi.price * oi.quantity), 0) AS total_revenue
            FROM order_items AS oi
            INNER JOIN stores AS s ON s.store_id = oi.store_id
            WHERE 
                oi.item_status = 'Delivered'
        ";

        $params = [];

        // ✅ Vendor mode: filter by vendor’s user ID
        if (!is_null($userId)) {
            $sql .= " AND s.user_id = ?";
            $params[] = $userId;
        }

        // ✅ Store-specific mode (for both admin + vendor)
        if (!is_null($storeId)) {
            $sql .= " AND oi.store_id = ?";
            $params[] = $storeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetchColumn();


        return (float) ($result !== false ? $result : 0);
        */

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
     * ✅ Total Orders by Status
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

        /*
        $sql = "
            SELECT 
                COALESCE(COUNT(oi.item_id), 0) AS total_orders
            FROM order_items AS oi
            INNER JOIN stores AS s ON s.store_id = oi.store_id
            WHERE 
                1
        ";

        $params = [];

        // ------------------------------------------
        // Vendor mode: filter by vendor's user_id
        // Admin mode: skip this (admin sees all)
        // ------------------------------------------
        if (!is_null($userId)) {
            $sql .= " AND s.user_id = ?";
            $params[] = $userId;
        }

        // ------------------------------------------
        // Status filter (Delivered, Pending, etc.)
        // Optional
        // ------------------------------------------
        if (!is_null($status)) {
            $sql .= " AND oi.item_status = ?";
            $params[] = $status;
        }

        // ------------------------------------------
        // Store filter (for admin + vendor)
        // Optional
        // ------------------------------------------
        if (!is_null($storeId)) {
            $sql .= " AND oi.store_id = ?";
            $params[] = $storeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetchColumn();

        return (int) ($result !== false ? $result : 0);
        */

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

            return (float) $total ?? 0;
    }

    /**
     * ✅ Total Unique Customers (for all stores or a specific store)
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

        /*
        $sql = "
            SELECT 
                COALESCE(COUNT(DISTINCT o.user_id), 0) AS unique_customers
            FROM order_items AS oi
            INNER JOIN stores AS s ON s.store_id = oi.store_id
            INNER JOIN orders AS o ON o.order_id = oi.order_id
            WHERE 
                1
        ";

        $params = [];

        // 🔥 Vendor mode → restrict by vendor's user_id
        if ($role === 'vendor' && !is_null($userId)) {
            $sql .= " AND s.user_id = ?";
            $params[] = $userId;
        }

        // 🔥 Optional store filter
        if (!is_null($storeId)) {
            $sql .= " AND oi.store_id = ?";
            $params[] = $storeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetchColumn();

        return (int) ($result !== false ? $result : 0);
        */

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
            // Admin: Global Totals
            /*
            $query = "
                SELECT 
                    COUNT(*) AS total_sales,
                    SUM(oi.price) AS total_revenue
                FROM order_items oi
                WHERE 
                    oi.created_at BETWEEN ? AND ?
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetch();
            */

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

            // --- 1️⃣ Vendor Totals (All Stores Combined)
            /*
            $summaryQuery = "
                SELECT 
                    COUNT(*) AS total_sales,
                    SUM(oi.price) AS total_revenue
                FROM order_items oi
                INNER JOIN stores s ON s.store_id = oi.store_id
                WHERE 
                    s.user_id = ? 
                    AND oi.created_at BETWEEN ? AND ?
            ";

            $summaryStmt = $this->db->prepare($summaryQuery);
            $summaryStmt->execute([$userId, $start, $end]);
            $summary = $summaryStmt->fetch();
            */

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

            // --- 2️⃣ Store Breakdown
            /*
            $storeQuery = "
                SELECT 
                    s.store_id AS store_id,
                    s.store_name,
                    COUNT(*) AS total_sales,
                    SUM(oi.price) AS total_revenue
                FROM order_items oi
                INNER JOIN stores s ON s.store_id = oi.store_id
                WHERE 
                    s.user_id = ? 
                    AND oi.created_at BETWEEN ? AND ?
                GROUP BY s.store_id
                ORDER BY total_revenue DESC
            ";

            $storeStmt = $this->db->prepare($storeQuery);
            $storeStmt->execute([$userId, $start, $end]);
            $stores = $storeStmt->fetchAll(PDO::FETCH_ASSOC);
            */

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
                ->orderBy('total_revenue', 'DESC')
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

    /*
    // FUNCTION USAGE 

    // Admin - Defaults to today
    $adminStats = getSalesAndRevenue();
    // Vendor - Defaults to today
    $vendorStats = getSalesAndRevenue('vendor', 7);
    // Vendor - Last month
    $vendorStatsLastMonth = getSalesAndRevenue('vendor', 7, 'last_month');
    // Admin - Custom range
    $customStats = getSalesAndRevenue('admin', null, 'custom', '2025-10-01', '2025-10-15');

    DEMO FUNCTION RESULT FOR A VENDOR
    Result for a demo vendor:
    {
        "view": "vendor",
        "total_sales": 54,
        "total_revenue": 2150.75,
        "timeframe": "today",
        "range": {
            "start": "2025-11-06 00:00:00",
            "end": "2025-11-06 23:59:59"
        },
        "stores": [
            {
                "store_id": 3,
                "store_name": "TechWorld",
                "total_sales": 32,
                "total_revenue": 1650.00
            },
            {
                "store_id": 9,
                "store_name": "StyleHub",
                "total_sales": 22,
                "total_revenue": 500.75
            }
        ]
    }
    */

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

        // 1️⃣ Role-based Filtering
        if ($view === 'admin') {
            $conditions[] = '1'; // no restriction
        } elseif (
            $view === 'vendor' 
            && $userId !== null
        ) {
            $conditions[] = 'store_id IN (SELECT store_id FROM stores WHERE user_id = ?)';
            $params[]     = $userId;
        }

        // 2️⃣ Optional Store Filter
        if ($storeId !== null) {
            $conditions[] = 'store_id = ?';
            $params[]     = $storeId;
        }

        // 3️⃣ Date filtering
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

        // 4️⃣ Merge Conditions
        $allConditions = array_merge([$baseCondition], $conditions);

        // 5️⃣ Build Query
        $sql = "
            SELECT 
                COUNT(DISTINCT order_id) AS total_orders,
                SUM(quantity) AS total_items_sold,
                SUM(price) AS total_revenue
            FROM order_items
            WHERE 
        " . implode(' AND ', $allConditions);

        // 6️⃣ Execute
        $result = $this->queryOne($sql, [$params]);

        // 7️⃣ Return clean values
        return [
            'total_orders'     => (int)($result['total_orders'] ?? 0),
            'total_items_sold' => (int)($result['total_items_sold'] ?? 0),
            'total_revenue'    => (float)($result['total_revenue'] ?? 0)
        ];
    }

    /*
    // FUNCTION USAGE 

    // Admin - All stores
    $stats = $analytics->getSalesAndRevenueByPeriod('admin', null, null, 'last_month');

    // Vendor - All their stores
    $stats = $analytics->getSalesAndRevenueByPeriod('vendor', $vendorId, null, 'last_week');

    // Vendor - One specific store
    $stats = $analytics->getSalesAndRevenueByPeriod('vendor', $vendorId, $storeId, 'today');

    // Vendor - Custom range
    $stats = $analytics->getSalesAndRevenueByPeriod('vendor', $vendorId, $storeId, 'custom', '2025-10-01', '2025-10-31');

    DEMO FUNCTION RESULT FOR A VENDOR
    Result for a demo vendor:
    [
        'total_orders'     => 10,
        'total_items_sold' => 50,
        'total_revenue'    => 500
    ];
    */
}
