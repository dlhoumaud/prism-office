<?php

declare(strict_types=1);

namespace PrismOffice;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle PrismOffice - Interface web pour gérer les scénarios Prism
 *
 * Interface de développement autonome (comme le Symfony Profiler)
 * - Activé uniquement en mode debug
 * - Interface dark indépendante
 * - CSS/JS inline (pas de compilation)
 */
final class PrismOfficeBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
