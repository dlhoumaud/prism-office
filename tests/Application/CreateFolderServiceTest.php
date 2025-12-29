<?php

declare(strict_types=1);

namespace PrismOffice\Tests\Application;

use PHPUnit\Framework\TestCase;
use PrismOffice\Application\CreateFolderService;
use Tests\PrismOffice\Fake\FakeFileSystem;

final class CreateFolderServiceTest extends TestCase
{
    private FakeFileSystem $fileSystem;
    private CreateFolderService $service;
    private string $prismDir = '/prism';

    protected function setUp(): void
    {
        $this->fileSystem = new FakeFileSystem();
        $this->fileSystem->addDirectory($this->prismDir);
        $this->service = new CreateFolderService($this->prismDir, $this->fileSystem);
    }

    public function testExecuteCreatesFolderSuccessfully(): void
    {
        $folderName = 'test_folder';
        $this->service->execute($folderName);

        $this->assertTrue($this->fileSystem->isDirectory($this->prismDir . '/' . $folderName));
    }

    public function testExecuteCreatesNestedFolders(): void
    {
        $folderName = 'parent/child';
        $this->service->execute($folderName);

        $this->assertTrue($this->fileSystem->isDirectory($this->prismDir . '/parent/child'));
    }

    public function testExecuteThrowsExceptionForEmptyFolderName(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Folder name cannot be empty');

        $this->service->execute('');
    }

    public function testExecuteThrowsExceptionForWhitespaceOnlyFolderName(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Folder name cannot be empty');

        $this->service->execute('   ');
    }

    public function testExecuteThrowsExceptionForInvalidCharacters(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Folder name can only contain letters, numbers, underscores, hyphens and slashes');

        $this->service->execute('folder@name');
    }

    public function testExecuteThrowsExceptionForExistingFolder(): void
    {
        $folderName = 'existing_folder';
        $this->fileSystem->addDirectory($this->prismDir . '/' . $folderName);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Folder "existing_folder" already exists');

        $this->service->execute($folderName);
    }

    public function testExecuteTrimsSlashesAndWhitespace(): void
    {
        $folderName = '  /test_folder/  ';
        $this->service->execute($folderName);

        $this->assertTrue($this->fileSystem->isDirectory($this->prismDir . '/test_folder'));
    }

    public function testExecuteThrowsExceptionForInvalidPath(): void
    {
        $this->fileSystem->getRealPathShouldFail = true;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid folder path');

        $this->service->execute('test');
    }

    public function testExecuteThrowsExceptionWhenMkdirFails(): void
    {
        $this->fileSystem->createDirectoryShouldFail = true;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to create folder');

        $this->service->execute('test_folder');
    }
}
