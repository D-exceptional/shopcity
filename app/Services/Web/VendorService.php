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

class VendorService
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

        $orderStats        = $this->orderModel->getVendorOrderStats($userId);
        $productStats      = $this->productModel->getVendorProductStats($userId);
        $storeStats        = $this->storeModel->getVendorStoreStats($userId);
        $walletStats       = $this->walletModel->getVendorWalletStats($userId);
        $mailStats         = $this->mailModel->getVendorMailStats($email, $name);
        $notificationStats = $this->notificationModel->getVendorNotificationStats($userId);

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

        $data = [
            'totalInbox'  => $mailCount['inbox'],
            'totalOutbox' => $mailCount['outbox'],
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

        $vendorNotifications = $this->notificationModel->getAllById($userId);

        $data = [
            'vendorNotifications' => $vendorNotifications,
        ];

        return $data;
    }

    public function profile(
        int $userId
    ): array {

        $userDetails   = $this->userModel->findById($userId);
        $socialDetails = $this->userModel->getSocials($userId);
        $bankDetails   = $this->walletModel->getBankDetails($userId);
        $bankList      = $bankManager->loadBanks($userDetails['country']);

        $data = [
            'userDetails'   => $userDetails,
            'socialDetails' => $socialDetails,
            'bankDetails'   => $bankDetails,
            'bankList'      => $bankList,
        ];

        return $data;
    }

    public function storeList(
        int $userId
    ): array {

        $storeStats = $this->storeModel->getVendorStoreStats($userId);
        $storeList  = $this->storeModel->findStoresByUser($userId);

        $data = [
            'storeStats' => $storeStats,
            'storeList'  => $storeList,
        ];

        return $data;
    }

    public function wallet(
        int $userId
    ): array {

        $walletStats = $this->walletModel->getVendorWalletStats($userId);

        $data = [
            'walletStats' => $walletStats,
        ];

        return $data;
    }

    public function withdrawal(
        int $userId
    ): array {

        $withdrawals   = $this->walletModel->getPaymentsByUser(env('WITHDRAWAL_TABLE'), $userId);
        $walletBalance = $this->walletModel->getBalance(env('PAYOUT_WALLET'), $userId);
        $bankDetails   = $this->walletModel->getBankDetails($userId);

        $data = [
            'withdrawals'   => $withdrawals,
            'walletBalance' => $walletBalance,
            'bankDetails'   => $bankDetails,
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
