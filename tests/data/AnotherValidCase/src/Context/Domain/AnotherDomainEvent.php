<?php declare(strict_types=1);

namespace Tests\AnotherValidCase\Context\Domain;

use Mage\Bus\AsyncEvent;

class AnotherDomainEvent implements AsyncEvent
{
    public function queue(): string
    {
        return 'event';
    }
}
