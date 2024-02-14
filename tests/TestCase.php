<?php declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Application;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\Data\EmptyPatternBusServiceProvider;
use Tests\Data\InvalidBusServiceProvider;
use Tests\Data\ValidBusServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use MockeryPHPUnitIntegration;

    /** @psalm-api */
    protected function usesValidBus(Application $app): void
    {
        $app->register(ValidBusServiceProvider::class);
    }

    /** @psalm-api */
    protected function usesInvalidQueryBus(Application $app): void
    {
        $app->register(InvalidBusServiceProvider::class);
    }

    /** @psalm-api */
    protected function usesEmptyPatternBus(Application $app): void
    {
        $app->register(EmptyPatternBusServiceProvider::class);
    }
}
