<?php declare(strict_types=1);

namespace Tests\ValidCase\Context\Application;

use Mage\Bus\Event\EventHandler;
use Tests\ValidCase\Context\Domain\DomainEvent;

class DomainEventEventHandler implements EventHandler
{
    public function __invoke(DomainEvent $event) {}
}
