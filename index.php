<?php
// Iniciar a sessão apenas se não estiver ativa
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia a sessão se não estiver ativa
}

// Configurações e funções globais
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';

// Capturar a URL solicitada
$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?'); // Ignorar parâmetros GET

// Roteamento
switch ($request) {
    case '/':
    case '/dashboard':
        include __DIR__ . '/views/dashboard.php';
        break;

    case '/login':
        include __DIR__ . '/views/login.php';
        break;

    case '/logout':
        include __DIR__ . '/controllers/LogoutController.php';
        break;

    case '/faturas':
        include __DIR__ . '/controllers/FaturaController.php';
        break;

    case '/faturas/cadastrar':
        include __DIR__ . '/views/faturas/cadastrar.php';
        break;

    case '/transportadoras':
        include __DIR__ . '/controllers/TransportadoraController.php';
        break;

    case '/transportadoras/cadastrar':
        include __DIR__ . '/views/transportadoras/cadastrar.php';
        break;

    default: // Página não encontrada
        http_response_code(404);
        echo "<h1>404 - Página não encontrada</h1>";
        break;
}
