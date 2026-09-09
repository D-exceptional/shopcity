<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Request;
use App\Http\Response;

class AuthController extends Controller
{
    public function __construct(
        protected Response $response
    ) {}

    public function userRegister(Request $request): Response
    {
        $role = ucfirst($request->input('type')) ?? 'Customer';     

        return $this->response->view('auth.public.register', [
            'role' => $role
        ]);
    }

    public function userLogin(Request $request): Response
    {
        return $this->response->view('auth.public.login');
    }

    public function adminLogin(Request $request): Response
    {
        return $this->response->view('auth.admin.login');
    }

    public function adminVerify(Request $request): Response
    {
        return $this->response->view('auth.admin.verify');
    }

    public function adminUpdate(Request $request): Response
    { 
        $email = $request->input('email') ?? null;

        return $this->response->view('auth.admin.update', [
            'email' => $email
        ]);
    }
}
