<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\StoreService;

class StoreController extends Controller
{
    public function __construct(
        protected Response $response, 
        protected StoreService $service
    ) {}

    public function createStore(Request $request): Response
    {
        $userId      = $request->user()['id'];
        $name        = $request->input('name');
        $avatar      = $request->input('avatar');
        $description = $request->input('description');
        $narration   = $request->input('narration');
        $delivery    = $request->input('delivery');

        $result = $this->service->createStore(
            $name, 
            $avatar, 
            $description, 
            $narration, 
            $delivery,
            $userId
        );

        return $this->response->json($result->toArray(), $result->status());
    }

    public function updateStoreDetails(Request $request): Response
    {
        $storeId     = $request->route('id');
        $name        = $request->input('name');
        $description = $request->input('description');
        $delivery    = $request->input('delivery');

        $result = $this->service->updateStoreDetails(
            $name, 
            $description, 
            $delivery, 
            $storeId
        );

        return $this->response->json($result->toArray(), $result->status());
    }

    public function updateStoreSocials(Request $request): Response
    {
        $storeId   = $request->route('id');
        $facebook  = $request->input('facebook');
        $instagram = $request->input('instagram');
        $tiktok    = $request->input('tiktok');
        $twitter   = $request->input('twitter');

        $result = $this->service->updateStoreSocials(
            $facebook, 
            $instagram, 
            $tiktok, 
            $twitter, 
            $storeId
        );

        return $this->response->json($result->toArray(), $result->status());
    }

    public function updateStoreAvatar(Request $request): Response
    {
        $storeId   = $request->route('id');
        $newAvatar = $request->input('url');
        $result = $this->service->updateStoreAvatar($newAvatar, $storeId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function updateStoreStatus(Request $request): Response
    {
        $storeId = $request->route('id');
        $status  = $request->route('status');
        $result = $this->service->updateStoreStatus($status, $storeId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function deleteStore(Request $request): Response
    {
        $storeId = $request->route('id');
        $result = $this->service->deleteStore($storeId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findOne(Request $request): Response
    { 
        $storeId = $request->route('id');
        $result = $this->service->findOne($storeId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findByStatus(Request $request): Response
    { 
        $status = $request->route('status');
        $page   = $request->route('page');
        $result = $this->service->findByStatus($status, $page);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findByUser(Request $request): Response
    { 
        $userId = $request->route('id');
        $page   = $request->route('page');
        $result = $this->service->findByUser($userId, $page);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function createCoupon(Request $request): Response
    {
        $storeId  = $request->route('id');
        $discount = $request->input('discount');
        $code     = $request->input('code');
        $result = $this->service->createCoupon($code, $discount, $storeId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findCoupon(Request $request): Response
    {
        $storeId = $request->route('id');
        $coupon  = $request->route('code');
        $result = $this->service->findCoupon($coupon, $storeId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function updateCoupon(Request $request): Response
    {
        $couponId = $request->route('id');
        $discount = $request->input('discount');
        $status   = $request->input('status');
        $code     = $request->input('code');
        $result = $this->service->updateCoupon($code, $discount, $status, $couponId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function deleteSingleCoupon(Request $request): Response
    { 
        $couponId = $request->route('id');
        $result = $this->service->deleteSingleCoupon($couponId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function deleteCouponByStore(Request $request): Response
    {
        $storeId = $request->route('id');
        $result = $this->service->deleteCouponByStore($storeId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findCouponsByStore(Request $request): Response
    {
        $storeId = $request->route('id');
        $page    = $request->route('page');
        $result = $this->service->findCouponsByStore($storeId, $page);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findCouponsByStoreAndStatus(Request $request): Response
    { 
        $storeId = $request->route('id');
        $status  = $request->route('status');
        $page    = $request->route('page');
        $result = $this->service->findCouponsByStoreAndStatus($storeId, $status, $page);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function countStoresByStatus(Request $request): Response
    { 
        $result = $this->service->countStoresByStatus();

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findStoreCustomers(Request $request): Response
    {
        $storeId = $request->route('id');
        $type    = $request->input('type');
        $page    = $request->input('page');
        $result = $this->service->findStoreCustomers($storeId, $type, $page);

        return $this->response->json($result->toArray(), $result->status());
    }
}
