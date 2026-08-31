<?php

declare(strict_types=1);

namespace App\Http\Middlewares;

use App\Http\Request;
use App\Contracts\SessionInterface;
use App\Exceptions\MiddlewareException;

class CsrfMiddleware
{
    public function __construct(
        protected SessionInterface $session
    ) {}

    // =========================================
    // HANDLE CSRF CHECKS
    // =========================================
    public function handle(
        Request $request, 
        callable $next, 
        array $config = []
    ) {
        $method = $request->method();

        // Safe methods bypass CSRF
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $next($request);
        }

        // Get headers & token
        $headers = $request->headers();
        $token =
            $headers['X-CSRF-TOKEN']
            ?? $headers['x-csrf-token']
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? $_POST['_csrf']
            ?? null;

        // Check token validity
        if (!$this->session->validateCsrf($token)) {
            throw new MiddlewareException('Invalid or missing CSRF token', 419, 'json', '/login');
        }

        // Continue pipeline
        return $next($request);
    }
}
