<?php

declare(strict_types=1);

namespace Tests\PrismOffice\Fake;

use Prism\Domain\Contract\PrismInterface;
use Prism\Domain\Contract\PrismRegistryInterface;
use Prism\Domain\ValueObject\PrismName;

/**
 * Fake PrismRegistry pour les tests (pas de mock)
 */
final class FakePrismRegistry implements PrismRegistryInterface
{
    /**
     * @param array<string, PrismInterface> $prisms
     */
    public function __construct(
        private array $prisms = []
    ) {
    }

    public function get(PrismName $name): ?PrismInterface
    {
        return $this->prisms[$name->toString()] ?? null;
    }

    public function all(): array
    {
        return array_values($this->prisms);
    }

    public function has(PrismName $name): bool
    {
        return isset($this->prisms[$name->toString()]);
    }

    public function register(PrismInterface $prism): void
    {
        $this->prisms[$prism->getName()->toString()] = $prism;
    }
}
