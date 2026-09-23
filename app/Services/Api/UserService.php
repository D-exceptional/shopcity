<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Core\Result;
use App\Contracts\SessionInterface;
use App\Events\User\UserRegistered;
use App\Events\User\OtpRequested;
use App\Events\User\ProfileUpdated;
use App\Events\User\UserStatusUpdated;
use App\Events\User\ContactMessageReceived;
use App\Events\EventDispatcher;
use App\Support\TextManager;
use App\Database\Database;
use App\Models\User;
use App\Models\Wallet;

class UserService
{
    private string $baseUrl;

    public function __construct(
        protected Result $result, 
        protected SessionInterface $sessionManager,
        protected EventDispatcher $eventDispatcher,
        protected TextManager $textManager,  
        protected Database $dbConn,
        protected User $userModel, 
        protected Wallet $walletModel, 
    ) {
        $this->baseUrl = config('app.url'); 
    }

    public function register(
        string $firstname,
        string $lastname,
        string $email,
        string $contact,
        string $country,
        string $password,
        string $role,
        string $code,
        string $abbr,
        string $currency,
        string $state,
        string $creator,
        ?string $file,
    ): Result {

        // Define Roles Configurations
        $rolesConfig = [
            'accepted' => ['Affiliate', 'Customer', 'Vendor', 'Worker'],
            'active'   => ['Affiliate', 'Customer', 'Worker'],
            'setup'    => ['Customer', 'Vendor'],
        ];

        // 1. Role-based Verification
        if (!in_array($role, $rolesConfig['accepted'])) {
            return $this->result->error('Role not supported', 409);
        }

        // 2. Email Uniqueness
        $userCheck = $this->userModel->findByEmail($email);
        if (!is_null($userCheck)) {
            return $this->result->error('Email already registered', 409);
        }

        // 3. Normalize Inputs
        $firstname = $this->textManager->formatUserName($firstname);
        $lastname  = $this->textManager->formatUserName($lastname);

        // Format Name And Status
        $fullName = $firstname . ' ' . $lastname;
        $status   = in_array($role, $rolesConfig['active'], true) ? 'Active' : 'Pending';
        
        // Handle Contact: Strip Leading 0 Only If It Exists
        $contact = $code . ltrim($contact, '0');

        // Hash Password (Argon2id Preferred If Supported)
        $password = password_hash($password, PASSWORD_BCRYPT ?? PASSWORD_ARGON2ID);

        // Prepare Mail Data
        $isActiveRole = in_array($role, $rolesConfig['active'], true);
        $userSubject  = $isActiveRole ? 'Registration Successful' : 'Registration Under Review';

        // 4. Begin Transaction (So DB + File Upload Are Atomic)
        try {

            $userId = $this->dbConn->transaction(function () use (
                $firstname,
                $lastname,
                $email,
                $contact,
                $country,
                $state,
                $password,
                $role,
                $currency,
                $file,
                $rolesConfig,
                $status
            ) {

                $userId = $this->userModel->createAccount(
                    'None',
                    $firstname,
                    $lastname,
                    $email,
                    $contact,
                    $country,
                    $state,
                    $password,
                    $role,
                    $status
                );

                if (!$userId) {
                    throw new \Exception(
                        'User creation failed'
                    );
                }

                // Handle Account Setups
                if (in_array($role, $rolesConfig['setup'], true)) {

                    // Handle Wallet Creation
                    $this->walletModel->createWallet(
                        $role,
                        0,
                        $userId
                    );

                    // Handle Billing Details
                    if ($role === 'Customer') {

                        $this->userModel->createBillingDetails(
                            'None',
                            'None',
                            'None',
                            $userId
                        );
                    }

                    // Handle Vendor Setup
                    if ($role === 'Vendor') {

                        $this->userModel->createSocials(
                            $userId
                        );

                        $this->walletModel->createDetails(
                            0,
                            'None',
                            'None',
                            $currency,
                            $userId
                        );

                        if ($file !== null) {

                            $this->userModel->uploadID(
                                $file,
                                $userId
                            );
                        }
                    }
                }

                return $userId;
            });

        } catch (\Throwable $e) {

            return $this->result->error(
                'Registration failed: ' . $e->getMessage(),
                500
            );
        }

        $this->eventDispatcher->dispatch(
            new UserRegistered(
                name: $fullName,
                email: $email,
                role: strtolower($role),
                creator: strtolower($creator),
                subject: $userSubject,
            )
        );

        return $this->result->success('Registration successful');
    }

