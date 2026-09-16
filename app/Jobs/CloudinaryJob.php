<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\JobInterface;
use App\Media\CloudinaryManager;

class CloudinaryJob implements JobInterface
{
    public mixed $media;
    public string $type;

    public function __construct(
        public CloudinaryManager $cloudinary
    ) {}

    public function setPayload(array $data): void
    {
        $this->media = $data[0];
        $this->type  = is_array($data[0]) ? 'bulk' : 'single';
    }

    public function handle(): void
    {
        if ($this->type === 'bulk') {

            $this->cloudinary->deleteBulk(
                $this->media
            );
            
        }
        else {

            $this->cloudinary->delete(
                $this->media
            );
        }
    }
}