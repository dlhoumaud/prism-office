<?php

declare(strict_types=1);

namespace PrismOffice\Domain\Entity;

/**
 * Représente la définition complète d'un scénario YAML en construction
 */
final class ScenarioDefinition
{
    /**
     * @param string $name Nom du scénario (sans extension)
     * @param array<string> $imports Liste des scénarios à importer
     * @param array<string, string> $variables Variables globales (nom => valeur)
     * @param array<LoadInstruction> $loadInstructions Instructions de chargement
     * @param array<PurgeInstruction> $purgeInstructions Instructions de purge custom
     * @param string|null $info Note globale optionnelle du scénario
     */
    public function __construct(
        private readonly string $name,
        private readonly array $imports = [],
        private readonly array $variables = [],
        private readonly array $loadInstructions = [],
        private readonly array $purgeInstructions = [],
        private readonly ?string $info = null
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<string>
     */
    public function getImports(): array
    {
        return $this->imports;
    }

    /**
     * @return array<string, string>
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @return array<LoadInstruction>
     */
    public function getLoadInstructions(): array
    {
        return $this->loadInstructions;
    }

    /**
     * @return array<PurgeInstruction>
     */
    public function getPurgeInstructions(): array
    {
        return $this->purgeInstructions;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function hasImports(): bool
    {
        return count($this->imports) > 0;
    }

    public function hasVariables(): bool
    {
        return count($this->variables) > 0;
    }

    public function hasLoadInstructions(): bool
    {
        return count($this->loadInstructions) > 0;
    }

    public function hasPurgeInstructions(): bool
    {
        return count($this->purgeInstructions) > 0;
    }
}
