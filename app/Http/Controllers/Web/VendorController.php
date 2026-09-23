<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Request;
use App\Http\Response;
use App\Services\Web\VendorService;

class VendorController extends Controller
{
    public function __construct(
        protected Response $response,
        protected VendorService $service
    ) {}

    public function dashboard(Request $request): Response
    {
        $userId = $request->user()['id']; 
        $email  = $request->user()['email']; 
        $name   = $request->user()['name']; 
        $viewData = $this->service->dashboard($userId, $email, $name);
            
        return $this->response->view('vendor.dashboard', $viewData);
    }

    public function mailCompose(Request $request): Response
    {
        $email = $request->user()['email']; 
        $name  = $request->user()['name']; 
        $viewData = $this->service->mailCompose($email, $name);

        return $this->response->view('vendor.mail_compose', $viewData);
    }

    public function mailRead(Request $request): Response
    {
        $mailId = (int)$request->route('id'); 
        $email  = $request->user()['email']; 
        $name   = $request->user()['name']; 
        $viewData = $this->service->mailRead($mailId, $email, $name);

        return $this->response->view('vendor.mail_read', $viewData);
    }

    public function mailSent(Request $request): Response
    {
        $page   = (int)$request->route('page') ?? 1; 
        $email  = $request->user()['email']; 
        $name   = $request->user()['name']; 
        $viewData = $this->service->mailSent($page, $email, $name);

        return $this->response->view('vendor.mail_sent', $viewData);
    }

    public function mailBox(Request $request): Response
    {
        $page   = (int)$request->route('page') ?? 1; 
        $email  = $request->user()['email']; 
        $name   = $request->user()['name']; 
        $viewData = $this->service->mailBox($page, $email, $name);

        return $this->response->view('vendor.mailbox', $viewData);
    }

    public function notification(Request $request): Response
    {
        $userId = $request->user()['id']; 
        $viewData = $this->service->notification($userId);

        return $this->response->view('vendor.notification', $viewData);
    }

    public function profile(Request $request): Response
    {
        $userId = $request->user()['id']; 
        $viewData = $this->service->profile($userId);

        return $this->response->view('vendor.profile', $viewData);
    }

    public function storeCreate(Request $request): Response
    {
        return $this->response->view('vendor.store_create');
    }

    public function storeList(Request $request): Response
    {
        $userId = $request->user()['id']; 
        $viewData = $this->service->storeList($userId);

        return $this->response->view('vendor.store_list', $viewData);
    }

    public function wallet(Request $request): Response
    {
        $userId = $request->user()['id']; 
        $viewData = $this->service->wallet($userId);

        return $this->response->view('vendor.wallet', $viewData);
    }

    public function withdrawal(Request $request): Response
    {
        $userId = $request->user()['id']; 
        $viewData = $this->service->withdrawal($userId);

        return $this->response->view('vendor.withdrawal', $viewData);
    }
}
