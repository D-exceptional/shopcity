<?php

declare(strict_types=1);

namespace App\Services\Api;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;  
use PHPMailer\PHPMailer\SMTP;

use App\Core\Result;
use App\Support\TextManager;
use App\Mail\MailManager;
use App\Models\Mail;
use App\Models\Notification;

class MailService
{
    private array $smtpConfig = [];
    private string $baseUrl;

    public function __construct(
        protected Result $result, 
        protected TextManager $textManager, 
        protected MailManager $mailManager,
        protected Mail $mailModel,
        protected Notification $notificationModel
    ) {
        $this->smtpConfig = [
            'host'      => env('MAIL_HOST'),
            'username'  => env('MAIL_USERNAME'),
            'password'  => env('MAIL_PASSWORD'),
            'fromEmail' => env('MAIL_ADDRESS'),
            'fromName'  => env('MAIL_SENDER'),
            'port'      => env('MAIL_PORT'),
            'secure'    => env('MAIL_SECURE')
        ];

        $this->baseUrl = $appUrl; 
    }

    public function countMessages(
        string $email, 
        string $name
    ): Result {

        $inbox  = $this->mailModel->countInbox($email);
        $outbox = $this->mailModel->countOutbox($name);

        return $this->result->success('Messages counted', ['inbox' => $count, 'outbox' => $outbox]);
    }

    public function getInbox(
        string $email, 
        int $page
    ): Result {

        $inbox = $this->mailModel->getInbox($email, $page);
        if (count($inbox['mails']) === 0) {
            return $this->result->error('No inbox to fetch', 200);
        }

        return $this->result->success('Inbox fetched', $inbox);
    }

    public function getOutbox(
        string $name, 
        int $page
    ): Result {

        $outbox = $this->mailModel->getOutbox($name, $page);
        if (count($outbox['mails']) === 0) {
            return $this->result->error('No outbox to fetch', 200);
        }

        return $this->result->success('Outbox fetched', $outbox);
    }

    public function getMail(
        int $mailId
    ): Result {

        $mail = $this->mailModel->getMail($mailId);
        if ($mail === false) {
            return $this->result->error('Failed to fetch mail', 500);
        }

        return $this->result->success('Mail fetched', ['mail' => $mail]);
    }

    public function deleteMail(
        int $mailId
    ): Result {   

        $deleted = $this->mailModel->deleteMail($mailId);
        if ($deleted === false) { 
            return $this->result->error('Failed to delete mail', 500); 
        }

        return $this->result->success('Mail deleted successfully');
    }

    public function sendBulk(
        array $recipients,
        string $subject,
        string $message,
        string $sender,
        bool $hasAttachment = false, 
        array $file = []
    ): Result {

        if (empty($subject) || empty($message) || empty($sender)) {
            return $this->result->error('Some fields are empty. Check all entries and try again.', 400);
        }

        if (empty($recipients)) {
            return $this->result->error('No recipients specified for this process.', 400);
        }

        return $this->processBulkMail($recipients, $subject, $message, $sender, $hasAttachment, $file);
    }

