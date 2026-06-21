<?php

namespace App\Controllers;

use App\Helpers\SessionManager;
use App\Helpers\Validator;
use App\Helpers\ResponseManager;
use App\Services\MailService;

class MailController 
{
    protected SessionManager $session;
    protected Validator $validator;
    protected ResponseManager $response;
    protected MailService $service;

    public function __construct(SessionManager $session, Validator $validator, ResponseManager $response, MailService $service)
    {
        // Required helpers for this controller class
        $this->session   = $session;
        $this->validator = $validator;
        $this->response  = $response;
        // Required services for this controller class
        $this->service = $service;
    }

    // *** BASIC MAIL FEATURES ********* //
    public function countInbox(array $payload)
    {
        $email = $this->session->user()['email'];
        $result = $this->service->countInbox($email);
        return $this->response->flash($result);
    }

    public function countOutbox(array $payload)
    {
        $name = $this->session->user()['name'];
        $result = $this->service->countOutbox($name);
        return $this->response->flash($result);
    }

    public function getInbox(array $payload)
    {
        try {
            $this->validator->validate($payload, ['page'  => ['required', 'number']]);

            $email = $this->session->user()['email'];
            $result = $this->service->getInbox($email, $payload['page']);
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

    public function getOutbox(array $payload)
    {
        try {
            $this->validator->validate($payload, ['page'  => ['required', 'number']]);

            $name = $this->session->user()['name'];
            $result = $this->service->getOutbox($name, $payload['page']);
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

    public function getMail(array $payload)
    {
        try {
            $this->validator->validate($payload, ['id'  => ['required', 'number']]);

            $result = $this->service->getMail($payload['id']);
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

    public function deleteMail(array $payload)
    {
        try {
            $this->validator->validate($payload, ['id'  => ['required', 'number']]);

            $result = $this->service->deleteMail($payload['id']);
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
    // *** BASIC MAIL FEATURES END ********* //

    /**
     * Entry point — auto detect if request includes a file
     * (so you don’t need two separate routes for text/attachment)
    */
    public function sendBulk(array $payload)
    {
        $result = $this->service->sendBulk($payload);
        return $this->response->flash($result);
    }
}
