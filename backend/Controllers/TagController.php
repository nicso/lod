<?php

namespace App\Controllers;

use App\Models\Connexion;
use PDO;

class TagController {
    private $conn;

    public function __construct() {
        $this->conn = Connexion::getInstance()->getConn();
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

            echo json_encode([
                'success' => true,
                'tags' => $tags
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function create() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $tagName = $data['name'] ?? '';

            if (empty($tagName)) {
                throw new \Exception('Tag name is required');
            }

            $stmt = $this->conn->prepare('
                INSERT INTO tag (tag_name)
                VALUES (?)
                ON DUPLICATE KEY UPDATE tag_name = tag_name
            ');

            $stmt->execute([$tagName]);
            $id = $this->conn->lastInsertId() ?: $this->getTagIdByName($tagName);

            echo json_encode([
                'success' => true,
                'tag' => [
                    'id' => $id,
                    'name' => $tagName
                ]
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function getTagIdByName($name) {
        $stmt = $this->conn->prepare('SELECT id FROM tag WHERE tag_name = ?');
        $stmt->execute([$name]);
        return $stmt->fetchColumn();
    }
}
