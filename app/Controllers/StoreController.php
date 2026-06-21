<?php

namespace App\Controllers;

use App\Helpers\SessionManager;
use App\Helpers\Validator;
use App\Helpers\ResponseManager;
use App\Services\StoreService;

class StoreController 
{
    protected SessionManager $session;
    protected Validator $validator;
    protected ResponseManager $response;
    protected StoreService $service;

    public function __construct(SessionManager $session, Validator $validator, ResponseManager $response, StoreService $service)
    {
        // Required helpers for this controller class
        $this->session   = $session;
        $this->validator = $validator;
        $this->response  = $response;
        // Required services for this controller class
        $this->service = $service;
    }

    /**
     * Create a new store
    */
    public function createStore(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'name'        => ['required', 'string'],
                'avatar'      => ['required', 'string'],
                'description' => ['required', 'string'],
                'narration'   => ['required', 'string'],
                'delivery'    => ['required', 'string'],
                'facebook'    => ['required', 'string'],
                'instagram'   => ['required', 'string'],
                'tiktok'      => ['required', 'string'],
                'twitter'     => ['required', 'string'],
            ]);

            $userId = $this->session->id();
            $result = $this->service->createStore($userId, $payload);
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
     * Update store details
    */
    public function updateStoreDetails(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'name'        => ['required', 'string'],
                'description' => ['required', 'string'],
                'delivery'    => ['required', 'string'],
                'id'          => ['required', 'number']
            ]);

            $result = $this->service->updateStoreDetails($payload);
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
     * Update store socials
    */
    public function updateStoreSocials(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'facebook'    => ['required', 'string'],
                'instagram'   => ['required', 'string'],
                'tiktok'      => ['required', 'string'],
                'twitter'     => ['required', 'string'],
                'id'          => ['required', 'number']
            ]);

            $result = $this->service->updateStoreSocials($payload);
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
     * Update store avatar
    */
    public function updateStoreAvatar(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id'  => ['required', 'number'],
                'url' => ['required', 'string'],
            ]);

            $result = $this->service->updateStoreAvatar($payload['id'], $payload['url']);
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
     * Update store status
    */
    public function updateStoreStatus(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'status' => ['required', 'string'],
                'id'     => ['required', 'number'],
            ]);

            $result = $this->service->updateStoreStatus($payload);
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
     * Delete store
    */
    public function deleteStore(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id' => ['required', 'number'],
            ]);

            $result = $this->service->deleteStore($payload['id']);
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
     * Find a store
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
     * Find stores by status
    */
    public function findByStatus(array $payload)
    { 
        try {
            // Validate data
            $this->validator->validate($payload, [
                'status' => ['required', 'string'],
                'page'   => ['required', 'number'],
            ]);

            $result = $this->service->findByStatus($payload['status'], $payload['page']);
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
     * Find stores by users
    */
    public function findByUser(array $payload)
    { 
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id'   => ['required', 'number'],
                'page' => ['required', 'number'],
            ]);

            $result = $this->service->findByUser($payload['id'], $payload['page']);
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
     * Create coupon
    */
    public function createCoupon(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'code'     => ['required', 'string'],
                'discount' => ['required', 'number'],
                'storeId'  => ['required', 'number']
            ]);

            $result = $this->service->createCoupon($payload['code'], $payload['discount'], $payload['storeId']);
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
     * Create coupon
    */
    public function findCoupon(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'coupon'  => ['required', 'string'],
                'storeId' => ['required', 'number'],
            ]);

            $result = $this->service->findCoupon($payload['coupon'], $payload['storeId']);
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
     * Update coupon
    */
    public function updateCoupon(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'code'     => ['required', 'string'],
                'discount' => ['required', 'number'],
                'status'   => ['required', 'string'],
                'couponId' => ['required', 'number'],
            ]);

            $result = $this->service->updateCoupon($payload['code'], $payload['discount'], $payload['status'], $payload['couponId']);
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
     * Delete a coupon
    */
    public function deleteSingleCoupon(array $payload)
    { 
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id'  => ['required', 'number']
            ]);

            $result = $this->service->deleteSingleCoupon($payload['id']);
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
     * Delete store coupons
    */
    public function deleteCouponByStore(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id'  => ['required', 'number']
            ]);

            $result = $this->service->deleteCouponByStore($payload['id']);
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
     * Find store coupons
    */
    public function findCouponsByStore(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id'    => ['required', 'number'],
                'page'  => ['required', 'number']
            ]);

            $result = $this->service->findCouponsByStore($payload['id'], $payload['page']);
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
     * Find coupons by store & status
    */
    public function findCouponsByStoreAndStatus(array $payload)
    { 
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id'     => ['required', 'number'],
                'status' => ['required', 'string'],
                'page'   => ['required', 'number']
            ]);

            $result = $this->service->findCouponsByStoreAndStatus($payload['id'], $payload['status'], $payload['page']);
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

    /** Coun stores by status (Pending/Active/Deactivated) */
    public function countStoresByStatus(array $payload)
    { 
        $result = $this->service->countStoresByStatus();
        return $this->response->flash($result);
    }

    /**
     * Find customers by store 
    */
    public function findStoreCustomers(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id'   => ['required', 'number'],
                'type' => ['required', 'string'],
                'page' => ['required', 'number']
            ]);

            $result = $this->service->findStoreCustomers($payload['id'], $payload['type'], $payload['page']);
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
