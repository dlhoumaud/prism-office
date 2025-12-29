<?php

declare(strict_types=1);

namespace PrismOffice\Infrastructure\Doctrine;

use Doctrine\DBAL\Connection;
use Prism\Domain\Contract\PrismResourceTrackerInterface;
use Prism\Domain\ValueObject\PrismName;
use Prism\Domain\ValueObject\Scope;
use PrismOffice\Domain\Entity\LoadedScenario;
use PrismOffice\Domain\Repository\LoadedScenarioRepositoryInterface;

/**
 * Repository Doctrine DBAL pour les scénarios chargés
 */
final class DoctrineLoadedScenarioRepository implements LoadedScenarioRepositoryInterface
{
    private const TABLE_NAME = 'prism_resource';

    public function __construct(
        private readonly Connection $connection,
        private readonly PrismResourceTrackerInterface $tracker
    ) {
    }

    /**
     * @return iterable<LoadedScenario>
     */
    public function findAllLoaded(): iterable
    {
        // Force la création de la table si elle n'existe pas
        $this->ensureTableExists();

        $qb = $this->connection->createQueryBuilder();

        $result = $qb
            ->select(
                'prism_name',
                'scope',
                'COUNT(*) as resource_count'
            )
            ->from(self::TABLE_NAME)
            ->groupBy('prism_name', 'scope')
            ->orderBy('prism_name', 'ASC')
            ->addOrderBy('scope', 'ASC')
            ->executeQuery();

        $loadedScenarios = [];
        foreach ($result->fetchAllAssociative() as $row) {
            $scenarioName = is_string($row['prism_name'] ?? null) ? $row['prism_name'] : '';
            $scope = (string) ($row['scope'] ?? '');
            $resourceCount = is_numeric($row['resource_count'] ?? null) ? (int) $row['resource_count'] : 0;

            $loadedScenarios[] = new LoadedScenario(
                scenarioName: $scenarioName,
                scope: $scope,
                resourceCount: $resourceCount
            );
        }

        return $loadedScenarios;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findResources(string $scenarioName, string $scope): array
    {
        // Force la création de la table si elle n'existe pas
        $this->ensureTableExists();

        $qb = $this->connection->createQueryBuilder();

        $result = $qb
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where('prism_name = :prism_name')
            ->andWhere('scope = :scope')
            ->orderBy('created_at', 'DESC')
            ->setParameter('prism_name', $scenarioName)
            ->setParameter('scope', $scope)
            ->executeQuery();

        return $result->fetchAllAssociative();
    }

    /**
     * Force la création de la table prism_resource via le tracker
     */
    private function ensureTableExists(): void
    {
        // Utiliser le tracker pour vérifier/créer la table
        // On fait une requête factice pour déclencher ensureTableExists()
        try {
            $this->tracker->findByPrismAndScope(
                PrismName::fromString('_init'),
                Scope::fromString('_init')
            );
        } catch (\Throwable $e) {
            // Ignorer les erreurs - la table est maintenant créée
        }
    }
}