    private function processBulkMail(
        array $recipients,
        string $subject,
        string $message,
        string $sender,
        bool $hasAttachment = false, 
        array $file = []
    ): Result {

        $type     = $hasAttachment ? 'Multimedia' : 'Text';
        $filename = null;
        $file_ext = null;
        $filePath = null;

        // Handle file upload if exists
        if ($hasAttachment && !empty($file['name'])) {
            $targetDir = dirname(__DIR__) . "/../attachments/";
            if (!is_dir($targetDir)) { 

                mkdir($targetDir, 0777, true); 
            }

            $filename = basename($file['name']);
            $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $tmp_name = $file['tmp_name'];

            $allowed_exts = ["jpeg", "png", "jpg", "pdf", "mp3", "mp4", "docx"];
            if (!in_array($file_ext, $allowed_exts)) {

                return $this->result->error('Invalid file type. Allowed: .jpg, .jpeg, .png, .pdf, .mp3, .mp4, .docx', 400);
            }

            $filePath = $targetDir . $filename;
            if (!move_uploaded_file($tmp_name, $filePath)) {

                return $this->result->error('Failed to upload attachment', 500); 
            }
        }

        $mailRecipients = [];
        
        foreach ($recipients as $receiver) {
            $fullname    = $receiver['name'] ?? 'User';
            $email       = $receiver['email'] ?? '';
            $recipientId = (int) $receiver['id'] ?? null;
            $member      = $receiver['member'] ?? null;
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

            $fullMessage = "
                <b style='font-size: 20px;'>Dear {$fullname}, </b>
                <br>
                <hr style='opacity:0;'>
                {$message}
            ";

            $mailRecipients[] = [
                "mail_type"      => $type,
                "mail_subject"   => $subject,
                "mail_sender"    => $sender,
                "mail_receiver"  => $email,
                "mail_message"   => $this->textManager->formatMailMessage($fullMessage),
                "mail_filename"  => $filename,
                "mail_extension" => $file_ext,
                "userId"         => $recipientId,
                "member"         => $member,
            ];
        }

        if (empty($mailRecipients)) {
            return $this->result->error('No valid email recipients found');
        }

        /*
        $this->eventDispatcher->dispatch(
            new MailSent(
                recipients: $mailRecipients,
                hasAttachment: $hasAttachment,
                type: $type,
            )
        );
        */

        return $this->sendBulkMail($mailRecipients, $hasAttachment, $type);
    }

    private function sendBulkMail(
        array $recipients, 
        bool $hasAttachment = false, 
        string $type = 'Text'
    ): Result {

        $errors  = [
            'mail'         => [],
            'notification' => [],
            'smtp'         => []
        ];

        $batches = array_chunk($recipients, 10);

        foreach ($batches as $index => $batch) {
            foreach ($batch as $recipient) {
                
                // Create in-app mail 
                $mailCreated = $this->mailModel->createMail(
                    $recipient['mail_type'],
                    $recipient['mail_subject'],
                    $recipient['mail_sender'],
                    $recipient['mail_receiver'],
                    $recipient['mail_date'],
                    $recipient['mail_time'],
                    $recipient['mail_message'],
                    $recipient['mail_filename'],
                    $recipient['mail_extension']
                );

                if (!$mailCreated) {
                    $errors['mail'][] = ['mailbox_error' => "Failed to save mail for {$recipient['mail_receiver']}"];
                    continue;
                }

                // Create in-app notification
                $notificationCreated = $this->notificationModel->create(
                    'An incoming mail was received',
                    'New Message',
                    $recipient['userId'],
                );

                if (!$notificationCreated) {
                    $errors['notification'][] = ['notification_error' => "Failed to save notification for {$recipient['mail_receiver']}"];
                    continue;
                }

                // Send email
                if ($type === 'Text') {
                    $this->mailManager->sendSimpleMail($recipient['mail_subject'], $recipient['mail_receiver'], $recipient['mail_message']);
                } else {
                    if (!$this->sendEmail($recipient, $hasAttachment)) {
                        $errors['smtp'][] = ['email_error' => "Failed to send email to {$recipient['mail_receiver']}"];
                    }
                }
            }

            if ($index < count($batches) - 1) sleep(2);
        }

        if (
            !empty($errors['mail']) 
            || !empty($errors['notification']) 
            || !empty($errors['smtp']) 
        ) {
            return $this->result->error('Some emails, notifications failed to send or save.', 500, ['errors' => $errors]);
        }

        return $this->result->success('Message sent successfully');
    }

    private function sendEmail(
        array $recipient, 
        bool $hasAttachment = false
    ): bool {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $this->smtpConfig['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->smtpConfig['username'];
            $mail->Password   = $this->smtpConfig['password'];
            $mail->SMTPSecure = $this->smtpConfig['secure'];
            $mail->Port       = $this->smtpConfig['port'];

            $mail->setFrom($this->smtpConfig['fromEmail'], $this->smtpConfig['fromName']);
            $mail->addAddress($recipient['mail_receiver']);

            if ($hasAttachment && isset($recipient['filePath']) && file_exists($recipient['filePath'])) {
                $mail->addAttachment($recipient['filePath']);
            }

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $recipient['mail_subject'];
            $mail->Body    = $recipient['mail_message'];
            $mail->send();

            return true;
        } catch (Exception $e) {
            error_log("PHPMailer Error ({$recipient['mail_receiver']}): " . $e->getMessage());
            return false;
        }
    }
}
