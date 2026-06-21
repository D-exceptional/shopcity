<?php

namespace App\Services;

use App\Helpers\ResponseManager;
use App\Helpers\MailManager;
use App\Helpers\PushManager;
use App\Helpers\MediaManager;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\Link;
use App\Models\User;
use App\Models\Notification;

class ProductService
{
    protected ResponseManager $response;
    protected MailManager $mailer;
    protected PushManager $push;
    protected MediaManager $mediaManager;
    protected Product $productModel;
    protected ProductMedia $productMedia;
    protected Link $linkModel;
    protected User $userModel;
    protected Notification $notificationModel;
    private string $baseUrl;

    public function __construct(
        ResponseManager $response,
        MailManager $mailer,
        PushManager $push,
        MediaManager $mediaManager,
        Product $productModel, 
        ProductMedia $productMedia, 
        Link $linkModel, 
        User $userModel, 
        Notification $notificationModel
    )
    {
        // Required services for this controller class
        $this->response     = $response;
        $this->mailer       = $mailer;
        $this->push         = $push;
        $this->mediaManager = $mediaManager;
        // Required models for this controller class
        $this->productModel      = $productModel;
        $this->productMedia      = $productMedia;
        $this->linkModel         = $linkModel;
        $this->userModel         = $userModel;
        $this->notificationModel = $notificationModel;
        // Set base URL
        $this->baseUrl = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) ? 'http://localhost/projects/demos/shopcity' : '__remote__site__link__';
    }

    public function formatName(string $name): string {
        // Allow only letters, spaces
        $clean = preg_replace("/[^A-Za-z\s]/", '', $name);

        // Trim leading/trailing spaces and replace multiple spaces with one
        $clean = preg_replace('/\s+/', ' ', trim($clean));

        // Convert to proper case (e.g. "john doe" -> "John Doe")
        $formatted = ucwords(strtolower($clean));

        return $formatted;
    }

    // ---------------- PRODUCT CRUD ----------------
    public function create(array $payload)
    {
        // Normalize inputs
        $payload['name'] = $this->formatName($payload['name']);

        // Create product
        $productId = $this->productModel->create(
            $payload['name'],
            $payload['description'],
            $payload['category'],
            $payload['sub'],
            $payload['price'],
            $payload['slash'],
            $payload['stock'],
            $payload['color'],
            $payload['id']
        );

        // Check operation
        if ($productId === false) {
            return $this->response->fail('Failed to upload product', 500);
        }

        // Attach media
        foreach ($payload['media'] ?? [] as $url) {
            if (!empty($url)) {
                $type = $this->mediaManager->detect($url);
                $this->productMedia->create($url, $type, $productId);
            }
        }

        // Initialize admin mail message
        $adminMessage =  "
            Hello Admin, 
            <br> A new product, <b>{$payload['name']}</b>, was created on the platform!
            <br> Kindly review and take necessary actions. 
        ";
        $date = date('Y-m-d H:i:s');

        // Send admin mails
        $admins = $this->userModel->allByRole('Admin');
        foreach ($admins as $admin) {
            $this->mailer->send('New Product', $admin['email'], $adminMessage);
            
            $notification = $this->notificationModel->create($adminMessage, 'New Product', $admin['user_id'], $date, 'Unread');
            if ($notification === false) {
                return $this->response->fail('Failed to create notification for admin', 500);
            }
        }

        return $this->response->success('Product created successfully', [], 201);
    }

    public function update(array $payload)
    {
        $existing = $this->productModel->find($payload['id']);
        if ($existing === false) {
            return $this->response->fail('Product not found', 404);
        }

        $updated = $this->productModel->update(
            $payload['name'],
            $payload['description'],
            $payload['category'],
            $payload['subcategory'],
            $payload['price'],
            $payload['slash'],
            $payload['stock'],
            $payload['color'],
            $payload['visibility'],
            $payload['reselling'],
            $payload['commission'],
            $payload['id']
        );

        if ($updated === false) {
            return $this->response->fail('Failed to update product', 500);
        }

        // Handle affiliate logic
        if (strtolower($payload['reselling']) === 'enabled') {
            $affiliates = $this->userModel->allByRole('Affiliate');
            foreach ($affiliates as $affiliate) {
                $link = $this->linkModel->findByUser($payload['id'], $affiliate['user_id']);
                if ($link === false) {
                    // Generate details
                    $shortCode = $this->linkModel->generateCode();
                    $shortLink = "{$this->baseUrl}/p?{$shortCode}";
                    $longLink = "{$this->baseUrl}/buy?ref={$affiliate['user_id']}&id={$payload['id']}"; // ref id and product id
                    // Create
                    $this->linkModel->create($payload['id'], $affiliate['user_id'], $shortLink, $longLink, $shortCode, 'Active');
                }
            }
        } else {
            $deleted = $this->linkModel->deleteAll($payload['id']);
            if ($deleted === false) {
                return $this->response->fail('Failed to delete stale product links', 500);
            }
        }

        return $this->response->success('Product updated successfully');
    }

    public function delete(int $id)
    { 
        $product = $this->productModel->find($id);
        if ($product === false) {
            return $this->response->fail('Product not found', 404);
        }

        $deleted = $this->productModel->delete($id);
        if ($deleted === false) {
            return $this->response->fail('Failed to delete product', 500);
        }

        return $this->response->success('Product deleted successfully');
    }

    // ---------------- FETCH METHODS ----------------
    public function findByAll(array $payload)
    {
        $products = $this->productModel->findByAll($payload['page'], $payload['total'], $payload['view']);
        // Prepare data
        return $this->response->success('Product list fetched', $products);
    }

    public function findByCategory(array $payload)
    {
        $products = $this->productModel->findByCategory($payload['category'], $payload['page'], $payload['total'], $payload['view']);
        // Prepare data
        return $this->response->success('Product list fetched', $products);
    }

    public function findByStore(array $payload)
    {
        $products = $this->productModel->findByStore($payload['id'], $payload['page'], $payload['total'], $payload['view']);
        // Prepare data
        return $this->response->success('Product list fetched', $products);
    }

    public function findByStoreCategory(array $payload)
    {
        $products = $this->productModel->findByStoreCategory($payload['id'], $payload['category'], $payload['page'], $payload['total'], $payload['view']);
        // Prepare data
        return $this->response->success('Product list fetched', $products);
    }

    public function findNewArrivals(array $payload)
    {
        $products = $this->productModel->findNewArrivals($payload['page'], $payload['total'], $payload['view']);
        // Prepare data
        return $this->response->success('New arrivals fetched', $products);
    }

    public function findFeatured(array $payload)
    {
        $products = $this->productModel->findFeatured($payload['id'], $payload['page'], $payload['total'], $payload['view']);
        // Prepare data
        return $this->response->success('Featured products fetched', $products);
    }

    public function findTopSelling(array $payload)
    {
        $products = $this->productModel->findTopSelling($payload['page'], $payload['total']);
        // Prepare data
        return $this->response->success('Top selling products fetched', $products);
    }

    public function findByPriceRange(array $payload)
    {
        $products = $this->productModel->findByPriceRange($payload['min'], $payload['max'], $payload['page'], $payload['total'], $payload['view']);
        // Prepare data
        return $this->response->success('Products by price range fetched', $products);
    }

    public function findByMinPrice(array $payload)
    {
        $products = $this->productModel->findByMinPrice($payload['min'], $payload['page'], $payload['total'], $payload['view']);
        // Prepare data
        return $this->response->success('Products above min price fetched', $products);
    }

    public function findByMaxPrice(array $payload)
    {
        $products = $this->productModel->findByMaxPrice($payload['max'], $payload['page'], $payload['total'], $payload['view']);
        // Prepare data
        return $this->response->success('Products below max price fetched', $products);
    }

    public function findByGroupedCategory(array $payload)
    {
        $products = $this->productModel->findByGroupedCategory($payload['page'], $payload['total']);
        // Prepare data
        return $this->response->success('Products categories fetched', $products);
    }

    public function findOne(int $id)
    {
        $product = $this->productModel->findOne($id);
        if ($product === false) {
            return $this->response->fail('Product not found', 404);
        }
        // Prepare data
        return $this->response->success('Product details fetched', $product);
    }

    public function findBySearch(array $payload)
    {
        $products = $this->productModel->findBySearch($payload['search'], $payload['page'], $payload['total'], $payload['view']);
        // Prepare data
        return $this->response->success('Search results fetched', $products);
    }

    public function findByColor(array $payload)
    {
        $products = $this->productModel->findByColor($payload['color'], $payload['page'], $payload['total'], $payload['view']);
        // Prepare data
        return $this->response->success('Product list fetched', $products);
    }

    public function addReview(int $userId, array $payload)
    {
        $review = $this->productModel->addReview($userId, $payload['productId'], $payload['review'], $payload['rating']);
        if ($review === false) {
            return $this->response->fail('Failed to add review', 500);
        }

        // Initialize date
        $date = date('Y-m-d H:i:s');

        // Get product details
        $productDetails = $this->productModel->find($payload['productId']);
        $productName = $productDetails['product_name'];
        $storeId = $productDetails['store_id'];

        // Get vendor details
        $vendorId = $this->storeModel->findUserByStoreId($storeId);
        $vendorDetails = $this->userModel->findById($vendorId);
        $vendorName = $vendorDetails['firstname'] . ' ' .  $vendorDetails['lastname'];
        $vendorEmail = $vendorDetails['email'];

        // Build message
        $vendorMessage = "
            Hi <b>{$vendorName}</b>, 
            <br> Your product, <b>{$productName}</b>, just got a new review. 
            <br> It's a positive sign customers love it.
            <br> Keep updating your store with awesome products like this one.
            <br> Have a great day ahead.
        ";

        // Create in-app notification
        $notification = $this->notificationModel->create($vendorMessage, "Product Review", $vendorId, $date, 'Unread');
        if ($notification === false) {
            return $this->response->fail('Failed to create notification', 500);
        }
        // send vendor mail
        $this->mailer->send("New Product Review", $vendorEmail, $vendorMessage);

        return $this->response->success('Review added successfully');
    }
}
