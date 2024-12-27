<?php

// Activer la gestion des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 0); // Désactiver l'affichage des erreurs
ini_set('log_errors', 1); // Activer la journalisation des erreurs

require_once __DIR__ . '/../vendor/autoload.php';

// Headers CORS
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 3600');
header('Content-Type: application/json; charset=UTF-8');

// Gérer les requêtes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Gestion des erreurs PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Router basique
try{
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Log des informations de la requête
error_log("Request Method: " . $method);
error_log("Request URI: " . $uri);
error_log("Raw Request Body: " . file_get_contents('php://input'));

$routes = [
    '/api/auth/login' => 'App\Controllers\AuthController@login',
    '/api/projects/{id}' => 'App\Controllers\ProjectController@show'
];


if ($uri === '/api/auth/login') {
    $controller = new App\Controllers\AuthController();
    $controller->login();
} elseif (preg_match('#^/api/projects/(\d+)$#', $uri)) {
    $controller = new App\Controllers\ProjectController();
    $controller->show();
} elseif ($uri === '/api/projects' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new App\Controllers\ProjectController();
    $controller->create();
} elseif ($uri === '/api/projects') {
    $controller = new App\Controllers\ProjectController();
    $controller->index();
} elseif (preg_match('#^/api/projects/tag/(\d+)$#', $uri, $matches)) {
    $controller = new App\Controllers\ProjectController();
    $controller->indexByTag($matches[1]);
} elseif ($uri === '/api/auth/check') {
    $controller = new App\Controllers\SessionController();
    $controller->checkSession();
} elseif ($uri === '/api/auth/logout') {
    $controller = new App\Controllers\SessionController();
    $controller->logout();

} elseif ($uri === '/api/categories') {
    $controller = new App\Controllers\CategoryController();
    $controller->index();
} elseif ($uri === '/api/tags/search') {
    $controller = new App\Controllers\TagController();
    $controller->search();
} elseif ($uri === '/api/tags' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new App\Controllers\TagController();
    $controller->create();
} elseif ($uri === '/api/tags') {
    $controller = new App\Controllers\TagController();
    $controller->index();
}
 else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Route non trouvée']);
}
} catch (\Exception $e) {
    error_log("Erreur dans index.php: " . $e->getMessage());
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal Server Error: ' . $e->getMessage()
    ]);
}
