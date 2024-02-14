<?php declare(strict_types=1);

namespace Tests\InvalidTypeCase\Context\Application;

/** @psalm-api */
final class InvalidQueryHandler implements \Mage\Bus\Contracts\Query\QueryHandler
{
    /** @psalm-param mixed $query */
    public function __invoke($query): mixed
    {
        return 2 * 2;
    }
}