    public function login(
        string $email, 
        string $password
    ): Result {

        // Check User Availability
        $user = $this->userModel->findByEmail($email);
        if ($user === null) {
            return $this->result->error('User not found', 404);
        }
        
        // Check User Status
        $status = $user['user_status'];
        if (in_array($status, ['Deactivated', 'Pending'])) {
            return $this->result->error('Cannot login at this time', 403);
        }

        // Verify User Password
        if (!password_verify($password, $user['user_password'])) {
            return $this->result->error('Invalid credentials', 401);
        }

        // Build User Session Data
        $sessionData = [
            'id'    => $user['user_id'],
            'name'  => $user['firstname'] . ' ' . $user['lastname'],
            'email' => $user['email'],
            'role'  => strtolower($user['user_role']),
            'state' => $user['user_state'],
        ];

        // Generate JWT Token For API Requests
       $jwtData = $this->sessionManager->jwtLogin($sessionData);

        // Get Dashboard Path
        $dashboardPath = $this->getPath($sessionData['role'], 'login');

        return $this->result->success(
            'Login successful', [
                'dashboard'  => $dashboardPath, 
                'connection' => $sessionData['role'], 
                'user'       => $sessionData,
                'token'      => $jwtData['token'], // JWT Token For API Requests
            ]
        );
    }

    public function sendOtp(
        string $email
    ): Result {

        $user = $this->userModel->findByEmail($email);
        if ($user === null) {
            return $this->result->error('User not found', 404);
        }

        $otpCode = rand(100000, 999999);

        $otpData = [
            'code'       => $otpCode,
            'email'      => $email,
            'expires_at' => time() + 300
        ];

        // Store OTP Session Data
        $this->sessionManager->store('otp', $otpData);

        $this->eventDispatcher->dispatch(
            new OtpRequested(
                name: $user['fullname'],
                email: $email,
                otp: $otp,
            )
        );

        return $this->result->success('OTP sent to your email');
    }

    public function reset(
        string $email, 
        string $password, 
        int $otp
    ): Result {

        $sessionOtp = $this->sessionManager->retrieve('otp');

        if (!$sessionOtp || time() > $sessionOtp['expires_at']) {
            return $this->result->error('OTP expired or not set', 400);
        }

        if ($otp != $sessionOtp['code']) {
            return $this->result->error('Invalid OTP', 401);
        }

        $email          = $sessionOtp['email'];
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $success = $this->userModel->updatePassword($email, $hashedPassword);
        if ($success === false) {
            return $this->result->error('Failed to reset password', 500);
        } 

        $this->sessionManager->destroy('otp');

        return $this->result->success('Password reset successful');
    }

    public function update(
        string $firstname, 
        string $lastname, 
        int $contact, 
        int $userId
    ): Result {

        $updated = $this->userModel->updateDetails($firstname, $lastname, $contact, $userId);
        if ($updated === false) {
            return $this->result->error('Update failed', 500);
        } 

        return $this->result->success('Details updated successfully');
    }

    public function social(
        string $facebook, 
        string $instagram, 
        string $tiktok, 
        string $twitter, 
        int $userId
    ): Result {

        $updated = $this->userModel->updateSocials($facebook, $instagram, $tiktok, $twitter, $userId);
        if ($updated === false) {
            return $this->result->error('Update failed', 500);
        } 

        return $this->result->success('Socials updated successfully');
    }

    public function profile(
        string $avatar, 
        int $userId
    ): Result {

        $profile = $this->userModel->getProfile($userId);
        if ($profile === null) {
            return $this->result->error('Profile not found', 404);
        }

        // Update DB with new URL
        $updated = $this->userModel->updateProfile($avatar, $userId);
        if ($updated === false) {
            return $this->result->error('Failed to update profile', 500);
        }

        if ($profile !== 'None') {

            $this->eventDispatcher->dispatch(
                new ProfileUpdated(
                    oldAvatar: $profile,
                    newAvatar: $avatar,
                )
            );

        }

        return $this->result->success('Profile updated successfully');
    }

    public function password(
        string $password, 
        int $userId
    ): Result
    {
        $user = $this->userModel->findById($userId);
        if ($user === null) {
            return $this->result->error('Details not found', 404);
        }

        // Get Database Password
        $email       = $user['email'];
        $oldPassword = $user['user_password'];

        // Check Both Passwords Match
        if (password_hash($password, PASSWORD_BCRYPT) === $oldPassword) {
            return $this->result->error('New password cannot be the same as the old password', 400);
        }

        // Hash New Password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $success = $this->userModel->updatePassword($email, $hashedPassword);
        if ($success === false) {
            return $this->result->error('Failed to change password', 500);
        } 

        return $this->result->success('Password changed successfully');
    }

