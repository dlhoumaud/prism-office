<?php

declare(strict_types=1);

namespace PrismOffice\Application;

use PrismOffice\Domain\Repository\LoadedScenarioRepositoryInterface;

/**
 * Use Case : Voir les ressources d'un scénario/scope
 */
final class ViewResourcesService
{
    public function __construct(
        private readonly LoadedScenarioRepositoryInterface $loadedScenarioRepository
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function execute(string $scenarioName, string $scope): array
    {
        return $this->loadedScenarioRepository->findResources($scenarioName, $scope);
    }
}
