<?php

namespace App\Services;

use App\Helpers\ResponseManager;
use App\Models\Cart;

class CartService
{
    protected ResponseManager $response;
    protected Cart $cartModel;

    public function __construct(ResponseManager $response, Cart $cartModel)
    {
        $this->response  = $response;
        // Required model for this controller class
        $this->cartModel = $cartModel;
    }

    public function view(int $userId): array
    {
        $cart = $this->cartModel->view($userId);

        // Empty cart is NOT an error
        return $this->response->success('Cart fetched', ['cart' => $cart]);
    }

    public function add(int $userId, int $productId, int $quantity): array
    {
        if ($quantity <= 0) {
            return $this->response->fail('Quantity must be greater than zero', 422);
        }

        $added = $this->cartModel->add($userId, $productId, $quantity);
        if ($added === false) {
            return $this->response->fail('Failed to add item', 500);
        }

        $count = $this->cartModel->countCart($userId);
        return $this->response->success('Item added to cart', ['count' => $count]);
    }

    public function update(int $userId, int $productId, int $quantity): array
    {
        if ($quantity <= 0) {
            return $this->response->fail('Quantity must be greater than zero', 422);
        }

        $updated = $this->cartModel->update($userId, $productId, $quantity);
        if ($updated === false) {
            return $this->response->fail('Failed to update cart', 500);
        }

        return $this->response->success('Cart updated successfully');
    }

    public function remove(int $userId, int $productId): array
    {
        $removed = $this->cartModel->remove($userId, $productId);
        if ($removed === false) {
            return $this->response->fail('Failed to remove item', 500);
        }

        $count = $this->cartModel->countCart($userId);
        return $this->response->success('Item removed from cart', ['count' => $count]);
    }

    public function clear(int $userId): array
    {
        $cleared = $this->cartModel->clear($userId);
        if ($cleared === false) {
            return $this->response->fail('Failed to clear cart', 500);
        }

        return $this->response->success('Cart cleared', ['count' => 0]);
    }

    public function merge(int $userId, array $cart): array
    {
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

            if ($this->cartModel->add($userId, $item['productId'], $item['quantity'])) {
                $successCount++;
            } else {
                $errors[] = "Failed to add product {$item['productId']}";
            }
        }

        if (!empty($errors)) {
            return $this->response->success('Cart merged with some issues', ['processed' => $successCount, 'errors' => $errors], 207);
        }

        return $this->response->success('Cart merged successfully', ['processed' => $successCount]);
    }

    public function countUser(int $userId): array
    {
        $count = $this->cartModel->countCart($userId);
        return $this->response->success('Cart counted', ['count' => $count]);
    }

    public function countAll(): array
    {
        $count = $this->cartModel->countAll();
        return $this->response->success('Carts counted', ['count' => $count]);
    }

    public function getCartUsers(): array
    {
        $users = $this->cartModel->getCartUsers();
        return $this->response->success('Cart users fetched', ['users' => $users]);
    }
}
