<?php

declare(strict_types=1);

namespace PrismOffice\Infrastructure\Bridge;

use Prism\Application\UseCase\LoadPrism;
use Prism\Domain\Contract\PrismInterface;
use Prism\Domain\ValueObject\Scope;
use PrismOffice\Domain\Contract\LoadPrismInterface;

/**
 * Adaptateur pour LoadPrism de PrismBundle
 */
final class LoadPrismAdapter implements LoadPrismInterface
{
    public function __construct(
        private readonly LoadPrism $loadPrism
    ) {
    }

    public function execute(PrismInterface $prism, ?Scope $scope = null): void
    {
        $this->loadPrism->execute($prism, $scope ?? Scope::fromString('default'));
    }
}