    public function status(
        int $userId, 
        string $status
    ): Result {

        $success = $this->userModel->updateStatus($status, $userId);
        if ($success === false) {
            return $this->result->error('Failed to update status', 500);
        }

        $this->eventDispatcher->dispatch(
            new UserStatusUpdated(
                userId: $userId,
                status: $status,
            )
        );

        return $this->result->success('Account status updated successfully');
    }

    public function count(): Result
    { 
        $counts = $this->userModel->countAllRoles();
        if (count($counts) === 0) {
            return $this->result->error('Counts failed', 500);
        } 
        
        return $this->result->success('Counts fetched successfully', ['counts' => $counts]);
    }

    public function billing(
        string $address, 
        string $city, 
        int $code, 
        int $userId
    ): Result { 

        $isExisting = $this->userModel->getBillingDetails($userId);
        if ($isExisting === false) {

            $created = $this->userModel->createBillingDetails($address, $city, $code, $userId);
            if ($created === false) {
                return $this->result->error('Failed to create details', 500);
            } 
           
            return $this->result->success('Details created successfully');
        }
        else{

            $updated = $this->userModel->updateBillingDetails($address, $city, $code, $userId);
            if ($updated === false) {
                return $this->result->error('Failed to update details', 500);
            } 
            
            return $this->result->success('Details updated successfully');
        }
    }

    public function logout(
        string $role
    ): Result {

        $loginPath = $this->getPath($role, 'logout');

        // Destroy Session
        $this->sessionManager->terminate('user');

        return $this->result->success('Logout successful', ['dashboard' => $loginPath]);
    }

    public function contact(
        string $name, 
        string $email, 
        int $contact, 
        string $country, 
        string $subject, 
        string $message, 
        string $code
    ): Result {

        // Handle Contact: Strip Leading 0 Only If It Exists
        $contact = $code . ltrim($contact, '0');

        $this->eventDispatcher->dispatch(
            new ContactMessageReceived(
                name: $name,
                email: $email,
                contact: $contact,
                country: $country,
                subject: $subject,
                message: $message,
            )
        );

        return $this->result->success('Message sent successfully');
    }

    public function subscribe(
        string $token, 
        string $deviceId, 
        int $userId, 
        string $userRole
    ): Result {

        $subscribed = $this->pushModel->saveToken($token, $deviceId, $userId, $userRole);
        if (!$subscribed) {
            return $this->result->error('Subscription sync failed');
        }

        return $this->result->success('Subscription sync successful');
    }

    public function unsubscribe(
        string $token, 
        string $deviceId
    ): Result {

        $deactivated = $this->pushModel->deactivateToken($token, $deviceId);
        if ($deactivated === false) {
            return $this->result->error('Failed to unsubscribe from notifications', 500);
        }

        return $this->result->success('Notification subscription unsubscribed successfully');
    }

    public function fetch(
        string $role, 
        int $page
    ): Result { 

        $users = $this->userModel->getByRole($role, $page);
        if (count($users['users']) === 0) {
            return $this->result->error('Failed to fetch users', 500);
        }
        
        return $this->result->success('Users fetched successfully', $users);
    }

    public function delete(
        int $userId
    ): Result {

        $deleted = $this->userModel->deleteUser($userId);
        if ($deleted === false) {
            return $this->result->error('Failed to delete user', 500);
        } 
        
        return $this->result->success('User deleted successfully');
    }

    public function doc(
        int $userId
    ): Result { 

        $file = $this->userModel->getID($userId);
        if (!$file || in_array($file, [null, 'None'])) {
            return $this->result->error('Failed to fetch user document', 500);
        } 
        
        return $this->result->success('Document fetched successfully', ['file' => $file]);
    }

    private function getPath(
        string $role, 
        string $action
    ): string {

        $userRole = strtolower($role);

        $adminLoginLink  = '/auth/admin/login';
        $publicLoginLink = '/auth/user/login';

        $pathConfig = [
            'admin'     => $action === 'login' ? '/admin/dashboard' : $adminLoginLink,
            'vendor'    => $action === 'login' ? '/vendor/dashboard' : $publicLoginLink,
            'customer'  => $action === 'login' ? '/' : $publicLoginLink,
            'affiliate' => $action === 'login' ? '/affiliate/dashboard' : $publicLoginLink,
        ];

        // return $baseUrl . $pathConfig[$userRole];
        return $pathConfig[$userRole];
    }
}
