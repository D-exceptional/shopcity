<?php
namespace App\Models;
use PDO;

class Order extends Database
{
    /** Create a new order */
    public function createOrder(?int $userId = null, ?float $subtotal = null, ?float $tax = null, ?float $discount = null, ?float $shipping = null, ?float $total = null, ?string $shippingAddress = null, array $items = [])
    {
        try {
            $this->db->beginTransaction();

            // Generate order tracking code
            $orderCode = $this->generateOrderCode();

            // Save order
            $sql = "INSERT INTO orders (
                user_id, subtotal_amount, tax_amount, discount_amount, 
                shipping_amount, total_amount, shipping_address, 
                tracking_code, facilitator_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId, $subtotal, $tax, $discount, $shipping, 
                $total, $shippingAddress, $orderCode, $userId
            ]);

            // Get orderId
            $orderId = $this->db->lastInsertId();

            // Insert order items
            $sqlItem = "INSERT INTO order_items (
                order_id, product_id, quantity, price, store_id, tracking_code
            ) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtItem = $this->db->prepare($sqlItem);

            // Track stores with totals
            $stores = [];

            foreach ($items as $item) {
                // Generate item tracking code
                $itemCode = $this->generateItemCode();

                $stmtItem->execute([
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price'],
                    $item['store_id'],
                    $itemCode
                ]);

                // Calculate item total = price * quantity
                $itemTotal = $item['price'] * $item['quantity'];

                // Aggregate per store
                if (!isset($stores[$item['store_id']])) {
                    $stores[$item['store_id']] = [
                        'store_id' => $item['store_id'],
                        'total' => 0
                    ];
                }

                $stores[$item['store_id']]['total'] += $itemTotal;
            }

            $this->db->commit();

            // Return orderId and unique store details
            return [
                'id' => $orderId,
                'code' => $orderCode,
                'stores' => array_values($stores) // reset keys
            ];

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /** Fetch orders */
    private function fetchOrders(?string $sql = null, array $params = [], int $page = 1, int $perPage = 20): ?array
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
        return $stmt->fetchAll();
    }

    /** Fetch orders */
    private function processOrders(?string $sql = null, array $params = [], int $page = 1, int $perPage = 20): ?array
    {
        $offset = ($page - 1) * $perPage;
        $baseQuery = $sql ?: "SELECT * FROM order_items";

        // Attach product info and first image
        $sql = "
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

        $stmt = $this->db->prepare($sql);

        $i = 1;
        foreach ($params as $param) {
            $type = is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($i++, $param, $type);
        }

        $stmt->bindValue($i++, (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue($i, (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();

        /************** EXAMPLE RESULT ******************
        [
            "item_id" => 15,
            "store_id" => 2,
            "product_id" => 5,
            "item_status" => "pending",
            "quantity" => 3,
            "price" => 120.00,
            "product_name" => "Wireless Headset",
            "stock" => 18,
            "product_image" => "uploads/products/5/thumbnail.jpg"
        ]
        /*****************************************/

    }

    /** Count orders */
    private function countOrders(?string $sql = null, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

     /** Paginate orders */
    private function paginate(array $data, int $total, int $page, int $perPage): ?array
    {
        return [
            'orders'      => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => ceil($total / $perPage),
        ];
    }

    /** Track an order with code  */
    public function trackOrder(int $userId, string $code)
    {
        $sql = "SELECT * FROM orders WHERE user_id = ? AND tracking_code = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $code]);
        return $stmt->fetch();
    }

    /** Fetch single order with items */
    public function getOrder(int $orderId)
    {
        $sql = "SELECT * FROM orders WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();

        if (is_array($order)) {
            $order['items'] = $this->getOrderItems($orderId);
        }
        return $order;
    }

    /** Fetch items for a given order */
    public function getOrderItems(int $orderId)
    {
        $sql = "
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
            WHERE oi.order_id = ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }


    /** Fetch all orders (admin use) */
    public function getAllOrders(int $page = 1, int $perPage = 20)
    {
        $sql = "SELECT * FROM orders ORDER BY created_at DESC";
        $orders = $this->fetchOrders($sql, [], $page, $perPage);
        $total = $this->countOrders("SELECT COUNT(*) FROM orders");
        return $this->paginate($orders, $total, $page, $perPage);
    }

    /** Fetch all orders by status (admin use) */
    public function getOrdersByStatus(?string $status = null, int $page = 1, int $perPage = 20)
    {
        $sql = "SELECT * FROM orders WHERE order_status = ? ORDER BY order_id DESC";
        $orders = $this->fetchOrders($sql, [$status], $page, $perPage);
        $total = $this->countOrders("SELECT COUNT(*) FROM orders WHERE order_status = ?", [$status]);
        return $this->paginate($orders, $total, $page, $perPage);
    }

    /** Fetch all orders for a user */
    public function getUserOrders(?int $userId = null, int $page = 1, int $perPage = 20)
    {
        $sql = "SELECT * FROM orders WHERE user_id = ? AND order_status != 'Cancelled' ORDER BY created_at DESC";
        $orders = $this->fetchOrders($sql, [$userId], $page, $perPage);
        $total = $this->countOrders("SELECT COUNT(*) FROM orders WHERE user_id = ?", [$userId]);
        return $this->paginate($orders, $total, $page, $perPage);
    }

    /** Fetch all orders for a store */
    public function getStoreOrders(?int $storeId = null, int $page = 1, int $perPage = 20)
    {
        $sql = "SELECT * FROM order_items WHERE store_id = ? ORDER BY item_id DESC";
        $orders = $this->processOrders($sql, [$storeId], $page, $perPage);
        $total = $this->countOrders("SELECT COUNT(*) FROM order_items WHERE store_id = ?", [$storeId]);
        return $this->paginate($orders, $total, $page, $perPage);
    }

    /** Fetch all orders for a store */
    public function getStoreOrdersByStatus(?int $storeId = null, ?string $status = null, int $page = 1, int $perPage = 20)
    {
        $sql = "SELECT * FROM order_items WHERE store_id = ? AND item_status = ? ORDER BY item_id DESC";
        $orders = $this->processOrders($sql, [$storeId, $status], $page, $perPage);
        $total = $this->countOrders("SELECT COUNT(*) FROM order_items WHERE store_id = ? AND item_status = ?", [$storeId, $status]);
        return $this->paginate($orders, $total, $page, $perPage);
    }

    /** Update order item status */
    public function updateItemStatus(int $itemId, string $status)
    {
        $sql = "UPDATE order_items SET item_status = ? WHERE item_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $itemId]);
    }

     /** Update order item finalized status */
    public function updateItemFinalizedStatus(int $itemId, string $status)
    {
        $sql = "UPDATE order_items SET finalized = ? WHERE item_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $itemId]);
    }

    /** Update order status */
    public function updateOrderStatus(int $orderId, string $status)
    {
        $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $orderId]);
    }

    /** Mark an order completed */
    public function completeOrder(int $orderId)
    {
        return $this->updateOrderStatus($orderId, 'Completed');
    }

    /** Cancel an order */
    public function cancelOrder(int $orderId)
    {
        return $this->updateOrderStatus($orderId, 'Cancelled');
    }

    /** Delete an order */
    public function deleteOrder(int $orderId)
    {
        $sql = "DELETE FROM orders WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$orderId]);
    }

    /** Delete an order items */
    public function deleteOrderItems(int $orderId)
    {
        $sql = "DELETE FROM order_items WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$orderId]);
    }

    /** Delete an order items */
    public function deleteOrderPayment(int $orderId)
    {
        $sql = "DELETE FROM payments WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$orderId]);
    }

    /** Generate order tracking code */
    public function generateOrderCode()
    {
       return '#Order-' . bin2hex(random_bytes(10));
    }

     /** Generate order item tracking code */
    public function generateItemCode()
    {
        return '#Item-' . bin2hex(random_bytes(10));
    }

    /** Get order total by ID */
    public function getOrderTotal(int $orderId)
    {
        $sql = "SELECT total_amount FROM orders WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchColumn(); // returns the single column value
    }

    /** Get order status and total amount by ID */
    public function getOrderDetails(int $orderId): ?array
    {
        $sql = "SELECT total_amount, tracking_code, created_at FROM orders WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetch();
    }

    /** Get unique stores involved in an order */
    public function getOrderStores(int $orderId): ?array
    {
        $sql = "SELECT DISTINCT store_id FROM order_items WHERE order_id = ? ORDER BY store_id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    /** Get item details from itemId */
    public function getItemDetails(int $itemId)
    {
        $sql = "SELECT * FROM order_items WHERE item_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$itemId]);
        return $stmt->fetch();
    }

    /** Get userId from order details */
    public function getUserByOrderId(int $orderId)
    {
        $sql = "SELECT user_id FROM orders WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchColumn();
    }

    /**
     * ✅ Total Revenue (Sum of all delivered items)
     *
     * @param int $userId  - Owner of the store(s)
     * @param int|null $storeId - Optional specific store ID
     * @return float
     */

    public function getTotalRevenue(?int $userId = null, ?int $storeId = null): float
    {
        $sql = "
            SELECT 
                COALESCE(SUM(oi.price * oi.quantity), 0) AS total_revenue
            FROM order_items AS oi
            INNER JOIN stores AS s ON s.store_id = oi.store_id
            WHERE oi.item_status = 'Delivered'
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
    }

    /**
     * ✅ Total Orders by Status
     *
     * @param int $userId  - Owner of the store(s)
     * @param string $status - Order item status (e.g., 'Pending', 'Delivered', etc.)
     * @param int|null $storeId - Optional specific store ID
     * @return int
     */

    public function getTotalOrdersByStatus(?int $userId = null, ?string $status = null, ?int $storeId = null): int
    {
        $sql = "
            SELECT 
                COALESCE(COUNT(oi.item_id), 0) AS total_orders
            FROM order_items AS oi
            INNER JOIN stores AS s ON s.store_id = oi.store_id
            WHERE 1
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
    }

    /**
     * ✅ Total Unique Customers (for all stores or a specific store)
     *
     * @param int $userId   - Vendor (store owner) ID
     * @param int|null $storeId - Optional specific store ID
     * @return int
    */
    public function countUniqueCustomers(?int $userId = null, ?int $storeId = null, string $role = 'vendor'): int
    {
        $sql = "
            SELECT 
                COALESCE(COUNT(DISTINCT o.user_id), 0) AS unique_customers
            FROM order_items AS oi
            INNER JOIN stores AS s ON s.store_id = oi.store_id
            INNER JOIN orders AS o ON o.order_id = oi.order_id
            WHERE 1
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
    }

    /**
     * Fetch all key dashboard stats in one call.
    */
    public function getVendorOrderStats(int $userId): ?array
    {
        return [
            'total_revenue'    => $this->getTotalRevenue($userId),
            'pending_orders'   => $this->getTotalOrdersByStatus($userId, 'Pending'),
            'shipped_orders'   => $this->getTotalOrdersByStatus($userId, 'Shipped'),
            'delivered_orders' => $this->getTotalOrdersByStatus($userId, 'Delivered'),
            'unique_customers'  => $this->countUniqueCustomers($userId, null, 'vendor'),
        ];
    }

    /**
     * Fetch all key dashboard stats in one call.
    */
    public function getVendorStoreStats(int $userId, int $storeId): ?array
    {
        return [
            'total_revenue'    => $this->getTotalRevenue($userId, $storeId),
            'pending_orders'   => $this->getTotalOrdersByStatus($userId, 'Pending', $storeId),
            'shipped_orders'   => $this->getTotalOrdersByStatus($userId, 'Shipped', $storeId),
            'delivered_orders' => $this->getTotalOrdersByStatus($userId, 'Delivered', $storeId),
            'unique_customers' => $this->countUniqueCustomers($userId, $storeId, 'vendor'),
        ];
    }

     /**
     * Fetch all key dashboard stats in one call.
    */
    public function getAdminOrderStats(): ?array
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
    private function getDateRange($timeframe = 'today', $startDate = null, $endDate = null) {
        $today = new DateTime();

        switch ($timeframe) {
            case 'today':
                $start = $today->format('Y-m-d 00:00:00');
                $end = $today->format('Y-m-d 23:59:59');
                break;

            case 'yesterday':
                $yesterday = (clone $today)->modify('-1 day');
                $start = $yesterday->format('Y-m-d 00:00:00');
                $end = $yesterday->format('Y-m-d 23:59:59');
                break;

            case 'last_week':
                $start = (clone $today)->modify('monday last week')->format('Y-m-d 00:00:00');
                $end = (clone $today)->modify('sunday last week')->format('Y-m-d 23:59:59');
                break;

            case 'last_month':
                $start = (clone $today)->modify('first day of last month')->format('Y-m-d 00:00:00');
                $end = (clone $today)->modify('last day of last month')->format('Y-m-d 23:59:59');
                break;

            case 'last_year':
                $start = (clone $today)->modify('first day of January last year')->format('Y-m-d 00:00:00');
                $end = (clone $today)->modify('last day of December last year')->format('Y-m-d 23:59:59');
                break;

            case 'custom':
                if (!$startDate || !$endDate) {
                    throw new Exception("Custom timeframe requires start and end dates.");
                }
                $start = $startDate . ' 00:00:00';
                $end = $endDate . ' 23:59:59';
                break;

            default:
                // fallback to today
                $start = $today->format('Y-m-d 00:00:00');
                $end = $today->format('Y-m-d 23:59:59');
                $timeframe = 'today';
        }

        return [$start, $end, $timeframe];
    }

    // Generate sales summary data
    public function getSalesAndRevenue($view = 'admin', $userId = null, $timeframe = 'today', $startDate = null, $endDate = null) 
    {
        list($start, $end, $resolvedTimeframe) = $this->getDateRange($timeframe, $startDate, $endDate);

        $params = [$start, $end];

        if ($view === 'admin') {
            // Admin: global totals
            $query = "
                SELECT 
                    COUNT(*) AS total_sales,
                    SUM(oi.price) AS total_revenue
                FROM order_items oi
                WHERE oi.created_at BETWEEN ? AND ?";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetch();

            return [
                'view' => $view,
                'total_sales' => (int)($result['total_sales'] ?? 0),
                'total_revenue' => (float)($result['total_revenue'] ?? 0),
                'timeframe' => $resolvedTimeframe,
                'range' => ['start' => $start, 'end' => $end]
            ];

        } elseif ($view === 'vendor') {
            if (!$userId) {
                throw new Exception("Vendor view requires user ID.");
            }

            // --- 1️⃣ Vendor totals (all stores combined)
            $summaryQuery = "
                SELECT 
                    COUNT(*) AS total_sales,
                    SUM(oi.price) AS total_revenue
                FROM order_items oi
                INNER JOIN stores s ON s.store_id = oi.store_id
                WHERE s.user_id = ? AND oi.created_at BETWEEN ? AND ?";

            $summaryStmt = $this->db->prepare($summaryQuery);
            $summaryStmt->execute([$userId, $start, $end]);
            $summary = $summaryStmt->fetch();

            // --- 2️⃣ Store breakdown
            $storeQuery = "
                SELECT 
                    s.store_id AS store_id,
                    s.store_name,
                    COUNT(*) AS total_sales,
                    SUM(oi.price) AS total_revenue
                FROM order_items oi
                INNER JOIN stores s ON s.store_id = oi.store_id
                WHERE s.user_id = ? AND oi.created_at BETWEEN ? AND ?
                GROUP BY s.store_id
                ORDER BY total_revenue DESC";

            $storeStmt = $this->db->prepare($storeQuery);
            $storeStmt->execute([$userId, $start, $end]);
            $stores = $storeStmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'view' => $view,
                'total_sales' => (int)($summary['total_sales'] ?? 0),
                'total_revenue' => (float)($summary['total_revenue'] ?? 0),
                'timeframe' => $resolvedTimeframe,
                'range' => ['start' => $start, 'end' => $end],
                'stores' => array_map(function($store) {
                    return [
                        'store_id' => (int)$store['store_id'],
                        'store_name' => $store['store_name'],
                        'total_sales' => (int)($store['total_sales'] ?? 0),
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
    public function getSalesAndRevenueByPeriod(string $view = 'admin', ?int $userId = null, ?int $storeId = null, ?string $period = 'today', ?string $startDate = null, ?string $endDate = null) 
    {
        $baseCondition = "item_status IN ('Shipped', 'Delivered')";
        $conditions = [];
        $params = [];

        // 1️⃣ Role-based filtering
        if ($view === 'admin') {
            $conditions[] = '1'; // no restriction
        } elseif ($view === 'vendor' && $userId !== null) {
            $conditions[] = 'store_id IN (SELECT store_id FROM stores WHERE user_id = ?)';
            $params[] = $userId;
        }

        // 2️⃣ Optional store filter
        if ($storeId !== null) {
            $conditions[] = 'store_id = ?';
            $params[] = $storeId;
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
                    $params[] = $startDate;
                    $params[] = $endDate;
                } else {
                    throw new InvalidArgumentException('Custom range requires start_date and end_date');
                }
                break;
        }

        // 4️⃣ Merge conditions
        $allConditions = array_merge([$baseCondition], $conditions);

        // 5️⃣ Build query
        $sql = "
            SELECT 
                COUNT(DISTINCT order_id) AS total_orders,
                SUM(quantity) AS total_items_sold,
                SUM(price) AS total_revenue
            FROM order_items
            WHERE " . implode(' AND ', $allConditions);

        // 6️⃣ Execute
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();

        // 7️⃣ Return clean values
        return [
            'total_orders'      => (int)($result['total_orders'] ?? 0),
            'total_items_sold'  => (int)($result['total_items_sold'] ?? 0),
            'total_revenue'     => (float)($result['total_revenue'] ?? 0)
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
        'total_orders'      => 10,
        'total_items_sold'  => 50,
        'total_revenue'     => 500
    ];
    */
}
