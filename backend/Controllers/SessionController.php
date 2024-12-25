<?php
namespace App\Controllers;

class SessionController {
    public function __construct() {
        // Configurer les options de session
        ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30); // 30 jours
        ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);

        // Options de sécurité pour les cookies
        session_set_cookie_params([
            'lifetime' => 60 * 60 * 24 * 30,
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }

    public function checkSession() {
        session_start();

        if (isset($_SESSION['user_id'])) {
            // Récupérer les informations de l'utilisateur depuis la base de données
            $conn = \App\Models\Connexion::getInstance()->getConn();
            $stmt = $conn->prepare('SELECT id, userName, email FROM user WHERE id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();

            if ($user) {
                echo json_encode([
                    'success' => true,
                    'isAuthenticated' => true,
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['userName'],
                        'email' => $user['email']
                    ]
                ]);
                return;
            }
        }

        echo json_encode([
            'success' => true,
            'isAuthenticated' => false
        ]);
    }

    public function logout() {
        session_start();
        session_destroy();

        echo json_encode([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }
}
