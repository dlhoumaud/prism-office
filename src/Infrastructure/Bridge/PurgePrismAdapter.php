<?php

declare(strict_types=1);

namespace PrismOffice\Infrastructure\Bridge;

use Prism\Application\UseCase\PurgePrism;
use Prism\Domain\Contract\PrismInterface;
use Prism\Domain\ValueObject\Scope;
use PrismOffice\Domain\Contract\PurgePrismInterface;

/**
 * Adaptateur pour PurgePrism de PrismBundle
 */
final class PurgePrismAdapter implements PurgePrismInterface
{
    public function __construct(
        private readonly PurgePrism $purgePrism
    ) {
    }

    public function execute(PrismInterface $prism, ?Scope $scope = null): void
    {
        $this->purgePrism->execute($prism, $scope ?? Scope::fromString('default'));
    }
}
