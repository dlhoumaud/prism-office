<?php

declare(strict_types=1);

namespace PrismOffice\Application;

use PrismOffice\Application\Contract\FileSystemInterface;

/**
 * Service pour créer un sous-dossier dans le répertoire prism/
 */
final class CreateFolderService
{
    public function __construct(
        private readonly string $prismDirectory,
        private readonly FileSystemInterface $fileSystem
    ) {
    }

    /**
     * Crée un sous-dossier dans le répertoire prism/
     *
     * @param string $folderName Nom du dossier (peut contenir des / pour sous-dossiers)
     * @throws \RuntimeException Si le dossier existe déjà ou ne peut pas être créé
     */
    public function execute(string $folderName): void
    {
        // Nettoyer le nom (supprimer les espaces, caractères dangereux)
        $folderName = trim($folderName, " \t\n\r\0\x0B/\\");

        if ($folderName === '') {
            throw new \RuntimeException('Folder name cannot be empty');
        }

        // Vérifier les caractères interdits
        if (preg_match('/[^a-zA-Z0-9_\-\/]/', $folderName)) {
            throw new \RuntimeException('Folder name can only contain letters, numbers, underscores, hyphens and slashes');
        }

        $folderPath = $this->prismDirectory . '/' . $folderName;

        // Vérifier que le chemin est bien dans le répertoire prism (sécurité)
        $realPath = $this->fileSystem->getRealPath($this->fileSystem->getDirectoryName($folderPath));
        $realPrismDir = $this->fileSystem->getRealPath($this->prismDirectory);

        if ($realPrismDir === false || ($realPath !== false && !str_starts_with($realPath, $realPrismDir))) {
            throw new \RuntimeException('Invalid folder path');
        }

        if ($this->fileSystem->fileExists($folderPath)) {
            throw new \RuntimeException(sprintf('Folder "%s" already exists', $folderName));
        }

        if (!$this->fileSystem->createDirectory($folderPath, 0755, true)) {
            throw new \RuntimeException(sprintf('Failed to create folder: %s', $folderName));
        }
    }
}
