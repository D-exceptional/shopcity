<?php

declare(strict_types=1);

namespace App\Services\Web;

use App\Models\Notification;

class NotificationService
{
    public function __construct( 
        protected Notification $notificationModel
    ) {}

    public function summary(
        int $userId
    ): array {

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

        return $summary;
    }
}
