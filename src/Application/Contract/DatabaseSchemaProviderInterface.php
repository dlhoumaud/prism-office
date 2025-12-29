<?php

declare(strict_types=1);

namespace PrismOffice\Application\Contract;

/**
 * Provides database schema information for autocompletion
 */
interface DatabaseSchemaProviderInterface
{
    /**
     * Get all table names from the database
     *
     * @return array<string> List of table names
     */
    public function getTableNames(): array;

    /**
     * Get all column names for a given table
     *
     * @param string $tableName The table name
     * @return array<string> List of column names
     */
    public function getColumnNames(string $tableName): array;

    /**
     * Get all tables with their columns
     *
     * For multi-database: array<string, array<string, array<string>>> (db => table => columns)
     * For single-database: array<string, array<string>> (table => columns)
     *
     * @return array<string, array<string>|array<string, array<string>>>
     */
    public function getTablesWithColumns(): array;

    /**
     * Get available database aliases for autocompletion
     *
     * Returns aliases like: ['logiciel', 'extranet', 'common', 'tampon', 'users', 'incare', 'api']
     *
     * @return array<int, string> List of database aliases
     */
    public function getDatabaseAliases(): array;

    /**
     * Get tables with their columns for a specific database
     *
     * This method loads the schema for a specific database on-demand (via AJAX)
     *
     * @param string $databaseAlias The database alias (e.g., 'logiciel', 'incare') or connection name
     * @return array<string, array<int, string>> Map of table name => column names
     */
    public function getTablesWithColumnsForDatabase(string $databaseAlias): array;
}
