<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Core\Result;
use App\Support\TextManager;
use App\Mail\MailManager;
use App\Notification\PushManager;
use App\Media\CloudinaryManager;
use App\Models\Store;
use App\Models\User;
use App\Models\Notification;

class StoreService
{
    private string $baseUrl;

    public function __construct(
        protected Result $result, 
        protected TextManager $textManager, 
        protected MailManager $mailManager, 
        protected PushManager $pushManager, 
        protected CloudinaryManager $cloudinaryManager, 
        protected Store $storeModel, 
        protected User $userModel, 
        protected Notification $notificationModel
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

        $vendorData  = $this->getBiodata($userId);
        $vendorName  = $vendorData['name'];
        $vendorEmail = $vendorData['email'];

        // Build Vendor Message
        $vendorMessage = "
            Hi <b>{$vendorName}</b>, 

            <br> Your store is currently <b>pending approval</b>. 
            <br> Our team is reviewing your store details. Once approved, you'll be able to start selling. 
            <br> We'll notify you as soon as the status changes.
            <br> Thank you for your patience.
        ";

        // Send Vendor Email
        $this->mailManager->sendSimpleMail('Store Creation Successful', $vendorEmail, $vendorMessage);

        // Send Vendor Push Notification
        $vendorPushMessage = $this->textManager->formatPushMessage($vendorMessage);

        $this->pushManager->send('Single Vendor', $userId, 'Store Creation Successful', $vendorPushMessage, [
            'url' => "{$this->baseUrl}/login",
            'type' => 'store'
        ]);


        /*
            Loop through the admins, 
            Create notifications
            Notify them via email
            Notify them via push if available
        */

        // Build Admin Message
        $adminMessage =  "
            Hello Admin, 

            <br> A new store, <b>{$name}</b>, was created on the platform!
            <br> Kindly review and take necessary actions. 
        ";

        // Process Admin Notifications
        $admins = $this->userModel->allByRole('Admin');
        foreach ($admins as $admin) {

            // Create In-App Admin Notification
            $notification = $this->notificationModel->create($adminMessage, 'New Store', $admin['user_id']);
            if ($notification === false) {
                return $this->result->error('Failed to create notification for admin', 500);
            }

            // Send Admin Email
            $this->mailManager->sendSimpleMail('New Store', $admin['email'], $adminMessage);

            // Send Admin Push Notification
            $adminPushMessage = $this->textManager->formatPushMessage($adminMessage);

            $this->push->send('Single Admin', $admin['user_id'], 'New Store', $adminPushMessage, [
                'url' => "{$this->baseUrl}/admin/",
                'type' => 'store'
            ]);
        }

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

        // Use Cloudinary Events later to delete old avatar
        $this->cloudinaryManager->delete($oldAvatar);

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

        $vendorId    = $this->storeModel->findUserByStoreId($storeId);
        $vendorData  = $this->getBiodata($vendorId);
        $vendorName  = $vendorData['name'];
        $vendorEmail = $vendorData['email'];

        // Build Venor Message Based On Status
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
                <br> Please contact support at <b>support@shopcity.com</b> or visit <b><a href='{$this->baseUrl}/contact'>Appeal Page</a></b> to resolve this and restore your store. 
                <br> We value your partnership and hope to have you back soon.
            ",
        ];

        // Fallback In Case Of Unknown Status
        $vendorMessage = $statusMessages[$status] ?? "
            Hi <b>{$vendorName}</b>,

            <br> There has been an update to your store status. 
            <br> Please check your vendor dashboard for more details.
        ";

        // Send Vendor Email
        $this->mailManager->sendSimpleMail('Store Status Updated', $vendorEmail, $vendorMessage);

        // Send Vendor Push Notification
        $vendorPushMessage = $this->textManager->formatPushMessage($vendorMessage);

        $this->pushManager->send('Single Vendor', $vendorId, 'Store Status Update', $vendorPushMessage, [
            'url' => "{$this->baseUrl}/login",
            'type' => 'store'
        ]);

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
