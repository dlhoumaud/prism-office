<?php

declare(strict_types=1);

namespace PrismOffice\Domain\Entity;

/**
 * Entité représentant un scénario disponible
 */
final class ScenarioInfo
{
    public function __construct(
        private readonly string $name,
        private readonly string $className
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClassName(): string
    {
        return $this->className;
    }
}
