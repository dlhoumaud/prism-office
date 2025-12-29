<?php

declare(strict_types=1);

namespace Tests\PrismOffice\Application;

use PHPUnit\Framework\TestCase;
use PrismOffice\Application\LoadScenarioService;
use Tests\PrismOffice\Fake\FakeLoadPrism;
use Tests\PrismOffice\Fake\FakePrism;
use Tests\PrismOffice\Fake\FakePrismRegistry;

final class LoadScenarioServiceTest extends TestCase
{
    public function testExecuteCallsLoadPrismWithCorrectArguments(): void
    {
        $prism = new FakePrism('test_users');
        $registry = new FakePrismRegistry(['test_users' => $prism]);
        $loadPrism = new FakeLoadPrism();

        $service = new LoadScenarioService($loadPrism, $registry);
        $service->execute('test_users', 'dev_john');

        self::assertCount(1, $loadPrism->calls);
        self::assertSame($prism, $loadPrism->calls[0]['prism']);
        self::assertSame('dev_john', $loadPrism->calls[0]['scope']->toString());
    }

    public function testExecuteThrowsExceptionWhenScenarioNotFound(): void
    {
        $registry = new FakePrismRegistry([]);
        $loadPrism = new FakeLoadPrism();

        $service = new LoadScenarioService($loadPrism, $registry);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Scenario "unknown" not found');

        $service->execute('unknown', 'dev_john');
    }
}
