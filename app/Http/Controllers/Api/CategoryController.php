<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Request;
use App\Http\Response;
use App\Services\Api\CategoryService;

class CategoryController extends Controller
{
    public function __construct(
        protected Response $response, 
        protected CategoryService $service
    ) {}

    public function all(Request $request): Response
    {
        $result = $this->service->all();

        return $this->response->json($result->toArray(), $result->status());
    }

    public function group(Request $request): Response
    {
        $result = $this->service->group();

        return $this->response->json($result->toArray(), $result->status());
    }

    public function create(Request $request): Response
    {
        $category = $request->input('category');
        $result = $this->service->create($category);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function update(Request $request): Response
    {
        $categoryId = $request->route('id');
        $name       = $request->input('name');
        $result = $this->service->update($name, $categoryId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function delete(Request $request): Response
    { 
        $categoryId = $request->route('id');
        $result = $this->service->delete($categoryId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function count(Request $request): Response
    { 
        $result = $this->service->count();

        return $this->response->json($result->toArray(), $result->status());
    }
    
    public function fetch(Request $request): Response
    { 
        $category = $request->route('category');
        $result = $this->service->fetch($category);

        return $this->response->json($result->toArray(), $result->status());
    }
}
