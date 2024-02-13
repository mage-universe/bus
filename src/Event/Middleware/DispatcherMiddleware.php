<?php declare(strict_types=1);

namespace Mage\Bus\Event\Middleware;

use Closure;
use Mage\Bus\Contracts\Event\Event;
use Mage\Bus\Event\Dispatcher;

readonly class DispatcherMiddleware implements Middleware
{
    public function __construct(private Dispatcher $dispatcher) {}

    public function handle(Event $event, Closure $next): mixed
    {
        return $this->dispatcher->dispatch($event);
    }
}
