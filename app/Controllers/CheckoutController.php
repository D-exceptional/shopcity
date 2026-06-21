<?php
namespace App\Controllers;

use App\Helpers\SessionManager;
use App\Helpers\Validator;
use App\Helpers\ResponseManager;
use App\Services\CheckoutService;

class CheckoutController
{
    protected SessionManager $session;
    protected Validator $validator;
    protected ResponseManager $response;
    protected CheckoutService $service;

    public function __construct(SessionManager $session, Validator $validator, ResponseManager $response, CheckoutService $service)
    {
        // Required helpers for this controller class
        $this->session   = $session;
        $this->validator = $validator;
        $this->response  = $response;
        // Required services for this controller class
        $this->service   = $service;
    }

    /** Process checkout */
    public function processCheckout(array $payload)
    { 
        try {
            // Validate data
            $this->validator->validate($payload, [
                'subtotal'    => ['required', 'number'],
                'tax'         => ['required', 'number'],
                'discount'    => ['required', 'number'],
                'shipping'    => ['required', 'number'],
                'total'       => ['required', 'number'],
                'address'     => ['required', 'string'],
                'items'       => ['required', 'array'],
            ]);

            $userId = $this->session->id();
            $result = $this->service->processCheckout($user, $payload);
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
