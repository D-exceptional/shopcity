<?php
namespace App\Controllers;

use App\Helpers\SessionManager;
use App\Helpers\Validator;
use App\Helpers\ResponseManager;
use App\Services\OrderService;

class OrderController 
{
    protected SessionManager $session;
    protected Validator $validator;
    protected ResponseManager $response;
    protected OrderService $service;

    public function __construct(SessionManager $session, Validator $validator, ResponseManager $response, OrderService $service)
    {
        // Required helpers for this controller class
        $this->session   = $session;
        $this->validator = $validator;
        $this->response  = $response;
        // Required services for this controller class
        $this->service = $service; 
    }

    /** Get a single order */
    public function trackOrder(array $payload)
    {  
        try {
            // Validate data
            $this->validator->validate($payload, [
                'code' => ['required', 'string'],
            ]);

            $userId = $this->session->id();
            $result = $this->service->trackOrder($userId, $payload['code']);
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

    /** Get a single order */
    public function getOrder(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id' => ['required', 'number'],
            ]);

            $result = $this->service->getOrder($payload['id']);
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

    /** Get all orders (admin) */
    public function getAllOrders(array $payload)
    { 
        try {
            // Validate data
            $this->validator->validate($payload, [
                'page' => ['required', 'number'],
            ]);

            $result = $this->service->getAllOrders($payload['page']);
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

     /** Get all orders (admin) */
    public function getOrdersByStatus(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'status' => ['required', 'string'],
                'page'   => ['required', 'number'],
            ]);

            $result = $this->service->getOrdersByStatus($payload['status'], $payload['page']);
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

    /** Get all orders for a user */
    public function getUserOrders(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'page' => ['required', 'number'],
            ]);

            $userId = $this->session->id();
            $result = $this->service->getUserOrders($userId, $payload['page']);
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

    /** Get all orders for a store */
    public function getStoreOrders(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id'   => ['required', 'number'],
                'page' => ['required', 'number'],
            ]);

            $result = $this->service->getStoreOrders($payload['id'], $payload['page']);
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

     /** Get all orders for a store */
    public function getStoreOrdersByStatus(array $payload)
    { 
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id'     => ['required', 'number'],
                'status' => ['required', 'string'],
                'page'   => ['required', 'number'],
            ]);

            $result = $this->service->getStoreOrdersByStatus($payload['id'], $payload['status'], $payload['page']);
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

    /** Update item status */
    public function updateItemStatus(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id'     => ['required', 'number'],
                'status' => ['required', 'string'],
            ]);

            $result = $this->service->updateItemStatus($payload);
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

    /** Complete order */
    public function completeOrder(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id' => ['required', 'number'],
            ]);

            $result = $this->service->completeOrder($payload);
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

    /** Cancel order */
    public function cancelOrder(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id' => ['required', 'number'],
            ]);

            $user = $this->session->user();
            $result = $this->service->cancelOrder($user, $payload);
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

    /** Get a single order */
    public function getSalesSummary(array $payload)
    {  
        try {
            // Validate data
            $this->validator->validate($payload, [
                'view'    => ['required', 'string'],
                'period'  => ['required', 'string'],
                'start'   => ['required', 'string'],
                'end'     => ['required', 'string'],
            ]);

            // Collect query params
            $view       = $payload['view'] ?? 'vendor';
            $storeId    = isset($payload['storeId']) && is_numeric($payload['storeId']) ? (int)$payload['storeId'] : null;
            $period     = $payload['period'] ?? 'today';
            $startDate  = $payload['start'] ?? null;
            $endDate    = $payload['end'] ?? null;

            // Get sales summary
            $userId = $this->session->id();
            $result = $this->service->getSalesSummary($view, $userId, $storeId, $period, $startDate, $endDate);
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
