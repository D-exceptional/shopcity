<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\WishlistService;

class WishlistController extends Controller
{
    public function __construct(
        protected Response $response, 
        protected WishlistService $service
    ) {}

    public function view(Request $request): Response
    { 
        $userId = $request->user()['id'];
        $result = $this->service->view($userId);

        return $this->response->flash($result);
    }

    public function add(Request $request): Response
    {
        $userId    = $request->user()['id'];
        $productId = $request->route('id');
        $result = $this->service->add($productId, $userId);

        return $this->response->flash($result);
    }

    public function merge(Request $request): Response
    {
        $userId   = $request->user()['id'];
        $wishlist = $request->input('wishlist');
        $result = $this->service->merge($wishlist, $userId);

        return $this->response->flash($result);
    }

    public function remove(Request $request): Response
    { 
        $userId    = $request->user()['id'];
        $productId = $request->route('id');
        $result = $this->service->remove($productId, $userId);

        return $this->response->flash($result);
    }

    public function clear(): Response
    { 
        $userId = $request->user()['id'];
        $result = $this->service->clear($userId);

        return $this->response->flash($result);
    }
}
