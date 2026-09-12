<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Core\Result;
use App\Events\Store\StoreCreated;
use App\Events\Store\StoreStatusUpdated;
use App\Events\Store\StoreAvatarUpdated;
use App\Events\EventDispatcher;
use App\Support\TextManager;
use App\Models\Store;

class StoreService
{
    private string $baseUrl;

    public function __construct(
        protected Result $result, 
        protected EventDispatcher $eventDispatcher,
        protected TextManager $textManager,  
        protected Store $storeModel, 
    ) {
        $this->baseUrl = $appUrl; 
    }

    public function createStore(
        string $name, 
        string $avatar, 
        string $description, 
        string $type, 
        string $delivery,
        int $userId
    ): Result {

        $name = $this->textManager->formatTitle($name);

        // Create Store
        $storeId = $this->storeModel->createStore(
            $name, 
            $avatar, 
            $description, 
            $type, 
            'Pending',
            $delivery,
            $userId
        );

        if ($storeId === null) {
            return $this->result->error('Failed to create store', 500);
        }

        $this->eventDispatcher->dispatch(
            new StoreCreated(
                name: $name,
                vendorId: $userId
            )
        );

        return $this->result->success('Store created successfully', [], 201);
    }

    public function updateStoreDetails( 
        string $name, 
        string $description, 
        string $delivery, 
        int $storeId
    ): Result {

        $updated = $this->storeModel->updateStoreDetails($name, $description, $delivery, $storeId);
        if ($updated === false) {
            return $this->result->error('Failed to update store', 500);
        }

        return $this->result->success('Details updated successfully');
    }

    public function updateStoreSocials(
        string $facebook, 
        string $instagram, 
        string $tiktok, 
        string $twitter, 
        int $storeId
    ): Result
    {
        $updated = $this->storeModel->updateStoreSocials($facebook, $instagram, $tiktok, $twitter, $storeId);
        if ($updated === false) {
            return $this->result->error('Failed to update store', 500);
        }

        return $this->result->success('Socials updated successfully');
    }

    public function updateStoreAvatar(
        string $newAvatar,
        int $storeId, 
    ): Result {

        $oldAvatar = $this->storeModel->findStoreAvatar($storeId);
        if ($oldAvatar === false) {
            return $this->result->error('Store avatar not found', 404);
        }

        $updated = $this->storeModel->updateStoreAvatar($newAvatar, $storeId);
        if ($updated === false) {
            return $this->result->error('Failed to update store avatar', 500);
        }

        if ($oldAvatar !== 'None') {

            $this->eventDispatcher->dispatch(
                new StoreAvatarUpdated(
                    oldAvatar: $oldAvatar,
                    newAvatar: $newAvatar,
                )
            );

        }

        return $this->result->success('Avatar updated successfully');
    }

    public function updateStoreStatus(
        string $status, 
        int $storeId
    ): Result {

        $status = $this->storeModel->updateStoreStatus($status, $storeId);
        if ($status === false) {
            return $this->result->error('Failed to update status', 500);
        }

        $vendorId = $this->storeModel->findUserByStoreId($storeId);

        $this->eventDispatcher->dispatch(
            new StoreStatusUpdated(
                status: $status,
                vendorId: $vendorId
            )
        );

        return $this->result->success('Status updated successfully');
    }

    public function deleteStore(
        int $storeId
    ): Result {

        $deleted = $this->storeModel->deleteStore($storeId);
        if ($deleted === false) {
            return $this->result->error('Failed to delete store', 505);
        }

        return $this->result->success('Store deleted successfully');
    }

    public function findOne(
        int $storeId
    ): Result { 

        $store = $this->storeModel->findOne($storeId);
        if ($store === false) {
            return $this->result->error('Failed to fetch store', 505);
        }

        return $this->result->success('Store fetched successfully', ['store' => $store]);
    }

    public function findByStatus(
        string $status, 
        int $page
    ): Result { 

        $stores = $this->storeModel->findStoresByStatus($status, $page);
        if ($stores === false) {
            return $this->result->error('Failed to fetch stores', 505);
        }

        return $this->result->success('Stores fetched successfully', ['stores' => $stores]);
    }

    public function findByUser(
        int $userId, 
        int $page
    ): Result { 

        $stores = $this->storeModel->findStoresByUser($userId, $page);
        if ($stores === false) {
            return $this->result->error('Failed to fetch stores', 505);
        }

        return $this->result->success('Stores fetched successfully', ['stores' => $stores]);
    }

    public function createCoupon(
        string $code, 
        int $discount, 
        int $storeId
    ): Result {

        $coupon = $this->storeModel->createCoupon($code, $discount, $storeId);
        if ($coupon === false) {
            return $this->result->error('Failed to create coupon', 500);
        }

        return $this->result->success('Coupon created successfully', [], 201);
    }

    public function findCoupon(
        string $couponCode, 
        int $storeId
    ): Result {

        $coupon = $this->storeModel->findCoupon($couponCode, $storeId);
        if ($coupon === false) {
            return $this->result->error('Coupon code not found', 500);
        }

        return $this->result->success('Coupon fetched successfully', $coupon);
    }

    public function updateCoupon(
        string $couponCode, 
        int $discount, 
        string $status, 
        int $couponId
    ): Result {

        $updated = $this->storeModel->updateCoupon($couponCode, $discount, $status, $couponId);
        if ($updated === false) {
            return $this->result->error('Failed to update coupon', 500);
        }

        return $this->result->success('Coupon updated successfully');
    }

    public function deleteSingleCoupon(
        int $couponId
    ): Result { 

        $deleted = $this->storeModel->deleteSingleCoupon($couponId);
        if ($deleted === false) {
            return $this->result->error('Failed to delete coupon', 500);
        }

        return $this->result->success('Coupon deleted successfully');
    }

    public function deleteCouponByStore(
        int $storeId
    ): Result {

        $deleted = $this->storeModel->deleteCouponByStore($storeId);
        if ($deleted === false) {
            return $this->result->error('Failed to delete coupons', 500);
        }

        return $this->result->success('Coupons deleted successfully');
    }

    public function findCouponsByStore(
        int $storeId, 
        int $page
    ): Result {

        $coupons = $this->storeModel->findCouponsByStore($storeId, $page);
        if (count($coupons['coupons']) === 0) {
            return $this->result->error('Failed to fetch coupons', 200);
        }

        return $this->result->success('Coupons fetched successfully', $coupons);
    }

    public function findCouponsByStoreAndStatus(
        int $storeId, 
        string $status, 
        int $page
    ): Result { 

        $coupons = $this->storeModel->findCouponsByStoreAndStatus($storeId, $status, $page);
        if (count($coupons['coupons']) === 0) {
            return $this->result->error('Failed to fetch coupons', 200);
        }
       
        return $this->result->success('Coupons fetched successfully', $coupons);
    }

    public function countStoresByStatus(): Result
    { 
        $counts = $this->storeModel->countStoresByStatus();
        if (count($counts) === 0) {
            return $this->result->error('Failed to count stores', 500);
        }
        
        return $this->result->success('Counts fetched successfully',  ['counts' => $counts]);
    }

    public function findStoreCustomers(
        int $id, 
        string $type, 
        int $page
    ): Result {

        $customers = $this->storeModel->getStoreCustomersByType($storeId, $type, $page);
        if (count($customers['customers']) === 0) {
            return $this->result->error('Failed to fetch customers', 500);
        }
        
        return $this->result->success('Customers fetched successfully', $customers);
    }
}
