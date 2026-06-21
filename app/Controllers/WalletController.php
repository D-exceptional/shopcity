<?php
namespace App\Controllers;

use App\Helpers\SessionManager;
use App\Helpers\Validator;
use App\Helpers\ResponseManager;
use App\Services\WalletService;

class WalletController 
{
    protected SessionManager $session;
    protected Validator $validator;
    protected ResponseManager $response;
    protected WalletService $service;

    public function __construct(SessionManager $session, Validator $validator, ResponseManager $response, WalletService $service)
    {
        // Required helpers for this controller class
        $this->session   = $session;
        $this->validator = $validator;
        $this->response  = $response;
        // Required services for this controller class
        $this->service = $service;
    }

    /** Initialize payment */
    public function updateDetails(array $payload)
    { 
        try {
            // Validate data
            $this->validator->validate($payload, [
                'account' => ['required', 'number'],
                'bank'    => ['required', 'string'],
                'code'    => ['required', 'string'],
            ]);

            $userId = $this->session->id();
            $result = $this->service->updateDetails($payload['account'], $payload['bank'], $payload['code'], $userId);
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

    /** Initialize payment */
    public function createPayment(array $payload)
    { 
        try {
            // Validate data
            $this->validator->validate($payload, [
                'amount' => ['required', 'number']
            ]);

            $userId = $this->session->id();
            $result = $this->service->createPayment($payload['amount'], $userId);
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

    /** Redeem funds to main wallet */
    public function redeemFunds(array $payload)
    { 
        try {
            // Validate data
            $this->validator->validate($payload, [
                'itemId'  => ['required', 'number'],
                'storeId' => ['required', 'number'],
                'status'  => ['required', 'string'],
            ]);

            $result = $this->service->redeemFunds($payload);
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

    /** Request withdrawal */
    public function requestFunds(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'amount'    => ['required', 'number'],
                'bank'      => ['required', 'string'],
                'account'   => ['required', 'number'],
                'narration' => ['required', 'string'],
            ]);

            $user = $this->session->user();
            $result = $this->service->requestFunds($payload, $user);
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

    /** 🔹 Flutterwave - Single Transfer */
    public function singleTransfer(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'bank'      => ['required', 'string'], 
                'account'   => ['required', 'string'],
                'amount'    => ['required', 'number'],
                'narration' => ['required', 'string'],
                'currency'  => ['required', 'string'], 
                'reference' => ['required', 'string']  
            ]);

            $result = $this->service->singleTransfer($payload);
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

    /** 🔹 Flutterwave - Bulk Transfer */
    public function bulkTransfer(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'title'     => ['required', 'string'],
                'bulk_data' => ['required', 'array'] 
            ]);

            $result = $this->service->bulkTransfer($payload);
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

    /** Get payment by reference */
    public function getByReference(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'type'      => ['required', 'string'], 
                'reference' => ['required', 'string']
            ]);

            $result = $this->service->getByReference($payload);
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

    /** Get payment by user */
    public function getPaymentsByUser(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'type' => ['required', 'string'], // database table name
                'page' => ['required', 'number']
            ]); 

            $userId = $this->session->id();
            $result = $this->service->getPaymentsByUser($payload, $userId);
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

    /** Fetch payments by type (withdrawals, payments, topups) -> Admin */
    public function getPaymentsByType(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'table' => ['required', 'string'], 
                'page'  => ['required', 'number']
            ]);

            $result = $this->service->getPaymentsByType($payload);
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

    /** Fetch payments by status (Pending, Completed, Failed) -> Admin */
    public function getPaymentsByStatus(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'table'  => ['required', 'string'], // database table name
                'column' => ['required', 'string'],
                'status' => ['required', 'string'], 
                'page'   => ['required', 'number']
            ]);

            $result = $this->service->getPaymentsByStatus($payload);
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

    /** Fetch payments by status (Pending, Completed, Failed) -> Admin */
    public function getPayoutsByStatus(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'status' => ['required', 'string'], 
                'page'   => ['required', 'number']
            ]);

            $result = $this->service->getPayoutsByStatus($payload);
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

    /** Verify payment or transfer with Flutterwave */
    public function verifyPayment(array $payload)
    {
        try {
            $this->validator->validate($payload, [
                'id'        => ['required', 'number'],
                'reference' => ['required', 'string'],
            ]);

            $result = $this->service->verifyPayment($payload);
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
