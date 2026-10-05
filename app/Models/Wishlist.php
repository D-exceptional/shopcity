<?php

declare(strict_types=1);

namespace App\Models;

class Wishlist extends Model
{
    protected string $table = 'wishlist';

   public function view(
        int $userId = 0,
        int $page = 1,
        int $limit = 20
    ): array {

        // =========================================
        // FIRST PRODUCT MEDIA
        // =========================================

        $firstMedia = $this->table('product_media')
            ->select([
                'product_id',
                'MIN(media_id) AS first_media_id',
            ])
            ->groupBy('product_id');

        // =========================================
        // WISHLIST QUERY
        // =========================================

        $wishlist = $this->table('wishlist AS w')
            ->select([
                'w.wishlist_id',
                'w.user_id',
                'w.product_id',
                'p.product_name',
                'p.category',
                'p.product_price',
                'pm.media_url AS product_image',
            ])
            ->join(
                'products AS p',
                'p.product_id',
                '=',
                'w.product_id'
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
            ->where('w.user_id', '=', $userId)
            ->latest()
            ->paginate($page, $limit)
            ->get();


        // =========================================
        // TOTAL WISHLIST ITEMS
        // =========================================

        $total = $this->table()
            ->where('user_id', '=', $userId)
            ->count();

        // =========================================
        // FORMAT RESPONSE
        // =========================================

        return $this->format(
            $wishlist,
            $total,
            $page,
            $limit
        );
    }

    public function add(
        int $userId, 
        int $productId
    ): ?bool {

        $existing = $this->table()
            ->where('user_id', '=', $userId)
            ->where('product_id', '=', $productId)
            ->lockForUpdate()
            ->first();

        if ($existing) {
            return null;
        }

        return $this->table()
            ->insert([
                'product_id' => $productId,
                'user_id'    => $userId,
            ]);
    }

    public function remove(
        int $userId, 
        int $productId
    ): bool {

        return $this->table()
            ->where('user_id', '=', $userId)
            ->where('product_id', '=', $productId)
            ->delete();
    }

    public function clear(
        int $userId
    ): bool {

        return $this->table()
            ->where('user_id', '=', $userId)
            ->delete();
    }

    public function count(
        int $userId
    ): int {

        return $this->table()
            ->where('user_id', '=', $userId)
            ->count();
    }

    // ======================================
    //  PRIVATE HELPER METHODS
    // =====================================
    private function format(
        array $data, 
        int $total, 
        int $page, 
        int $limit
    ): array {

        return [
            'wishlist'    => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $limit,
            'total_pages' => ceil($total / $limit),
        ];
    }
}