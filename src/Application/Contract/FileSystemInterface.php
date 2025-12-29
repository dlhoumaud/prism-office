<?php

declare(strict_types=1);

namespace PrismOffice\Application\Contract;

/**
 * Interface pour abstraire les opérations du système de fichiers
 */
interface FileSystemInterface
{
    /**
     * @return array<int, string>|false
     */
    public function scanDirectory(string $directory): array|false;

    public function isDirectory(string $path): bool;

    public function isFile(string $path): bool;

    public function fileExists(string $path): bool;

    public function readFile(string $path): string|false;

    public function writeFile(string $path, string $content): int|false;

    public function createDirectory(string $path, int $permissions = 0755, bool $recursive = false): bool;

    public function deleteFile(string $path): bool;

    public function deleteDirectory(string $path): bool;

    public function getRealPath(string $path): string|false;

    public function getDirectoryName(string $path): string;
}
