<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;  
use PHPMailer\PHPMailer\SMTP;

use App\Helpers\SessionManager;
use App\Helpers\Validator;
use App\Helpers\ResponseManager;
use App\Helpers\MailManager;
use App\Helpers\PushManager;
use App\Models\Mail;
use App\Models\Notification;

require_once dirname(__DIR__, 2) . '/bootstrap.php'; // Auto load files

class MailService
{
    protected SessionManager $session;
    protected Validator $validator;
    protected ResponseManager $response;
    protected MailManager $mailer;
    protected PushManager $push;
    protected Mail $mailModel;
    protected Notification $notificationModel;
    private array $smtpConfig = [];
    private string $baseUrl;

    public function __construct(
        SessionManager $session,
        Validator $validator,
        ResponseManager $response,
        MailManager $mailer,
        PushManager $push,
        Mail $mailModel,
        Notification $notificationModel
    )
    {
        // Required services for this controller class
        $this->session   = $session;
        $this->validator = $validator;
        $this->response  = $response;
        $this->mailer    = $mailer;
        $this->push      = $push;
        // Required model for this controller class
        $this->mailModel         = $mailModel;
        $this->notificationModel = $notificationModel;
        // Set base datetime
        date_default_timezone_set("Africa/Lagos");
        // Setup SMTP configurations
        $this->smtpConfig = [
            'host'      => $_ENV['MAIL_HOST'],
            'username'  => $_ENV['MAIL_USERNAME'],
            'password'  => $_ENV['MAIL_PASSWORD'],
            'fromEmail' => $_ENV['MAIL_ADDRESS'],
            'fromName'  => $_ENV['MAIL_SENDER'],
            'port'      => $_ENV['MAIL_PORT'],
            'secure'    => $_ENV['MAIL_SECURE']
        ];
        // Set base URL
        $this->baseUrl = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) ? 'http://localhost/projects/demos/shopcity' : '__remote__site__link__';
    }

    // *** BASIC MAIL FEATURES ********* //
     public function countInbox(string $email)
    {
        $count = $this->mailModel->countInbox($email);
        if ($count === null) return $this->response->fail('Failed to get count', 500);
        return $this->response->success('Inbox counted', ['count' => $count]);
    }

    public function countOutbox(string $name)
    {
        $count = $this->mailModel->countOutbox($name);
        if ($count === null) return $this->response->fail('Failed to get count', 500);
        return $this->response->success('Outbox counted', ['count' => $count]);
    }

    public function getInbox(string $email, int $page)
    {
        $inbox = $this->mailModel->getInbox($email, $page);
        if (count($inbox['mails']) === 0) return $this->response->fail('No inbox to fetch', 200);
        return $this->response->success('Inbox fetched', $inbox);
    }

    public function getOutbox(string $name, int $page)
    {
        $outbox = $this->mailModel->getOutbox($name, $page);
        if (count($outbox['mails']) === 0) return $this->response->fail('No outbox to fetch', 200);
        return $this->response->success('Outbox fetched', $outbox);
    }

    public function getMail(int $id)
    {
        $mail = $this->mailModel->getMail($id);
        if ($mail === false) return $this->response->fail('Failed to fetch mail', 500);
        return $this->response->success('Mail fetched', ['mail' => $mail]);
    }

    public function deleteMail(int $id)
    {        
        $deleted = $this->mailModel->deleteMail($id);
        if ($deleted === false) return $this->response->fail('Failed to delete mail', 500);
        return $this->response->success('Mail deleted successfully');
    }
    // *** BASIC MAIL FEATURES END ********* //

    /**
     * Entry point — auto detect if request includes a file
     * (so you don’t need two separate routes for text/attachment)
    */
    public function sendBulk(array $payload)
    {
        $hasAttachment = isset($payload['_files']['attachment']);
        $file = $hasAttachment ? $payload['_files']['attachment'] : [];
        return $this->processBulkMail($payload, $hasAttachment, $file);
    }

    /**
     * Core bulk mail handler for both text and attachments
    */
    private function processBulkMail(array $data, bool $hasAttachment = false, array $file = [])
    {
        if (empty($data['recipients']) || empty($data['subject']) || empty($data['message']) || empty($data['sender'])) {
            return $this->response->fail('All fields must be filled up');
        }

        $recipients = $data['recipients'];
        $subject = $data['subject'];
        $message = $data['message'];
        $sender = $data['sender'];

        $date = date('Y-m-d');
        $time = date('H:i');
        $type = $hasAttachment ? 'Multimedia' : 'Text';

        $filename = 'None';
        $file_ext = 'None';
        $filePath = null;

        // Handle file upload if exists
        if ($hasAttachment && !empty($file['name'])) {
            $targetDir = dirname(__DIR__) . "/../attachments/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

            $filename = basename($file['name']);
            $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $tmp_name = $file['tmp_name'];

            $allowed_exts = ["jpeg", "png", "jpg", "pdf", "mp3", "mp4", "docx"];
            if (!in_array($file_ext, $allowed_exts)) {
                return $this->response->fail('Invalid file type. Allowed: .jpg, .jpeg, .png, .pdf, .mp3, .mp4, .docx');
            }

            $filePath = $targetDir . $filename;
            if (!move_uploaded_file($tmp_name, $filePath)) {
                return $this->response->fail('Failed to upload attachment');
            }
        }

        // Validate recipients JSON
        $mail_receivers = json_decode($recipients, true);
        if (is_null($mail_receivers)) {
            return $this->response->fail('Invalid JSON in recipients');
        }

        // Prepare messages
        $mailArray = [];
        foreach ($mail_receivers as $value) {
            $fullname = $value['name'] ?? 'User';
            $email = $value['email'] ?? '';
            $recipientId = (int) $value['id'] ?? null;
            $member = $value['member'] ?? null;
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

            $mailArray[] = [
                "mail_type" => $type,
                "mail_subject" => $subject,
                "mail_sender" => $sender,
                "mail_receiver" => $email,
                "mail_date" => $date,
                "mail_time" => $time,
                "mail_message" => $this->formatMessage(
                    "<b style='font-size: 20px;'>Dear {$fullname},</b><br><hr style='opacity:0;'>" . $message
                ),
                "mail_filename" => $filename,
                "mail_extension" => $file_ext,
                "filePath" => $filePath,
                "userId" => $recipientId,
                "member" => $member
            ];
        }

        if (empty($mailArray)) {
            return $this->response->fail('No valid email recipients found');
        }

        return $this->sendBulkMail($mailArray, $hasAttachment, $type);
    }

    /**
     * Send emails in batches
    */
    private function sendBulkMail(array $array, bool $hasAttachment = false, string $type = 'Text')
    {
        $save_date = date('Y-m-d H:i:s');
        $batches = array_chunk($array, 10);
        $errors = [];

        foreach ($batches as $index => $batch) {
            foreach ($batch as $item) {
                // Save mail
                $mailCreated = $this->mailModel->createMail(
                    $item['mail_type'],
                    $item['mail_subject'],
                    $item['mail_sender'],
                    $item['mail_receiver'],
                    $item['mail_date'],
                    $item['mail_time'],
                    $item['mail_message'],
                    $item['mail_filename'],
                    $item['mail_extension']
                );

                if (!$mailCreated) {
                    $errors[] = ['mailbox_error' => "Failed to save mail for {$item['mail_receiver']}"];
                    error_log("Mailbox Save Error ({$item['mail_receiver']}): ");
                    continue;
                }

                // Save notification
                $notificationCreated = $this->notificationModel->create(
                    'An incoming mail was received',
                    'New Message',
                    $item['userId'],
                    $save_date,
                    'Unread'
                );

                if (!$notificationCreated) {
                    $errors[] = ['notification_error' => "Failed to save notification for {$item['mail_receiver']}"];
                    error_log("Notification Save Error ({$item['mail_receiver']}): ");
                    continue;
                }

                // Send email
                if ($type === 'Text') {
                    // $this->mailer->send($item['mail_subject'], $item['mail_receiver'], $item['mail_message']);
                    // Send push notification to admins
                    $processedMessage = $this->processMessage($item['mail_message']);
                    $this->push->send("Single {$item['member']}", $item['userId'], $item['mail_subject'], $processedMessage, [
                        'url' => in_array(strtolower($item['member']), ['customer', 'vendor']) ? "{$this->baseUrl}/login" : "{$this->baseUrl}/admin/",
                        'type' => 'mail'
                    ]);
                } else {
                    if (!$this->sendEmail($item, $hasAttachment)) {
                        $errors[] = ['email_error' => "Failed to send email to {$item['mail_receiver']}"];
                    }
                }
            }

            if ($index < count($batches) - 1) sleep(2);
        }

        if (!empty($errors)) {
            return $this->response->fail('Some emails failed to send or save.', 500, ['errors' => $errors]);
        }

        return $this->response->success('Message sent successfully');
    }

    /**
     * Send single email via PHPMailer
    */
    private function sendEmail(array $item, bool $hasAttachment = false): bool
    {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $this->smtpConfig['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpConfig['username'];
            $mail->Password = $this->smtpConfig['password'];
            $mail->SMTPSecure = $this->smtpConfig['secure'];
            $mail->Port = $this->smtpConfig['port'];

            $mail->setFrom($this->smtpConfig['fromEmail'], $this->smtpConfig['fromName']);
            $mail->addAddress($item['mail_receiver']);

            if ($hasAttachment && isset($item['filePath']) && file_exists($item['filePath'])) {
                $mail->addAttachment($item['filePath']);
            }

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $item['mail_subject'];
            $mail->Body = $item['mail_message'];
            $mail->send();

            return true;
        } catch (Exception $e) {
            error_log("PHPMailer Error ({$item['mail_receiver']}): " . $e->getMessage());
            return false;
        }
    }

    private function formatMessage($text): string
    {
        $text = str_replace(["\r\n", "\\r\\n", "\\r", "\\n"], "\n", $text);
        $text = stripslashes(rtrim($text, '\\'));
        return nl2br($text);
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
}
