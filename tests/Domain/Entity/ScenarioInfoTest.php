<?php

declare(strict_types=1);

namespace Tests\PrismOffice\Domain\Entity;

use PHPUnit\Framework\TestCase;
use PrismOffice\Domain\Entity\ScenarioInfo;

final class ScenarioInfoTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $scenario = new ScenarioInfo('test_users', 'App\\Scenario\\TestUsers');

        self::assertSame('test_users', $scenario->getName());
        self::assertSame('App\\Scenario\\TestUsers', $scenario->getClassName());
    }

    public function testIsImmutable(): void
    {
        $scenario = new ScenarioInfo('test_users', 'App\\Scenario\\TestUsers');

        $this->expectException(\Error::class);
        $scenario->name = 'other'; // @phpstan-ignore-line
    }
}
