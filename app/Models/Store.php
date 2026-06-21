<?php

namespace App\Models;

use PDO;

class Store extends Database
{
    /** Create a store */
    public function createStore(
        string $name, 
        string $avatar, 
        string $description, 
        string $type, 
        string $status, 
        string $delivery, 
        string $facebook, 
        string $instagram, 
        string $tiktok, 
        string $twitter, 
        int $userId
    )
    {
        $sql = "INSERT INTO stores (store_name, store_avatar, store_description, store_type, store_status, store_delivery, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name, $avatar, $description, $type, $status, $delivery, $userId]);

        // Get storeId
        $storeId = $this->db->lastInsertId() ?: null;

        // Save social handles
        $sql = "INSERT INTO store_socials (facebook, instagram, tiktok, twitter, store_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$facebook, $instagram, $tiktok, $twitter, $storeId]);

        return $storeId;
    }

    /** Update store details */
    public function updateStoreDetails(string $name, string $description, string $delivery, int $storeId)
    {
        $sql = "UPDATE stores SET store_name = ?, store_description = ?, store_delivery = ? WHERE store_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name, $description, $delivery, $storeId]);
    }

    /** Update store socials */
    public function updateStoreSocials(string $facebook, string $instagram, string $tiktok, string $twitter, int $storeId)
    {
        // Update social handles
        $sql = "UPDATE store_socials SET facebook = ?, instagram = ?, tiktok = ?, twitter = ? WHERE store_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$facebook, $instagram, $tiktok, $twitter, $storeId]);
    }

    /** Find avatar by storeId */
    public function findStoreAvatar(int $storeId): ?array
    {
        $stmt = $this->db->prepare("SELECT store_avatar FROM stores WHERE store_id = ?");
        $stmt->execute([$storeId]);
        return $result = $stmt->fetch();
    }

    /** Update store avatar */
    public function updateStoreAvatar(string $avatar, int $storeId)
    {
        $sql = "UPDATE stores SET store_avatar = ? WHERE store_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$avatar, $storeId]);
    }

    /** Update store status */
    public function updateStoreStatus(string $status, int $storeId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE stores 
            SET store_status = ?
            WHERE store_id = ?
        ");
        return $stmt->execute([$status, $storeId]);
    }

    /** Delete a store  */
    public function deleteStore(int $storeId)
    {
        $sql = "DELETE FROM stores WHERE store_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$storeId]);
    }

    /** Find user by storeId */
    public function findUserByStoreId(int $storeId): ?int
    {
        $stmt = $this->db->prepare("SELECT user_id FROM stores WHERE store_id = ?");
        $stmt->execute([$storeId]);
        $result = $stmt->fetch();

        return $result ? (int)$result['user_id'] : null;
    }

