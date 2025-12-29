<?php

declare(strict_types=1);

namespace PrismOffice\Domain\Repository;

/**
 * Repository pour gérer les scénarios chargés
 */
interface LoadedScenarioRepositoryInterface
{
    /**
     * Récupère tous les scénarios chargés (groupés par scenario_name et scope)
     *
     * @return iterable<\PrismOffice\Domain\Entity\LoadedScenario>
     */
    public function findAllLoaded(): iterable;

    /**
     * Récupère les ressources d'un scénario/scope spécifique
     *
     * @return array<int, array<string, mixed>>
     */
    public function findResources(string $scenarioName, string $scope): array;
}
