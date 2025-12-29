<?php

declare(strict_types=1);

namespace PrismOffice\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Extension Symfony pour PrismOffice
 */
final class PrismOfficeExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Charger les services seulement si enabled
        if ($config['enabled']) {
            $loader = new YamlFileLoader(
                $container,
                new FileLocator(__DIR__ . '/../../config')
            );
            $loader->load('services.yaml');

            // Paramètres de configuration
            $container->setParameter('prism_office.enabled', $config['enabled']);
            $container->setParameter('prism_office.route_prefix', $config['route_prefix']);
        }
    }
}
