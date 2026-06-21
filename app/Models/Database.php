<?php

namespace App\Models;

use PDO;
use Exception;

abstract class Database
{
    /**
     * 🔹 Shared PDO connection for the entire app
     */
    protected static ?PDO $sharedConnection = null;

    /**
     * 🔹 Reference to the shared connection
     */
    protected PDO $db;

    public function __construct()
    {
        if (self::$sharedConnection === null) {
            self::$sharedConnection = $this->connect();
        }

        $this->db = self::$sharedConnection;
    }

    /**
     * Establish a new PDO connection (only called once)
     */
    protected function connect(): PDO
    {
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $db   = $_ENV['DB_NAME'] ?? '';
        $user = $_ENV['DB_USER'] ?? '';
        $pass = $_ENV['DB_PASS'] ?? '';

        if (!$db || !$user) {
            throw new Exception("Database environment variables not set", 1);
        }

        $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT         => true // ✅ Helps reuse DB connection
            ]);
        } catch (Exception $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }

        return $pdo;
    }

    /**
     * Get the shared PDO instance
     */
    public static function getDb(): PDO
    {
        return self::$sharedConnection;
    }

    // ==========================================================
    // 🔹 Query Helpers
    // ==========================================================

    protected function queryAll(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    protected function queryOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    protected function executeQuery(string $sql, array $params = []): bool
    {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    protected function lastInsertId(): string
    {
        return $this->db->lastInsertId();
    }

    // ==========================================================
    // 🔹 Transaction helpers
    // ==========================================================

    protected function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    protected function commit(): bool
    {
        return $this->db->commit();
    }

    protected function rollback(): bool
    {
        return $this->db->rollBack();
    }

    protected function transaction(callable $callback)
    {
        try {
            $this->beginTransaction();
            $result = $callback($this->db);
            $this->commit();
            return $result;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
}
