<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\User\UserStatusUpdated;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Listeners\Listener;

class SendUserStatusEmail extends Listener
{
    public function __construct(
        protected Queue $queue
    ) {}

    public function handle(
        UserStatusUpdated $event
    ): void {

        $statusMessages = [
            'Active' => "
                Hi <b>{$event->name}</b>, 
                <br> Great news! 🎉 Your account has been <b>activated</b>. 
                <br> You can now log into your account and pick up from where you left off. 
                <br> Take care to adhere to the regulations in order to prevent sanctions of this nature.
                <br> We're excited to have you back!
            ",

            'Deactivated' => "
                Hi <b>{$event->name}</b>, 
                <br> Your account has been <b>deactivated</b>. 
                <br> This may be due to policy violations, inactivity, or other issues. 
                <br> Please contact support at <b>support@mrsamase.com</b> or visit <b><a href='/contact'>Appeal Page</a></b> 
                    to resolve this and restore your account. 
                <br> We value your partnership and hope to have you back soon.
            ",
        ];

        $message = $statusMessages[$event->status] ?? "
            Hi <b>{$event->name}</b>, 
            <br> There has been an update to your account status. 
            <br> Please check account for more details.
        ";

        $this->queue->dispatch(
            SimpleMailJob::class,
            [
                'Account Status Updated',
                $event->email,
                $message
            ],
            'emails'
        );
    }
}