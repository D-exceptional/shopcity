<?php

declare(strict_types=1);

namespace App\Http\Middlewares;

use App\Http\Request;
use App\Contracts\SessionInterface;

class GuestMiddleware
{
    public function __construct(
        protected SessionInterface $session
    ) {}

    public function handle(
        Request $request,
        callable $next,
        array $config = []
    ) {
        // Redirect authenticated users elsewhere.
        if ($this->session->validate()) {
            
            $role = $request->user()['role'] ?? 'customer';
            $path = $this->getPath($role);

            $this->session->redirect($path);
        }

        return $next($request);
    }

    private function getPath(
        string $role,
        string $action = 'login'
    ): string {

        $adminLoginLink  = '/auth/admin/login';
        $publicLoginLink = '/auth/user/login';

        $pathConfig = [
            'admin'     => $action === 'login' ? '/admin/dashboard' : $adminLoginLink,
            'vendor'    => $action === 'login' ? '/vendor/dashboard' : $publicLoginLink,
            'customer'  => $action === 'login' ? '/' : $publicLoginLink,
            'affiliate' => $action === 'login' ? '/affiliate/dashboard' : $publicLoginLink,
        ];

        return $pathConfig[$role];
    }
}