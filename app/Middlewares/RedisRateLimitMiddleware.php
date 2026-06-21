<?php
namespace App\Middlewares;

use App\Services\RedisService;
use App\Helpers\SessionManager;
// use Predis\Client;

class RateLimitMiddleware
{
    protected RedisService $redis;
    protected SessionManager $session;
    protected int $limit = 60;
    protected int $window = 60;

    public function __construct(RedisService $redis, SessionManager $session)
    {
        $this->redis   = $redis;
        $this->session = $session;
    }

    public function handle(array $options = []): array
    {
        $redisClient = $this->redis->get();

        $userId = $this->session->id();
        $ip     = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $identifier = $userId ? "user:$userId" : "ip:$ip";
        $scope      = $options['scope'] ?? 'global';
        $key        = "rl:$scope:$identifier";

        $count = $redisClient->incr($key);

        if ($count === 1) {
            // first request, set expiry
            $redisClient->expire($key, $this->window);
        }

        // optional: different limits for logged-in / anonymous
        $limit = $userId ? ($options['userLimit'] ?? $this->limit) : ($options['anonLimit'] ?? ($this->limit / 3));

        if ($count > $limit) {
            return $this->deny();
        }

        return $this->allow();
    }

    /* ----------------------------------
       Helpers
    -----------------------------------*/

    protected function allow(): array
    {
        return [
            'ok'       => true,
            'status'   => 200,
            'message'  => 'Authorized',
            'action'   => 'continue',
            'redirect' => null,
        ];
    }

    protected function deny(): array
    {
        return [
            'ok'       => false,
            'status'   => 429,
            'message'  => 'Too many requests',
            'action'   => 'json',
            'redirect' => null,
        ];
    }
}
