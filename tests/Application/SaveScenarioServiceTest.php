<?php

declare(strict_types=1);

namespace PrismOffice\Tests\Application;

use PHPUnit\Framework\TestCase;
use PrismOffice\Application\SaveScenarioService;
use PrismOffice\Domain\Entity\ScenarioDefinition;
use PrismOffice\Domain\Entity\LoadInstruction;
use PrismOffice\Domain\Entity\PurgeInstruction;
use Tests\PrismOffice\Fake\FakeFileSystem;

final class SaveScenarioServiceTest extends TestCase
{
    private FakeFileSystem $fileSystem;
    private SaveScenarioService $service;
    private string $prismDir = '/prism';

    protected function setUp(): void
    {
        $this->fileSystem = new FakeFileSystem();
        $this->fileSystem->addDirectory($this->prismDir);
        $this->service = new SaveScenarioService($this->prismDir, $this->fileSystem);
    }

    public function testExecuteSavesScenarioSuccessfully(): void
    {
        $loadInstruction = new LoadInstruction('users', [['id' => 1, 'name' => 'John']], [], null);
        $scenario = new ScenarioDefinition(
            'test_scenario',
            [],
            [],
            [$loadInstruction],
            []
        );

        $filepath = $this->service->execute($scenario);

        $this->assertSame($this->prismDir . '/test_scenario.yaml', $filepath);
        $content = $this->fileSystem->getFile($filepath);
        $this->assertNotNull($content);
        $this->assertStringContainsString('load:', $content);
        $this->assertStringContainsString('table: users', $content);
    }

    public function testExecuteSavesScenarioWithAllSections(): void
    {
        $loadInstruction = new LoadInstruction('users', [['id' => 1]], ['id' => 'integer'], null);
        $purgeInstruction = new PurgeInstruction('sessions', ['user_id' => 1]);

        $scenario = new ScenarioDefinition(
            'full_scenario',
            ['base_scenario'],
            ['table_name' => 'users'],
            [$loadInstruction],
            [$purgeInstruction]
        );

        $filepath = $this->service->execute($scenario);

        $content = $this->fileSystem->getFile($filepath);
        $this->assertNotNull($content);
        $this->assertStringContainsString('import:', $content);
        $this->assertStringContainsString('vars:', $content);
        $this->assertStringContainsString('load:', $content);
        $this->assertStringContainsString('purge:', $content);
    }

    public function testExecuteCreatesDirectoryIfNotExists(): void
    {
        $scenario = new ScenarioDefinition(
            'subfolder/test_scenario',
            [],
            [],
            [new LoadInstruction('users', [['id' => 1]], [], null)],
            []
        );

        $filepath = $this->service->execute($scenario);

        $this->assertTrue($this->fileSystem->fileExists($filepath));
        $this->assertTrue($this->fileSystem->isDirectory($this->prismDir . '/subfolder'));
    }

    public function testExecuteIncludesHeaderInFile(): void
    {
        $scenario = new ScenarioDefinition(
            'scenario_with_header',
            [],
            [],
            [new LoadInstruction('users', [['id' => 1]], [], null)],
            []
        );

        $filepath = $this->service->execute($scenario);
        $content = $this->fileSystem->getFile($filepath);

        $this->assertNotNull($content);
        $this->assertStringContainsString('# Scenario: scenario_with_header', $content);
        $this->assertStringContainsString('# Created by PrismOffice on', $content);
        $this->assertStringContainsString('# Usage:', $content);
        $this->assertStringContainsString('php bin/console app:prism:load scenario_with_header --scope=YOUR_SCOPE', $content);
    }

    public function testExecuteSavesScenarioWithPivot(): void
    {
        $loadInstruction = new LoadInstruction('users', [['id' => 1]], [], ['column' => 'user_id']);
        $scenario = new ScenarioDefinition(
            'scenario_with_pivot',
            [],
            [],
            [$loadInstruction],
            []
        );

        $filepath = $this->service->execute($scenario);
        $content = $this->fileSystem->getFile($filepath);

        $this->assertNotNull($content);
        $this->assertStringContainsString('pivot:', $content);
    }

    public function testExecuteIncludesPurgeSectionWhenPresent(): void
    {
        $purgeInstruction = new PurgeInstruction('users', ['id' => 1], true);
        $scenario = new ScenarioDefinition(
            'scenario_with_purge_pivot',
            [],
            [],
            [new LoadInstruction('users', [['id' => 1]], [], null)],
            [$purgeInstruction]
        );

        $filepath = $this->service->execute($scenario);
        $content = $this->fileSystem->getFile($filepath);

        $this->assertNotNull($content);
        $this->assertStringContainsString('purge:', $content);
        $this->assertStringContainsString('purge_pivot: true', $content);
    }

