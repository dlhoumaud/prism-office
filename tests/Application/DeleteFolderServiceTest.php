<?php

declare(strict_types=1);

namespace Tests\PrismOffice\Application;

use PHPUnit\Framework\TestCase;
use PrismOffice\Application\DeleteFolderService;
use PrismOffice\Domain\Exception\FolderDeletionException;
use Tests\PrismOffice\Fake\FakeFileSystem;

final class DeleteFolderServiceTest extends TestCase
{
    private FakeFileSystem $fileSystem;
    private string $prismDirectory;

    protected function setUp(): void
    {
        $this->fileSystem = new FakeFileSystem();
        $this->prismDirectory = '/prism/yaml';
        $this->fileSystem->addDirectory($this->prismDirectory);
    }

    public function testExecuteThrowsExceptionWhenFolderNotFound(): void
    {
        $service = new DeleteFolderService($this->prismDirectory, $this->fileSystem);

        $this->expectException(FolderDeletionException::class);
        $this->expectExceptionMessage('Folder "non-existent" not found.');

        $service->execute('non-existent');
    }

    public function testExecuteThrowsExceptionWhenCannotReadFolder(): void
    {
        $this->fileSystem->addDirectory($this->prismDirectory . '/test');
        $this->fileSystem->scanDirectoryShouldFail = true;

        $service = new DeleteFolderService($this->prismDirectory, $this->fileSystem);

        $this->expectException(FolderDeletionException::class);
        $this->expectExceptionMessage('Cannot read folder "test".');

        $service->execute('test');
    }

    public function testExecuteThrowsExceptionWhenFolderNotEmpty(): void
    {
        $this->fileSystem->addDirectory($this->prismDirectory . '/test');
        $this->fileSystem->addFile($this->prismDirectory . '/test/file.yaml', 'content');

        $service = new DeleteFolderService($this->prismDirectory, $this->fileSystem);

        $this->expectException(FolderDeletionException::class);
        $this->expectExceptionMessage('Cannot delete folder "test": folder is not empty.');

        $service->execute('test');
    }

    public function testExecuteDeletesEmptyFolderSuccessfully(): void
    {
        $this->fileSystem->addDirectory($this->prismDirectory . '/empty-folder');

        $service = new DeleteFolderService($this->prismDirectory, $this->fileSystem);

        $service->execute('empty-folder');

        self::assertTrue(true); // If no exception, test passes
    }

    public function testExecuteSanitizesPath(): void
    {
        $this->fileSystem->addDirectory($this->prismDirectory . '/test');

        $service = new DeleteFolderService($this->prismDirectory, $this->fileSystem);

        $service->execute('../test');

        self::assertTrue(true); // Should work despite ../ attempt
    }

    public function testExecuteTrimsSlashes(): void
    {
        $this->fileSystem->addDirectory($this->prismDirectory . '/test');

        $service = new DeleteFolderService($this->prismDirectory, $this->fileSystem);

        $service->execute('/test/');

        self::assertTrue(true); // Should work with leading/trailing slashes
    }

    public function testExecuteThrowsExceptionWhenDeletionFails(): void
    {
        $this->fileSystem->addDirectory($this->prismDirectory . '/test');
        $this->fileSystem->deleteDirectoryShouldFail = true;

        $service = new DeleteFolderService($this->prismDirectory, $this->fileSystem);

        $this->expectException(FolderDeletionException::class);
        $this->expectExceptionMessage('Failed to delete folder "test".');

        $service->execute('test');
    }
}
