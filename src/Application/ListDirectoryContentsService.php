<?php

declare(strict_types=1);

namespace PrismOffice\Application;

use PrismOffice\Application\Contract\FileSystemInterface;

/**
 * Service pour lister le contenu d'un répertoire spécifique
 * Retourne les dossiers en premier, puis les fichiers
 */
final class ListDirectoryContentsService
{
    public function __construct(
        private readonly string $prismDirectory,
        private readonly FileSystemInterface $fileSystem
    ) {
    }

    /**
     * Liste le contenu d'un répertoire
     *
     * @return array{folders: array<string>, files: array<string>}
     */
    public function execute(string $subPath = ''): array
    {
        $targetPath = $this->prismDirectory;

        if ($subPath !== '') {
            // Sécurité : empêcher la navigation en dehors du dossier prism
            $subPath = str_replace(['..', '\\'], ['', '/'], $subPath);
            $subPath = trim($subPath, '/');
            $targetPath = $this->prismDirectory . '/' . $subPath;
        }

        if (!$this->fileSystem->isDirectory($targetPath)) {
            return ['folders' => [], 'files' => []];
        }

        $folders = [];
        $files = [];

        $items = $this->fileSystem->scanDirectory($targetPath);
        if ($items === false) {
            return ['folders' => [], 'files' => []];
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = $targetPath . '/' . $item;

            if ($this->fileSystem->isDirectory($fullPath)) {
                $folders[] = $item;
            } elseif (str_ends_with($item, '.yaml')) {
                // Retirer l'extension .yaml pour cohérence avec le reste
                $files[] = substr($item, 0, -5);
            }
        }

        // Trier les dossiers et fichiers
        sort($folders);
        sort($files);

        return [
            'folders' => $folders,
            'files' => $files,
        ];
    }
}
