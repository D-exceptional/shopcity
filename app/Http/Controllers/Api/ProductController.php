<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Request;
use App\Http\Response;
use App\Services\Api\ProductService;

class ProductController extends Controller
{
    public function __construct(
        protected Response $response, 
        protected ProductService $service
    ) {}

    // ---------------- PRODUCT CRUD ----------------
    public function create(Request $request): Response
    {
        $storeId     = $request->route('id');  
        $name        = $request->input('name');  
        $description = $request->input('description');  
        $category    = $request->input('category');  
        $subcategory = $request->input('subcategory');      
        $price       = $request->input('price');     
        $slash       = $request->input('slash');  
        $stock       = $request->input('stock');     
        $color       = $request->input('color');   
        $media       = $request->input('media');

        $result = $this->service->create(
            $name, 
            $description, 
            $category, 
            $subcategory, 
            $price, 
            $slash, 
            $stock, 
            $color, 
            $media, 
            $storeId
        );

        return $this->response->json($result->toArray(), $result->status());
    }

    public function update(Request $request): Response
    {
        $productId   = $request->route('id');  
        $name        = $request->input('name');  
        $description = $request->input('description');  
        $category    = $request->input('category');  
        $subcategory = $request->input('subcategory');      
        $price       = $request->input('price');     
        $slash       = $request->input('slash');  
        $stock       = $request->input('stock');     
        $color       = $request->input('color');   
        $visibility  = $request->input('visibility'); 
        $reselling   = $request->input('reselling'); 
        $commission  = $request->input('commission'); 

        $result = $this->service->update(
            $name, 
            $description, 
            $category, 
            $subcategory, 
            $price, 
            $slash, 
            $stock, 
            $color, 
            $visibility, 
            $reselling, 
            $commission, 
            $productId
        );

        return $this->response->json($result->toArray(), $result->status());
    }

    public function metadata(Request $request): Response
    {
        $productId   = $request->route('id');     
        $visibility  = $request->input('visibility');  
        $isFeatured  = $request->input('featured'); 

        $result = $this->service->metadata( 
            $visibility,  
            $isFeatured,
            $productId
        );

        return $this->response->json($result->toArray(), $result->status());
    }

    public function delete(Request $request): Response
    { 
        $productId = $request->route('id');
        $result = $this->service->delete($productId);

        return $this->response->json($result->toArray(), $result->status());
    }

    // ---------------- FETCH METHODS ----------------
    public function findByAll(Request $request): Response
    {
        $page  = $request->input('page');
        $total = $request->input('total');
        $view  = $request->input('view');
        $result = $this->service->findByAll($page, $total, $view);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findByCategory(Request $request): Response
    {
        $category = $request->route('category');
        $page     = $request->input('page');
        $total    = $request->input('total');
        $view     = $request->input('view');
        $result = $this->service->findByCategory($category, $page, $total, $view);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findByStore(Request $request): Response
    {
        $storeId = $request->route('id');
        $page    = $request->input('page');
        $total   = $request->input('total');
        $view    = $request->input('view');
        $result = $this->service->findByStore($storeId, $page, $total, $view);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findByStoreCategory(Request $request): Response
    {
        $storeId  = $request->route('id');
        $category = $request->route('category');
        $page     = $request->input('page');
        $total    = $request->input('total');
        $view     = $request->input('view');
        $result = $this->service->findByStoreCategory($storeId, $category, $page, $total, $view);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findNewArrivals(Request $request): Response
    {
        $page  = $request->input('page');
        $total = $request->input('total');
        $view  = $request->input('view');
        $result = $this->service->findNewArrivals($page, $total, $view);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findFeatured(Request $request): Response
    {
        $page  = $request->input('page');
        $total = $request->input('total');
        $view  = $request->input('view');
        $result = $this->service->findFeatured($page, $total, $view);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findTopSelling(Request $request): Response
    {
        $page  = $request->input('page');
        $total = $request->input('total');
        $result = $this->service->findTopSelling($page, $total);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findByPriceRange(Request $request): Response
    {
        $min   = $request->input('min');
        $max   = $request->input('max');
        $page  = $request->input('page');
        $total = $request->input('total');
        $view  = $request->input('view');
        $result = $this->service->findByPriceRange($min, $max, $page, $total, $view);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findByMinPrice(Request $request): Response
    {
        $min   = $request->input('min');
        $page  = $request->input('page');
        $total = $request->input('total');
        $view  = $request->input('view');
        $result = $this->service->findByMinPrice($min, $page, $total, $view);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findByMaxPrice(Request $request): Response
    {
        $max   = $request->input('max');
        $page  = $request->input('page');
        $total = $request->input('total');
        $view  = $request->input('view');
        $result = $this->service->findByMaxPrice($max, $page, $total, $view);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findByGroupedCategory(Request $request): Response
    {
        $page  = $request->input('page');
        $total = $request->input('total');
        $result = $this->service->findByGroupedCategory($page, $total);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findOne(Request $request): Response
    {
        $productId = $request->route('id');
        $result = $this->service->findOne($productId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findBySearch(Request $request): Response
    {
        $query = $request->route('query');
        $page  = $request->input('page');
        $total = $request->input('total');
        $view  = $request->input('view');
        $result = $this->service->findBySearch($query, $page, $total, $view);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findByColor(Request $request): Response
    {
        $color = $request->route('color');
        $page  = $request->input('page');
        $total = $request->input('total');
        $view  = $request->input('view');
        $result = $this->service->findByColor($color, $page, $total, $view);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function addReview(Request $request): Response
    {
        $userId    = $request->user()['id'];
        $productId = $request->route('id');
        $review    = $request->input('review');
        $rating    = $request->input('rating');
        $result = $this->service->addReview($userId, $productId, $review, $rating);

        return $this->response->json($result->toArray(), $result->status());
    }
}
