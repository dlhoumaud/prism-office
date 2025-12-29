<?php

declare(strict_types=1);

namespace PrismOffice\Domain\Contract;

use Prism\Domain\Contract\PrismInterface;
use Prism\Domain\ValueObject\Scope;

/**
 * Interface pour le use case PurgePrism
 */
interface PurgePrismInterface
{
    public function execute(PrismInterface $prism, ?Scope $scope = null): void;
}
