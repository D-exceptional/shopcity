<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Core\Result;
use App\Database\Database;
use App\Models\Cart;

class CartService
{
    public function __construct(
        protected Result $result, 
        protected Database $dbClient,
        protected Cart $cartModel
    ) {}

    public function view(
        int $userId
    ): Result {

        $cart = $this->cartModel->view($userId);

        return $this->result->success('Cart fetched', ['cart' => $cart]);
    }

    public function add(
        int $productId, 
        int $quantity,
        int $userId
    ): Result {

        $count = $this->dbClient->transaction(function () use (
            $productId, 
            $quantity,
            $userId
        ) {

            if ($quantity <= 0) {
                throw new \Exception("Quantity must be greater than zero");
            }

            $added = $this->cartModel->add($productId, $quantity, $userId);
            if (!$added) {
                throw new \Exception("Failed to add item to cart");
            }

            $count = $this->cartModel->countCart($userId);

            return $count;
        });

        return $this->result->success('Item added to cart', ['count' => $count]);
    }

    public function update(
        int $productId, 
        int $quantity,
        int $userId
    ): Result {

        if ($quantity <= 0) {
            return $this->result->error('Quantity must be greater than zero', 422);
        }

        $updated = $this->cartModel->update($productId, $quantity, $userId);
        if ($updated === false) {
            return $this->result->error('Failed to update cart', 500);
        }

        return $this->result->success('Cart updated successfully');
    }

    public function remove(
        int $productId,
        int $userId
    ): Result {

        $removed = $this->cartModel->remove($productId, $userId);
        if ($removed === false) {
            return $this->result->error('Failed to remove item', 500);
        }

        $count = $this->cartModel->countCart($userId);

        return $this->result->success('Item removed from cart', ['count' => $count]);
    }

    public function clear(
        int $userId
    ): Result {

        $cleared = $this->cartModel->clear($userId);
        if ($cleared === false) {
            return $this->result->error('Failed to clear cart', 500);
        }

        return $this->result->success('Cart cleared', ['count' => 0]);
    }

    public function merge(
        array $cart, 
        int $userId
    ): Result {

        if (empty($cart)) {
            throw new \Exception("Cart is empty. Add items to your cart and try again");
        }

        $successCount = $this->dbClient->transaction(function () use (
            $cart, 
            $userId
        ) {

            $successCount = 0;
            $errors = [
                'validations' => [],
                'additions'   => []
            ];

            // Split Into batches Of 5
            $batches = array_chunk($cart, 5);

            foreach ($batches as $batch) {

                foreach ($batch as $item) {

                    $productId = $item['productId'];
                    $quantity  = $item['quantity'];

                    if (!isset($productId, $quantity)) {
                        $errors['validations'][] = 'Invalid item structure';
                        continue;
                    }

                    if ($quantity <= 0) {
                        $errors['validations'][] = "Invalid quantity for product {$productId}";
                        continue;
                    }

                    $itemAdded = $this->cartModel->add($productId, $quantity, $userId);
                    if (!$itemAdded) {
                        $errors['additions'][] = "Failed to add product {$productId} to cart";
                    }
                    else {
                        $successCount++;
                    }
                }

                // (Optional) Add a slight pause if needed for performance throttling
                usleep(100000);
            }

            if (
                !empty($errors['validations']) 
                || !empty($errors['additions'])
            ) {
                throw new \Exception("Cart merge failed with some issues " . json_encode(['processed' => $successCount, 'errors' => $errors]));
            }

            return $successCount;
        });

        return $this->result->success('Cart merged successfully', ['processed' => $successCount]);
    }

    public function countUser(
        int $userId
    ): Result {

        $count = $this->cartModel->countCart($userId);

        return $this->result->success('Cart counted', ['count' => $count]);
    }

    public function countAll(): Result
    {
        $count = $this->cartModel->countAll();

        return $this->result->success('Carts counted', ['count' => $count]);
    }

    public function getCartUsers(): Result
    {
        $users = $this->cartModel->getCartUsers();
        
        return $this->result->success('Cart users fetched', ['users' => $users]);
    }
}
