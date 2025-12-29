<?php

declare(strict_types=1);

namespace PrismOffice\Application;

use PrismOffice\Application\Contract\FileSystemInterface;

/**
 * Service pour lister tous les fichiers YAML du dossier prism/ (incluant les sous-dossiers)
 * Utilisé pour les imports de scénarios
 */
final class ListYamlFilesService
{
    public function __construct(
        private readonly string $prismDirectory,
        private readonly FileSystemInterface $fileSystem
    ) {
    }

    /**
     * Retourne la liste des chemins relatifs des fichiers YAML (sans extension)
     *
     * @return array<string>
     */
    public function execute(): array
    {
        if (!$this->fileSystem->isDirectory($this->prismDirectory)) {
            return [];
        }

        $yamlFiles = [];
        $this->scanDirectory($this->prismDirectory, '', $yamlFiles);

        sort($yamlFiles);

        return $yamlFiles;
    }

    /**
     * Scan récursif du répertoire
     *
     * @param array<string> $yamlFiles
     */
    private function scanDirectory(string $directory, string $relativePath, array &$yamlFiles): void
    {
        $items = $this->fileSystem->scanDirectory($directory);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = $directory . '/' . $item;
            $itemRelativePath = $relativePath ? $relativePath . '/' . $item : $item;

            if ($this->fileSystem->isDirectory($fullPath)) {
                // Récursion dans les sous-dossiers
                $this->scanDirectory($fullPath, $itemRelativePath, $yamlFiles);
            } elseif ($this->fileSystem->isFile($fullPath) && str_ends_with($item, '.yaml')) {
                // Fichier YAML trouvé - on retire l'extension
                $yamlFiles[] = substr($itemRelativePath, 0, -5);
            }
        }
    }
}
