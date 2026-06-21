<?php

namespace App\Models;

use PDO;

class Notification extends Database
{
    /**
     * Create a new notification
    */
    public function create(string $details, string $type, int $receiver, string $date, string $status): int
    {
        $stmt = $this->db->prepare("INSERT INTO general_notifications (notification_details, notification_type, notification_receiver, notification_date, notification_status) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$details, $type, $receiver, $date, $status]);
    }

    /**
     * Count all notifications
     */
    public function countAll(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM general_notifications");
        return (int)$stmt->fetchColumn();
    }

    /**
     * Count notifications by id
    */
    public function countAllById(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM general_notifications WHERE notification_receiver = ?");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Count total unread notifications by id
    */
    public function countUnreadById(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM general_notifications WHERE notification_receiver = ? AND notification_status = 'Unread'");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Count unread notifications grouped by id
    */
    public function countUnreadGroupedById(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM general_notifications WHERE notification_receiver = ? AND notification_status = 'Unread' GROUP BY notification_type");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Count notifications by type (with latest date)
    */
    public function countByTypeWithLastDate(string $type, int $userId): ?array
    {
        // --- First query: count unread notifications by type ---
        $countStmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM general_notifications 
            WHERE notification_type = ? 
            AND notification_receiver = ? 
            AND notification_status = 'Unread'
        ");
        $countStmt->execute([$type, $userId]);
        $count = (int)$countStmt->fetchColumn();

        // --- Second query: latest unseen incoming_mail date ---
        $dateStmt = $this->db->prepare("
            SELECT notification_date 
            FROM general_notifications 
            WHERE notification_type = ?
            AND notification_receiver = ? 
            AND notification_status = 'Unread' 
            ORDER BY notification_id DESC 
            LIMIT 1
        ");
        $dateStmt->execute([$type, $userId]);
        $lastDate = $dateStmt->fetchColumn();

        // Return both in one response
        return [
            'count' => $count,
            'last_date' => $lastDate ?: null
        ];
    }

    /**
     * Get unread notifications by id
    */
    public function getUnreadById(?int $userId = null, int $limit = 20, int $page = 1): ?array
    {
        $offset = ($page - 1) * $limit;

        $stmt = $this->db->prepare("
            SELECT * FROM general_notifications
            WHERE notification_receiver = ? 
            AND notification_status = 'Unread' 
            ORDER BY notification_date DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Fetch notifications by id (with optional limit & offset)
     */
    public function getAllById(?int $userId = null, int $limit = 20, int $page = 1): ?array
    {
        $offset = ($page - 1) * $limit;

        $stmt = $this->db->prepare("
            SELECT * FROM general_notifications
            WHERE notification_receiver = ? 
            ORDER BY notification_date DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Mark a notification as read
    */
    public function markAsRead(int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE general_notifications SET notification_status = 'Read' WHERE notification_receiver = ?");
        return $stmt->execute([$userId]);
    }

    /**
     * Fetch all key dashboard stats in one call.
    */
    public function getVendorNotificationStats(int $userId): ?array
    {
        return [
            'all'    => $this->countAllById($userId),
            'unread' => $this->countUnreadById($userId),
        ];
    }

    /**
     * Fetch all key dashboard stats in one call.
    */
    public function getAdminNotificationStats(int $userId): ?array
    {
        return [
            'all'    => $this->countAllById($userId),
            'unread' => $this->countUnreadById($userId),
        ];
    }
}
