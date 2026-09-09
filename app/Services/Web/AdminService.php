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

class AdminService 
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

    public function dashboard(
        int $userId, 
        string $email,
        string $name
    ): array {

        $orderStats        = $this->orderModel->getAdminOrderStats();
        $productStats      = $this->productModel->getAdminProductStats();
        $storeStats        = $this->storeModel->countStoresByStatus();
        $walletStats       = $this->walletModel->getAdminWalletStats($userId);
        $mailStats         = $this->mailModel->getAdminMailStats($email, $name);
        $notificationStats = $this->notificationModel->getAdminNotificationStats($userId);

        $data = [
            'orderStats'        => $orderStats,
            'productStats'      => $productStats,
            'storeStats'        => $storeStats,
            'walletStats'       => $walletStats,
            'mailStats'         => $mailStats,
            'notificationStats' => $notificationStats,
        ];

        return $data;
    }

    public function mailCompose(
        string $email,
        string $name
    ): array {

        // Get counts
        $mailCount = $this->getMailCounts($email, $name);

        // Get users
        $allAdmins    = $this->userModel->allByRole('Admin');
        $allCustomers = $this->userModel->allByRole('Customer');
        $allVendors   = $this->userModel->allByRole('Vendor');

        $data = [
            'allAdmins'    => $allAdmins,
            'allCustomers' => $allCustomers,
            'allVendors'   => $allVendors,
            'totalInbox'   => $mailCount['inbox'],
            'totalOutbox'  => $mailCount['outbox'],
        ];

        return $data;
    }

    public function mailRead(
        int $mailId,
        string $email,
        string $name
    ): array {

        // Get mail details
        $mailDetails = $this->mailModel->getMail($mailId);

        // Get counts
        $mailCount = $this->getMailCounts($email, $name);

        $data = [
            'mailDetails' => $mailDetails,
            'totalInbox'  => $mailCount['inbox'],
            'totalOutbox' => $mailCount['outbox'],
        ];

        return $data;
    }

    public function mailSent(
        int $page,
        string $email,
        string $name
    ): array {

        // Get outbox mails
        $outboxMails = $this->mailModel->getOutbox($name, $page);

        // Get counts
        $mailCount = $this->getMailCounts($email, $name);

        $data = [
            'outboxMails' => $outboxMails,
            'totalInbox'  => $mailCount['inbox'],
            'totalOutbox' => $mailCount['outbox'],
        ];

        return $data;
    }

    public function mailBox(
        int $page,
        string $email,
        string $name
    ): array {

        // Get inbox mails
        $inboxMails = $this->mailModel->getInbox($email, $page);

        // Get counts
        $mailCount = $this->getMailCounts($email, $name);

        $data = [
            'inboxMails'  => $inboxMails,
            'totalInbox'  => $mailCount['inbox'],
            'totalOutbox' => $mailCount['outbox'],
        ];

        return $data;
    }

    public function notification(
        int $userId
    ): array {

        $adminNotifications = $this->notificationModel->getAllById($userId);

        $data = [
            'adminNotifications' => $adminNotifications,
        ];

        return $data;
    }

    public function orderList(
        string $status,
        int $page
    ): array {

        $orderList = in_array($status, ['Pending', 'Completed', 'Cancelled']) 
            ? $this->orderModel->getOrdersByStatus($status, $page)
            : $this->orderModel->getAllOrders($page);

        $data = [
            'orderList' => $orderList,
            'status'    => $status
        ];

        return $data;
    }

    public function orderView(
        int $orderId
    ): array {

        $orderDetails = $this->orderModel->getOrder($orderId);

        $data = [
            'orderDetails' => $orderDetails,
        ];

        return $data;
    }

    public function payouts(
        string $status,
        int $page
    ): array {

        $paymentList = $this->walletModel->getWithdrawalsByStatus($status, $page);

        $data = [
            'paymentList' => $paymentList,
            'status'      => $status
        ];

        return $data;
    }

    public function productList(
        int $page
    ): array {

        $allProducts = $this->productModel->findByAll($page, 20, 'admin');

        $data = [
            'allProducts' => $allProducts,
        ];

        return $data;
    }

    public function productView(
        int $productId
    ): array {

        $productDetails = $this->productModel->findOne($productId);

        $data = [
            'productDetails' => $productDetails,
        ];

        return $data;
    }

    public function profile(
        int $userId
    ): array {

        $userDetails = $this->userModel->findById($userId);

        $data = [
            'userDetails' => $userDetails,
        ];

        return $data;
    }

    public function storeList(
        int $page
    ): array {

        $storeList = $this->storeModel->findStoresByStatus(null, $page);

        $data = [
            'storeList' => $storeList,
        ];

        return $data;
    }

    public function users(
        string $role,
        int $page
    ): array {

        $userList = $this->userModel->getByRole($role, $page);

        $data = [
            'userList' => $userList,
            'role'     => $role,
        ];

        return $data;
    }

    // -------------------------------------
    // Mail Counts Helper
    // ----------------------------------
    private function getMailCounts(
        string $email,
        string $name
    ): array {

        // Get counts
        $inboxCount  = $this->mailModel->countInbox($email);
        $outboxCount = $this->mailModel->countOutbox($name);

        $counts = [
            'inbox'  => $inboxCount,
            'outbox' => $outboxCount,
        ];

        return $counts;
    }
}
