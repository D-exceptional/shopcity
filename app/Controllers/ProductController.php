<?php

namespace App\Controllers;

use App\Helpers\SessionManager;
use App\Helpers\Validator;
use App\Helpers\ResponseManager;
use App\Services\ProductService;

class ProductController 
{
    protected SessionManager $session;
    protected Validator $validator;
    protected ResponseManager $response;
    protected ProductService $service;

    public function __construct(SessionManager $session, Validator $validator, ResponseManager $response, ProductService $service)
    {
        // Required helpers for this controller class
        $this->session   = $session;
        $this->validator = $validator;
        $this->response  = $response;
        // Required services for this controller class
        $this->service = $service;
    }

    // ---------------- PRODUCT CRUD ----------------
    public function create(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'name'        => ['required', 'string'],
                'description' => ['required', 'string'],
                'category'    => ['required', 'string'],
                'sub'         => ['required', 'string'],
                'price'       => ['required', 'number'],
                'slash'       => ['required', 'number'],
                'stock'       => ['required', 'number'],
                'color'       => ['required', 'string'],
                'id'          => ['required', 'number'],
                'media'       => ['required', 'array'],
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

    public function update(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'name'        => ['required', 'string'],
                'description' => ['required', 'string'],
                'category'    => ['required', 'string'],
                'subcategory' => ['required', 'string'],
                'price'       => ['required', 'number'],
                'slash'       => ['required', 'number'],
                'stock'       => ['required', 'number'],
                'color'       => ['required', 'string'],
                'visibility'  => ['required', 'string'],
                'reselling'   => ['required', 'string'],
                'commission'  => ['required', 'number'],
                'id'          => ['required', 'number'],
            ]);

            $result = $this->service->update($payload);
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

    // ---------------- FETCH METHODS ----------------
    public function findByAll(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'page'  => ['required', 'number'],
                'total' => ['required', 'number'],
                'view'  => ['required', 'string'],
            ]);

            $result = $this->service->findByAll($payload);
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

    public function findByCategory(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'category' => ['required', 'string'],
                'page'     => ['required', 'number'],
                'total'    => ['required', 'number'],
                'view'     => ['required', 'string'],
            ]);

            $result = $this->service->findByCategory($payload);
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

    public function findByStore(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id'    => ['required', 'number'],
                'page'  => ['required', 'number'],
                'total' => ['required', 'number'],
                'view'  => ['required', 'string'],
            ]);

            $result = $this->service->findByStore($payload);
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

    public function findByStoreCategory(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id'       => ['required', 'number'],
                'category' => ['required', 'string'],
                'page'     => ['required', 'number'],
                'total'    => ['required', 'number'],
                'view'     => ['required', 'string'],
            ]);

            $result = $this->service->findByStoreCategory($payload);
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

    public function findNewArrivals(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'page'  => ['required', 'number'],
                'total' => ['required', 'number'],
                'view'  => ['required', 'string'],
            ]);

            $result = $this->service->findNewArrivals($payload);
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

    public function findFeatured(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id'    => ['required', 'number'],
                'page'  => ['required', 'number'],
                'total' => ['required', 'number'],
                'view'  => ['required', 'string'],
            ]);

            $result = $this->service->findFeatured($payload);
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

    public function findTopSelling(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'page'  => ['required', 'number'],
                'total' => ['required', 'number'],
            ]);

            $result = $this->service->findTopSelling($payload);
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

    public function findByPriceRange(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'min'   => ['required', 'number'],
                'max'   => ['required', 'number'],
                'page'  => ['required', 'number'],
                'total' => ['required', 'number'],
                'view'  => ['required', 'string'],
            ]);

            $result = $this->service->findByPriceRange($payload);
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

    public function findByMinPrice(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'min'   => ['required', 'number'],
                'page'  => ['required', 'number'],
                'total' => ['required', 'number'],
                'view'  => ['required', 'string'],
            ]);

            $result = $this->service->findByMinPrice($payload);
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

    public function findByMaxPrice(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'max'   => ['required', 'number'],
                'page'  => ['required', 'number'],
                'total' => ['required', 'number'],
                'view'  => ['required', 'string'],
            ]);

            $result = $this->service->findByMaxPrice($payload);
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

    public function findByGroupedCategory(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'page'  => ['required', 'number'],
                'total' => ['required', 'number'],
            ]);

            $result = $this->service->findByGroupedCategory($payload);
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

    public function findBySearch(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'search' => ['required', 'string'],
                'page'   => ['required', 'number'],
                'total' => ['required', 'number'],
                'view'  => ['required', 'string'],
            ]);

            $result = $this->service->findBySearch($payload);
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

    public function findByColor(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'color'  => ['required', 'string'],
                'page'   => ['required', 'number'],
                'total' => ['required', 'number'],
                'view'  => ['required', 'string'],
            ]);

            $result = $this->service->findByColor($payload);
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

    public function addReview(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'productId' => ['required', 'number'],
                'review'    => ['required', 'string'],
                'rating'    => ['required', 'number'],
            ]);

            $userId = $this->session->id();
            $result = $this->service->addReview($userId, $payload);
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
