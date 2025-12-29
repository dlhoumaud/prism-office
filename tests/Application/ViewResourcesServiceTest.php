<?php

declare(strict_types=1);

namespace Tests\PrismOffice\Application;

use PHPUnit\Framework\TestCase;
use PrismOffice\Application\ViewResourcesService;
use PrismOffice\Domain\Entity\LoadedScenario;
use Tests\PrismOffice\Fake\FakeLoadedScenarioRepository;

final class ViewResourcesServiceTest extends TestCase
{
    public function testReturnsResourcesForGivenScenarioAndScope(): void
    {
        $resources = [
            'test_users-dev_john' => [
                ['table' => 'users', 'column' => 'id', 'value' => 1],
                ['table' => 'users', 'column' => 'id', 'value' => 2],
            ],
        ];

        $repository = new FakeLoadedScenarioRepository([], $resources);
        $service = new ViewResourcesService($repository);

        $result = $service->execute('test_users', 'dev_john');

        self::assertCount(2, $result);
        self::assertSame('users', $result[0]['table']);
        self::assertSame(1, $result[0]['value']);
    }

    public function testReturnsEmptyArrayWhenNoResources(): void
    {
        $repository = new FakeLoadedScenarioRepository([], []);
        $service = new ViewResourcesService($repository);

        $result = $service->execute('test_users', 'dev_john');

        self::assertCount(0, $result);
    }
}
