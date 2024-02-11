<?php declare(strict_types=1);

namespace Mage\Bus\Event;

use Mage\Bus\Event;
use function Lambdish\Phunctional\each;

final readonly class EventPublisher implements \Mage\Bus\EventPublisher
{
    public function __construct(private EventBus $eventBus) {}

    /** @psalm-param Event|array<Event> $events */
    public function publish(array|Event $events): void
    {
        $events = !is_array($events) ? [$events] : $events;
        each(fn (Event $event) => $this->eventBus->dispatch($event), $events);
    }
}
