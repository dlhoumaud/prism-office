<?php

declare(strict_types=1);

namespace PrismOffice\Domain\Exception;

final class FolderDeletionException extends \RuntimeException
{
    public static function notFound(string $path): self
    {
        return new self(sprintf('Folder "%s" not found.', $path));
    }

    public static function cannotRead(string $path): self
    {
        return new self(sprintf('Cannot read folder "%s".', $path));
    }

    public static function notEmpty(string $path): self
    {
        return new self(sprintf('Cannot delete folder "%s": folder is not empty.', $path));
    }

    public static function deletionFailed(string $path): self
    {
        return new self(sprintf('Failed to delete folder "%s".', $path));
    }
}
