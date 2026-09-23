<?php

declare(strict_types=1);

namespace App\Services\Web;

use App\Models\User;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\Wallet;

class HomeService 
{
    public function __construct(
        protected User $userModel,
        protected Order $cartModel,
        protected Category $categoryModel,
        protected Order $orderModel,
        protected Product $productModel,
        protected Store $storeModel,
        protected Wallet $walletModel,
    ) {}

    public function brands(
        int $page
    ): array {

        $topBrands = $this->storeModel->getTopBrands($page);

        $data = [
            'topBrands' => $topBrands,
        ];

        return $data;
    }

    public function cart(
        ?int $userId
    ): array {

        $cartItems = $userId ? $this->cartModel->view($userId) : [];

        $totalAmount = 0;

        if (!empty($cartItems)) {

            foreach ($cartItems as $item) {
                
                $totalAmount += (float)$item['total_price'];
            }
        }

        $data = [
            'cartItems'   => $cartItems,
            'totalAmount' => $totalAmount,
        ];

        return $data;
    }

    public function checkout(
       ?int $userId
    ): array {

        $cartItems = $userId ? $this->cartModel->view($userId) : [];

        $totalAmount = 0;

        if (!empty($cartItems)) {

            foreach ($cartItems as $item) {
                
                $totalAmount += (float)$item['total_price'];
            }
        }

        // Get Page Data
        $userDetails    = $userId ? $this->userModel->findById($userId) : null;
        $walletBalance  = $userId ? $this->walletModel->getBalance(env('PAYMENT_TABLE'), $userId) : 0;
        $billingDetails = $userId ? $this->userModel->getBillingDetails($userId) : [];

        $data = [
            'cartItems'      => $cartItems,
            'totalAmount'    => $totalAmount,
            'userDetails'    => $userDetails,
            'walletBalance'  => $walletBalance,
            'billingDetails' => $billingDetails,
        ];

        return $data;
    }

    public function home(
        int $page
    ): array {

        $allProducts      = $this->productModel->findByAll($page);
        $newArrivals      = $this->productModel->findNewArrivals();
        $featuredProducts = $this->productModel->findFeatured(); 
        $topProducts      = $this->productModel->findTopSelling();
        $productColors    = $this->productModel->groupByColor();

        $data = [
            'allProducts'      => $allProducts,
            'newArrivals'      => $newArrivals,
            'featuredProducts' => $featuredProducts,
            'topProducts'      => $topProducts,
            'productColors'    => $productColors,
        ];

        return $data;
    }

    public function orderView(
        ?int $orderId
    ): array {

        $orderDetails = $orderId ? $this->orderModel->getOrder($orderId) : [];

        $statusMap = [

            'Pending' => [
                'status' => ['prop' => 'danger'],
                'action' => ['prop' => 'info', 'text' => 'View'],
            ],

            'Processing' => [
                'status' => ['prop' => 'info'],
                'action' => ['prop' => 'info', 'text' => 'View'],
            ],

            'Shipped' => [
                'status' => ['prop' => 'warning'],
                'action' => ['prop' => 'success', 'text' => 'I have received this item'],
            ],

            'Delivered' => [
                'status' => ['prop' => 'success'],
                'action' => ['prop' => 'success', 'text' => 'Completed'],
            ],

        ];

        $data = [
            'orderDetails' => $orderDetails,
            'statusMap'    => $statusMap,
        ];

        return $data;
    }

    public function orders(
        ?int $userId,
        int $page
    ): array {

        $orderList = $userId ? $this->orderModel->getUserOrders($userId, $page) : [];

        $data = [
            'orderList' => $orderList,
        ];

        return $data;
    }

    public function productView(
        int $productId
    ): array {

        $productDetails   = $this->productModel->findOne($productId);
        $featuredProducts = $this->productModel->findFeatured(); 
        $relatedProducts  = $this->productModel->findByRelated($productDetails['category'], $productId);
        $productColors    = $this->productModel->groupByColor();

        $data = [
            'productDetails'   => $productDetails,
            'featuredProducts' => $featuredProducts,
            'relatedProducts'  => $relatedProducts,
            'productColors'    => $productColors,
        ];

        return $data;
    }

