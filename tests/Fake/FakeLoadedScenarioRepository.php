<?php

declare(strict_types=1);

namespace Tests\PrismOffice\Fake;

use PrismOffice\Domain\Entity\LoadedScenario;
use PrismOffice\Domain\Repository\LoadedScenarioRepositoryInterface;

/**
 * Fake repository pour les tests (pas de mock)
 */
final class FakeLoadedScenarioRepository implements LoadedScenarioRepositoryInterface
{
    /**
     * @param array<LoadedScenario> $loadedScenarios
     * @param array<string, array<array{table: string, column: string, value: mixed}>> $resources
     */
    public function __construct(
        private array $loadedScenarios = [],
        private array $resources = []
    ) {
    }

    public function findAllLoaded(): iterable
    {
        return $this->loadedScenarios;
    }

    public function findResources(string $scenarioName, string $scope): array
    {
        $key = sprintf('%s-%s', $scenarioName, $scope);
        return $this->resources[$key] ?? [];
    }
}
