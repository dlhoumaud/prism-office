<?php

declare(strict_types=1);

namespace PrismOffice\Domain\Entity;

/**
 * Entité représentant un scénario chargé
 */
final class LoadedScenario
{
    public function __construct(
        private readonly string $scenarioName,
        private readonly string $scope,
        private readonly int $resourceCount
    ) {
    }

    public function getScenarioName(): string
    {
        return $this->scenarioName;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getResourceCount(): int
    {
        return $this->resourceCount;
    }

    public function getIdentifier(): string
    {
        return sprintf('%s-%s', $this->scenarioName, $this->scope);
    }
}
