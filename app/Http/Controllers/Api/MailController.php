<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Request;
use App\Http\Response;
use App\Services\Api\MailService;

class MailController extends Controller
{
    public function __construct(
        protected Response $response, 
        protected MailService $service
    ) {}

    public function countMessages(Request $request): Response
    {
        $email = $request->user()['email'];
        $name  = $request->user()['name'];
        $result = $this->service->countMessages($email, $name);
        
        return $this->response->json($result->toArray(), $result->status());
    }

    public function getInbox(Request $request): Response
    {
        $email = $request->user()['email'];
        $page  = $request->route('page');
        $result = $this->service->getInbox($email, $page);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function getOutbox(Request $request): Response
    {
        $name  = $request->user()['name'];
        $page  = $request->route('page');
        $result = $this->service->getOutbox($name, $page);

        return $this->response->json($result->toArray(), $result->status());
        
    }

    public function getMail(Request $request): Response
    {
        $mailId = $request->route('id');
        $result = $this->service->getMail($mailId);

        return $this->response->json($result->toArray(), $result->status());
        
    }

    public function deleteMail(Request $request): Response
    {
        $mailId = $request->route('id');
        $result = $this->service->deleteMail($mailId);

        return $this->response->json($result->toArray(), $result->status());
        
    }

    /**
     * Entry point — auto detect if request includes a file
     * (so you don’t need two separate routes for text/attachment)
    */
    public function sendBulk(Request $request): Response
    {
        $recipients    = $request->input('recipients');
        $subject       = $request->input('subject');
        $message       = $request->input('message');
        $recipients    = $request->input('sender');
        $file          = $request->file('attachment');
        $hasAttachment = isset($file) && !empty($file);

        $result = $this->service->sendBulk(
            $recipients,
            $subject,
            $message,
            $sender,
            $hasAttachment,
            $file
        );

        return $this->response->json($result->toArray(), $result->status());
    }
}
