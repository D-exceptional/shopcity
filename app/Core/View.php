<?php

declare(strict_types=1);

namespace App\Core;

class View
{
     /**
     * Shared data available to all views.
     *
     * @var array<string, mixed>
     */
    protected array $shared = [];

    /**
     * Share data with all views.
     */
    public function share(
        string $key,
        mixed $value
    ): void {
        $this->shared[$key] = $value;
    }

    /**
     * Render a view and return its HTML.
     *
     * @param string $view
     * @param array<string, mixed> $data
     *
     * @throws \RuntimeException
     */
    public function render(
        string $view,
        array $data = []
    ): string {

        // Check if views are enabled.
        if (!config('app.display.use_views')) {
            throw new \RuntimeException(
                'Views are disabled in the configuration.'
            );
        }

        // Resolve view directory.
        $viewPath = dirname(__DIR__, 2)
            . DIRECTORY_SEPARATOR
            . config('app.display.view_path');

        // Convert dot notation.
        $view = str_replace(
            '.',
            DIRECTORY_SEPARATOR,
            $view
        );

        $file = $viewPath
            . DIRECTORY_SEPARATOR
            . $view
            . '.php';

        if (!is_file($file)) {
            throw new \RuntimeException(
                "View [{$view}] not found."
            );
        }

        $this->log(
            'VIEW RENDER OBJECT: ' . spl_object_id($this)
        );

        $this->log(
            'VIEW SHARED BEFORE RENDER: ' .
            json_encode($this->shared)
        );

        // Merge shared data with view-specific data.
        $data = array_merge(
            $this->shared,
            $data
        );

        // Extract variables.
        extract($data, EXTR_SKIP);

        ob_start();

        require $file;

        return ob_get_clean() ?: '';
    }

    // =========================================
    // WRITE MEDIA LOG
    // =========================================
    private function log(
        mixed $data
    ): void {

        $timestamp = date('Y-m-d H:i:s');

        $message = is_array($data)
            ? json_encode($data, JSON_PRETTY_PRINT)
            : (string) $data;

        $logFile =
            dirname(__DIR__, 2) .
            '/storage/logs/php-error.log';

        $result = file_put_contents(
            $logFile,
            "[{$timestamp}] {$message}" . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );

        if ($result === false) {
            throw new \RuntimeException(
                "Unable to write service provider log: {$logFile}"
            );
        }
    }
}