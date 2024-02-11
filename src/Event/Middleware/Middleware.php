<?php declare(strict_types=1);

namespace Mage\Bus\Event\Middleware;

use Closure;
use Mage\Bus\Event;

interface Middleware
{
    public function handle(Event $event, Closure $next): mixed;
}
