<?php declare(strict_types=1);

namespace Mage\Bus\Command;

use Mage\Bus\Command;
use Mage\Bus\Query;

interface Dispatcher
{
    public function dispatch(Command|Query $command): mixed;
    public function asyncDispatch(Command $command, string $queue): void;
}
