<?php

declare(strict_types=1);

namespace PrismOffice\Tests\Application;

use PHPUnit\Framework\TestCase;
use PrismOffice\Application\DeleteScenarioService;
use Tests\PrismOffice\Fake\FakeFileSystem;

final class DeleteScenarioServiceTest extends TestCase
{
    private FakeFileSystem $fileSystem;
    private DeleteScenarioService $service;
    private string $prismDir = '/prism';

    protected function setUp(): void
    {
        $this->fileSystem = new FakeFileSystem();
        $this->fileSystem->addDirectory($this->prismDir);
        $this->service = new DeleteScenarioService($this->prismDir, $this->fileSystem);
    }

    public function testExecuteDeletesScenarioSuccessfully(): void
    {
        $scenarioName = 'test_scenario';
        $filePath = $this->prismDir . '/' . $scenarioName . '.yaml';
        $this->fileSystem->addFile($filePath, "load:\n  - table: test\n");

        $this->service->execute($scenarioName);

        $this->assertFalse($this->fileSystem->fileExists($filePath));
    }

    public function testExecuteThrowsExceptionWhenFileDoesNotExist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Scenario file not found');

        $this->service->execute('non_existent_scenario');
    }

    public function testExecuteThrowsExceptionForInvalidPath(): void
    {
        $scenarioName = 'test_scenario';
        $filePath = $this->prismDir . '/' . $scenarioName . '.yaml';
        $this->fileSystem->addFile($filePath, "load:\n  - table: test\n");
        $this->fileSystem->getRealPathShouldFail = true;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid scenario path');

        $this->service->execute($scenarioName);
    }

    public function testExecuteThrowsExceptionWhenUnlinkFails(): void
    {
        $scenarioName = 'test_scenario';
        $filePath = $this->prismDir . '/' . $scenarioName . '.yaml';
        $this->fileSystem->addFile($filePath, "load:\n  - table: test\n");
        $this->fileSystem->deleteFileShouldFail = true;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to delete scenario file');

        $this->service->execute($scenarioName);
    }
}
