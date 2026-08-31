<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Result;
use App\Models\Link;

class LinkService
{
    public function __construct(
        protected Result $result,  
        protected Link $linkModel
    ) {}

    public function create(
        int $productId, 
        int $userId, 
        string $short, 
        string $long, 
        string $code, 
        string $status
    ): Result {

        $created = $this->linkModel->create($productId, $userId, $short, $long, $code, $status);
        if ($created === false) {
            return $this->result->error('Failed to create link', 500);
        }

        return $this->result->success('Link created successfully');
    }

    public function findAll(
        int $productId
    ): Result {

        $links = $this->linkModel->findAll($productId);
        if ($links === false) {
            return $this->result->error('No links found', 404);
        }

        return $this->result->success('Links fetched successfully', ['links' => $links]);
    }

    public function findOne(
        int $linkId
    ): Result {

        $link = $this->linkModel->findOne($linkId);
        if ($link === false) {
            return $this->result->error('No link found', 404);
        }

        return $this->result->success('Link fetched successfully', ['link' => $link]);
    }

    public function updateAll(
        int $productId, 
        string $status
    ): Result {

        $updated = $this->linkModel->updateAll($productId, $status);
        if ($updated === false) {
            return $this->result->error('Failed to update link status', 500);
        }

        return $this->result->success('Link status updated successfully');
    }

    public function updateOne(
        int $linkId, 
        string $status
    ): Result {

        $updated = $this->linkModel->updateOne($linkId, $status);
        if ($updated === false) {
            return $this->result->error('Failed to update link status', 500);
        }

        return $this->result->success('Link updated successfully');
    }

    public function deleteAll(
        int $productId
    ): Result {

        $deleted = $this->linkModel->deleteAll($productId);
        if ($deleted === false) {
            return $this->result->error('Failed to delete links', 500);
        }

        return $this->result->success('Links deleted successfully');
    }

    public function deleteOne(
        int $linkId
    ): Result {
        
        $deleted = $this->linkModel->deleteOne($linkId);
        if ($deleted === false) {
            return $this->result->error('Failed to delete link', 500);
        }

        return $this->result->success('Link deleted successfully');
    }

    public function generateCode(): string
    {
        return bin2hex(random_bytes(6));
    }
}
