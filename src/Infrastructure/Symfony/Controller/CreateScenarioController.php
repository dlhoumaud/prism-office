<?php

declare(strict_types=1);

namespace PrismOffice\Infrastructure\Symfony\Controller;

use PrismOffice\Application\Contract\DatabaseSchemaProviderInterface;
use PrismOffice\Application\ListScenariosService;
use PrismOffice\Application\ListYamlFilesService;
use PrismOffice\Application\LoadScenarioForEditService;
use PrismOffice\Application\SaveScenarioService;
use PrismOffice\Domain\Entity\LoadInstruction;
use PrismOffice\Domain\Entity\PurgeInstruction;
use PrismOffice\Domain\Entity\ScenarioDefinition;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CreateScenarioController extends AbstractController
{
    public function __construct(
        private readonly ListScenariosService $listScenariosService,
        private readonly SaveScenarioService $saveScenarioService,
        private readonly LoadScenarioForEditService $loadScenarioForEditService,
        private readonly ListYamlFilesService $listYamlFilesService,
        private readonly DatabaseSchemaProviderInterface $databaseSchemaProvider
    ) {
    }

    #[Route('/prism/create', name: 'prism_office_create', methods: ['GET'])]
    public function create(Request $request): Response
    {
        // Générer un scope aléatoire si aucun n'existe en session
        $session = $request->getSession();
        if (!$session->has('prism_scope')) {
            $session->set('prism_scope', $this->generateRandomScope());
        }

        // Récupérer le dossier cible depuis les paramètres
        $targetFolder = $request->query->get('folder');
        if (!is_string($targetFolder) || trim($targetFolder) === '') {
            $targetFolder = '';
        } else {
            $targetFolder = trim($targetFolder);
        }

        // Récupérer la liste de tous les fichiers YAML pour les imports (incluant sous-dossiers)
        $yamlFiles = $this->listYamlFilesService->execute();

        // Get database schema for autocomplete
        $tablesWithColumns = $this->databaseSchemaProvider->getTablesWithColumns();

        // Check if we're editing an existing scenario
        $scenarioName = $request->query->get('edit');
        $scenarioData = null;

        if ($scenarioName && is_string($scenarioName)) {
            try {
                $scenarioData = $this->loadScenarioForEditService->execute($scenarioName);

                // Si on édite un scénario et que targetFolder n'est pas défini,
                // extraire le dossier depuis le nom du scénario
                if ($targetFolder === '' && str_contains($scenarioName, '/')) {
                    $targetFolder = dirname($scenarioName);
                    // Si dirname retourne '.' (pas de dossier), on laisse vide
                    if ($targetFolder === '.') {
                        $targetFolder = '';
                    }
                }
            } catch (\Exception $e) {
                $this->addFlash('error', sprintf('Failed to load scenario: %s', $e->getMessage()));
            }
        }

        return $this->render('@PrismOffice/create_scenario.html.twig', [
            'existing_scenarios' => $yamlFiles,
            'scenario_data' => $scenarioData,
            'edit_mode' => $scenarioName !== null,
            'tables_with_columns' => $tablesWithColumns,
            'database_aliases' => $this->databaseSchemaProvider->getDatabaseAliases(),
            'default_scope' => $this->generateRandomScope(),
            'target_folder' => $targetFolder,
        ]);
    }

    #[Route('/prism/create/import-vars', name: 'prism_office_get_import_vars', methods: ['POST'])]
    public function getImportVars(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $imports = $data['imports'] ?? [];

            if (!is_array($imports)) {
                return $this->json(['variables' => []]);
            }

            $allVariables = $this->extractVariablesFromImports($imports);

            $allInfos = $this->extractInfosFromImports($imports);

            return $this->json([
                'variables' => $allVariables,
                'infos' => $allInfos,
            ]);
        } catch (\Exception $e) {
            return $this->json(['variables' => [], 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Extract top-level "info" from imported YAML files (recursively)
     *
     * @param array<string> $imports
     * @param array<string> $visited
     * @return array<string, string|null>
     */
    private function extractInfosFromImports(array $imports, array $visited = []): array
    {
        $infos = [];
        $projectDir = $this->getParameter('kernel.project_dir');
        if (!is_string($projectDir)) {
            return [];
        }
        $scenariosPath = $projectDir . '/prism/yaml';

        foreach ($imports as $importPath) {
            if (in_array($importPath, $visited, true)) {
                continue;
            }

            $visited[] = $importPath;

            $filePath = $scenariosPath . '/' . $importPath . '.yaml';
            if (!file_exists($filePath)) {
                continue;
            }

            try {
                $content = file_get_contents($filePath);
                if ($content === false) {
                    continue;
                }

                $yaml = \Symfony\Component\Yaml\Yaml::parse($content);
                if (!is_array($yaml)) {
                    continue;
                }

                $infos[$importPath] = isset($yaml['info']) && is_string($yaml['info']) ? $yaml['info'] : null;

                if (isset($yaml['import']) && is_array($yaml['import'])) {
                    $nested = $this->extractInfosFromImports($yaml['import'], $visited);
                    $infos = array_merge($infos, $nested);
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $infos;
    }
    #[Route('/prism/create/database-schema', name: 'prism_office_get_database_schema', methods: ['POST'])]
    public function getDatabaseSchema(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $database = $data['database'] ?? '';

            if (!is_string($database) || trim($database) === '') {
                return $this->json(['error' => 'Database parameter is required'], 400);
            }

            // Retirer les % si présents (format %logiciel% -> logiciel)
            $database = trim($database, '%');

            $tablesWithColumns = $this->databaseSchemaProvider->getTablesWithColumnsForDatabase($database);

            return $this->json(['tables' => $tablesWithColumns]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
    #[Route('/prism/create', name: 'prism_office_create_save', methods: ['POST'])]
    public function save(Request $request): Response
    {
        try {
            // Récupérer les données du formulaire
            $data = json_decode($request->getContent(), true);

            if (!$data) {
                throw new \InvalidArgumentException('Invalid JSON data');
            }

            // Construire le scénario
            $scenario = $this->buildScenarioFromRequest($data);

            // Sauvegarder le fichier
            $filepath = $this->saveScenarioService->execute($scenario);

            $this->addFlash('success', sprintf('Scenario "%s" created successfully!', $scenario->getName()));

            return $this->json([
                'success' => true,
                'message' => 'Scenario created successfully',
                'scenario_name' => $scenario->getName(),
                'filepath' => $filepath,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Construit un ScenarioDefinition à partir des données de requête
     *
     * @param array<string, mixed> $data
     */
    private function buildScenarioFromRequest(array $data): ScenarioDefinition
    {
        $name = $data['name'] ?? throw new \InvalidArgumentException('Scenario name is required');
        $info = $data['info'] ?? null;

        // Imports
        $imports = $data['imports'] ?? [];

        // Variables
        $variables = $data['variables'] ?? [];

        // Load instructions
        $loadInstructions = [];
        $loadData = $data['load'] ?? [];
        if (!is_array($loadData)) {
            throw new \InvalidArgumentException('Load data must be an array');
        }
        foreach ($loadData as $loadItem) {
            if (!is_array($loadItem)) {
                continue;
            }
            $table = $loadItem['table'] ?? throw new \InvalidArgumentException('Table is required for load instruction');
            $instructionData = $loadItem['data'] ?? [];
            $types = $loadItem['types'] ?? [];
            $pivot = $loadItem['pivot'] ?? null;
            $database = $loadItem['database'] ?? null;

            $instructionInfo = $loadItem['info'] ?? null;
            $loadInstructions[] = new LoadInstruction($table, $instructionData, $types, $pivot, $database, $instructionInfo);
        }

        // Purge instructions
        $purgeInstructions = [];
        $purgeData = $data['purge'] ?? [];
        if (!is_array($purgeData)) {
            throw new \InvalidArgumentException('Purge data must be an array');
        }
        foreach ($purgeData as $purgeItem) {
            if (!is_array($purgeItem)) {
                continue;
            }
            $table = $purgeItem['table'] ?? throw new \InvalidArgumentException('Table is required for purge instruction');
            $where = $purgeItem['where'] ?? [];
            $purgePivot = $purgeItem['purge_pivot'] ?? false;
            $database = $purgeItem['database'] ?? null;

            $purgeInstructionInfo = $purgeItem['info'] ?? null;
            $purgeInstructions[] = new PurgeInstruction($table, $where, $purgePivot, $database, $purgeInstructionInfo);
        }

        return new ScenarioDefinition(
            $name,
            $imports,
            $variables,
            $loadInstructions,
            $purgeInstructions,
            $info
        );
    }

    /**
     * Extrait récursivement les variables des fichiers importés
     *
     * @param array<string> $imports
     * @param array<string> $visited Pour éviter les boucles infinies
     * @return array<string, string>
     */
    private function extractVariablesFromImports(array $imports, array $visited = []): array
    {
        $variables = [];
        $projectDir = $this->getParameter('kernel.project_dir');
        if (!is_string($projectDir)) {
            return [];
        }
        $scenariosPath = $projectDir . '/prism/yaml';

        foreach ($imports as $importPath) {
            // Éviter les boucles infinies
            if (in_array($importPath, $visited, true)) {
                continue;
            }

            $visited[] = $importPath;

            // Construire le chemin complet du fichier
            $filePath = $scenariosPath . '/' . $importPath . '.yaml';

            if (!file_exists($filePath)) {
                continue;
            }

            try {
                $content = file_get_contents($filePath);
                if ($content === false) {
                    continue;
                }

                $yaml = \Symfony\Component\Yaml\Yaml::parse($content);

                if (!is_array($yaml)) {
                    continue;
                }

                // Extraire les variables de ce fichier
                if (isset($yaml['vars']) && is_array($yaml['vars'])) {
                    foreach ($yaml['vars'] as $key => $value) {
                        // Préfixer avec le nom du fichier pour éviter les collisions
                        $variables[$key] = $importPath;
                    }
                }

                // Récursivement extraire les variables des imports de ce fichier
                if (isset($yaml['import']) && is_array($yaml['import'])) {
                    $nestedVars = $this->extractVariablesFromImports($yaml['import'], $visited);
                    $variables = array_merge($variables, $nestedVars);
                }
            } catch (\Exception $e) {
                // Ignorer les fichiers YAML invalides
                continue;
            }
        }

        return $variables;
    }

    /**
     * Génère un scope aléatoire de 8 caractères alphanumériques
     */
    private function generateRandomScope(): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $scope = '';

        for ($i = 0; $i < 8; $i++) {
            $scope .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $scope;
    }
}
