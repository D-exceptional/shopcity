<?php
namespace App\Middlewares;

use App\Helpers\SessionManager;

class AuthMiddleware
{
    protected SessionManager $session;

    public function __construct(SessionManager $session)
    {
        $this->session = $session;
    }

    /**
     * Base auth check
     */
    public function check()
    {
        if (!$this->session->validate()) {
            return $this->deny(401, 'Unauthorized access', 'redirect', '/login');
        }

        return $this->allow();
    }

    public function admin(): array
    {
        $this->check();

        if (!$this->session->authorize('admin')) {
            return $this->deny(403, 'Access denied: Admins only', 'json', '/admin');
        }

        return $this->allow();
    }

    public function vendor(): array
    {
        $this->check();

        if (!$this->session->authorize('vendor')) {
            return $this->deny(403, 'Access denied: Vendors only', 'json', '/login');
        }

        return $this->allow();
    }

    public function user(): array
    {
        $this->check();

        if (!$this->session->authorize('customer')) {
            return $this->deny(403, 'Access denied: Customers only', 'json', '/login');
        }

        return $this->allow();
    }

    public function adminOrVendor(): array
    {
        $this->check();

        $role = $this->session->role();

        if (!in_array($role, ['admin', 'vendor'], true)) {
            return $this->deny(403, 'Access denied: Admins or Vendors only', 'json', '/login');
        }

        return $this->allow();
    }

    public function customerOrVendor(): array
    {
        $this->check();
        
        $role = $this->session->role();

        if (!in_array($role, ['customer', 'vendor'], true)) {
            return $this->deny(403, 'Access denied: Customers or Vendors only', 'json', '/login');
        }

        return $this->allow();
    }

    /* ----------------------------------
       Helpers
    -----------------------------------*/

    protected function allow(): array
    {
        return [
            'ok'       => true,
            'status'   => 200,
            'message'  => 'Authorized',
            'action'   => 'continue',
            'redirect' => null,
        ];
    }

    protected function deny(int $status, string $message, string $action, string $path): array
    {
        return [
            'ok'       => false,
            'status'   => $status,
            'message'  => $message,
            'action'   => $action,
            'redirect' => $path,
        ];
    }
}
