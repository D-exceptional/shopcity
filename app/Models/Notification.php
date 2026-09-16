<?php

declare(strict_types=1);

namespace App\Models;

class Notification extends Model
{
    protected string $table = 'general_notifications';

    public function create(
        string $details, 
        string $type, 
        int $receiver
    ): bool {

        return $this->query()
            ->insert([
                'notification_details'  => $details,
                'notification_type'     => $type,
                'notification_receiver' => $receiver,
            ]);
    }

    public function countAll(): int
    {
        return $this->query()
            ->count();
    }

    public function countAllById(
        int $userId
    ): int {

        return $this->query()
            ->where('notification_receiver', '=', $userId)
            ->count();
    }

    public function countUnreadById(
        int $userId
    ): int {

        return $this->query()
            ->where('notification_receiver', '=', $userId)
            ->where('notification_status', '=', 'Unread')
            ->count();
    }

    public function countUnreadByType(
        int $userId
    ): ?array {

        $fetchQuery = "
            SELECT 
                notification_type, 
                COUNT(*) AS count, 
                MAX(notification_date) AS last_date
            FROM {$this->table}
            WHERE 
                notification_receiver = ?
                AND notification_status = 'Unread'
            GROUP BY notification_type
        ";

        return $this->queryAll($fetchQuery, [$userId]);
    }

    public function getUnreadById(
        ?int $userId = null, 
        int $page = 1,
        int $limit = 20
    ): ?array {

        return $this->query()
            ->where('notification_receiver', '=', $userId)
            ->where('notification_status', '=', 'Unread')
            ->orderBy('notification_date', 'DESC')
            ->paginate($page, $limit)
            ->get();
    }

    public function getAllById(
        ?int $userId = null, 
        int $page = 1,
        int $limit = 20
    ): ?array {

        return $this->query()
            ->where('notification_receiver', '=', $userId)
            ->orderBy('notification_date', 'DESC')
            ->paginate($page, $limit)
            ->get();
    }

    public function markAsRead(
        int $userId
    ): bool {

        return $this->query()
            ->where('notification_receiver', '=', $userId)
            ->update(['notification_status' => 'Read']);
    }

    public function getVendorNotificationStats(
        int $userId
    ): array {

        return [
            'all'    => $this->countAllById($userId),
            'unread' => $this->countUnreadById($userId),
        ];
    }

    public function getAdminNotificationStats(
        int $userId
    ): array {

        return [
            'all'    => $this->countAllById($userId),
            'unread' => $this->countUnreadById($userId),
        ];
    }
}
