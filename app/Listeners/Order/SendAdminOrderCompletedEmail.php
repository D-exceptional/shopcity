<?php

declare(strict_types=1);

namespace App\Listeners\Order;

use App\Events\Order\OrderCompleted;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Models\User;
use App\Listeners\Listener;

class SendAdminOrderCompletedEmail extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected User $userModel,
    ) {}

    public function handle(
        OrderCompleted $event
    ): void {

        $adminEmailMessage =  "
            Hello Admin,

            <br> The order, <b>{$event->orderCode}</b>, has been completed!
            <br> Kindly review and take necessary actions. 
        ";

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $this->queue->dispatch(
                SimpleMailJob::class,
                [
                    'Order Completed',
                    $admin['email'],
                    $adminEmailMessage
                ],
                'emails'
            );
        }
    }
}