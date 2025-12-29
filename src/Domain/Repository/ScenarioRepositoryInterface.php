<?php

declare(strict_types=1);

namespace PrismOffice\Domain\Repository;

/**
 * Repository pour gérer les scénarios disponibles
 */
interface ScenarioRepositoryInterface
{
    /**
     * Récupère la liste de tous les scénarios disponibles
     *
     * @return iterable<\PrismOffice\Domain\Entity\ScenarioInfo>
     */
    public function findAll(): iterable;
}
