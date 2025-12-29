<?php

declare(strict_types=1);

namespace PrismOffice\Tests\Domain\Entity;

use PHPUnit\Framework\TestCase;
use PrismOffice\Domain\Entity\PurgeInstruction;

final class PurgeInstructionTest extends TestCase
{
    public function testGetters(): void
    {
        $table = 'users';
        $where = ['id' => 1];
        $purgePivot = true;

        $instruction = new PurgeInstruction($table, $where, $purgePivot);

        $this->assertSame($table, $instruction->getTable());
        $this->assertSame($where, $instruction->getWhere());
        $this->assertTrue($instruction->getPurgePivot());
    }

    public function testGetPurgePivotDefaultsToFalse(): void
    {
        $instruction = new PurgeInstruction('users', ['id' => 1]);

        $this->assertFalse($instruction->getPurgePivot());
    }
}
