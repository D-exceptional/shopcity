<?php
namespace App\Services;

use App\Helpers\ResponseManager;

class RateLimiterService
{
    protected ResponseManager $response;
    protected int $maxAttempts = 60;
    protected int $window = 60; // seconds

    public function __construct(ResponseManager $response)
    {
        $this->response = $response;
    }

    public function check(?int $userId, string $key, array $options): array
    {
        $now = time();

        $success = false;
        $data = apcu_fetch($key, $success);

        if (!$success || $now > $data['reset_at']) {
            apcu_store($key, [
                'attempts' => 1,
                'reset_at' => $now + $this->window
            ], $this->window);

            return $this->allow();
        }

        // optional: different limits for logged-in / anonymous
        $limit = !is_null($userId) ? ($options['userLimit'] ?? $this->maxAttempts) : ($options['anonLimit'] ?? ($this->maxAttempts / 3));

        if ($data['attempts'] >= $limit) {
            $remainingSeconds = max(0, $data['reset_at'] - $now);

            $minutes = intdiv($remainingSeconds, 60);
            $seconds = $remainingSeconds % 60;

            $message = $minutes > 0
                ? "Rate limit exceeded. Try again in {$minutes} minute" . ($minutes > 1 ? 's' : '') .
                ($seconds > 0 ? " {$seconds} second" . ($seconds > 1 ? 's' : '') : '')
                : "Rate limit exceeded. Try again in {$seconds} second" . ($seconds > 1 ? 's' : '');

            return $this->deny($message, $remainingSeconds);
        }

        $data['attempts']++;
        apcu_store($key, $data, $data['reset_at'] - $now);

        return $this->allow();
    }

    protected function allow(): array
    {
        return [
            'ok'       => true,
            'status'   => 200,
            'message'  => 'Limit validated',
            'action'   => 'continue',
            'redirect' => null,
        ];
    }

    protected function deny(string $message, int $retry): array
    {
        return [
            'ok'       => false,
            'status'   => 429,
            'message'  => $message,
            'action'   => 'json',
            'redirect' => null,
            'retry'    => $retry,
        ];
    }
}
