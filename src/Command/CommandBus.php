<?php declare(strict_types=1);

namespace Mage\Bus\Command;

use Mage\Bus\Command;

final class CommandBus extends Bus
{
    public function handle(Command $command): void
    {
        $this->dispatch($command);
    }

    public function asyncHandle(Command $command, string $queue = 'command'): void
    {
        $this->asyncDispatch($command, $queue);
    }
}
