<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\User\ContactMessageReceived;
use App\Models\User;
use App\Models\Mail;
use App\Listeners\Listener;

class CreateContactMailRecord extends Listener
{
    public function __construct(
        protected User $userModel,
        protected Mail $mailModel
    ) {}

    public function handle(
        ContactMessageReceived $event
    ): void {

        $message = "
            A message was sent by <b> " . trim($event->name) . "</b> from  <b> " . trim($event->country) . "</b>

            <br> You can reach out to them via their mobile: <b>" . trim($event->contact) . "</b> or email address: <b>" . trim($event->email) . "</b>
            <br> Below is the message:
            <br> {$event->message}
        ";

        $admins = $this->userModel->allByRole('Admin');

        foreach ($admins as $admin) {

            $this->mailModel->createMail(
                'Text',
                $event->subject,
                $event->name,
                $admin['email'],
                $message,
                'None', 
                'None'
            );
        }
    }
}