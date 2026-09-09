<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Core\Result;
use App\Support\TextManager;
use App\Mail\MailManager;
use App\Notification\PushManager;
use App\Media\CloudinaryManager;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\Link;
use App\Models\User;
use App\Models\Notification;

class ProductService
{
    private string $baseUrl;

    public function __construct(
        protected Result $result, 
        protected TextManager $textManager, 
        protected MailManager $mailManager,
        protected PushManager $pushManager,
        protected CloudinaryManager $cloudinaryManager,
        protected Product $productModel, 
        protected ProductMedia $productMedia, 
        protected Link $linkModel, 
        protected User $userModel, 
        protected Notification $notificationModel
    ) {
        $this->baseUrl = $appUrl;
    }

    public function create(
        string $name, 
        string $description, 
        string $category, 
        string $subcategory, 
        float $price, 
        float $slash, 
        int $stock, 
        string $color, 
        array $media, 
        int $storeId
    ): Result  {

        $name = $this->textManager->formatTitle($name);

        // Create Product
        $productId = $this->productModel->create(
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

        if (!$productId) {
            return $this->result->error('Failed to upload product', 500);
        }

        // Attach Product Media
        foreach ($media ?? [] as $url) {
            if (!empty($url)) {
                $type = $this->cloudinaryManager->detect($url);
                $this->productMedia->create($url, $type, $productId);
            }
        }

        /*
            Loop through the admins, 
            Notify them via email
        */

        // Build Admin Message
        $adminMessage = "
            Hello Admin, 

            <br> A new product, <b>{$name}</b>, was created on the platform!
            <br> Kindly review and take necessary actions. 
        ";

        // Process Admin Notifications
        $admins = $this->userModel->allByRole('Admin');
        foreach ($admins as $admin) {

            // Create In-App Admin Notification
            $notification = $this->notificationModel->create($adminMessage, 'New Product', $admin['user_id']);
            if ($notification === false) {
                return $this->result->error('Failed to create notification for admin', 500);
            }

            // Send Admin Email
            $this->mailManager->sendSimpleMail('New Product', $admin['email'], $adminMessage);
        }

        return $this->result->success('Product created successfully', [], 201);
    }

    public function update(
        string $name, 
        string $description, 
        string $category, 
        string $subcategory, 
        float $price, 
        float $slash, 
        int $stock, 
        string $color, 
        string $visibility, 
        string $reselling, 
        int $commission, 
        int $productId
    ): Result {

        $existing = $this->productModel->find($productId);
        if ($existing === false) {
            return $this->result->error('Product not found', 404);
        }

        if ($price < 1000) {
            return $this->result->error('Invalid amount. Enter a valid amount...starting from 1,000 and above', 400);
        }

        if ($stock <= 1) {
            return $this->result->error('Stock count is too low', 400);
        }

        $updated = $this->productModel->update(
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

        if ($updated === false) {
            return $this->result->error('Failed to update product', 500);
        }

        // Create Affiliate Links If Enabled
        if ($reselling === 'enabled') {

            $affiliates = $this->userModel->allByRole('Affiliate');
            foreach ($affiliates as $affiliate) {

                $affiliateId = $affiliate['user_id'];

                $link = $this->linkModel->findByUser($productId, $affiliateId);
                if ($link === false) {

                    // Generate Links
                    $shortCode = $this->linkModel->generateCode();
                    $shortLink = "{$this->baseUrl}/p/{$shortCode}";                           // Example: https://mysite.com/p/asX56yXC
                    $longLink  = "{$this->baseUrl}/product/{$productId}/ref/{$affiliateId}"; // Example: https://mysite.com/product/22/ref/10

                    // Create Link
                    $this->linkModel->create($productId, $affiliateId, $shortLink, $longLink, $shortCode, 'Active');
                }
            }
        } else {

            $deleted = $this->linkModel->deleteAll($productId);
            if ($deleted === false) {
                return $this->result->error('Failed to delete stale product links', 500);
            }
        }

        return $this->result->success('Product updated successfully');
    }

    public function metadata(
        string $visibility,  
        bool $isFeatured,
        int $productId
    ): Result {

        $existing = $this->productModel->find($productId);
        if ($existing === false) {
            return $this->result->error('Product not found', 404);
        }

        $updated = $this->productModel->metadata( 
            $visibility, 
            $isFeatured,
            $productId
        );

        if ($updated === false) {
            return $this->result->error('Failed to update product metadata', 500);
        }

        return $this->result->success('Product metadata updated successfully');
    }

    public function delete(
        int $productId
    ): Result { 

        $product = $this->productModel->find($productId);
        if ($product === false) {
            return $this->result->error('Product not found', 404);
        }

        $deleted = $this->productModel->delete($productId);
        if ($deleted === false) {
            return $this->result->error('Failed to delete product', 500);
        }

        return $this->result->success('Product deleted successfully');
    }
    
    public function findByAll(
        int $page, 
        int $total, 
        string $view
    ): Result {

        $products = $this->productModel->findByAll($page, $total, $view);

        return $this->result->success('Product list fetched', $products);
    }

    public function findByCategory(
        string $category, 
        int $page, 
        int $total, 
        string $view
    ): Result {

        $products = $this->productModel->findByCategory($category, $page, $total, $view);

        return $this->result->success('Product list fetched', $products);
    }

    public function findByStore(
        int $storeId, 
        int $page, 
        int $total, 
        string $view
    ): Result {

        $products = $this->productModel->findByStore($storeId, $page, $total, $view);
        
        return $this->result->success('Product list fetched', $products);
    }

    public function findByStoreCategory(
        int $storeId, 
        string $category, 
        int $page,
        int $total, 
        string $view
    ): Result {

        $products = $this->productModel->findByStoreCategory($storeId, $category, $page, $total, $view);
        
        return $this->result->success('Product list fetched', $products);
    }

    public function findNewArrivals(
        int $page, 
        int $total, 
        string $view
    ): Result {

        $products = $this->productModel->findNewArrivals($page, $total, $view);
        
        return $this->result->success('New arrivals fetched', $products);
    }

    public function findFeatured(
        int $page, 
        int $total, 
        string $view
    ): Result {

        $products = $this->productModel->findFeatured($page, $total, $view);
        
        return $this->result->success('Featured products fetched', $products);
    }

    public function findTopSelling(
        int $page, 
        int $total
    ): Result {

        $products = $this->productModel->findTopSelling($page, $total);
        
        return $this->result->success('Top selling products fetched', $products);
    }

    public function findByPriceRange(
        float $min, 
        float $max, 
        int $page, 
        int $total, 
        string $view
    ): Result {

        $products = $this->productModel->findByPriceRange($min, $max, $page, $total, $view);
        
        return $this->result->success('Products by price range fetched', $products);
    }

    public function findByMinPrice(
        float $min, 
        int $page, 
        int $total, 
        string $view
    ): Result {

        $products = $this->productModel->findByMinPrice($min, $page, $total, $view);
        
        return $this->result->success('Products above min price fetched', $products);
    }

    public function findByMaxPrice(
        float $max, 
        int $page, 
        int $total, 
        string $view
    ): Result {

        $products = $this->productModel->findByMaxPrice($max, $page, $total, $view);
        
        return $this->result->success('Products below max price fetched', $products);
    }

    public function findByGroupedCategory(
        int $page, 
        int $total
    ): Result {

        $products = $this->productModel->findByGroupedCategory($page, $total);
        
        return $this->result->success('Products categories fetched', $products);
    }

    public function findOne(
        int $productId
    ): Result {

        $product = $this->productModel->findOne($productId);
        if ($product === false) {
            return $this->result->error('Product not found', 404);
        }
        
        return $this->result->success('Product details fetched', $product);
    }

    public function findBySearch(
        string $query, 
        int $page, 
        int $total, 
        string $view
    ): Result {

        $products = $this->productModel->findBySearch($query, $page, $total, $view);
        
        return $this->result->success('Search results fetched', $products);
    }

    public function findByColor(
        string $color, 
        int $page, 
        int $total, 
        string $view
    ): Result {

        $products = $this->productModel->findByColor($color, $page, $total, $view);
        
        return $this->result->success('Product list fetched', $products);
    }

    public function addReview(
        int $userId, 
        int $productId, 
        string $review, 
        int $rating
    ): Result {

        $review = $this->productModel->addReview($userId, $productId, $review, $rating);
        if ($review === false) {
            return $this->result->error('Failed to add review', 500);
        }

        // Get Product Details
        $productData = $this->productModel->find($productId);
        $productName = $productData['product_name'];
        $storeId     = $productData['store_id'];

        // Get Vendor Details
        $vendorId    = $this->storeModel->findUserByStoreId($storeId);
        $vendorData  = $this->getBiodata($vendorId);
        $vendorName  = $vendorData['name'];
        $vendorEmail = $vendorData['email'];

        // Build Vendor Message
        $vendorMessage = "
            Hi <b>{$vendorName}</b>, 

            <br> Your product, <b>{$productName}</b>, just got a new review. 
            <br> It's a positive sign customers love it.
            <br> Keep updating your store with awesome products like this one.
            <br> Have a great day ahead.
        ";

        // Create In-App Customer Notification
        $notification = $this->notificationModel->create($vendorMessage, "Product Review", $vendorId);
        if ($notification === false) {
            return $this->result->error('Failed to create notification', 500);
        }

        // Send Vendor Email
        $this->mailManager->sendSimpleMail("New Product Review", $vendorEmail, $vendorMessage);

        return $this->result->success('Review added successfully');
    }

    private function getBiodata(
        int $userId
    ): array {

        $userData = $this->userModel->findById($userId);

        return [
            'name'  => $userData['firstname'] . ' ' . $userData['lastname'],
            'email' => $userData['email'],
            'role'  => $userData['user_role']
        ];
    }
}
