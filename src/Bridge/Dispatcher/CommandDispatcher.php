<?php declare(strict_types=1);

namespace Mage\Bus\Bridge\Dispatcher;

use Mage\Bus\Bridge\Job\CommandAsync;
use Mage\Bus\Command;
use Mage\Bus\Command\Dispatcher;
use Mage\Bus\Locator\Locator;
use Mage\Bus\Query;

final readonly class CommandDispatcher implements Dispatcher
{
    public function __construct(
        private \Illuminate\Contracts\Bus\Dispatcher $dispatcher,
        Locator $locator
    ) {
        $this->dispatcher->map($locator->mappings());
    }

    public function dispatch(Command|Query $command): mixed
    {
        return $this->dispatcher->dispatch($command);
    }

    public function asyncDispatch(Command $command, string $queue): void
    {
        $this->dispatcher->dispatch(new CommandAsync($command, $queue));
    }
}
