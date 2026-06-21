<?php

namespace App\Controllers;

use App\Helpers\SessionManager;
use App\Helpers\Validator;
use App\Helpers\ResponseManager;
use App\Services\WishlistService;

class WishlistController 
{
    protected SessionManager $session;
    protected Validator $validator;
    protected ResponseManager $response;
    protected WishlistService $service;

    public function __construct(SessionManager $session, Validator $validator, ResponseManager $response, WishlistService $service)
    {
        // Required helpers for this controller class
        $this->session   = $session;
        $this->validator = $validator;
        $this->response  = $response;
        // Required services for this controller class
        $this->service = $service;
    }

    public function view(array $payload)
    { 
        $userId = $this->session->id();
        $result = $this->service->view($userId);
        return $this->response->flash($result);
    }

    public function add(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'productId' => ['required', 'number'],
            ]);

            $userId = $this->session->id();
            $result = $this->service->add($userId, $payload['productId']);
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

    public function merge(array $payload)
    {
        try {
            // Validate the incoming data
            $this->validator->validate($payload, [
                'wishlist' => ['required', 'array'],
            ]);

            $userId = $this->session->id();
            $result = $this->service->merge($userId, $payload['wishlist']);
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

    public function remove(array $payload)
    { 
        try {
            // Validate data
            $this->validator->validate($payload, [
                'productId' => ['required', 'number'],
            ]);

            $userId = $this->session->id();
            $result = $this->service->remove($userId, $payload['productId']);
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

    public function clear()
    { 
        $userId = $this->session->id();
        $result = $this->service->clear($userId);
        return $this->response->flash($result);
    }
}
