<?php

declare(strict_types=1);

namespace PrismOffice\Tests\Application;

use PHPUnit\Framework\TestCase;
use PrismOffice\Application\LoadScenarioForEditService;
use Tests\PrismOffice\Fake\FakeFileSystem;

final class LoadScenarioForEditServiceTest extends TestCase
{
    private FakeFileSystem $fileSystem;
    private LoadScenarioForEditService $service;
    private string $prismDir = '/prism';

    protected function setUp(): void
    {
        $this->fileSystem = new FakeFileSystem();
        $this->fileSystem->addDirectory($this->prismDir);
        $this->service = new LoadScenarioForEditService($this->prismDir, $this->fileSystem);
    }

    public function testExecuteLoadsScenarioSuccessfully(): void
    {
        $scenarioName = 'test_scenario';
        $yaml = <<<YAML
import:
  - base_scenario
vars:
  table_name: users
load:
  - table: users
    data:
      - id: 1
        name: John
purge:
  - table: sessions
    where:
      user_id: 1
YAML;
        $this->fileSystem->addFile($this->prismDir . '/' . $scenarioName . '.yaml', $yaml);

        $scenario = $this->service->execute($scenarioName);

        $this->assertSame($scenarioName, $scenario->getName());
        $this->assertSame(['base_scenario'], $scenario->getImports());
        $this->assertSame(['table_name' => 'users'], $scenario->getVariables());
        $this->assertCount(1, $scenario->getLoadInstructions());
        $this->assertCount(1, $scenario->getPurgeInstructions());
    }

    public function testExecuteThrowsExceptionWhenFileDoesNotExist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Scenario file not found');

        $this->service->execute('non_existent');
    }

    public function testExecuteLoadsScenarioWithoutOptionalSections(): void
    {
        $scenarioName = 'minimal_scenario';
        $yaml = <<<YAML
load:
  - table: users
    data:
      - id: 1
YAML;
        $this->fileSystem->addFile($this->prismDir . '/' . $scenarioName . '.yaml', $yaml);

        $scenario = $this->service->execute($scenarioName);

        $this->assertSame($scenarioName, $scenario->getName());
        $this->assertSame([], $scenario->getImports());
        $this->assertSame([], $scenario->getVariables());
        $this->assertCount(1, $scenario->getLoadInstructions());
        $this->assertCount(0, $scenario->getPurgeInstructions());
    }

    public function testExecuteThrowsExceptionForInvalidYaml(): void
    {
        $scenarioName = 'invalid_scenario';
        $this->fileSystem->addFile($this->prismDir . '/' . $scenarioName . '.yaml', 'not a valid yaml: [unclosed');

        $this->expectException(\Exception::class);

        $this->service->execute($scenarioName);
    }

    public function testExecuteLoadsScenarioWithPurgePivot(): void
    {
        $scenarioName = 'scenario_with_purge_pivot';
        $yaml = <<<YAML
purge:
  - table: pivot_table
    where:
      user_id: 1
    purge_pivot: true
load:
  - table: users
    data:
      - id: 1
YAML;
        $this->fileSystem->addFile($this->prismDir . '/' . $scenarioName . '.yaml', $yaml);

        $scenario = $this->service->execute($scenarioName);

        $this->assertCount(1, $scenario->getPurgeInstructions());
        $purgeInstruction = $scenario->getPurgeInstructions()[0];
        $this->assertTrue($purgeInstruction->getPurgePivot());
    }

    public function testExecuteSkipsInvalidLoadEntries(): void
    {
        $scenarioName = 'invalid_entries';
        $yaml = <<<YAML
load:
  - invalid_string_entry
  - table: valid_table
    data:
      - id: 1
  - invalid_entry_without_table:
      data: something
YAML;
        $this->fileSystem->addFile($this->prismDir . '/' . $scenarioName . '.yaml', $yaml);

        $scenario = $this->service->execute($scenarioName);

        // Seule l'entrée valide devrait être chargée
        $this->assertCount(1, $scenario->getLoadInstructions());
    }

    public function testExecuteSkipsInvalidPurgeEntries(): void
    {
        $scenarioName = 'invalid_purge_entries';
        $yaml = <<<YAML
load:
  - table: users
    data:
      - id: 1
purge:
  - invalid_string_entry
  - table: valid_table
    where:
      id: 1
  - invalid_entry_without_table:
      where: something
YAML;
        $this->fileSystem->addFile($this->prismDir . '/' . $scenarioName . '.yaml', $yaml);

        $scenario = $this->service->execute($scenarioName);

        // Seule l'entrée valide devrait être chargée
        $this->assertCount(1, $scenario->getPurgeInstructions());
    }

    public function testExecuteThrowsExceptionForNonArrayYaml(): void
    {
        $scenarioName = 'scalar_yaml';
        $this->fileSystem->addFile($this->prismDir . '/' . $scenarioName . '.yaml', 'just a string');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid YAML structure');

        $this->service->execute($scenarioName);
    }

    public function testExecuteThrowsExceptionWhenFileCannotBeRead(): void
    {
        $scenarioName = 'unreadable_scenario';
        $this->fileSystem->addFile($this->prismDir . '/' . $scenarioName . '.yaml', 'content');
        $this->fileSystem->readFileShouldFail = true;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to read scenario file');

        $this->service->execute($scenarioName);
    }
}
