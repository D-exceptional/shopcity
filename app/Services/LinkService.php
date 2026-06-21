<?php

namespace App\Services;

use App\Helpers\ResponseManager;
use App\Models\Link;

class LinkService
{
    protected ResponseManager $response;
    protected Link $linkModel;

    public function __construct(ResponseManager $response, Link $linkModel)
    {
        // Required helpers for this controller class
        $this->response  = $response;
        // Required model for this controller class
        $this->linkModel = $linkModel;
    }

    /**
     * Create a new product link
    */
    public function create(array $payload)
    {
        $created = $this->linkModel->create($payload['product'], $payload['user'], $payload['short'], $payload['long'], $payload['code'], $payload['status']);
        if ($created === false) {
            return $this->response->fail('Failed to create link', 500);
        }

        return $this->response->success('Link created successfully');
    }

    /**
     * Fetch all links (product_id)
    */
    public function findAll(int $id)
    {
        $links = $this->linkModel->findAll($id);
        if ($links === false) {
            return $this->response->fail('No links found', 404);
        }

        // Prepare data
        return $this->response->success('Links fetched successfully', ['links' => $links]);
    }

    /**
     * Fetch one link (link_id)
    */
    public function findOne(int $id)
    {
        $link = $this->linkModel->findOne($id);
        if ($link === false) {
            return $this->response->fail('No link found', 404);
        }

        // Prepare data
        return $this->response->success('Link fetched successfully', ['link' => $link]);
    }

    /**
     * Update all link status (product_id)
    */
    public function updateAll(int $id, string $status)
    {
        $updated = $this->linkModel->updateAll($id, $status);
        if ($updated === false) {
            return $this->response->fail('Failed to update link status', 500);
        }

        return $this->response->success('Link status updated successfully');
    }

    /**
     * Update one link status (link_id)
    */
    public function updateOne(int $id, string $status)
    {
        $updated = $this->linkModel->updateOne($id, $status);
        if ($updated === false) {
            return $this->response->fail('Failed to update link status', 500);
        }

        return $this->response->success('Link updated successfully');
    }

    /**
     * Delete all link (product_id)
    */
    public function deleteAll(int $id)
    {
        $deleted = $this->linkModel->deleteAll($id);
        if ($deleted === false) {
            return $this->response->fail('Failed to delete links', 500);
        }

        return $this->response->success('Links deleted successfully');
    }

    /**
     * Delete a link (link_id)
    */
    public function deleteOne(int $id)
    {
        $deleted = $this->linkModel->deleteOne($id);
        if ($deleted === false) {
            return $this->response->fail('Failed to delete link', 500);
        }

        return $this->response->success('Link deleted successfully');
    }

    /**
     * Generate unique affiliate code
    */
    public function generateCode(): string
    {
        return bin2hex(random_bytes(6));
    }
}
