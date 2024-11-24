<?php
// backend/public/index.php

// Fonction pour gérer les headers CORS
function setCorsHeaders() {
    // Autoriser l'origine spécifique (votre frontend)
    header('Access-Control-Allow-Origin: http://localhost:3000');
    // Autoriser les credentials (cookies, authorization headers, etc.)
    header('Access-Control-Allow-Credentials: true');
    // Méthodes HTTP autorisées
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    // Headers autorisés
    header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Requested-With');
    // Durée de mise en cache des résultats du preflight
    header('Access-Control-Max-Age: 3600');
    // Type de contenu
    header('Content-Type: application/json; charset=UTF-8');
}

// Appliquer les headers CORS
setCorsHeaders();

// Gérer la requête OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Connexion à la base de données
try {
    $pdo = new PDO(
        'mysql:host=mariadb;dbname=userapp',
        'appuser',
        'apppassword',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de connexion à la base de données: ' . $e->getMessage()
    ]);
    exit;
}

// Router simple
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Route de login
if ($uri === '/api/auth/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Récupération des données JSON
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Log pour le débogage
        error_log('Données reçues : ' . print_r($data, true));
        
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Validation basique
        if (empty($email) || empty($password)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Email et mot de passe requis'
            ]);
            exit;
        }

        // Recherche de l'utilisateur
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Log pour le débogage
        error_log('Utilisateur trouvé : ' . ($user ? 'oui' : 'non'));

        // Vérification des identifiants
        if ($user && password_verify($password, $user['password'])) {
            // Démarrage de la session
            session_start();
            $_SESSION['user_id'] = $user['id'];

            echo json_encode([
                'success' => true,
                'message' => 'Connexion réussie',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
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
    } catch (Exception $e) {
        error_log('Erreur : ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur serveur: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Route non trouvée'
    ]);
}

