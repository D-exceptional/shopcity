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

        return $this->table()
            ->insert([
                'notification_details'  => $details,
                'notification_type'     => $type,
                'notification_receiver' => $receiver,
            ]);
    }

    public function countAll(): int
    {
        return $this->table()
            ->count();
    }

    public function countAllById(
        int $userId
    ): int {

        return $this->table()
            ->where('notification_receiver', '=', $userId)
            ->count();
    }

    public function countUnreadById(
        int $userId
    ): int {

        return $this->table()
            ->where('notification_receiver', '=', $userId)
            ->where('notification_status', '=', 'Unread')
            ->count();
    }

    public function countUnreadByType(
        int $userId
    ): array {

        return $this->table()
            ->select([
                'notification_type',
                'COUNT(*) AS count',
                'MAX(notification_date) AS last_date',
            ])
            ->where('notification_receiver', '=', $userId)
            ->where('notification_status', '=', 'Unread')
            ->groupBy([
                'notification_type',
            ])
            ->get();
    }

    public function getUnreadById(
        ?int $userId = null, 
        int $page = 1,
        int $limit = 20
    ): ?array {

        return $this->table()
            ->where('notification_receiver', '=', $userId)
            ->where('notification_status', '=', 'Unread')
            ->latest('notification_date')
            ->paginate($page, $limit)
            ->get();
    }

    public function getAllById(
        ?int $userId = null, 
        int $page = 1,
        int $limit = 20
    ): ?array {

        return $this->table()
            ->where('notification_receiver', '=', $userId)
            ->latest('notification_date')
            ->paginate($page, $limit)
            ->get();
    }

    public function markAsRead(
        int $userId
    ): bool {

        return $this->table()
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
