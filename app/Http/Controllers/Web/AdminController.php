<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Request;
use App\Http\Response;
use App\Services\Web\AdminService;

class AdminController extends Controller
{
    public function __construct(
        protected Response $response,
        protected AdminService $service
    ) {}

    public function dashboard(Request $request): Response
    {
        $userId = $request->user()['id']; 
        $email  = $request->user()['email']; 
        $name   = $request->user()['name']; 
        $viewData = $this->service->dashboard($userId, $email, $name);
            
        return $this->response->view('admin.dashboard', $viewData);
    }

    public function mailCompose(Request $request): Response
    {
        $email = $request->user()['email']; 
        $name  = $request->user()['name']; 
        $viewData = $this->service->mailCompose($email, $name);

        return $this->response->view('admin.mail_compose', $viewData);
    }

    public function mailRead(Request $request): Response
    {
        $mailId = (int)$request->route('id'); 
        $email  = $request->user()['email']; 
        $name   = $request->user()['name']; 
        $viewData = $this->service->mailRead($mailId, $email, $name);

        return $this->response->view('admin.mail_read', $viewData);
    }

    public function mailSent(Request $request): Response
    {
        $page   = (int)$request->route('page') ?? 1; 
        $email  = $request->user()['email']; 
        $name   = $request->user()['name']; 
        $viewData = $this->service->mailSent($page, $email, $name);

        return $this->response->view('admin.mail_sent', $viewData);
    }

    public function mailBox(Request $request): Response
    {
        $page   = (int)$request->route('page') ?? 1; 
        $email  = $request->user()['email']; 
        $name   = $request->user()['name']; 
        $viewData = $this->service->mailBox($page, $email, $name);

        return $this->response->view('admin.mailbox', $viewData);
    }

    public function notification(Request $request): Response
    {
        $userId = $request->user()['id']; 
        $viewData = $this->service->notification($userId);

        return $this->response->view('admin.notification', $viewData);
    }

    public function orderList(Request $request): Response
    {
        $status = $request->route('status') ?? 'Pending'; 
        $page   = (int)$request->route('page') ?? 1; 
        $viewData = $this->service->orderList($status, $page);

        return $this->response->view('admin.order_list', $viewData);
    }

    public function orderView(Request $request): Response
    {
        $orderId = (int)$request->route('id') ?? null; 
        $viewData = $this->service->orderView($orderId);

        return $this->response->view('admin.order_view', $viewData);
    }

    public function payouts(Request $request): Response
    {
        $status = $request->route('status') ?? 'Pending'; 
        $page   = (int)$request->route('page') ?? 1; 
        $viewData = $this->service->payouts($status, $page);

        return $this->response->view('admin.payouts', $viewData);
    }

    public function productList(Request $request): Response
    {
        $page   = (int)$request->route('page') ?? 1; 
        $viewData = $this->service->productList($page);

        return $this->response->view('admin.product_list', $viewData);
    }

    public function productView(Request $request): Response
    {
        $productId = (int)$request->route('id') ?? null; 
        $viewData = $this->service->productView($productId);

        return $this->response->view('admin.product_view', $viewData);
    }

    public function profile(Request $request): Response
    {
        $userId = $request->user()['id']; 
        $viewData = $this->service->profile($userId);

        return $this->response->view('admin.profile', $viewData);
    }

    public function storeList(Request $request): Response
    {
        $page   = (int)$request->route('page') ?? 1; 
        $viewData = $this->service->storeList($page);

        return $this->response->view('admin.store_list', $viewData);
    }

    public function users(Request $request): Response
    {
        $role   = $request->route('role') ?? 'Customer'; 
        $page   = (int)$request->route('page') ?? 1; 
        $viewData = $this->service->users($role, $page);

        return $this->response->view('admin.users', $viewData);
    }
}
