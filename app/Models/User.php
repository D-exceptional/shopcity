<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class User extends Model
{
    protected string $table = 'users';

    public function createAccount(
        string $avatar, 
        string $firstname, 
        string $lastname, 
        string $email, 
        string $contact, 
        string $country, 
        string $state, 
        string $password, 
        string $role, 
        string $status
    ): ?int {

        return $this->query()
            ->insertGetId([
                'avatar'        => $avatar,
                'firstname'     => $firstname,
                'lastname'      => $lastname,
                'email'         => $email,
                'contact'       => $contact,
                'country'       => $country,
                'user_state'    => $state,
                'user_password' => $password,
                'user_role'     => $role,
                'user_status'   => $status
            ]);
    }

    public function createSocials(
        int $userId
    ): bool {

        $socialQuery = "
            INSERT INTO user_socials (facebook, instagram, tiktok, twitter, user_id) 
            VALUES ('None', 'None', 'None', 'None', ?)
        ";

        return $this->executeQuery(
            $socialQuery, 
            ['None', 'None', 'None', 'None', $userId]
        );
    }

    public function createBillingDetails(
        string $address, 
        string $city, 
        string $code, 
        int $userId
    ): bool {

        $billingsQuery = "
            INSERT INTO billing_details (delivery_address, city, postcode, user_id) 
            VALUES (?, ?, ?, ?)
        ";

        return $this->executeQuery(
            $billingsQuery, 
            [$address, $city, $code, $userId]
        );
    }

    public function uploadID(
        string $file, 
        int $userId
    ): bool {

        $documentQuery = "
           INSERT INTO user_documents (identity_file, user_id) 
           VALUES (?, ?)
        ";

        return $this->executeQuery($documentQuery, [$file, $userId]);
    }
    
    public function getID(
        int $userId
    ): ?string {

        $fetchQuery = "
            SELECT 
                identity_file 
            FROM user_documents 
            WHERE 
                user_id = ?
        ";

        $result = $this->queryOne($fetchQuery, [$userId]);

        return $result ? $result['identity_file'] : null;
    }

    public function findByEmail(
        string $email
    ): ?array {

        return $this->query()
            ->where('email', '=', $email)
            ->first();
    }

    public function findById(
        int $userId
    ): ?array {

        return $this->query()
            ->where('user_id', '=', $userId)
            ->first();
    }

    public function updatePassword(
        string $email, 
        string $password
    ): bool {

        return $this->query()
            ->where('email', '=', $email)
            ->update(['user_password' => $password]);
    }

    public function updateProfile(
        string $avatar, 
        int $userId
    ): bool {

        return $this->query()
            ->where('user_id', '=', $userId)
            ->update(['avatar' => $avatar]);
    }

    public function updateDetails(
        string $firstname, 
        string $lastname, 
        string $contact, 
        int $userId
    ): bool {

        return $this->query()
            ->where('user_id', '=', $userId)
            ->update([
                'firstname' => $firstname,
                'lastname'  => $lastname,
                'contact'   => $contact
            ]);
    }

    public function updateSocials(
        string $facebook, 
        string $instagram, 
        string $tiktok, 
        string $twitter, 
        int $userId
    ): bool {

        $updateQuery = "
           UPDATE user_socials 
           SET 
                facebook = ?, instagram = ?, tiktok = ?, twitter = ? 
           WHERE 
                user_id = ?
        ";

        return $this->executeQuery(
            $updateQuery, 
            [$facebook, $instagram, $tiktok, $twitter, $userId]
        );
    }

    public function allByRole(
        string $role
    ): ?array {

        return $this->query()
            ->where('user_role', '=', $role)
            ->orderBy('firstname', 'ASC')
            ->get();
    }

    public function getProfile(
        int $userId
    ): ?string {

        $result = $this->query()
            ->select(['avatar'])
            ->where('user_id', '=', $userId)
            ->first();

        return $result ? $result['avatar'] : null;
    }

    private function fetchUsers(
        ?string $sql = null, 
        array $params = [], 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        $offset = ($page - 1) * $limit;

        $sql .= " LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);

        $i = 1;
        foreach ($params as $param) {
            $type = is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($i++, $param, $type);
        }

        $stmt->bindValue($i++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($i, (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function countUsers(
        string $sql, 
        array $params = []
    ): int {

        return $this->fetchColumn($sql, $params);
    }

    private function format(
        array $data, 
        int $total, 
        int $page, 
        int $limit
    ): array {

        return [
            'users'       => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $limit,
            'total_pages' => ceil($total / $limit),
        ];
    }

    public function getByRole(
        ?string $role = null, 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        $users = $this->query()
            ->when(
                $role
                && !is_null($role),

                fn($query) =>
                    $query->where('user_role', '=', $role)
            )
            ->orderBy('user_id', 'ASC')
            ->paginate($page, $limit)
            ->get();

        $total = $this->query()
            ->where('user_role', '=', $role)
            ->count();

        return $this->format($users, $total, $page, $limit);
    }

    public function updateStatus(
        string $status, 
        int $userId
    ): bool {

        return $this->query()
            ->where('user_id', '=', $userId)
            ->update(['user_status' => $status]);
    }

    public function getBillingDetails(
        int $userId
    ): ?array {

        $fetchQuery = "
            SELECT 
                * 
            FROM billing_details 
            WHERE 
                user_id = ?
        ";

        return $this->queryOne($fetchQuery, [$userId]);
    }

    public function updateBillingDetails(
        string $address, 
        string $city, 
        string $code, 
        int $userId
    ): bool {

        $updateQuery = "
            UPDATE billing_details 
            SET 
                delivery_address = ?, city = ?, postcode = ? 
            WHERE 
                user_id = ?
        ";

        return $this->executeQuery(
            $updateQuery, 
            [$address, $city, $code, $userId]
        );
    }

    public function getSocials(
        int $userId
    ): ?array {

        $fetchQuery = "
            SELECT 
                * 
            FROM user_socials 
            WHERE 
                user_id = ?
        ";

        return $this->executeQuery($fetchQuery, [$userId]);
    }

    public function countAllRoles(): ?array
    {
        // Define All Possible Roles
        $roles = ["Admin", "Vendor", "User"];

        $fetchQuery = "
            SELECT 
                user_role, COUNT(*) AS total 
            FROM {$this->table}
            GROUP BY user_role
        ";
        
        $results = $this->queryAll($fetchQuery);

        // Initialize All Roles With Zero
        $countKeys = array_fill_keys($roles, 0);

        // Overwrite With Actual Counts From DB
        foreach ($results as $row) {
            $countKeys[$row['user_role']] = (int) $row['total'];
        }

        return $countKeys;
    }

    public function deleteUser(
        int $userId
    ): bool {

        return $this->query()
            ->where('user_id', '=', $userId)
            ->delete();
    }
}
