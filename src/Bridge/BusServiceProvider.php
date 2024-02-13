<?php declare(strict_types=1);

namespace Mage\Bus\Bridge;

use Illuminate\Support\ServiceProvider;
use Mage\Bus\Bridge\Dispatcher\CommandDispatcher;
use Mage\Bus\Bridge\Dispatcher\EventDispatcher;
use Mage\Bus\Command\CommandBus;
use Mage\Bus\Command\QueryBus;
use Mage\Bus\Event\EventBus;
use Mage\Bus\Locator\CommandLocator;
use Mage\Bus\Locator\EventLocator;
use Mage\Bus\Locator\QueryLocator;

abstract class BusServiceProvider extends ServiceProvider
{
    abstract protected function commandPaths(): array;

    abstract protected function queryPaths(): array;

    abstract protected function eventPaths(): array;

    abstract protected function commandMiddlewares(): array;

    abstract protected function queryMiddlewares(): array;

    abstract protected function eventMiddlewares(): array;

    public function register(): void
    {
        /** @psalm-var \Illuminate\Bus\Dispatcher $busDispatcher */
        $busDispatcher = $this->app->make(\Illuminate\Bus\Dispatcher::class);

        /** @psalm-var \Illuminate\Events\Dispatcher $eventDispatcher */
        $eventDispatcher = $this->app->make(\Illuminate\Events\Dispatcher::class);

        $this->registerCommandBus($busDispatcher);
        $this->registerQueryBus($busDispatcher);
        $this->registerEventBus($busDispatcher, $eventDispatcher);
    }

    private function registerCommandBus(\Illuminate\Bus\Dispatcher $busDispatcher): void
    {
        $this->app->when(CommandBus::class)->needs(\Mage\Bus\Command\Dispatcher::class)->give(
            fn () => new CommandDispatcher($busDispatcher, new CommandLocator($this->commandPaths()))
        );
        $this->app->when(CommandBus::class)->needs('$middlewares')->give(fn () => $this->commandMiddlewares());
    }
    private function registerQueryBus(\Illuminate\Bus\Dispatcher $busDispatcher): void
    {
        $this->app->when(QueryBus::class)->needs(\Mage\Bus\Command\Dispatcher::class)->give(
            fn () => new CommandDispatcher($busDispatcher, new QueryLocator($this->queryPaths()))
        );
        $this->app->when(QueryBus::class)->needs('$middlewares')->give(fn () => $this->queryMiddlewares());
    }
    private function registerEventBus(
        \Illuminate\Bus\Dispatcher $busDispatcher,
        \Illuminate\Events\Dispatcher $eventDispatcher
    ): void {
        $this->app->bind(\Mage\Bus\Contracts\Event\EventPublisher::class, \Mage\Bus\Event\EventPublisher::class);
        $this->app->when(EventBus::class)->needs(\Mage\Bus\Event\Dispatcher::class)->give(
            fn () => new EventDispatcher($busDispatcher, $eventDispatcher, new EventLocator($this->eventPaths()))
        );
        $this->app->when(EventBus::class)->needs('$middlewares')->give(fn () => $this->eventMiddlewares());
    }
}
