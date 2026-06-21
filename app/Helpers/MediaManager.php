<?php

namespace App\Helpers;

use Cloudinary\Cloudinary;
use Exception;

require_once dirname(__DIR__, 2) . '/bootstrap.php';

class MediaManager
{
    protected Cloudinary $cloudinary;
    protected string $logFile;
    protected string $pemFile;
    protected bool $isLocal;

    public function __construct()
    {
        // Load Cloudinary config (v3)
        $this->cloudinary = require dirname(__DIR__, 2) . '/config/cloudinary.php';

        // Log file setup
        $this->logFile = dirname(__DIR__, 2) . '/storage/logs/media_manager.log';
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }

        $this->isLocal = in_array($_SERVER['SERVER_NAME'] ?? 'localhost', ['localhost', '127.0.0.1']) || PHP_SAPI === 'cli';

        // SS Lcertificate path
        $this->pemFile = $this->isLocal 
            ? "C:/wamp64/www/projects/eCommerce/storage/cacert.pem"
            : dirname(__DIR__, 2) . "/storage/cacert.pem";

        if (!file_exists($this->pemFile)) {
            $this->log("⚠️ PEM file not found at {$this->pemFile}");
        }
    }

    protected function log(string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        error_log("[{$timestamp}] {$message}\n", 3, $this->logFile);
    }

    public function detect(string $url): string
    {
        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) return 'image';
        if (in_array($ext, ['mp4','webm','avi','mov','mkv'])) return 'video';
        return 'raw';
    }

    public function extract(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $parts = explode('/', trim($path, '/'));
        $uploadIndex = array_search('upload', $parts);
        if ($uploadIndex === false) return '';

        $publicIdParts = array_slice($parts, $uploadIndex + 1);

        if (isset($publicIdParts[0]) && preg_match('/^v\d+$/', $publicIdParts[0])) {
            array_shift($publicIdParts);
        }

        $lastPart = array_pop($publicIdParts);
        $filename = pathinfo($lastPart, PATHINFO_FILENAME);

        $publicIdParts[] = $filename;

        return implode('/', $publicIdParts);
    }

    public function delete(string $url): array
    {
        $this->log("Deleting: {$url}");

        $resourceType = $this->detect($url);
        $publicId     = $this->extract($url);

        if (empty($publicId)) {
            return ['status' => 'error', 'message' => 'Invalid public_id from URL'];
        }

        $options = [
            'resource_type' => $resourceType
        ];

        // SSL handling
        if ($this->isLocal) {
            $options['curl_options'] = [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0
            ];
        } else {
            if (!file_exists($this->pemFile)) {
                return ['status' => 'error', 'message' => 'SSL certificate missing'];
            }

            $options['curl_options'] = [
                CURLOPT_CAINFO => $this->pemFile,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2
            ];
        }

        try {
            // 🔥 UploadApi delete (single asset)
            $result = $this->cloudinary
                ->uploadApi()
                ->destroy($publicId, $options);

            $this->log("Delete result: " . json_encode($result));

            if (($result['result'] ?? null) === 'ok') {
                return ['status' => 'success', 'message' => 'File deleted'];
            }

            return [
                'status'  => 'warning',
                'message' => $result['result'] ?? 'Delete not confirmed'
            ];

        } catch (Exception $e) {
            $this->log("Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function deleteBulk(array $urls): array
    {
        $grouped = [
            'image' => [],
            'video' => [],
            'raw'   => []
        ];

        foreach ($urls as $url) {
            $publicId = $this->extract($url);
            if (!$publicId) continue;

            $type = $this->detect($url);
            $grouped[$type][] = $publicId;
        }

        return $this->deleteGrouped($grouped);
    }

    protected function deleteGrouped(array $grouped): array
    {
        $results = [];

        foreach ($grouped as $type => $publicIds) {
            if (empty($publicIds)) continue;

            try {
                $res = $this->cloudinary
                    ->adminApi()
                    ->deleteAssets($publicIds, [
                        'resource_type' => $type
                    ]);

                $results[$type] = $res;

            } catch (\Exception $e) {
                $results[$type] = [
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }
}
