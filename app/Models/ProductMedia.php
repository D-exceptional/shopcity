<?php

declare(strict_types=1);

namespace App\Models;

class ProductMedia extends Model
{
    protected string $table = 'product_media';

    public function create(
        string $url, 
        string $type, 
        int $productId
    ): bool {

        return $this->table()
            ->insert([
                'media_url'  => $url,
                'media_type' => $type,
                'product_id' => $productId,
            ]);
    }

    public function findAll(
        int $productId
    ): ?array {

        return $this->table()
            ->where('product_id', '=', $productId)
            ->get();
    }

    public function findOne(
        int $mediaId
    ): ?array {

        return $this->table()
            ->where('media_id', '=', $mediaId)
            ->first();
    }

    public function update(
        int $mediaId,
        string $url
    ): bool {

        return $this->table()
            ->where('media_id', '=', $mediaId)
            ->update(['media_url' => $url]);
    }

    public function deleteAll(
        int $productId
    ): bool {

        return $this->table()
            ->where('product_id', '=', $productId)
            ->delete();
    }

    public function deleteOne(
        int $mediaId
    ): bool {

        return $this->table()
            ->where('media_id', '=', $mediaId)
            ->delete();
    }
}
