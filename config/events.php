<?php

declare(strict_types=1);

// Blog Events Imports
use App\Events\Blog\BlogCreated;
use App\Events\Blog\BannerUpdated;
use App\Events\Blog\BlogStatusUpdated;

// Mail Events Imports
use App\Events\Mail\MailSent;

// Task Events Imports
use App\Events\Task\TaskCreated;
use App\Events\Task\TaskStatusUpdated;

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

// Blog Listeners Imports
use App\Listeners\Blog\CreateAdminBlogCreationNotification;
use App\Listeners\Blog\SendAdminBlogCreationEmail;
use App\Listeners\Blog\SendAdminBlogCreationPush;
use App\Listeners\Blog\SendAuthorBlogCreationEmail;
use App\Listeners\Blog\SendAuthorBlogCreationPush;
use App\Listeners\Blog\SendAuthorBlogStatusUpdatedEmail;
use App\Listeners\Blog\SendAuthorBlogStatusUpdatedPush;
use App\Listeners\Blog\UpdateBanner;

// Mail Listeners Imports
use App\Listeners\Mail\SendBulkMail;

// Task Listeners Imports
use App\Listeners\Task\CreateAdminTaskCreationNotification;
use App\Listeners\Task\SendAdminTaskCreationEmail;
use App\Listeners\Task\SendAdminTaskCreationPush;
use App\Listeners\Task\SendUserTaskStatusUpdatedEmail;
use App\Listeners\Task\SendUserTaskStatusUpdatedPush;

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

    // Blog Events
    BlogCreated::class => [
        CreateAdminBlogCreationNotification::class,
        SendAdminBlogCreationEmail::class,
        SendAdminBlogCreationPush::class,
        SendAuthorBlogCreationEmail::class,
        SendAuthorBlogCreationPush::class,
    ],

    BannerUpdated::class => [
        UpdateBanner::class,
    ],

    BlogStatusUpdated::class => [
        SendAuthorBlogStatusUpdatedEmail::class,
        SendAuthorBlogStatusUpdatedPush::class,
    ],

    // Mail Events
    MailSent::class => [
        SendBulkMail::class,
    ],

    // Task Events
    TaskCreated::class => [
        CreateAdminTaskCreationNotification::class,
        SendAdminTaskCreationEmail::class,
        SendAdminTaskCreationPush::class,
    ],

    TaskStatusUpdated::class => [
        SendUserTaskStatusUpdatedEmail::class,
        SendUserTaskStatusUpdatedPush::class,
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

    RegistrationPaymentFinalized::class => [
        CreateAdminRegistrationPaymentFinalizedNotification::class,
        CreateAffiliateRegistrationPaymentFinalizedNotification::class,
        SendAdminRegistrationPaymentFinalizedEmail::class,
        SendAdminRegistrationPaymentFinalizedPush::class,
        SendAffiliateRegistrationPaymentFinalizedEmail::class,
        SendAffiliateRegistrationPaymentFinalizedPush::class,
        SendUserRegistrationPaymentFinalizedEmail::class,
        SendUserRegistrationPaymentFinalizedPush::class,
    ],

    RequestPlaced::class => [
        CreateUserRequestPlacedNotification::class,
        SendUserRequestPlacedEmail::class,
        SendUserRequestPlacedPush::class,
    ],

];