<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\LinkService;

class LinkController extends Controller
{
    public function __construct(
        protected Response $response, 
        protected LinkService $service
    ) {}

    public function create(Request $request): Response
    {
        $productId = $request->input('productId'); 
        $userId    = $request->input('userId'); 
        $short     = $request->input('short'); 
        $long      = $request->input('long'); 
        $code      = $request->input('code'); 
        $status    = $request->input('status'); 
        $result = $this->service->create($productId, $userId, $short, $long, $code, $status);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findAll(Request $request): Response
    {
        $productId = $request->route('id'); 
        $result = $this->service->findAll($productId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findOne(Request $request): Response
    {
        $linkId = $request->route('id'); 
        $result = $this->service->findOne($linkId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function updateAll(Request $request): Response
    {
        $productId = $request->route('id'); 
        $status    = $request->input('status'); 
        $result = $this->service->updateAll($productId, $status);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function updateOne(Request $request): Response
    {
        $linkId = $request->route('id'); 
        $status = $request->input('status'); 
        $result = $this->service->updateOne($linkId, $status);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function deleteAll(Request $request): Response
    {
        $productId = $request->route('id'); 
        $result = $this->service->deleteAll($productId);

        return $this->response->json($result->toArray(), $result->status());

    }

    public function deleteOne(Request $request): Response
    {
        $linkId = $request->route('id'); 
        $result = $this->service->deleteOne($linkId);

        return $this->response->json($result->toArray(), $result->status());
        
    }
}
