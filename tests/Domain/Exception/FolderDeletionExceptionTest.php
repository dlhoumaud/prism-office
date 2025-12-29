<?php

declare(strict_types=1);

namespace Tests\PrismOffice\Domain\Exception;

use PHPUnit\Framework\TestCase;
use PrismOffice\Domain\Exception\FolderDeletionException;

final class FolderDeletionExceptionTest extends TestCase
{
    public function testNotFoundCreatesExceptionWithCorrectMessage(): void
    {
        $exception = FolderDeletionException::notFound('test/path');

        self::assertInstanceOf(FolderDeletionException::class, $exception);
        self::assertSame('Folder "test/path" not found.', $exception->getMessage());
    }

    public function testCannotReadCreatesExceptionWithCorrectMessage(): void
    {
        $exception = FolderDeletionException::cannotRead('test/path');

        self::assertInstanceOf(FolderDeletionException::class, $exception);
        self::assertSame('Cannot read folder "test/path".', $exception->getMessage());
    }

    public function testNotEmptyCreatesExceptionWithCorrectMessage(): void
    {
        $exception = FolderDeletionException::notEmpty('test/path');

        self::assertInstanceOf(FolderDeletionException::class, $exception);
        self::assertSame('Cannot delete folder "test/path": folder is not empty.', $exception->getMessage());
    }

    public function testDeletionFailedCreatesExceptionWithCorrectMessage(): void
    {
        $exception = FolderDeletionException::deletionFailed('test/path');

        self::assertInstanceOf(FolderDeletionException::class, $exception);
        self::assertSame('Failed to delete folder "test/path".', $exception->getMessage());
    }

    public function testExceptionIsRuntimeException(): void
    {
        $exception = FolderDeletionException::notFound('test');

        self::assertInstanceOf(\RuntimeException::class, $exception);
    }
}
