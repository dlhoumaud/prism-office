<?php

declare(strict_types=1);

namespace PrismOffice\Application;

use PrismOffice\Domain\Contract\LoadPrismInterface;
use Prism\Domain\Contract\PrismRegistryInterface;
use Prism\Domain\ValueObject\PrismName;
use Prism\Domain\ValueObject\Scope;

/**
 * Use Case : Charger un scénario
 */
final class LoadScenarioService
{
    public function __construct(
        private readonly LoadPrismInterface $loadPrism,
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

        $this->loadPrism->execute($prism, Scope::fromString($scope));
    }
}
