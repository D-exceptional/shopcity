<?php

namespace App\Services;

use App\Helpers\ResponseManager;
use App\Helpers\MailManager;
use App\Helpers\PushManager;
use App\Helpers\MediaManager;
use App\Models\Store;
use App\Models\User;
use App\Models\Notification;
use Exception;

class StoreService
{
    protected ResponseManager $response;
    protected MailManager $mailer;
    protected PushManager $push;
    protected MediaManager $mediaManager;
    protected Store $storeModel;
    protected User $userModel;
    protected Notification $notificationModel;
    private string $baseUrl;

    public function __construct(
        ResponseManager $response, 
        MailManager $mailer, 
        PushManager $push, 
        MediaManager $mediaManager, 
        Store $storeModel, 
        User $userModel, 
        Notification $notificationModel
    )
    {
        // Required helpers for this controller class
        $this->response  = $response;
        $this->mailer    = $mailer;
        $this->push      = $push;
        $this->mediaManager = $mediaManager;
        // Required models for this controller class
        $this->storeModel         = $storeModel;
        $this->userModel          = $userModel;
        $this->notificationModel  = $notificationModel;
        // Set base URL
        $this->baseUrl = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) ? 'http://localhost/projects/demos/shopcity' : '__remote__site__link__';
    }

    public function formatName(string $name): string 
    {
        // Allow only letters, spaces
        $clean = preg_replace("/[^A-Za-z\s]/", '', $name);

        // Trim leading/trailing spaces and replace multiple spaces with one
        $clean = preg_replace('/\s+/', ' ', trim($clean));

        // Convert to proper case (e.g. "john doe" -> "John Doe")
        $formatted = ucwords(strtolower($clean));

        return $formatted;
    }

    /**
     * Converts HTML message to push-friendly plain text
     *
     * @param string $htmlMessage The HTML content
     * @return string Plain text suitable for push notifications
     */
    private function processMessage(string $htmlMessage): string
    {
        // Replace <br> (and variants) with \n
        $textWithLineBreaks = preg_replace('/<br\s*\/?>/i', "\n", $htmlMessage);

        // Remove all other HTML tags
        $plainText = strip_tags($textWithLineBreaks);

        // Trim extra whitespace at start/end and return
        return trim($plainText);
    }

    /**
     * Create a new store
    */
    public function createStore(int $userId, array $payload)
    {
        // Normalize inputs
        $payload['name'] = $this->formatName($payload['name']);

        // Default status
        $status = 'Pending';

        // Create store
        $storeId = $this->storeModel->createStore(
            $payload['name'], 
            $payload['avatar'], 
            $payload['description'], 
            $payload['narration'], 
            $status, 
            $payload['delivery'], 
            $payload['facebook'], 
            $payload['instagram'], 
            $payload['tiktok'], 
            $payload['twitter'], 
            $userId
        );

        if ($storeId === null) {
            return $this->response->fail('Failed to create store', 500);
        }

        $vendorDetails = $this->userModel->findById($userId);
        $vendorName = $vendorDetails['firstname'] . ' ' . $vendorDetails['lastname'];
        $vendorEmail = $vendorDetails['email'];

        $mail = [
            'subject' => 'Store Creation Successful',
            'message' => "Hi <b>{$vendorName}</b>, 
                <br> Your store is currently <b>pending approval</b>. 
                <br> Our team is reviewing your store details. Once approved, you'll be able to start selling. 
                <br> We'll notify you as soon as the status changes.
                <br> Thank you for your patience.
            "
        ];

        $this->mailer->send($mail['subject'], $vendorEmail, $mail['message']);
        // Send push notification to vendor
        $processedMessage = $this->processMessage($mail['message']);
        $this->push->send('Single Vendor', $userId, $mail['subject'], $processedMessage, [
            'url' => "{$this->baseUrl}/login",
            'type' => 'store'
        ]);

        // Initialize admin mail message
        $adminMessage =  "
            Hello Admin, 
            <br> A new store, <b>{$payload['name']}</b>, was created on the platform!
            <br> Kindly review and take necessary actions. 
        ";
        $date = date('Y-m-d H:i:s');

        // Send admin mails
        $admins = $this->userModel->allByRole('Admin');
        foreach ($admins as $admin) {
            $this->mailer->send('New Store', $admin['email'], $adminMessage);
            // Send push notification to admins
            $processedMessage = $this->processMessage($adminMessage);
            $this->push->send('Single Admin', $admin['user_id'], 'New Store', $processedMessage, [
                'url' => "{$this->baseUrl}/admin/",
                'type' => 'store'
            ]);
            
            $notification = $this->notificationModel->create($adminMessage, 'New Store', $admin['user_id'], $date, 'Unread');
            if ($notification === false) {
                return $this->response->fail('Failed to create notification for admin', 500);
            }
        }

        return $this->response->success('Store created successfully', [], 201);
    }

    /**
     * Update store details
    */
    public function updateStoreDetails(array $payload)
    {
        $updated = $this->storeModel->updateStoreDetails($payload['name'], $payload['description'], $payload['delivery'], $payload['id']);
        if ($updated === false) {
            return $this->response->fail('Failed to update store', 500);
        }
        return $this->response->success('Details updated successfully');
    }

    /**
     * Update store socials
    */
    public function updateStoreSocials(array $payload)
    {
        $updated = $this->storeModel->updateStoreSocials($payload['facebook'], $payload['instagram'], $payload['tiktok'], $payload['twitter'], $payload['id']);
        if ($updated === false) {
            return $this->response->fail('Failed to update store', 500);
        }
        return $this->response->success('Socials updated successfully');
    }

    /**
     * Update store avatar
    */
    public function updateStoreAvatar(int $id, string $url)
    {
        $avatar = $this->storeModel->findStoreAvatar($id);
        if ($avatar === false) {
            return $this->response->fail('Store avatar not found', 404);
        }

        // Update DB with new URL
        $updated = $this->storeModel->updateStoreAvatar($url, $id);
        if ($updated === false) {
            return $this->response->fail('Failed to update store avatar', 500);
        }

        // Delete old file from Cloudinary
        $result = $this->mediaManager->delete($avatar['store_avatar']);
        if (!isset($result['result']) || $result['result'] !== 'ok') {
            return $this->response->fail('Failed to delete file from Cloudinary', 500);
        }

        return $this->response->success('Avatar updated successfully');
    }

    /**
     * Update store status
    */
    public function updateStoreStatus(array $payload)
    {
        $status = $this->storeModel->updateStoreStatus($payload['status'], $payload['id']);
        if ($status === false) {
            return $this->response->fail('Failed to update status', 500);
        }

        $vendorId = $this->storeModel->findUserByStoreId($payload['id']);
        $vendorDetails = $this->userModel->findById($vendorId);
        $vendorName = $vendorDetails['firstname'] . ' ' . $vendorDetails['lastname'];
        $vendorEmail = $vendorDetails['email'];

        // Build message based on status
        $statusMessages = [
            'Active' => "
                Hi <b>{$vendorName}</b>, 
                <br> Great news! 🎉 Your store is now <b>active</b>. 
                <br> Customers can start placing orders, and you'll receive credits into your savings wallet for every order fulfilled. 
                <br> Keep your inventory updated to maximize your sales.
                <br> We're excited to see your growth on our platform!
            ",

            'Deactivated' => "
                Hi <b>{$vendorName}</b>, 
                <br> Your store has been <b>deactivated</b>. 
                <br> This may be due to policy violations, inactivity, or other issues. 
                <br> Please contact support at <b>support@mrsamase.com</b> or visit <b><a href='{$this->baseUrl}/contact'>Appeal Page</a></b> to resolve this and restore your store. 
                <br> We value your partnership and hope to have you back soon.
            ",
        ];

        // Fallback in case of unknown status
        $message = $statusMessages[$payload['status']] ?? "
            Hi <b>{$vendorName}</b>, 
            <br> There has been an update to your store status. 
            <br> Please check your vendor dashboard for more details.
        ";

        $mail = [
            'subject' => 'Store Status Updated',
            'message' => $message
        ];

        $this->mailer->send($mail['subject'], $vendorEmail, $mail['message']);
        // Send push notification to vendor
        $processedMessage = $this->processMessage($message);
        $this->push->send('Single Vendor', $vendorId, 'Store Status Update', $processedMessage, [
            'url' => "{$this->baseUrl}/login",
            'type' => 'store'
        ]);

        return $this->response->success('Status updated successfully');
    }

    /**
     * Delete store
    */
    public function deleteStore(int $id)
    {
        $deleted = $this->storeModel->deleteStore($id);
        if ($deleted === false) {
            return $this->response->fail('Failed to delete store', 505);
        }
        return $this->response->success('Store deleted successfully');
    }

    /**
     * Find a store
    */
    public function findOne(int $id)
    { 
        $store = $this->storeModel->findOne($id);
        if ($store === false) {
            return $this->response->fail('Failed to fetch store', 505);
        }
        // Prepare data
        return $this->response->success('Store fetched successfully', ['store' => $store]);
    }

    /**
     * Find stores by status
    */
    public function findByStatus(string $status, int $page)
    { 
        $stores = $this->storeModel->findStoresByStatus($status, $page);
        if ($stores === false) {
            return $this->response->fail('Failed to fetch stores', 505);
        }
        // Prepare data
        return $this->response->success('Stores fetched successfully', ['stores' => $stores]);
    }

     /**
     * Find stores by users
    */
    public function findByUser(int $id, int $page)
    { 
        $stores = $this->storeModel->findStoresByUser($id, $page);
        if ($stores === false) {
            return $this->response->fail('Failed to fetch stores', 505);
        }
        // Prepare data
        return $this->response->success('Stores fetched successfully', ['stores' => $stores]);
    }

    /**
     * Create coupon
    */
    public function createCoupon(string $code, int $discount, int $storeId)
    {
        $coupon = $this->storeModel->createCoupon($code, $discount, $storeId);
        if ($coupon === false) {
            return $this->response->fail('Failed to create coupon', 500);
        }
        return $this->response->success('Coupon created successfully', [], 201);
    }

    /**
     * Create coupon
    */
    public function findCoupon(string $couponCode, int $storeId)
    {
        $coupon = $this->storeModel->findCoupon($couponCode, $storeId);
        if ($coupon === false) {
            return $this->response->fail('Coupon code not found', 500);
        }
        return $this->response->success('Coupon fetched successfully', $coupon);
    }

    /**
     * Update coupon
    */
    public function updateCoupon(string $couponCode, int $discount, string $status, int $couponId)
    {
        $updated = $this->storeModel->updateCoupon($couponCode, $discount, $status, $couponId);
        if ($updated === false) {
            return $this->response->fail('Failed to update coupon', 500);
        }
        return $this->response->success('Coupon updated successfully');
    }

    /**
     * Delete a coupon
    */
    public function deleteSingleCoupon(int $id)
    { 
        $deleted = $this->storeModel->deleteSingleCoupon($id);
        if ($deleted === false) {
            return $this->response->fail('Failed to delete coupon', 500);
        }
        return $this->response->success('Coupon deleted successfully');
    }

    /**
     * Delete store coupons
    */
    public function deleteCouponByStore(int $id)
    {
        $deleted = $this->storeModel->deleteCouponByStore($id);
        if ($deleted === false) {
            return $this->response->fail('Failed to delete coupons', 500);
        }
        return $this->response->success('Coupons deleted successfully');
    }

    /**
     * Find store coupons
    */
    public function findCouponsByStore(int $id, int $page)
    {
        $coupons = $this->storeModel->findCouponsByStore($id, $page);
        if (count($coupons['coupons']) === 0) {
            return $this->response->fail('Failed to fetch coupons', 200);
        }
        // Prepare data
        return $this->response->success('Coupons fetched successfully', $coupons);
    }

    /**
     * Find coupons by store & status
    */
    public function findCouponsByStoreAndStatus(int $id, string $status, int $page)
    { 
        $coupons = $this->storeModel->findCouponsByStoreAndStatus($id, $status, $page);
        if (count($coupons['coupons']) === 0) {
            return $this->response->fail('Failed to fetch coupons', 200);
        }
        // Prepare data
        return $this->response->success('Coupons fetched successfully', $coupons);
    }

    /** Coun stores by status (Pending/Active/Deactivated) */
    public function countStoresByStatus()
    { 
        $counts = $this->storeModel->countStoresByStatus();
        if (count($counts) === 0) {
            return $this->response->fail('Failed to count stores', 500);
        }
        // Prepare data
        return $this->response->success('Counts fetched successfully',  ['counts' => $counts]);
    }

    /**
     * Find customers by store 
    */
    public function findStoreCustomers(int $id, string $type, int $page) 
    {
        $customers = $this->storeModel->getStoreCustomersByType($id, $type, $page);
        if (count($customers['customers']) === 0) {
            return $this->response->fail('Failed to fetch customers', 500);
        }
        // Prepare data
        return $this->response->success('Customers fetched successfully', $customers);
    }
}
