<?php

declare(strict_types=1);

namespace PrismOffice\Tests\Domain\Entity;

use PHPUnit\Framework\TestCase;
use PrismOffice\Domain\Entity\LoadInstruction;

final class LoadInstructionTest extends TestCase
{
    public function testGetters(): void
    {
        $table = 'users';
        $data = [['id' => 1, 'name' => 'John']];
        $types = ['id' => 'integer'];
        $pivot = ['column' => 'user_id'];

        $instruction = new LoadInstruction($table, $data, $types, $pivot);

        $this->assertSame($table, $instruction->getTable());
        $this->assertSame($data, $instruction->getData());
        $this->assertSame($types, $instruction->getTypes());
        $this->assertSame($pivot, $instruction->getPivot());
        $this->assertTrue($instruction->hasTypes());
        $this->assertTrue($instruction->hasPivot());
    }

    public function testHasTypesReturnsFalseWhenEmpty(): void
    {
        $instruction = new LoadInstruction('users', [['id' => 1]], [], null);

        $this->assertFalse($instruction->hasTypes());
    }

    public function testHasPivotReturnsFalseWhenNull(): void
    {
        $instruction = new LoadInstruction('users', [['id' => 1]], [], null);

        $this->assertFalse($instruction->hasPivot());
    }
}
