<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\EventDispatcher;

class EventServiceProvider extends ServiceProvider
{
    public function __construct() {}

    public function register(): void
    {
        container()
            ->singleton(
                EventDispatcher::class,
                fn () => new EventDispatcher(
                    //$this->container
                    container()
                )
            );
    }

    public function boot(): void
    {
        $dispatcher = container()
            ->get(EventDispatcher::class);

        $events = config()
            ->get('events', []);

        foreach ($events as $event => $listeners) {

            foreach ($listeners as $listener) {

                $dispatcher->listen(
                    $event,
                    $listener
                );
            }
        }
    }
}