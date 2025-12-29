<?php

declare(strict_types=1);

namespace PrismOffice\Domain\Entity;

/**
 * Représente une instruction de chargement (insertion de données)
 */
final class LoadInstruction
{
    /**
     * @param string $table Nom de la table
     * @param array<int|string, mixed> $data Données à insérer (peut être array de rows ou colonne => valeur)
     * @param array<string, string> $types Types de données optionnels (colonne => type)
     * @param array<string, mixed>|null $pivot Configuration du pivot custom
     * @param string|null $database Nom de la base de données (optionnel)
     */
    public function __construct(
        private readonly string $table,
        private readonly array $data,
        private readonly array $types = [],
        private readonly ?array $pivot = null,
        private readonly ?string $database = null
    ) {
    }

    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array<string, string>
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getPivot(): ?array
    {
        return $this->pivot;
    }

    public function getDatabase(): ?string
    {
        return $this->database;
    }

    public function hasTypes(): bool
    {
        return count($this->types) > 0;
    }

    public function hasPivot(): bool
    {
        return $this->pivot !== null;
    }
}
