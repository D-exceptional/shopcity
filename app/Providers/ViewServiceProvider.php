<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\View;
use App\Contracts\SessionInterface;
use App\Contracts\CacheInterface;
use App\Support\CurrencyManager;
use App\Support\RateManager;
use App\Support\BankManager;
use App\Support\NumberManager;
use App\Support\TimeManager;
use App\Models\Category;
use App\Models\Cart;
use App\Models\User;
use App\Services\Web\NotificationService; 

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $view = $this->container()
            ->get(View::class);

        $session = $this->container()
            ->get(SessionInterface::class);

        $cache = $this->container()
            ->get(CacheInterface::class);

        $currencyManager = $this->container()
            ->get(CurrencyManager::class);

        $rateManager = $this->container()
            ->get(RateManager::class);

        $bankManager = $this->container()
            ->get(BankManager::class);

        $numberManager = $this->container()
            ->get(NumberManager::class);

        $timeManager = $this->container()
            ->get(TimeManager::class);

        $categoryModel = $this->container()
            ->get(Category::class);

        $cartModel = $this->container()
            ->get(Cart::class);

        $userModel = $this->container()
            ->get(User::class);

        $notificationService = $this->container()
            ->get(NotificationService::class);

        // CSRF Token
        $csrfToken = $session->token();

        // Public Pages Categories
        $allCategories = $cache->remember(
            'categories.all',
            fn () => $categoryModel->all(),
            3600
        );

        $groupedCategories = $cache->remember(
            'categories.grouped',
            fn () => $categoryModel->group(),
            3600
        );

        // Public Pages Cart
        $cartCount = $isLoggedIn && $userId !== null
            ? $cartModel->countCart($userId)
            : 0;

        // Login State Checker
        $isLoggedIn = $session->validate(7200, 1800);

        // User Data
        $user = null;
        $userId = null;

        if ($isLoggedIn) {
            $user   = $session->user();
            $userId = $user['id'] ?? null;
            $email  = $user['email'];

            // Get name
            $fullName  = $user['name'];
            $nameParts = explode(' ', $fullName);
            $firstName = $nameParts[0];
            $lastName  = $nameParts[1];

            // Get profile
            $avatar  = $userModel->getProfile($userId);
            $profile = ($avatar === 'None') ? asset('img/avatar.jpg') : $avatar;
        }

        // Dashboard Notifications
        $notifications = [];

        if ($isLoggedIn && $userId !== null) {
            $notifications = $notificationService->summary($userId);
        }

        // -----------------------------------------
        // SHARE VIEW DATA GLOBALLY ACROSS APP
        // ----------------------------------------

        // Global Shared Data
        $view->share('appName', config('app.name'));
        $view->share('appUrl', config('app.url'));
        $view->share('csrfToken', $csrfToken);
        $view->share('currencyManager', $currencyManager);
        $view->share('rateManager', $rateManager);
        $view->share('bankManager', $bankManager);
        $view->share('numberManager', $numberManager);
        $view->share('timeManager', $timeManager);
        $view->share('notifications', $notifications);
        $view->share('isLoggedIn', $isLoggedIn);
        // $view->share('user', $user);
        $view->share('userId', $userId);
        $view->share('email', $email ?? null);
        $view->share('fullName', $fullName ?? null);
        $view->share('firstName', $firstName ?? null);
        $view->share('lastName', $lastName ?? null);
        $view->share('avatar', $avatar ?? null);
        $view->share('profile', $profile ?? null);
        // Public Pages Shared Data
        $view->share('allCategories', $allCategories);
        $view->share('groupedCategories', $groupedCategories);
        $view->share('cartCount', $cartCount);
    }
}