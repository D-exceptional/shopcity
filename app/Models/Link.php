<?php

declare(strict_types=1);

namespace App\Models;

class Link extends Model
{
    protected string $table = 'product_links';

    public function create(
        int $productId, 
        int $userId, 
        string $short, 
        string $long, 
        string $code, 
        string $status
    ): bool {

        return $this->query()
            ->insert([
                'product_id'  => $productId,
                'user_id'     => $userId,
                'short_link'  => $short,
                'long_link'   => $long,
                'short_code'  => $code,
                'link_status' => $status,
            ]);
    }

    public function findAll(
        int $productId
    ): ?array {

        return $this->query()
            ->where('product_id', '=', $productId)
            ->get();
    }

    public function findOne(
        int $linkId
    ): ?array {

        return $this->query()
            ->where('link_id', '=', $linkId)
            ->first();
    }

    public function findByUser(
        int $productId, 
        int $userId
    ): ?array {

        return $this->query()
            ->where('product_id', '=', $productId)
            ->where('user_id', '=', $userId)
            ->first();
    }

    public function updateAll(
        int $productId, 
        string $status
    ): bool {

        return $this->query()
            ->where('product_id', '=', $productId)
            ->update(['link_status' => $status]);
    }

    public function updateOne(
        int $linkId, 
        string $status
    ): bool {

        return $this->query()
            ->where('link_id', '=', $linkId)
            ->update(['link_status' => $status]);
    }

    public function deleteAll(
        int $productId
    ): bool {

        return $this->query()
            ->where('product_id', '=', $productId)
            ->delete();
    }

    public function deleteOne(
        int $linkId
    ): bool {

        return $this->query()
            ->where('link_id', '=', $linkId)
            ->delete();
    }

    public function count(
        ?int $productId = null
    ): int {

        return $this->query()
            ->when(
                $productId
                && !is_null($productId),

                fn($query) =>
                    $query->where('product_id', '=', $productId)
            )
            ->count();
    }

    public function generateCode(): string
    {
        return bin2hex(random_bytes(6));
    }
}
