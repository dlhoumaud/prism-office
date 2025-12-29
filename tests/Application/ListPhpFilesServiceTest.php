<?php

declare(strict_types=1);

namespace Tests\PrismOffice\Application;

use PHPUnit\Framework\TestCase;
use PrismOffice\Application\ListPhpFilesService;
use Tests\PrismOffice\Fake\FakeFileSystem;

final class ListPhpFilesServiceTest extends TestCase
{
    private FakeFileSystem $fileSystem;
    private string $scriptsDirectory;

    protected function setUp(): void
    {
        $this->fileSystem = new FakeFileSystem();
        $this->scriptsDirectory = '/prism/scripts';
        $this->fileSystem->addDirectory($this->scriptsDirectory);
    }

    public function testExecuteReturnsEmptyWhenDirectoryNotExists(): void
    {
        $service = new ListPhpFilesService($this->scriptsDirectory, $this->fileSystem);

        $result = $service->execute('non-existent');

        self::assertSame(['folders' => [], 'files' => []], $result);
    }

    public function testExecuteReturnsEmptyWhenScanFails(): void
    {
        $this->fileSystem->addDirectory($this->scriptsDirectory . '/test');
        $this->fileSystem->scanDirectoryShouldFail = true;

        $service = new ListPhpFilesService($this->scriptsDirectory, $this->fileSystem);

        $result = $service->execute('test');

        self::assertSame(['folders' => [], 'files' => []], $result);
    }

    public function testExecuteListsFoldersAndPhpFiles(): void
    {
        $this->fileSystem->addDirectory($this->scriptsDirectory . '/includes');
        $this->fileSystem->addDirectory($this->scriptsDirectory . '/helpers');
        $this->fileSystem->addFile($this->scriptsDirectory . '/TestUsersPrism.php', 'content');
        $this->fileSystem->addFile($this->scriptsDirectory . '/DataPrism.php', 'content');

        $service = new ListPhpFilesService($this->scriptsDirectory, $this->fileSystem);

        $result = $service->execute();

        self::assertSame(['helpers', 'includes'], $result['folders']);
        self::assertSame(['DataPrism', 'TestUsersPrism'], $result['files']);
    }

    public function testExecuteWithSubpath(): void
    {
        $this->fileSystem->addDirectory($this->scriptsDirectory . '/includes');
        $this->fileSystem->addFile($this->scriptsDirectory . '/includes/TestPrism.php', 'content');

        $service = new ListPhpFilesService($this->scriptsDirectory, $this->fileSystem);

        $result = $service->execute('includes');

        self::assertSame([], $result['folders']);
        self::assertSame(['TestPrism'], $result['files']);
    }

    public function testExecuteSanitizesPath(): void
    {
        $this->fileSystem->addDirectory($this->scriptsDirectory . '/test');
        $this->fileSystem->addFile($this->scriptsDirectory . '/test/file.php', 'content');

        $service = new ListPhpFilesService($this->scriptsDirectory, $this->fileSystem);

        $result = $service->execute('../test');

        self::assertSame([], $result['folders']);
        self::assertSame(['file'], $result['files']);
    }

    public function testExecuteIgnoresDotDirectories(): void
    {
        $this->fileSystem->addFile($this->scriptsDirectory . '/TestPrism.php', 'content');

        $service = new ListPhpFilesService($this->scriptsDirectory, $this->fileSystem);

        $result = $service->execute();

        self::assertSame([], $result['folders']);
        self::assertSame(['TestPrism'], $result['files']);
    }

    public function testExecuteSortsFoldersAndFiles(): void
    {
        $this->fileSystem->addDirectory($this->scriptsDirectory . '/zfolder');
        $this->fileSystem->addDirectory($this->scriptsDirectory . '/afolder');
        $this->fileSystem->addFile($this->scriptsDirectory . '/ZTestPrism.php', 'content');
        $this->fileSystem->addFile($this->scriptsDirectory . '/ATestPrism.php', 'content');

        $service = new ListPhpFilesService($this->scriptsDirectory, $this->fileSystem);

        $result = $service->execute();

        self::assertSame(['afolder', 'zfolder'], $result['folders']);
        self::assertSame(['ATestPrism', 'ZTestPrism'], $result['files']);
    }

    public function testExecuteFiltersPhpFilesOnly(): void
    {
        $this->fileSystem->addFile($this->scriptsDirectory . '/TestPrism.php', 'content');
        $this->fileSystem->addFile($this->scriptsDirectory . '/test.txt', 'content');
        $this->fileSystem->addFile($this->scriptsDirectory . '/data.yaml', 'content');

        $service = new ListPhpFilesService($this->scriptsDirectory, $this->fileSystem);

        $result = $service->execute();

        self::assertSame(['TestPrism'], $result['files']);
    }

    public function testExecuteRemovesPhpExtension(): void
    {
        $this->fileSystem->addFile($this->scriptsDirectory . '/MyScenarioPrism.php', 'content');

        $service = new ListPhpFilesService($this->scriptsDirectory, $this->fileSystem);

        $result = $service->execute();

        self::assertSame(['MyScenarioPrism'], $result['files']);
    }
}
