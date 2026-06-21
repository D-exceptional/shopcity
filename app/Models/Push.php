<?php
namespace App\Models;

class Push extends Database
{
    /**
     * Get Token IDs based on target type
     */
    public function getTokenIds(string $targetType = 'All', ?int $targetId = null): array
    {
        $targetType = ucwords($targetType);

        switch ($targetType) {

            case 'All':
                $stmt = $this->db->prepare(
                    "SELECT token FROM push_tokens WHERE is_active = 1"
                );
                $stmt->execute();
                break;

            case 'Admin':
            case 'Customer':
            case 'Vendor':
                $stmt = $this->db->prepare(
                    "SELECT token FROM push_tokens 
                    WHERE user_type = ? AND is_active = 1"
                );
                $stmt->execute([$targetType]);
                break;

            case 'Single Admin':
            case 'Single Customer':
            case 'Single Vendor':
                if ($targetId === null) {
                    return [];
                }

                $userType = str_replace('Single ', '', $targetType);

                $stmt = $this->db->prepare(
                    "SELECT token FROM push_tokens 
                    WHERE user_type = ? 
                    AND user_id = ? 
                    AND is_active = 1"
                );
                $stmt->execute([$userType, $targetId]);
                break;

            default:
                return [];
        }

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Save or re-activate a token.
     * Called on subscribe & auto-sync.
     */
    public function saveToken(string $token, string $deviceId, int $userId, string $userType): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO push_tokens (token, device_id, user_id, user_type, is_active, last_seen)
            VALUES (?, ?, ?, ?, 1, NOW())
            ON DUPLICATE KEY UPDATE
                token      = VALUES(token),
                is_active  = 1,
                last_seen  = NOW()
        ");

        return $stmt->execute([$token, $deviceId, $userId, $userType]);
    }

    /**
     * Mark token as inactive (unsubscribe).
     * Optionally restrict by deviceId
     */
    public function deactivateToken(string $token, ?string $deviceId = null): bool
    {
        $sql = "
            UPDATE push_tokens
            SET is_active = 0,
                last_seen = NOW()
            WHERE token = ?
        ";

        $params = [$token];

        if ($deviceId !== null) {
            $sql .= " AND device_id = ?";
            $params[] = $deviceId;
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Permanently delete dead tokens (cleanup).
     */
    public function deleteToken(string $token): void
    {
        $stmt = $this->db->prepare("
            DELETE FROM push_tokens WHERE token = ?
        ");

        $stmt->execute([$token]);
    }
}
