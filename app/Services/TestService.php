<?php
namespace App\Services;

use App\Helpers\ResponseManager;

class TestService
{
    protected ResponseManager $response;

    public function __construct(ResponseManager $response)
    {
        $this->response = $response;
    }

    public function ping() {
        return $this->response->success('API connected successfully!');
    }
}
