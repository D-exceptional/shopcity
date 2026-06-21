<?php

namespace App\Services;

use App\Helpers\ResponseManager;
use App\Helpers\MediaManager;
use App\Models\ProductMedia;
use PDO;

class ProductMediaService 
{
    protected ResponseManager $response;
    protected MediaManager $mediaManager;
    protected ProductMedia $mediaModel;

    public function __construct(ResponseManager $response, MediaManager $mediaManager, ProductMedia $mediaModel)
    {
        // Required helpers for this service class
        $this->response     = $response;
        $this->mediaManager = $mediaManager;
        // Required model for this service class
        $this->mediaModel = $mediaModel;
    }

    public function findAll(int $id)
    {
        $media = $this->mediaModel->findAll($id);
        if ($media === false) {
            return $this->response->fail('Media not found', 404);
        }
        // Prepare data
        return $this->response->success('Media fetched successfully', ['media' => $media]);
    }

    public function findOne(int $id)
    {
        $media = $this->mediaModel->findOne($id);
        if ($media === false) {
            return $this->response->fail('Media not found', 404);
        }
        // Prepare data
        return $this->response->success('Media fetched successfully', ['media' => $media]);
    }

    public function update(int $id, string $url)
    {
        $media = $this->mediaModel->findOne($id);
        if ($media === false) {
            return $this->response->fail('Media not found', 404);
        }

        // Update DB with new URL
        $updated = $this->mediaModel->update($url, $id);
        if ($updated === false) {
            return $this->response->fail('Failed to update media', 500);
        }

        // Delete old file from Cloudinary
        $result = $this->mediaManager->delete($media['media_url']);
        if (!isset($result['status']) || $result['status'] !== 'success') {
            return $this->response->fail('Failed to delete file from Cloudinary', 500);
        }

        return $this->response->success('Media updated successfully');
    }

    public function deleteAll(int $id)
    {
        $productId = (int) $id;
        $mediaList = $this->mediaModel->findAll($productId);

        if ($mediaList === false) {
            return $this->response->fail('Media not found', 404);
        }

        // Get PDO connection from model
        $pdo = $this->mediaModel->getDb(); // expose db getter instead of accessing directly
        $pdo->beginTransaction();

        $errors = [];

        try {
            foreach ($mediaList as $item) {
                $result = $this->mediaManager->delete($item['media_url']);

                if (isset($result['status']) && $result['status'] !== 'success') {
                    $errors[] = [
                        'url'     => $item['media_url'],
                        'message' => $result['message']
                    ];
                }
            }

            if (!empty($errors)) {
                $pdo->rollBack();
                error_log("Cloudinary deletion errors for product {$productId}: " . json_encode($errors));
                return $this->response->fail('Some media could not be deleted from Cloudinary', 400, $errors);
            }

            $this->mediaModel->deleteAll($productId);
            $pdo->commit();

            return $this->response->success('All media deleted successfully');
        } catch (\Exception $e) {
            $pdo->rollBack();
            error_log("Exception during bulk delete for product {$productId}: " . $e->getMessage());
            return $this->response->fail('Failed to delete media due to server error', 500);
        }
    }

    public function deleteOne(int $id)
    {
        $media = $this->mediaModel->findOne($id);
        if ($media === false) {
            return $this->response->fail('Media not found', 404);
        }

        $deleted = $this->mediaModel->deleteOne($id);
        if ($deleted === false) {
            return $this->response->fail('Failed to delete media', 500);
        }

        $result = $this->mediaManager->delete($media['media_url']);
        if (!isset($result['result']) || $result['status'] !== 'success') {
            return $this->response->fail('Failed to delete file from Cloudinary', 500);
        }

        return $this->response->success('Media deleted successfully');
    }

    public function deleteBulk(array $urls)
    {
        $result = $this->mediaManager->deleteBulk($urls);
        return $this->response->success('Media deleted successfully', ['result' => $result ]);
    }
}
