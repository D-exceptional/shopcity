<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Request;
use App\Http\Response;
use App\Services\Api\UserService;

class UserController extends Controller
{
    public function __construct(
        protected Response $response, 
        protected UserService $service
    ) {}

    public function register(Request $request): Response
    {           
        $firstname = $request->input('firstname');
        $lastname  = $request->input('lastname');
        $email     = $request->input('email');
        $contact   = $request->input('contact');
        $country   = $request->input('country');
        $password  = $request->input('password');
        $role      = $request->input('role');
        $code      = $request->input('code');
        $abbr      = $request->input('abbr');
        $currency  = $request->input('currency');
        $state     = $request->input('state');
        $creator   = $request->input('creator');
        $file      = $request->input('file') ?? null;

        $result = $this->service->register(
            $firstname,
            $lastname,
            $email,
            $contact,
            $country,
            $password,
            $role,
            $code,
            $abbr,
            $currency,
            $state,
            $creator,
            $file
        );

        return $this->response->json($result->toArray(), $result->status());
    }

    public function login(Request $request): Response
    {
        $email    = $request->input('email');
        $password = $request->input('password');
        $result = $this->service->login($email, $password);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function otp(Request $request): Response
    {
        $email = $request->input('email');
        $result = $this->service->sendOtp($email);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function reset(Request $request): Response
    {
        $email    = $request->input('email');
        $password = $request->input('password');
        $otp      = $request->input('otp');
        $result = $this->service->reset($email, $password, $otp);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function update(Request $request): Response
    {
        $userId    = $request->user()['id'];
        $firstname = $request->input('firstname');
        $lastname  = $request->input('lastname');
        $contact   = $request->input('contact');
        $result = $this->service->update($firstname, $lastname, $contact, $userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function social(Request $request): Response
    {
        $userId    = $request->user()['id'];
        $facebook  = $request->input('facebook');
        $instagram = $request->input('instagram');
        $tiktok    = $request->input('tiktok');
        $twitter   = $request->input('twitter');
        $result = $this->service->social($facebook, $instagram, $tiktok, $twitter, $userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    /** Update profile image */
    public function profile(Request $request): Response
    {
        $userId = $request->user()['id'];
        $avatar = $request->input('avatar');
        $result = $this->service->profile($avatar, $userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    /** Update profile image */
    public function password(Request $request): Response
    {
        $userId     = $request->user()['id'];
        $password   = $request->input('password');
        $result = $this->service->password($password, $userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function status(Request $request): Response
    {
        $userId = $request->route('id');
        $status = $request->route('status');
        $result = $this->service->status($userId, $status);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function count(Request $request): Response
    { 
        $result = $this->service->count();

        return $this->response->json($result->toArray(), $result->status());
    }

    public function billing(Request $request): Response
    { 
        $userId  = $request->user()['id'];
        $address = $request->input('address');
        $city    = $request->input('city');
        $code    = $request->input('code');
        $result = $this->service->billing($address, $city, $code, $userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function logout(Request $request): Response
    {
        $role = $request->user()['role'] ?? 'customer';
        $result = $this->service->logout($role);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function contact(Request $request): Response
    {
        $name    = $request->input('name');
        $email   = $request->input('email');
        $contact = $request->input('contact');
        $country = $request->input('country');
        $subject = $request->input('subject');
        $message = $request->input('message');
        $code    = $request->input('code');

        $result = $this->service->contact(
            $name, 
            $email, 
            $contact, 
            $country, 
            $subject, 
            $message, 
            $code
        );

        return $this->response->json($result->toArray(), $result->status());
    }

    public function subscribe(Request $request): Response
    {
        $userId   = $request->user()['id'];
        $userRole = ucfirst($request->user()['role']);
        $token    = $request->input('token');
        $deviceId = $request->input('device_id');
        $result = $this->service->subscribe($token, $deviceId, $userId, $userRole);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function unsubscribe(Request $request): Response
    {
        $token    = $request->input('token');
        $deviceId = $request->input('device_id');
        $result = $this->service->unsubscribe($token, $deviceId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function fetch(Request $request): Response
    { 
        $role = $request->route('role');
        $page = $request->route('page');
        $result = $this->service->fetch($role, $page);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function delete(Request $request): Response
    {
        $userId = $request->route('id');
        $result = $this->service->delete($userId);

        return $this->response->json($result->toArray(), $result->status());
    }
    
    public function doc(Request $request): Response
    { 
        $userId = $request->route('id');
        $result = $this->service->doc($userId);

        return $this->response->json($result->toArray(), $result->status());
    }
}
