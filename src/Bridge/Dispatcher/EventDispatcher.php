<?php declare(strict_types=1);

namespace Mage\Bus\Bridge\Dispatcher;

use Mage\Bus\Bridge\Job\EventAsync;
use Mage\Bus\Contracts\Event\Event;
use Mage\Bus\Event\Dispatcher;
use Mage\Bus\Locator\Locator;
use function Lambdish\Phunctional\each;

final readonly class EventDispatcher implements Dispatcher
{
    public function __construct(
        private \Illuminate\Contracts\Bus\Dispatcher $busDispatcher,
        private \Illuminate\Contracts\Events\Dispatcher $eventDispatcher,
        Locator $locator
    ) {
        each(function (array $handlers, string $event) {
            if (!$this->eventDispatcher->hasListeners($event)) {
                each(fn (string $handler) => $this->eventDispatcher->listen($event, $handler), $handlers);
            }
        }, $locator->mappings());
    }

    public function dispatch(Event $event): void
    {
        $this->eventDispatcher->dispatch($event);
    }

    public function asyncDispatch(Event $event, string $queue): void
    {
        $this->busDispatcher->dispatch(new EventAsync($event, $queue));
    }
}
