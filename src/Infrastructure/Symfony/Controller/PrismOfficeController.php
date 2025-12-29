<?php

declare(strict_types=1);

namespace PrismOffice\Infrastructure\Symfony\Controller;

use PrismOffice\Application\ListScenariosService;
use PrismOffice\Application\ListYamlFilesService;
use PrismOffice\Application\ListLoadedScenariosService;
use PrismOffice\Application\ListDirectoryContentsService;
use PrismOffice\Application\ListPhpFilesService;
use PrismOffice\Application\LoadScenarioService;
use PrismOffice\Application\PurgeScenarioService;
use PrismOffice\Application\DeleteScenarioService;
use PrismOffice\Application\CreateFolderService;
use PrismOffice\Application\DeleteFolderService;
use PrismOffice\Application\ViewResourcesService;
use PrismOffice\Domain\Exception\FolderDeletionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur principal de PrismOffice
 *
 * Interface web autonome pour gérer les scénarios Prism
 */
#[Route('/prism')]
final class PrismOfficeController extends AbstractController
{
    /**
     * Page principale : Liste tous les scénarios disponibles (racine)
     */
    #[Route('', name: 'prism_office_list', methods: ['GET'])]
    public function list(
        ListScenariosService $listScenarios,
        ListDirectoryContentsService $listContents,
        ListPhpFilesService $listPhpFiles,
        ListLoadedScenariosService $listLoadedScenarios,
        Request $request
    ): Response {
        // Générer un scope aléatoire si aucun n'existe en session
        $session = $request->getSession();
        if (!$session->has('prism_scope')) {
            $session->set('prism_scope', $this->generateRandomScope());
        }

        $phpScenarios = $listScenarios->execute();
        $yamlContents = $listContents->execute(''); // Racine yaml
        $phpContents = $listPhpFiles->execute(''); // Racine scripts
        $loadedScenarios = $listLoadedScenarios->execute();

        // Sauvegarder le dossier courant en session
        $session->set('prism_last_folder', '');

        return $this->render('@PrismOffice/list.html.twig', [
            'php_scenarios' => $phpScenarios,
            'yaml_folders' => $yamlContents['folders'],
            'yaml_files' => $yamlContents['files'],
            'php_folders' => $phpContents['folders'],
            'php_files' => $phpContents['files'],
            'loadedScenarios' => $loadedScenarios,
            'default_scope' => $this->generateRandomScope(),
            'current_path' => '',
            'breadcrumb' => [],
            'is_yaml_section' => true,
            'target_folder' => '',
        ]);
    }

    /**
     * Navigation dans un sous-dossier
     */
    #[Route('/directory/{path}', name: 'prism_office_directory', requirements: ['path' => '.+'], methods: ['GET'])]
    public function directory(
        string $path,
        ListScenariosService $listScenarios,
        ListDirectoryContentsService $listContents,
        ListPhpFilesService $listPhpFiles,
        ListLoadedScenariosService $listLoadedScenarios,
        Request $request
    ): Response {
        // Générer un scope aléatoire si aucun n'existe en session
        $session = $request->getSession();
        if (!$session->has('prism_scope')) {
            $session->set('prism_scope', $this->generateRandomScope());
        }

        $phpScenarios = $listScenarios->execute();
        $yamlContents = $listContents->execute($path);
        $phpContents = $listPhpFiles->execute($path);
        $loadedScenarios = $listLoadedScenarios->execute();

        // Sauvegarder le dossier courant en session
        $session->set('prism_last_folder', $path);

        // Générer le breadcrumb
        $breadcrumb = $this->generateBreadcrumb($path);

        return $this->render('@PrismOffice/list.html.twig', [
            'php_scenarios' => $phpScenarios,
            'yaml_folders' => $yamlContents['folders'],
            'yaml_files' => $yamlContents['files'],
            'php_folders' => $phpContents['folders'],
            'php_files' => $phpContents['files'],
            'loadedScenarios' => $loadedScenarios,
            'default_scope' => $this->generateRandomScope(),
            'current_path' => $path,
            'breadcrumb' => $breadcrumb,
            'is_yaml_section' => true,
            'target_folder' => $path,
        ]);
    }

