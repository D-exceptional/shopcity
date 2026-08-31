<?php

declare(strict_types=1);

// Import controllers
use App\Http\Controllers\{
    HomeController,
    ProfileController,
    JobController,
    PostController,
};


$router->get('/', [
    HomeController::class,
    'index'
], [], 'home');


$router->get('/u/{username}', [
    ProfileController::class,
    'show'
], [], 'profile.show');


$router->get('/jobs/{slug}', [
    JobController::class,
    'show'
], [], 'jobs.show');


$router->get('/post/{id}', [
    PostController::class,
    'show'
], [], 'posts.show');
