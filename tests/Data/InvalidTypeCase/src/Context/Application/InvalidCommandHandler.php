<?php declare(strict_types=1);

namespace Tests\InvalidTypeCase\Context\Application;

/** @psalm-api */
final class InvalidCommandHandler implements \Mage\Bus\Contracts\Command\CommandHandler
{
    public function __invoke(InvalidCommand $command): void {}
}
