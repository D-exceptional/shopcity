<?php

declare(strict_types=1);

namespace App\Listeners\Wallet;

use App\Events\Wallet\RequestPlaced;
use App\Models\Notification;
use App\Listeners\Listener;

class CreateUserRequestPlacedNotification extends Listener
{
    public function __construct(
        protected Notification $notificationModel
    ) {}

    public function handle(
        RequestPlaced $event
    ): void {

        $created = $this->notificationModel->create(
            $event->message,
            'Fund Request',
            $event->userId
        );

        if ($created === false) {

            $this->logError("Failed to create request withdrawal notification for user: {$event->email}");
        }
    }
}