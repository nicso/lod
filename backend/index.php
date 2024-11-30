<?php
// Définir le chemin racine
define('ROOT_PATH', dirname(__DIR__));

// Inclure l'autoloader
require_once ROOT_PATH . '/system/autoload.php';

use models\Connexion;

// Autoriser toutes les origines en développement (à restreindre en production)
header('Access-Control-Allow-Origin: http://localhost:3000'); 
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json');

$conn = Connexion::getInstance()->getConn() ;

// Gérer la requête OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Réponse JSON
header('Content-Type: application/json');

echo json_encode(['message' => 'Hello World!']);