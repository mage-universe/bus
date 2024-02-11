<?php declare(strict_types=1);

namespace Tests\Command;

use Illuminate\Support\Facades\Queue;
use Mage\Bus\Bridge\Dispatcher\CommandDispatcher;
use Mage\Bus\Bridge\Dispatcher\EventDispatcher;
use Mage\Bus\Bridge\Job\CommandAsync;
use Mage\Bus\Bridge\Job\EventAsync;
use Mage\Bus\Command\CommandBus;
use Mage\Bus\Event\EventBus;
use Mage\Bus\Event\EventPublisher;
use Mage\Bus\Locator\CommandLocator;
use Mage\Bus\Locator\EventLocator;
use Tests\AnotherValidCase\Context\Application\AnotherDomainEventEventHandler;
use Tests\AnotherValidCase\Context\Domain\AnotherDomainEvent;
use Tests\TestCase;
use Tests\ValidCase\Context\Application\DomainEventEventHandler;
use Tests\ValidCase\Context\Application\ValidCommand;
use Tests\ValidCase\Context\Application\ValidCommandHandler;
use Tests\ValidCase\Context\Application\ValidWithEventCommand;
use Tests\ValidCase\Context\Application\ValidWithSyncEventCommandHandler;
use Tests\ValidCase\Context\Domain\DomainEvent;
use Tests\ValidCase\Context\Domain\SyncEvent;

final class CommandBusTest extends TestCase
{
    public function test(): void
    {
        $spy = $this->spy(ValidCommandHandler::class);
        $bus = $this->getValidCommandBus();
        $bus->handle(new ValidCommand());
        $spy->shouldHaveReceived('__invoke');
    }

    public function test2(): void
    {
        Queue::fake();
        $bus = $this->getValidCommandBus();
        $bus->asyncHandle(new ValidCommand());
        Queue::assertPushedOn('command', CommandAsync::class);
    }

    public function test3(): void
    {
        $spy = $this->spy(ValidCommandHandler::class);
        $bus = $this->getValidCommandBus();
        $commandAsync = new CommandAsync(new ValidCommand(), 'command');
        $commandAsync->handle($bus);

        $spy->shouldHaveReceived('__invoke');
        $this->assertEquals(ValidCommand::class, $commandAsync->displayName());
    }

    public function test4(): void
    {
        $this->getValidEventBus();
        Queue::fake();
        $bus = $this->getValidCommandBus();
        $bus->handle(new ValidWithEventCommand());
        Queue::assertPushedOn('event', EventAsync::class);
    }

    public function test6(): void
    {
        $spy = $this->spy(DomainEventEventHandler::class);
        $bus = $this->getValidEventBus();
        $commandAsync = new EventAsync(new DomainEvent(), 'event');
        $commandAsync->handle($bus);
        $spy->shouldHaveReceived('__invoke');
    }

    public function test7(): void
    {
        Queue::fake();
        $bus = $this->getValidEventBus();
        $eventPublisher = new EventPublisher($bus);
        $eventPublisher->publish(new DomainEvent());
        Queue::assertPushedOn('event', EventAsync::class);
    }

    public function test5(): void
    {
        $bus = $this->getValidEventBus();

        $spy = $this->spy(DomainEventEventHandler::class);
        $bus->dispatch(new DomainEvent());
        $spy->shouldHaveReceived('__invoke');

        $spy = $this->spy(AnotherDomainEventEventHandler::class);
        $bus->dispatch(new AnotherDomainEvent());
        $spy->shouldHaveReceived('__invoke');
    }

    public function test8(): void
    {
        $bus = $this->getValidEventBus();
        $spy = $this->spy(ValidWithSyncEventCommandHandler::class);
        $bus->dispatch(new SyncEvent());
        $spy->shouldHaveReceived('__invoke');
    }

    private function getValidCommandBus(): CommandBus
    {
        return new CommandBus($this->getCommandDispatcher(), []);
    }

    private function getCommandDispatcher(): CommandDispatcher
    {
        /** @psalm-var \Illuminate\Bus\Dispatcher $busDispatcher */
        $busDispatcher = $this->app->make(\Illuminate\Contracts\Bus\Dispatcher::class);

        return new CommandDispatcher(
            $busDispatcher,
            new CommandLocator([[
                'path' => dirname(__DIR__) . '/data/ValidCase',
                'pattern' => '/.*\/Application\/.*/',
            ]])
        );
    }

    private function getValidEventBus(): EventBus
    {
        $this->app->bind(\Mage\Bus\EventPublisher::class, EventPublisher::class);

        /** @psalm-var \Illuminate\Bus\Dispatcher $busDispatcher */
        $busDispatcher = $this->app->make(\Illuminate\Contracts\Bus\Dispatcher::class);
        /** @psalm-var \Illuminate\Events\Dispatcher $eventDispatcher */
        $eventDispatcher = $this->app->make(\Illuminate\Contracts\Events\Dispatcher::class);

        $dispatcher = new EventDispatcher(
            $busDispatcher,
            $eventDispatcher,
            new EventLocator([[
                'path' => dirname(__DIR__) . '/data/ValidCase',
                'pattern' => '/.*\/Application\/.*/',
            ], [
                'path' => dirname(__DIR__) . '/data/AnotherValidCase',
                'pattern' => '/.*\/Application\/.*/',
            ]])
        );

        $this->app
            ->when(EventBus::class)
            ->needs(\Mage\Bus\Event\Dispatcher::class)
            ->give(fn () => $dispatcher);
        $this->app
            ->when(EventBus::class)
            ->needs('$middlewares')
            ->give([]);

        return new EventBus($dispatcher, []);
    }
}
