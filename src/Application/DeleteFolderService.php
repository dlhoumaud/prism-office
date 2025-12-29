<?php

declare(strict_types=1);

namespace PrismOffice\Application;

use PrismOffice\Application\Contract\FileSystemInterface;
use PrismOffice\Domain\Exception\FolderDeletionException;

/**
 * Service pour supprimer un dossier (doit être vide)
 */
final class DeleteFolderService
{
    public function __construct(
        private readonly string $prismDirectory,
        private readonly FileSystemInterface $fileSystem
    ) {
    }

    /**
     * Supprime un dossier s'il est vide
     *
     * @throws FolderDeletionException
     */
    public function execute(string $folderPath): void
    {
        // Sécurité : empêcher la navigation en dehors du dossier prism
        $folderPath = str_replace(['..', '\\'], ['', '/'], $folderPath);
        $folderPath = trim($folderPath, '/');

        $targetPath = $this->prismDirectory . '/' . $folderPath;

        // Vérifier que le dossier existe
        if (!$this->fileSystem->isDirectory($targetPath)) {
            throw FolderDeletionException::notFound($folderPath);
        }

        // Vérifier que le dossier est vide
        $items = $this->fileSystem->scanDirectory($targetPath);
        if ($items === false) {
            throw FolderDeletionException::cannotRead($folderPath);
        }

        $hasContent = false;
        foreach ($items as $item) {
            if ($item !== '.' && $item !== '..') {
                $hasContent = true;
                break;
            }
        }

        if ($hasContent) {
            throw FolderDeletionException::notEmpty($folderPath);
        }

        // Supprimer le dossier
        if (!$this->fileSystem->deleteDirectory($targetPath)) {
            throw FolderDeletionException::deletionFailed($folderPath);
        }
    }
}
