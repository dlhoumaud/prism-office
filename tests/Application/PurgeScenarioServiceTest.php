<?php

declare(strict_types=1);

namespace Tests\PrismOffice\Application;

use PHPUnit\Framework\TestCase;
use PrismOffice\Application\PurgeScenarioService;
use Tests\PrismOffice\Fake\FakePrism;
use Tests\PrismOffice\Fake\FakePrismRegistry;
use Tests\PrismOffice\Fake\FakePurgePrism;

final class PurgeScenarioServiceTest extends TestCase
{
    public function testExecuteCallsPurgePrismWithCorrectArguments(): void
    {
        $prism = new FakePrism('test_users');
        $registry = new FakePrismRegistry(['test_users' => $prism]);
        $purgePrism = new FakePurgePrism();

        $service = new PurgeScenarioService($purgePrism, $registry);
        $service->execute('test_users', 'dev_john');

        self::assertCount(1, $purgePrism->calls);
        self::assertSame($prism, $purgePrism->calls[0]['prism']);
        self::assertSame('dev_john', $purgePrism->calls[0]['scope']->toString());
    }

    public function testExecuteThrowsExceptionWhenScenarioNotFound(): void
    {
        $registry = new FakePrismRegistry([]);
        $purgePrism = new FakePurgePrism();

        $service = new PurgeScenarioService($purgePrism, $registry);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Scenario "unknown" not found');

        $service->execute('unknown', 'dev_john');
    }

    public function testExecuteAllScopePurgesAllScenarios(): void
    {
        $prism1 = new FakePrism('scenario1');
        $prism2 = new FakePrism('scenario2');
        $registry = new FakePrismRegistry(['scenario1' => $prism1, 'scenario2' => $prism2]);
        $purgePrism = new FakePurgePrism();

        $service = new PurgeScenarioService($purgePrism, $registry);
        $service->executeAllScope('dev_john');

        self::assertCount(2, $purgePrism->calls);
        self::assertSame($prism1, $purgePrism->calls[0]['prism']);
        self::assertSame($prism2, $purgePrism->calls[1]['prism']);
        self::assertSame('dev_john', $purgePrism->calls[0]['scope']->toString());
        self::assertSame('dev_john', $purgePrism->calls[1]['scope']->toString());
    }
}
