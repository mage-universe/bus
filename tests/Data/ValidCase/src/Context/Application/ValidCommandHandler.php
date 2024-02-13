<?php declare(strict_types=1);

namespace Tests\ValidCase\Context\Application;

class ValidCommandHandler implements \Mage\Bus\Contracts\Command\CommandHandler
{
    public function __invoke(ValidCommand $command): void {}
}
