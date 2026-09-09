<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Request;
use App\Http\Response;
use App\Services\Api\OrderService;

class OrderController extends Controller
{
    public function __construct(
        protected Response $response, 
        protected OrderService $service
    ) {}

    public function trackOrder(Request $request): Response
    {  
        $userId = $request->user()['id'];
        $code   = $request->route('code');
        $result = $this->service->trackOrder($userId, $code);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function getOrder(Request $request): Response
    {
        $orderId = $request->route('id');
        $result = $this->service->getOrder($orderId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function getAllOrders(Request $request): Response
    { 
        $page = $request->route('page');
        $result = $this->service->getAllOrders($page);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function getOrdersByStatus(Request $request): Response
    {
        $status = $request->route('status');
        $page   = $request->route('page');
        $result = $this->service->getOrdersByStatus($status, $page);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function getUserOrders(Request $request): Response
    {
        $userId = $request->user()['id'];
        $page   = $request->route('page');
        $result = $this->service->getUserOrders($userId, $page);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function getStoreOrders(Request $request): Response
    {
        $storeId = $request->route('id');
        $page    = $request->route('page');
        $result = $this->service->getStoreOrders($storeId, $page);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function getStoreOrdersByStatus(Request $request): Response
    { 
        $storeId = $request->route('id');
        $status  = $request->route('status');
        $page    = $request->route('page');
        $result = $this->service->getStoreOrdersByStatus($storeId, $status, $page);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function updateItemStatus(Request $request): Response
    {
        $itemId = $request->route('id');
        $status = $request->route('status');
        $result = $this->service->updateItemStatus($itemId, $status);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function completeOrder(Request $request): Response
    {
        $orderId = $request->route('id');
        $result = $this->service->completeOrder($orderId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function cancelOrder(Request $request): Response
    {
        $user    = $request->user();
        $orderId = $request->route('id');
        $result = $this->service->cancelOrder($user, $orderId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function getSalesSummary(Request $request): Response
    {  
        $userId     = $request->user()['id'];
        $view       = $request->input('view') ?? 'vendor';
        $storeId    = $request->input('storeId') ?? null;
        $period     = $request->input('period') ?? 'today';
        $startDate  = $request->input('start') ?? null;
        $endDate    = $request->input('end') ?? null;

        $result = $this->service->getSalesSummary(
            $view, 
            $userId, 
            $storeId, 
            $period, 
            $startDate, 
            $endDate
        );
        
        return $this->response->json($result->toArray(), $result->status());
    }
}
