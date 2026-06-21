<?php

namespace App\Controllers;

use App\Helpers\Validator;
use App\Helpers\ResponseManager;
use App\Services\CategoryService;

class CategoryController
{
    protected Validator $validator;
    protected ResponseManager $response;
    protected CategoryService $service;

    public function __construct(Validator $validator, ResponseManager $response, CategoryService $service)
    {
        // Required helpers for this controller class
        $this->validator = $validator;
        $this->response  = $response;
        // Required services for this controller class
        $this->service   = $service;
    }

    public function all(array $payload)
    {
        $result = $this->service->all();
        return $this->response->flash($result);
    }

    public function group(array $payload)
    {
        $result = $this->service->group();
        return $this->response->flash($result);
    }

    public function create(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'category' => ['required', 'string'],
            ]);

            $result = $this->service->create($payload['category']);
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
                'name' => ['required', 'string'],
                'id'   => ['required', 'number'],
            ]);

            $result = $this->service->update($payload['name'], $payload['id']);
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

    public function delete(array $payload)
    { 
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id' => ['required', 'number'],
            ]);

            $result = $this->service->delete($payload['id']);
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

    public function count(array $payload)
    { 
        $result = $this->service->count();
        return $this->response->flash($result);
    }
    
    public function fetch(array $payload)
    { 
        try {
            // Validate data
            $this->validator->validate($payload, [
                'category' => ['required', 'string'],
            ]);

            $result = $this->service->fetch($payload['category']);
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
