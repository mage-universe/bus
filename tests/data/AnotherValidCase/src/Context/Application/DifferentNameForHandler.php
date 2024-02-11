<?php declare(strict_types=1);

namespace Tests\AnotherValidCase\Context\Application;

use Mage\Bus\Command\QueryHandler;

/** @psalm-api */
final class DifferentNameForHandler implements QueryHandler
{
    public function __invoke(AnotherValidQuery $query): int
    {
        return 5;
    }
}
