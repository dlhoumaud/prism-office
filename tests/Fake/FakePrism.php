<?php

declare(strict_types=1);

namespace Tests\PrismOffice\Fake;

use Prism\Domain\Contract\PrismInterface;
use Prism\Domain\ValueObject\PrismName;
use Prism\Domain\ValueObject\Scope;

/**
 * Fake Prism pour les tests (pas de mock)
 */
final class FakePrism implements PrismInterface
{
    public function __construct(
        private readonly string $name
    ) {
    }

    public function getName(): PrismName
    {
        return PrismName::fromString($this->name);
    }

    public function load(Scope $scope): void
    {
        // Fake implementation - does nothing
    }

    public function purge(Scope $scope): void
    {
        // Fake implementation - does nothing
    }
}
