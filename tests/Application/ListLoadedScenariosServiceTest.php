<?php

declare(strict_types=1);

namespace Tests\PrismOffice\Application;

use PHPUnit\Framework\TestCase;
use PrismOffice\Application\ListLoadedScenariosService;
use PrismOffice\Domain\Entity\LoadedScenario;
use Tests\PrismOffice\Fake\FakeLoadedScenarioRepository;

final class ListLoadedScenariosServiceTest extends TestCase
{
    public function testReturnsAllLoadedScenariosFromRepository(): void
    {
        $loadedScenarios = [
            new LoadedScenario('test_users', 'dev_john', 10),
            new LoadedScenario('chat_messages', 'dev_alice', 25),
        ];

        $repository = new FakeLoadedScenarioRepository($loadedScenarios);
        $service = new ListLoadedScenariosService($repository);

        $result = iterator_to_array($service->execute());

        self::assertCount(2, $result);
        self::assertSame('test_users', $result[0]->getScenarioName());
        self::assertSame('dev_john', $result[0]->getScope());
        self::assertSame(10, $result[0]->getResourceCount());
    }

    public function testReturnsEmptyArrayWhenNoLoadedScenarios(): void
    {
        $repository = new FakeLoadedScenarioRepository([]);
        $service = new ListLoadedScenariosService($repository);

        $result = iterator_to_array($service->execute());

        self::assertCount(0, $result);
    }
}
