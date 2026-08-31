<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Result;
use App\Contracts\SessionInterface;
use App\Support\TextManager;
use App\Mail\MailManager;
use App\Notification\PushManager;
use App\Media\CloudinaryManager;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Notification;
use App\Models\Mail;
use App\Models\Push;

class UserService
{
    private Connection $dbConn;
    private string $baseUrl;

    public function __construct(
        protected Result $result, 
        protected SessionInterface $sessionManager,
        protected TextManager $textManager, 
        protected MailManager $mailManager,
        protected PushManager $pushManager,
        protected CloudinaryManager $cloudinaryManager, 
        protected User $userModel, 
        protected Wallet $walletModel, 
        protected Notification $notificationModel, 
        protected Mail $mailModel, 
        protected Push $pushModel
    ) {
        $this->dbConn  = $this->userModel->db;
        $this->baseUrl = config('app.base_path', '/projects/showcase/shopcity'); 
    }

    public function register(
        string $firstname,
        string $lastname,
        string $email,
        int $contact,
        string $country,
        string $password,
        string $role,
        string $code,
        string $abbr,
        string $currency,
        string $state,
        string $creator,
        ?string $file
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
        $hasRegistered = $this->userModel->findByEmail($email);
        if ($hasRegistered === true) {
            return $this->result->error('Email already registered', 409);
        }

        // 3. Normalize Inputs
        $firstname = $this->textManager->formatUserName($firstname);
        $lastname  = $this->textManager->formatUserName($lastname);
        
        // Handle Contact: Strip Leading 0 Only If It Exists
        $cleanContact = ltrim($contact, '0');
        $contact      = $code . $cleanContact;

        // Hash Password (Argon2id Preferred If Supported)
        $password = password_hash($password, PASSWORD_BCRYPT ?? PASSWORD_ARGON2ID);

        // Format Name And Status
        $fullName = $firstname . ' ' . $lastname;
        $status   = in_array($role, $rolesConfig['active'], true) ? 'Active' : 'Pending';

        // 4. Begin Transaction (So DB + File Upload Are Atomic)
        $this->dbConn->beginTransaction();

        try {
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
                throw new \Exception("User creation failed");
            }

            // Handle Account Setups
            if (in_array($role, $rolesConfig['setup'])) {

                // Handle Wallet Creation
                $this->walletModel->createWallet($role, 0, $userId);

                // Handle Billing Details
                if ($role === 'Customer') {
                    $this->userModel->createBillingDetails('None', 'None', 'None', $userId);
                }

                // Handle Socials + Bank + File Upload
                if ($role === 'Vendor') {
                    $this->userModel->createSocials($userId);
                    $this->walletModel->createDetails(0, 'None', 'None', $currency, $userId);

                    if (isset($file) && !is_null($file)) {
                        $this->userModel->uploadID($file, $userId);
                    }
                }
            }

            $this->dbConn->commit();

        } catch (\Throwable $e) {

            $this->dbConn->rollBack();

            return $this->result->error('Registration failed: ' . $e->getMessage(), 500);
        }

        // 5. Role-based Messaging (DRY pproach)
        $defaultMessage = "
            Hi <b>{$fullName}</b>, 

            <br> We are currently reviewing your registration. 
            <br> We'll notify you as soon as there's any new developments.
            <br> Thank you for your patience.
        ";

        $roleMessages = [
            'Customer'  => $this->buildWelcomeMessage($fullName),
            'Affiliate' => $this->buildWelcomeMessage($fullName),
            'Worker'    => $this->buildWelcomeMessage($fullName),
            'Vendor'    => ($creator === 'Admin') 
            ? $this->buildWelcomeMessage($fullName) 
            : "
                Hi <b>{$fullName}</b>, 

                <br> Your registration is currently <b>undergoing review</b>. 
                <br> Our team is reviewing your details. Once approved, you'll be able to start selling. 
                <br> We'll notify you as soon as the status changes.
                <br> Thank you for your patience.
            ",
        ];

        // Prepare Mail Data
        $isActiveRole = in_array($role, $rolesConfig['active'], true);
        $userMessage  = $roleMessages[$role] ?? $defaultMessage;
        $userSubject  = $isActiveRole ? 'Registration Successful' : 'Registration Under Review';

        // Send User Email
        $this->mailManager->sendSimpleMail($userSubject, $email, $userMessage);

        /*
            Loop through the admins, 
            Notify them via email
            Notify them via push if available
        */

        // Build Admin Message
        $adminMessage = "
            Hello Admin, 

            <br> A new {$role}, <b>{$fullName}</b>, just registered on the platform!
            <br> Kindly review and take necessary actions. 
        ";

        // Process Admin Notifications
        $admins = $this->userModel->allByRole('Admin');
        foreach ($admins as $admin) {

            // Create In-App Admin Notification
            $notification = $this->notificationModel->create($adminMessage, 'New Registration', $admin['user_id']);
            if ($notification === false) {
                return $this->result->error('Failed to create notification for admin', 500);
            }

            // Send Admin Email
            $this->mailManager->sendSimpleMail('New Registration', $admin['email'], $adminMessage);

            // Send Admin Push Notification
            $adminPushMessage = $this->textManager->formatPushMessage($adminMessage);

            $this->pushManager->send('Single Admin', $admin['user_id'], 'New Registration', $adminPushMessage, [
                'url' => "{$this->baseUrl}/admin/",
                'type' => 'registration'
            ]);
        }

