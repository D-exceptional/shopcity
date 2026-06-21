<?php

namespace App\Services;

use App\Helpers\ResponseManager;
use App\Models\Wishlist;

class WishlistService
{
    protected ResponseManager $response;
    protected Wishlist $wishlistModel;

    public function __construct(ResponseManager $response, Wishlist $wishlistModel)
    {
        // Required helper for this controller class
        $this->response = $response;
        // Required model for this controller class
        $this->wishlistModel = $wishlistModel;
    }

    public function view(int $id)
    { 
        $wishlist = $this->wishlistModel->view($id);
        if (count($wishlist['wishlist']) === 0) {
            return $this->response->fail('No wishlist found', 404);
        }
        // Prepare data
        return $this->response->success('Wishlist fetched', ['wishlist' => $wishlist]);
    }

    public function add(int $userId, int $productId)
    {
        $added = $this->wishlistModel->add($userId, $productId);
        if ($added === false) {
            return $this->response->fail('Failed to add item', 500);
        }

        if ($added === null) {
            return $this->response->fail('Item already added previously', 400);
        }

        return $this->response->success('Item added to wishlist');
    }

    public function merge(int $userId, array $wishlist)
    {
        // Split into batches of 5
        $batches = array_chunk($wishlist, 5);

        $errors = [];
        $successCount = 0;

        foreach ($batches as $batch) {
            foreach ($batch as $item) {
                // Validate each item
                if (!isset($item['productId'])) {
                    $errors[] = "Invalid item structure";
                    continue;
                }

                // Attempt to add each item
                if ($this->wishlistModel->add($userId, $item['productId'])) {
                    $successCount++;
                } else {
                    $errors[] = "Failed to add product ID {$item['productId']}";
                }
            }

            // (Optional) Add a slight pause if needed for performance throttling
            usleep(100000); // 0.1s pause between batches
        }

        // Final response after all batches processed
        if (count($errors) > 0) {
            return $this->response->fail('Some items failed to merge', 207, $errors); // 207 = Multi-Status
        } else {
            return $this->response->success('Wishlist merged successfully', ['processed' => $successCount]);
        }
    }

    public function remove(int $userId, int $productId)
    { 
        $removed = $this->wishlistModel->remove($userId, $productId);
        if ($removed === false) {
            return $this->response->fail('Failed to remove item', 500);
        }
        return $this->response->success('Item removed from wishlist');
    }

    public function clear(int $userId)
    { 
        $cleared = $this->wishlistModel->clear($userId);
        if ($cleared === false) {
            return $this->response->fail('Failed to clear wishlist', 500);
        }
        return $this->response->success('Wishlist cleared');
    }
}
