<?php

declare(strict_types=1);

namespace App\Listeners\Checkout;

use App\Events\Checkout\Checkout;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Models\User;
use App\Listeners\Listener;

class SendAdminCheckoutEmail extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected User $userModel,
    ) {}

    public function handle(
        Checkout $event
    ): void {

        $adminEmailMessage = "
            Hello Admin, 

            <br> A new order, <b>{$event->orderCode}</b>, has been created!
            <br> Kindly review and take necessary actions. 
        ";

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $this->queue->dispatch(
                SimpleMailJob::class,
                [
                    'New Order Notification',
                    $admin['email'],
                    $adminEmailMessage
                ],
                'emails'
            );
        }
    }
}