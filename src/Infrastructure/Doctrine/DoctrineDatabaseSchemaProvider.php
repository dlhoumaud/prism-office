<?php

declare(strict_types=1);

namespace PrismOffice\Infrastructure\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\Persistence\ManagerRegistry;
use PrismOffice\Application\Contract\DatabaseSchemaProviderInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Doctrine implementation of database schema provider
 */
final class DoctrineDatabaseSchemaProvider implements DatabaseSchemaProviderInterface
{
    private const CACHE_TTL = 86400; // 24 heures
    private const CACHE_KEY_PREFIX = 'prism_db_schema_';

    /**
     * @param array<string, string> $databaseAliases Map des alias vers noms de BDD (ex: ['logiciel' => 'safti_omega', ...])
     */
    public function __construct(
        private readonly Connection $connection,
        private readonly ?ManagerRegistry $managerRegistry = null,
        private readonly array $databaseAliases = [],
        private readonly ?CacheItemPoolInterface $cache = null
    ) {
    }

    public function getTableNames(): array
    {
        $schemaManager = $this->getSchemaManager();
        $tables = $schemaManager->listTableNames();

        // Sort alphabetically
        sort($tables);

        return $tables;
    }

    public function getColumnNames(string $tableName): array
    {
        $schemaManager = $this->getSchemaManager();

        try {
            $columns = $schemaManager->listTableColumns($tableName);
            $columnNames = array_keys($columns);

            // Sort alphabetically
            sort($columnNames);

            return $columnNames;
        } catch (\Throwable $e) {
            // Table doesn't exist or error
            return [];
        }
    }

    public function getTablesWithColumns(): array
    {
        // Vérifier le cache d'abord
        if ($this->cache !== null) {
            $cacheKey = self::CACHE_KEY_PREFIX . 'default';
            $cachedItem = $this->cache->getItem($cacheKey);

            if ($cachedItem->isHit()) {
                $cached = $cachedItem->get();
                if (is_array($cached)) {
                    return $cached;
                }
            }
        }

        // Par défaut, on charge seulement la base par défaut (performance)
        $schemaManager = $this->getSchemaManager();
        $tables = $schemaManager->listTableNames();

        $result = [];
        foreach ($tables as $tableName) {
            try {
                $columns = $schemaManager->listTableColumns($tableName);
                $columnNames = array_keys($columns);
                sort($columnNames);
                $result[$tableName] = $columnNames;
            } catch (\Throwable $e) {
                // Skip tables that can't be read
                continue;
            }
        }

        // Sort tables alphabetically
        ksort($result);

        // Mettre en cache
        if ($this->cache !== null) {
            $cacheKey = self::CACHE_KEY_PREFIX . 'default';
            $cachedItem = $this->cache->getItem($cacheKey);
            $cachedItem->set($result);
            $cachedItem->expiresAfter(self::CACHE_TTL);
            $this->cache->save($cachedItem);
        }

        return $result;
    }

    /**
     * Récupère les tables et colonnes pour une base de données spécifique
     *
     * @param string $databaseAlias Alias de la base (ex: 'logiciel', 'incare') ou nom de connexion Doctrine
     * @return array<string, array<int, string>> Map table => colonnes
     */
    public function getTablesWithColumnsForDatabase(string $databaseAlias): array
    {
        // Vérifier le cache d'abord
        if ($this->cache !== null) {
            $cacheKey = self::CACHE_KEY_PREFIX . $databaseAlias;
            $cachedItem = $this->cache->getItem($cacheKey);

            if ($cachedItem->isHit()) {
                $cached = $cachedItem->get();
                if (is_array($cached)) {
                    return $cached;
                }
            }
        }

        // Vérifier si c'est un alias shared.db
        if (isset($this->databaseAliases[$databaseAlias])) {
            $dbName = $this->databaseAliases[$databaseAlias];

            try {
                // Créer une connexion temporaire à cette base
                $params = $this->connection->getParams();
                $params['dbname'] = $dbName;

                $tempConnection = \Doctrine\DBAL\DriverManager::getConnection($params);
                $schemaManager = $tempConnection->createSchemaManager();
                $tables = $schemaManager->listTableNames();

                $result = [];
                foreach ($tables as $tableName) {
                    try {
                        $columns = $schemaManager->listTableColumns($tableName);
                        $columnNames = array_keys($columns);
                        sort($columnNames);
                        $result[$tableName] = $columnNames;
                    } catch (\Throwable $e) {
                        continue;
                    }
                }

                ksort($result);
                $tempConnection->close();

                // Mettre en cache
                if ($this->cache !== null) {
                    $cacheKey = self::CACHE_KEY_PREFIX . $databaseAlias;
                    $cachedItem = $this->cache->getItem($cacheKey);
                    $cachedItem->set($result);
                    $cachedItem->expiresAfter(self::CACHE_TTL);
                    $this->cache->save($cachedItem);
                }

                return $result;
            } catch (\Throwable $e) {
                return [];
            }
        }

        // Sinon vérifier si c'est une connexion Doctrine
        if ($this->managerRegistry !== null) {
            try {
                /** @var Connection $connection */
                $connection = $this->managerRegistry->getConnection($databaseAlias);
                $schemaManager = $connection->createSchemaManager();
                $tables = $schemaManager->listTableNames();

                $result = [];
                foreach ($tables as $tableName) {
                    try {
                        $columns = $schemaManager->listTableColumns($tableName);
                        $columnNames = array_keys($columns);
                        sort($columnNames);
                        $result[$tableName] = $columnNames;
                    } catch (\Throwable $e) {
                        continue;
                    }
                }

                ksort($result);

                // Mettre en cache
                if ($this->cache !== null) {
                    $cacheKey = self::CACHE_KEY_PREFIX . $databaseAlias;
                    $cachedItem = $this->cache->getItem($cacheKey);
                    $cachedItem->set($result);
                    $cachedItem->expiresAfter(self::CACHE_TTL);
                    $this->cache->save($cachedItem);
                }

                return $result;
            } catch (\Throwable $e) {
                return [];
            }
        }

        return [];
    }

    /**
     * Retourne la liste des alias de bases de données disponibles
     *
     * @return array<int, string> Liste des alias (ex: ['logiciel', 'extranet', 'common', ...])
     */
    public function getDatabaseAliases(): array
    {
        $aliases = array_keys($this->databaseAliases);

        // Si aucun alias shared.db n'est configuré, utiliser les connexions Doctrine comme fallback
        if (empty($aliases) && $this->managerRegistry !== null) {
            $aliases = $this->managerRegistry->getConnectionNames();
            $aliases = array_keys($aliases);
        }

        sort($aliases);
        return $aliases;
    }

    /**
     * @return AbstractSchemaManager<\Doctrine\DBAL\Platforms\AbstractPlatform>
     */
    private function getSchemaManager(): AbstractSchemaManager
    {
        return $this->connection->createSchemaManager();
    }
}
