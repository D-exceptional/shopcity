<?php

namespace App\Controllers;

use App\Helpers\SessionManager;
use App\Helpers\Validator;
use App\Helpers\ResponseManager;
use App\Services\UserService;

class UserController 
{
    protected SessionManager $session;
    protected Validator $validator;
    protected ResponseManager $response;
    protected UserService $service;

    public function __construct(SessionManager $session, Validator $validator, ResponseManager $response, UserService $service)
    {
        // Required helpers for this controller class
        $this->session   = $session;
        $this->validator = $validator;
        $this->response  = $response;
        // Required services for this controller class
        $this->service = $service;
    }

    public function register(array $payload)
    {
        try {
            // 1. Validation rules
            $this->validator->validate($payload, [
                'avatar'    => ['required', 'string'],
                'firstname' => ['required', 'string'],
                'lastname'  => ['required', 'string'],
                'email'     => ['required', 'email'], // ✅ email format
                'contact'   => ['required', 'string', 'min:7'],
                'country'   => ['required', 'string'],
                'password'  => ['required', 'string', 'min:6'], // ✅ enforce password length
                'role'      => ['required', 'string'], // ✅ whitelist roles
                'code'      => ['required', 'string'],
                'abbr'      => ['required', 'string'],
                'creator'   => ['required', 'string'], // System/Admin
                'currency'  => ['required', 'string'],
                'state'     => ['required', 'string'],
            ]);

            $result = $this->service->register($payload);
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

    public function login(array $payload) 
    {
        try {
            // Validate inputs
            $this->validator->validate($payload, [
                'email'    => ['required', 'string'],
                'password' => ['required', 'string'],
            ]);

            $result = $this->service->login($payload);
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

    public function otp(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload,  [
                'email' => ['required', 'string']
            ]);

            $result = $this->service->sendOtp($payload['email']);
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

    public function reset(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload,  [
                'email'    => ['required', 'string'],
                'password' => ['required', 'string'],
                'otp'      => ['required', 'number'],
            ]);

            $result = $this->service->reset($payload);
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
            $this->validator->validate($payload,[
                'firstname' => ['required', 'string'],
                'lastname'  => ['required', 'string'],
                'contact'   => ['required', 'string'],
            ]);

            $userId = $this->session->id();
            $result = $this->service->update($payload, $userId);
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

    public function social(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'facebook'  => ['required', 'string'],
                'instagram' => ['required', 'string'],
                'tiktok'    => ['required', 'string'],
                'twitter'   => ['required', 'string'],
            ]);

            $userId = $this->session->id();
            $result = $this->service->social($payload, $userId);
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

    /** Update profile image */
    public function profile(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'avatar' => ['required', 'string'],
            ]);

            $userId = $this->session->id();
            $result = $this->service->profile($payload['avatar'], $userId);
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

    /** Update profile image */
    public function password(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'password' => ['required', 'string'],
                'repassword' => ['required', 'string'],
            ]);

            $userId = $this->session->id();
            $result = $this->service->password($payload, $userId);
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

    public function status(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'status' => ['required', 'string'],
                'id'     => ['required', 'number'],
            ]);

            $result = $this->service->status($payload);
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

    public function billing(array $payload)
    { 
        try {
            // Validate data
            $this->validator->validate($payload, [
                'address' => ['required', 'string'],
                'city'    => ['required', 'string'],
                'code'    => ['required', 'number']
            ]);

            $userId = $this->session->id();
            $result = $this->service->billing($payload, $userId);
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

    public function logout(array $payload)
    {
        $role = $this->session->user()['role'] ?? 'customer';
        $result = $this->service->logout($role);
        return $this->response->flash($result);
    }

    public function contact(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'name'     => ['required', 'string'],
                'email'    => ['required', 'email'],
                'contact'  => ['required', 'string'],
                'country'  => ['required', 'string'],
                'subject'  => ['required', 'string'],
                'message'  => ['required', 'string'],
                'code'     => ['required', 'string'],
            ]);

            $result = $this->service->contact($payload);
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

    public function subscribe(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'token'     => ['required', 'string'],
                'device_id' => ['required', 'string'],
            ]);

            // Get details
            $user = $this->session->user();
            $userId = $user['id'];
            $userType = ucfirst($user['role']);

            $result = $this->service->subscribe($payload, $userId, $userType);
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

    public function unsubscribe(array $payload)
    {
        try {
            // Validate data
            $this->validator->validate($payload, [
                'token'     => ['required', 'string'],
                'device_id' => ['required', 'string'],
            ]);

            $result = $this->service->unsubscribe($payload['token'], $payload['device_id']);
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

    public function fetch(array $payload)
    { 
        try {
            // Validate data
            $this->validator->validate($payload, [
                'role' => ['required', 'string'],
                'page' => ['required', 'number']
            ]);

            $result = $this->service->fetch($payload['role'], $payload['page']);
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
    
    public function id(array $payload)
    { 
        try {
            // Validate data
            $this->validator->validate($payload, [
                'id' => ['required', 'number'],
            ]);

            $result = $this->service->id($payload['id']);
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
