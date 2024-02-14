<?php declare(strict_types=1);

namespace Mage\Bus\Event;

use Mage\Bus\Contracts\Event\Event;

interface Dispatcher
{
    public function dispatch(Event $event): void;
    public function asyncDispatch(Event $event, string $queue): void;
}
