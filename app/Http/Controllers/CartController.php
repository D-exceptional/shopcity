<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\CartService;

class CartController extends Controller
{
    public function __construct( 
        protected Response $response, 
        protected CartService $service
    ) {}

    public function view(Request $request): Response
    {
        $userId = $request->user()['id'];
        $result = $this->service->view($userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function add(Request $request): Response
    {
        $userId    = $request->user()['id'];
        $productId = $request->route('id');
        $quantity  = $request->route('quantity');
        $result = $this->service->add($productId, $quantity, $userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function update(Request $request): Response
    { 
        $userId    = $request->user()['id'];
        $productId = $request->route('id');
        $quantity  = $request->route('quantity');
        $result = $this->service->update($productId, $quantity, $userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function remove(Request $request): Response
    { 
        $userId    = $request->user()['id'];
        $productId = $request->route('id');
        $result = $this->service->remove($productId, $userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function clear(): Response
    {
        $userId = $request->user()['id'];
        $result = $this->service->clear($userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function merge(Request $request): Response
    {
        $userId = $request->user()['id'];
        $cart   = $request->input('cart');
        $result = $this->service->merge($cart, $userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function countUser(): Response
    {
        $userId = $request->user()['id'];
        $result = $this->service->countUser($userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function countAll(): Response
    { 
        $result = $this->service->countAll();

        return $this->response->json($result->toArray(), $result->status());
    }

    public function getCartUsers(): Response
    {
        $result = $this->service->getCartUsers();
        
        return $this->response->json($result->toArray(), $result->status());
    }
}
