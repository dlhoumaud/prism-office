<?php

declare(strict_types=1);

namespace PrismOffice\Application;

use PrismOffice\Application\Contract\FileSystemInterface;
use PrismOffice\Domain\Entity\ScenarioDefinition;
use PrismOffice\Domain\Exception\ScenarioSaveException;
use Symfony\Component\Yaml\Yaml;

/**
 * Service pour sauvegarder une définition de scénario dans un fichier YAML
 */
final class SaveScenarioService
{
    public function __construct(
        private readonly string $prismDirectory,
        private readonly FileSystemInterface $fileSystem
    ) {
    }

    /**
     * Sauvegarde le scénario dans prism/{name}.yaml
     *
     * @throws \RuntimeException Si le fichier ne peut pas être créé
     */
    public function execute(ScenarioDefinition $scenario): string
    {
        $filename = $scenario->getName() . '.yaml';
        $filepath = $this->prismDirectory . '/' . $filename;

        // Créer les répertoires parents si nécessaire
        $directory = $this->fileSystem->getDirectoryName($filepath);
        if (!$this->fileSystem->isDirectory($directory)) {
            if (!$this->fileSystem->createDirectory($directory, 0755, true)) {
                throw new \RuntimeException(sprintf('Failed to create directory: %s', $directory));
            }
        }

        // Construire le tableau YAML
        $yamlData = $this->buildYamlStructure($scenario);

        // Convertir en YAML avec le flag pour garder les listes inline
        $yamlContent = Yaml::dump($yamlData, 10, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);

        // Post-traitement : mettre "- table:" sur une seule ligne
        // Transformer "  -\n    table:" en "  - table:"
        $yamlContent = preg_replace('/^(\s+)-\n\1  table:/m', '$1- table:', $yamlContent);

        // Ajouter un en-tête de commentaire
        $header = $this->buildHeader($scenario);
        $content = $header . "\n" . $yamlContent;

        // Écrire le fichier
        $result = $this->fileSystem->writeFile($filepath, $content);

        if ($result === false) {
            throw ScenarioSaveException::failedToWrite($filepath);
        }

        return $filepath;
    }

    /**
     * Construit la structure de données pour le YAML
     *
     * @return array<string, mixed>
     */
    private function buildYamlStructure(ScenarioDefinition $scenario): array
    {
        // Fonction de normalisation : convertit 'null' (string) en null (PHP) et préserve les chaînes vides
        $normalize = function ($value) use (&$normalize) {
            if (is_array($value)) {
                $result = [];
                foreach ($value as $k => $v) {
                    $result[$k] = $normalize($v);
                }
                return $result;
            }
            // Conversion explicite de la chaîne 'null' en null PHP
            if ($value === 'null') {
                return null;
            }
            if ($value === null) {
                return null;
            }
            if ($value === '') {
                return "";
            }
            return $value;
        };

        $data = [];

        // Global scenario info
        if ($scenario->getInfo()) {
            $data['info'] = $normalize($scenario->getInfo());
        }

        // Section import
        if ($scenario->hasImports()) {
            $data['import'] = $normalize($scenario->getImports());
        }

        // Section vars
        if ($scenario->hasVariables()) {
            $data['vars'] = $normalize($scenario->getVariables());
        }

        // Section load (obligatoire)
        if ($scenario->hasLoadInstructions()) {
            $data['load'] = [];
            foreach ($scenario->getLoadInstructions() as $instruction) {
                $loadEntry = [
                    'table' => $instruction->getTable(),
                    'data' => $normalize($instruction->getData()),
                ];

                // Instruction-level info
                if (method_exists($instruction, 'getInfo') && $instruction->getInfo()) {
                    $loadEntry['info'] = $normalize($instruction->getInfo());
                }

                if ($instruction->getDatabase()) {
                    $loadEntry['db'] = $instruction->getDatabase();
                }

                if ($instruction->hasTypes()) {
                    $loadEntry['types'] = $normalize($instruction->getTypes());
                }

                if ($instruction->hasPivot()) {
                    $loadEntry['pivot'] = $normalize($instruction->getPivot());
                }

                $data['load'][] = $loadEntry;
            }
        }

        // Section purge (optionnel)
        if ($scenario->hasPurgeInstructions()) {
            $data['purge'] = [];
            foreach ($scenario->getPurgeInstructions() as $instruction) {
                $purgeEntry = [
                    'table' => $instruction->getTable(),
                    'where' => $normalize($instruction->getWhere()),
                ];

                if ($instruction->getDatabase()) {
                    $purgeEntry['db'] = $instruction->getDatabase();
                }

                if ($instruction->getPurgePivot()) {
                    $purgeEntry['purge_pivot'] = true;
                }

                // Instruction-level info for purge
                if (method_exists($instruction, 'getInfo') && $instruction->getInfo()) {
                    $purgeEntry['info'] = $normalize($instruction->getInfo());
                }

                $data['purge'][] = $purgeEntry;
            }
        }

        return $data;
    }

    /**
     * Construit l'en-tête de commentaire du fichier
     */
    private function buildHeader(ScenarioDefinition $scenario): string
    {
        $lines = [
            '# Scenario: ' . $scenario->getName(),
            '#',
            '# Created by PrismOffice on ' . date('Y-m-d H:i:s'),
            '#',
            '# Usage:',
            '#   php bin/console app:prism:load ' . $scenario->getName() . ' --scope=YOUR_SCOPE',
            '#   php bin/console app:prism:purge ' . $scenario->getName() . ' --scope=YOUR_SCOPE',
        ];

        return implode("\n", $lines);
    }
}
