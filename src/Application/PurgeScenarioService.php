<?php

declare(strict_types=1);

namespace PrismOffice\Application;

use PrismOffice\Domain\Contract\PurgePrismInterface;
use Prism\Domain\Contract\PrismRegistryInterface;
use Prism\Domain\ValueObject\PrismName;
use Prism\Domain\ValueObject\Scope;

/**
 * Use Case : Purger un scénario
 */
final class PurgeScenarioService
{
    public function __construct(
        private readonly PurgePrismInterface $purgePrism,
        private readonly PrismRegistryInterface $prismRegistry
    ) {
    }

    public function execute(string $scenarioName, string $scope): void
    {
        $name = PrismName::fromString($scenarioName);
        $prism = $this->prismRegistry->get($name);

        if ($prism === null) {
            throw new \InvalidArgumentException(
                sprintf('Scenario "%s" not found', $scenarioName)
            );
        }

        $this->purgePrism->execute($prism, Scope::fromString($scope));
    }

    public function executeAllScope(string $scope): void
    {
        $scopeObj = Scope::fromString($scope);

        // Récupérer tous les scénarios enregistrés dans le registry
        $allPrisms = $this->prismRegistry->all();

        // Purger chaque scénario pour ce scope
        foreach ($allPrisms as $prism) {
            $this->purgePrism->execute($prism, $scopeObj);
        }
    }
}
