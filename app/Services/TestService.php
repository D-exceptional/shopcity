<?php

declare(strict_types=1);

namespace App\Services;

use Predis\Client;

use App\Core\Result;
use App\Core\Redis;
use App\Events\User\UserRegistered;
use App\Events\User\ProfileUpdated;
use App\Events\EventDispatcher;

class TestService
{
    public function __construct(
        protected Result $result,
        protected Client $redisClient,
        protected Redis $redisManager,
        protected EventDispatcher $eventDispatcher,
    ) {
        $this->redisClient = $this->redisManager->cache();
    }

    // =========================================
    // CHECK API WORKING STATUS
    // =========================================
    public function ping(): Result 
    {
        return $this->result->success('API connected successfully');
    }

    // =========================================
    // TEST REDIS CONNECTION
    // =========================================
    public function redis(): Result
    {
        try {

            $ping = (string) $this->redisClient->ping(); // PONG
            $this->redisClient->setex('test:key', 60, 'Hello Redis!');
            $value = $this->redisClient->get('test:key');
            $ttl   = $this->redisClient->ttl('test:key');

            return $this->result->success(
                'Redis connection successful',
                [
                    'ping' => $ping,
                    'value' => $value,
                    'ttl' => $ttl
                ]
            );

        } catch (\Throwable $e) {
            
            return $this->result->error(
                'Redis connection failed',
                500,
                $e->getMessage()
            );
        }
    }

    // =========================================
    // TEST EVENT DISPATCHER
    // =========================================
    public function event(): Result
    {
        try {

            // Mail Event Check
            /*
            $this->eventDispatcher->dispatch(
                new UserRegistered(
                    fullName: 'Test User',
                    email: 'test@example.com',
                    contact: '08000000000',
                    membership: 'Doctor',
                    reference: null,
                )
            );

            return $this->result->success(
                'Event dispatched successfully',
                [
                    'message' => 'Event dispatched successfully.',
                    'result' => 'Check your email for the registration notification.'
                ]
            );
            */

            // Cloudinary Event Check
            $this->eventDispatcher->dispatch(
                new ProfileUpdated(
                    oldAvatar: 'https://res.cloudinary.com/da1kbkchq/image/upload/v1752449550/i1suewpc0cptq6umn2sy.jpg',
                    newAvatar: 'https://res.cloudinary.com/dxlwyzwa0/image/upload/v1787100352/ghlxsjjai5i7q9kb9lwo.png',
                )
            );

            return $this->result->success(
                'Event dispatched successfully',
                [
                    'message' => 'Event dispatched successfully.',
                    'result' => 'Cloudinary job is processing in the background'
                ]
            );

        } catch (\Throwable $e) {

            return $this->result->error(
                'Event dispatching failed',
                500,
                $e->getMessage()
            );
        }
    }
}
