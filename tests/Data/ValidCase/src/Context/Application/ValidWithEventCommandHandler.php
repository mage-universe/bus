<?php declare(strict_types=1);

namespace Tests\ValidCase\Context\Application;

use Mage\Bus\Contracts\Event\EventPublisher;
use Tests\ValidCase\Context\Domain\DomainEvent;

/** @psalm-api */
class ValidWithEventCommandHandler implements \Mage\Bus\Contracts\Command\CommandHandler
{
    public function __construct(private EventPublisher $event) {}
    public function __invoke(ValidWithEventCommand $command): void
    {
        $this->event->publish(new DomainEvent());
    }
}
