<?php declare(strict_types=1);

namespace Tests\AnotherValidCase\Context\Application;

use Mage\Bus\Event\EventHandler;
use Tests\AnotherValidCase\Context\Domain\AnotherDomainEvent;

class AnotherDomainEventEventHandler implements EventHandler
{
    public function __invoke(AnotherDomainEvent $event) {}
}
