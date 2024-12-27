<?php

namespace App\Controllers;

use App\Models\Connexion;
use PDO;

class TagController {
    private $conn;

    public function __construct() {
        $this->conn = Connexion::getInstance()->getConn();
    }

    public function index() {
        try {
            $stmt = $this->conn->query('
                SELECT id, tag_name as name
                FROM tag
                ORDER BY tag_name ASC
            ');

            $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'tags' => $tags
            ]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la récupération des tags: ' . $e->getMessage()
            ]);
        }
    }

    public function search() {
        try {
            $search = $_GET['q'] ?? '';

            $stmt = $this->conn->prepare('
                SELECT id, tag_name as name
                FROM tag
                WHERE tag_name LIKE CONCAT(?, "%")
                ORDER BY tag_name
                LIMIT 10
            ');

            $stmt->execute([$search]);
            $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'tags' => $tags
            ]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la recherche des tags: ' . $e->getMessage()
            ]);
        }
    }

    public function create() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $tagName = $data['name'] ?? '';

            if (empty($tagName)) {
                throw new \Exception('Le nom du tag est requis');
            }

            // Vérifier si le tag existe déjà
            $existingId = $this->getTagIdByName($tagName);
            if ($existingId) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'tag' => [
                        'id' => $existingId,
                        'name' => $tagName
                    ],
                    'message' => 'Tag existant récupéré'
                ]);
                return;
            }

            // Créer le nouveau tag
            $stmt = $this->conn->prepare('
                INSERT INTO tag (tag_name)
                VALUES (?)
            ');

            $stmt->execute([$tagName]);
            $id = $this->conn->lastInsertId();

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'tag' => [
                    'id' => $id,
                    'name' => $tagName
                ],
                'message' => 'Tag créé avec succès'
            ]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la création du tag: ' . $e->getMessage()
            ]);
        }
    }

    public function delete($id) {
        try {
            // Vérifier si le tag est utilisé
            $stmt = $this->conn->prepare('
                SELECT COUNT(*)
                FROM project_tags
                WHERE id_tag = ?
            ');
            $stmt->execute([$id]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                throw new \Exception('Ce tag est utilisé par des projets et ne peut pas être supprimé');
            }

            // Supprimer le tag
            $stmt = $this->conn->prepare('
                DELETE FROM tag
                WHERE id = ?
            ');

            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                throw new \Exception('Tag non trouvé');
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Tag supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la suppression du tag: ' . $e->getMessage()
            ]);
        }
    }

    public function update($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $newName = $data['name'] ?? '';

            if (empty($newName)) {
                throw new \Exception('Le nouveau nom du tag est requis');
            }

            $stmt = $this->conn->prepare('
                UPDATE tag
                SET tag_name = ?
                WHERE id = ?
            ');

            $stmt->execute([$newName, $id]);

            if ($stmt->rowCount() === 0) {
                throw new \Exception('Tag non trouvé');
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'tag' => [
                    'id' => $id,
                    'name' => $newName
                ],
                'message' => 'Tag mis à jour avec succès'
            ]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du tag: ' . $e->getMessage()
            ]);
        }
    }

    private function getTagIdByName($name) {
        $stmt = $this->conn->prepare('SELECT id FROM tag WHERE tag_name = ?');
        $stmt->execute([$name]);
        return $stmt->fetchColumn();
    }
}
