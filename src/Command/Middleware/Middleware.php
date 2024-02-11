<?php declare(strict_types=1);

namespace Mage\Bus\Command\Middleware;

use Closure;
use Mage\Bus\Command;
use Mage\Bus\Query;

interface Middleware
{
    public function handle(Command|Query $command, Closure $next): mixed;
}
