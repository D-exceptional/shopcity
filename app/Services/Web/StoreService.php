<?php

declare(strict_types=1);

namespace App\Services\Web;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\Wallet;
use App\Models\Mail;
use App\Models\Notification;

class StoreService
{
    public function __construct(
        protected User $userModel,
        protected Order $orderModel,
        protected Product $productModel,
        protected Store $storeModel,
        protected Wallet $walletModel,
        protected Mail $mailModel, 
        protected Notification $notificationModel,  
    ) {}

    public function couponCreate(
        int $storeId
    ): array {

        $storeData  = $this->getStoreData($storeId);

        $data = [
            'storeId'     => $storeId,
            'storeAvatar' => $storeData['avatar'],
            'storeName'   => $storeData['name'],
        ];

        return $data;
    }

    public function couponList(
        int $storeId,
        int $page
    ): array {

        $couponList = $this->storeModel->findCouponsByStore($storeId, $page);
        $storeData  = $this->getStoreData($storeId);

        $data = [
            'couponList'  => $couponList,
            'storeId'     => $storeId,
            'storeAvatar' => $storeData['avatar'],
            'storeName'   => $storeData['name'],
        ];

        return $data;
    }

    public function customers(
        int $storeId,
        string $type,
        int $page,
    ): array {

        $customerList = $this->storeModel->getStoreCustomersByType($storeId, $type, $page);
        $storeData    = $this->getStoreData($storeId);

        $data = [
            'customerList' => $customerList,
            'storeId'     => $storeId,
            'type'        => $type,
            'storeAvatar' => $storeData['avatar'],
            'storeName'   => $storeData['name'],
        ];

        return $data;
    }

    public function dashboard(
        int $userId,
        int $storeId
    ): array {

        $storeOrderStats   = $this->orderModel->getVendorStoreStats($userId, $storeId);
        $activeProducts    = $this->productModel->countProductsByType(null, $storeId, 'Visible', 'vendor');
        $pendingProducts   = $this->productModel->countProductsByType(null, $storeId, 'Hidden', 'vendor');
        $storeReviewsStats = $this->productModel->countReviewsByVendor($userId, $storeId);
        $storeCouponStats  = $this->storeModel->getStoreCouponStats($storeId);
        $storeData         = $this->getStoreData($storeId);

        $data = [
            'storeOrderStats'   => $storeOrderStats,
            'activeProducts'    => $activeProducts,
            'pendingProducts'   => $pendingProducts,
            'storeReviewsStats' => $storeReviewsStats,
            'storeCouponStats'  => $storeCouponStats,
            'storeAvatar'       => $storeData['avatar'],
            'storeName'         => $storeData['name'],
        ];

        return $data;
    }

    public function orderList(
        int $storeId, 
        string $status, 
        int $page
    ): array {

        $orderList = $this->orderModel->getStoreOrdersByStatus($storeId, $status, $page);
        $storeData = $this->getStoreData($storeId);

        $data = [
            'orderList'   => $orderList,
            'storeId'     => $storeId,
            'status'      => $status,
            'storeAvatar' => $storeData['avatar'],
            'storeName'   => $storeData['name'],
        ];

        return $data;
    }

    public function productCreate(
        int $storeId
    ): array {

        $storeData = $this->getStoreData($storeId);

        $data = [
            'storeId'     => $storeId,
            'storeAvatar' => $storeData['avatar'],
            'storeName'   => $storeData['name'],
        ];

        return $data;
    }

    public function productList(
        int $storeId,
        int $page
    ): array {

        $storeProducts = $this->productModel->findByStore($storeId, $page, 20, 'vendor');
        $storeData     = $this->getStoreData($storeId);

        $data = [
            'storeProducts' => $storeProducts,
            'storeId'       => $storeId,
            'storeAvatar'   => $storeData['avatar'],
            'storeName'     => $storeData['name'],
        ];

        return $data;
    }

    public function productView(
        int $storeId, 
        int $productId
    ): array {

        $productDetails = $this->productModel->findOne($productId);
        $storeList      = $this->storeModel->findStoresByUser($userId);

        $data = [
            'productDetails' => $productDetails,
            'storeId'        => $storeId,
            'productId'      => $productId,
            'storeAvatar'    => $storeData['avatar'],
            'storeName'      => $storeData['name'],
        ];

        return $data;
    }

    public function settings(
        int $storeId
    ): array {

        $storeDetails = $this->storeModel->findOne($storeId);
        $storeData  = $this->getStoreData($storeId);

        $data = [
            'storeDetails' => $storeDetails,
            'storeId'      => $storeId,
            'storeAvatar'  => $storeData['avatar'],
            'storeName'    => $storeData['name'],
        ];

        return $data;
    }

    // -------------------------------------
    // Store Details Helper
    // ----------------------------------
    private function getStoreData(
        int $storeId
    ): array {

        $storeData   = $this->storeModel->findOne($storeId);
        $storeAvatar = $storeData['store_avatar'];
        $storeName   = $storeData['store_name'];

        $data = [
            'avatar' => $storeAvatar,
            'name'   => $storeName
        ];

        return $data;
    }
}
