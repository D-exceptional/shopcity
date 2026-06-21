<?php
namespace App\Middlewares;

use App\Helpers\SessionManager;

class CsrfMiddleware
{
    protected SessionManager $session;

    public function __construct(SessionManager $session)
    {
        $this->session = $session;
    }

    public function default(): array
    {
        return $this->allow();
    }

    public function handle(): array
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Safe methods bypass CSRF
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $this->allow();
        }

        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $token =
            $headers['X-CSRF-TOKEN']
            ?? $headers['x-csrf-token']
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? $_POST['_csrf']
            ?? null;

        if (!$this->session->validateCsrf($token)) {
            return $this->deny(419, 'Invalid or missing CSRF token', 'json', '/login');
        }

        return $this->allow();
    }

    protected function allow(): array
    {
        return [
            'ok'       => true,
            'status'   => 200,
            'message'  => 'CSRF validated',
            'action'   => 'continue',
            'redirect' => null,
        ];
    }

    protected function deny(int $status, string $message, string $action, string $path): array
    {
        return [
            'ok'       => false,
            'status'   => $status,
            'message'  => $message,
            'action'   => $action,
            'redirect' => $path,
        ];
    }
}
