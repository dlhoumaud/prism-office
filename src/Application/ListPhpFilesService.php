<?php

declare(strict_types=1);

namespace PrismOffice\Application;

use PrismOffice\Application\Contract\FileSystemInterface;

/**
 * Service pour lister les fichiers PHP du dossier prism/scripts (lecture seule)
 * Retourne les dossiers en premier, puis les fichiers
 */
final class ListPhpFilesService
{
    public function __construct(
        private readonly string $scriptsDirectory,
        private readonly FileSystemInterface $fileSystem
    ) {
    }

    /**
     * Liste le contenu d'un répertoire scripts
     *
     * @return array{folders: array<string>, files: array<string>}
     */
    public function execute(string $subPath = ''): array
    {
        $targetPath = $this->scriptsDirectory;

        if ($subPath !== '') {
            // Sécurité : empêcher la navigation en dehors du dossier prism/scripts
            $subPath = str_replace(['..', '\\'], ['', '/'], $subPath);
            $subPath = trim($subPath, '/');
            $targetPath = $this->scriptsDirectory . '/' . $subPath;
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
            } elseif (str_ends_with($item, '.php')) {
                // Retirer l'extension .php et le suffixe Prism si présent
                $fileName = substr($item, 0, -4);
                // if (str_ends_with($fileName, 'Prism')) {
                //     $fileName = substr($fileName, 0, -5);
                // }
                $files[] = $fileName;
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
