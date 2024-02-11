<?php declare(strict_types=1);

namespace Mage\Bus\Command\Middleware;

use Closure;
use Mage\Bus\Command;
use Mage\Bus\Command\Dispatcher;
use Mage\Bus\Query;

readonly class DispatcherMiddleware implements Middleware
{
    public function __construct(private Dispatcher $dispatcher) {}

    public function handle(Command|Query $command, Closure $next): mixed
    {
        return $this->dispatcher->dispatch($command);
    }
}
