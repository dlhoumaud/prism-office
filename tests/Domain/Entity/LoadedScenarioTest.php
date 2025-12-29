<?php

declare(strict_types=1);

namespace Tests\PrismOffice\Domain\Entity;

use PHPUnit\Framework\TestCase;
use PrismOffice\Domain\Entity\LoadedScenario;

final class LoadedScenarioTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $loaded = new LoadedScenario('test_users', 'dev_john', 42);

        self::assertSame('test_users', $loaded->getScenarioName());
        self::assertSame('dev_john', $loaded->getScope());
        self::assertSame(42, $loaded->getResourceCount());
    }

    public function testGetIdentifierCombinesNameAndScope(): void
    {
        $loaded = new LoadedScenario('test_users', 'dev_john', 42);

        self::assertSame('test_users-dev_john', $loaded->getIdentifier());
    }

    public function testIsImmutable(): void
    {
        $loaded = new LoadedScenario('test_users', 'dev_john', 42);

        $this->expectException(\Error::class);
        $loaded->scope = 'other'; // @phpstan-ignore-line
    }
}
