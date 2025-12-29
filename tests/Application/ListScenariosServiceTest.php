<?php

declare(strict_types=1);

namespace Tests\PrismOffice\Application;

use PHPUnit\Framework\TestCase;
use PrismOffice\Application\ListScenariosService;
use PrismOffice\Domain\Entity\ScenarioInfo;
use Tests\PrismOffice\Fake\FakeScenarioRepository;

final class ListScenariosServiceTest extends TestCase
{
    public function testReturnsAllScenariosFromRepository(): void
    {
        $scenarios = [
            new ScenarioInfo('test_users', 'App\\Scenario\\TestUsers'),
            new ScenarioInfo('chat_messages', 'App\\Scenario\\ChatMessages'),
        ];

        $repository = new FakeScenarioRepository($scenarios);
        $service = new ListScenariosService($repository);

        $result = iterator_to_array($service->execute());

        self::assertCount(2, $result);
        self::assertSame('test_users', $result[0]->getName());
        self::assertSame('chat_messages', $result[1]->getName());
    }

    public function testReturnsEmptyArrayWhenNoScenarios(): void
    {
        $repository = new FakeScenarioRepository([]);
        $service = new ListScenariosService($repository);

        $result = iterator_to_array($service->execute());

        self::assertCount(0, $result);
    }
}