    public function testExecuteThrowsExceptionWhenDirectoryCreationFails(): void
    {
        $this->fileSystem->createDirectoryShouldFail = true;

        $scenario = new ScenarioDefinition(
            'sub/scenario',
            [],
            [],
            [new LoadInstruction('users', [['id' => 1]], [], null)],
            []
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to create directory');

        $this->service->execute($scenario);
    }

    public function testExecuteThrowsExceptionWhenFileWriteFails(): void
    {
        $this->fileSystem->writeFileShouldFail = true;

        $scenario = new ScenarioDefinition(
            'test_scenario',
            [],
            [],
            [new LoadInstruction('users', [['id' => 1]], [], null)],
            []
        );

        $this->expectException(\RuntimeException::class);

        $this->service->execute($scenario);
    }

    public function testExecuteSavesScenarioWithDatabase(): void
    {
        $loadInstruction = new LoadInstruction('users', [['id' => 1]], [], null, 'secondary');
        $scenario = new ScenarioDefinition(
            'db_scenario',
            [],
            [],
            [$loadInstruction],
            []
        );

        $filepath = $this->service->execute($scenario);
        $content = $this->fileSystem->getFile($filepath);

        $this->assertNotNull($content);
        $this->assertStringContainsString('db: secondary', $content);
    }

    public function testExecuteSavesScenarioWithPurgeDatabaseField(): void
    {
        $purgeInstruction = new PurgeInstruction('sessions', ['user_id' => 1], false, 'secondary');
        $scenario = new ScenarioDefinition(
            'purge_db_scenario',
            [],
            [],
            [new LoadInstruction('users', [['id' => 1]], [], null)],
            [$purgeInstruction]
        );

        $filepath = $this->service->execute($scenario);
        $content = $this->fileSystem->getFile($filepath);

        $this->assertNotNull($content);
        $this->assertStringContainsString('purge:', $content);
        $this->assertStringContainsString('db: secondary', $content);
    }

    public function testExecuteNormalizesNullStringToNull(): void
    {
        $loadInstruction = new LoadInstruction('users', ['name' => 'null', 'email' => ''], [], null);
        $scenario = new ScenarioDefinition(
            'test_null_normalization',
            [],
            [],
            [$loadInstruction],
            []
        );

        $filepath = $this->service->execute($scenario);
        $content = $this->fileSystem->getFile($filepath);

        $this->assertNotNull($content);
        $this->assertStringContainsString('name: null', $content);
        $this->assertStringContainsString("email: ''", $content);
    }

    public function testExecuteNormalizesNullInVariables(): void
    {
        $scenario = new ScenarioDefinition(
            'test_vars_null',
            [],
            ['var1' => 'null', 'var2' => '', 'var3' => 'value'],
            [new LoadInstruction('users', [['id' => 1]], [], null)],
            []
        );

        $filepath = $this->service->execute($scenario);
        $content = $this->fileSystem->getFile($filepath);

        $this->assertNotNull($content);
        $this->assertStringContainsString('var1: null', $content);
        $this->assertStringContainsString("var2: ''", $content);
        $this->assertStringContainsString('var3: value', $content);
    }

    public function testExecuteNormalizesNullInPurgeWhere(): void
    {
        $purgeInstruction = new PurgeInstruction('users', ['status' => 'null', 'email' => ''], false);
        $scenario = new ScenarioDefinition(
            'test_purge_null',
            [],
            [],
            [new LoadInstruction('users', [['id' => 1]], [], null)],
            [$purgeInstruction]
        );

        $filepath = $this->service->execute($scenario);
        $content = $this->fileSystem->getFile($filepath);

        $this->assertNotNull($content);
        $this->assertStringContainsString('status: null', $content);
        $this->assertStringContainsString("email: ''", $content);
    }

    public function testExecuteNormalizesPhpNullValue(): void
    {
        $loadInstruction = new LoadInstruction('users', [['id' => 1, 'name' => null, 'email' => 'test@test.com']], [], null);
        $scenario = new ScenarioDefinition(
            'test_php_null',
            [],
            [],
            [$loadInstruction],
            []
        );

        $filepath = $this->service->execute($scenario);
        $content = $this->fileSystem->getFile($filepath);

        $this->assertNotNull($content);
        $this->assertStringContainsString('id: 1', $content);
        $this->assertStringContainsString('name: null', $content);
        $this->assertStringContainsString('email: test@test.com', $content);
    }

    /**
     * Test que buildHeader est appelé et génère le bon header
     */
    public function testExecuteIncludesHeaderInGeneratedYaml(): void
    {
        $scenario = new ScenarioDefinition(
            'test_header',
            [],
            [],
            [new LoadInstruction('users', [['id' => 1]], [], null)],
            []
        );

        $filepath = $this->service->execute($scenario);
        $content = $this->fileSystem->getFile($filepath);

        $this->assertNotNull($content);
        $this->assertStringContainsString('# Scenario: test_header', $content);
        $this->assertStringContainsString('# Created by PrismOffice on', $content);
        $this->assertStringContainsString('# Usage:', $content);
        $this->assertStringContainsString('php bin/console app:prism:load test_header --scope=YOUR_SCOPE', $content);
        $this->assertStringContainsString('php bin/console app:prism:purge test_header --scope=YOUR_SCOPE', $content);
    }

    /**
     * Test la normalisation récursive dans des tableaux imbriqués
     */
    public function testExecuteNormalizesNullInNestedArrays(): void
    {
        $loadInstruction = new LoadInstruction(
            'users',
            [
                [
                    'id' => 1,
                    'nested' => [
                        'level1' => [
                            'value1' => 'null',
                            'value2' => '',
                            'value3' => 'actual'
                        ]
                    ]
                ]
            ],
            [],
            null
        );

        $scenario = new ScenarioDefinition(
            'test_nested',
            [],
            [],
            [$loadInstruction],
            []
        );

        $filepath = $this->service->execute($scenario);
        $content = $this->fileSystem->getFile($filepath);

        $this->assertNotNull($content);
        $this->assertStringContainsString('value1: null', $content);
        $this->assertStringContainsString("value2: ''", $content);
        $this->assertStringContainsString("value3: actual", $content);
    }
}
