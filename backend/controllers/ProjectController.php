<?php
namespace App\Controllers;

use App\Models\Project;

class ProjectController {
    private $projectModel;

    public function __construct() {
        $this->projectModel = new Project();
    }

    public function show() {
        try {
            // Récupérer l'ID du projet depuis l'URL
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            preg_match('/\/api\/projects\/(\d+)/', $uri, $matches);
            $id = $matches[1] ?? null;

            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'ID du projet manquant'
                ]);
                return;
            }

            $project = $this->projectModel->find($id);
            error_log("Projet trouvé: " . ($project ? 'oui' : 'non'));

            if (!$project) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Projet non trouvé'
                ]);
                return;
            }

            $response = [
                'success' => true,
                'project' => $project->toArray()
            ];
            error_log("Réponse finale: " . json_encode($response));
            echo json_encode($response);
        } catch (\Exception $e) {
            error_log("Erreur dans show(): " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function index() {
        try {
            $projects = $this->projectModel->getAllAsArray();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'projects' => $projects
            ]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function indexByTag($tagId) {
        try {
            $projects = $this->projectModel->getProjectsByTag($tagId);
            $projectsArray = array_map(function($project){
                return $project->toArray();
            }, $projects);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'projects' => $projectsArray
            ]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


}
