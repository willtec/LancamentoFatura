<?php
// Importar configurações globais
require_once '../config/db.php';
require_once '../config/auth.php';
require_once '../config/helpers.php';
require_once '../config/constants.php'; // Inclua a constante BASE_URL

// Capturar a URL solicitada e remover qualquer parâmetro GET
$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?'); // Ignorar parâmetros GET

// Roteamento baseado na URL
switch ($request) {
    case '/':
    case '/dashboard':
        include '../views/dashboard.php';
        break;

    case '/login':
        include '../controllers/LoginController.php';
        break;

    case '/logout':
        include '../controllers/LogoutController.php';
        break;

    case '/faturas':
        include '../controllers/FaturaController.php';
        break;

    case '/faturas/cadastrar':
        include '../views/faturas/cadastrar.php';
        break;

    case '/transportadoras':
        include '../controllers/TransportadoraController.php';
        break;

    case '/transportadoras/cadastrar':
        include '../views/transportadoras/cadastrar.php';
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Página não encontrada</h1>";
        break;
}
?>
