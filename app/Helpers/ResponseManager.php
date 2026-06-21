<?php

namespace App\Helpers;

class ResponseManager
{
    /**
     * Send a success response from controllers (formerly)
     */
    public function succeed(string $message = 'Success', $data = null, int $status = 200, string $caller = 'api'): void
    {
        $this->format(true, $status, $message, $data, null, $caller);
    }

    /**
     * Send an error response from controllers (formerly) / router
     */
    public function error(string $message = 'An error occurred', int $status = 400, $error = null, string $caller = 'api'): void
    {
        $this->format(false, $status, $message, null, $error, $caller);
    }

    /**
     * Core formatting method for controllers (formerly) and router response
     */
    protected function format(bool $ok, int $status, string $message, $data, $error, $caller): void
    {
        http_response_code($status); // set HTTP status code
        header('Content-Type: application/json'); // set response type

        echo json_encode([
            'ok'      => $ok,
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
            'error'   => $error
        ]);

        // Exit only when it's an API call
        if ($caller === 'api') {
            exit;
        }
    }

    /**
     * Send success response from services
     */
    public function success(
        ?string $message = null,
        array $data = [],
        int $status = 200
    ): array {
        return [
            'ok'      => true,
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
            'error'   => null
        ];
    }

    /**
     * Send error response from services
     */
    public function fail(
        ?string $message = null,
        int $status = 400,
        $error = null
    ): array {
        return [
            'ok'      => false,
            'status'  => $status,
            'message' => $message,
            'data'    => null,
            'error'   => $error
        ];
    }

    /**
     * Send final response to client from controllers
     */
    public function flash(array $result): never
    {
        http_response_code($result['status']);     // set http response code
        header('Content-Type: application/json'); // set response type

        echo json_encode([
            'ok'      => $result['ok'],
            'status'  => $result['status'],
            'message' => $result['message'],
            'data'    => $result['data'],
            'error'   => $result['error']
        ]);

        exit;
    }
}
