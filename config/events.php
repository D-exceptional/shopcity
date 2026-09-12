<?php

declare(strict_types=1);

// Mail Events Imports
use App\Events\Mail\MailSent;

// User Events Imports
use App\Events\User\ContactMessageReceived;
use App\Events\User\OtpRequested;
use App\Events\User\ProfileUpdated;
use App\Events\User\UserRegistered;
use App\Events\User\UserStatusUpdated;

// Wallet Events Imports
use App\Events\Wallet\PaymentProcessed;
use App\Events\Wallet\RegistrationPaymentFinalized;
use App\Events\Wallet\RequestPlaced;

// Mail Listeners Imports
use App\Listeners\Mail\SendBulkMail;

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
use App\Listeners\Wallet\CreateAdminRegistrationPaymentFinalizedNotification;
use App\Listeners\Wallet\CreateAffiliateRegistrationPaymentFinalizedNotification;
use App\Listeners\Wallet\CreateUserPaymentProcessedNotification;
use App\Listeners\Wallet\CreateUserRequestPlacedNotification;
use App\Listeners\Wallet\SendAdminRegistrationPaymentFinalizedEmail;
use App\Listeners\Wallet\SendAdminRegistrationPaymentFinalizedPush;
use App\Listeners\Wallet\SendAffiliateRegistrationPaymentFinalizedEmail;
use App\Listeners\Wallet\SendAffiliateRegistrationPaymentFinalizedPush;
use App\Listeners\Wallet\SendUserPaymentProcessedEmail;
use App\Listeners\Wallet\SendUserPaymentProcessedPush;
use App\Listeners\Wallet\SendUserRegistrationPaymentFinalizedEmail;
use App\Listeners\Wallet\SendUserRegistrationPaymentFinalizedPush;
use App\Listeners\Wallet\SendUserRequestPlacedEmail;
use App\Listeners\Wallet\SendUserRequestPlacedPush;

return [

    // Mail Events
    MailSent::class => [
        SendBulkMail::class,
    ],

    // User Events
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

    // Wallet Events
    PaymentProcessed::class => [
        CreateUserPaymentProcessedNotification::class,
        SendUserPaymentProcessedEmail::class,
        SendUserPaymentProcessedPush::class,
    ],

    RequestPlaced::class => [
        CreateUserRequestPlacedNotification::class,
        SendUserRequestPlacedEmail::class,
        SendUserRequestPlacedPush::class,
    ],

];