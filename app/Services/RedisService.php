<?php
namespace App\Services;

use Predis\Client;
use App\Helpers\ResponseManager;

require_once dirname(__DIR__, 2) . '/bootstrap.php'; // Auto load files

class RedisService
{
    protected Client $client;
    protected ResponseManager $response;

    public function __construct(ResponseManager $response)
    {
        $this->response = $response;

        $this->client = new Client([
            'scheme'   => 'tls', // Redis Cloud requires TLS
            'host'     => 'redis-12199.c57.us-east-1-4.ec2.cloud.redislabs.com',
            'port'     => 12199,
            'username' => $_ENV['REDIS_USERNAME'] ?? 'default',
            'password' => $_ENV['REDIS_PASSWORD'],
            'timeout'  => 5.0,
            'ssl'      => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ]);
    }

    public function get(): Client
    {
        return $this->client;
    }

    public function ping()
    {
        try {
            $redis = $this->get();

            $ping = $redis->ping(); // PONG
            $redis->setex('test:key', 60, 'Hello Redis!');
            $value = $redis->get('test:key');
            $ttl   = $redis->ttl('test:key');

            return $this->response->success(
                'Redis connection successful',
                [
                    'ping' => $ping,
                    'value' => $value,
                    'ttl' => $ttl
                ]
            );
        } catch (\Throwable $e) {
            return $this->response->fail(
                'Redis connection failed',
                500,
                $e->getMessage()
            );
        }
    }
}
