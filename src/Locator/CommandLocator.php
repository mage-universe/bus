<?php declare(strict_types=1);

namespace Mage\Bus\Locator;

use Mage\Bus\Contracts\Command\Command;
use Mage\Bus\Contracts\Command\CommandHandler;

class CommandLocator extends Locator
{
    public function __construct(array $paths)
    {
        parent::__construct($paths, Command::class, CommandHandler::class);
    }
}
