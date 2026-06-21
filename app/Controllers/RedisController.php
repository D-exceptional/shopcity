<?php
namespace App\Controllers;

use App\Helpers\ResponseManager;
use App\Services\RedisService;

class RedisController
{
    protected ResponseManager $response;
    protected RedisService $service;

    public function __construct(ResponseManager $response, RedisService $service)
    {
        // Required helpers for this controller class
        $this->response = $response;
        // Required services for this controller class
        $this->service = $service;
    }

    public function ping() 
    {
        $result = $this->service->ping();
        return $this->response->flash($result);
    }
}
