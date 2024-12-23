<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Connexion;

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
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$routes = [
    '/api/auth/login' => 'App\Controllers\AuthController@login',
    '/api/projects/{id}' => 'App\Controllers\ProjectController@show'
];

// if (isset($routes[$uri])) {
//     [$controller, $method] = explode('@', $routes[$uri]);
//     $controllerInstance = new $controller();
//     $controllerInstance->$method();
// } else {
//     http_response_code(404);
//     echo json_encode(['success' => false, 'message' => 'Route non trouvée']);
// }

if ($uri === '/api/auth/login') {
    $controller = new App\Controllers\AuthController();
    $controller->login();
} elseif (preg_match('#^/api/projects/(\d+)$#', $uri)) {
    $controller = new App\Controllers\ProjectController();
    $controller->show();
} elseif ($uri === '/api/projects') {
    $controller = new App\Controllers\ProjectController();
    $controller->index();
} elseif (preg_match('#^/api/projects/tag/(\d+)$#', $uri, $matches)) {
    $controller = new App\Controllers\ProjectController();
    $controller->indexByTag($matches[1]);
}
 else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Route non trouvée']);
}
