<?php

declare(strict_types=1);

namespace PrismOffice\Tests\Application;

use PHPUnit\Framework\TestCase;
use PrismOffice\Application\ListYamlFilesService;
use Tests\PrismOffice\Fake\FakeFileSystem;

final class ListYamlFilesServiceTest extends TestCase
{
    private FakeFileSystem $fileSystem;
    private ListYamlFilesService $service;
    private string $prismDir = '/prism';

    protected function setUp(): void
    {
        $this->fileSystem = new FakeFileSystem();
        $this->service = new ListYamlFilesService($this->prismDir, $this->fileSystem);
    }

    public function testExecuteReturnsEmptyArrayWhenDirectoryDoesNotExist(): void
    {
        $result = $this->service->execute();

        $this->assertSame([], $result);
    }

    public function testExecuteReturnsYamlFilesWithoutExtension(): void
    {
        $this->fileSystem->addDirectory($this->prismDir);
        $this->fileSystem->addFile($this->prismDir . '/scenario1.yaml', 'test');
        $this->fileSystem->addFile($this->prismDir . '/scenario2.yaml', 'test');
        $this->fileSystem->addFile($this->prismDir . '/not_yaml.txt', 'test');

        $result = $this->service->execute();

        $this->assertCount(2, $result);
        $this->assertContains('scenario1', $result);
        $this->assertContains('scenario2', $result);
        $this->assertNotContains('not_yaml', $result);
    }

    public function testExecuteReturnsYamlFilesFromSubdirectories(): void
    {
        $this->fileSystem->addDirectory($this->prismDir);
        $this->fileSystem->addDirectory($this->prismDir . '/subfolder');
        $this->fileSystem->addFile($this->prismDir . '/root.yaml', 'test');
        $this->fileSystem->addFile($this->prismDir . '/subfolder/nested.yaml', 'test');

        $result = $this->service->execute();

        $this->assertCount(2, $result);
        $this->assertContains('root', $result);
        $this->assertContains('subfolder/nested', $result);
    }

    public function testExecuteReturnsSortedList(): void
    {
        $this->fileSystem->addDirectory($this->prismDir);
        $this->fileSystem->addFile($this->prismDir . '/zebra.yaml', 'test');
        $this->fileSystem->addFile($this->prismDir . '/alpha.yaml', 'test');
        $this->fileSystem->addFile($this->prismDir . '/beta.yaml', 'test');

        $result = $this->service->execute();

        $this->assertSame(['alpha', 'beta', 'zebra'], $result);
    }

    public function testExecuteHandlesScanDirectoryFailure(): void
    {
        $this->fileSystem->addDirectory($this->prismDir);
        $this->fileSystem->scanDirectoryShouldFail = true;

        $result = $this->service->execute();

        $this->assertSame([], $result);
    }
}
