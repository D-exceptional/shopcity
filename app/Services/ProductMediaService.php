<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Result;
use App\Media\CloudinaryManager;
use App\Models\ProductMedia;

class ProductMediaService 
{
    public function __construct(
        protected Result $result,  
        protected CloudinaryManager $cloudinaryManager, 
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

        // Implement This Using Events
        $this->cloudinaryManager->delete($media['media_url']);

        return $this->result->success('Media updated successfully');
    }

    public function deleteAll(
        int $productId
    ): Result {

        $mediaList = $this->mediaModel->findAll($productId);

        if ($mediaList === false) {
            return $this->result->error('Media not found', 404);
        }

        $pdo = $this->mediaModel->db; 
        $pdo->beginTransaction();

        $errors = [];

        try {
            foreach ($mediaList as $item) {

                // Implement This Using Events
                $this->cloudinaryManager->delete($item['media_url']);
            }

            if (!empty($errors)) {
                $pdo->rollBack();

                return $this->result->error('Some media could not be deleted from Cloudinary', 400, $errors);
            }

            $this->mediaModel->deleteAll($productId);

            $pdo->commit();

            return $this->result->success('All media deleted successfully');

        } catch (\Exception $e) {

            $pdo->rollBack();
            return $this->result->error('Failed to delete media due to server error: ' . $e->getMessage(), 500);
        }
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

        // Implement This Using Events
        $this->cloudinaryManager->delete($media['media_url']);

        return $this->result->success('Media deleted successfully');
    }

    public function deleteBulk(
        array $urls
    ): Result {

        $this->mediaManager->deleteBulk($urls);

        return $this->result->success('Media deleted successfully', ['result' => $result ]);
    }
}
