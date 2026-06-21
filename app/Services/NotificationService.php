<?php

namespace App\Services;

use App\Helpers\ResponseManager;
use App\Models\Notification;

class NotificationService
{
    protected ResponseManager $response;
    protected Notification $notificationModel;

    public function __construct(ResponseManager $response, Notification $notificationModel)
    {
        // Required helpers for this controller class
        $this->response  = $response;
        // Required model for this controller class
        $this->notificationModel = $notificationModel;
    }

    /**
     * Create a new notification
     */
    public function create(array $payload)
    {
        $created = $this->notificationModel->create($payload['details'], $payload['type'], $payload['receiver'], $payload['date'], $payload['status']);
        if ($created === false) {
            return $this->response->fail('Failed to create notification', 500);
        }

        return $this->response->success('Notification created', 201);
    }

    /**
     * Count all notifications
    */
    public function countAll()
    {
        $count = $this->notificationModel->countAll();
        return $this->response->success('All notifications counted', ['count' => $count]);
    }

    /**
     * Count notifications by id
     */
    public function countAllById(int $userId)
    {
        $count = $this->notificationModel->countAllById($userId);
        return $this->response->success('All notifications counted', ['count' => $count]);
    }

    /**
     * Count unread notifications by id
    */
    public function countUnreadById(int $userId)
    {
        $count = $this->notificationModel->countUnreadById($userId);
        return $this->response->success('Unread notifications counted', ['count' => $count]);
    }

    /**
     * Get unread notifications
     */
    public function getUnread(int $userId, int $limit, int $offset)
    {
        $notifications = $this->notificationModel->getUnreadById($userId, $limit, $offset);
        if ($notifications === false) {
           return $this->response->fail('Failed to fetch notifications', 400);
        }

        // Prepare data
        return $this->response->success('Unread notifications fetched', ['notifications' => $notifications]);
    }

    /**
     * Fetch notifications by id
    */
    public function fetchById(int $userId, int $limit, int $offset)
    {
        $notifications = $this->notificationModel->getAllById($userId, $limit, $offset);
        if ($notifications === false) {
           return $this->response->fail('Failed to fetch notifications', 400);
        }

        // Prepare data
        return $this->response->success('Notifications fetched', $notifications);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(int $userId)
    {
        $marked = $this->notificationModel->markAsRead($userId);
        if ($marked === false) {
            return $this->response->fail('Failed to mark as read', 500);
        }
        
        return $this->response->success('Notification marked as read');
    }
}
