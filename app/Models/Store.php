<?php

declare(strict_types=1);

namespace App\Models;

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

        $storeId = $this->table()
            ->insertGetId([
                'store_name'        => $name,
                'store_avatar'      => $avatar,
                'store_description' => $description,
                'store_status'      => $status,
                'store_delivery'    => $delivery,
                'user_id'           => $userId,
            ]);

        if (!$storeId) {
            throw new \Exception('Failed to create store');
        }

        // Save Social Handles
        $socialSaved = $this->table('store_socials')
            ->insert([
                'facebook'  => 'N/A',
                'instagram' => 'N/A',
                'tiktok'    => 'N/A',
                'twitter'   => 'N/A',
                'store_id'   => $storeId,
            ]);

        if (!$socialSaved) {
            throw new \Exception('Failed to create store socials');
        }

        return $storeId;
    }

    public function updateStoreDetails(
        string $name, 
        string $description, 
        string $delivery, 
        int $storeId
    ): bool {

        return $this->table()
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

        return $this->table('store_socials')
            ->update([
                'facebook'  => $facebook,
                'instagram' => $instagram,
                'tiktok'    => $tiktok,
                'twitter'   => $twitter,
                'store_id'  => $storeId,
            ]);
    }

    public function findStoreAvatar(
        int $storeId
    ): ?string {

        $result = $this->table()
            ->select(['store_avatar'])
            ->where('store_id', '=', $storeId)
            ->first();

        return $result ? $result['store_avatar'] : null;
    }

    public function updateStoreAvatar(
        string $avatar, 
        int $storeId
    ): bool {

        return $this->table()
            ->where('store_id', '=', $storeId)
            ->update(['store_avatar' => $avatar]);
    }

    public function updateStoreStatus(
        string $status, 
        int $storeId
    ): bool {

        return $this->table()
            ->where('store_id', '=', $storeId)
            ->update(['store_status' => $status]);
    }

    public function deleteStore(
        int $storeId
    ): bool {

        return $this->table()
            ->where('store_id', '=', $storeId)
            ->delete();
    }

    public function findUserByStoreId(
        int $storeId
    ): ?int {

        $result = $this->table()
            ->select(['user_id'])
            ->where('store_id', '=', $storeId)
            ->first();

        return $result ? (int)$result['user_id'] : null;
    }

    public function findOne(
        int $storeId
    ): ?array {

        return $this->table('stores s')
            ->select([
                's.*',
                'ss.facebook, ss.instagram, ss.tiktok, ss.twitter',
            ])
            ->leftJoin(
                'store_socials ss',
                's.store_id',
                '=',
                'ss.store_id',
            )
            ->where('s.store_id', '=', $storeId)
            ->first();
    }

    public function findStoresByStatus(
        string $status = 'Pending', 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        return $this->table('stores s')
            ->select([
                's.*',
                'ss.facebook, ss.instagram, ss.tiktok, ss.twitter',
            ])
            ->leftJoin(
                'store_socials ss',
                's.store_id',
                '=',
                'ss.store_id',
            )
            ->when(
                $status
                && in_array($status, ['Pending','Active','Deactivated']),

                function ($query) use ($status) {
                    $query->where('s.store_status', '=', $status);
                }
            )
            ->paginate($page, $limit)
            ->get();
    }

    public function findStoresByUser(
        ?int $userId = null, 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        return $this->table('stores s')
            ->select([
                's.*',
                'ss.facebook, ss.instagram, ss.tiktok, ss.twitter',
            ])
            ->leftJoin(
                'store_socials ss',
                's.store_id',
                '=',
                'ss.store_id',
            )
            ->when(
                $userId !== null,

                function ($query) use ($userId) {
                    $query->where('s.user_id', '=', $userId);
                }
            )
            ->paginate($page, $limit)
            ->get();
    }

    public function createCoupon(
        string $code, 
        int $discount, 
        int $storeId
    ): bool {

        return $this->table('store_coupons')
            ->insert([
                'coupon_code'     => $code,
                'coupon_discount' => $discount,
                'store_id'        => $storeId,
            ]);
    }

    public function findCoupon(
        string $code, 
        int $storeId
    ): bool {

        return $this->table('store_coupons')
            ->where('coupon_code', '=', $code)
            ->where('store_id', '=', $storeId)
            ->exists();
    }

    public function updateCoupon(
        string $code, 
        int $discount, 
        string $status, 
        int $couponId
    ): bool {

        return $this->table('store_coupons')
            ->update([
                'coupon_code'     => $code,
                'coupon_discount' => $discount,
                'coupon_status'   => $status,
                'coupon_id'       => $couponId,
            ]);
    }

    public function deleteSingleCoupon(
        int $couponId
    ): bool {

        return $this->table('store_coupons')
            ->where('coupon_id', '=', $couponId)
            ->delete();
    }

    public function deleteCouponByStore(
        int $storeId
    ): bool {

        return $this->table('store_coupons')
            ->where('store_id', '=', $storeId)
            ->delete();
    }

    public function deleteCouponByStoreAndStatus(
        string $status, 
        int $storeId
    ): bool {

        return $this->table('store_coupons')
            ->where('coupon_status', '=', $status)
            ->where('store_id', '=', $storeId)
            ->delete();
    }

    public function findCouponsByStore(
        ?int $storeId = null, 
        int $page = 1, 
        int $limit = 20
    ): array {

        $coupons = $this->table('store_coupons')
            ->where('store_id', '=', $storeId)
            ->latest()
            ->paginate($page, $limit)
            ->get();

        $total = $this->table('store_coupons')
            ->where('store_id', '=', $storeId)
            ->count();

        return $this->formatCoupons($coupons, $total, $page, $limit);
    }

    public function findCouponsByStoreAndStatus(
        ?int $storeId = null, 
        ?string $status = null, 
        int $page = 1, 
        int $limit = 20
    ): array {

        $coupons = $this->table('store_coupons')
            ->where('store_id', '=', $storeId)
            ->where('coupon_status', '=', $status)
            ->latest()
            ->paginate($page, $limit)
            ->get();

        $total = $this->table('store_coupons')
            ->where('store_id', '=', $storeId)
            ->where('coupon_status', '=', $status)
            ->count();

        return $this->formatCoupons($coupons, $total, $page, $limit);
    }

    public function countStoresByStatus(): array
    {
        $results = $this->table()
            ->select([
                'store_status',
                'COUNT(*) AS total',
            ])
            ->groupBy(['store_status'])
            ->get();

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

    // Get brands in alphabetical order
    public function getBrands(
        int $page = 1,
        int $limit = 20
    ): ?array {

        return $this->table('stores AS s')
            ->select([
                's.*',
                'COUNT(p.product_id) AS product_count',
            ])
            ->join(
                'products AS p',
                'p.store_id',
                '=',
                's.store_id'
            )
            ->groupBy('s.store_id')
            ->oldest('s.store_name')
            ->paginate($page, $limit)
            ->get();
    }

    // Get brands by most products
    public function getTopProductBrands(
        int $page = 1,
        int $limit = 20
    ): ?array {

        $brands = $this->table('stores AS s')
            ->select([
                's.*',
                'COUNT(p.product_id) AS product_count',
            ])
            ->join(
                'products AS p',
                'p.store_id',
                '=',
                's.store_id'
            )
            ->groupBy('s.store_id')
            ->latest('product_count')
            ->paginate($page, $limit)
            ->get();

        $total = $this->table('stores AS s')
            ->join(
                'products AS p',
                'p.store_id',
                '=',
                's.store_id'
            )
            ->countDistinct('s.store_id');

        return $this->formatBrands(
            $brands,
            $total,
            $page,
            $limit
        );
    }

    public function getTopSellingBrands(
        int $page = 1,
        int $limit = 20
    ): ?array {
        
        $brands = $this->table('stores AS s')
            ->select([
                's.*',
                'SUM(oi.quantity) AS total_quantity_sold',
            ])
            ->join(
                'products AS p',
                'p.store_id',
                '=',
                's.store_id'
            )
            ->join(
                'order_items AS oi',
                'oi.product_id',
                '=',
                'p.product_id'
            )
            ->groupBy('s.store_id')
            ->having(
                'SUM(oi.quantity)',
                '>=',
                200
            )
            ->latest(
                'total_quantity_sold'
            )
            ->paginate($page, $limit)
            ->get();

        /*
        * Count the number of stores that have sold
        * at least 200 products.
        */
        $qualifiedBrands = $this->table('stores AS s')
            ->select([
                's.store_id',
            ])
            ->join(
                'products AS p',
                'p.store_id',
                '=',
                's.store_id'
            )
            ->join(
                'order_items AS oi',
                'oi.product_id',
                '=',
                'p.product_id'
            )
            ->groupBy('s.store_id')
            ->having(
                'SUM(oi.quantity)',
                '>=',
                200
            );

        $total = $this->newQuery()
            ->fromSub(
                $qualifiedBrands,
                'qualified_brands'
            )
            ->count();

        return $this->formatBrands(
            $brands,
            $total,
            $page,
            $limit
        );
    }

    public function countVendorStores(
        int $userId, 
        string $status
    ): int {

        return $this->table()
            ->where('user_id', '=', $userId)
            ->where('store_status', '=', $status)
            ->count();
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
     * Count All Coupons for a Store
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

        return $this->table('store_coupons')
            ->select([
                'COALESCE(COUNT(coupon_id), 0) AS total_coupons',
            ])
            ->where('store_id', '=', $storeId)
            ->count();
    }

    /**
     * Count Coupons by Status for a Store
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

        return $this->table('store_coupons')
            ->select([
                'COALESCE(COUNT(coupon_id), 0) AS total_by_status',
            ])
            ->where('store_id', '=', $storeId)
            ->where('coupon_status', '=', $status)
            ->count();
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

    public function getStoreCustomersByType(
        ?int $storeId = null,
        string $type = 'random',
        int $page = 1,
        int $limit = 20
    ): array {

        $type = strtolower($type);

        if (!in_array($type, ['random', 'loyal'], true)) {
            throw new \InvalidArgumentException(
                "Type must be 'random' or 'loyal'."
            );
        }

        // ----------------------------------------
        // Build customer query
        // ----------------------------------------
        $customersQuery = $this->table('order_items AS oi')
            ->select([
                'u.user_id',
                'u.firstname',
                'u.lastname',
                'u.email',
                'u.contact',
                'u.country',
                'COUNT(DISTINCT o.order_id) AS total_orders',
            ])
            ->join(
                'orders AS o',
                'o.order_id',
                '=',
                'oi.order_id'
            )
            ->join(
                'users AS u',
                'u.user_id',
                '=',
                'o.user_id'
            )
            ->where(
                'oi.store_id',
                '=',
                $storeId
            )
            ->groupBy([
                'u.user_id',
                'u.firstname',
                'u.lastname',
                'u.email',
                'u.contact',
                'u.country',
            ]);

        // ----------------------------------------
        // Customer type
        // ----------------------------------------
        $customersQuery->having(
            'COUNT(DISTINCT o.order_id)',
            '>=',
            $type === 'loyal' ? 20 : 1
        );

        // ----------------------------------------
        // Count qualified customers
        // ----------------------------------------
        $total = $this->newQuery()
            ->fromSub(
                $customersQuery,
                'customers'
            )
            ->count();

        // ----------------------------------------
        // Get paginated customers
        // ----------------------------------------
        $customers = $customersQuery
            ->orderBy('total_orders', 'DESC')
            ->paginate($page, $limit)
            ->get();

        return $this->formatCustomers(
            $customers,
            $total,
            $page,
            $limit
        );
    }

    // ======================================
    //  PRIVATE HELPER METHODS
    // ======================================
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
}
