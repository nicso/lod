<?php
namespace App\Controllers;

use App\Models\Connexion;
use PDOException;

class AuthController {
    private $conn;

    public function __construct() {
        $this->conn = Connexion::getInstance()->getConn();
    }

    public function login() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            if (empty($email) || empty($password)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Email et mot de passe requis'
                ]);
                return;
            }

            $stmt = $this->conn->prepare('SELECT * FROM user WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['passwordHash'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Connexion réussie',
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['userName'],
                        'email' => $user['email']
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Identifiants invalides'
                ]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }
}