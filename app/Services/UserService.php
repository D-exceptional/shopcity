<?php

namespace App\Services;

use App\Helpers\SessionManager;
use App\Helpers\ResponseManager;
use App\Helpers\MailManager;
use App\Helpers\PushManager;
use App\Helpers\MediaManager;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Notification;
use App\Models\Mail;
use App\Models\Push;

require_once dirname(__DIR__, 2) . '/bootstrap.php'; // Auto load files

class UserService
{
    protected SessionManager $session;
    protected ResponseManager $response;
    protected MailManager $mailer;
    protected PushManager $push;
    protected MediaManager $mediaManager;
    protected User $userModel;
    protected Wallet $walletModel;
    protected Notification $notificationModel;
    protected Mail $mailModel;
    protected Push $pushModel;
    private $conn;
    private string $baseUrl;

    public function __construct(
        SessionManager $session,
        ResponseManager $response,
        MailManager $mailer,
        PushManager $push,
        MediaManager $mediaManager, 
        User $userModel, 
        Wallet $walletModel, 
        Notification $notificationModel, 
        Mail $mailModel, 
        Push $pushModel
    )
    {
        // Required helpers for this controller class
        $this->session       = $session;
        $this->response      = $response;
        $this->mailer        = $mailer;
        $this->push          = $push;
        $this->mediaManager  = $mediaManager;
        // Required models for this controller class
        $this->userModel         = $userModel;
        $this->walletModel       = $walletModel;
        $this->notificationModel = $notificationModel;
        $this->mailModel         = $mailModel;
        $this->pushModel         = $pushModel;
        // Required shared database connection
        $this->conn = $this->userModel->getDb();
        // Set base URL
        $this->baseUrl = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) ? 'http://localhost/projects/demos/shopcity' : '__remote__site__link__';
    }

    public function formatName(string $name): string 
    {
        // Allow only letters, spaces, apostrophes, hyphens (Kent O'neil, Mary-jane)
        $clean = preg_replace("/[^A-Za-z\s'-]/", '', $name);

        // Trim leading/trailing spaces and replace multiple spaces with one
        $clean = preg_replace('/\s+/', ' ', trim($clean));

        // Convert to proper case (e.g. "john doe" -> "John Doe")
        $formatted = ucwords(strtolower($clean));

        return $formatted;
    }

    /**
     * Converts HTML message to push-friendly plain text
     *
     * @param string $htmlMessage The HTML content
     * @return string Plain text suitable for push notifications
     */
    private function processMessage(string $htmlMessage): string
    {
        // Replace <br> (and variants) with \n
        $textWithLineBreaks = preg_replace('/<br\s*\/?>/i', "\n", $htmlMessage);

        // Remove all other HTML tags
        $plainText = strip_tags($textWithLineBreaks);

        // Trim extra whitespace at start/end and return
        return trim($plainText);
    }

    private function buildWelcomeMessage(string $fullName): string
    {
        return "
            Hi <b>{$fullName}</b>, 
            <br> Great news! 🎉 Your registration is successful. 
            <br> Login to your account for maximum shopping experience curated just for you!
            <br> Thank you for choosing to shop with us.
            <br> We're excited to have you on our platform!
        ";
    }

    public function register(array $payload)
    {
        // 1. Role-based verification
        $acceptedRoles = ['Affiliate', 'Customer', 'Vendor', 'Worker'];
        if (!in_array($payload['role'], $acceptedRoles)) {
            return $this->response->fail('Role not supported', 409);
        }

        // 2. Email uniqueness
        $hasRegistered = $this->userModel->findByEmail($payload['email']);
        if ($hasRegistered === true) {
            return $this->response->fail('Email already registered', 409);
        }

        // 3. Normalize inputs
        $payload['firstname'] = $this->formatName($payload['firstname']);
        $payload['lastname']  = $this->formatName($payload['lastname']);
        
        // Handle contact: ensure we strip leading 0 only if it exists
        $cleanContact = ltrim($payload['contact'], '0');
        $payload['contact'] = $payload['code'] . $cleanContact;

        // Hash password (Argon2id preferred if supported)
        $payload['password'] = password_hash($payload['password'], PASSWORD_BCRYPT ?? PASSWORD_ARGON2ID);

        // Format name and status
        $fullName = $payload['firstname'] . ' ' . $payload['lastname'];

        // Format status
        $activeRoles = ['Affiliate', 'Customer', 'Worker'];
        $payload['status'] = in_array($payload['role'], $activeRoles, true) ? 'Active' : 'Pending';

        // 4. Begin transaction (so DB + file upload are atomic)
        $this->conn->beginTransaction();
        try {
            $userId = $this->userModel->createAccount(
                $payload['avatar'],
                $payload['firstname'],
                $payload['lastname'],
                $payload['email'],
                $payload['contact'],
                $payload['country'],
                $payload['state'],
                $payload['password'],
                $payload['role'],
                $payload['status']
            );

            if (!$userId) {
                throw new \Exception("User creation failed");
            }

            // Handle wallet creation
            if (in_array($payload['role'], ['Customer', 'Vendor'])) {
                $this->walletModel->createWallet($payload['role'], 0, $userId);
            }

            // Handle billing details
            if ($payload['role'] === 'Customer') {
                $this->userModel->createBillingDetails('None', 'None', 'None', $userId);
            }

            // Handle socials + bank
            if ($payload['role'] === 'Vendor') {
                $this->userModel->createSocials($userId);
                $this->walletModel->createDetails(0, 'None', 'None', $payload['currency'], $userId);
            }

            // Handle vendor file upload + bank details creation
            /*
            if ($payload['role'] === 'Vendor' && isset($payload['file'])) {
                $this->userModel->uploadID($payload['file'], $userId);
            }
            */

            $this->conn->commit();
        } catch (\Throwable $e) {
            $this->conn->rollBack();
            return $this->response->fail('Registration failed: ' . $e->getMessage(), 500);
        }

        // 5. Role-based messaging (DRY approach)
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
            'Vendor'    => ($payload['creator'] === 'Admin') 
            ? $this->buildWelcomeMessage($fullName) 
            : "
                Hi <b>{$fullName}</b>, 
                <br> Your registration is currently <b>undergoing review</b>. 
                <br> Our team is reviewing your details. Once approved, you'll be able to start selling. 
                <br> We'll notify you as soon as the status changes.
                <br> Thank you for your patience.
            ",
        ];

        $message = $roleMessages[$payload['role']] ?? $defaultMessage;

        $mail = [
            'subject' => in_array($payload['role'], $activeRoles, true) 
                ? 'Registration Successful' 
                : 'Registration Under Review',
            'message' => $message
        ];

        // ✅ Mail sending could be queued for performance
        $this->mailer->send($mail['subject'], $payload['email'], $mail['message']);

        // Initialize admin mail message
        $adminMessage =  "
            Hello Admin, 
            <br> A new {$payload['role']}, <b>{$fullName}</b>, just registered on the platform!
            <br> Kindly review and take necessary actions. 
        ";
        $date = date('Y-m-d H:i:s');

        // Send admin mails
        $admins = $this->userModel->allByRole('Admin');
        foreach ($admins as $admin) {
            $this->mailer->send('New Registration', $admin['email'], $adminMessage);
            // Send push notification to admins
            $processedMessage = $this->processMessage($adminMessage);
            $this->push->send('Single Admin', $admin['user_id'], 'New Registration', $processedMessage, [
                'url' => "{$this->baseUrl}/admin/",
                'type' => 'registration'
            ]);
            
            $notification = $this->notificationModel->create($adminMessage, 'New Registration', $admin['user_id'], $date, 'Unread');
            if ($notification === false) {
                return $this->response->fail('Failed to create notification for admin', 500);
            }
        }

        return $this->response->success('Registration successful');
    }

    public function login(array $payload)
    {
        // Find user by email
        $user = $this->userModel->findByEmail($payload['email']);
        if ($user === false) {
            return $this->response->fail('User not found', 404);
        }
        
        // Check status
        $status = $user['user_status'];
        if (in_array($status, ['Deactivated', 'Pending'])) {
            return $this->response->fail('Cannot login at this time', 403);
        }

        // Verify password
        if (!password_verify($payload['password'], $user['user_password'])) {
            return $this->response->fail('Invalid credentials', 401);
        }

        // Build user session data
        $data = [
            'id'    => $user['user_id'],
            'name'  => $user['firstname'] . ' ' . $user['lastname'],
            'email' => $user['email'],
            'role'  => strtolower($user['user_role']),
            'state' => $user['user_state'],
        ];

        // Store in session
        $this->session->login($data);

        // Define full paths
        $path = [
            'admin'     => $this->baseUrl . '/admin/dashboard',
            'vendor'    => $this->baseUrl . '/seller/',
            'customer'  => $this->baseUrl,
            'affiliate' => $this->baseUrl . '/affiliate',
        ];

        // Success response
        return $this->response->success('Login successful', ['dashboard' => $path[$data['role']], 'connection' => $data['role'], 'user' => $data]);
    }

    public function sendOtp(string $email)
    {
        $user = $this->userModel->findByEmail($email);
        if ($user === false) {
            return $this->response->fail('User not found', 404);
        }

        $otp = rand(100000, 999999);
        $data = [
            'code'       => $otp,
            'email'      => $email,
            'expires_at' => time() + 300
        ];

        $this->session->store('otp', $data);

        $mail = [
            'subject' => 'Password Reset OTP',
            'message' => "Hi, <br> Your password reset OTP is <b>$otp</b> and it expires in 5 minutes",
        ];
        $this->mailer->send($mail['subject'], $email, $mail['message']);

        return $this->response->success('OTP sent to your email');
    }

    public function reset(array $payload)
    {
        $sessionOtp = $this->session->retrieve('otp');

        if (!$sessionOtp || time() > $sessionOtp['expires_at']) {
            return $this->response->fail('OTP expired or not set', 400);
        }

        if ($payload['otp'] != $sessionOtp['code']) {
            return $this->response->fail('Invalid OTP', 401);
        }

        $email = $sessionOtp['email'];
        $hashedPassword = password_hash($payload['password'], PASSWORD_BCRYPT);

        $success = $this->userModel->updatePassword($email, $hashedPassword);
        if ($success === false) {
            return $this->response->fail('Failed to reset password', 500);
        } 
        $this->session->destroy('otp');
        return $this->response->success('Password reset successful');
    }

    public function update(array $payload, int $userId)
    {
        $updated = $this->userModel->updateDetails($payload['firstname'], $payload['lastname'], $payload['contact'], $userId);
        if ($updated === false) {
            return $this->response->fail('Update failed', 500);
        } 
        return $this->response->success('Details updated successfully');
    }

    public function social(array $payload, int $userId)
    {
        $updated = $this->userModel->updateSocials($payload['facebook'], $payload['instagram'], $payload['tiktok'], $payload['twitter'], $userId);
        if ($updated === false) {
            return $this->response->fail('Update failed', 500);
        } 
        return $this->response->success('Socials updated successfully');
    }

    /** Update profile image */
    public function profile(string $avatar, int $userId)
    {
        $profile = $this->userModel->getProfile($userId);
        if ($profile === null) {
            return $this->response->fail('Profile not found', 404);
        }

        // Delete old file from Cloudinary
        if ($profile !== 'None') {
            $result = $this->mediaManager->delete($profile);
            if (!isset($result['result']) || $result['result'] !== 'ok') {
                return $this->response->fail('Failed to delete file from Cloudinary', 500);
            }
        }

        // Update DB with new URL
        $updated = $this->userModel->updateProfile($avatar, $userId);
        if ($updated === false) {
            return $this->response->fail('Failed to update profile', 500);
        }

        return $this->response->success('Profile updated successfully');
    }

    /** Update profile image */
    public function password(array $payload, int $userId)
    {
        $details = $this->userModel->findById($userId);
        if ($details === false) {
            return $this->response->fail('Details not found', 404);
        }

        // Get database password
        $email = $details['email'];
        $dbPassword = $details['user_password'];

        // Extract request data
        $requestPassword = $payload['password'];
        $newPassword = $payload['repassword'];

        // Check password match
        if ($requestPassword !== $newPassword) {
            return $this->response->fail('Passwords do not match', 400);
        }

        // Check new password match
        if (password_hash($newPassword, PASSWORD_BCRYPT) === $dbPassword) {
            return $this->response->fail('New password cannot be the same as the old password', 400);
        }

        // Verify authenticity
        if (password_verify($requestPassword, $dbPassword)) {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            $success = $this->userModel->updatePassword($email, $hashedPassword);
            if ($success === false) {
                return $this->response->fail('Failed to change password', 500);
            } 
            return $this->response->success('Password changed successfully');
        } else {
           return $this->response->fail('Failed to verify password', 500);
        }
    }

    public function status(array $payload)
    {
        $status = $this->userModel->updateStatus($payload['status'], $payload['id']);
        if ($status === false) {
            return $this->response->fail('Failed to update status', 500);
        }

        $userDetails = $this->userModel->findById($payload['id']);
        $userName = $userDetails['firstname'] . ' ' . $userDetails['lastname'];
        $userEmail = $userDetails['email'];

        // Build message based on status
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

        // Fallback in case of unknown status
        $message = $statusMessages[$payload['status']] ?? "
            Hi <b>{$userName}</b>, 
            <br> There has been an update to your account status. 
            <br> Please check account for more details.
        ";

        $mail = [
            'subject' => 'Account Status Updated',
            'message' => $message
        ];
        $this->mailer->send($mail['subject'], $userEmail, $mail['message']);

        return $this->response->success('Account status updated successfully');
    }

    public function count()
    { 
        $counts = $this->userModel->countAllRoles();
        if (count($counts) === 0) {
            return $this->response->fail('Counts failed', 500);
        } 
        // Prepare data
        return $this->response->success('Counts fetched successfully', ['counts' => $counts]);
    }

    public function billing(array $payload, int $userId)
    { 
        $check = $this->userModel->getBillingDetails($userId);
        if ($check === false) {
            $created = $this->userModel->createBillingDetails($payload['address'], $payload['city'], $payload['code'], $userId);
            if ($created === false) {
                return $this->response->fail('Failed to create details', 500);
            } 
            // Prepare data
            return $this->response->success('Details created successfully');
        }
        else{
            $updated = $this->userModel->updateBillingDetails($payload['address'], $payload['city'], $payload['code'], $userId);
            if ($updated === false) {
                return $this->response->fail('Failed to update details', 500);
            } 
            // Prepare data
            return $this->response->success('Details updated successfully');
        }
    }

    public function logout(string $role)
    {
        // Define full paths
        $path = [
            'admin'     => $this->baseUrl . '/admin',
            'vendor'    => $this->baseUrl . '/login',
            'customer'  => $this->baseUrl . '/login',
            'affiliate' => $this->baseUrl . '/login',
        ];

        // Destroy session
        $this->session->terminate('user');

        // Success response
        return $this->response->success('Logout successful', ['dashboard' => $path[$role]]);
    }

    public function contact(array $payload)
    {
        // Handle contact: ensure we strip leading 0 only if it exists
        $cleanContact = ltrim($payload['contact'], '0');
        $payload['contact'] = $payload['code'] . $cleanContact;

        // Build message
        $builtMessage = "
            A message was sent by <b> " . trim($payload['name']) . "</b> from  <b> " . trim($payload['country']) . "</b>
            <br>
            You can reach out to them via their mobile: <b>" . trim($payload['contact']) . "</b> or email address: <b>" . trim($payload['email']) . "</b>
        ";

        // Define dates and time
        $fullDate = date('Y-m-d H:i:s');
        $shortDate = date('Y-m-d');
        $time = date('H:i');

        // Send admin mails
        $admins = $this->userModel->allByRole('Admin');
        foreach ($admins as $admin) {
            $this->mailer->send($payload['subject'], $admin['email'], $builtMessage);
            
            $notification = $this->notificationModel->create($payload['message'], 'New Message', $admin['user_id'], $fullDate, 'Unread');
            if ($notification === false) {
                return $this->response->fail('Failed to create notification for admin', 500);
            }

            $mailed = $this->mailModel->createMail('Text', $payload['subject'], $payload['name'], $admin['email'], $shortDate, $time, $payload['message'], 'None', 'None');
            if ($mailed === false) {
                return $this->response->fail('Failed to create mail for admin', 500);
            }
        }

        return $this->response->success('Message sent successfully');
    }

    public function subscribe(array $payload, int $userId, string $userType)
    {
        // Save subscription
        $subscribed = $this->pushModel->saveToken($payload['token'], $payload['device_id'], $userId, $userType);
        if ($subscribed) {
            return $this->response->success('Sync successful');
        } else {
            return $this->response->fail('Sync failed');
        }
    }

    public function unsubscribe(string $token, string $deviceId) 
    {
        $deleted = $this->pushModel->deactivateToken($token, $deviceId);
        if ($deleted === false) {
            return $this->response->fail('Failed to disable notifications', 500);
        }
        return $this->response->success('Notifications disabled successfully');
    }

    public function fetch(string $role, int $page)
    { 
        $users = $this->userModel->getByRole($role, $page);
        if (count($users['users']) === 0) {
            return $this->response->fail('Failed to fetch users', 500);
        }
        // Prepare data
        return $this->response->success('Users fetched successfully', $users);
    }

    public function delete(int $id)
    {
        $deleted = $this->userModel->deleteUser($id);
        if ($deleted === false) {
            return $this->response->fail('Failed to delete user', 500);
        } 
        // Prepare data
        return $this->response->success('User deleted successfully');
    }

    public function id(int $id)
    { 
        $file = $this->userModel->getID($id);
        if (!$file || in_array($file, [null, 'None'])) {
            return $this->response->fail('Failed to fetch user document', 500);
        } 
        // Prepare data
        return $this->response->success('Document fetched successfully', ['file' => $file]);
    }
}
