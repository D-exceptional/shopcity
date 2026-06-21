<?php

namespace App\Controllers;

use App\Helpers\Validator;
use App\Helpers\ResponseManager;
use App\Services\LinkService;

class LinkController 
{
    protected Validator $validator;
    protected ResponseManager $response;
    protected LinkService $service;

    public function __construct(Validator $validator, ResponseManager $response, LinkService $service)
    {
        // Required helpers for this controller class
        $this->validator = $validator;
        $this->response  = $response;
        // Required services for this controller class
        $this->service = $service;
    }

    /**
     * Create a new product link
    */
    public function create(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'product' => ['required', 'number'],
                'user'    => ['required', 'number'],
                'short'   => ['required', 'string'],
                'long'    => ['required', 'string'],
                'code'    => ['required', 'string'],
                'status'  => ['required', 'string'],
            ]);

            $result = $this->service->create($payload);
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

    /**
     * Fetch all links (product_id)
    */
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

    /**
     * Fetch one link (link_id)
    */
    public function findOne(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
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

    /**
     * Update all link status (product_id)
    */
    public function updateAll(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id'     => ['required', 'number'],
                'status' => ['required', 'string'],
            ]);

            $result = $this->service->updateAll($payload['id'], $payload['status']);
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

    /**
     * Update one link status (link_id)
    */
    public function updateOne(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id'     => ['required', 'number'],
                'status' => ['required', 'string'],
            ]);

            $result = $this->service->updateOne($payload['id'], $payload['status']);
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

    /**
     * Delete all link (product_id)
    */
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

    /**
     * Delete a link (link_id)
    */
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
}
