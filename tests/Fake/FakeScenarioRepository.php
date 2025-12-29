<?php

declare(strict_types=1);

namespace Tests\PrismOffice\Fake;

use PrismOffice\Domain\Entity\ScenarioInfo;
use PrismOffice\Domain\Repository\ScenarioRepositoryInterface;

/**
 * Fake repository pour les tests (pas de mock)
 */
final class FakeScenarioRepository implements ScenarioRepositoryInterface
{
    /**
     * @param array<ScenarioInfo> $scenarios
     */
    public function __construct(
        private array $scenarios = []
    ) {
    }

    public function findAll(): iterable
    {
        return $this->scenarios;
    }
}
