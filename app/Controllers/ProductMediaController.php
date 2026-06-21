<?php

namespace App\Controllers;

use App\Helpers\SessionManager;
use App\Helpers\Validator;
use App\Helpers\ResponseManager;
use App\Services\ProductMediaService;

use PDO;

class ProductMediaController 
{
    protected SessionManager $session;
    protected Validator $validator;
    protected ResponseManager $response;
    protected ProductMediaService $service;

    public function __construct(SessionManager $session, Validator $validator, ResponseManager $response, ProductMediaService $service)
    {
        // Required helpers for this controller class
        $this->session   = $session;
        $this->validator = $validator;
        $this->response  = $response;
        // Required services for this controller class
        $this->service = $service;
    }

    public function findAll(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id' => ['required', 'number'],
            ]);

            $result = $this->service->findAll($payload['id']);
            return $this->response->flash($result);
        } 
        catch (ValidationException $e) {
            return $this->response->flash([
                'ok'      => false,
                'status'  => $e->status,
                'message' => $e->getMessage(),
                'data'    => null,
                'error'   => $e->errors
            ]);
        }
    }

    public function findOne(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload,  [
                'id' => ['required', 'number'],
            ]);

            $result = $this->service->findOne($payload['id']);
            return $this->response->flash($result);
        } 
        catch (ValidationException $e) {
            return $this->response->flash([
                'ok'      => false,
                'status'  => $e->status,
                'message' => $e->getMessage(),
                'data'    => null,
                'error'   => $e->errors
            ]);
        }
    }

    public function update(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id'  => ['required', 'number'],
                'url' => ['required', 'string'],
            ]);
            
            $result = $this->service->update($payload['id'], $payload['url']);
            return $this->response->flash($result);
        } 
        catch (ValidationException $e) {
            return $this->response->flash([
                'ok'      => false,
                'status'  => $e->status,
                'message' => $e->getMessage(),
                'data'    => null,
                'error'   => $e->errors
            ]);
        }
    }

    public function deleteAll(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [ 
                'id' => ['required', 'number'],
            ]);

            $result = $this->service->deleteAll($payload['id']);
            return $this->response->flash($result);
        } 
        catch (ValidationException $e) {
            return $this->response->flash([
                'ok'      => false,
                'status'  => $e->status,
                'message' => $e->getMessage(),
                'data'    => null,
                'error'   => $e->errors
            ]);
        }
    }

    public function deleteOne(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id' => ['required', 'number'],
            ]);

            $result = $this->service->deleteOne($payload['id']);
            return $this->response->flash($result);
        } 
        catch (ValidationException $e) {
            return $this->response->flash([
                'ok'      => false,
                'status'  => $e->status,
                'message' => $e->getMessage(),
                'data'    => null,
                'error'   => $e->errors
            ]);
        }
    }

    public function deleteBulk(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'urls' => ['required', 'array'],
            ]);

            $result = $this->service->deleteBulk($payload['urls']);
            return $this->response->flash($result);
        } 
        catch (ValidationException $e) {
            return $this->response->flash([
                'ok'      => false,
                'status'  => $e->status,
                'message' => $e->getMessage(),
                'data'    => null,
                'error'   => $e->errors
            ]);
        }
    }
}
