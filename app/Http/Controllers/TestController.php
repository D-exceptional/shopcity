<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Response;
use App\Services\TestService;

class TestController extends Controller
{
    public function __construct(
        protected Response $response,
        protected TestService $service
    ) {}

    // =========================================
    // CHECK API WORKING STATUS
    // =========================================
    public function ping(): Response
    {
        $result = $this->service->ping();

        return $this->response->json($result->toArray(), $result->status());
    }

    // =========================================
    // TEST REDIS CONNECTION
    // =========================================
    public function redis(): Response
    {
        $result = $this->service->redis();

        return $this->response->json($result->toArray(), $result->status());
    }

    // =========================================
    // TEST EVENT DISPATCHER
    // =========================================
    public function event(): Response
    {
        $result = $this->service->event();

        return $this->response->json($result->toArray(), $result->status());
    }
}
