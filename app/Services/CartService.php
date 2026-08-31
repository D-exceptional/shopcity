<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Result;
use App\Models\Cart;

class CartService
{
    public function __construct(
        protected Result $result, 
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

        if ($quantity <= 0) {
            return $this->result->error('Quantity must be greater than zero', 422);
        }

        $added = $this->cartModel->add($productId, $quantity, $userId);
        if ($added === false) {
            return $this->result->error('Failed to add item', 500);
        }

        $count = $this->cartModel->countCart($userId);

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

        $errors = [];
        $successCount = 0;

        foreach ($cart as $item) {
            if (!isset($item['productId'], $item['quantity'])) {
                $errors[] = 'Invalid item structure';
                continue;
            }

            if ($item['quantity'] <= 0) {
                $errors[] = "Invalid quantity for product {$item['productId']}";
                continue;
            }

            if ($this->cartModel->add($item['productId'], $item['quantity'], $userId)) {
                $successCount++;
            } else {
                $errors[] = "Failed to add product {$item['productId']}";
            }
        }

        if (!empty($errors)) {
            return $this->result->success('Cart merged with some issues', ['processed' => $successCount, 'errors' => $errors], 207);
        }

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
