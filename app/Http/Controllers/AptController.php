<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\BlogService;

class BlogController extends Controller
{
    public function __construct(
        protected Response $response,
        protected BlogService $service
    ) {}

    // =========================================
    // CREATE BLOG
    // =========================================
    public function createBlog(Request $request): Response
    {
        $userId = $request->user()['id'];
        $result = $this->service->createBlog(
            $request->input('banner'), 
            $request->input('title'), 
            $request->input('category'), 
            $request->input('article'), 
            $userId
        );

        return $this->response->json($result->toArray(), $result->status());
    }

    // =========================================
    // UPDATE BLOG DETAILS
    // =========================================
    public function updateDetails(Request $request): Response
    {
        $result = $this->service->updateDetails(
            $request->input('title'), 
            $request->input('article'),  
            (int) $request->input('id')
        );

        return $this->response->json($result->toArray(), $result->status());
    }

    // =========================================
    // UPDATE BLOG BANNER
    // =========================================
    public function updateBanner(Request $request): Response
    {
        $result = $this->service->updateBanner(
            (int) $request->input('id'), 
            $request->input('url')
        );
        
        return $this->response->json($result->toArray(), $result->status());
    }

    // =========================================
    // UPDATE BLOG STATUS
    // =========================================
    public function updateStatus(Request $request): Response
    {
        $result = $this->service->updateStatus(
            $request->input('status'),  
            (int) $request->input('id')
        );

        return $this->response->json($result->toArray(), $result->status());
    }

    // =========================================
    // UPDATE BLOG COUNTERS (LIKES, VIEWS)
    // =========================================
    public function updateCounter(Request $request): Response
    { 
        $result = $this->service->updateCounter(
            (int) $request->input('id'), 
            $request->input('type')
        );

        return $this->response->json($result->toArray(), $result->status());
    }

    // =========================================
    // DELETE BLOG
    // =========================================
    public function deleteBlog(Request $request): Response
    { 
        $result = $this->service->deleteBlog(
            (int) $request->input('id')
        );

        return $this->response->json($result->toArray(), $result->status());
    }

    // =========================================
    // GET SINGLE BLOG DATA
    // =========================================
    public function findOne(Request $request): Response
    { 
        $result = $this->service->findOne(
            (int) $request->input('id')
        );

        return $this->response->json($result->toArray(), $result->status());
    }

    // =========================================
    // GET BLOGS BY STATUS
    // =========================================
    public function findByStatus(Request $request): Response
    { 
        $result = $this->service->findByStatus(
            $request->input('status'), 
            (int) $request->input('page')
        );

        return $this->response->json($result->toArray(), $result->status());
    }

    // =========================================
    // GET BLOGS BY AUTHORS (USERS)
    // =========================================
    public function findByUser(Request $request): Response
    { 
        $result = $this->service->findByUser(
            (int) $request->input('id'), 
            (int) $request->input('page')
        );

        return $this->response->json($result->toArray(), $result->status());
    }
    
}
