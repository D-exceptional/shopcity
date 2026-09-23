<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Store extends Model
{
    protected string $table = 'stores';

    public function createStore(
        string $name, 
        string $avatar, 
        string $description, 
        string $type, 
        string $status, 
        string $delivery, 
        int $userId
    ): int {

        $storeId = $this->query()
            ->insertGetId([
                'store_name'        => $name,
                'store_avatar'      => $avatar,
                'store_description' => $description,
                'store_type'        => $type,
                'store_status'      => $status,
                'store_delivery'    => $delivery,
                'user_id'           => $userId,
            ]);

        // Save Social Handles
        $socialQuery = "
            INSERT INTO store_socials (facebook, instagram, tiktok, twitter, store_id) 
            VALUES (?, ?, ?, ?, ?)
        ";

        $this->executeQuery($socialQuery, ['N/A', 'N/A', 'N/A', 'N/A', $storeId]);

        return $storeId;
    }

    public function updateStoreDetails(
        string $name, 
        string $description, 
        string $delivery, 
        int $storeId
    ): bool {

        return $this->query()
            ->where('store_id', '=', $storeId)
            ->update([
                'store_name'        => $name,
                'store_description' => $description,
                'store_delivery'    => $delivery
            ]);
    }

    public function updateStoreSocials(
        string $facebook, 
        string $instagram, 
        string $tiktok, 
        string $twitter, 
        int $storeId
    ): bool {

        // Update Social Handles
        $updateQuery = "
            UPDATE store_socials 
            SET 
                facebook = ?, instagram = ?, tiktok = ?, twitter = ? 
            WHERE 
                store_id = ?
        ";

        $this->executeQuery($updateQuery, [$facebook, $instagram, $tiktok, $twitter, $storeId]);
    }

    public function findStoreAvatar(
        int $storeId
    ): ?string {

        $result = $this->query()
            ->select(['store_avatar'])
            ->where('store_id', '=', $storeId)
            ->first();

        return $result ? $result['store_avatar'] : null;
    }

    public function updateStoreAvatar(
        string $avatar, 
        int $storeId
    ): bool {

        return $this->query()
            ->where('store_id', '=', $storeId)
            ->update(['store_avatar' => $avatar]);
    }

    public function updateStoreStatus(
        string $status, 
        int $storeId
    ): bool {

        return $this->query()
            ->where('store_id', '=', $storeId)
            ->update(['store_status' => $status]);
    }

    public function deleteStore(
        int $storeId
    ): bool {

        return $this->query()
            ->where('store_id', '=', $storeId)
            ->delete();
    }

    public function findUserByStoreId(
        int $storeId
    ): ?int {

        $result = $this->query()
            ->select(['user_id'])
            ->where('store_id', '=', $storeId)
            ->first();

        return $result ? (int)$result['user_id'] : null;
    }

    public function findOne(
        int $storeId
    ): ?array {

        $storeQuery = "
            SELECT 
                s.*, 
                ss.facebook, ss.instagram, ss.tiktok, ss.twitter
            FROM stores s
            LEFT JOIN store_socials ss ON s.store_id = ss.store_id
            WHERE 
                s.store_id = ?
            LIMIT 1
        ";

        return $this->queryOne($storeQuery, [$storeId]);
    }

    public function findStoresByStatus(
        ?string $status = null, 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        $offset = ($page - 1) * $limit;

        $storeQuery = "
            SELECT 
                s.*, 
                ss.facebook, ss.instagram, ss.tiktok, ss.twitter
            FROM stores s
            LEFT JOIN store_socials ss ON s.store_id = ss.store_id
        ";

        $params = [];

        if ($status && in_array($status, ['Pending','Active','Deactivated'])) {
            $storeQuery .= " WHERE s.store_status = ?";
            $params[] = $status;
        }

        $storeQuery .= " LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;

        $stmt = $this->db->prepare($storeQuery);

        // Bind All By Order
        foreach ($params as $index => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($index + 1, $value, $type);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findStoresByUser(
        ?int $userId = null, 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        $offset = ($page - 1) * $limit;

        $storeQuery = "
            SELECT 
                s.*, 
                ss.facebook, ss.instagram, ss.tiktok, ss.twitter
            FROM stores s
            LEFT JOIN store_socials ss ON s.store_id = ss.store_id
            WHERE 
                s.user_id = ? 
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->db->prepare($storeQuery);

        // Bind Params In Order: user_id → limit → offset
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function createCoupon(
        string $code, 
        int $discount, 
        int $storeId
    ): bool {

        $createQuery = "
            INSERT INTO store_coupons (coupon_code, coupon_discount, store_id) 
            VALUES (?, ?, ?)
        ";

        return $this->executeQuery($createQuery, [$code, $discount, $storeId]);
    }

    public function findCoupon(
        string $code, 
        int $storeId
    ): bool {

        $fetchQuery = "
            SELECT 
                * 
            FROM store_coupons 
            WHERE 
                coupon_code = ? 
                AND store_id = ?
        ";

        return $this->executeQuery($fetchQuery, [$code, $storeId]);
    }

    public function updateCoupon(
        string $code, 
        int $discount, 
        string $status, 
        int $couponId
    ): bool {

        $updateQuery = "
            UPDATE store_coupons 
            SET 
                coupon_code = ?, coupon_discount = ?, coupon_status = ? 
            WHERE 
                coupon_id = ?
        ";

        return $this->executeQuery($updateQuery, [$code, $discount, $status, $couponId]);
    }

    public function deleteSingleCoupon(
        int $couponId
    ): bool {

        $deleteQuery = "
           DELETE FROM store_coupons 
           WHERE 
                coupon_id = ?
        ";

        return $this->executeQuery($deleteQuery, [$couponId]);
    }

    public function deleteCouponByStore(
        int $storeId
    ): bool {

        $deleteQuery = "
           DELETE FROM store_coupons 
           WHERE 
                store_id = ?
        ";

        return $this->executeQuery($deleteQuery, [$storeId]);
    }

    public function deleteCouponByStoreAndStatus(
        string $status, 
        int $storeId
    ): bool {

        $deleteQuery = "
           DELETE FROM store_coupons 
           WHERE 
                coupon_status = ?
                AND store_id = ?
        ";

        return $this->executeQuery($deleteQuery, [$status, $storeId]);
    }

    private function fetchCoupons(
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

        return $stmt->fetchAll();
    }

    private function countResults(
        ?string $sql = null, 
        array $params = []
    ): int {

        return $this->fetchColumn($sql, $params);
    }

    private function formatCoupons(
        array $data, 
        int $total, 
        int $page, 
        int $limit
    ): array {

        return [
            'coupons'     => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $limit,
            'total_pages' => ceil($total / $limit),
        ];
    }

    public function findCouponsByStore(
        ?int $storeId = null, 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        $fetchQuery = "
            SELECT 
                c.* 
            FROM store_coupons c 
            WHERE 
                c.store_id = ?
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM store_coupons c 
            WHERE 
                c.store_id = ?
        ";

        $coupons = $this->fetchCoupons($fetchQuery, [$storeId], $page, $limit);
        $total   = $this->countResults($countQuery, [$storeId]);

        return $this->formatCoupons($coupons, $total, $page, $limit);
    }

    public function findCouponsByStoreAndStatus(
        ?int $storeId = null, 
        ?string $status = null, 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        $fetchQuery = "
            SELECT 
                c.* 
            FROM store_coupons c 
            WHERE 
                c.store_id = ? 
                AND c.coupon_status = ?
        ";

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM store_coupons c 
            WHERE 
                c.store_id = ? 
                AND c.coupon_status = ?
        ";

        $coupons = $this->fetchCoupons($fetchQuery, [$storeId, $status], $page, $limit);
        $total   = $this->countResults($countQuery, [$storeId, $status]);

        return $this->formatCoupons($coupons, $total, $page, $limit);
    }

    public function countStoresByStatus(): array
    {
        $countQuery = "
            SELECT 
                store_status, COUNT(*) AS total
            FROM {$this->table}
            GROUP BY store_status
        ";

        $results = $this->queryAll($countQuery);

        // Initialize Counts With 0 To Avoid Missing Keys
        $countKeys = [
            'Pending'     => 0,
            'Active'      => 0,
            'Deactivated' => 0,
        ];

        foreach ($results as $row) {
            $countKeys[$row['store_status']] = (int) $row['total'];
        }

        return $countKeys;
    }

    public function getBrands(
        int $page = 1, 
        int $limit = 20
    ): ?array {

        $offset = ($page - 1) * $limit;

        $brandsQuery = "
            SELECT 
                s.*, COUNT(p.product_id) AS product_count
            FROM {$this->table} s
            INNER JOIN products p ON p.store_id = s.store_id
            GROUP BY s.store_id
            HAVING COUNT(p.product_id) > 0
            ORDER BY s.store_name ASC
            LIMIT {$limit} OFFSET {$offset}
        ";

        return $this->queryAll($brandsQuery);
    }

    private function formatBrands(
        array $data, 
        int $total, 
        int $page, 
        int $limit
    ): array {

        return [
            'brands'      => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $limit,
            'total_pages' => ceil($total / $limit),
        ];
    }

    public function getTopBrands(
        int $page = 1, 
        int $limit = 20
    ): ?array {

        $countQuery = "
            SELECT COUNT(*) FROM (
                SELECT 
                    s.store_id
                FROM {$this->table} s
                INNER JOIN products p ON p.store_id = s.store_id
                GROUP BY s.store_id
                HAVING COUNT(p.product_id) > 0
            ) AS brand_count
        ";

        $brands = $this->getBrands($page, $limit);
        $total  = $this->countResults($countQuery);

        return $this->formatBrands($brands, $total, $page, $limit);
    }

    public function countVendorStores(
        int $userId, 
        string $status
    ): int {

        $countQuery = "
            SELECT 
                COUNT(*) 
            FROM {$this->table}
            WHERE 
                user_id = ? 
                AND store_status = ?
        ";

        return $this->fetchColumn($countQuery, [$userId, $status]);
    }

    public function getVendorStoreStats(
        int $userId
    ): array {

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
    public function countAllCouponsByStore(
        int $storeId
    ): int {

        if (is_null($storeId)) {
            return 0;
        }

        $countQuery = "
            SELECT 
                COALESCE(COUNT(coupon_id), 0) AS total_coupons
            FROM store_coupons
            WHERE 
                store_id = ?
        ";

        return $this->fetchColumn($countQuery, [$storeId]);
    }

    /**
     * ✅ Count Coupons by Status for a Store
     *
     * @param int $storeId - The store ID
     * @param string $status - Coupon status ('Active' or 'Deactivated')
     * @return int
     */
    public function countResultsByStatus(
        int $storeId, 
        string $status
    ): int {

        if (is_null($storeId)) {
            return 0;
        }

        $countQuery = "
            SELECT 
                COALESCE(COUNT(coupon_id), 0) AS total_by_status
            FROM store_coupons
            WHERE 
                store_id = ?
                AND coupon_status = ?
        ";

        return $this->fetchColumn($countQuery, [$storeId, $status]);
    }

    public function getStoreCouponStats(
        int $storeId
    ): array {

        return [
            'total'     => $this->countAllCouponsByStore($storeId),
            'active'    => $this->countResultsByStatus($storeId, 'Active'),
            'inactive'  => $this->countResultsByStatus($storeId, 'Deactivated'),
        ];
    }

    private function formatCustomers(
        array $data, 
        int $total, 
        int $page, 
        int $limit
    ): array {

        return [
            'customers'   => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $limit,
            'total_pages' => ceil($total / $limit),
        ];
    }

    public function getStoreCustomersByType(
        ?int $storeId = null, 
        string $type = 'unique', 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        $offset = ($page - 1) * $limit;
        $type   = strtolower($type);

        if (!in_array($type, ['unique', 'loyal'])) {
            throw new InvalidArgumentException("Type must be 'unique' or 'loyal'.");
        }

        // -------------------------------
        // Base SQL (shared by both queries)
        // -------------------------------
        $baseQuery = "
            FROM order_items oi
            JOIN orders o ON o.order_id = oi.order_id
            JOIN users u ON u.user_id = o.user_id
            WHERE 
                oi.store_id = ?
            GROUP BY u.user_id, u.firstname, u.lastname, u.email, u.contact, u.country
        ";

        // -------------------------------
        // Add loyalty condition
        // -------------------------------
        $havingCondition = ($type === 'loyal')
            ? " HAVING COUNT(DISTINCT o.order_id) >= 20" // Adjust as necessary
            : " HAVING COUNT(DISTINCT o.order_id) >= 1";

        // -------------------------------
        // 1️⃣ Get total count
        // -------------------------------
        $countQuery = "
            SELECT COUNT(*) AS total FROM (
                SELECT u.user_id {$baseQuery} {$havingCondition}
            ) AS subquery
        ";

        $total = $this->fetchColumn($countQuery, [$storeId]);

        // -------------------------------
        // 2️⃣ Get paginated results
        // -------------------------------
        $customerQuery = "
            SELECT 
                u.user_id,
                u.firstname,
                u.lastname,
                u.email,
                u.contact, 
                u.country,
                COUNT(DISTINCT o.order_id) AS total_orders
            {$baseQuery}
            {$havingCondition}
            ORDER BY total_orders DESC
        LIMIT {$limit} OFFSET {$offset}
        ";

        $customers = $this->queryAll($customerQuery, [$storeId]);

        // -------------------------------
        // 3️⃣ Return in pagination format
        // -------------------------------
        return $this->formatCustomers($customers, $total, $page, $limit);
    }
}
