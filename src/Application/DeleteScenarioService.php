<?php

declare(strict_types=1);

namespace PrismOffice\Application;

use PrismOffice\Application\Contract\FileSystemInterface;

/**
 * Service pour supprimer un fichier de scénario YAML
 */
final class DeleteScenarioService
{
    public function __construct(
        private readonly string $prismDirectory,
        private readonly FileSystemInterface $fileSystem
    ) {
    }

    /**
     * Supprime un fichier de scénario YAML
     *
     * @throws \RuntimeException Si le fichier n'existe pas ou ne peut pas être supprimé
     */
    public function execute(string $scenarioName): void
    {
        $yamlFilePath = $this->prismDirectory . '/' . $scenarioName . '.yaml';

        if (!$this->fileSystem->fileExists($yamlFilePath)) {
            throw new \RuntimeException(sprintf('Scenario file not found: %s', $yamlFilePath));
        }

        // Vérifier que le fichier est bien dans le répertoire prism (sécurité)
        $realPath = $this->fileSystem->getRealPath($yamlFilePath);
        $realPrismDir = $this->fileSystem->getRealPath($this->prismDirectory);

        if ($realPath === false || $realPrismDir === false || !str_starts_with($realPath, $realPrismDir)) {
            throw new \RuntimeException('Invalid scenario path');
        }

        if (!$this->fileSystem->deleteFile($yamlFilePath)) {
            throw new \RuntimeException(sprintf('Failed to delete scenario file: %s', $yamlFilePath));
        }
    }
}
