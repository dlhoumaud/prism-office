<?php

declare(strict_types=1);

namespace PrismOffice\Infrastructure\FileSystem;

use PrismOffice\Application\Contract\FileSystemInterface;

/**
 * Implémentation native du système de fichiers
 */
final class NativeFileSystem implements FileSystemInterface
{
    public function scanDirectory(string $directory): array|false
    {
        return @scandir($directory);
    }

    public function isDirectory(string $path): bool
    {
        return is_dir($path);
    }

    public function isFile(string $path): bool
    {
        return is_file($path);
    }

    public function fileExists(string $path): bool
    {
        return file_exists($path);
    }

    public function readFile(string $path): string|false
    {
        return @file_get_contents($path);
    }

    public function writeFile(string $path, string $content): int|false
    {
        return file_put_contents($path, $content);
    }

    public function createDirectory(string $path, int $permissions = 0755, bool $recursive = false): bool
    {
        return mkdir($path, $permissions, $recursive);
    }

    public function deleteFile(string $path): bool
    {
        return @unlink($path);
    }

    public function deleteDirectory(string $path): bool
    {
        return @rmdir($path);
    }

    public function getRealPath(string $path): string|false
    {
        return realpath($path);
    }

    public function getDirectoryName(string $path): string
    {
        return dirname($path);
    }
}
