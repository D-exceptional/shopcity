<?php

declare(strict_types=1);

namespace App\Listeners\Store;

use App\Events\Store\StoreCreated;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Models\User;
use App\Listeners\Listener;

class SendVendorStoreCreatedEmail extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected User $userModel,
    ) {}

    public function handle(
        StoreCreated $event
    ): void {

        // Get Vendor Details
        $vendorData  = $this->getBiodata($event->vendorId);
        $vendorName  = $vendorData['name'];
        $vendorEmail = $vendorData['email'];

        // Build Vendors Message
        $vendorEmailMessage = "
            Hi <b>{$vendorName}</b>, 

            <br> Your new store, {$event->name}, is currently <b>pending approval</b>. 
            <br> Our team is reviewing your store details. Once approved, you'll be able to start selling. 
            <br> We'll notify you as soon as the status changes.
            <br> Thank you for your patience.
        ";

        $this->queue->dispatch(
            SimpleMailJob::class,
            [
                'Store Creation Successful',
                $vendorEmail,
                $vendorEmailMessage
            ],
            'emails'
        );
    }

    private function getBiodata(
        int $userId
    ): array {

        $userData = $this->userModel->findById($userId);

        return [
            'name'  => $userData['firstname'] . ' ' . $userData['lastname'],
            'email' => $userData['email'],
        ];
    }
}