<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Core\Result;
use App\Support\TextManager;
use App\Events\Mail\MailSent;
use App\Events\EventDispatcher;

class MailService
{
    private array $smtpConfig = [];
    private string $baseUrl;

    public function __construct(
        protected Result $result,
        protected TextManager $textProcessor,
        protected EventDispatcher $eventDispatcher,
        protected Mail $mailModel,
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

        $this->eventDispatcher->dispatch(
            new MailSent(
                recipients: $mailRecipients,
                hasAttachment: $hasAttachment,
                type: $type,
            )
        );

        return $this->result->success('Message queued successfully');
    }
}