    /**
     * Action : Supprimer un dossier vide
     */
    #[Route('/delete-folder', name: 'prism_office_delete_folder', methods: ['POST'])]
    public function deleteFolder(Request $request, DeleteFolderService $deleteFolderService): Response
    {
        $folderPath = (string) $request->request->get('folder_path', '');

        try {
            $deleteFolderService->execute($folderPath);
            $this->addFlash('success', sprintf('Folder "%s" deleted successfully.', $folderPath));
        } catch (FolderDeletionException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        // Rediriger vers le dossier parent
        $parentPath = dirname($folderPath);
        if ($parentPath === '.' || $parentPath === '') {
            return $this->redirectToRoute('prism_office_list');
        }

        return $this->redirectToRoute('prism_office_directory', ['path' => $parentPath]);
    }

    /**
     * Génère le breadcrumb à partir du chemin
     *
     * @return array<array{name: string, path: string|null}>
     */
    private function generateBreadcrumb(string $path): array
    {
        $breadcrumb = [['name' => 'Home', 'path' => null]];

        if ($path === '') {
            return $breadcrumb;
        }

        $parts = explode('/', trim($path, '/'));
        $currentPath = '';

        foreach ($parts as $part) {
            $currentPath .= ($currentPath !== '' ? '/' : '') . $part;
            $breadcrumb[] = ['name' => $part, 'path' => $currentPath];
        }

        return $breadcrumb;
    }

    /**
     * Action : Charger un scénario
     */
    #[Route('/load', name: 'prism_office_load', methods: ['POST'])]
    public function load(Request $request, LoadScenarioService $loadScenario): Response
    {
        $scenarioName = (string) $request->request->get('scenario_name', '');
        $scope = (string) $request->request->get('scope', 'default');
        $currentPath = (string) $request->request->get('current_path', '');
        $redirectTo = (string) $request->request->get('redirect_to', '');

        // Sauvegarder le scope en session
        $request->getSession()->set('prism_scope', $scope);

        try {
            $loadScenario->execute($scenarioName, $scope);

            $this->addFlash('success', sprintf(
                'Scenario "%s" loaded successfully with scope "%s"',
                $scenarioName,
                $scope
            ));
        } catch (\Exception $e) {
            $this->addFlash('error', sprintf(
                'Failed to load scenario: %s',
                $e->getMessage()
            ));
        }

        // Rediriger vers l'URL spécifiée ou la page d'origine
        if ($redirectTo !== '') {
            // Si c'est une URL complète, rediriger directement
            if (str_starts_with($redirectTo, '/')) {
                return $this->redirect($redirectTo);
            }
            // Sinon, c'est peut-être un nom de route
            if ($redirectTo === 'prism_office_loaded') {
                return $this->redirectToRoute('prism_office_loaded');
            }
        }

        if ($currentPath !== '') {
            return $this->redirectToRoute('prism_office_directory', ['path' => $currentPath]);
        }

        return $this->redirectToRoute('prism_office_list');
    }

    /**
     * Action : Purger un scénario
     */
    #[Route('/purge', name: 'prism_office_purge', methods: ['POST'])]
    public function purge(Request $request, PurgeScenarioService $purgeScenario): Response
    {
        $scenarioName = (string) $request->request->get('scenario_name', '');
        $scope = (string) $request->request->get('scope', 'default');
        $currentPath = (string) $request->request->get('current_path', '');
        $redirectTo = (string) $request->request->get('redirect_to', '');

        // Sauvegarder le scope en session
        $request->getSession()->set('prism_scope', $scope);

        try {
            $purgeScenario->execute($scenarioName, $scope);

            $this->addFlash('success', sprintf(
                'Scenario "%s" purged successfully for scope "%s"',
                $scenarioName,
                $scope
            ));
        } catch (\Exception $e) {
            $this->addFlash('error', sprintf(
                'Failed to purge scenario: %s',
                $e->getMessage()
            ));
        }

        // Rediriger vers la page spécifiée ou la page d'origine
        if ($redirectTo === 'prism_office_loaded') {
            return $this->redirectToRoute('prism_office_loaded');
        }

        if ($currentPath !== '') {
            return $this->redirectToRoute('prism_office_directory', ['path' => $currentPath]);
        }

        return $this->redirectToRoute('prism_office_list');
    }

    /**
     * Action : Purger tous les scénarios d'un scope
     */
    #[Route('/purge-scope', name: 'prism_office_purge_scope', methods: ['POST'])]
    public function purgeScope(Request $request, PurgeScenarioService $purgeScenario): Response
    {
        $scope = (string) $request->request->get('scope', '');
        $redirectTo = (string) $request->request->get('redirect_to', 'prism_office_list');

        if (empty($scope)) {
            $this->addFlash('error', 'Scope cannot be empty');
            return $this->redirectToRoute($redirectTo);
        }

        try {
            $purgeScenario->executeAllScope($scope);

            $this->addFlash('success', sprintf(
                'Successfully purged all scenarios for scope "%s"',
                $scope
            ));
        } catch (\Exception $e) {
            $this->addFlash('error', sprintf(
                'Failed to purge scope: %s',
                $e->getMessage()
            ));
        }

        return $this->redirectToRoute($redirectTo);
    }

    /**
     * Action : Supprimer un scénario YAML
     */
    #[Route('/delete', name: 'prism_office_delete', methods: ['POST'])]
    public function delete(Request $request, DeleteScenarioService $deleteScenario): Response
    {
        $scenarioName = (string) $request->request->get('scenario_name', '');
        $currentPath = (string) $request->request->get('current_path', '');

        try {
            $deleteScenario->execute($scenarioName);

            $this->addFlash('success', sprintf(
                'Scenario "%s" deleted successfully',
                $scenarioName
            ));
        } catch (\Exception $e) {
            $this->addFlash('error', sprintf(
                'Failed to delete scenario: %s',
                $e->getMessage()
            ));
        }

        if ($currentPath !== '') {
            return $this->redirectToRoute('prism_office_directory', ['path' => $currentPath]);
        }

        return $this->redirectToRoute('prism_office_list');
    }

    /**
     * Action : Créer un sous-dossier
     */
    #[Route('/create-folder', name: 'prism_office_create_folder', methods: ['POST'])]
    public function createFolder(Request $request, CreateFolderService $createFolder): Response
    {
        $folderPath = (string) $request->request->get('folder_path', '');

        try {
            $createFolder->execute($folderPath);

            $this->addFlash('success', sprintf(
                'Folder "%s" created successfully',
                basename($folderPath)
            ));
        } catch (\Exception $e) {
            $this->addFlash('error', sprintf(
                'Failed to create folder: %s',
                $e->getMessage()
            ));
        }

        // Rediriger vers le répertoire parent du dossier créé
        $parentPath = dirname($folderPath);
        if ($parentPath === '.' || $parentPath === '') {
            return $this->redirectToRoute('prism_office_list');
        }

        return $this->redirectToRoute('prism_office_directory', ['path' => $parentPath]);
    }

    /**
     * Page : Scénarios chargés
     */
    #[Route('/loaded', name: 'prism_office_loaded', methods: ['GET'])]
    public function loaded(ListLoadedScenariosService $listLoadedScenarios, Request $request): Response
    {
        // Générer un scope aléatoire si aucun n'existe en session
        $session = $request->getSession();
        if (!$session->has('prism_scope')) {
            $session->set('prism_scope', $this->generateRandomScope());
        }

        $loadedScenarios = $listLoadedScenarios->execute();

        // Récupérer le dernier dossier visité depuis la session
        $targetFolder = (string) $session->get('prism_last_folder', '');

        return $this->render('@PrismOffice/loaded.html.twig', [
            'loadedScenarios' => $loadedScenarios,
            'default_scope' => $this->generateRandomScope(),
            'target_folder' => $targetFolder,
        ]);
    }

    /**
     * Page : Détails des ressources d'un scénario/scope
     */
    #[Route('/{name}/{scope}/resources', name: 'prism_office_resources', requirements: ['name' => '.+'], methods: ['GET'])]
    public function resources(
        string $name,
        string $scope,
        ViewResourcesService $viewResources,
        Request $request
    ): Response {
        // Générer un scope aléatoire si aucun n'existe en session
        $session = $request->getSession();
        if (!$session->has('prism_scope')) {
            $session->set('prism_scope', $this->generateRandomScope());
        }

        $resources = $viewResources->execute($name, $scope);

        // Récupérer le dernier dossier visité depuis la session
        $targetFolder = (string) $session->get('prism_last_folder', '');

        return $this->render('@PrismOffice/resources.html.twig', [
            'scenarioName' => $name,
            'scope' => $scope,
            'resources' => $resources,
            'default_scope' => $this->generateRandomScope(),
            'target_folder' => $targetFolder,
        ]);
    }

    /**
     * Action : Mettre à jour le scope en session
     */
    #[Route('/update-scope', name: 'prism_office_update_scope', methods: ['POST'])]
    public function updateScope(Request $request): Response
    {
        $scope = (string) $request->request->get('scope', '');

        if ($scope !== '') {
            $request->getSession()->set('prism_scope', $scope);
        }

        return new Response('', Response::HTTP_NO_CONTENT);
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
