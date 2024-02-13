<?php declare(strict_types=1);

namespace Tests\ValidCase\Context\Application;

use Tests\ValidCase\Context\Domain\SyncEvent;

class ValidWithSyncEventCommandHandler implements \Mage\Bus\Event\EventHandler
{
    public function __invoke(SyncEvent $event): void {}
}
