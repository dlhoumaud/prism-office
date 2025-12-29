<?php

declare(strict_types=1);

namespace PrismOffice\Application;

use PrismOffice\Domain\Repository\LoadedScenarioRepositoryInterface;

/**
 * Use Case : Lister tous les scénarios chargés
 */
final class ListLoadedScenariosService
{
    public function __construct(
        private readonly LoadedScenarioRepositoryInterface $loadedScenarioRepository
    ) {
    }

    /**
     * @return iterable<\PrismOffice\Domain\Entity\LoadedScenario>
     */
    public function execute(): iterable
    {
        return $this->loadedScenarioRepository->findAllLoaded();
    }
}
