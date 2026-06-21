<?php

namespace App\Models;
use PDO;

class User extends Database
{
    /** @return int|false */
    public function createAccount(string $avatar, string $firstname, string $lastname, string $email, string $contact, string $country, string $state, string $password, string $role, string $status)
    {
        $stmt = $this->db->prepare("INSERT INTO users (avatar, firstname, lastname, email, contact, country, user_state, user_password, user_role, user_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ok = $stmt->execute([$avatar, $firstname, $lastname, $email, $contact, $country, $state, $password, $role, $status]);
        return $ok ? (int) $this->db->lastInsertId() : false;
    }

    public function createSocials(int $userId): bool
    {
        $stmt = $this->db->prepare("INSERT INTO user_socials (facebook, instagram, tiktok, twitter, user_id) VALUES ('None', 'None', 'None', 'None', ?)");
        return $stmt->execute([$userId]);
    }

    public function createBillingDetails(string $address, string $city, string $code, int $userId)
    {
        $stmt = $this->db->prepare("INSERT INTO billing_details (delivery_address, city, postcode, user_id) VALUES (?, ?, ?, ?)");
        $ok = $stmt->execute([$address, $city, $code, $userId]);
        return $ok ? (int) $this->db->lastInsertId() : false;
    }

    public function uploadID(string $file, int $userId): bool
    {
        $stmt = $this->db->prepare("INSERT INTO user_documents (identity_file, user_id) VALUES (?, ?)");
        return $stmt->execute([$file, $userId]);
    }
    
    public function getID(int $userId): string
    {
        $stmt = $this->db->prepare("SELECT identity_file FROM user_documents WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

     /** @return array|false */
    public function findByEmail(string $email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findById(int $userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function updatePassword(string $email, string $password): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET user_password = ? WHERE email = ?");
        return $stmt->execute([$password, $email]);
    }

    public function updateProfile(string $profile, int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET avatar = ? WHERE user_id = ?");
        return $stmt->execute([$profile, $userId]);
    }

    public function updateDetails(string $firstname, string $lastname, string $contact, int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET firstname = ?, lastname = ?, contact = ? WHERE user_id = ?");
        return $stmt->execute([$firstname, $lastname, $contact, $userId]);
    }

    public function updateSocials(string $facebook, string $instagram, string $tiktok, string $twitter, int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE user_socials SET facebook = ?, instagram = ?, tiktok = ?, twitter = ? WHERE user_id = ?");
        return $stmt->execute([$facebook, $instagram, $tiktok, $twitter, $userId]);
    }

    public function allByRole(string $role): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_role = ? ORDER BY firstname ASC");
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }

    public function getProfile(int $userId)
    {
        $stmt = $this->db->prepare("SELECT avatar FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

     /**
     * Generic fetch with enrichment & pagination
    */
    private function fetchUsers(?string $sql = null, array $params = [], int $page = 1, int $perPage = 20): ?array
    {
        $offset = ($page - 1) * $perPage;

        $sql .= " LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);

        $i = 1;
        foreach ($params as $param) {
            $type = is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($i++, $param, $type);
        }

        $stmt->bindValue($i++, (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue($i, (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function countUsers(string $sql, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    private function paginate(array $data, int $total, int $page, int $perPage): ?array
    {
        return [
            'users'       => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => ceil($total / $perPage),
        ];
    }

    public function getByRole(?string $role = null, int $page = 1, int $perPage = 20): ?array
    {
        $sql = "SELECT * FROM users WHERE user_role = ? ORDER BY user_id ASC";
        $users = $this->fetchUsers($sql, [$role], $page, $perPage);
        $total = $this->countUsers("SELECT COUNT(*) FROM users WHERE user_role = ?", [$role]);
        return $this->paginate($users, $total, $page, $perPage);
    }

    public function updateStatus(string $status, int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET user_status = ? WHERE user_id = ?");
        return $stmt->execute([$status, $userId]);
    }

    public function getBillingDetails(int $userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM billing_details WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function updateBillingDetails(string $address, string $city, string $code, int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE billing_details SET delivery_address = ?, city = ?, postcode = ? WHERE user_id = ?");
        return $stmt->execute([$address, $city, $code, $userId]);
    }

    public function getSocials(int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM user_socials WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function countAllRoles(): ?array
    {
        // Define all possible roles
        $roles = ["Admin", "Vendor", "User"];

        // Query counts from DB
        $stmt = $this->db->prepare("
            SELECT user_role, COUNT(*) AS total 
            FROM users 
            GROUP BY user_role
        ");
        $stmt->execute();
        
        $rows = $stmt->fetchAll();

        // Initialize all roles with zero
        $counts = array_fill_keys($roles, 0);

        // Overwrite with actual counts from DB
        foreach ($rows as $row) {
            $counts[$row['user_role']] = (int) $row['total'];
        }

        return $counts;
    }

    public function deleteUser(int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}
