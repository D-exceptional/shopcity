<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\User\UserRegistered;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Listeners\Listener;

class SendUserRegistrationEmail extends Listener
{
    public function __construct(
        protected Queue $queue
    ) {}

    public function handle(
        UserRegistered $event
    ): void {

        // Default Message
        $defaultMessage = "
            Hi <b>{$event->name}</b>, 

            <br> We are currently reviewing your registration. 
            <br> We'll notify you as soon as there's any new developments.
            <br> Thank you for your patience.
        ";

        // Role-based Messaging (DRY pproach)
        $roleMessage = [
            'customer'  => $this->buildWelcomeMessage($event->name),
            'affiliate' => $this->buildWelcomeMessage($event->name),
            'worker'    => $this->buildWelcomeMessage($event->name),
            'vendor'    => ($event->creator === 'admin') 
                ? $this->buildWelcomeMessage($event->name) 
                : "
                    Hi <b>{$event->name}</b>, 

                    <br> Your registration is currently <b>undergoing review</b>. 
                    <br> Our team is reviewing your details. Once approved, you'll be able to start selling. 
                    <br> We'll notify you as soon as the status changes.
                    <br> Thank you for your patience.
                ",
        ];

       $userEmailMessage = $roleMessage[$event->role] ?? $defaultMessage;

        $this->queue->dispatch(
            SimpleMailJob::class,
            [
                $event->subject,
                $event->email,
                $userEmailMessage
            ],
            'emails'
        );
    }

    private function buildWelcomeMessage(
        string $name
    ): string {

        return "
            Hi <b>{$name}</b>,

            <br> Great news! 🎉 Your registration is successful. 
            <br> Login to your account for maximum shopping experience curated just for you!
            <br> Thank you for choosing to shop with us.
            <br> We're excited to have you on our platform!
        ";
    }
}