    public function productList(
       string $filter, 
       string $value, 
       int $page
    ): array {

        $pageProducts    = ($filter === 'category') 
            ? $this->productModel->findByCategory($value, $page) 
            : $this->productModel->findByColor($value, $page);
        $productColors    = $this->productModel->groupByColor(); 
        $featuredProducts = $this->productModel->findFeatured(); 

        $data = [
            'filter'           => $filter,
            'value'            => $value,
            'productColors'    => $productColors,
            'pageProducts'     => $pageProducts,
            'featuredProducts' => $featuredProducts,
        ];

        return $data;
    }

    public function productSearch(
        string $search, 
        int $page
    ): array {

        $matchedProducts  = $this->productModel->findBySearch($search, $page);
        $productColors    = $this->productModel->groupByColor(); 
        $featuredProducts = $this->productModel->findFeatured(); 

        $data = [
            'search'           => $search,
            'productColors'    => $productColors,
            'matchedProducts'  => $matchedProducts,
            'featuredProducts' => $featuredProducts,
        ];

        return $data;
    }

    public function productStore(
       int $storeId, 
       string $filter, 
       string $value, 
       int $page
    ): array {

        $storeDetails    = $this->storeModel->findOne($storeId);
        $storeCategories = $this->categoryModel->group($storeId);
        $storeColors     = $this->productModel->groupByColor($storeId); 
        $storeProducts   = ($filter === 'category') 
            ? $this->productModel->findByStoreCategory($storeId, $value, $page) 
            : $this->productModel->findByStoreColor($storeId, $value, $page);
        $featuredProducts = $this->productModel->findFeatured(); 

        $data = [
            'storeId'          => $storeId,
            'filter'           => $filter,
            'value'            => $value,
            'storeDetails'     => $storeDetails,
            'storeCategories'  => $storeCategories,
            'storeColors'      => $storeColors,
            'storeProducts'    => $storeProducts,
            'featuredProducts' => $featuredProducts,
        ];

        return $data;
    }

    public function profile(
        ?int $userId
    ): array {

        $userDetails    = $userId ? $this->userModel->findById($userId) : null;
        $billingDetails = $userId ? $this->userModel->getBillingDetails($userId) : [];

        $data = [
            'userDetails'    => $userDetails,
            'billingDetails' => $billingDetails,
        ];

        return $data;
    }

    public function store(
        int $storeId,
        int $page
    ): array {

        $storeDetails     = $this->storeModel->findOne($storeId);
        $storeCategories  = $this->categoryModel->group($storeId);
        $storeColors      = $this->productModel->groupByColor($storeId);
        $storeProducts    = $this->productModel->findByStore($storeId, $page); 
        $featuredProducts = $this->productModel->findFeatured(); 

        $data = [
            'storeId'          => $storeId,
            'storeDetails'     => $storeDetails,
            'storeCategories'  => $storeCategories,
            'storeColors'      => $storeColors,
            'storeProducts'    => $storeProducts,
            'featuredProducts' => $featuredProducts,
        ];

        return $data;
    }

    public function wallet(
        ?int $userId
    ): array {

        $walletBalance = $userId ? $this->walletModel->getBalance(env('PAYMENT_TABLE'), $userId) : 0;
        $userDetails   = $userId ? $this->userModel->findById($userId) : null;

        $data = [
            'walletBalance' => $walletBalance,
            'userDetails'   => $userDetails
        ];

        return $data;
    }

    public function wishlist(
        ?int $userId,
        int $page
    ): array {

        $wishlistItems = $userId ? $this->wishlistModel->view($userId, $page) : [];

        $data = [
            'wishlistItems' => $wishlistItems,
        ];

        return $data;
    }
}
