<?php
// Este arquivo gerencia as rotas do sistema

// Iniciar a sessão apenas se não estiver ativa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Capturar a URI atual
$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?'); // Ignorar parâmetros GET na URL

// Verificar se o prefixo "/LancamentoFatura" está presente
$basePath = '/LancamentoFatura';
if (strpos($request, $basePath) === 0) {
    $request = substr($request, strlen($basePath));
}

// Adicionar uma barra inicial para rotas padrão
$request = '/' . ltrim($request, '/');

// Definir as rotas disponíveis no sistema
switch ($request) {
    case '/':
    case '/dashboard':
        // Verificar autenticação
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login');
            exit();
        }
        include __DIR__ . '/../views/dashboard.php';
        break;

    case '/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include __DIR__ . '/../controllers/LoginController.php';
        } else {
            include __DIR__ . '/../views/login.php';
        }
        break;

    case '/logout':
        include __DIR__ . '/../controllers/LogoutController.php';
        break;

    case '/faturas':
        include __DIR__ . '/../controllers/FaturaController.php';
        break;

    case '/faturas/cadastrar':
        include __DIR__ . '/../views/faturas/cadastrar.php';
        break;

    case '/transportadoras':
        include __DIR__ . '/../controllers/TransportadoraController.php';
        break;

    case '/transportadoras/cadastrar':
        include __DIR__ . '/../views/transportadoras/cadastrar.php';
        break;

    default:
        // Página não encontrada
        http_response_code(404);
        echo "404 - Página $request não encontrada";
        break;
}
