<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Core\Result;
use App\Database\Database;
use App\Models\Wishlist;

class WishlistService
{
    public function __construct(
        protected Result $result, 
        protected Database $dbClient,
        protected Wishlist $wishlistModel
    ) {}

    public function view(
        int $userId
    ): Result { 

        $wishlist = $this->wishlistModel->view($userId);
        if (count($wishlist['wishlist']) === 0) {
            return $this->result->error('No wishlist found', 404);
        }
       
        return $this->result->success('Wishlist fetched', ['wishlist' => $wishlist]);
    }

    public function add(
        int $productId,
        int $userId
    ): Result {

        $count = $this->dbClient->transaction(function () use (
            $productId, 
            $userId
        ) {

            $added = $this->wishlistModel->add($userId, $productId);
            if ($added === false) {
                throw new \Exception("Failed to add item to wishlist");
            }

            if ($added === null) {
                throw new \Exception("Item already added previously");
            }

            $count = $this->wishlistModel->count($userId);

            return $count;
        });

        return $this->result->success('Item added to wishlist', ['count' => $count]);
    }

    public function merge(
        array $wishlist,
        int $userId
    ): Result {

        if (empty($wishlist)) {
            throw new \Exception("Wishlist is empty. Add items to your wishlist and try again");
        }

        $successCount = $this->dbClient->transaction(function () use (
            $wishlist, 
            $userId
        ) {

            $successCount = 0;
            $errors = [
                'validations' => [],
                'additions'   => []
            ];

            // Split Into Batches Of 5
            $batches = array_chunk($wishlist, 5);

            foreach ($batches as $batch) {

                foreach ($batch as $item) {

                    $productId = $item['productId'];

                    // Validate Each Item
                    if (!isset($productId)) {
                        $errors['validations'][] = "Invalid item structure for product ID: {$productId}";
                        continue;
                    }

                    // Attempt To Add Each Item
                    $itemAdded = $this->wishlistModel->add($productId, $userId);
                    if ($itemAdded === false) {
                        $errors['additions'][] = "Failed to add product ID: {$productId}";
                    }
                    else {
                        $successCount++;
                    }
                }

                // (Optional) Add a slight pause if needed for performance throttling
                usleep(100000);
            }

            // Final Result After All Batches Processed
            if (
                !empty($errors['validations']) 
                || !empty($errors['additions'])
            ) {
                throw new \Exception("Wishlist merge failed with some issues " . json_encode(['processed' => $successCount, 'errors' => $errors]));
            }

            return $successCount;
        });

        return $this->result->success('Wishlist merged successfully', ['processed' => $successCount]);
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
