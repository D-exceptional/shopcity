<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Request;
use App\Http\Response;
use App\Services\Api\ProductMediaService;

class ProductMediaController extends Controller
{
    public function __construct(
        protected Response $response, 
        protected ProductMediaService $service
    ) {}

    public function findAll(Request $request): Response
    {
        $productId = $request->route('id');
        $result = $this->service->findAll($productId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function findOne(Request $request): Response
    {
        $mediaId = $request->route('id');
        $result = $this->service->findOne($mediaId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function update(Request $request): Response
    {
        $mediaId = $request->route('id');
        $newUrl  = $request->input('url');
        $result = $this->service->update($mediaId, $newUrl);

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
        $mediaId = $request->route('id');
        $result = $this->service->deleteOne($mediaId);

        return $this->response->json($result->toArray(), $result->status());
    }

    public function deleteBulk(Request $request): Response
    {
        $mediaUrls = $request->input('urls');
        $result = $this->service->deleteBulk($mediaUrls);

        return $this->response->json($result->toArray(), $result->status());
    }
}
