<?php

declare(strict_types=1);

namespace App\Providers;

abstract class ServiceProvider
{
    /**
     * Register services into the container.
     */
    public function register(): void
    {}

    /**
     * Boot services after all providers
     * have been registered.
     */
    public function boot(): void
    {}
}