<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Request;
use App\Http\Response;
use App\Services\Web\StoreService;

class StoreController extends Controller
{
    public function __construct(
        protected Response $response,
        protected StoreService $service
    ) {}

    public function couponCreate(Request $request): Response
    {
        $storeId = $request->route('id');  
        $viewData = $this->service->couponCreate($storeId);

        return $this->response->view('store.coupon_create', $viewData);
    }

    public function couponList(Request $request): Response
    {
        $storeId = $request->route('id'); 
        $page    = $request->route('page') ?? 1; 
        $viewData = $this->service->couponList($storeId, $page);

        return $this->response->view('store.coupon_list', $viewData);
    }

    public function customers(Request $request): Response
    {
        $storeId = $request->route('id'); 
        $type    = $request->route('type') ?? null; 
        $page    = $request->route('page') ?? 1; 
        $viewData = $this->service->customers($storeId, $type, $page);

        return $this->response->view('store.customers', $viewData);
    }

    public function dashboard(Request $request): Response
    {
        $userId  = $request->user()['id']; 
        $storeId = $request->route('id');  
        $viewData = $this->service->dashboard($userId, $storeId);
            
        return $this->response->view('store.dashboard', $viewData);
    }

    public function orderList(Request $request): Response
    {
        $storeId = $request->route('id');
        $status  = $request->route('status') ?? null; 
        $page    = $request->route('page') ?? 1;
        $viewData = $this->service->orderList($storeId, $status, $page);

        return $this->response->view('store.order_list', $viewData);
    }

    public function productCreate(Request $request): Response
    {
        $storeId = $request->route('id');
        $viewData = $this->service->productCreate($storeId);

        return $this->response->view('store.product_create', $viewData);
    }

    public function productList(Request $request): Response
    {
        $storeId = $request->route('id');
        $page    = $request->route('page') ?? 1;
        $viewData = $this->service->productList($storeId, $page);

        return $this->response->view('store.product_list', $viewData);
    }

    public function productView(Request $request): Response
    {
        $storeId   = $request->route('id');
        $productId = $request->route('pid');
        $viewData = $this->service->productView($storeId, $productId);

        return $this->response->view('store.product_view', $viewData);
    }

    public function settings(Request $request): Response
    {
        $storeId = $request->route('id');
        $viewData = $this->service->settings($storeId);

        return $this->response->view('store.settings', $viewData);
    }
}