        return $this->result->success('Registration successful');
    }

    public function login(
        string $email, 
        string $password
    ): Result {

        // Check User Availability
        $user = $this->userModel->findByEmail($email);
        if ($user === false) {
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

        // Store Session Data
        $this->sessionManager->login($sessionData);

        // Get Dashboard Path
        $dashboardPath = $this->getPath($sessionData['role'], 'login');

        return $this->result->success(
            'Login successful', [
                'dashboard'  => $dashboardPath, 
                'connection' => $sessionData['role'], 
                'user'       => $sessionData
            ]
        );
    }

    public function sendOtp(
        string $email
    ): Result {

        $user = $this->userModel->findByEmail($email);
        if ($user === false) {
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

        // Build User Message
        $userMessage = "
            Hi, 

            <br> Your password reset OTP is <b>{$otpCode}</b> and it expires in 5 minutes.
            <br> Ensure you never share OTP code with anyone to prevent your account from being compromised.
        ";

        // Send User Email
        $this->mailManager->sendSimpleMail('Password Reset OTP', $email, $userMessage);

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

        // Later Use Cloudinary Events To Delete Old Profile
        if ($profile !== 'None') {
            $this->cloudinaryManager->delete($profile);
        }

        return $this->result->success('Profile updated successfully');
    }

    public function password(
        string $password, 
        int $userId
    ): Result
    {
        $user = $this->userModel->findById($userId);
        if ($user === false) {
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

        $userData  = $this->getBiodata($userId);
        $userName  = $userData['name'];
        $userEmail = $userData['email'];

        // Build Message Based On Status
        $statusMessages = [
            'Active' => "
                Hi <b>{$userName}</b>, 

                <br> Great news! 🎉 Your account has been <b>activated</b>. 
                <br> You can now log into your account and pick up from where you left off. 
                <br> Take care to adhere to the regulations in order to prevent sanctions of this nature.
                <br> We're excited to have you back!
            ",

            'Deactivated' => "
                Hi <b>{$userName}</b>, 

                <br> Your account has been <b>deactivated</b>. 
                <br> This may be due to policy violations, inactivity, or other issues. 
                <br> Please contact support at <b>support@mrsamase.com</b> or visit <b><a href='{$this->baseUrl}/contact'>Appeal Page</a></b> to resolve this and restore your account. 
                <br> We value your partnership and hope to have you back soon.
            ",
        ];

        // Fallback In Case Of Unknown Status
        $userMessage = $statusMessages[$status] ?? "
            Hi <b>{$userName}</b>, 

            <br> There has been an update to your account status. 
            <br> Please check account for more details.
        ";

        // Send User Email
        $this->mailManager->sendSimpleMail('Account Status Updated', $userEmail, $userMessage);

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
        $cleanContact = ltrim($contact, '0');
        $contact      = $code . $cleanContact;

        // Build Message
        $adminMessage = "
            A message was sent by <b>" . trim($name) . "</b> from  <b> " . trim($country) . " </b>
            <br>
            You can reach out to them via their mobile: <b>" . trim($contact) . "</b> or email address: <b>" . trim($email) . "</b>
        ";

        // Define Dates & Time
        $fullDate    = date('Y-m-d H:i:s');
        $shortDate   = date('Y-m-d');
        $currentTime = date('H:i');

        // Process Admin Notifications
        $admins = $this->userModel->allByRole('Admin');
        foreach ($admins as $admin) {

            // Create In-App Admin Notification
            $notification = $this->notificationModel->create($adminMessage, 'New Message', $admin['user_id']);
            if ($notification === false) {
                return $this->result->error('Failed to create notification for admin', 500);
            }

            // Create In-App Admin Mail Notification
            $mail = $this->mailModel->createMail(
                'Text', 
                $subject, 
                $name, 
                $admin['email'], 
                $shortDate, 
                $currentTime, 
                $message, 
                'None', 
                'None'
            );

            if ($mail === false) {
                return $this->result->error("Failed to create mail record for admin {$admin['email']}", 500);
            }

            // Send Admin Email
            $this->mailManager->sendSimpleMil($subject, $admin['email'], $adminMessage);
        }

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

    private function getBiodata(
        int $userId
    ): array {

        $userData = $this->userModel->findById($userId);

        return [
            'name'  => $userData['firstname'] . ' ' . $userData['lastname'],
            'email' => $userData['email'],
            'role'  => $userData['user_role']
        ];
    }

    private function buildWelcomeMessage(
        string $name
    ): string {

        return "
            Hi <b>{$name}</b>,

            <br> Great news! 🎉 Your registration is successful. 
            <br> Login to your account for maximum shopping experience curated just for you!
            <br> Thank you for choosing to shop with us.
            <br> We're excited to have you on our platform!
        ";
    }

    private function getPath(
        string $role, 
        string $action
    ): string {

        $userRole = strtolower($role);

        $adminLoginLink  = $this->baseUrl . '/admin';
        $publicLoginLink = $this->baseUrl . '/login';

        $pathConfig = [
            'admin'     => $action === 'login' ? $this->baseUrl . '/admin/dashboard' : $adminLoginLink,
            'vendor'    => $action === 'login' ? $this->baseUrl . '/seller/' : $publicLoginLink,
            'customer'  => $action === 'login' ? $this->baseUrl : $publicLoginLink,
            'affiliate' => $action === 'login' ? $this->baseUrl . '/affiliate' : $publicLoginLink,
        ];

        return $pathConfig[$userRole];
    }
}
