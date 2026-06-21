<?php
namespace App\Models;

use PDO;

class Mail extends Database
{
    /**
     * Create a new mail
    */
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
    ): bool
    {
        $stmt = $this->db->prepare("
           INSERT INTO mailbox 
            (mail_type, mail_subject, mail_sender, mail_receiver, mail_date, mail_time, mail_message, mail_filename, mail_extension) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$type, $subject, $sender, $receiver, $date, $time, $message, $filename, $extension]);
    }
    
     /**
     * Count admin inbox
    */
    public function countInbox(string $email): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM mailbox WHERE mail_receiver = ?");
        $stmt->execute([$email]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Count admin outbox
    */
    public function countOutbox(string $name): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM mailbox WHERE mail_sender = ?");
        $stmt->execute([$name]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Paginate results
    */
    private function paginate(array $data, int $total, int $page, int $perPage): ?array
    {
        // Calculate start and end item numbers
        $start = ($page - 1) * $perPage + 1;
        $end   = min($page * $perPage, $total); // ensures it doesn’t exceed total

        return [
            'mails'         => $data,
            'total'         => $total,
            'page'          => $page,
            'per_page'      => $perPage,
            'total_pages'   => ceil($total / $perPage),
            'display_range' => "{$start}-{$end}/{$total}" // e.g. "1-5/200"
        ];
    }

     /**
     * Fetch admin inbox logs
    */
    public function getInbox(?string $email = null, int $page = 1, int $perPage = 20): ?array
    {
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare("SELECT * FROM mailbox WHERE mail_receiver = ? ORDER BY mail_date DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, (string)$email, PDO::PARAM_STR);
        $stmt->bindValue(2, (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        // return $stmt->fetchAll();

        // Modification
        $mails = $stmt->fetchAll();
        $total = $this->countInbox($email);

        return $this->paginate($mails, $total, $page, $perPage);
    }

     /**
     * Fetch admin outbox logs
    */
    public function getOutbox(?string $name = null, int $page = 1, int $perPage = 20): ?array
    {
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare("SELECT * FROM mailbox WHERE mail_sender = ? ORDER BY mail_date DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, (string)$name, PDO::PARAM_STR);
        $stmt->bindValue(2, (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        // return $stmt->fetchAll();

        // Modification
        $mails = $stmt->fetchAll();
        $total = $this->countOutbox($name);

        return $this->paginate($mails, $total, $page, $perPage);
    }

    /**
     * Get mail by ID
     */
    public function getMail(int $mailId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM mailbox WHERE mail_id = ?");
        $stmt->execute([$mailId]);
        $result = $stmt->fetch();
        return $result;
    }

    /**
     * Delete mail record
     */
    public function deleteMail(int $mailId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM mailbox WHERE mail_id = ?");
        return $stmt->execute([$mailId]);
    }

    /**
     * Fetch all key dashboard stats in one call.
    */
    public function getVendorMailStats(string $email, string $name): ?array
    {
        return [
            'inbox'  => $this->countInbox($email),
            'outbox' => $this->countOutbox($name),
        ];
    }

     /**
     * Fetch all key dashboard stats in one call.
    */
    public function getAdminMailStats(string $email, string $name): ?array
    {
        return [
            'inbox'  => $this->countInbox($email),
            'outbox' => $this->countOutbox($name),
        ];
    }
}
