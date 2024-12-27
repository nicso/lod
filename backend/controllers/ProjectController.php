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
            // Récupérer le terme de recherche
            $searchTerm = $_GET['search'] ?? '';

            // Récupérer les tags depuis l'URL (format: tags[]=1&tags[]=2)
            $tags = isset($_GET['tags']) ? (array)$_GET['tags'] : null;

            // Si les tags sont fournis comme une chaîne unique, les convertir en tableau
            if (isset($_GET['tags']) && !is_array($_GET['tags'])) {
                $tags = explode(',', $_GET['tags']);
            }

            // Convertir les IDs de tags en integers
            if ($tags) {
                $tags = array_map('intval', $tags);
            }

            // Utiliser la méthode de recherche mise à jour
            $projects = $this->projectModel->search($searchTerm, $tags);

            // Convertir les projets en tableaux
            $projectsArray = array_map(function($project) {
                return $project->toArray();
            }, $projects);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'projects' => $projectsArray,
                'meta' => [
                    'total' => count($projectsArray),
                    'filters' => [
                        'search' => $searchTerm,
                        'tags' => $tags
                    ]
                ]
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

    public function create() {
        header('Content-Type: application/json');

        try {
            // Vérifier la session
            session_start();
            if (!isset($_SESSION['user_id'])) {
                error_log("Tentative de création de projet sans session utilisateur");
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Utilisateur non connecté'
                ]);
                return;
            }

            // Capturer et vérifier les données
            $rawData = file_get_contents('php://input');
            error_log("Données brutes reçues: " . $rawData);

            $data = json_decode($rawData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Données JSON invalides: ' . json_last_error_msg());
            }

            // Valider les données requises
            if (empty($data['title']) || empty($data['content']) || empty($data['id_category'])) {
                throw new \Exception('Données manquantes: titre, contenu et catégorie sont requis');
            }

            // Créer le projet
            $projectId = $this->projectModel->createWithAuthor($data, $_SESSION['user_id']);
            error_log("Projet créé avec l'ID: " . $projectId);

            // Associer les tags si présents
            if (!empty($data['selectedTags'])) {
                error_log("Association des tags pour le projet " . $projectId);
                $this->associateProjectTags($projectId, $data['selectedTags']);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Projet créé avec succès',
                'projectId' => $projectId
            ]);

        } catch (\Exception $e) {
            error_log("Erreur lors de la création du projet: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function associateProjectTags($projectId, $tags) {
        try {
            $conn = $this->projectModel->getConnection();
            $stmt = $conn->prepare('
                INSERT INTO project_tags (id_project, id_tag)
                VALUES (?, ?)
            ');

            foreach ($tags as $tag) {
                $stmt->execute([$projectId, $tag['id']]);
            }
        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de l'association des tags: " . $e->getMessage());
        }
    }


}
