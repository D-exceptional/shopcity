<?php
namespace App\Controllers;

use App\Helpers\ResponseManager;
use App\Services\TestService;

class TestController
{
    protected ResponseManager $response;
    protected TestService $service;

    public function __construct(ResponseManager $response, TestService $service)
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
