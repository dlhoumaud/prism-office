<?php

declare(strict_types=1);

namespace Tests\PrismOffice\Fake;

use Prism\Domain\Contract\PrismInterface;
use Prism\Domain\ValueObject\Scope;
use PrismOffice\Domain\Contract\LoadPrismInterface;

/**
 * Fake LoadPrism pour les tests (pas de mock)
 */
final class FakeLoadPrism implements LoadPrismInterface
{
    /** @var array<array{prism: PrismInterface, scope: Scope}> */
    public array $calls = [];

    public function execute(PrismInterface $prism, ?Scope $scope = null): void
    {
        $this->calls[] = [
            'prism' => $prism,
            'scope' => $scope ?? Scope::fromString('default'),
        ];
    }
}
