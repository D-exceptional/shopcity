<?php

declare(strict_types=1);

namespace App\Services;

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
}
