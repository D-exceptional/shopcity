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
    public function register(): void
    {
        container()
            ->singleton(
                View::class
            );
    }

    public function boot(): void
    {
        $view = container()
            ->get(View::class);

        $this->log('VIEW PROVIDER BOOTED');
        $this->log('VIEW OBJECT: ' . spl_object_id($view));

        $session = container()
            ->get(SessionInterface::class);

        $cache = container()
            ->get(CacheInterface::class);

        $currencyManager = container()
            ->get(CurrencyManager::class);

        $rateManager = container()
            ->get(RateManager::class);

        $bankManager = container()
            ->get(BankManager::class);

        $numberManager = container()
            ->get(NumberManager::class);

        $timeManager = container()
            ->get(TimeManager::class);

        $categoryModel = container()
            ->get(Category::class);

        $cartModel = container()
            ->get(Cart::class);

        $userModel = container()
            ->get(User::class);

        $notificationService = container()
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

        // Login State Checker
        $isLoggedIn = $session->validate(7200, 1800);

        // User Data
        $user = null;
        $userId = null;

        if ($isLoggedIn) {
            $user = $session->user();
            $userId = $user['id'] ?? null;
            $email = $user['email'];

            // Get name
            $fullName = $user['name'];
            $nameParts = explode(' ', $fullName);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1];

            // Get profile
            $avatar = $userModel->getProfile($userId);
            $profile = ($avatar === 'None')
                ? asset('img/avatar.jpg')
                : $avatar;
        }

        // Public Pages Cart
        $cartCount = $isLoggedIn && $userId !== null
            ? $cartModel->countCart($userId)
            : 0;

        // Dashboard Notifications
        $notifications = [];

        if ($isLoggedIn && $userId !== null) {
            $notifications =
                $notificationService->summary($userId);
        }

        // Get App Data
        $appName = config('app.name');
        $appUrl = config('app.url');

        $this->log('APP NAME SHARED: ' . ($appName ?? 'NULL'));

        // -----------------------------------------
        // SHARE VIEW DATA GLOBALLY ACROSS APP
        // -----------------------------------------

        $view->share('appName', $appName);
        $view->share('appUrl', $appUrl);
        $view->share('csrfToken', $csrfToken);
        $view->share('currencyManager', $currencyManager);
        $view->share('rateManager', $rateManager);
        $view->share('bankManager', $bankManager);
        $view->share('numberManager', $numberManager);
        $view->share('timeManager', $timeManager);
        $view->share('notifications', $notifications);
        $view->share('isLoggedIn', $isLoggedIn);
        $view->share('userId', $userId);
        $view->share('email', $email ?? null);
        $view->share('fullName', $fullName ?? null);
        $view->share('firstName', $firstName ?? null);
        $view->share('lastName', $lastName ?? null);
        $view->share('avatar', $avatar ?? null);
        $view->share('profile', $profile ?? null);
        $view->share(
            'allCategories',
            $allCategories ?? []
        );
        $view->share(
            'groupedCategories',
            $groupedCategories ?? []
        );
        $view->share(
            'cartCount',
            $cartCount ?? 0
        );

        $this->log('VIEW SHARED DATA: ' . json_encode($view, true));
    }
}