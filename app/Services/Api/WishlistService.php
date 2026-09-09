<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Core\Result;
use App\Models\Wishlist;

class WishlistService
{
    public function __construct(
        protected Result $result, 
        protected Wishlist $wishlistModel
    ) {}

    public function view(
        int $userId
    ): Result { 

        $wishlist = $this->wishlistModel->view($id);
        if (count($wishlist['wishlist']) === 0) {
            return $this->result->error('No wishlist found', 404);
        }
       
        return $this->result->success('Wishlist fetched', ['wishlist' => $wishlist]);
    }

    public function add(
        int $productId,
        int $userId, 
    ): Result {

        $added = $this->wishlistModel->add($userId, $productId);
        if ($added === false) {
            return $this->result->error('Failed to add item', 500);
        }

        if ($added === null) {
            return $this->result->error('Item already added previously', 400);
        }

        return $this->result->success('Item added to wishlist');
    }

    public function merge(
        array $wishlist,
        int $userId
    ): Result {

        // Split Into Batches Of 5
        $batches = array_chunk($wishlist, 5);

        $errors = [
            'validations' => [],
            'additions'   => []
        ];

        $successCount = 0;

        foreach ($batches as $batch) {
            foreach ($batch as $item) {

                $productId = $item['productId'];

                // Validate Each Item
                if (!isset($productId)) {
                    $errors['validations'][] = "Invalid item structure for product ID: {$productId}";
                    continue;
                }

                // Attempt To Add Each Item
                if ($this->wishlistModel->add($productId, $userId)) {
                    $successCount++;
                } else {
                    $errors['additions'][] = "Failed to add product ID: {$productId}";
                }
            }

            // (Optional) Add a slight pause if needed for performance throttling
            usleep(100000);
        }

        // Final Result After All Batches Processed
        if (
            count($errors['validations']) > 0 
            || count($errors['additions']) > 0
        ) {
            return $this->result->error('Some items failed to merge', 207, $errors); // 207 = Multi-Status
        } else {
            return $this->result->success('Wishlist merged successfully', ['processed' => $successCount]);
        }
    }

    public function remove(
        int $productId,
        int $userId
    ): Result { 

        $removed = $this->wishlistModel->remove($productId, $userId);
        if ($removed === false) {
            return $this->result->error('Failed to remove item', 500);
        }

        return $this->result->success('Item removed from wishlist');
    }

    public function clear(
        int $userId
    ): Result { 

        $cleared = $this->wishlistModel->clear($userId);
        if ($cleared === false) {
            return $this->result->error('Failed to clear wishlist', 500);
        }
        
        return $this->result->success('Wishlist cleared');
    }
}
