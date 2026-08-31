<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    public function __construct(
        protected Response $response, 
        protected NotificationService $service
    ) {}

    public function create(Request $request): Response
    {
        $details  = $request->input('details');
        $type     = $request->input('type');
        $receiver = $request->input('receiver');
        $result = $this->service->create($details, $type, $receiver);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function countAll(): Response
    {
        $result = $this->service->countAll();

        return $this->response->json($result->toArray(), $result->status());
    }

    public function countAllById(Request $request): Response
    {
        $userId = $request->user()['id'];
        $result = $this->service->countAllById($userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function countUnreadById(Request $request): Response
    {
        $userId = $request->user()['id'];
        $result = $this->service->countUnreadById($userId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function getUnread(Request $request): Response
    {
        $userId = $request->user()['id'];
        $page   = $request->input('page')  ?? 0;
        $limit  = $request->input('limit') ?? 20;
        $result = $this->service->getUnreadById($userId, $page, $limit);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function fetchById(Request $request): Response
    {
        $userId = $request->user()['id'];
        $page   = $request->input('page')  ?? 0;
        $limit  = $request->input('limit') ?? 20;
        $result = $this->service->fetchById($userId, $page, $limit);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function markAsRead(Request $request): Response
    {
        $userId = $request->user()['id'];
        $result = $this->service->markAsRead($userId);
        
        return $this->response->json($result->toArray(), $result->status());
    }
}
