<?php declare(strict_types=1);

namespace Tests\Query;

use Error;
use Mage\Bus\Bridge\Dispatcher\CommandDispatcher;
use Mage\Bus\Command\QueryBus;
use Mage\Bus\Locator\QueryLocator;
use ReflectionException;
use Tests\AnotherValidCase\Context\Application\AnotherValidQuery;
use Tests\InvalidTypeCase\Context\Application\InvalidQuery;
use Tests\TestCase;
use Tests\ValidCase\Context\Application\ValidQuery;

final class QueryBusTest extends TestCase
{
    public function test(): void
    {
        $bus = $this->getValidQueryBus();
        $this->assertEquals('test', $bus->handle(new ValidQuery()));
        $this->assertEquals(5, $bus->handle(new AnotherValidQuery()));
    }

    public function test2(): void
    {
        $message = 'Method argument in Tests\InvalidTypeCase\Context\Application\InvalidQueryHandler::__invoke()
         implementing Mage\Bus\Query not detected';

        $this->expectException(ReflectionException::class);
        $this->expectExceptionMessage(preg_replace('/\s+/', ' ', $message));
        $bus = $this->getInvalidQueryBus();
        $bus->handle(new InvalidQuery());
    }

    public function test3(): void
    {
        $this->expectException(Error::class);
        $bus = $this->getEmptyPatternQueryBus();
        $bus->handle(new ValidQuery());
    }

    private function getValidQueryBus(): QueryBus
    {
        return $this->getQueryBus([[
            'path' => dirname(__DIR__) . '/data/ValidCase',
            'pattern' => '/.*\/Application\/.*/',
        ], [
            'path' => dirname(__DIR__) . '/data/AnotherValidCase',
            'pattern' => '/.*\/Application\/.*/',
        ]]);
    }

    private function getInvalidQueryBus(): QueryBus
    {
        return $this->getQueryBus([[
            'path' => dirname(__DIR__) . '/data/InvalidTypeCase',
            'pattern' => '/.*\/Application\/.*/',
        ]]);
    }

    private function getEmptyPatternQueryBus(): QueryBus
    {
        return $this->getQueryBus([[
            'path' => dirname(__DIR__) . '/data/InvalidTypeCase',
            'pattern' => '',
        ]]);
    }

    private function getQueryBus(array $paths): QueryBus
    {
        /** @psalm-var \Illuminate\Bus\Dispatcher $busDispatcher */
        $busDispatcher = $this->app->make(\Illuminate\Contracts\Bus\Dispatcher::class);

        $dispatcher = new CommandDispatcher($busDispatcher, new QueryLocator($paths));
        return new QueryBus($dispatcher, []);
    }
}
