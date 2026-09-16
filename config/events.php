<?php

declare(strict_types=1);

// -------------------------------------------------------- //
// ------------------- EVENTS SECTION -------------------- //
// ------------------------------------------------------- //

// Checkout Event Imports
use App\Events\Checkout\Checkout;

// Mail Event Imports
use App\Events\Mail\MailSent;

// Media Event Imports
use App\Events\Media\SingleMediaDeleted;
use App\Events\Media\BulkMediaDeleted;

// Order Event Imports
use App\Events\Order\ItemStatusUpdated;
use App\Events\Order\OrderCanceled;
use App\Events\Order\OrderCompleted;

// Product Event Imports
use App\Events\Product\ProductCreated;
use App\Events\Product\ReviewCreated;

// Store Event Imports
use App\Events\Store\StoreAvatarUpdated;
use App\Events\Store\StoreCreated;
use App\Events\Store\StoreStatusUpdated;

// User Event Imports
use App\Events\User\ContactMessageReceived;
use App\Events\User\OtpRequested;
use App\Events\User\ProfileUpdated;
use App\Events\User\UserRegistered;
use App\Events\User\UserStatusUpdated;

// Wallet Event Imports
use App\Events\Wallet\TopupProcessed;
use App\Events\Wallet\RequestPlaced;
use App\Events\Wallet\PayoutProcessed;

// -------------------------------------------------------- //
// ----------------- LISTENERS SECTION -------------------- //
// ------------------------------------------------------- //

// Checkout Listeners Imports
use App\Listeners\Checkout\CreateAdminCheckoutNotification;
use App\Listeners\Checkout\CreateCustomerCheckoutNotification;
use App\Listeners\Checkout\CreateVendorCheckoutNotification;
use App\Listeners\Checkout\ProcessVendorCheckoutCommission;
use App\Listeners\Checkout\SendAdminCheckoutEmail;
use App\Listeners\Checkout\SendAdminCheckoutPush;
use App\Listeners\Checkout\SendCustomerCheckoutEmail;
use App\Listeners\Checkout\SendCustomerCheckoutPush;
use App\Listeners\Checkout\SendVendorCheckoutEmail;
use App\Listeners\Checkout\SendVendorCheckoutPush;

// Mail Listeners Imports
use App\Listeners\Mail\SendBulkMail;

// Media Listeners Imports
use App\Listeners\Media\DeleteSingleMedia;
use App\Listeners\Media\DeleteBulkMedia;

// Order Listeners Imports
use App\Listeners\Order\CreateAdminItemStatusUpdatedNotification;
use App\Listeners\Order\CreateAdminOrderCanceledNotification;
use App\Listeners\Order\CreateAdminOrderCompletedNotification;
use App\Listeners\Order\CreateCustomerItemStatusUpdatedNotification;
use App\Listeners\Order\CreateCustomerOrderCanceledNotification;
use App\Listeners\Order\CreateCustomerOrderCompletedNotification;
use App\Listeners\Order\CreateVendorItemStatusUpdatedNotification;
use App\Listeners\Order\CreateVendorOrderCanceledNotification;
use App\Listeners\Order\CreateVendorOrderCompletedNotification;
use App\Listeners\Order\ProcessCustomerRefund;
use App\Listeners\Order\ProcessVendorCompensation;
use App\Listeners\Order\SendAdminItemStatusUpdatedEmail;
use App\Listeners\Order\SendAdminItemStatusUpdatedPush;
use App\Listeners\Order\SendAdminOrderCanceledEmail;
use App\Listeners\Order\SendAdminOrderCanceledPush;
use App\Listeners\Order\SendAdminOrderCompletedEmail;
use App\Listeners\Order\SendAdminOrderCompletedPush;
use App\Listeners\Order\SendCustomerItemStatusUpdatedEmail;
use App\Listeners\Order\SendCustomerItemStatusUpdatedPush;
use App\Listeners\Order\SendCustomerOrderCanceledEmail;
use App\Listeners\Order\SendCustomerOrderCanceledPush;
use App\Listeners\Order\SendCustomerOrderCompletedEmail;
use App\Listeners\Order\SendCustomerOrderCompletedPush;
use App\Listeners\Order\SendVendorItemStatusUpdatedEmail;
use App\Listeners\Order\SendVendorItemStatusUpdatedPush;
use App\Listeners\Order\SendVendorOrderCanceledEmail;
use App\Listeners\Order\SendVendorOrderCanceledPush;
use App\Listeners\Order\SendVendorOrderCompletedEmail;
use App\Listeners\Order\SendVendorOrderCompletedPush;

