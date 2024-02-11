<?php declare(strict_types=1);

namespace Tests\ValidCase\Context\Application;

/** @psalm-api */
final class ValidQueryHandler implements \Mage\Bus\Command\QueryHandler
{
    public function __invoke(ValidQuery $query): mixed
    {
        return 'test';
    }
}
