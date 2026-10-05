<?php

declare(strict_types=1);

namespace App\Models;

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

        return $this->table()
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

        return $this->table('user_socials')
            ->insert([
                'facebook'  => 'N/A',
                'instagram' => 'N/A',
                'tiktok'    => 'N/A',
                'twitter'   => 'N/A',
                'user_id'   => $userId
            ]);
    }

    public function createBillingDetails(
        string $address, 
        string $city, 
        string $code, 
        int $userId
    ): bool {

        return $this->table('billing_details')
            ->insert([
                'delivery_address' => $address,
                'city'             => $city,
                'postcode'         => $code,
                'user_id'          => $userId
            ]);
    }

    public function uploadID(
        string $file, 
        int $userId
    ): bool {

        return $this->table('user_documents')
            ->insert([
                'identity_file' => $file,
                'user_id'       => $userId
            ]);
    }
    
    public function getID(
        int $userId
    ): ?string {

        $result = $this->table('user_documents')
            ->select(['identity_file'])
            ->where('user_id', '=', $userId)
            ->first();

        return $result ? $result['identity_file'] : null;
    }

    public function findByEmail(
        string $email
    ): ?array {

        return $this->table()
            ->where('email', '=', $email)
            ->first();
    }

    public function findById(
        int $userId
    ): ?array {

        return $this->table()
            ->where('user_id', '=', $userId)
            ->first();
    }

    public function updatePassword(
        string $email, 
        string $password
    ): bool {

        return $this->table()
            ->where('email', '=', $email)
            ->update(['user_password' => $password]);
    }

    public function updateProfile(
        string $avatar, 
        int $userId
    ): bool {

        return $this->table()
            ->where('user_id', '=', $userId)
            ->update(['avatar' => $avatar]);
    }

    public function updateDetails(
        string $firstname, 
        string $lastname, 
        string $contact, 
        int $userId
    ): bool {

        return $this->table()
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

        return $this->table('user_socials')
            ->where('user_id', '=', $userId)
            ->update([
                'facebook'  => $facebook,
                'instagram' => $instagram,
                'tiktok'    => $tiktok,
                'twitter'   => $twitter
            ]);
    }

    public function allByRole(
        string $role
    ): ?array {

        return $this->table()
            ->where('user_role', '=', $role)
            ->oldest('firstname')
            ->get();
    }

    public function getProfile(
        int $userId
    ): ?string {

        $result = $this->table()
            ->select(['avatar'])
            ->where('user_id', '=', $userId)
            ->first();

        return $result ? $result['avatar'] : null;
    }

    public function getByRole(
        ?string $role = null, 
        int $page = 1, 
        int $limit = 20
    ): ?array {

        $users = $this->table()
            ->when(
                $role
                && !is_null($role),

                fn($query) =>
                    $query->where('user_role', '=', $role)
            )
            ->oldest('user_id')
            ->paginate($page, $limit)
            ->get();

        $total = $this->table()
            ->where('user_role', '=', $role)
            ->count();

        return $this->format($users, $total, $page, $limit);
    }

    public function updateStatus(
        string $status, 
        int $userId
    ): bool {

        return $this->table()
            ->where('user_id', '=', $userId)
            ->update(['user_status' => $status]);
    }

    public function getBillingDetails(
        int $userId
    ): ?array {

        return $this->table('billing_details')
            ->where('user_id', '=', $userId)
            ->first();
    }

    public function updateBillingDetails(
        string $address, 
        string $city, 
        string $code, 
        int $userId
    ): bool {

        return $this->table('billing_details')
            ->where('user_id', '=', $userId)
            ->update([
                'delivery_address' => $address,
                'city'             => $city,
                'postcode'         => $code,
            ]);
    }

    public function getSocials(
        int $userId
    ): ?array {

        return $this->table('user_socials')
            ->where('user_id', '=', $userId)
            ->first();
    }

    public function countAllRoles(): array
    {
        // Define All Possible Roles
        $roles = ["Admin", "Vendor", "User"];
        
        $results = $this->table()
            ->select([
                'user_role',
                'COUNT(*) AS total',
            ])
            ->groupBy([
                'user_role',
            ])
            ->get();

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

        return $this->table()
            ->where('user_id', '=', $userId)
            ->delete();
    }

    // =========================================
    //  PRIVATE HELPER METHODS
    // =========================================
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
}
