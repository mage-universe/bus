<?php declare(strict_types=1);

namespace Mage\Bus\Command;

use Mage\Bus\Contracts\Query\Query;

final class QueryBus extends Bus
{
    public function handle(Query $query): mixed
    {
        return $this->dispatch($query);
    }
}
