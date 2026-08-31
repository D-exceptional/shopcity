<?php

declare(strict_types=1);

namespace App\Models;

class Mail extends Model
{
   protected string $table = 'mailbox';

    public function createMail(
        string $type, 
        string $subject, 
        string $sender, 
        string $receiver, 
        string $date, 
        string $time,
        string $message,
        string $filename,
        string $extension
    ): bool {

        return $this->query()
            ->insert([
                'mail_type'      => $type,
                'mail_subject'   => $subject,
                'mail_sender'    => $sender,
                'mail_receiver'  => $receiver,
                'mail_date'      => $date,
                'mail_time'      => $time,
                'mail_message'   => $message,
                'mail_filename'  => $filename,
                'mail_extension' => $extension,
            ]);
    }
    
    public function countInbox(
        string $email
    ): int {

        return $this->query()
            ->where('mail_receiver', '=', $email)
            ->count();
    }

    public function countOutbox(
        string $name
    ): int {

        return $this->query()
            ->where('mail_sender', '=', $name)
            ->count();
    }

    public function getInbox(
        ?string $email = null, 
        int $page = 1, 
        int $limit = 20
    ): array {

        $mails = $this->query()
            ->where('mail_receiver', '=', $email)
            ->orderBy('mail_date', 'DESC')
            ->paginate($page, $limit)
            ->get();

        $total = $this->countInbox($email);

        return $this->format($mails, $total, $page, $limit);
    }

    public function getOutbox(
        ?string $name = null, 
        int $page = 1, 
        int $limit = 20
    ): array {

        $mails = $this->query()
            ->where('mail_sender', '=', $name)
            ->orderBy('mail_date', 'DESC')
            ->paginate($page, $limit)
            ->get();

        $total = $this->countOutbox($name);

        return $this->format($mails, $total, $page, $limit);
    }

    public function getMail(
        int $mailId
    ): ?array {

        return $this->query()
            ->where('mail_id', '=', $mailId)
            ->first();
    }

    public function deleteMail(
        int $mailId
    ): bool {

        return $this->query()
            ->where('mail_id', '=', $mailId)
            ->delete();
    }

    public function getVendorMailStats(
        string $email, 
        string $name
    ): array {

        return [
            'inbox'  => $this->countInbox($email),
            'outbox' => $this->countOutbox($name),
        ];
    }

    public function getAdminMailStats(
        string $email, 
        string $name
    ): array {

        return [
            'inbox'  => $this->countInbox($email),
            'outbox' => $this->countOutbox($name),
        ];
    }

    private function format(
        array $data, 
        int $total, 
        int $page, 
        int $limit
    ): array {

        $start = ($page - 1) * $limit + 1;
        $end   = min($page * $limit, $total); // ensures it doesn’t exceed total

        return [
            'mails'         => $data,
            'total'         => $total,
            'page'          => $page,
            'per_page'      => $limit,
            'total_pages'   => ceil($total / $limit),
            'display_range' => "{$start}-{$end}/{$total}" // e.g. "1-5/200"
        ];
    }
}
