<?php

declare(strict_types=1);

namespace Tests\PrismOffice\Application;

use PHPUnit\Framework\TestCase;
use PrismOffice\Application\ListDirectoryContentsService;
use Tests\PrismOffice\Fake\FakeFileSystem;

final class ListDirectoryContentsServiceTest extends TestCase
{
    private FakeFileSystem $fileSystem;
    private string $prismDirectory;

    protected function setUp(): void
    {
        $this->fileSystem = new FakeFileSystem();
        $this->prismDirectory = '/prism/yaml';
        $this->fileSystem->addDirectory($this->prismDirectory);
    }

    public function testExecuteReturnsEmptyWhenDirectoryNotExists(): void
    {
        $service = new ListDirectoryContentsService($this->prismDirectory, $this->fileSystem);

        $result = $service->execute('non-existent');

        self::assertSame(['folders' => [], 'files' => []], $result);
    }

    public function testExecuteReturnsEmptyWhenScanFails(): void
    {
        $this->fileSystem->addDirectory($this->prismDirectory . '/test');
        $this->fileSystem->scanDirectoryShouldFail = true;

        $service = new ListDirectoryContentsService($this->prismDirectory, $this->fileSystem);

        $result = $service->execute('test');

        self::assertSame(['folders' => [], 'files' => []], $result);
    }

    public function testExecuteListsFoldersAndFiles(): void
    {
        $this->fileSystem->addDirectory($this->prismDirectory . '/folder1');
        $this->fileSystem->addDirectory($this->prismDirectory . '/folder2');
        $this->fileSystem->addFile($this->prismDirectory . '/scenario1.yaml', 'content1');
        $this->fileSystem->addFile($this->prismDirectory . '/scenario2.yaml', 'content2');

        $service = new ListDirectoryContentsService($this->prismDirectory, $this->fileSystem);

        $result = $service->execute();

        self::assertSame(['folder1', 'folder2'], $result['folders']);
        self::assertSame(['scenario1', 'scenario2'], $result['files']);
    }

    public function testExecuteWithSubpath(): void
    {
        $this->fileSystem->addDirectory($this->prismDirectory . '/subfolder');
        $this->fileSystem->addFile($this->prismDirectory . '/subfolder/test.yaml', 'content');

        $service = new ListDirectoryContentsService($this->prismDirectory, $this->fileSystem);

        $result = $service->execute('subfolder');

        self::assertSame([], $result['folders']);
        self::assertSame(['test'], $result['files']);
    }

    public function testExecuteSanitizesPath(): void
    {
        $this->fileSystem->addDirectory($this->prismDirectory . '/test');
        $this->fileSystem->addFile($this->prismDirectory . '/test/file.yaml', 'content');

        $service = new ListDirectoryContentsService($this->prismDirectory, $this->fileSystem);

        $result = $service->execute('../test');

        self::assertSame([], $result['folders']);
        self::assertSame(['file'], $result['files']);
    }

    public function testExecuteIgnoresDotDirectories(): void
    {
        $this->fileSystem->addFile($this->prismDirectory . '/scenario.yaml', 'content');

        $service = new ListDirectoryContentsService($this->prismDirectory, $this->fileSystem);

        $result = $service->execute();

        self::assertSame([], $result['folders']);
        self::assertSame(['scenario'], $result['files']);
    }

    public function testExecuteSortsFoldersAndFiles(): void
    {
        $this->fileSystem->addDirectory($this->prismDirectory . '/zfolder');
        $this->fileSystem->addDirectory($this->prismDirectory . '/afolder');
        $this->fileSystem->addFile($this->prismDirectory . '/zfile.yaml', 'content');
        $this->fileSystem->addFile($this->prismDirectory . '/afile.yaml', 'content');

        $service = new ListDirectoryContentsService($this->prismDirectory, $this->fileSystem);

        $result = $service->execute();

        self::assertSame(['afolder', 'zfolder'], $result['folders']);
        self::assertSame(['afile', 'zfile'], $result['files']);
    }

    public function testExecuteFiltersYamlFilesOnly(): void
    {
        $this->fileSystem->addFile($this->prismDirectory . '/scenario.yaml', 'content');
        $this->fileSystem->addFile($this->prismDirectory . '/test.txt', 'content');
        $this->fileSystem->addFile($this->prismDirectory . '/data.json', 'content');

        $service = new ListDirectoryContentsService($this->prismDirectory, $this->fileSystem);

        $result = $service->execute();

        self::assertSame(['scenario'], $result['files']);
    }
}