    /** Find a store */
    public function findOne(int $storeId): ?array
    {
        $sql = "SELECT s.*, 
                       ss.facebook, ss.instagram, ss.tiktok, ss.twitter
                FROM stores s
                LEFT JOIN store_socials ss ON s.store_id = ss.store_id
                WHERE s.store_id = ?
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$storeId]);
        $store = $stmt->fetch();
        return $store;
    }

    /** Find stores by status */
    public function findStoresByStatus(?string $status = null, int $page = 1, int $perPage = 20): ?array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT s.*, 
                    ss.facebook, ss.instagram, ss.tiktok, ss.twitter
                FROM stores s
                LEFT JOIN store_socials ss ON s.store_id = ss.store_id";

        $params = [];

        if ($status && in_array($status, ['Pending','Active','Deactivated'])) {
            $sql .= " WHERE s.store_status = ?";
            $params[] = $status;
        }

        $sql .= " LIMIT ? OFFSET ?";
        $params[] = (int)$perPage;
        $params[] = (int)$offset;

        $stmt = $this->db->prepare($sql);

        // bind all by order
        foreach ($params as $index => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($index + 1, $value, $type);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** Find stores by user */
    public function findStoresByUser(?int $userId = null, int $page = 1, int $perPage = 20): ?array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT s.*, 
                    ss.facebook, ss.instagram, ss.tiktok, ss.twitter
                FROM stores s
                LEFT JOIN store_socials ss ON s.store_id = ss.store_id
                WHERE s.user_id = ? 
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);

        // Bind params in order: user_id → perPage → offset
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** Create a coupon */
    public function createCoupon(string $code, int $discount, int $storeId)
    {
        $sql = "INSERT INTO store_coupons (coupon_code, coupon_discount, store_id) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$code, $discount, $storeId]);
    }

     /** Create a coupon */
    public function findCoupon(string $coupon, int $storeId)
    {
        $sql = "SELECT * FROM store_coupons WHERE coupon_code = ? AND store_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$coupon, $storeId]);
    }

    /** Update a coupon */
    public function updateCoupon(string $code, int $discount, string $status, int $couponId)
    {
        $sql = "UPDATE store_coupons SET coupon_code = ?, coupon_discount = ?, coupon_status = ? WHERE coupon_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$code, $discount, $status, $couponId]);
    }

    /** Delete a coupon */
    public function deleteSingleCoupon(int $couponId)
    {
        $sql = "DELETE FROM store_coupons WHERE coupon_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$couponId]);
    }

    /** Delete a coupon by storeId */
    public function deleteCouponByStore(int $storeId)
    {
        $sql = "DELETE FROM store_coupons WHERE store_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$storeId]);
    }

     /** Delete a coupon by storeId and status */
    public function deleteCouponByStoreAndStatus(string $status, int $storeId)
    {
        $sql = "DELETE FROM store_coupons WHERE coupon_status = ? AND store_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $storeId]);
    }

    /**
     * Generic fetch with enrichment & pagination
    */
    private function fetchCoupons(?string $sql = null, array $params = [], int $page = 1, int $perPage = 20): ?array
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
        $coupons = $stmt->fetchAll();

        return $coupons;
    }

    /**
     * Count coupons
    */
    public function countCoupons(?string $sql = null, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Paginate coupon results
    */
    private function paginateCoupons(array $data, int $total, int $page, int $perPage): ?array
    {
        return [
            'coupons'     => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => ceil($total / $perPage),
        ];
    }

    /** Find coupons belonging to a store */
    public function findCouponsByStore(?int $storeId = null, int $page = 1, int $perPage = 20): ?array
    {
        $sql = "SELECT c.* FROM store_coupons c WHERE c.store_id = ?";
        $coupons = $this->fetchCoupons($sql, [$storeId], $page, $perPage);
        $total = $this->countCoupons("SELECT COUNT(*) FROM store_coupons c WHERE c.store_id = ?", [$storeId]);
        return $this->paginateCoupons($coupons, $total, $page, $perPage);
    }

    /** Find coupons by store and status */
    public function findCouponsByStoreAndStatus(?int $storeId = null, ?string $status = null, int $page = 1, int $perPage = 20): ?array
    {
        $sql = "SELECT c.* FROM store_coupons c WHERE c.store_id = ? AND c.coupon_status = ?";
        $coupons = $this->fetchCoupons($sql, [$storeId, $status], $page, $perPage);
        $total = $this->countCoupons("SELECT COUNT(*) FROM store_coupons c WHERE c.store_id = ? AND c.coupon_status = ?", [$storeId, $status]);
        return $this->paginateCoupons($coupons, $total, $page, $perPage);
    }

    public function countStoresByStatus(): ?array
    {
        $sql = "
            SELECT store_status, COUNT(*) AS total
            FROM stores
            GROUP BY store_status
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        // Initialize counts with 0 to avoid missing keys
        $counts = [
            'Pending'     => 0,
            'Active'      => 0,
            'Deactivated' => 0,
        ];

        foreach ($rows as $row) {
            $counts[$row['store_status']] = (int) $row['total'];
        }

        return $counts;
    }

    // Fetch paginated top brands that have products
    public function getBrands(int $page = 1, int $perPage = 20): ?array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT s.*, COUNT(p.product_id) AS product_count
            FROM stores s
            INNER JOIN products p ON p.store_id = s.store_id
            GROUP BY s.store_id
            HAVING COUNT(p.product_id) > 0
            ORDER BY s.store_name ASC
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function countBrands(?string $sql = null, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    private function paginate(array $data, int $total, int $page, int $perPage): ?array
    {
        return [
            'brands'      => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => ceil($total / $perPage),
        ];
    }

    public function getTopBrands(int $page = 1, int $perPage = 20): ?array
    {
        $brands = $this->getBrands($page, $perPage);

        $totalSql = "
            SELECT COUNT(*) FROM (
                SELECT s.store_id
                FROM stores s
                INNER JOIN products p ON p.store_id = s.store_id
                GROUP BY s.store_id
                HAVING COUNT(p.product_id) > 0
            ) AS brand_count
        ";
        $total = $this->countBrands($totalSql);

        return $this->paginate($brands, $total, $page, $perPage);
    }

    public function countVendorStores(int $userId, string $status): int
    {
        $sql = "SELECT COUNT(*) FROM stores WHERE user_id = ? AND store_status = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $status]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Fetch all key dashboard stats in one call.
    */
    public function getVendorStoreStats(int $userId): ?array
    {
        return [
            'active'      => $this->countVendorStores($userId, 'Active'),
            'pending'     => $this->countVendorStores($userId, 'Pending'),
            'deactivated' => $this->countVendorStores($userId, 'Deactivated'),
        ];
    }

    /**
     * ✅ Count All Coupons for a Store
     *
     * @param int $storeId - The store ID
     * @return int
     */
    public function countAllCouponsByStore(int $storeId): int
    {
        if (is_null($storeId)) {
            return 0;
        }

        $sql = "
            SELECT 
                COALESCE(COUNT(coupon_id), 0) AS total_coupons
            FROM store_coupons
            WHERE store_id = ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$storeId]);
        $result = $stmt->fetchColumn();

        return (int) ($result !== false ? $result : 0);
    }

    /**
     * ✅ Count Coupons by Status for a Store
     *
     * @param int $storeId - The store ID
     * @param string $status - Coupon status ('Active' or 'Deactivated')
     * @return int
     */
    public function countCouponsByStatus(int $storeId, string $status): int
    {
        if (is_null($storeId)) {
            return 0;
        }

        $sql = "
            SELECT 
                COALESCE(COUNT(coupon_id), 0) AS total_by_status
            FROM store_coupons
            WHERE store_id = ?
            AND coupon_status = ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$storeId, $status]);
        $result = $stmt->fetchColumn();

        return (int) ($result !== false ? $result : 0);
    }

    /**
     * Fetch all key dashboard stats in one call.
    */
    public function getStoreCouponStats(int $storeId): ?array
    {
        return [
            'total'     => $this->countAllCouponsByStore($storeId),
            'active'    => $this->countCouponsByStatus($storeId, 'Active'),
            'inactive'  => $this->countCouponsByStatus($storeId, 'Deactivated'),
        ];
    }

    /**
     * Paginate customers
    */
    private function paginateCustomers(array $data, int $total, int $page, int $perPage): ?array
    {
        return [
            'customers'   => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => ceil($total / $perPage),
        ];
    }

    /**
     * Fetch customers (all and loyal)
    */
    public function getStoreCustomersByType(?int $storeId = null, string $type = 'unique', int $page = 1, int $perPage = 20): ?array
    {
        $offset = ($page - 1) * $perPage;
        $type = strtolower($type);

        if (!in_array($type, ['unique', 'loyal'])) {
            throw new InvalidArgumentException("Type must be 'unique' or 'loyal'.");
        }

        // -------------------------------
        // Base SQL (shared by both queries)
        // -------------------------------
        $baseSql = "
            FROM order_items oi
            JOIN orders o ON o.order_id = oi.order_id
            JOIN users u ON u.user_id = o.user_id
            WHERE oi.store_id = ?
            GROUP BY u.user_id, u.firstname, u.lastname, u.email, u.contact, u.country
        ";

        // -------------------------------
        // Add loyalty condition
        // -------------------------------
        $havingCondition = ($type === 'loyal')
            ? " HAVING COUNT(DISTINCT o.order_id) >= 10"
            : " HAVING COUNT(DISTINCT o.order_id) >= 1";

        // -------------------------------
        // 1️⃣ Get total count
        // -------------------------------
        $countSql = "SELECT COUNT(*) AS total FROM (
            SELECT u.user_id $baseSql $havingCondition
        ) AS subquery";

        $countStmt = $this->db->prepare($countSql);
        $countStmt->bindValue(1, $storeId, PDO::PARAM_INT);
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        // -------------------------------
        // 2️⃣ Get paginated results
        // -------------------------------
        $dataSql = "
            SELECT 
                u.user_id,
                u.firstname,
                u.lastname,
                u.email,
                u.contact, 
                u.country,
                COUNT(DISTINCT o.order_id) AS total_orders
            $baseSql
            $havingCondition
            ORDER BY total_orders DESC
            LIMIT ? OFFSET ?
        ";

        $dataStmt = $this->db->prepare($dataSql);
        $dataStmt->bindValue(1, $storeId, PDO::PARAM_INT);
        $dataStmt->bindValue(2, (int)$perPage, PDO::PARAM_INT);
        $dataStmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
        $dataStmt->execute();

        $customers = $dataStmt->fetchAll();

        // -------------------------------
        // 3️⃣ Return in pagination format
        // -------------------------------
        return $this->paginateCustomers($customers, $total, $page, $perPage);
    }
}
