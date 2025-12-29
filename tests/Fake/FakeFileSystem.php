<?php

declare(strict_types=1);

namespace Tests\PrismOffice\Fake;

use PrismOffice\Application\Contract\FileSystemInterface;

/**
 * Fake FileSystem pour les tests avec simulation d'erreurs
 */
final class FakeFileSystem implements FileSystemInterface
{
    /** @var array<string, string> */
    private array $files = [];

    /** @var array<string, bool> */
    private array $directories = [];

    public bool $scanDirectoryShouldFail = false;
    public bool $readFileShouldFail = false;
    public bool $writeFileShouldFail = false;
    public bool $createDirectoryShouldFail = false;
    public bool $deleteFileShouldFail = false;
    public bool $deleteDirectoryShouldFail = false;
    public bool $getRealPathShouldFail = false;

    /**
     * @return array<int, string>|false
     */
    public function scanDirectory(string $directory): array|false
    {
        if ($this->scanDirectoryShouldFail) {
            return false;
        }

        if (!isset($this->directories[$directory])) {
            return false;
        }

        $items = ['.', '..'];
        foreach (array_keys($this->files) as $path) {
            if (str_starts_with($path, $directory . '/')) {
                $relativePath = substr($path, strlen($directory) + 1);
                if (!str_contains($relativePath, '/')) {
                    $items[] = $relativePath;
                }
            }
        }

        foreach (array_keys($this->directories) as $path) {
            if ($path !== $directory && str_starts_with($path, $directory . '/')) {
                $relativePath = substr($path, strlen($directory) + 1);
                if (!str_contains($relativePath, '/')) {
                    $items[] = $relativePath;
                }
            }
        }

        return $items;
    }

    public function isDirectory(string $path): bool
    {
        return isset($this->directories[$path]);
    }

    public function isFile(string $path): bool
    {
        return isset($this->files[$path]);
    }

    public function fileExists(string $path): bool
    {
        return isset($this->files[$path]) || isset($this->directories[$path]);
    }

    public function readFile(string $path): string|false
    {
        if ($this->readFileShouldFail) {
            return false;
        }

        return $this->files[$path] ?? false;
    }

    public function writeFile(string $path, string $content): int|false
    {
        if ($this->writeFileShouldFail) {
            return false;
        }

        $this->files[$path] = $content;
        return strlen($content);
    }

    public function createDirectory(string $path, int $permissions = 0755, bool $recursive = false): bool
    {
        if ($this->createDirectoryShouldFail) {
            return false;
        }

        if ($recursive) {
            // Pour les chemins absolus, on doit créer tous les répertoires parents
            $path = str_replace('\\', '/', $path);
            $parts = explode('/', $path);
            $current = '';
            foreach ($parts as $part) {
                if ($part === '') {
                    continue;
                }
                $current .= ($current === '' && str_starts_with($path, '/') ? '/' : ($current ? '/' : '')) . $part;
                if (!isset($this->directories[$current])) {
                    $this->directories[$current] = true;
                }
            }
        } else {
            $this->directories[$path] = true;
        }

        return true;
    }

    public function deleteFile(string $path): bool
    {
        if ($this->deleteFileShouldFail) {
            return false;
        }

        if (isset($this->files[$path])) {
            unset($this->files[$path]);
            return true;
        }

        return false;
    }

    public function getRealPath(string $path): string|false
    {
        if ($this->getRealPathShouldFail) {
            return false;
        }

        // Simulation simple : retourne le chemin normalisé
        if (isset($this->files[$path]) || isset($this->directories[$path])) {
            return $path;
        }

        // Pour les chemins de répertoires parents
        $parts = explode('/', $path);
        array_pop($parts);
        $parentPath = implode('/', $parts);

        if (isset($this->directories[$parentPath])) {
            return $parentPath;
        }

        return false;
    }

    public function getDirectoryName(string $path): string
    {
        return dirname($path);
    }

    public function deleteDirectory(string $path): bool
    {
        if ($this->deleteDirectoryShouldFail) {
            return false;
        }

        if (!isset($this->directories[$path])) {
            return false;
        }

        unset($this->directories[$path]);
        return true;
    }

    // Méthodes helper pour les tests
    public function addFile(string $path, string $content): void
    {
        $this->files[$path] = $content;
    }

    public function addDirectory(string $path): void
    {
        $this->directories[$path] = true;
    }

    public function getFile(string $path): ?string
    {
        return $this->files[$path] ?? null;
    }
}
