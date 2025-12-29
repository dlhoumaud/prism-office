<?php

declare(strict_types=1);

namespace PrismOffice\Application;

use PrismOffice\Application\Contract\FileSystemInterface;
use PrismOffice\Domain\Entity\ScenarioDefinition;
use PrismOffice\Domain\Entity\LoadInstruction;
use PrismOffice\Domain\Entity\PurgeInstruction;
use Symfony\Component\Yaml\Yaml;

/**
 * Service pour charger un scénario YAML existant et le convertir en ScenarioDefinition
 */
final class LoadScenarioForEditService
{
    public function __construct(
        private readonly string $prismDirectory,
        private readonly FileSystemInterface $fileSystem
    ) {
    }

    public function execute(string $scenarioName): ScenarioDefinition
    {
        $yamlFilePath = $this->prismDirectory . '/' . $scenarioName . '.yaml';

        if (!$this->fileSystem->fileExists($yamlFilePath)) {
            throw new \RuntimeException(sprintf('Scenario file not found: %s', $yamlFilePath));
        }

        $content = $this->fileSystem->readFile($yamlFilePath);
        if ($content === false) {
            throw new \RuntimeException(sprintf('Failed to read scenario file: %s', $yamlFilePath));
        }

        $yaml = Yaml::parse($content);
        if (!is_array($yaml)) {
            throw new \RuntimeException('Invalid YAML structure');
        }

        // Parse imports
        $imports = [];
        if (isset($yaml['import']) && is_array($yaml['import'])) {
            $imports = $yaml['import'];
        }

        // Parse variables
        $variables = [];
        if (isset($yaml['vars']) && is_array($yaml['vars'])) {
            $variables = $yaml['vars'];
        }

        // Parse load instructions
        $loadInstructions = [];
        if (isset($yaml['load']) && is_array($yaml['load'])) {
            foreach ($yaml['load'] as $loadEntry) {
                if (!is_array($loadEntry)) {
                    continue;
                }

                $table = $loadEntry['table'] ?? '';
                $data = $loadEntry['data'] ?? [];
                $types = $loadEntry['types'] ?? [];
                $pivot = $loadEntry['pivot'] ?? null;
                $database = $loadEntry['database'] ?? $loadEntry['db'] ?? null;

                if ($table !== '') {
                    $loadInstructions[] = new LoadInstruction($table, $data, $types, $pivot, $database);
                }
            }
        }

        // Parse purge instructions
        $purgeInstructions = [];
        if (isset($yaml['purge']) && is_array($yaml['purge'])) {
            foreach ($yaml['purge'] as $purgeEntry) {
                if (!is_array($purgeEntry)) {
                    continue;
                }

                $table = $purgeEntry['table'] ?? '';
                $where = $purgeEntry['where'] ?? [];
                $purgePivot = $purgeEntry['purge_pivot'] ?? false;
                $database = $purgeEntry['database'] ?? $purgeEntry['db'] ?? null;

                if ($table !== '') {
                    $purgeInstructions[] = new PurgeInstruction($table, $where, $purgePivot, $database);
                }
            }
        }

        return new ScenarioDefinition(
            $scenarioName,
            $imports,
            $variables,
            $loadInstructions,
            $purgeInstructions
        );
    }
}