// Product Listeners Imports
use App\Listeners\Product\CreateAdminProductCreationNotification;
use App\Listeners\Product\CreateVendorReviewCreationNotification;
use App\Listeners\Product\SendAdminProductCreationEmail;
use App\Listeners\Product\SendVendorReviewCreationEmail;

// Store Listeners Imports
use App\Listeners\Store\SendAdminStoreCreatedEmail;
use App\Listeners\Store\SendAdminStoreCreatedPush;
use App\Listeners\Store\SendVendorStoreCreatedEmail;
use App\Listeners\Store\SendVendorStoreCreatedPush;
use App\Listeners\Store\SendVendorStoreStatusUpdatedEmail;
use App\Listeners\Store\SendVendorStoreStatusUpdatedPush;
use App\Listeners\Store\UpdateStoreAvatar;

// User Listeners Imports
use App\Listeners\User\CreateAdminRegistrationNotification;
use App\Listeners\User\CreateContactMailRecord;
use App\Listeners\User\CreateContactNotification;
use App\Listeners\User\SendAdminRegistrationEmail;
use App\Listeners\User\SendAdminRegistrationPush;
use App\Listeners\User\SendContactMessageEmail;
use App\Listeners\User\SendOtpEmail;
use App\Listeners\User\SendUserRegistrationEmail;
use App\Listeners\User\SendUserStatusEmail;
use App\Listeners\User\UpdateProfileImage;

// Wallet Listeners Imports
use App\Listeners\Wallet\CreateUserPayoutProcessedNotification;
use App\Listeners\Wallet\CreateUserRequestPlacedNotification;
use App\Listeners\Wallet\CreateUserTopupProcessedNotification;
use App\Listeners\Wallet\ProcessRequestPlacedDebit;
use App\Listeners\Wallet\SendUserPayoutProcessedEmail;
use App\Listeners\Wallet\SendUserPayoutProcessedPush;
use App\Listeners\Wallet\SendUserRequestPlacedEmail;
use App\Listeners\Wallet\SendUserRequestPlacedPush;
use App\Listeners\Wallet\SendUserTopupProcessedEmail;
use App\Listeners\Wallet\SendUserTopupProcessedPush;

// -------------------------------------------------------- //
// --------------- EXECUTION CHAIN SECTION ---------------- //
// ------------------------------------------------------- //

