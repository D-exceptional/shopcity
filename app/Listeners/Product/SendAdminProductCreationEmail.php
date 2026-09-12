<?php

declare(strict_types=1);

namespace App\Listeners\Product;

use App\Events\Product\ProductCreated;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Models\User;
use App\Listeners\Listener;

class SendAdminProductCreationEmail extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected User $userModel,
    ) {}

    public function handle(
        ProductCreated $event
    ): void {

        $adminEmailMessage = "
           Hello Admin, 

            <br> A new product, <b>{$event->name}</b>, was created on the platform!
            <br> Kindly review and take necessary actions. 
        ";

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $this->queue->dispatch(
                SimpleMailJob::class,
                [
                    'New Product Created',
                    $admin['email'],
                    $adminEmailMessage
                ],
                'emails'
            );
        }
    }
}