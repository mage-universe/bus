<?php declare(strict_types=1);

namespace Tests\Query;

use Error;
use Mage\Bus\Command\QueryBus;
use Mage\Bus\Contracts\Query\Query;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use ReflectionException;
use Tests\AnotherValidCase\Context\Application\AnotherValidQuery;
use Tests\InvalidTypeCase\Context\Application\InvalidQuery;
use Tests\TestCase;
use Tests\ValidCase\Context\Application\ValidQuery;

final class QueryBusTest extends TestCase
{
    #[DefineEnvironment('usesValidBus')]
    public function test_handler_got_called_by_query(): void
    {
        $bus = $this->getQueryBus();
        $this->assertEquals('test', $bus->handle(new ValidQuery()));
        $this->assertEquals(5, $bus->handle(new AnotherValidQuery()));
    }

    #[DefineEnvironment('usesInvalidQueryBus')]
    public function test_query_handler_arguments_not_implement_query_interface(): void
    {
        $queryInterface = Query::class;
        $message = "Method argument in Tests\InvalidTypeCase\Context\Application\InvalidQueryHandler::__invoke()
        implementing $queryInterface not detected";

        $this->expectException(ReflectionException::class);
        $this->expectExceptionMessage(preg_replace('/\s+/', ' ', $message));
        $bus = $this->getQueryBus();
        $bus->handle(new InvalidQuery());
    }

    #[DefineEnvironment('usesEmptyPatternBus')]
    public function test_empty_pattern_produces_error(): void
    {
        $this->expectException(Error::class);
        $bus = $this->getQueryBus();
        $bus->handle(new ValidQuery());
    }

    private function getQueryBus(): QueryBus
    {
        /** @psalm-var QueryBus $queryBus */
        $queryBus = $this->app?->make(QueryBus::class);
        return $queryBus;
    }
}
