<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Core\Result;
use App\Events\Media\SingleMediaDeleted;
use App\Events\Media\BulkMediaDeleted;
use App\Events\EventDispatcher;
use App\Models\ProductMedia;

class ProductMediaService 
{
    public function __construct(
        protected Result $result,  
        protected EventDispatcher $eventDispatcher,
        protected ProductMedia $mediaModel
    ) {}

    public function findAll(
        int $productId
    ): Result {

        $media = $this->mediaModel->findAll($productId);
        if ($media === false) {
            return $this->result->error('Media not found', 404);
        }

        return $this->result->success('Media fetched successfully', ['media' => $media]);
    }

    public function findOne(
        int $mediaId
    ): Result {

        $media = $this->mediaModel->findOne($mediaId);
        if ($media === false) {
            return $this->result->error('Media not found', 404);
        }

        return $this->result->success('Media fetched successfully', ['media' => $media]);
    }

    public function update(
        int $mediaId, 
        string $url
    ): Result {

        $media = $this->mediaModel->findOne($mediaId);
        if ($media === false) {
            return $this->result->error('Media not found', 404);
        }

        $updated = $this->mediaModel->update($url, $mediaId);
        if ($updated === false) {
            return $this->result->error('Failed to update media', 500);
        }

        $this->eventDispatcher->dispatch(
            new SingleMediaDeleted(
                url: $media['media_url'],
            )
        );

        return $this->result->success('Media updated successfully');
    }

    public function deleteAll(
        int $productId
    ): Result {

        $mediaList = $this->mediaModel->findAll($productId);

        if ($mediaList === false) {
            return $this->result->error('Media not found', 404);
        }

        $mediaItems = [];

        foreach ($mediaList as $item) {
            // Queue media urls to delete
            $mediaItems[] = $item['media_url'];
        }

        $this->mediaModel->deleteAll($productId);

        $this->eventDispatcher->dispatch(
            new BulkMediaDeleted(
                media: $mediaItems,
            )
        );

        return $this->result->success('All media deleted successfully');
    }

    public function deleteOne(
        int $mediaId
    ): Result {

        $media = $this->mediaModel->findOne($mediaId);
        if ($media === false) {
            return $this->result->error('Media not found', 404);
        }

        $deleted = $this->mediaModel->deleteOne($mediaId);
        if ($deleted === false) {
            return $this->result->error('Failed to delete media', 500);
        }

        $this->eventDispatcher->dispatch(
            new SingleMediaDeleted(
                url: $media['media_url'],
            )
        );

        return $this->result->success('Media deleted successfully');
    }

    public function deleteBulk(
        array $urls
    ): Result {

        $this->eventDispatcher->dispatch(
            new BulkMediaDeleted(
                media: $urls,
            )
        );

        return $this->result->success('Media items queued for deletion successfully');
    }
}
