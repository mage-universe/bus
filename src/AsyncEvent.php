<?php declare(strict_types=1);

namespace Mage\Bus;

interface AsyncEvent extends Event
{
    public function queue(): string;
}
