<?php
// Ativar sessões
session_start();

// Incluir configurações e funções globais
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';

// Gerenciar requisições (roteamento simples)
$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?'); // Ignorar parâmetros GET na URL

// Roteamento básico
switch ($request) {
    case '/':
    case '/dashboard':
        // Verificar autenticação e exibir o painel do usuário
        include __DIR__ . '/views/dashboard.php';
        break;

    case '/login':
        // Exibir a página de login
        include __DIR__ . '/controllers/LoginController.php';
        break;

    case '/logout':
        // Finalizar a sessão do usuário
        session_destroy();
        header('Location: /login');
        break;

    case '/faturas':
        // Gerenciamento de faturas
        include __DIR__ . '/controllers/FaturaController.php';
        break;

    case '/transportadoras':
        // Gerenciamento de transportadoras
        include __DIR__ . '/controllers/TransportadoraController.php';
        break;

    default:
        // Página não encontrada
        http_response_code(404);
        echo "<h1>404 - Página não encontrada</h1>";
        break;
}
?>
