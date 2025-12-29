<?php

declare(strict_types=1);

namespace PrismOffice\Infrastructure\Bridge;

use Prism\Domain\Contract\PrismRegistryInterface;
use PrismOffice\Domain\Entity\ScenarioInfo;
use PrismOffice\Domain\Repository\ScenarioRepositoryInterface;

/**
 * Bridge vers PrismBundle Registry
 *
 * Adaptateur permettant à PrismOffice d'accéder aux scénarios du PrismBundle
 */
final class PrismBundleBridge implements ScenarioRepositoryInterface
{
    public function __construct(
        private readonly PrismRegistryInterface $prismRegistry
    ) {
    }

    /**
     * @return iterable<ScenarioInfo>
     */
    public function findAll(): iterable
    {
        $prisms = $this->prismRegistry->all();

        $scenarioInfos = [];
        foreach ($prisms as $prism) {
            $scenarioInfos[] = new ScenarioInfo(
                name: $prism->getName()->toString(),
                className: $prism::class
            );
        }

        return $scenarioInfos;
    }
}
