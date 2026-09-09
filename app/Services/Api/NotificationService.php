<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Core\Result;
use App\Models\Notification;

class NotificationService
{
    public function __construct(
        protected Result $result,  
        protected Notification $notificationModel
    ) {}

    public function create(
        string $details, 
        string $type, 
        int $receiver
    ): Result {

        $created = $this->notificationModel->create($details, $type, $receiver);
        if ($created === false) {
            return $this->result->error('Failed to create notification', 500);
        }

        return $this->result->success('Notification created', 201);
    }

    public function countAll(): Result
    {
        $count = $this->notificationModel->countAll();

        return $this->result->success('All notifications counted', ['count' => $count]);
    }

    public function countAllById(
        int $userId
    ): Result {

        $count = $this->notificationModel->countAllById($userId);

        return $this->result->success('All notifications counted', ['count' => $count]);
    }

    public function countUnreadById(
        int $userId
    ): Result {

        $count = $this->notificationModel->countUnreadById($userId);

        return $this->result->success('Unread notifications counted', ['count' => $count]);
    }

    public function getUnread(
        int $userId, 
        int $page, 
        int $limit
    ): Result {

        $notifications = $this->notificationModel->getUnreadById($userId, $page, $limit);
        if ($notifications === false) {
           return $this->result->error('Failed to fetch notifications', 400);
        }

        return $this->result->success('Unread notifications fetched', ['notifications' => $notifications]);
    }

    public function fetchById(
        int $userId, 
        int $page, 
        int $limit
    ): Result {

        $notifications = $this->notificationModel->getAllById($userId, $page, $limit);
        if ($notifications === false) {
           return $this->result->error('Failed to fetch notifications', 400);
        }

        return $this->result->success('Notifications fetched', $notifications);
    }

    public function markAsRead(
        int $userId
    ): Result {
        
        $marked = $this->notificationModel->markAsRead($userId);
        if ($marked === false) {
            return $this->result->error('Failed to mark as read', 500);
        }
        
        return $this->result->success('Notification marked as read');
    }

    public function summary(
        int $userId
    ): Result {

        $notifications = $this->notificationModel
            ->countUnreadByType($userId);

        $summary = [

            'unread' => 0,

            'newOrders' => [
                'count' => 0,
                'last_date' => null,
            ],

            'orderCompletions' => [
                'count' => 0,
                'last_date' => null,
            ],

            'orderCancellations' => [
                'count' => 0,
                'last_date' => null,
            ],

            'itemUpdates' => [
                'count' => 0,
                'last_date' => null,
            ],

            'productReviews' => [
                'count' => 0,
                'last_date' => null,
            ],

            'newMessages' => [
                'count' => 0,
                'last_date' => null,
            ],

            'productApprovals' => [
                'count' => 0,
                'last_date' => null,
            ],

            'fundRedeems' => [
                'count' => 0,
                'last_date' => null,
            ],

            'fundRequests' => [
                'count' => 0,
                'last_date' => null,
            ],

            'fundPayouts' => [
                'count' => 0,
                'last_date' => null,
            ],

            'accountUpdates' => [
                'count' => 0,
                'last_date' => null,
            ],
        ];

        foreach ($notifications as $notification) {

            $type     = $notification['notification_type'];
            $count    = (int) $notification['count'];
            $lastDate = $notification['last_date'];

            $summary['unread'] += $count;

            switch ($type) {

                case 'New Order':
                    $summary['newOrders'] = [
                        'count' => $count,
                        'last_date' => $lastDate,
                    ];
                    break;

                case 'Order Completion':
                    $summary['orderCompletions'] = [
                        'count' => $count,
                        'last_date' => $lastDate,
                    ];
                    break;

                case 'Order Cancellation':
                    $summary['orderCancellations'] = [
                        'count' => $count,
                        'last_date' => $lastDate,
                    ];
                    break;

                case 'Item Update':
                    $summary['itemUpdates'] = [
                        'count' => $count,
                        'last_date' => $lastDate,
                    ];
                    break;

                case 'Product Review':
                    $summary['productReviews'] = [
                        'count' => $count,
                        'last_date' => $lastDate,
                    ];
                    break;

                case 'New Message':
                    $summary['newMessages'] = [
                        'count' => $count,
                        'last_date' => $lastDate,
                    ];
                    break;

                case 'Product Approval':
                    $summary['productApprovals'] = [
                        'count' => $count,
                        'last_date' => $lastDate,
                    ];
                    break;

                case 'Fund Redeem':
                    $summary['fundRedeems'] = [
                        'count' => $count,
                        'last_date' => $lastDate,
                    ];
                    break;

                case 'Fund Request':
                    $summary['fundRequests'] = [
                        'count' => $count,
                        'last_date' => $lastDate,
                    ];
                    break;

                case 'Fund Payout':
                    $summary['fundPayouts'] = [
                        'count' => $count,
                        'last_date' => $lastDate,
                    ];
                    break;

                case 'Account Update':
                    $summary['accountUpdates'] = [
                        'count' => $count,
                        'last_date' => $lastDate,
                    ];
                    break;
            }
        }

        return $this->result->success('Notification stats fetched', $summary);
    }
}
