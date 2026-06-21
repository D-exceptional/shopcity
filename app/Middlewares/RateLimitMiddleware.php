<?php
namespace App\Middlewares;

use App\Services\RateLimiterService;
use App\Helpers\ResponseManager;
use App\Helpers\SessionManager;

class RateLimitMiddleware
{
    protected RateLimiterService $service;
    protected ResponseManager $response;
    protected SessionManager $session;

    public function __construct(RateLimiterService $service, ResponseManager $response, SessionManager $session) 
    {
        $this->service  = $service;
        $this->response = $response;
        $this->session  = $session;
    }

    public function handle(array $options = [])
    {
        if (!function_exists('apcu_fetch')) {
            return $this->allow("APCu is not enabled", 505); 
        }

        $userId = $this->session->id();
        $userIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $identifier = $userId ? "user:$userId" : "ip:$userIp";
        $scope      = $options['scope'] ?? 'global';
        $key        = "rate:{$scope}:{$identifier}";

        $result = $this->service->check($userId, $key, $options);
        if (!$result['ok']) {
            return $this->deny($result['message'], $result['status'], $result['retry']);
        }

        return $this->allow('Authorized', 200);
    }

    /* ----------------------------------
       Helpers
    -----------------------------------*/

    protected function allow(string $message, int $status,): array
    {
        return [
            'ok'       => true,
            'status'   => $status,
            'message'  => $message,
            'action'   => 'continue',
            'redirect' => null,
        ];
    }

    protected function deny(string $message, int $status, int $retry): array
    {
        return [
            'ok'       => false,
            'status'   => $status,
            'message'  => $message,
            'action'   => 'json',
            'headers'  => [
                'Retry-After' => $retry
            ],
            'redirect' => null,
        ];
    }
}
