<?php declare(strict_types=1);

namespace Mage\Bus\Command;

use Mage\Bus\Contracts\Command\Command;
use Mage\Bus\Contracts\Query\Query;

interface Dispatcher
{
    public function dispatch(Command|Query $command): mixed;
    public function asyncDispatch(Command $command, string $queue): void;
}
