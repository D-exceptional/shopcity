<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Result;
use App\Support\TextManager;
use App\Events\Blog\BlogCreated;
use App\Events\Blog\BannerUpdated;
use App\Events\Blog\BlogStatusUpdated;
use App\Events\EventDispatcher;
use App\Models\Blog;
use App\Models\User;

class BlogService
{
    private string $baseUrl;

    public function __construct(
        protected Result $result, 
        protected TextManager $textProcessor, 
        protected EventDispatcher $eventDispatcher,
        protected Blog $blogModel, 
        protected User $userModel, 
    ) {
        $this->baseUrl = config('app.base_path', '/projects/showcase/jobspot'); 
    }

    // =========================================
    // CREATE BLOG
    // =========================================
    public function createBlog(
        string $banner, 
        string $title, 
        string $category, 
        string $article, 
        int $userId
    ): Result {

        $title = $this->textProcessor->formatTitle($title);

        $created = $this->blogModel->createBlog($banner, $title, $category, $article, $userId);
        if ($created === false) {
            return $this->result->error('Failed to create blog', 500);
        }

        $authorDetails = $this->userModel->findById($userId);
        $authorName    = $authorDetails['fullname'];
        $authorEmail   = $authorDetails['email'];
        $authorRole    = $authorDetails['user_role'];

        $this->eventDispatcher->dispatch(
            new BlogCreated(
                userId: $userId,
                name: $authorName,
                email: $authorEmail,
                role: $authorRole,
            )
        );

        return $this->result->success('Blog created successfully', [], 201);
    }

    // =========================================
    // UPDATE BLOG DETAILS
    // =========================================
    public function updateDetails(
        string $title, 
        string $article, 
        int $id
    ): Result {

        $updated = $this->blogModel->updateDetails($title, $article, $id);
        if ($updated === false) {
            return $this->result->error('Failed to update blog', 500);
        }
        
        return $this->result->success('Details updated successfully');
    }

    // =========================================
    // UPDATE BLOG BANNER
    // =========================================
    public function updateBanner(
        int $id, 
        string $url
    ): Result {

        $banner = $this->blogModel->findBanner($id);
        if ($banner === false) {
            return $this->result->error('Blog banner not found', 404);
        }

        $updated = $this->blogModel->updateBanner($url, $id);
        if ($updated === false) {
            return $this->result->error('Failed to update blog banner', 500);
        }

        $this->eventDispatcher->dispatch(
            new BannerUpdated(
                oldBanner: $banner,
                newBanner: $url,
            )
        );

        return $this->result->success('Banner updated successfully');
    }

    // =========================================
    // UPDATE BLOG STATUS
    // =========================================
    public function updateStatus(
        string $status, 
        int $id
    ): Result {

        $updated = $this->blogModel->updateStatus($status, $id);
        if ($updated === false) {
            return $this->result->error('Failed to update status', 500);
        }

        $authorId      = $this->blogModel->findUserByBlogId($id);
        $authorDetails = $this->userModel->findById($authorId);
        $authorName    = $authorDetails['fullname'];
        $authorEmail   = $authorDetails['email'];
        $authorRole    = $authorDetails['user_role'];

        $this->eventDispatcher->dispatch(
            new BlogStatusUpdated(
                userId: $authorId,
                name: $authorName,
                email: $authorEmail,
                role: $authorRole,
                status: $status,
                id: $id,
            )
        );

        return $this->result->success('Status updated successfully');
    }

    // =========================================
    // UPDATE BLOG COUNTERS
    // =========================================
    public function updateCounter(
        int $blogId, 
        string $type
    ): Result {

        $updated = $this->blogModel->updateCounter($blogId, $type);
        if ($updated === false) {
            return $this->result->error('Failed to update counter', 500);
        }

        $stats = $this->blogModel->getStats($blogId);

        return $this->result->success('Counter updated successfully', ['stats' => $stats]);
    }

    // =========================================
    // DELETE BLOG
    // =========================================
    public function deleteBlog(
        int $id
    ): Result {

        $deleted = $this->blogModel->deleteBlog($id);
        if ($deleted === false) {
            return $this->result->error('Failed to delete blog', 505);
        }

        return $this->result->success('Blog deleted successfully');
    }

    // =========================================
    // GET SINGLE BLOG
    // =========================================
    public function findOne(
        int $id
    ): Result { 

        $blog = $this->blogModel->findOne($id);
        if ($blog === false) {
            return $this->result->error('Failed to fetch blog', 505);
        }
        
        return $this->result->success('Blog fetched successfully', ['blog' => $blog]);
    }

    // =========================================
    // GET BLOGS BY STATUS
    // =========================================
    public function findByStatus(
        string $status, 
        int $page
    ): Result { 

        $blogs = $this->blogModel->findByStatus($status, $page);
        if ($blogs === false) {
            return $this->result->error('Failed to fetch blogs', 505);
        }
       
        return $this->result->success('Blogs fetched successfully', ['blogs' => $blogs]);
    }

    // =========================================
    // GET BLOGS BY AUTHORS
    // =========================================
    public function findByUser(
        int $id, 
        int $page
    ): Result { 
        
        $blogs = $this->blogModel->findByUser($id, $page);
        if ($blogs === false) {
            return $this->result->error('Failed to fetch blogs', 505);
        }
        
        return $this->result->success('Blogs fetched successfully', ['blogs' => $blogs]);
    }
}