return [

    // Checkout Event Execution Chain
    Checkout::class => [
        CreateAdminCheckoutNotification::class,
        CreateCustomerCheckoutNotification::class,
        CreateVendorCheckoutNotification::class,
        ProcessVendorCheckoutCommission::class,
        SendAdminCheckoutEmail::class,
        SendAdminCheckoutPush::class,
        SendCustomerCheckoutEmail::class,
        SendCustomerCheckoutPush::class,
        SendVendorCheckoutEmail::class,
        SendVendorCheckoutPush::class,
    ],

    // Mail Event Execution Chain
    MailSent::class => [
        SendBulkMail::class,
    ],

    // Media Event Execution Chain
    SingleMediaDeleted::class => [
        DeleteSingleMedia::class,
    ],

    BulkMediaDeleted::class => [
        DeleteBulkMedia::class,
    ],

    // Order Event Execution Chain
    ItemStatusUpdated::class => [
       CreateAdminItemStatusUpdatedNotification::class,
       CreateCustomerItemStatusUpdatedNotification::class,
       CreateVendorItemStatusUpdatedNotification::class,
       SendAdminItemStatusUpdatedEmail::class,
       SendAdminItemStatusUpdatedPush::class,
       SendCustomerItemStatusUpdatedEmail::class,
       SendCustomerItemStatusUpdatedPush::class,
       SendVendorItemStatusUpdatedEmail::class,
       SendVendorItemStatusUpdatedPush::class,
    ],

    OrderCompleted::class => [
        CreateAdminOrderCompletedNotification::class,
        CreateCustomerOrderCompletedNotification::class,
        CreateVendorOrderCompletedNotification::class,
        SendAdminOrderCompletedEmail::class,
        SendAdminOrderCompletedPush::class,
        SendCustomerOrderCompletedEmail::class,
        SendAdminOrderCompletedPush::class,
        SendCustomerOrderCompletedEmail::class,
        SendCustomerOrderCompletedPush::class,
        SendVendorOrderCompletedEmail::class,
        SendVendorOrderCompletedPush::class,
    ],

    OrderCanceled::class => [
        ProcessCustomerRefund::class,
        ProcessVendorCompensation::class,
        CreateAdminOrderCanceledNotification::class,
        CreateCustomerOrderCanceledNotification::class,
        CreateVendorOrderCanceledNotification::class,
        SendAdminOrderCanceledEmail::class,
        SendAdminOrderCanceledPush::class,
        SendCustomerOrderCanceledEmail::class,
        SendCustomerOrderCanceledPush::class,
        SendVendorOrderCanceledEmail::class,
        SendVendorOrderCanceledPush::class,
    ],

    // Product Event Execution Chain
    ProductCreated::class => [
        CreateAdminProductCreationNotification::class,
        SendAdminProductCreationEmail::class,
    ],

    ReviewCreated::class => [
        CreateVendorReviewCreationNotification::class,
        SendVendorReviewCreationEmail::class,
    ],

    // Store Event Execution Chain
    StoreCreated::class => [
       SendAdminStoreCreatedEmail::class,
       SendAdminStoreCreatedPush::class,
       SendVendorStoreCreatedEmail::class,
       SendVendorStoreCreatedPush::class,
    ],

    StoreAvatarUpdated::class => [
       UpdateStoreAvatar::class,
    ],

    StoreStatusUpdated::class => [
        SendVendorStoreStatusUpdatedEmail::class,
        SendVendorStoreStatusUpdatedPush::class,
    ],

    // User Event Execution Chain
    ContactMessageReceived::class => [
        CreateContactMailRecord::class,
        CreateContactNotification::class,
        SendContactMessageEmail::class,
    ],

    OtpRequested::class => [
        SendOtpEmail::class,
    ],

    ProfileUpdated::class => [
        UpdateProfileImage::class,
    ],

    UserRegistered::class => [
        CreateAdminRegistrationNotification::class,
        SendAdminRegistrationEmail::class,
        SendAdminRegistrationPush::class,
        SendUserRegistrationEmail::class,
    ],

    UserStatusUpdated::class => [
        SendUserStatusEmail::class,
    ],

    // Wallet Event Execution Chain
    TopupProcessed::class => [
        CreateUserTopupProcessedNotification::class,
        SendUserTopupProcessedEmail::class,
        SendUserTopupProcessedPush::class,
    ],

    RequestPlaced::class => [
        ProcessRequestPlacedDebit::class,
        CreateUserRequestPlacedNotification::class,
        SendUserRequestPlacedEmail::class,
        SendUserRequestPlacedPush::class,
    ],

    PayoutProcessed::class => [
        CreateUserPayoutProcessedNotification::class,
        SendUserPayoutProcessedEmail::class,
        SendUserPayoutProcessedPush::class,
    ],

];