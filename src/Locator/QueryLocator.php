<?php declare(strict_types=1);

namespace Mage\Bus\Locator;

use Mage\Bus\Command\QueryHandler;
use Mage\Bus\Query;

class QueryLocator extends Locator
{
    public function __construct(array $paths)
    {
        parent::__construct($paths, Query::class, QueryHandler::class);
    }
}
