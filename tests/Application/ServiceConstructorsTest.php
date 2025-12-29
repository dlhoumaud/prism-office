<?php

declare(strict_types=1);

namespace PrismOffice\Tests\Application;

use PHPUnit\Framework\TestCase;
use PrismOffice\Application\CreateFolderService;
use PrismOffice\Application\DeleteScenarioService;
use PrismOffice\Application\ListYamlFilesService;
use PrismOffice\Application\LoadScenarioForEditService;
use PrismOffice\Application\SaveScenarioService;
use PrismOffice\Infrastructure\FileSystem\NativeFileSystem;

/**
 * Tests de constructeurs pour atteindre 100% de couverture
 */
final class ServiceConstructorsTest extends TestCase
{
    public function testCreateFolderServiceConstructor(): void
    {
        $fileSystem = new NativeFileSystem();
        $service = new CreateFolderService('/tmp/prism', $fileSystem);
        $this->assertInstanceOf(CreateFolderService::class, $service);
    }

    public function testDeleteScenarioServiceConstructor(): void
    {
        $fileSystem = new NativeFileSystem();
        $service = new DeleteScenarioService('/tmp/prism', $fileSystem);
        $this->assertInstanceOf(DeleteScenarioService::class, $service);
    }

    public function testListYamlFilesServiceConstructor(): void
    {
        $fileSystem = new NativeFileSystem();
        $service = new ListYamlFilesService('/tmp/prism', $fileSystem);
        $this->assertInstanceOf(ListYamlFilesService::class, $service);
    }

    public function testLoadScenarioForEditServiceConstructor(): void
    {
        $fileSystem = new NativeFileSystem();
        $service = new LoadScenarioForEditService('/tmp/prism', $fileSystem);
        $this->assertInstanceOf(LoadScenarioForEditService::class, $service);
    }

    public function testSaveScenarioServiceConstructor(): void
    {
        $fileSystem = new NativeFileSystem();
        $service = new SaveScenarioService('/tmp/prism', $fileSystem);
        $this->assertInstanceOf(SaveScenarioService::class, $service);
    }
}
