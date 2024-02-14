<?php declare(strict_types=1);

namespace Mage\Bus\Locator;

use Mage\Bus\Contracts\Event\Event;
use Mage\Bus\Contracts\Event\EventHandler;
use ReflectionClass;

class EventLocator extends Locator
{
    public function __construct(array $paths)
    {
        parent::__construct($paths, Event::class, EventHandler::class);
    }

    protected function setMapping(ReflectionClass $class, array $acc): array
    {
        /** @psalm-var array<string, array<string>> $acc */
        $acc[$this->commandType($class)->getName()][] = $class->getName();
        return $acc;
    }
}
