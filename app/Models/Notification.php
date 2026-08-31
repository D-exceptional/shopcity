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

    public function countUnreadGroupedById(
        int $userId
    ): int {

        return $this->query()
            ->where('notification_receiver', '=', $userId)
            ->where('notification_status', '=', 'Unread')
            ->groupBy('notification_type')
            ->count();
    }


    public function countByTypeWithLastDate(
        string $type, 
        int $userId
    ): array {

        // First query: count unread notifications by type 
        $count = $this->query()
            ->where('notification_type', '=', $type)
            ->where('notification_receiver', '=', $userId)
            ->where('notification_status', '=', 'Unread')
            ->count();

        // Second query: latest unseen incoming_mail date 
        $lastDate = $this->query()
            ->select(['notification_date']) // Change to `created_at` for a cleaner design
            ->where('notification_type', '=', $type)
            ->where('notification_receiver', '=', $userId)
            ->where('notification_status', '=', 'Unread')
            ->orderBy('notification_id', 'DESC')
            ->first();

        // Return both in one response
        return [
            'count'     => $count,
            'last_date' => $lastDate ?: null
        ];
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
