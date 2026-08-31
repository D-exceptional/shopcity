<?php

declare(strict_types=1);

namespace App\Http\Middlewares;

use App\Http\Request;
use App\Redis\RedisManager;
use App\Redis\RedisStore;
use App\Auth\JWT;
use App\Exceptions\MiddlewareException;

class AuthMiddleware extends RedisStore
{
    public function __construct(
        RedisManager $redis,
        protected JWT $jwt
    ) {
        parent::__construct(
            $redis->session()
        );

        $this->jwt = $jwt;
    }

    public function handle(
        Request $request, 
        callable $next, 
        array $config = []
    ) {
        
        $token = $request->bearerToken();

        try {
            $payload = $this->jwt->verify($token);

            $sessionId = $payload['session_id'];

            $session = $this->getValue("session:$sessionId");

            if (!$session) {
                throw new MiddlewareException('Session expired', 401, 'redirect', '/login');
            }

            // Role check (if provided)
            if (isset($config['role'])) {
                $role = $payload['role'];

                $allowed = (array) $config['role'];

                if (!in_array($role, $allowed, true)) {
                    throw new MiddlewareException('Access denied', 403, 'json', '/login');
                }
            }

            // Remove unnecessary session data from payload
            unset($payload['session_id']);
            unset($payload['iat']);
            unset($payload['exp']);

            // Attach authenticated user
            $request->setUser($payload);

        } catch (\Exception $e) {

            throw new MiddlewareException($e->getMessage(), 401, 'redirect', '/login');
        }

        return $next($request);
    }
}