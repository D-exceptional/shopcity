<?php

namespace App\Controllers;

use App\Helpers\SessionManager;
use App\Helpers\Validator;
use App\Helpers\ResponseManager;
use App\Services\NotificationService;

class NotificationController 
{
    protected SessionManager $session;
    protected Validator $validator;
    protected ResponseManager $response;
    protected NotificationService $service;

    public function __construct(SessionManager $session, Validator $validator, ResponseManager $response, NotificationService $service)
    {
        // Required helpers for this controller class
        $this->session   = $session;
        $this->validator = $validator;
        $this->response  = $response;
        // Required services for this controller class
        $this->service   = $service;
    }

    /**
     * Create a new notification
     */
    public function create(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'details'   => ['required', 'string'],
                'type'      => ['required', 'string'],
                'receiver'  => ['required', 'int'],
                'date'      => ['required', 'string'],
                'status'    => ['required', 'string']
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
     * Count all notifications
    */
    public function countAll()
    {
        $result = $this->service->countAll();
        return $this->response->flash($result);
    }

    /**
     * Count notifications by id
     */
    public function countAllById(array $payload)
    {
        $userId = $this->session->id();
        $result = $this->service->countAllById($userId);
        return $this->response->flash($result);
    }

    /**
     * Count unread notifications by id
    */
    public function countUnreadById(array $payload)
    {
        $userId = $this->session->id();
        $result = $this->service->countUnreadById($userId);
        return $this->response->flash($result);
    }

    /**
     * Get unread notifications
     */
    public function getUnread(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'limit' => ['number'],
                'offset'=> ['number'],
            ]);

            $limit  = $payload['limit']  ?? 20;
            $offset = $payload['offset'] ?? 0;

            $userId = $this->session->id();
            $result = $this->service->getUnreadById($userId, $limit, $offset);
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
     * Fetch notifications by id
    */
    public function fetchById(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'limit' => ['number'],
                'offset'=> ['number'],
            ]);

            $limit  = $payload['limit']  ?? 20;
            $offset = $payload['offset'] ?? 0;

            $userId = $this->session->id();
            $result = $this->service->fetchById($userId, $limit, $offset);
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
     * Mark a notification as read
     */
    public function markAsRead(array $payload)
    {
        $userId = $this->session->id();
        $result = $this->service->markAsRead($userId);
        return $this->response->flash($result);
    }
}
