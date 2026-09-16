<?php

declare(strict_types=1);

namespace App\Listeners\Store;

use App\Events\Store\StoreStatusUpdated;
use App\Queue\Queue;
use App\Jobs\SimpleMailJob;
use App\Models\User;
use App\Listeners\Listener;

class SendVendorStoreStatusUpdatedEmail extends Listener
{
    public function __construct(
        protected Queue $queue,
        protected User $userModel,
    ) {}

    public function handle(
        StoreStatusUpdated $event
    ): void {

        // Get Vendor Details
        $vendorData  = $this->getBiodata($event->vendorId);
        $vendorName  = $vendorData['name'];
        $vendorEmail = $vendorData['email'];

        // Build Venor Message Based On Status
        $statusMessage = [
            'Active' => "
                Hi <b>{$vendorName}</b>, 

                <br> Great news! 🎉 Your store is now <b>active</b>. 
                <br> Customers can start placing orders, and you'll receive credits into your savings wallet for every order fulfilled. 
                <br> Keep your inventory updated to maximize your sales.
                <br> We're excited to see your growth on our platform!
            ",

            'Deactivated' => "
                Hi <b>{$vendorName}</b>, 

                <br> Your store has been <b>deactivated</b>. 
                <br> This may be due to policy violations, inactivity, or other issues. 
                <br> Please contact support at <b>support@shopcity.com</b> or visit <b><a href='/contact'>Appeal Page</a></b> to resolve this and restore your store. 
                <br> We value your partnership and hope to have you back soon.
            ",
        ];

        // Fallback In Case Of Unknown Status
        $vendorEmailMessage = $statusMessage[$event->status] ?? "
            Hi <b>{$vendorName}</b>,

            <br> There has been an update to your store status. 
            <br> Please check your vendor dashboard for more details.
        ";

        $this->queue->dispatch(
            SimpleMailJob::class,
            [
                'Store Status Updated',
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