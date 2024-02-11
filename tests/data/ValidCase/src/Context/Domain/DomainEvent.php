<?php declare(strict_types=1);

namespace Tests\ValidCase\Context\Domain;

use Mage\Bus\AsyncEvent;

class DomainEvent implements AsyncEvent
{
    public function queue(): string
    {
        return 'event';
    }
}
