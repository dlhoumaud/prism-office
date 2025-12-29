<?php

declare(strict_types=1);

namespace PrismOffice\Application;

use PrismOffice\Domain\Repository\ScenarioRepositoryInterface;

/**
 * Use Case : Lister tous les scénarios disponibles
 */
final class ListScenariosService
{
    public function __construct(
        private readonly ScenarioRepositoryInterface $scenarioRepository
    ) {
    }

    /**
     * @return iterable<\PrismOffice\Domain\Entity\ScenarioInfo>
     */
    public function execute(): iterable
    {
        return $this->scenarioRepository->findAll();
    }
}
