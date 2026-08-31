<?php

declare(strict_types=1);

namespace App\Events\Task;

use App\Contracts\EventInterface;

class TaskCreated implements EventInterface
{
    public function __construct() {}
}