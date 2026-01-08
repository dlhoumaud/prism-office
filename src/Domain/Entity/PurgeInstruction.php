<?php

declare(strict_types=1);

namespace PrismOffice\Domain\Entity;

/**
 * Représente une instruction de purge personnalisée
 */
final class PurgeInstruction
{
    /**
     * @param string $table Nom de la table
     * @param array<string, mixed> $where Conditions de purge (colonne => valeur)
     * @param bool $purgePivot Activer purge_pivot: true
     * @param string|null $database Nom de la base de données (optionnel)
     * @param string|null $info Note courte optionnelle pour l'instruction
     */
    public function __construct(
        private readonly string $table,
        private readonly array $where,
        private readonly bool $purgePivot = false,
        private readonly ?string $database = null,
        private readonly ?string $info = null
    ) {
    }

    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return array<string, mixed>
     */
    public function getWhere(): array
    {
        return $this->where;
    }

    public function getPurgePivot(): bool
    {
        return $this->purgePivot;
    }

    public function getDatabase(): ?string
    {
        return $this->database;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }
}
